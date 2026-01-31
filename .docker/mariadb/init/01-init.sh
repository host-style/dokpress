#!/bin/bash
# Script de inicialização do banco de dados
# Executado apenas na primeira criação do container

set -e

echo "Iniciando configuração do banco de dados..."

mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    -- Cria o banco de dados se não existir
    CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}\`
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

    -- Cria o usuário se não existir
    CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';
    CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PASSWORD}';

    -- Garante que o usuário tenha todas as permissões necessárias
    GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}\`.* TO '${MYSQL_USER}'@'%';
    GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}\`.* TO '${MYSQL_USER}'@'localhost';

    -- Aplica as permissões
    FLUSH PRIVILEGES;

    -- Mostra os usuários criados
    SELECT User, Host FROM mysql.user WHERE User = '${MYSQL_USER}';
EOSQL

echo "Configuração do banco de dados concluída!"
