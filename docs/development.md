# Desenvolvimento

## Setup

```bash
npm install
npm run dev
```

O comando `npm run dev` inicia o `wp-env`, instala dependências PHP dentro do container e sobe o Vite.

O `wp-env` base usado pelo CI não monta os plugins de conteúdo, porque eles são dependências funcionais opcionais do tema e devem aparecer como aviso no admin quando ausentes.

Para desenvolvimento local com os plugins de conteúdo ativos, copie o exemplo de override:

```bash
cp .wp-env.override.example.json .wp-env.override.json
```

O override monta os plugins de conteúdo usados pelo tema a partir de checkouts locais:

- `../../plugins-wordpress/free-materials`
- `../../plugins-wordpress/testimonials`
- `../../plugins-wordpress/crm-leads-capture`
- `../../plugins-wordpress/sales-pages`

Esses caminhos são relativos a este repositório. Eles mantêm os slugs canônicos `free-materials`, `testimonials`, `crm-leads-capture` e `sales-pages` dentro do WordPress local.

O `@wordpress/env` substitui a lista `plugins` quando `.wp-env.override.json` define esse campo. Por isso, o override também inclui o Theme Check.

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

## Auditoria de performance

Para auditar uma URL publica com PageSpeed Insights:

```bash
npm run perf:pagespeed -- https://www.exemplo.com/
```

O script le `GOOGLE_PSI_API_KEY` do `.env` local quando ela nao estiver exportada no shell.

Para uma auditoria Lighthouse local, primeiro deixe o WordPress local ativo e depois rode:

```bash
npm run perf:lighthouse
```

Os relatórios ficam em `reports/` e não são versionados. O fluxo completo está em `docs/performance.md`.

Use Lighthouse local para iterar antes de publicar o tema. Use PageSpeed em URL publica para confirmar o impacto real depois que a alteracao estiver em staging ou producao.

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

## Releases e atualizacoes do tema

Releases sao publicadas a partir de tags versionadas:

```bash
git tag v0.2.0
git push origin v0.2.0
```

O workflow de release valida se a tag bate com `package.json`, `style.css` e `readme.txt`, roda `npm run validate` e publica o ZIP `dist/proenem-wordpress-theme.zip` na GitHub Release.

O tema usa GitHub Releases para informar atualizacoes ao admin do WordPress. Essa rotina assume que o repositorio e suas releases estao publicamente acessiveis.
