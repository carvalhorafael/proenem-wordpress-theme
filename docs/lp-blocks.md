# Blocos das LPs de campanha

Este documento e o inventario de referencia das paginas de venda da Proenem. Ele existe para que a evolucao dos widgets Elementor de LP seja feita contra as secoes realmente usadas em campanha, e nao contra suposicao.

Trabalho coordenado pela issue pai `carvalhorafael/proenem-wordpress-theme#191`.

## Fontes analisadas

Data da analise: 2026-08-26.

| Fonte | O que e | Camada visual |
| --- | --- | --- |
| `https://intensiva.proenem.com.br/` | LP de campanha da Turma Intensiva ENEM 2026, em producao | aplicacao React/Next fora do tema, com tokens Tailwind proprios (`bg-surface`, `bg-brand`, `border-border`) |
| `https://intensiva.proenem.com.br/redacao` | variacao da mesma campanha com foco em redacao | mesma aplicacao React/Next |
| `https://proenem.com.br/lp/intenisva/` | LP equivalente ja dentro do WordPress | CPT `sales_page` do plugin `sales-page`, template Elementor Canvas |

A LP que ja esta no WordPress usa apenas seis widgets: `pro_offer_hero`, `pro_offer_countdown`, `pro_benefits_list`, `pro_pricing_card`, `pro_home_marquee` e `pro_home_faq`. Ela cobre uma fracao do que a LP de campanha entrega.

## Inventario secao por secao

A coluna "cobertura" descreve o widget mais proximo hoje registrado em `inc/elementor-sales-widgets.php`.

### 1. Header sticky de conversao

- Composicao: logo e um unico CTA (`Garantir minha vaga`), fixo no topo, com fundo translucido.
- Cobertura: `pro_navbar`, com controles `mode`, `menu_id`, `aria_label` e CTA mobile.
- Gap: falta um modo de LP sem menu de navegacao, com CTA unico sempre visivel e destino em ancora interna.

### 2. Hero de campanha

- Composicao: eyebrow (`TURMA INTENSIVA ENEM 2026`), titulo, paragrafo de dor, CTA primario, linha de microcopy (`Vagas limitadas • Inicio hoje • Acesso imediato`) e imagem de fundo com tres cards flutuantes de mockup (`Cronograma pronto / Semana 1 de 12`, `Redacao 940 / Corrigida em 10 dias`, `Organizacao diaria / 4-5 tarefas hoje`).
- Cobertura: `pro_offer_hero`, com `eyebrow`, `title`, `body`, `image`, `primary_label`, `primary_url`, `secondary_label` e `secondary_url`.
- Gap: linha de microcopy de confianca, imagem tratada como fundo da secao e nao como imagem lateral, e repeater de cards flutuantes de prova.

### 3. O metodo

- Composicao: eyebrow (`O METODO`), titulo, corpo e quatro cards com icone, titulo e descricao (`Diagnostico da sua nota`, `Cronograma pronto`, `Correcao de redacao`, `Evolucao acompanhada`). Na variacao de redacao, um dos cards recebe badge `DESTAQUE` e tratamento visual diferenciado.
- Cobertura: `pro_benefits_list`, com `title`, `body` e repeater `items`. `pro_home_pillars` resolve algo parecido, mas com a copy da home fatiada nos controles.
- Gap: icone por item, controle de colunas de dois a quatro, item em destaque com badge e eyebrow de secao.

### 4. Prova social, destaque de oferta e metricas

Uma unica faixa concentra tres blocos distintos.

- Prova social: titulo (`Mais de 44 mil aprovados confiaram no PROENEM`) e paragrafo de apoio.
- Destaque de oferta: badge de urgencia (`INICIO HOJE!`), nome do produto, subtitulo, cinco bullets e CTA.
- Metricas: tres pares valor e rotulo (`+44.000 alunos aprovados`, `4,9/5 avaliacao media`, `12 anos de experiencia`).
- Cobertura: `pro_home_proof` cobre prova social, porem preso a IDs do CPT de depoimento, sem espaco para numeros editoriais livres. Nada cobre o destaque de oferta nem as metricas.
- Gap: dois widgets novos, uma faixa de metricas e um card de destaque de oferta.

