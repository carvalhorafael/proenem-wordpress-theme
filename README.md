# Proenem WordPress Theme

Tema WordPress da Proenem.

Este repositório nasce como uma base técnica para desenvolvimento do tema. A arquitetura inicial segue boas práticas do tema WordPress de referência do Executive Signal, mas não reutiliza direção visual, conteúdo ou identidade daquele projeto.

## Requisitos

- Node.js compatível com Vite 7
- npm
- Docker
- Composer disponível no container do `@wordpress/env`

## Desenvolvimento

```bash
npm install
npm run dev
```

URLs locais:

- Site: http://localhost:8898/
- Admin: http://localhost:8898/wp-admin
- Login: http://localhost:8898/wp-login.php

Credenciais locais padrão:

- Usuário: `admin`
- Senha: `password`

## Comandos

```bash
npm run build
npm run i18n
npm run test:static
npm run test:php
npm run test:e2e
npm run validate
```

## Estrutura

- `functions.php`: bootstrap do tema.
- `inc/`: setup WordPress, assets, Vite e helpers.
- `template-parts/`: partes reutilizáveis de templates.
- `src/`: JavaScript e CSS fonte.
- `languages/`: catálogos de tradução.
- `scripts/`: automações de i18n, release e empacotamento.
- `docs/`: decisões e documentação de desenvolvimento.
