<?php

namespace App\Validator;

use phpDocumentor\Reflection\Types\Mixed_;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class BanWordPhp extends Constraint
{




    // You can use #[HasNamedArguments] to make some constraint options required.
    // All configurable options must be passed to the constructor.
    public function __construct(
        public string $mode = 'strict',
        public string $message = 'Le terme "{{ banWordEmail }}" est interdit',
        public $banWordEmail = ['spam','yopmail'],
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
