<?php

namespace App\GraphQL\Queries;

/**
 * Query simples que retorna uma mensagem de hello world
 */
class HelloQuery
{
    /**
     * Retorna uma mensagem de hello world
     */
    public function __invoke(): string
    {
        return 'Hello from GraphQL! 🚀 Projeto funcionando perfeitamente!';
    }
}
