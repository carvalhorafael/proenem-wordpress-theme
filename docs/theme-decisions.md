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

## 2026-08-06: Cor do numero no selo dos pilares

- Contexto: o variant vermelho de `pen-step-card` define texto claro e essa cor e herdada pelo numero 03 dentro do selo amarelo, diferente dos demais pilares.
- Decisao: fixar localmente `var(--pen-color-ink)` no numero dos quatro cards para manter contraste e consistencia entre variants.
- Adaptacao local: `src/styles/theme.css` restringe o override aos selos numericos do slider de pilares da home.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#36`; tema `carvalhorafael/proenem-wordpress-theme#102`.
- Criterio de remocao: atualizar `@carvalhorafael/proenem-css` para a versao que definir a cor ink no selo do `pen-step-card` e remover o override local.

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

## 2026-08-08: Detalhes comerciais nos cards de planos

- Contexto: a home precisa distinguir o valor da parcela e a garantia antes do clique no checkout.
- Decisao: centralizar a secao de preco em fluxo normal entre a lista de beneficios e o CTA de cada plano; nos planos pagos, posicionar a garantia imediatamente abaixo do CTA.
- Adaptacao local: as classes `pro-home-plan-card__price`, `pro-home-plan-card__price-amount` e `pro-home-plan-card__trust` complementam temporariamente `pen-plan-card`.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#37`; tema `carvalhorafael/proenem-wordpress-theme#128`.
- Criterio de remocao: atualizar os pacotes publicados, migrar template, widget e JSON para o contrato compartilhado e remover as classes e fallbacks locais.

## 2026-08-10: Metodo PRO Avancado substitui a oferta de Medicina

- Contexto: a definicao comercial provisoria remove o plano Pro Medicina da home e organiza as ofertas pagas como Metodo PRO e Metodo PRO Avancado.
- Decisao: manter a mesma hierarquia visual e o contrato de preco existente, alterando apenas nome, resumo, beneficios, CTA e textos relacionados. O plano avancado nao recebe identidade visual medica.
- Paridade: template PHP, defaults e compatibilidade do widget Elementor, JSON de importacao e conteudo persistido devem exibir o mesmo contrato editorial.
- Compatibilidade: o renderer atualiza apenas valores persistidos que ainda sejam identicos ao contrato antigo, sem sobrescrever customizacoes editoriais posteriores.

## 2026-08-13: Menu WordPress como fonte dos destinos da navbar

- Contexto: o fallback de conversao substituia URLs vazias ou `#` com base no label do item, fazendo o HTML divergir da configuracao salva no menu `primary`.
- Decisao: preservar literalmente no renderer as URLs dos itens principais e subitens do menu WordPress, inclusive `#`.
- Persistencia: `scripts/sync-home-conversion.php` deixa de reescrever o menu `primary` e permanece restrito a dados Elementor da home.
- Compatibilidade: o comportamento mobile de itens com submenu continua cancelando a navegacao e alternando o dropdown; no desktop, o navegador recebe o destino configurado no WordPress.

## 2026-08-12: Oferta atual limitada a Gratis e Metodo PRO

- Contexto: a terceira oferta sera retomada no futuro, mas a home atual deve apresentar somente os planos Gratis e Metodo PRO.
- Decisao: remover o Metodo PRO Avancado do template PHP, dos defaults Elementor e do modelo de importacao; a grade passa a ter duas colunas centralizadas no desktop e uma coluna no mobile.
- Persistencia: o renderer nao exibe Metodo PRO Avancado nem o nome legado Pro Medicina em paginas Elementor ainda nao sincronizadas. `scripts/sync-home-plans.php` remove somente essas ofertas conhecidas e atualiza a resposta conhecida da FAQ, preservando outras customizacoes editoriais.
- Reativacao futura: o repeater generico e o destino interno `advanced` permanecem disponiveis, mas a oferta so deve voltar mediante uma nova decisao comercial e atualizacao coordenada dos tres caminhos de renderizacao.
- Design system: a mudanca reutiliza o contrato existente e a adaptacao local ja acompanhada pelas issues do plano gratuito; nao cria um novo gap.

