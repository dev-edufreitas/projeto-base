#!/bin/bash
set -e

# Caminho raiz do projeto
ROOT_PATH="/var/www"
APP_PATH="${ROOT_PATH}/html"


# Função para aguardar a disponibilidade do PostgreSQL
wait_for_postgres() {
    echo "Aguardando conexão com PostgreSQL..."
    until php -r "
        \$dsn = 'pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}';
        echo \"Tentando conectar a \$dsn...\n\";
        try {
            \$dbh = new PDO(\$dsn, '${DB_USERNAME}', '${DB_PASSWORD}');
            echo \"PostgreSQL está disponível!\n\";
            exit(0);
        } catch (PDOException \$e) {
            echo \"PostgreSQL indisponível: {\$e->getMessage()}\n\";
            exit(1);
        }
    " 2>&1; do
        echo "Aguardando PostgreSQL..."
        sleep 2
    done
}

# Executar setup Laravel
setup_laravel() {
    if [ ! -f "$APP_PATH/.setup_done" ]; then
        echo "Executando setup inicial do Laravel..."

        # Instalar dependências se necessário
        if [ ! -d "$APP_PATH/vendor" ] || [ "$FORCE_COMPOSER_INSTALL" = "true" ]; then
            echo "Instalando dependências do Composer..."
            composer install --no-interaction --optimize-autoloader
        fi

        # Gerar chave da aplicação, se necessário
        if [ -z "$APP_KEY" ]; then
            echo "Gerando chave da aplicação..."
            php artisan key:generate --ansi
        fi

        # Executar migrações
        echo "Executando migrações do banco de dados..."
        php artisan migrate --force

        # Executar seeders se ativado
        if [ "$RUN_SEEDERS" = "true" ]; then
            echo "Executando seeders..."
            php artisan db:seed --force
        fi

        # Publicar GraphQL se necessário
        if [ ! -f "$APP_PATH/config/graphql.php" ] || [ "$FORCE_GRAPHQL_PUBLISH" = "true" ]; then
            echo "Publicando arquivos do GraphQL..."
            php artisan vendor:publish --provider="Rebing\GraphQL\GraphQLServiceProvider"
        fi

        # Limpar cache
        echo "Limpando cache do Laravel..."
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear
        php artisan view:clear

        # Marca como configurado
        touch "$APP_PATH/.setup_done"
    else
        echo "Laravel já configurado. Pulando setup inicial."
    fi
}

# Executar tudo
cd "$APP_PATH"
wait_for_postgres
setup_laravel

# Rodar Laravel (dev ou produção)
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
    echo "Iniciando servidor de desenvolvimento Laravel..."
    php artisan serve --host=0.0.0.0 --port=8000
else
    echo "Iniciando aplicação em modo produção..."
    php-fpm
fi

# Fim - opcional: manter container vivo com bash se quiser
exec "$@"