### 5. Plano de estudos

- Composicao: eyebrow (`PLANO DE ESTUDOS`), titulo, corpo, tres bullets e mockup de produto ao lado, com indicadores de progresso.
- Cobertura: `pro_home_platform` resolve um padrao parecido, mas com titulo fatiado em `title_span`, `title_strong` e `title_tail` e com copy da home nos defaults.
- Gap: um spotlight generico de midia mais copy, com bullets, CTA opcional e lado invertivel.

### 6. Redacao

- Composicao: mesmo padrao da secao anterior, com mockup do outro lado. Na variacao de redacao a secao ganha um paragrafo extra e CTA proprio.
- Cobertura: nenhuma especifica.
- Gap: atendido pelo mesmo spotlight da secao 5, o que confirma a necessidade do controle de lado.

### 7. Oferta

- Composicao: ancora `#oferta`, eyebrow (`VAGAS POR TEMPO LIMITADO`), titulo, corpo e um card unico centralizado com nome do plano, subtitulo de acesso, sete features, preco parcelado (`12x de R$ 29,90`), preco a vista (`ou R$ 306,90 a vista`), CTA e tres selos de confianca (`Pagamento 100% seguro`, `Garantia de 7 dias`, `Acesso liberado na hora`).
- Cobertura: `pro_pricing_card` cobre nome, descricao, features, preco, recorrencia, badge e CTA. `pro_pricing_grid` cobre a grade com titulo de secao.
- Gap: parcelamento e preco a vista como campos distintos, linha de selos de confianca, cabecalho de secao com eyebrow e corpo, e layout de card unico centralizado.

### 8. Historia real

- Composicao: eyebrow (`HISTORIA REAL`), titulo, corpo, video incorporado e CTA em link.
- Cobertura: nenhuma.
- Gap: widget novo de depoimento em video.

### 9. Aprovados

- Composicao: titulo, corpo e tres cards com foto, nome, curso e universidade e badge de aprovacao.
- Cobertura: `pro_home_testimonials`, alimentado por IDs do CPT de depoimento.
- Gap: limite de itens e layout adequado quando o widget e usado fora da home.

### 10. CTA final

- Composicao: eyebrow (`A RETA FINAL COMECOU`), titulo, corpo, CTA e microcopy (`Acesso imediato • Garantia de 7 dias • Vagas limitadas`), em faixa de cor de marca.
- Cobertura: `pro_cta`, com `title`, `body`, `button_label` e `button_url`.
- Gap: eyebrow, microcopy abaixo do botao e variante de faixa de marca.

### 11. Footer minimo

- Composicao: logo e linha de copyright.
- Cobertura: `pro_footer`, que renderiza o footer completo do site.
- Gap: modo minimo para LP.

## Resumo do gap

| Necessidade | Encaminhamento | Fase |
| --- | --- | --- |
| Faixa de metricas | `pro_lp_metrics` (feito) | 2 |
| Card de destaque de oferta | `pro_lp_offer_highlight` (feito) | 2 |
| Spotlight de midia mais copy | `pro_lp_spotlight` (feito) | 2 |
| Depoimento em video | `pro_lp_video_story` (feito) | 2 |
| Modo LP da navbar | `pro_navbar` (feito) | 3 |
| Microcopy, fundo e cards flutuantes no hero | `pro_offer_hero` (feito) | 3 |
| Icones, colunas e item em destaque | `pro_benefits_list` (feito) | 3 |
| Parcelamento, preco a vista e selos | `pro_pricing_card` (feito) | 3 |
| Eyebrow e corpo na grade de planos | `pro_pricing_grid` (feito) | 3 |
| Eyebrow, microcopy e faixa de marca | `pro_cta` (feito) | 3 |
| Modo minimo do footer | `pro_footer` (feito) | 3 |
| Limite e layout de LP nos depoimentos | `pro_home_testimonials` (feito) | 3 |
| Cabecalho de secao compartilhado | fundacao comum no base class | 1 |
| IDs de heading unicos por instancia | fundacao comum no base class | 1 |
| Template kits, assets condicionais, ancora e CTA fixo mobile | camada de pagina | 4 |

