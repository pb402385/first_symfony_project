<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\RuntimeException;
use function PHPUnit\Framework\containsEqual;

final class BanWordPhpValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var BanWordPhp $constraint */
        if (!$constraint instanceof BanWordPhp) {
            throw new RuntimeException('Expected BanWordPhp constraint.');
        }

        if (null === $value || '' === $value) {
            return;
        }

        $value = strtolower($value);
        foreach ($constraint->banWordEmail as $banWordEmail) {
            if(str_contains($value, $banWordEmail)){
                //dd($value, $banWordEmail);
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ banWordEmail }}', $banWordEmail)
                    ->addViolation()
                ;
            }
        }

        // TODO: implement the validation here
        //$this->context->buildViolation($constraint->message)
        //    ->setParameter('{{ value }}', $value)
        //    ->addViolation()
        //;
    }
}
