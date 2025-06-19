<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserType
{
    public function resolveEmailMasked($root, array $args, GraphQLContext $context): string
    {
        // Exemplo de campo customizado que mascara o email
        $email = $root->email;
        $parts = explode('@', $email);
        $username = substr($parts[0], 0, 2) . str_repeat('*', strlen($parts[0]) - 2);
        return $username . '@' . $parts[1];
    }
}