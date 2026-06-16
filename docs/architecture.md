# Arquitetura

O tema Proenem e uma camada de apresentacao WordPress.

## Principios

- O tema adapta WordPress para a experiencia Proenem.
- Regras de negocio duraveis devem ficar fora do tema, preferencialmente em plugins.
- A identidade visual ainda nao esta definida neste scaffold; os estilos atuais sao fundacionais e substituiveis.
- Todo texto visivel em PHP deve usar o text domain `proenem-wordpress-theme`.

## Organizacao

- `functions.php`: carrega constantes e modulos.
- `inc/setup.php`: theme supports, menus, block styles e sidebars.
- `inc/assets.php`: carregamento de assets front-end e editor.
- `inc/vite.php`: integracao com Vite em desenvolvimento e producao.
- `inc/template-tags.php`: helpers compartilhados por templates.
- `template-parts/`: markup reutilizavel.
- `src/`: CSS e JavaScript fonte.
- `languages/`: POT/PO/MO.
- `scripts/`: automacoes de pacote, release e i18n.

## Referencia

O tema Executive Signal foi usado como referencia de arquitetura e qualidade, nao como referencia visual.
