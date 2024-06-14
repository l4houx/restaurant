<?php

namespace App\Entity\Traits;

use App\Entity\Rules;
use App\Entity\RulesAgreement;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;

trait HasRulesAgreementsTrait
{
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RulesAgreement::class, fetch: 'EXTRA_LAZY', cascade: ["persist"])]
    #[ORM\OrderBy(['agreedAt' => 'desc'])]
    private Collection $rulesAgreements;

    public function acceptRules(Rules $rules): void
    {
        $this->agreeRules($rules, true);
    }

    public function refuseRules(Rules $rules): void
    {
        $this->agreeRules($rules, false);
    }

    protected function getAgreementByRules(Rules $rules): ?RulesAgreement
    {
        $criteria = (new Criteria())
            ->setMaxResults(1)
            ->andWhere(Criteria::expr()->eq("rules", $rules))
        ;

        /** @var RulesAgreement $agreement */
        $agreement = $this->rulesAgreements->matching($criteria)->first();
        return !$agreement ? null : $agreement;
    }

    protected function agreeRules(Rules $rules, bool $accepted): void
    {
        $agreement = $this->getAgreementByRules($rules);

        if ($agreement === null) {
            $agreement = (new RulesAgreement())
                ->setUser($this)
                ->setRules($rules)
            ;

            $this->rulesAgreements->add($agreement);
        }

        $agreement->setAccepted($accepted)->setAgreedAt(new \DateTimeImmutable());
    }

    public function hasAcceptedRules(Rules $rules): bool
    {
        $agreement = $this->getAgreementByRules($rules);

        return $agreement === null ? false : $agreement->isAccepted();
    }

    /**
     * @return Collection<int, RulesAgreement>
     */
    public function getRulesAgreements(): Collection
    {
        return $this->rulesAgreements;
    }

    public function getLastRulesAgreement(): ?RulesAgreement
    {
        if ($this->rulesAgreements->count() === 0) {
            return null;
        }

        /** @var RulesAgreement $rulesAgreement */
        $rulesAgreement = $this->rulesAgreements->first();

        return $rulesAgreement;
    }
}
