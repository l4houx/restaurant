<?php

namespace App\Entity\Data;

use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasGedmoTimestampTrait;
use App\Entity\Traits\HasIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Transfer
{
    use HasIdTrait;
    use HasGedmoTimestampTrait;
    use HasDeletedAtTrait;
    // use SoftDeleteableEntity;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private int $points;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Account $from;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Account $to;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\ManyToMany(targetEntity: Transaction::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'transfer_transactions')]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getFrom(): Account
    {
        return $this->from;
    }

    public function setFrom(Account $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): Account
    {
        return $this->to;
    }

    public function setTo(Account $to): static
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Transfer
    {
        $this->comment = $comment;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->from === $this->to) {
            $context->buildViolation('You cannot transfer points between same account.')->addViolation();
        }

        if ($this->from->getBalance() < $this->points) {
            $context->buildViolation('The amount of point cannot be less than account balance.')->addViolation();
        }
    }
}
