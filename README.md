# Proenem WordPress Theme

Tema WordPress da Proenem.

Este repositório nasce como uma base técnica para desenvolvimento do tema. A arquitetura inicial segue boas práticas do tema WordPress de referência do Executive Signal, mas não reutiliza direção visual, conteúdo ou identidade daquele projeto.

## Requisitos

- Node.js compatível com Vite 7
- npm
- Docker
- Composer disponível no container do `@wordpress/env`
- Token GitHub com acesso aos pacotes privados `@carvalhorafael/*` no GitHub Packages

Copie `.npmrc.example` para `.npmrc` e informe o token localmente:

```ini
@carvalhorafael:registry=https://npm.pkg.github.com
//npm.pkg.github.com/:_authToken=SEU_TOKEN_GITHUB
```

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
npm run perf:pagespeed -- https://www.exemplo.com/
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
- `docs/performance.md`: fluxo de auditoria PageSpeed/Lighthouse.

## Design system

O tema consome os pacotes publicados da Proenem como bibliotecas versionadas:

- `@carvalhorafael/proenem-tokens`
- `@carvalhorafael/proenem-css`
- `@carvalhorafael/proenem-web`

O pacote React `@carvalhorafael/proenem-ui` não é dependência deste tema.
