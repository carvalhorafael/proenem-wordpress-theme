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
- Adaptacao local: as classes `pro-home-proof-students`, `pro-home-proof-student` e `pro-home-proof-student__caption` complementam temporariamente o pattern publicado com grade variavel e identificacao individual legivel.
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
