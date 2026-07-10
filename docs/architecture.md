# Arquitetura

O tema Proenem é uma camada de apresentação WordPress.

## Principios

- O tema adapta WordPress para a experiência da Proenem.
- Regras de negócio duráveis devem ficar fora do tema, preferencialmente em plugins.
- A identidade visual deve vir dos pacotes publicados do design system da Proenem.
- Todo texto visível em PHP deve usar o text domain `proenem-wordpress-theme`.

## Design system

O tema consome pacotes publicados do repositório `carvalhorafael/proenem-design-system-brand-guide`:

- `@carvalhorafael/proenem-tokens`: tokens CSS.
- `@carvalhorafael/proenem-css`: classes e padrões CSS compartilhados.
- `@carvalhorafael/proenem-web`: contratos HTML e comportamentos progressivos para consumidores sem React.

Não use `@carvalhorafael/proenem-ui` enquanto o tema não renderizar React. Componentes React devem continuar fora do tema WordPress até haver decisão explícita.

## Organizacao

- `functions.php`: carrega constantes e módulos.
- `inc/setup.php`: theme supports, menus, block styles e sidebars.
- `inc/assets.php`: carregamento de assets front-end e editor.
- `inc/vite.php`: integração com Vite em desenvolvimento e produção.
- `inc/template-tags.php`: helpers compartilhados por templates.
- `template-parts/`: markup reutilizável.
- `src/`: CSS e JavaScript fonte.
- `languages/`: POT/PO/MO.
- `scripts/`: automações de pacote, release e i18n.
- `docs/performance.md`: arquitetura de auditoria PageSpeed/Lighthouse e rotina de otimizacao.
- `lhci.config.cjs`: configuracao advisory para Lighthouse CI local.

## Performance

A performance e tratada em camadas:

- PageSpeed Insights para URLs publicas e baselines de sprint.
- Lighthouse CI local em modo advisory para medir tendencia durante desenvolvimento.
- Playwright e axe para regressao funcional e acessibilidade automatizada.

O fluxo completo esta documentado em `docs/performance.md`. Auditorias geram arquivos em `reports/`, que nao devem ser versionados.

## Referencia

O tema Executive Signal foi usado como referência de arquitetura e qualidade, não como referência visual.