## Achados estruturais

1. Os widgets `pro_home_*` nao sao reutilizaveis em LP. Os controles carregam a copy da home fatiada na propria estrutura (`title_line_1`, `title_emphasis_2`, `statement_1`) e o markup emite IDs fixos como `pro-home-title` e `pro-final-title`. Duas instancias na mesma pagina produzem ID duplicado, o que quebra a associacao de `aria-labelledby` e prejudica semantica e SEO. A LP `/lp/intenisva/` ja usa `pro_home_marquee` e `pro_home_faq` fora da home.
2. Nao existe cabecalho de secao compartilhado. Cada widget resolve eyebrow, titulo, corpo e tom de fundo do seu jeito, embora as LPs alternem fundo a cada secao. O padrao precisa virar contrato comum, com `eyebrow`, `title`, `body`, `tone` e `anchor_id`.
3. Nao existe camada de pagina de LP. O repositorio publica apenas `docs/elementor/proenem-home.json`. Falta template kit importavel de LP, carregamento condicional de estilos para `sales_page`, ancora suave para a secao de oferta e CTA fixo no mobile.
4. As LPs de campanha nao consomem os contratos publicados do design system. Elas usam tokens Tailwind proprios. A traducao para a linguagem visual publicada da Proenem pode revelar contratos ausentes nos pacotes `@carvalhorafael/proenem-*`, e cada ausencia precisa do par de issues previsto no `AGENTS.md`.

## Fundacao compartilhada dos widgets de LP

Implementada na Fase 1 (`#194`).

### Categoria e base

- `proenem-sales` continua sendo a categoria dos widgets ja publicados, incluindo as secoes da home.
- `proenem-lp` e a categoria dos widgets genericos de pagina de venda.
- `Proenem_Elementor_Lp_Widget_Base`, em `inc/class-proenem-elementor-lp-widget-base.php`, e a base dos widgets `pro_lp_*`. Ela define a categoria e os keywords de LP e herda o contrato de secao do base class de vendas.

### Contrato de secao

Disponivel em `Proenem_Elementor_Sales_Widget_Base` e usado pelos widgets de vendas e de LP.

| Membro | Papel |
| --- | --- |
| `add_section_header_controls()` | registra `eyebrow`, `title` e `body`; cada chave e opcional, entao o widget mantem a ordem de painel que ja tinha |
| `add_section_layout_controls()` | abre um painel `Seção` com `tone` e `anchor_id` |
| `add_section_anchor_control()` | registra apenas `anchor_id`, com default para secoes que ja tem ancora publicada |
| `add_section_render_attributes()` | monta a faixa (`<key>`) e o wrapper de conteudo (`<key>_inner`) com classe, ancora e `aria-labelledby` |
| `is_section_host()` | declara se o widget representa uma secao de pagina; controla o marcador `pro-section-host` |
| `render_section_header()` | imprime selo, titulo e texto com o id de heading unico |
| `section_heading_id()` | id de heading derivado do id do widget Elementor |
| `section_anchor()` | ancora saneada da instancia, com fallback |
| `widget_dom_id()` | qualquer id de DOM derivado do id do widget |

Valores de `tone`:

- `default`: nenhuma classe extra, a faixa nao recebe fundo nem ritmo vertical proprio;
- `surface`: fundo `--pen-color-surface`, que alterna contra o canvas `--pen-color-background` da pagina;
- `brand`: fundo `--pen-color-proenem-red` com texto `--pen-color-on-red`.

