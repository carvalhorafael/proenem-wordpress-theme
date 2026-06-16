# Desenvolvimento

## Setup

```bash
npm install
npm run dev
```

O comando `npm run dev` inicia o `wp-env`, instala dependências PHP dentro do container e sobe o Vite.

## GitHub Packages

Para instalar os pacotes do design system da Proenem, copie `.npmrc.example` para `.npmrc` e configure um token GitHub com acesso a `@carvalhorafael/*`.

URLs locais:

- Site: http://localhost:8898/
- Admin: http://localhost:8898/wp-admin
- Testes: http://localhost:8899/

## Validação

```bash
npm run build
npm run i18n
npm run test:static
npm run test:php
npm run test:e2e
npm run validate
```

## Internacionalização

Use sempre o text domain `proenem-wordpress-theme`.

Quando alterar strings traduzíveis:

```bash
npm run i18n
```

O idioma base inicial é `pt_BR`.

## Empacotamento

```bash
npm run theme:zip
npm run theme:validate-zip
```

O ZIP final fica em `dist/proenem-wordpress-theme.zip`.
