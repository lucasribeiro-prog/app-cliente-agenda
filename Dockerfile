# Usar uma imagem do Laravel
FROM php:8.2-fpm

# Instalar extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Instalar Node.js versão 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Configurar o diretório da aplicação
WORKDIR /var/www/html

# Copiar os arquivos para o container
COPY . .

# Instalar dependências PHP
RUN composer install --optimize-autoloader --no-dev

# Instalar dependências npm e compilar Vue
RUN npm install && npm run build

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor a porta padrão do Laravel
EXPOSE 8000

# Comando para iniciar o servidor Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000

CMD APP_PORT_NUMERIC=${APP_PORT//\"/} && PHP_CLI_SERVER_WORKERS_NUMERIC=${PHP_CLI_SERVER_WORKERS//\"/} && \
    php artisan serve --host=0.0.0.0 --port=$APP_PORT_NUMERIC

