<?php

namespace App\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AuthorValidator
{
    public static function validate($object, ExecutionContextInterface $context, $payload)
    {
        if ($object->getFirstName() === $object->getLastName()) {
            $context->buildViolation('Vous ne pouvez pas utiliser votre prénom comme mot de passe')
                ->atPath('firstName')
                ->addViolation();
        }
    }
}
