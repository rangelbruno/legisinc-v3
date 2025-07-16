# Visão Geral do Projeto LegisInc

Este documento fornece uma análise detalhada da arquitetura, tecnologias e estrutura do projeto LegisInc.

## 1. Ambiente de Desenvolvimento (Docker)

O projeto utiliza Docker para criar um ambiente de desenvolvimento consistente e isolado. A configuração está dividida em dois arquivos principais, permitindo flexibilidade para diferentes cenários.

### 1.1. Estrutura do Container

- **Servidor Web:** Nginx
- **Processador PHP:** PHP-FPM 8.2
- **Base:** A imagem Docker é baseada em `php:8.2-fpm-alpine`, uma imagem leve e segura.
- **Gerenciamento de Processos:** Embora o Supervisor esteja instalado, o comando de inicialização padrão executa o Nginx e o PHP-FPM diretamente.

### 1.2. Configurações do Docker Compose

Existem dois ambientes principais definidos:

#### a) Ambiente Padrão (`docker-compose.yml`)

- **Acesso:** `http://localhost:8000`
- **Banco de Dados:** Utiliza **PostgreSQL** containerizado. O banco de dados roda em um container separado com persistência de dados via volumes Docker.
- **Propósito:** Ambiente completo para desenvolvimento e testes que necessitam de persistência de dados.

#### b) Ambiente de Desenvolvimento (`docker-compose.dev.yml`)

- **Acesso:** `http://localhost:3001`
- **Banco de Dados:** A conexão com o banco de dados é **desabilitada** por padrão (`DB_CONNECTION=null`).
- **Otimização:** Utiliza um volume nomeado do Docker para a pasta `vendor`, melhorando a performance de I/O em sistemas macOS e Windows.
- **Propósito:** Ideal para desenvolvimento focado no frontend ou em partes da aplicação que não requerem acesso ao banco de dados, utilizando a API Mock.

## 2. Arquitetura do Backend (Laravel)

O backend é uma aplicação Laravel robusta e bem estruturada.

### 2.1. Rotas e Endpoints

O sistema possui uma clara separação entre rotas web, rotas de API e uma API de mock.

- **Rotas Web (`routes/web.php`):**
    - Inclui rotas de autenticação (login, registro), dashboard e perfis de usuário.
    - Utiliza o sistema de autenticação padrão do Laravel, baseado em sessão.
    - Contém rotas para os módulos principais de **Parlamentares** e **Comissões**.

- **Rotas de API (`routes/api.php`):**
    - Define uma **API de Mock** completa sob o prefixo `/mock-api`.
    - Esta API simula todo o comportamento do backend real, facilitando o desenvolvimento do frontend de forma isolada.
    - É controlada pelo `MockApiController`.

- **Rotas de API de Usuário (`routes/web.php`):**
    - Curiosamente, há um grupo de rotas de API para usuários (`/user-api`) dentro do arquivo de rotas web.
    - Possui seus próprios endpoints de autenticação, sugerindo que pode ser consumida por um cliente JavaScript rico (SPA) ou um cliente externo, possivelmente com autenticação baseada em token.

### 2.2. Controle de Acesso e Segurança

- A aplicação implementa um sistema de **Controle de Acesso Baseado em Permissões (RBAC)**.
- O middleware `check.permission` é utilizado para proteger rotas críticas, como `parlamentares.view` ou `comissoes.create`.
- Isso garante que apenas usuários com as permissões corretas possam acessar funcionalidades específicas.

### 2.3. Comunicação com Banco de Dados

- A aplicação está configurada para usar **PostgreSQL** containerizado, proporcionando melhor performance e recursos avançados de banco de dados.
- Os Models do Eloquent (em `app/Models/`) são responsáveis pela interação com o banco de dados. A estrutura exata dos models precisaria ser analisada para um detalhamento maior das tabelas.

## 3. Arquitetura do Frontend

O frontend segue uma abordagem clássica do Laravel, sem a complexidade de um framework JavaScript de grande porte.

### 3.1. Tecnologias

- **Templates:** **Laravel Blade** é o motor de templates principal.
- **Estilização:** **Tailwind CSS** é utilizado para a construção da interface, seguindo uma abordagem de utility-first.
- **JavaScript:** Utiliza JavaScript "puro" (vanilla), com `app.js` como ponto de entrada principal. A biblioteca **Axios** está incluída para realizar requisições HTTP às APIs do backend.

### 3.2. Estrutura de Componentes

- O projeto faz uso extensivo de **Componentes Blade**.
- A UI é organizada em componentes reutilizáveis localizados em `resources/views/components`.
- Os layouts principais da aplicação, como o layout base, estão em `resources/views/components/layouts`, o que mantém o código das views limpo e organizado.

## 4. Resumo e Fluxo de Trabalho

1.  **Ambiente:** O desenvolvedor pode escolher entre o ambiente completo com PostgreSQL (`docker-compose up`) ou o ambiente focado em frontend sem banco de dados (`docker-compose -f docker-compose.dev.yml up`).
2.  **Backend:** A lógica de negócio, regras de acesso e manipulação de dados são controladas pela aplicação Laravel.
3.  **Frontend:** As views são renderizadas no lado do servidor com Blade e estilizadas com Tailwind CSS. A interatividade do lado do cliente é adicionada com JavaScript e as chamadas de API são feitas com Axios, principalmente para a API de mock durante o desenvolvimento. 