Faixa nao tem borda nem canto arredondado. Borda e raio faziam a secao ler como card emoldurado.

### Sangria total

Implementada na Fase 1.1 (`#200`).

A secao e composta em duas camadas:

- `.pro-sales-section` e a faixa. Ocupa 100% da largura do container, e dona do fundo e do gutter da pagina;
- `.pro-sales-section__inner` e o conteudo. Mantem `max-width` e fica centralizado.

Os widgets que representam secao de pagina recebem o marcador `pro-section-host` no wrapper Elementor, via `get_html_wrapper_class()`. Em `sales_page`, o tema usa esse marcador com `:has()` para liberar o gutter do container do Elementor, que por padrao e boxed em 1140 px. Tambem zera a contribuicao vertical do container, para que faixas vizinhas nao fiquem separadas por costura de canvas, e zera o offset superior da primeira faixa, para o hero encostar no topo.

`pro_pricing_card` declara `is_section_host()` como falso: e card para compor dentro de coluna, nao secao de pagina.

Risco aceito: em `sales_page`, o tema sobrepoe a largura do container do Elementor. Quem escolher container boxed de proposito para uma secao da Proenem nao sera atendido. O override alcanca apenas containers que hospedam diretamente um widget marcado como secao.

Medido em `/lp/homologacao-widgets-lp/`: em 1280 px, as oito faixas medem 1280 px a partir de `left=0`, com conteudo em `left=48` e 1184 px; em 390 px, as faixas medem 390 px com conteudo em `left=20` e 350 px, sem overflow horizontal. `pro_pricing_card` permanece em `left=70` com 1140 px.

### Ancoras dos widgets da home

Os widgets `pro_home_pillars`, `pro_home_questions`, `pro_home_pricing`, `pro_home_faq` e `pro_home_testimonials` emitiam ancora fixa (`metodo`, `questoes`, `planos`, `faq`, `depoimentos`), que sao destinos dos CTAs. Agora a ancora vem do controle `anchor_id`, com esses mesmos valores como default. O comportamento de uma instancia por pagina nao muda, e duas instancias podem receber ancoras diferentes.

### Widgets de LP

Implementados na Fase 2 (`#195`), todos na categoria `Proenem LP` e consumindo o contrato de secao.

| Widget | Secao coberta | Controles proprios |
| --- | --- | --- |
| `pro_lp_metrics` | trio de metricas da faixa de prova social | repeater de `value` e `label`; cabecalho de secao opcional |
| `pro_lp_offer_highlight` | card `INICIO HOJE!` | `badge`, `name`, `summary`, `features`, botao. O nome da oferta e o heading da secao |
| `pro_lp_spotlight` | `PLANO DE ESTUDOS` e `REDACAO` | `bullets`, `image`, `image_alt`, botao, `media_position` para inverter o lado |
| `pro_lp_video_story` | `HISTORIA REAL` | `video_url`, `poster`, `poster_alt`, `play_label`, botao |

`pro_lp_spotlight` colapsa para uma coluna quando nao ha imagem, via `pro-lp-spotlight--no-media`, para nao deixar coluna vazia.

`pro_lp_video_story` usa fachada: renderiza capa e botao, e o `iframe` do provedor so entra depois do clique. A URL de embed e resolvida no servidor por `proenem_get_testimonial_video_embed_url()`. Sem capa local nenhuma imagem externa e carregada, porque miniatura hospedada pelo provedor tambem seria requisicao de terceiro antes da interacao.

### Controles adicionados aos widgets existentes

Implementados na Fase 3 (`#196`).

