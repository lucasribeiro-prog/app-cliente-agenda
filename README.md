Sistema de Agendamentos*

Este projeto é um Sistema de Agendamentos construído com Laravel 11 e Vue 3 (instalado via Breeze e Vite). O sistema permite que os usuários agendem serviços, preenchendo informações como nome, telefone, CPF, tipo de atendimento, e outros detalhes.

*Funcionalidades*

-Cadastro de Agendamentos: Formulário que permite ao usuário agendar um serviço.
-Consulta de Agendamentos: Página onde o usuário pode visualizar seus agendamentos.
-Navegação Simples: Menu de navegação com links para as principais páginas (Início, Agendar, Consultar).

*Tecnologias Utilizadas*

Backend:
-Laravel 11
-Inertia.js
-MySQL

Frontend:
-Vue 3 (com Breeze e Vite)
-Bootstrap 5 (para layout e estilização)
-Font Awesome (para ícones)

*Pré-requisitos*
Antes de começar, certifique-se de ter as seguintes ferramentas instaladas:

-Node.js
-Composer
-MySQL
-PHP

*Como Executar o Projeto*
Clonar o Repositório

  git clone https://github.com/seu-usuario/seu-projeto.git
  cd seu-projeto

*Instalar as Dependências do Backend*

  composer install
  cp .env.example .env
  php artisan key:generate

*Configurar o Banco de Dados*

1. Crie um banco de dados MySQL e atualize as credenciais no arquivo .env:
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=nome_do_banco
  DB_USERNAME=usuario
  DB_PASSWORD=senha

2. Rode as migrações para criar as tabelas necessárias:
  php artisan migrate

*Instalar as Dependências do Frontend*
  npm install
  npm run dev

*Iniciar o Servidor Local*
  php artisan serve

*Executar o Vite para Hot-Reload*

Em outra aba do terminal, rode o seguinte comando:
  npm run dev