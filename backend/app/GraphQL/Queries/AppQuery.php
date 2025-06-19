<?php

namespace App\GraphQL\Queries;

/**
 * Query que retorna informações da aplicação
 */
class AppQuery
{
    /**
     * Retorna informações da aplicação Laravel
     */
    public function __invoke(): array
    {
        return [
            'name' => config('app.name', 'projeto_base'),
            'version' => '1.0.0',
            'env' => config('app.env', 'local'),
            'url' => config('app.url', 'http://localhost:8000'),
        ];
    }
}
