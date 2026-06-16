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