## 2026-08-10: Jornada de conversao e CTA mobile persistente

- Contexto: labels de cadastro e exploracao da home levavam para `#planos`, o menu WordPress mantinha itens com destino `#` e o mobile atravessava varias dobras sem uma acao de conversao visivel.
- Decisao: documentar a taxonomia em `docs/home-cta-map.md`, direcionar cadastro para `/signup`, exploracao para o banco de questoes e manter `#planos` apenas para consulta de ofertas.
- Persistencia: `scripts/sync-home-conversion.php` atualizava de forma idempotente o menu principal e paginas Elementor que contenham os widgets da home. A parcela desta decisao que reescrevia o menu foi substituida pela decisao de 2026-08-13; a sincronizacao permanece apenas para dados Elementor.
- Sticky: o host da navbar permanece no fluxo com `position: sticky`, evitando salto de layout em template PHP, header convencional e widget Elementor.
- Adaptacao local: `pro-mobile-persistent-action` compoe o botao publicado dentro de uma barra fixa de largura total no rodape mobile, exibida apos 600 px, com safe area e reposicionamento temporario do suporte flutuante enquanto a barra estiver visivel.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#38`; tema `carvalhorafael/proenem-wordpress-theme#110`.
- Criterio de remocao: atualizar `@carvalhorafael/proenem-css` e `@carvalhorafael/proenem-web`, migrar para o pattern publicado e remover markup, CSS e JavaScript locais da barra persistente.

## 2026-08-10: Prova social verificavel na home

- Contexto: a faixa de aprovados mantinha imagens e instituicoes sem identificacao individual verificavel, enquanto o claim de mais de 40 mil aprovados nao possuia fonte, periodo e metodologia confirmados.
- Decisao: o tema renderiza a faixa somente com registros que o plugin Testimonials declare elegiveis para a home; sem registros elegiveis, a secao inteira permanece ausente.
- Persistencia: `docs/elementor/proenem-home.json` nao inclui mais fotos anonimas nem configuracao editorial duplicada de logos no widget. O renderer compartilhado preserva as seis logos institucionais como assets de apresentacao, e `scripts/sync-home-proof.php` mantem template e paginas Elementor alinhados.
- Responsabilidade: identificacao, curso, instituicao, ano, evidencia, verificacao, consentimento e selecao editorial pertencem ao plugin, para sobreviver a trocas de tema.
- Excecao editorial: por orientacao explicita, o claim `+ de 40.000 aprovados em universidades publicas` e o apoio original permanecem na faixa. A origem, o periodo e a metodologia do numero continuam sem documentacao no repositorio e precisam ser confirmados para satisfazer integralmente a issue #111.
- Adaptacao local: as classes `pro-home-proof-students`, `pro-home-proof-student`, `pro-home-proof-student__media` e `pro-home-proof-student__caption` complementam temporariamente o pattern publicado com grade variavel, midia de proporcao estavel e identificacao individual legivel.
- Tracking: design system `carvalhorafael/proenem-design-system-brand-guide#39`; tema `carvalhorafael/proenem-wordpress-theme#132`.
- Criterio de remocao: atualizar os pacotes publicados, adotar as classes compartilhadas de figura e legenda e remover os seletores locais quando o design system cobrir o contrato.

## 2026-08-10: Depoimentos reais no carrossel da home

- Contexto: o carrossel inferior da home mantinha quatro relatos, nomes, descricoes e imagens ficticios duplicados no template PHP e no widget Elementor.
- Decisao: o carrossel consome somente registros que o plugin Testimonials declare elegiveis para a home e que possuam relato ou excerto nao vazio. Sem registros elegiveis, a secao inteira permanece ausente.
- Fonte canonica: publicacao, imagem destacada, nome, curso, instituicao, ano, evidencia, verificacao, consentimento, selecao editorial e relato pertencem ao CPT do plugin. O tema limita, ordena e apresenta os registros.
- Elementor: o widget permite selecionar IDs elegiveis ou, sem selecao, usa os registros mais recentes. `scripts/sync-home-testimonials.php` remove os repeaters legados das paginas persistidas e preserva a copia e o link editorial.
- Fallbacks: o ano e opcional e so aparece quando preenchido; curso e instituicao sao obrigatorios pelo contrato do plugin; nenhum texto de aluno e inventado pelo tema.
- Testes: PHPUnit protege a ausencia de fallback ficticio e o modelo Elementor; Playwright protege os dois estados validos, os controles do carrossel e a ausencia de overflow.

