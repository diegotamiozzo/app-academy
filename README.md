# Optimize Academy

Plataforma de cursos online (EAD) desenvolvida com Laravel e Livewire. O projeto parte do Livewire Starter Kit oficial do Laravel, com autenticação nativa, componentes de arquivo único (single-file) e interface construída com Flux UI.

Repositório: https://github.com/diegotamiozzo/app-academy.git

## Sumário

- [Tecnologias](#tecnologias)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Executando o projeto](#executando-o-projeto)
- [Testes e qualidade de código](#testes-e-qualidade-de-código)
- [Estrutura do projeto](#estrutura-do-projeto)
- [Laravel Boost](#laravel-boost)
- [Roadmap](#roadmap)
- [Fluxo de trabalho com Git](#fluxo-de-trabalho-com-git)
- [Licença](#licença)

## Tecnologias

- Laravel
- Livewire 4 (componentes single-file)
- Flux UI
- Autenticação nativa do Laravel (Fortify)
- Tailwind CSS
- Vite
- Pest (testes)
- Laravel Pint (padronização de código)
- Laravel Boost (diretrizes e MCP para assistentes de IA)

## Requisitos

- PHP 8.2 ou superior (confira o requisito exato da versão do Laravel instalada em `composer.json`)
- Composer 2
- Node.js 20 ou superior e npm
- Banco de dados: MySQL, MariaDB ou PostgreSQL
- Git

Extensões PHP necessárias: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml` e o driver PDO do banco escolhido.

## Instalação

Clone o repositório e entre na pasta do projeto:

```bash
git clone https://github.com/diegotamiozzo/app-academy.git
cd app-academy
```

Instale as dependências do PHP e do Node:

```bash
composer install
npm install
```

Crie o arquivo de ambiente e gere a chave da aplicação:

```bash
cp .env.example .env
php artisan key:generate
```

No Windows (PowerShell), use `copy .env.example .env` no lugar de `cp`.

## Configuração

Abra o arquivo `.env` e ajuste os valores principais:

```env
APP_NAME="Optimize Academy"
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
```

Para usar MySQL ou PostgreSQL, altere as variáveis de banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=optimize_academy
DB_USERNAME=root
DB_PASSWORD=
```

Execute as migrações:

```bash
php artisan migrate
```

Para o envio de e-mails (recuperação de senha, verificação de conta), configure as variáveis `MAIL_*` no `.env`. Em desenvolvimento, o valor padrão `MAIL_MAILER=log` grava as mensagens em `storage/logs`.

## Executando o projeto

Em desenvolvimento, um único comando sobe o servidor, a fila, os logs e o Vite:

```bash
composer run dev
```

A aplicação ficará disponível em `http://localhost:8000`.

Caso prefira rodar cada serviço separadamente:

```bash
php artisan serve
npm run dev
```

Para gerar os assets de produção:

```bash
npm run build
```

## Testes e qualidade de código

Executar a suíte de testes:

```bash
php artisan test
```

Padronizar o código com o Laravel Pint:

```bash
./vendor/bin/pint
```

Para apenas verificar, sem alterar arquivos:

```bash
./vendor/bin/pint --test
```

### Componentes Livewire

Este projeto usa componentes de arquivo único: a lógica em PHP e o template Blade ficam no mesmo arquivo, dentro de `resources/views/components`. Para criar um novo componente:

```bash
php artisan make:livewire nome-do-componente
```

### Identidade visual

A logo da marca está disponível como componentes Blade em SVG, com `currentColor`, o que permite adaptar a cor ao tema claro e escuro:

```blade
<x-app-logo-icon class="size-8 text-black dark:text-white" />
<x-app-logo-full class="h-10 w-auto text-black dark:text-white" />
```

## Laravel Boost

O projeto utiliza o Laravel Boost para fornecer diretrizes, skills e um servidor MCP aos assistentes de IA. Para configurar ou reconfigurar:

```bash
php artisan boost:install
```

Os arquivos gerados (como `CLAUDE.md`, `AGENTS.md` e as pastas de skills) descrevem as convenções do projeto e das versões instaladas.


## Fluxo de trabalho com Git

Branch principal: `main`. Para novas funcionalidades, crie uma branch a partir dela:

```bash
git checkout -b feature/nome-da-funcionalidade
```

Faça commits pequenos e descritivos, rode os testes e o Pint antes de enviar e abra um Pull Request para a `main`:

```bash
php artisan test
./vendor/bin/pint
git push -u origin feature/nome-da-funcionalidade
```

Primeiro envio de um projeto local para o repositório:

```bash
git init
git add .
git commit -m "Commit inicial"
git branch -M main
git remote add origin https://github.com/diegotamiozzo/app-academy.git
git push -u origin main
```

## Licença

A definir. Adicione um arquivo `LICENSE` na raiz do projeto quando a licença for escolhida.