| Widget | Controles novos |
| --- | --- |
| `pro_navbar` | modo `lp`, com logo e um unico CTA sempre visivel, sem menu e sem toggle; `cta_label` e `cta_url`, que aceita ancora |
| `pro_offer_hero` | `heading_level` com default `h1`, `microcopy`, `media_mode` para usar a imagem como fundo, repeater `proof_cards` |
| `pro_benefits_list` | `eyebrow`, `body`, `columns` de 2 a 4, e por item `icon`, `highlight` e `badge` |
| `pro_pricing_card` | `price_prefix`, `price_details`, `trust_items`; card centralizado |
| `pro_pricing_grid` | `eyebrow`, `body`, os mesmos campos de preco por plano, e centralizacao quando ha um plano so |
| `pro_cta` | `eyebrow`, `microcopy`; na faixa de marca o card proprio some e a faixa vira o CTA |
| `pro_footer` | modo `minimal`, com logo e copyright |
| `pro_home_testimonials` | `limit` de itens |

O nivel do heading e controle, e nao valor fixo, porque qual secao carrega o `h1` e decisao editorial. O default do hero e `h1`, entao uma LP montada com os widgets atuais nasce com exatamente um `h1`.

O controle de colunas dos beneficios e limite real de colunas, nao largura minima: `repeat(N, minmax(0, 1fr))` com colapso em 980 px e 620 px. Com `auto-fit` o numero de colunas seria decidido pela largura disponivel, e o rotulo do controle mentiria.

`render_plan_card()` no base class e o unico lugar que renderiza card de plano, consumido pela grade e pelo card avulso. Antes o markup era duplicado nos dois widgets.

O icone dos beneficios usa controle de midia, e nao biblioteca de icones do Elementor, para nao introduzir fonte de terceiro no tema. Sem imagem, o item mantem o marcador padrao.

### Tone como contrato de par de cores

`tone` nao e apenas fundo. Todo componente que pinta a propria superficie precisa manter o proprio par de cores dentro da faixa:

- `.pro-sales-card` declara `color` proprio, porque pinta fundo branco e herdaria o branco da faixa de marca;
- o botao primario inverte na faixa de marca, onde o vermelho do componente nao teria contraste, e volta ao par padrao quando esta dentro de um card.

Varredura completa dentro de `--tone-brand` em `/lp/homologacao-widgets-lp/`, cobrindo hero, CTA, destaque de oferta, grade de planos e FAQ: menor contraste encontrado 4.85, todos acima de AA para texto normal. Componentes com superficie propria, como `.pro-sales-card`, `.pro-sales-comparison table` e `.pro-sales-faq__item`, mantem fundo claro e texto ink; os que nao pintam superficie herdam o par da faixa.

## Revisao da biblioteca de widgets

Feita na Fase 3.5 (`#201`), sobre saida renderizada em `/lp/homologacao-widgets-lp/`, nao sobre leitura de codigo.

### Categorias do editor

| Categoria | Titulo no painel | Widgets |
| --- | --- | --- |
| `proenem-sales` | Proenem | 10 widgets genericos de venda |
| `proenem-lp` | Proenem LP | 4 widgets genericos de landing page |
| `proenem-home` | Proenem Home (somente na home) | 13 widgets exclusivos da home |

Os widgets da home ja tinham `pro_home_` no nome tecnico e `Pro Home` no titulo visivel, mas dividiam a categoria com os genericos, entao `Pro Home Hero` aparecia ao lado de `Pro Hero de Oferta`. Com categoria propria, o uso indevido deixa de depender apenas de o editor ler o nome.

Os 13 widgets da home permanecem isolados por decisao explicita e nao entram na consolidacao.

### Matriz de consolidacao dos 14 widgets nao-home