## 2026-08-10: Volume editorial do banco de questoes

- Contexto: a home misturava o claim global de mais de 50 mil questoes com contagens exatas e quantidades de aulas sem fonte estavel nos cards de disciplinas.
- Decisao: publicar o limiar arredondado `Mais de 60 mil questoes`, sustentado pelo banco publico com 65.461 registros em 2026-08-10, e remover dos cards todas as contagens exatas de questoes e aulas.
- Responsabilidade: os cards permanecem como atalhos editoriais com as URLs aprovadas; o tema nao passa a calcular nem sincronizar dados volateis do catalogo da plataforma.
- Persistencia: `docs/home-question-bank.md` registra a fonte e a regra de atualizacao, enquanto `scripts/sync-home-question-bank.php` atualiza a copy e remove metadados legados das paginas Elementor persistidas.

## 2026-08-11: Areas de toque moveis da home

- Contexto: a validacao da home em 390x844 encontrou controles e links prioritarios menores que 44x44 CSS px nos pilares, na paginacao movel da plataforma e no rodape.
- Gap do design system: `@carvalhorafael/proenem-css@0.3.0` publica os controles de `pen-pillars-slider__control` com 36x36 px e nao garante altura minima para as acoes de `pen-site-footer`.
- Decisao: complementar localmente os contratos em `src/styles/theme.css`, preservando icones compactos dentro de alvos de pelo menos 44x44 px e usando foco visivel sem alteracao de geometria.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#114`; design system `carvalhorafael/proenem-design-system-brand-guide#40`.
- Criterio de remocao: atualizar os pacotes para a versao que resolver a issue do design system, remover os seletores locais de tamanho/foco e confirmar novamente a cobertura E2E em 390x844.

## 2026-08-21: Label longo no CTA final de Aprovados

- Contexto: o CTA final compartilhado por `/aprovados/` e pelas paginas individuais usa um `pen-button--lg` cujo label excede a largura util de viewports entre 320 e 390 px.
- Gap do design system: `@carvalhorafael/proenem-css@0.3.0` aplica `white-space: nowrap` a todo `pen-button` e nao publica uma variante para quebra controlada de labels longos em containers estreitos.
- Decisao: permitir quebra centralizada somente em `.pro-testimonials-next__actions .pen-button` ate 760 px e remover a pressao de largura intrinseca dos itens da grade, preservando o contrato compartilhado nos demais botoes e no desktop.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#183`; design system `carvalhorafael/proenem-design-system-brand-guide#42`.
- Criterio de remocao: atualizar o pacote para a versao que publicar o contrato responsivo, remover o override local e revalidar 320, 390 e desktop.

## 2026-08-21: Composicao tablet do footer

- Contexto: em 768 px, o footer mantinha o manifesto e uma grade desktop de tres grupos de links lado a lado, expandindo o documento para 945 px e deixando a coluna Ferramentas fora da tela.
- Gap do design system: `@carvalhorafael/proenem-css@0.3.0` so publica a transicao do footer para uma coluna em 760 px e nao possui uma composicao intermediaria para tablets em retrato.
- Decisao: entre 761 e 980 px, Matérias lecionadas ocupa a primeira linha da area de links e Gabaritos e Ferramentas formam duas colunas abaixo, sem aplicar ao tablet todo o comportamento da navegacao mobile.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#184`; design system `carvalhorafael/proenem-design-system-brand-guide#43`.
- Criterio de remocao: atualizar o pacote para a versao que publicar o layout tablet, remover o override local e revalidar 390, 768, 980 e desktop.

## 2026-08-21: Areas de toque moveis em Aprovados

