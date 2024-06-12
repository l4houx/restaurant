<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function Symfony\Component\String\u;

class CompanyNumberValidator extends ConstraintValidator
{
    private const SIRET_LENGTH = 14;

    public function validate($value, Constraint $constraint): void
    {
        /* @var CompanyNumber $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $value = u($value)->trim()->toString();

        $addViolation = fn () => $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();

        if (!is_numeric($value) || self::SIRET_LENGTH !== u($value)->length()) {
            $addViolation();
        }

        $sum = 0;
        for ($i = 0; $i < self::SIRET_LENGTH; ++$i) {
            if (0 === $i % 2) {
                $tmp = ((int) u($value)->slice($i, 1)->toString()) * 2;
                $tmp = $tmp > 9 ? $tmp - 9 : $tmp;
            } else {
                $tmp = (int) u($value)->slice($i, 1)->toString();
            }
            $sum += $tmp;
        }

        if ($sum % 10 > 0) {
            $addViolation();
        }
    }
}