| Widget | Veredito | Evidencia |
| --- | --- | --- |
| `pro_navbar` | manter | chrome de pagina, sem sobreposicao |
| `pro_footer` | manter | chrome de pagina, sem sobreposicao |
| `pro_faq` | manter | unico widget de perguntas fora da home |
| `pro_lp_video_story` | manter | unico com fachada de video |
| `pro_pricing_grid` | manter, absorve os outros dois do grupo | com um plano ja renderiza card centralizado de 544 px com preco, preco a vista e selos, e traz cabecalho de secao |
| `pro_pricing_card` | aposentado, oculto do painel | renderiza a 544 px com a mesma sequencia de filhos da grade com um plano; e um plano da grade sem cabecalho de secao |
| `pro_lp_offer_highlight` | aposentado, oculto do painel | renderiza a 544 px com a mesma sequencia de filhos, sem os campos de preco; `__summary` e `__name` sao os mesmos papeis de `__description` e do heading do plano |
| `pro_offer_hero` | manter, escopo congelado | primeira faixa da pagina, dona do `h1`, com imagem de fundo e cards de prova |
| `pro_lp_spotlight` | manter, escopo congelado | faixa de meio de pagina, com bullets e lado invertivel |
| `pro_cta` | manter, escopo congelado | subconjunto estrito do hero, mas a intencao de faixa minima e legitima e esta em uso |
| `pro_benefits_list` | manter | grade de cards com icone, titulo e corpo |
| `pro_lp_metrics` | manter | grade de numeros com tipografia de display; estrutura parecida, peso semantico diferente |
| `pro_offer_countdown` | terminado | renderizava a data como texto de maquina visivel, `2026-12-31 23:59`, sem formatacao e sem contagem. Agora tem contagem real |
| `pro_plans_comparison` | manter dormente | tabela funcional, com wrapper de rolagem; nenhuma secao mapeada usa, mas comparativo e pedido recorrente |

Saldo: 14 widgets, 12 mantidos e 2 aposentados no grupo de oferta.

### Aposentadoria sem quebrar pagina publicada

`/lp/intenisva/` em producao usa `pro_pricing_card`, e o Elementor guarda `widgetType` em post meta: remover a classe faria a secao deixar de renderizar.

Decisao: os dois widgets aposentados sobrevivem como classe registrada, com `show_in_panel()` retornando falso e `(obsoleto)` no titulo visivel. Paginas que ja os usam continuam renderizando identicas; ninguem consegue adicionar um novo pelo painel. A remocao definitiva fica para quando a pagina publicada for remontada.

Medido: 25 widgets no painel, 2 ocultos, e os dois ocultos continuam renderizando na pagina de homologacao.

### Contador de oferta

O widget prometia contagem e entregava a string crua do controle. Agora:

- o servidor formata a data com `wp_date()` no fuso configurado no WordPress e emite `datetime` em ISO 8601;
- a contagem dinamica entra por JavaScript progressivo, em dias, horas e minutos, atualizando a cada minuto;
- sem JavaScript, a data formatada permanece visivel;
- depois do prazo, o texto de encerramento substitui a contagem;
- os rotulos das unidades vem do PHP, para continuarem traduziveis, o que dispensa plural no JavaScript.

Medido na pagina de homologacao: prazo futuro exibe `127:09:31` em dias, horas e minutos; prazo passado exibe `Oferta encerrada` e esconde as unidades.

Escopo congelado significa que o grupo de faixa de texto nao recebe controle novo sem revisao do papel dos tres. `pro_cta` e subconjunto do hero: crescer os dois em paralelo recria a convergencia que aconteceu no grupo de oferta.

### Por que o grupo de oferta convergiu

`pro_lp_offer_highlight` nasceu na Fase 2, antes de `pro_pricing_card` receber parcelamento, preco a vista e selos na Fase 3. Quando o card de plano ganhou esses campos, passou a cobrir o caso do destaque de oferta. A ordem inversa das fases teria evitado o widget novo. Registro aqui para que a proxima adicao de widget verifique primeiro se um widget existente esta a um controle de distancia do caso.

### Pagina de homologacao

`scripts/seed-lp-homologation.php` cria ou atualiza a `sales_page` `homologacao-widgets-lp` com widgets repetidos de proposito, para que id duplicado apareca na revisao.

