# Performance, SEO e acessibilidade

Este documento define o formato de trabalho para auditorias de performance do tema Proenem.

## Objetivo

O tema precisa ter uma rotina capaz de:

- medir uma URL publica instalada em WordPress real;
- gerar um baseline comparavel antes de uma rodada de otimizacao;
- separar problemas do tema de problemas de hospedagem, cache, plugins, conteudo editorial ou terceiros;
- orientar correcoes em issues ou PRs pequenos;
- evoluir para uma etapa automatizada da suite quando os limites estiverem estaveis.

## Camadas de auditoria

O modelo validado para este projeto e:

1. usar Lighthouse local para iterar sem publicar o tema;
2. usar PageSpeed Insights em URL publica para confirmar o impacto real em staging ou producao;
3. usar Playwright/axe para proteger regressao funcional e acessibilidade automatizada.

Lighthouse local e PageSpeed publico podem divergir nos numeros absolutos. Isso e esperado: localhost nao tem a mesma rede, CDN, cache, robots.txt, scripts de terceiros e configuracao do ambiente publico. O que importa durante a implementacao e a tendencia dos gargalos do tema, principalmente LCP, peso de imagens, render-blocking, dimensoes de imagens, contraste e ARIA.

### 1. URL publica: PageSpeed Insights e CrUX

Use para diagnosticar a pagina que usuarios e Google enxergam.

```bash
npm run perf:pagespeed -- https://www.exemplo.com/
```

Tambem e possivel informar multiplas URLs:

```bash
GOOGLE_PSI_API_KEY=... npm run perf:pagespeed -- https://www.exemplo.com/ https://www.exemplo.com/blog/
```

O script roda mobile e desktop por padrao, consulta as categorias `performance`, `accessibility`, `best-practices` e `seo`, e grava JSON bruto mais resumo Markdown em `reports/performance/`.

A chave `GOOGLE_PSI_API_KEY` deve ficar fora do repositorio. Sem chave, a API pode funcionar com limite reduzido, mas o fluxo padrao do projeto assume chave configurada no ambiente local ou no agente MCP.

O script le `.env` automaticamente quando `GOOGLE_PSI_API_KEY` ainda nao estiver exportada no shell.

### 2. Local advisory: Lighthouse CI

Use para medir uma instalacao local do tema durante o desenvolvimento.

```bash
npm run dev
npm run perf:lighthouse
```

O arquivo `lhci.config.cjs` audita `http://localhost:8898/`, faz 3 execucoes e grava os relatórios em `reports/lighthouse-ci/`.

Este e o comando padrao para testar uma otimizacao antes de publicar o tema. Ele nao substitui o PageSpeed publico, mas evita atualizar staging/producao a cada tentativa.

Hoje os limites sao `warn`, nao `error`. Isso e proposital: a camada local serve para observar tendencia e evitar decisoes baseadas em uma unica execucao instavel. Depois de alguns baselines reais, os limites podem subir e parte deles pode entrar na suite obrigatoria.

O `@lhci/cli` e executado via `npx` pinado em vez de ficar como devDependency. A versao atual traz dependencias transitivas antigas quando instalada no lock do projeto; antes de promover essa etapa para CI obrigatorio, reavalie a versao do CLI e o resultado de `npm audit`.

### 3. Regressao funcional: Playwright e axe

O Playwright continua sendo a camada de verificacao local para renderizacao, console, navegacao e acessibilidade automatizada. Quando uma otimizacao mexer em markup, interacao ou carregamento de assets, adicione ou ajuste testes em `tests/e2e/`.

## Formato do sprint

1. Escolher URLs-alvo e contexto:
   - home;
   - pagina de venda Elementor;
   - pagina de blog ou material gratuito, quando existir no site testado.
