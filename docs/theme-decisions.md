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

## 2026-07-07: Widgets Elementor controlados para paginas de vendas

- Contexto: o time de marketing precisa montar paginas de vendas com mais autonomia do que os templates PHP atuais permitem.
- Decisao: iniciar uma biblioteca opcional de widgets Elementor controlados pela Proenem, com nomes tecnicos prefixados por `pro_` e titulos visiveis prefixados por `Pro`.
- Escopo inicial: `Pro Navbar`, `Pro Hero de Oferta`, `Pro Contador de Oferta`, `Pro Grade de Planos`, `Pro Card de Plano`, `Pro Lista de Beneficios`, `Pro Comparativo de Planos`, `Pro CTA` e `Pro FAQ`.
- Fronteira: o tema registra os widgets apenas quando Elementor esta ativo; o tema nao deve falhar nem exigir Elementor para templates WordPress comuns.
- Design system: a primeira versao usa HTML/CSS local no tema para validar a experiencia de edicao. O contrato duravel deve migrar para pacotes compartilhados quando houver componentes, patterns ou renderers equivalentes no design system da Proenem.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#28`; tema `carvalhorafael/proenem-wordpress-theme#27`.

## 2026-07-08: Widgets Elementor controlados para a home

- Contexto: o time precisa editar textos simples da home sem depender de alteracao de codigo, preservando a identidade e o comportamento da LP atual.
- Decisao: criar um widget Elementor por secao inteira da home, com defaults equivalentes ao template `page-templates/home.php` e controles focados em texto, imagens, CTAs e listas editaveis.
- Escopo inicial: `Pro Home Hero`, `Pro Home Barra de Acao`, `Pro Home Marquee`, `Pro Home Pilares`, `Pro Home Prova Social`, `Pro Home Dores`, `Pro Home Plataforma`, `Pro Home Banco de Questoes`, `Pro Home Planos`, `Pro Home Depoimentos`, `Pro Home Escolas`, `Pro Home CTA Final` e `Pro Home FAQ`.
- Fronteira: o Elementor fica responsavel pela composicao e edicao simples; o tema continua controlando markup, classes, assets, comportamento progressivo e integracao com o design system.
- Design system: os widgets reutilizam os contratos visuais `pen-*` e a cola local ja existente para a home. A migracao para contratos compartilhados segue o tracking da LP e dos widgets Elementor.

## 2026-08-04: Conteudo complementar e CTA no hero da home

- Contexto: a revisao de copy da home exige subtitulo, apoio e CTA no primeiro bloco editorial.
- Decisao: posicionar o subtitulo dentro do hero fotografico e estender localmente a barra de acao com texto de apoio no bloco rosa e um CTA no bloco amarelo.
- Elementor: os widgets `Pro Home Hero` e `Pro Home Barra de Acao` recebem controles equivalentes aos novos campos e mantem paridade com `page-templates/home.php`.
- Adaptacao local: as classes `pro-home-hero-action-bar__*` ficam em `src/styles/theme.css` ate existir um contrato portavel para essa composicao.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#29`; tema `carvalhorafael/proenem-wordpress-theme#86`.
- Criterio de remocao: atualizar os pacotes publicados, migrar template e widget para o novo contrato e remover o CSS local quando o design system cobrir a composicao.

## 2026-08-04: Texto de apoio na prova social da home

- Contexto: a nova copy inclui uma frase curta entre o numero de aprovados e os logos das universidades.
- Decisao: adicionar o apoio ao template e ao widget `Pro Home Prova Social`, mantendo o titulo como prova principal e o texto novo em escala secundaria.
- Adaptacao local: a classe `pro-home-proof-support` controla temporariamente tipografia, largura de leitura e espacamento porque o pattern publicado nao possui um contrato de apoio secundario.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#30`; tema `carvalhorafael/proenem-wordpress-theme#87`.
- Criterio de remocao: atualizar o pacote `@carvalhorafael/proenem-css`, migrar para a classe compartilhada e remover o seletor local.