```bash
npx wp-env run cli wp eval-file /var/www/html/wp-content/themes/proenem-wordpress-theme/scripts/seed-lp-homologation.php
```

A pagina fica em `http://localhost:8898/lp/homologacao-widgets-lp/`.

## Como este inventario e usado

- Fase 1 (`#194`) implementa a fundacao dos itens 1 e 2 dos achados estruturais.
- Fase 2 (`#195`) cria os quatro widgets novos do resumo de gap.
- Fase 3 (`#196`) executa as oito melhorias do resumo de gap.
- Fase 4 (`#197`) resolve o achado estrutural 3.
- Fase 5 (`#198`) resolve o achado estrutural 4 abrindo o par de issues de design system e de debito no tema.
- Fase 6 (`#199`) fecha o ciclo com testes, i18n, performance e homologacao.

## Apendice: catalogo completo dos widgets avaliados

A analise cobriu os 23 widgets registrados em `inc/elementor-sales-widgets.php`, nao apenas os seis usados na LP `/lp/intenisva/`. Este apendice registra o veredito de cada um para que nenhuma ausencia no plano seja confundida com esquecimento.

### Mapeados no plano

| Widget | Papel na LP | Encaminhamento |
| --- | --- | --- |
| `pro_navbar` | header sticky de conversao | melhoria, modo `lp` (Fase 3) |
| `pro_footer` | footer minimo | melhoria, modo `minimal` (Fase 3) |
| `pro_offer_hero` | hero de campanha | melhoria, microcopy, fundo e cards flutuantes (Fase 3) |
| `pro_benefits_list` | bloco `O METODO` | melhoria, icones, colunas e item em destaque (Fase 3) |
| `pro_pricing_card` | card da secao de oferta | melhoria, parcelamento, preco a vista e selos (Fase 3) |
| `pro_pricing_grid` | cabecalho da secao de oferta | melhoria, eyebrow e corpo (Fase 3) |
| `pro_cta` | CTA final em faixa | melhoria, eyebrow, microcopy e faixa de marca (Fase 3) |
| `pro_home_testimonials` | secao `Aprovados` | melhoria, limite e layout de LP (Fase 3) |

### Avaliados como paralelos e descartados como base

Resolvem o padrao visual certo, mas com a copy da home embutida na estrutura dos controles, o que os torna inadequados como base generica de LP.

| Widget | Padrao paralelo | Motivo do descarte |
| --- | --- | --- |
| `pro_home_proof` | faixa de prova social | preso a IDs do CPT de depoimento, sem espaco para numeros editoriais livres |
| `pro_home_pillars` | grade de cards de metodo | titulo e corpo fatiados em `body_1`, `body_2` e defaults da home |
| `pro_home_platform` | spotlight de midia mais copy | titulo fatiado em `title_span`, `title_strong` e `title_tail` |

### Avaliados e sem secao correspondente nas LPs de campanha

Permanecem registrados e validos; apenas nao entram na paridade das duas LPs analisadas.

| Widget | Motivo |
| --- | --- |
| `pro_offer_countdown` | as LPs de campanha usam urgencia textual (`Vagas limitadas • Inicio hoje`), nao cronometro; a LP `/lp/intenisva/` usa o countdown |
| `pro_plans_comparison` | as LPs de campanha vendem plano unico, sem tabela comparativa |
| `pro_faq` | nenhuma das duas LPs de campanha tem FAQ |
| `pro_home_faq` | idem; achado lateral, a LP `/lp/intenisva/` usa `pro_home_faq` quando o generico `pro_faq` ja existe |
| `pro_home_marquee` | usado em `/lp/intenisva/`, ausente nas LPs de campanha |

### Exclusivos da home

Nao recebem generalizacao retroativa e nao devem ser usados em LP.

`pro_home_hero`, `pro_home_action_bar`, `pro_home_pain`, `pro_home_questions`, `pro_home_pricing`, `pro_home_schools`, `pro_home_final_cta`.
