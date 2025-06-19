<?php

namespace App\GraphQL\Queries;

class HelloQuery
{
    public function __invoke(mixed $root, array $args): string
    {
        $name = $args['name'] ?? 'World';
        return "Hello, {$name}!";
    }
}