## 2026-08-06: Listas informativas no platform showcase

- Contexto: o pattern publicado aplica aparencia e cursor de item interativo a qualquer `li` dentro de `.pen-platform-showcase__panel`, incluindo os beneficios sem acao exibidos na tela da plataforma.
- Decisao: manter as abas do menu esquerdo como controles reais e neutralizar apenas a affordance falsa da lista `.pro-home-platform-mock__bullets`.
- Adaptacao local: `src/styles/theme.css` remove background, raio, sombra, cursor de clique e altura minima herdados nos itens informativos.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#35`; tema `carvalhorafael/proenem-wordpress-theme#101`.
- Criterio de remocao: atualizar `@carvalhorafael/proenem-css` para a versao que restringir o estilo interativo a itens explicitos e remover o reset local apos validacao no navegador.

## 2026-08-04: Grade responsiva com quatro dores na home

- Contexto: a nova estrutura inclui o card `Comeca e abandona`, levando a secao de dores de tres para quatro itens.
- Decisao: usar quatro colunas no desktop, duas em larguras intermediarias e uma no mobile, preservando os cards e tons existentes.
- Adaptacao local: a classe `pro-home-pain-grid--four` complementa temporariamente o contrato `pen-feature-grid`; a frase final tambem recebe quebra responsiva no mobile para evitar overflow.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#31`; tema `carvalhorafael/proenem-wordpress-theme#88`.
- Criterio de remocao: atualizar o pacote `@carvalhorafael/proenem-css`, migrar para a variacao compartilhada e remover a classe local.

## 2026-08-04: Escala tipografica semantica da home

- Contexto: textos com a mesma funcao editorial usavam escalas fluidas diferentes, produzindo tamanhos inconsistentes entre secoes e larguras de tela.
- Decisao: padronizar a home por papel tipografico usando os tokens publicados: auxiliar em `--pen-text-sm`, corpo em `--pen-text-base`, apoio em `--pen-text-lg`, titulo de card em `1.5rem` e titulo de secao entre `2rem` e `3rem`.
- Excecoes: hero, precos, selos, badges e marquees permanecem com direcao tipografica promocional propria.
- Design system: a composicao reutiliza os tokens existentes e nao cria um novo token ou contrato local.

## 2026-08-04: Linha de posicionamento na plataforma da home

- Contexto: a nova copy precisa diferenciar a plataforma de uma biblioteca de videos antes da apresentacao dos recursos.
- Decisao: inserir uma faixa curta entre o cabecalho e as abas, com a promessa do proximo passo em destaque e controles equivalentes no widget Elementor.
- Adaptacao local: a classe `pro-home-platform-guard` usa apenas tokens publicados, mas sua composicao ainda nao possui um pattern compartilhado apropriado; o callout editorial existente nao corresponde ao contexto da LP.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#32`; tema `carvalhorafael/proenem-wordpress-theme#89`.
- Criterio de remocao: atualizar os pacotes publicados, migrar template e widget para o novo contrato e remover `pro-home-platform-guard` quando o design system cobrir a composicao.

## 2026-08-04: Plano gratuito na home

- Contexto: a nova estrutura torna o freemium um degrau visivel antes dos dois planos pagos.
- Decisao: apresentar Gratis, Metodo PRO e Pro Medicina nessa ordem; o Metodo PRO continua sendo a unica oferta visualmente dominante.
- Adaptacao local: a classe `is-free` complementa temporariamente `pen-plan-card`, que nao possui uma variacao gratuita no contrato publicado.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#33`; tema `carvalhorafael/proenem-wordpress-theme#90`.
- Criterio de remocao: atualizar os pacotes publicados, migrar template e widget para a variacao compartilhada e remover `is-free` quando o design system cobrir o plano gratuito.
