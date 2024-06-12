<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use App\Entity\HomepageHeroSetting;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, User::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('u')
            ->orderBy('u.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('u.updatedAt <= :now')
            ->orWhere('u.isVerified = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::USER_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['u.id', 'u.username', 'u.lastname', 'u.firstname'],
            ]
        );
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Query used to retrieve a user for the login.
     */
    public function findUserByEmailOrUsername(string $usernameOrEmail): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.email) = :identifier')
            // ->where('LOWER(u.email) = :identifier OR LOWER(u.username) = :identifier')
            // ->andWhere('u.isVerified = true')
            ->orWhere('LOWER(u.username) = :identifier')
            ->setParameter('identifier', mb_strtolower($usernameOrEmail))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return User[]
     */
    public function findUsers()
    {
        return $this->createQueryBuilder('u')
            ->where('u.isVerified = all')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * To get the aministrator.
     */
    public function getAdministrator()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_ADMIN_APPLICATION"%')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findTeam(int $limit): array // (PagesController)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.designation', 'DESC')
            ->andwhere('u.isVerified = :isVerified')
            ->andwhere('u.isTeam = :isTeam')
            ->setParameter('isVerified', true)
            ->setParameter('isTeam', true)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return User[]
     */
    public function clean(): array
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.deletedAt IS NOT NULL')
            ->andWhere('u.deletedAt < NOW()')
        ;

        /** @var User[] $users */
        $users = $query->getQuery()->getResult();
        $query->delete(User::class, 'u')->getQuery()->execute();

        return $users;
    }

    /**
     * List suspended users
     */
    public function querySuspended(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->where('u.isSuspended IS NOT NULL')
            ->orderBy('u.isSuspended', 'DESC')
        ;
    }

    /**
     * Returns the users after applying the specified search criterias.
     *
     * @param string                   $role
     * @param string                   $keyword
     * @param string                   $username
     * @param string                   $email
     * @param string                   $firstname
     * @param string                   $lastname
     * @param bool                     $isVerified
     * @param bool                     $isSuspended
     * @param string                   $slug
     * @param string                   $followedby
     * @param HomepageHeroSetting|null $isOnHomepageSlider
     * @param int                      $limit
     * @param string                   $sort
     * @param string                   $order
     * @param int                      $count
     */
    public function getUsers($role, $keyword, $username, $email, $firstname, $lastname, $isVerified, $isSuspended, $slug, $followedby, $isOnHomepageSlider, $limit, $sort, $order, $count): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');

        if ($count) {
            $qb->select('COUNT(u)');
        } else {
            $qb->select('u');
        }

        if ($role !== "all") {
            $qb->andWhere("u.roles LIKE :role")->setParameter("role", "%ROLE_" . strtoupper($role) . "%");
        }

        if ('all' !== $keyword) {
            $qb->andWhere('u.username LIKE :keyword or :keyword LIKE u.username or u.email LIKE :keyword or :keyword LIKE u.email or u.firstname LIKE :keyword or :keyword LIKE u.firstname or u.lastname LIKE :keyword or :keyword LIKE u.lastname')->setParameter('keyword', '%'.$keyword.'%');
        }

        if ('all' !== $username) {
            $qb->andWhere('u.username = :username')->setParameter('username', $username);
        }

        if ('all' !== $email) {
            $qb->andWhere('u.email = :email')->setParameter('email', $email);
        }

        if ('all' !== $firstname) {
            $qb->andWhere('u.firstname LIKE :firstname or :firstname LIKE u.firstname')->setParameter('firstname', '%'.$firstname.'%');
        }

        if ('all' !== $lastname) {
            $qb->andWhere('u.lastname LIKE :lastname or :lastname LIKE u.lastname')->setParameter('lastname', '%'.$lastname.'%');
        }

        if ('all' !== $isVerified) {
            $qb->andWhere('u.isVerified = :isVerified')->setParameter('isVerified', $isVerified);
        }

        if ('all' !== $isSuspended) {
            $qb->andWhere('u.isSuspended = :isSuspended')->setParameter('isSuspended', $isSuspended);
        }

        if ('all' !== $slug) {
            $qb->andWhere('u.slug = :slug')->setParameter('slug', $slug);
        }

        if ($followedby !== "all") {
            $qb->andWhere(":followedby MEMBER OF restaurant.followedby")->setParameter("followedby", $followedby);
        }

        if (true === $isOnHomepageSlider) {
            $qb->andWhere('u.isrestaurantonhomepageslider IS NOT NULL');
        }

        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        $qb->orderBy('u.'.$sort, $order);

        $qb->andWhere('u.id != :superadmin')->setParameter('superadmin', 'superadmin');

        return $qb;
    }
}
