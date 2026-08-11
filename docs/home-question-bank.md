# Contrato editorial do banco de questões

Este documento registra a fonte e a regra editorial do volume de questões apresentado na home da Proenem.

## Claim global

- Claim publicado: `Mais de 60 mil questões`.
- Fonte: banco público em `https://estude.proenem.com.br/treino/questoes`.
- Referência verificada em 2026-08-10: 65.461 questões encontradas.
- Regra de atualização: usar um limiar arredondado inferior ao total público. Antes de elevar o limiar ou se o catálogo ficar abaixo de 60 mil, verificar novamente a fonte pública e atualizar todas as superfícies da home.

## Cards de disciplinas

Os cards são atalhos editoriais para as páginas aprovadas da plataforma. Eles exibem somente disciplina e área do conhecimento.

Contagens exatas de questões e aulas não fazem parte do contrato do tema porque variam com o catálogo e não possuem uma fonte estável consumida pelo WordPress. As URLs existentes foram confirmadas como os destinos corretos em 2026-08-10 e não devem ser reconstruídas a partir dos labels.

## Superfícies sincronizadas

O contrato deve permanecer equivalente em:

- `page-templates/home.php`;
- defaults do widget em `inc/class-proenem-elementor-home-widget-base.php`;
- `docs/elementor/proenem-home.json`;
- dados Elementor persistidos, atualizados por `scripts/sync-home-question-bank.php`.
