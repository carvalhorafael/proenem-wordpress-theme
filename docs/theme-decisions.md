# Decisoes do tema

Este arquivo registra decisoes que afetam arquitetura, fronteiras de responsabilidade ou contratos duraveis do tema.

## 2026-06-16: Scaffold inicial

- O tema comeca como tema classico WordPress, com templates PHP e `theme.json`.
- Vite compila os assets de `src/` para `assets/dist`.
- `@wordpress/env` e o ambiente local padrao.
- O tema usa `proenem-wordpress-theme` como slug tecnico e text domain.
- Nenhuma direcao visual definitiva do Proenem foi inventada nesta etapa.

## 2026-06-16: Consumo do design system da Proenem

- O tema consome `@carvalhorafael/proenem-tokens`, `@carvalhorafael/proenem-css` e `@carvalhorafael/proenem-web` como bibliotecas versionadas.
- O pacote React `@carvalhorafael/proenem-ui` fica fora do tema enquanto não houver necessidade explícita de renderização React no WordPress.
- O acesso aos pacotes privados deve ser feito via GitHub Packages, usando `.npmrc.example` como modelo local.

## 2026-06-16: Template de home LP ProEnem

- Contexto: a primeira home do tema foi implementada a partir do frame Figma `LP---ProEnem / Nova LP / Proposta`.
- Decisao: criar um template de pagina dedicado (`page-templates/home.php`) para a LP, mantendo o tema como camada de apresentacao WordPress.
- Design system: o tema continua importando `@carvalhorafael/proenem-tokens` e `@carvalhorafael/proenem-css`; a LP usa CSS local para compor a direcao visual especifica entregue no Figma.
- Adaptacao local: componentes de LP como hero editorial, cards de pilares, grade de planos, showcase de plataforma, FAQ e footer de campanha ficaram no tema ate que existam contratos portaveis equivalentes no design system.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#10`; tema `carvalhorafael/proenem-wordpress-theme#2`.
- Criterio de remocao: quando o design system publicar patterns/classes/renderers equivalentes para essas secoes, substituir a marcacao local pelos contratos publicados e remover o CSS local correspondente.

## 2026-06-23: Home consumindo patterns publicados do design system

- Contexto: o design system publicou novos contratos em `@carvalhorafael/proenem-css@0.2.0` e `@carvalhorafael/proenem-web@0.2.0` para as secoes da LP.
- Decisao: refatorar `page-templates/home.php` para emitir as classes publicas `pen-*` dos patterns publicados, espelhando a estrutura HTML dos renderers do pacote `web` sem adicionar React ao tema.
- Remocao de workaround: o CSS local da LP foi reduzido para ajustes de integracao WordPress; layout, cores, cards, hero, navbar, FAQ, pricing, audience e footer passam a vir do design system.
- Tracking: manter `carvalhorafael/proenem-wordpress-theme#2` aberto apenas para qualquer cola residual do tema e fechar quando nao houver mais adaptacao local ligada a LP.

## 2026-07-03: Releases e atualizacao via GitHub Releases

- Contexto: o tema precisa publicar pacotes instalaveis e permitir que o admin do WordPress detecte novas versoes.
- Decisao: usar tags `vX.Y.Z` para disparar o workflow de release, validar a versao declarada no tema e publicar `proenem-wordpress-theme.zip` na GitHub Release.
- Atualizacao: o tema registra `Update URI` para o repositorio GitHub e consulta a ultima release para montar o payload de update do WordPress.
- Distribuicao: a rotina assume que o repositorio e suas releases estao publicamente acessiveis; o repositorio publico nao concede licenca de uso alem do que esta declarado em `LICENSE.md`.