- Contexto: a auditoria de `/aprovados/` e das paginas individuais encontrou links prioritarios com menos de 44 px de altura em viewports de 320 e 390 px.
- Gap do design system: o contrato geral de area minima de toque segue aberto e nao cobre, nos pacotes publicados, o pill usado como link, a acao dos cards e os links secundarios e de compartilhamento desta composicao.
- Decisao: ate 760 px, complementar esses elementos com area minima de 44x44 CSS px, preservando textos, cores, foco visivel e geometria desktop.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#185`; design system `carvalhorafael/proenem-design-system-brand-guide#40`.
- Criterio de remocao: atualizar os pacotes para a versao que publicar o contrato compartilhado, remover os seletores locais e revalidar os alvos em 320 e 390 px.

## 2026-08-26: Inventario das LPs de campanha como contrato de trabalho

- Contexto: as LPs de campanha em producao (`intensiva.proenem.com.br` e `intensiva.proenem.com.br/redacao`) sao uma aplicacao React/Next fora do tema, com tokens Tailwind proprios, enquanto a LP equivalente no WordPress (`/lp/intenisva/`, CPT `sales_page`) usa apenas seis widgets e cobre uma fracao das secoes.
- Decisao: versionar o inventario das secoes em `docs/lp-blocks.md` e tratar esse arquivo como contrato de trabalho das fases de evolucao dos widgets de LP, antes de criar ou alterar qualquer widget.
- Fronteira: o inventario descreve secoes e gaps; ele nao define copy definitiva de campanha nem substitui especificacao de marketing.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#191` como issue pai e `#193` para esta etapa.

## 2026-08-26: LPs traduzidas para a linguagem visual publicada da Proenem

- Contexto: reproduzir a aparencia da aplicacao de campanha exigiria importar tokens Tailwind que nao existem nos pacotes publicados do design system, criando adaptacao local ampla e permanente no tema.
- Decisao: traduzir as secoes das LPs de campanha para a linguagem visual publicada da Proenem, consumindo os contratos `pen-*` e o que o site ja usa hoje, em vez de replicar os tokens da aplicacao de campanha.
- Consequencia: a LP no WordPress pode divergir visualmente da LP de campanha atual. A paridade exigida e de secoes, hierarquia de conversao e conteudo, nao de pixel.
- Fronteira: quando um contrato necessario nao existir nos pacotes publicados, o tema pode criar adaptacao local temporaria, sempre com o par de issues obrigatorio de design system e de debito no tema.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#191` e `#198`.

## 2026-08-26: Namespace `pro_lp_*` separado dos widgets da home

- Contexto: os widgets `pro_home_*` carregam a copy da home fatiada nos controles (`title_line_1`, `title_emphasis_2`, `statement_1`) e emitem IDs de heading fixos como `pro-home-title` e `pro-final-title`. Duas instancias na mesma pagina produzem ID duplicado. A LP `/lp/intenisva/` ja reusa `pro_home_marquee` e `pro_home_faq` fora da home.
- Decisao: os widgets genericos de pagina de venda usam o prefixo tecnico `pro_lp_` e categoria propria no editor; os widgets `pro_home_*` permanecem como widgets exclusivos da home e nao recebem generalizacao retroativa.
- Complemento: os IDs de heading passam a ser derivados do ID do widget Elementor, e o cabecalho de secao (`eyebrow`, `title`, `body`, `tone`, `anchor_id`) vira contrato compartilhado no base class de vendas.
- Fronteira: nenhuma LP existente deve quebrar; os widgets `pro_home_*` continuam registrados e funcionais onde ja estao em uso.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#194`.

## 2026-08-26: Contrato compartilhado de secao nos widgets de venda

- Contexto: cada widget de venda resolvia selo, titulo, texto e fundo do seu jeito, e os widgets da home emitiam ids de heading fixos, o que produzia id duplicado quando dois widgets iam para a mesma pagina.
- Decisao: centralizar em `Proenem_Elementor_Sales_Widget_Base` o contrato de secao com `eyebrow`, `title`, `body`, `tone` e `anchor_id`, alem dos helpers de id de heading e de ancora derivados do id do widget Elementor.
- Aplicacao: os widgets existentes passam a consumir o contrato mantendo as proprias classes de layout, para nao mudar o resultado visual atual.
- Tone: `default` nao emite classe, `surface` usa `--pen-color-surface` e `brand` usa `--pen-color-proenem-red` com `--pen-color-on-red`. Apenas tokens publicados.
- Fronteira: `tone` pinta faixa contida na largura maxima do widget. Faixa de sangria total continua decisao da camada de pagina.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#194`.

