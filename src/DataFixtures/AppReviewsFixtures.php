<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\Shop\Product;
use App\Entity\Testimonial;
use App\Entity\User;
use App\Entity\User\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppReviewsFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    private function createTestimonials(ObjectManager $manager): void
    {
        /** @var array<Customer> $customers */
        $customers = $manager->getRepository(Customer::class)->findAll();

        // Create 20 Testimonial by User
        for ($i = 1; $i <= 20; ++$i) {
            $testimonial = new Testimonial();
            $testimonial
                ->setAuthor($this->faker()->randomElement($customers))
                ->setIsOnline($this->faker()->numberBetween(0, 1))
                ->setRating($this->faker()->numberBetween(1, 5))
                ->setName($this->faker()->unique()->sentence(5, true))
                ->setSlug($this->slugger->slug($testimonial->getName())->lower())
                ->setContent($this->faker()->paragraph())
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $manager->persist($testimonial);
        }
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<int, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        /** @var array<int, Product> $products */
        $products = $manager->getRepository(Product::class)->findAll();

        $this->createTestimonials($manager);
        $manager->flush();

        // Create 2000 Review by Product
        for ($i = 0; $i <= 2000; ++$i) {
            $review = new Review();
            $review
                ->setAuthor($this->faker()->randomElement($users))
                ->setProduct($this->faker()->randomElement($products))
                ->setIsVisible($this->faker()->numberBetween(0, 1))
                ->setRating($this->faker()->numberBetween(1, 5))
                ->setName($this->faker()->unique()->sentence(5, true))
                ->setSlug($this->slugger->slug($review->getName())->lower())
                ->setContent($this->faker()->paragraph())
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $manager->persist($review);
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppUserFixtures::class,
            AppShopFixtures::class,
        ];
    }
}