2. Rodar `npm run perf:pagespeed` nas URLs publicas, mobile e desktop.
3. Rodar `npm run perf:lighthouse` em `localhost:8898` para criar um baseline local antes da implementacao.
4. Classificar cada item encontrado:
   - `theme`: markup, enqueue, CSS, JS, imagens do tema, fontes ou estrutura do template;
   - `content`: imagem, video, embed ou conteudo administrado no WordPress;
   - `plugin`: Elementor, plugin de captura, analytics, tag manager ou outro plugin;
   - `hosting`: TTFB, cache, compressao, CDN ou headers;
   - `design-system`: gap que deve ser resolvido nos pacotes da Proenem.
5. Corrigir primeiro itens `theme` de alto impacto e baixo risco.
6. Repetir `npm run perf:lighthouse` localmente ate a tendencia melhorar.
7. Validar uma vez em URL publica com `npm run perf:pagespeed` quando a mudanca estiver pronta para staging/producao.
8. Quando a correcao exigir workaround local por falta no design system, abrir as issues obrigatorias descritas em `AGENTS.md`.
9. Registrar no PR:
   - URL testada;
   - data e estrategia;
   - scores antes/depois;
   - itens que ficaram fora do tema e por que.

## Como nao perder contexto

Os arquivos em `reports/` nao sao versionados. Eles servem como evidencia local temporaria para analise, nao como documentacao permanente.

Para preservar o contexto de uma rodada de performance:

1. mantenha o JSON/Markdown gerado em `reports/performance/` enquanto estiver trabalhando no sprint;
2. copie para a issue ou PR apenas o resumo necessario:
   - URL;
   - data da auditoria;
   - estrategia (`mobile` ou `desktop`);
   - scores principais;
   - principais oportunidades;
   - classificacao de responsabilidade (`theme`, `content`, `plugin`, `hosting`, `third-party`, `design-system`);
   - decisao tomada para cada item;
3. se a otimizacao gerar uma mudanca no tema, inclua o antes/depois no PR;
4. se o item nao pertencer ao tema, registre como recomendacao operacional e nao esconda o problema em workaround local;
5. se houver gap do design system, abra e linke as issues obrigatorias antes de considerar a tarefa pronta.

Formato recomendado para PRs e comentarios de sprint:

```md
## Performance audit

- URL:
- Data:
- Estratégia:
- Ferramenta:

| Métrica | Antes | Depois | Observação |
| --- | ---: | ---: | --- |
| Performance |  |  |  |
| Accessibility |  |  |  |
| Best Practices |  |  |  |
| SEO |  |  |  |
| LCP |  |  |  |
| CLS |  |  |  |
| TBT |  |  |  |

## Classificação

| Achado | Responsável | Decisão |
| --- | --- | --- |
|  | theme/content/plugin/hosting/third-party/design-system |  |
```

## Criterios para entrar na suite

A suite obrigatoria nao deve depender de uma URL publica externa por padrao. A entrada deve acontecer em etapas:

1. Manter `perf:pagespeed` como ferramenta manual de sprint.
2. Usar `perf:lighthouse` como advisory local com `warn`.
3. Depois de pelo menos 3 baselines estaveis, adicionar um job separado de CI para Lighthouse local.
4. Promover apenas limites confiaveis para `error`, com margem para variacao:
   - acessibilidade e SEO podem ser mais rigidos;
   - performance deve comecar com limites conservadores;
   - auditorias afetadas por rede, cache externo ou terceiros devem permanecer advisory.

## MCP recomendado

Para trabalho assistido por Codex, o MCP `ruslanlap/pagespeed-insights-mcp` e a melhor opcao avaliada ate agora porque combina PageSpeed Insights com Chrome UX Report e ferramentas especificas para imagens, JavaScript, render-blocking, terceiros e comparacao.

Configuracao sugerida fora do repositorio:

```toml
[mcp_servers.pagespeed-insights]
command = "npx"
args = ["-y", "pagespeed-insights-mcp"]
env = { GOOGLE_API_KEY = "sua-chave-aqui", NODE_ENV = "production" }
```

O MCP e opcional. O script `perf:pagespeed` existe para que a auditoria basica continue disponivel mesmo sem o MCP instalado.