## 2026-08-26: Ancora dos widgets da home vira controle editavel

- Contexto: `pro_home_pillars`, `pro_home_questions`, `pro_home_pricing`, `pro_home_faq` e `pro_home_testimonials` emitiam ancora fixa (`metodo`, `questoes`, `planos`, `faq`, `depoimentos`). Essas ancoras sao destinos de CTA, mas duas instancias na mesma pagina produziam id duplicado.
- Decisao: expor a ancora no controle `anchor_id`, com o valor publicado atual como default.
- Consequencia: uma instancia por pagina mantem exatamente o comportamento anterior; duas instancias podem receber ancoras diferentes pelo editor.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#194`.

## 2026-08-26: Categoria e base propria para widgets de LP

- Contexto: os widgets `pro_home_*` carregam a copy da home na estrutura dos controles e nao servem como base generica de pagina de venda, mas apareciam na mesma categoria dos widgets genericos e com o keyword `lp`.
- Decisao: registrar a categoria `proenem-lp` e criar `Proenem_Elementor_Lp_Widget_Base` como base dos widgets `pro_lp_*`; remover o keyword `lp` dos widgets da home.
- Fronteira: `proenem-sales` continua registrando os widgets ja publicados, incluindo as secoes da home, para nao quebrar paginas existentes.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#194`.

## 2026-08-26: Secoes de LP com sangria total

- Contexto: na homologacao da Fase 1, hero, CTA final e FAQ renderizaram com 1140 px em viewport de 1280 px, comecando em `left=70`. O recuo vinha do `.e-con-inner` do Elementor (`max-width: min(100%, 1140px)` e `margin-inline: 60px`) somado ao `padding: 10px` do container, e o modificador `tone` aplicava borda e canto arredondado, o que fazia a faixa ler como card emoldurado.
- Decisao: separar faixa de conteudo. `.pro-sales-section` ocupa 100% da largura e e dona do fundo e do gutter; `.pro-sales-section__inner` mantem a largura de conteudo. `tone` perde borda e raio.
- Decisao: em `sales_page`, liberar o gutter do container do Elementor a partir do marcador `pro-section-host`, aplicado pelos widgets que representam secao de pagina. Tambem zerar a contribuicao vertical do container entre faixas e o offset superior da primeira faixa.
- Alternativa descartada: resolver apenas no template kit da Fase 4, com containers full width por configuracao. Nao atende pagina montada a mao pelo time, que e o fluxo esperado.
- Risco aceito: o tema sobrepoe a largura do container do page builder em `sales_page`. Quem escolher container boxed de proposito para uma secao da Proenem nao sera atendido. Mitigacao: o override alcanca apenas containers que hospedam diretamente um widget marcado como secao.
- Gap do design system: os pacotes publicados nao trazem contrato de banda de sangria total, classe de container nem token de largura de conteudo; o pacote resolve casos parecidos com margem negativa local dentro de componentes. A cola fica local no tema.
- Fronteira: `pro_pricing_card` declara `is_section_host()` como falso e continua se comportando como card.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#200`; gap do design system registrado em `carvalhorafael/proenem-wordpress-theme#198`.

## 2026-08-26: Widgets genericos de LP na categoria Proenem LP

- Contexto: quatro secoes das LPs de campanha nao tinham widget equivalente: faixa de metricas, card de destaque de oferta, spotlight de midia mais copy e depoimento em video.
- Decisao: criar `pro_lp_metrics`, `pro_lp_offer_highlight`, `pro_lp_spotlight` e `pro_lp_video_story` sobre `Proenem_Elementor_Lp_Widget_Base`, consumindo o contrato de secao compartilhado.
- `pro_lp_offer_highlight` nao registra cabecalho de secao: o nome da oferta e o proprio heading, como na LP de campanha.
- `pro_lp_spotlight` expoe `media_position` para inverter o lado da imagem, cobrindo as duas secoes de spotlight da mesma LP com um widget so.
- Fronteira: os widgets ficam na categoria `proenem-lp` e nao substituem os widgets `pro_home_*`.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#195`.

