# Imagem PHP 8.2 com extensões necessárias
FROM php:8.2-cli

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /app

# Copiar arquivos do projeto
COPY . .

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Cache de configuração Laravel
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Expor porta
EXPOSE 8080

# Comando para iniciar
CMD ["/bin/sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