## 2026-08-26: Video de LP carregado por fachada

- Contexto: o depoimento em video das LPs de campanha usa provedor externo. Carregar o player no load da pagina significa contatar terceiro antes de qualquer interacao, com custo de performance e de privacidade.
- Decisao: `pro_lp_video_story` renderiza capa e botao de reproduzir, e o `iframe` do provedor entra apenas no clique, reaproveitando `proenem_get_testimonial_video_embed_url()` no servidor e o mesmo padrao de fachada ja usado nos depoimentos.
- Consequencia: sem capa local, nenhuma imagem e carregada. Miniatura hospedada pelo provedor foi descartada de proposito, porque seria requisicao de terceiro antes da interacao.
- Verificado em `/lp/homologacao-widgets-lp/`: zero requisicoes ao provedor antes do clique; apos o clique, apenas a URL de embed.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#195`.

## 2026-08-26: Tone e contrato de par de cores, nao so de fundo

- Contexto: ao compor um card branco dentro da faixa de marca, o texto do card herdava o branco da faixa e ficava branco sobre branco. O botao primario, por sua vez, renderizava vermelho sobre vermelho na faixa de marca.
- Decisao: todo componente que pinta a propria superficie declara o proprio `color`. `.pro-sales-card` passa a declarar `color`, o botao primario inverte na faixa de marca e volta ao par padrao quando esta dentro de um card.
- Consequencia: widget novo que pinte superficie propria precisa declarar o par de cores; herdar da faixa nao e suficiente.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#195`.

## 2026-08-26: Nivel do heading das secoes de LP como controle

- Contexto: nenhum widget de LP emitia `h1`. `pro_offer_hero` renderizava `h2`, entao uma LP montada com os widgets do tema ficava sem titulo principal.
- Decisao: expor `heading_level` no hero, com `h1` como default, em vez de fixar o nivel no markup. Qual secao carrega o `h1` e decisao editorial, e ha LP em que o titulo principal nao esta no hero.
- Consequencia: uma LP montada com os widgets atuais nasce com exatamente um `h1`. O helper `render_section_header()` ja aceitava `title_tag`, entao a mudanca ficou no controle.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#196`.

## 2026-08-26: Icone dos beneficios por biblioteca de midia

- Contexto: o bloco de metodo das LPs usa um icone por card. O controle `ICONS` do Elementor resolveria, mas carrega a biblioteca de icones do proprio Elementor, ou seja fonte de terceiro no front-end.
- Decisao: usar controle de midia, para o time escolher o icone da biblioteca da marca. Sem imagem, o item mantem o marcador padrao.
- Consequencia: o tema nao passa a depender de biblioteca de icones externa, e o icone fica sob curadoria da marca.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#196`.

## 2026-08-26: Card de plano renderizado em um unico lugar

- Contexto: `pro_pricing_grid` e `pro_pricing_card` duplicavam o markup do card de plano. Adicionar parcelamento, preco a vista e selos de confianca duplicaria a mudanca.
- Decisao: extrair `render_plan_card()` e `add_plan_price_controls()` para o base class de vendas e consumir dos dois widgets.
- Consequencia: a grade com um plano so passa a ser o layout de card unico centralizado que a secao de oferta das LPs usa, sem widget novo.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#196`.

## 2026-08-26: Categoria propria para os widgets da home

- Contexto: os 13 widgets `pro_home_*` ja tinham `pro_home_` no nome tecnico e `Pro Home` no titulo visivel, mas estavam registrados em `proenem-sales`, a mesma categoria dos genericos. No painel do editor, `Pro Home Hero` aparecia ao lado de `Pro Hero de Oferta`.
- Decisao: registrar a categoria `proenem-home`, com titulo `Proenem Home (somente na home)`, e mover os 13 widgets para ela.
- Consequencia: evitar o uso indevido deixa de depender de o editor ler o nome do widget. Conteudo salvo nao e afetado, porque a categoria so organiza o painel.
- Fronteira: os widgets da home permanecem isolados e nao entram em consolidacao com os genericos.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#201`.

## 2026-08-26: Grupo de oferta consolidado em pro_pricing_grid

- Contexto: `pro_pricing_grid` com um plano, `pro_pricing_card` e `pro_lp_offer_highlight` renderizam a mesma forma. Medido em `/lp/homologacao-widgets-lp/`: os tres a 544 px, com a mesma sequencia de filhos. `pro_pricing_card` e um plano da grade sem cabecalho de secao; `pro_lp_offer_highlight` e o mesmo card sem os campos de preco.
- Causa: `pro_lp_offer_highlight` foi criado na Fase 2, antes de o card de plano receber parcelamento, preco a vista e selos na Fase 3. A ordem inversa teria evitado o widget novo.
- Decisao: `pro_pricing_grid` e o widget da secao de oferta, com preco opcional. `pro_pricing_card` e `pro_lp_offer_highlight` ficam marcados para aposentadoria.
- Pendencia: a forma da aposentadoria depende de decisao humana, porque `/lp/intenisva/` em producao usa `pro_pricing_card` e o Elementor guarda `widgetType` em post meta.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#201`.

## 2026-08-26: Escopo congelado no grupo de faixa de texto

- Contexto: `pro_offer_hero`, `pro_lp_spotlight` e `pro_cta` compartilham a forma selo, titulo, corpo e CTA. `pro_cta` e subconjunto estrito do hero.
- Decisao: manter os tres, com papeis documentados, e nao adicionar controle novo a nenhum deles sem revisar o papel dos tres em conjunto.
- Alternativa descartada: fundir em um widget com variante. Trocaria clareza de intencao editorial por economia de widget, com risco em pagina publicada.
- Papeis: `pro_offer_hero` e a primeira faixa e dona do `h1`; `pro_lp_spotlight` e faixa de meio de pagina com bullets e lado invertivel; `pro_cta` e faixa minima de chamada.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#201`.

## 2026-08-26: Aposentadoria de widget preserva pagina publicada

- Contexto: `pro_pricing_card` e `pro_lp_offer_highlight` foram consolidados em `pro_pricing_grid`, mas `/lp/intenisva/` em producao usa o primeiro, e o Elementor guarda `widgetType` em post meta. Remover a classe faria a secao deixar de renderizar na pagina publicada.
- Decisao: manter as classes registradas com `show_in_panel()` retornando falso e `(obsoleto)` no titulo visivel. Pagina existente continua identica; ninguem adiciona um novo pelo painel.
- Alternativas descartadas: alias de `widgetType`, que exigiria mapear controles antigos para o formato de plano da grade; e migrar a pagina publicada antes de remover, que mexeria em conteudo no ar para uma pagina que sera remontada de novo no template kit.
- Criterio de remocao definitiva: quando `/lp/intenisva/` for remontada com `pro_pricing_grid`, remover as duas classes e a nota de obsolescencia.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#201`.

## 2026-08-26: Contador de oferta com contagem real

- Contexto: `pro_offer_countdown` renderizava o valor do controle de data como texto visivel, `2026-12-31 23:59`, sem formatacao e sem contagem. A descricao do proprio controle admitia que a contagem ficou para depois.
- Decisao: formatar a data no servidor com `wp_date()` no fuso do WordPress, emitir `datetime` em ISO 8601 e adicionar contagem por JavaScript progressivo em dias, horas e minutos.
- Degradacao: sem JavaScript, a data formatada permanece visivel. Depois do prazo, o texto de encerramento substitui a contagem.
- Internacionalizacao: os rotulos das unidades vem do PHP por marcacao propria, e nao de string no JavaScript, o que mantem tudo no catalogo e dispensa plural no cliente.
- Tracking: tema `carvalhorafael/proenem-wordpress-theme#201`.
