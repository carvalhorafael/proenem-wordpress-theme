# Eventos dos materiais gratuitos

Este documento define o contrato de medição dos materiais gratuitos: o que já chega sozinho, o que o tema envia e por quê. A nomenclatura segue `docs/home-cta-map.md` — intenção primeiro, ferramenta depois.

## O que não é nosso

A produção usa o [plugin do Amplitude](https://wordpress.org/plugins/amplitude/), que carrega o Browser SDK e o inicializa com `autocapture: { elementInteractions: true }`.

O predicado que o SDK usa para cada captura é `!1 !== (config?.[chave])`: **chave ausente não é `false`**. Então continuam ligados `pageViews`, `sessions`, `formInteractions`, `fileDownloads` e `attribution`, e `elementInteractions`, desligado de fábrica, é ligado explicitamente pelo plugin.

Chegam sem nada da nossa parte:

| Evento | Quando |
| --- | --- |
| `[Amplitude] Page Viewed` | visita ao catálogo e a cada material |
| `[Amplitude] Form Started` | primeiro `change` num campo do formulário |
| `[Amplitude] Form Submitted` | no `submit`, **inclusive com o `preventDefault()` do `crm-leads-capture`** |
| `[Amplitude] Element Clicked` | clique em botão ou link, com texto, classe e hierarquia |

`Form Submitted` dispara mesmo com a submissão interceptada porque o SDK escuta `submit` no próprio `<form>`, e `preventDefault` cancela a ação padrão, não os listeners.

**Não instrumente nada disso de novo.** Duplicar contagem é pior do que não medir.

## Como cada formulário se identifica

O autocapture identifica um formulário por `Form ID`, `Form Name` e `Form Destination`. As duas instâncias postam para o mesmo `admin-post.php`, então o `action` não distingue nada. O que distingue são os dois atributos que `template-parts/materials/capture.php` deriva da instância:

| Instância | `id` e `name` |
| --- | --- |
| Hero | `pro-material-capture-hero-form` |
| Fechamento | `pro-material-capture-footer-form` |

É o que permite avaliar se o segundo formulário vale a pena (#232). Renomear ou remover esses atributos quebra a análise em silêncio, sem quebrar a página.

## O que o tema envia

Três eventos, e só três. Cada um existe porque nenhuma ferramenta cobre aquilo.

### `material_capture_succeeded` · `material_capture_failed`

O formulário envia por `fetch`, então sucesso e erro compartilham a mesma URL e o mesmo clique. Nem o autocapture nem o RD Station distinguem os dois: o RD, por definição, só conhece as conversões que chegaram nele.

Sem esses eventos ficam invisíveis nos dois lados: material publicado sem `url_entrega`, provedor fora do ar, e-mail recusado na validação do servidor. Uma indisponibilidade aparece como tráfego normal de um lado e silêncio do outro.

| Propriedade | Origem |
| --- | --- |
| `material_id` | campo oculto `material_id` do formulário |
| `material_slug` | `data-material-slug` no painel |
| `material_format` | `data-material-format` no painel |
| `material_category` | `data-material-category` no painel |
| `instance` | `data-capture-instance` no painel: `hero` ou `footer` |
| `error_code` | só na falha: `error_code` ou `code` da resposta REST |

### `material_filter_applied`

Até a #262, filtrar era um GET e cada filtro virava um `Page Viewed`. O filtro instantâneo trocou navegação por `fetch` + `history.replaceState`: ganho de uso que custou observabilidade. Este evento repõe o que se perdeu.

| Propriedade | Origem |
| --- | --- |
| `categories` | `material_categoria[]` da URL resultante |
| `order` | `ordenar` da URL, `recentes` quando ausente |
| `page` | `pagina` da URL, `1` quando ausente |
| `results` | texto do contador, já atualizado pela troca |

O parâmetro de ordenação chama-se `ordenar`. Escrever `ordem` produz um campo sempre vazio, que não quebra nada e não aparece em teste nenhum a não ser que alguém afirme o valor.

## Onde o contrato mora

A cadeia atravessa dois repositórios, de propósito:

```
crm-leads-capture          dispara CustomEvent "crm-leads-capture:result"
   assets/js/free-material-capture.js      { success, materialId, errorCode }
            │
            ▼
tema                       escuta, acrescenta o contexto do material e traduz
   src/main.js                             proTrack(...)
   template-parts/materials/capture.php    data attributes
```

**O plugin não sabe que Amplitude existe, e isso é deliberado.** Ele foi desacoplado de um fornecedor de design system neste mesmo ciclo; acoplá-lo a um fornecedor de analytics repetiria o erro com outra roupa. Trocar de ferramenta mexe só no tema, e quem usar o plugin com outra ferramenta continua atendido.

## Degradação

Todo envio passa por `proTrack` em `src/main.js`, que verifica `typeof window.amplitude?.track === "function"` e ainda envolve a chamada em `try/catch`.

O SDK vem de um plugin de terceiro: chave não configurada, CDN bloqueada ou plugin desativado não podem custar nada ao visitante. Há um teste em `tests/e2e/free-materials.spec.js` que remove `window.amplitude`, aplica um filtro e afirma que nenhum erro de página é lançado.

Não é preciso esperar a inicialização: o Browser SDK 2 enfileira eventos rastreados antes do `init` e os despacha depois.

## Como o RD Station entra

O Amplitude chaveia por dispositivo e sessão; o RD Station chaveia por lead. Sem um identificador comum não dá para perguntar *"dos que vieram de busca orgânica e viram o material X, quantos converteram"* — só comparar agregados entre ferramentas.

A ligação é feita **no sentido do RD**, não do Amplitude: o identificador anônimo do dispositivo viaja com o lead, e o e-mail nunca sai daqui. O cruzamento acontece no sistema que já guarda o dado pessoal.

### A cadeia

| Etapa | Onde | O quê |
| --- | --- | --- |
| 1 | `template-parts/materials/capture.php` | campo oculto `analytics_device_id`, nasce vazio |
| 2 | `src/main.js` | preenche no `submit`, em fase de captura, com `window.amplitude.getDeviceId()` |
| 3 | `class-free-material-capture.php` | sanitiza e leva adiante no `provider_context()` |
| 4 | `class-rd-station-provider.php` | envia como `cf_amplitude_device_id` |

O preenchimento é na **fase de captura** de propósito: o listener do `crm-leads-capture` está no `document` em fase de bolha, então o nosso roda antes e o valor está fresco no momento em que é lido.

### Por que o nome muda no caminho

Da etapa 1 à 3 o campo chama-se `analytics_device_id`, sem marca. Só na etapa 4, na fronteira do provider, ele vira `cf_amplitude_device_id`.

O plugin de captura não conhece o Amplitude nem o RD Station — ele carrega uma string opaca. Trocar de ferramenta de análise mexe no tema e no provider; o meio da cadeia não muda. É a mesma razão pela qual o resultado da captura vai por `CustomEvent`.

### Garantias

O valor vem do cliente e é repassado ao CRM sem outra transformação, então o plugin restringe o charset a `A-Za-z0-9._:-` e corta em 128 caracteres. Ele nunca interpreta o conteúdo.

Valores vazios são removidos pelo `array_filter` do provider: um visitante sem análise no navegador manda **um campo a menos**, não um campo vazio, e vira lead do mesmo jeito.

### Dependência fora do código

O campo customizado `cf_amplitude_device_id` precisa existir no RD Station. Sem ele o valor é enviado e descartado do outro lado, sem erro visível de nenhum dos dois lados.

### Privacidade

Guardar o identificador do dispositivo ao lado do e-mail **torna o identificador comportamental atrelável a uma pessoa dentro do RD**. Continua sendo o desenho mais conservador dos dois possíveis — a alternativa seria mandar o e-mail para o Amplitude —, mas é uma escolha consciente, não um efeito colateral.

## O que não fazemos

- Não enviar e-mail, telefone ou nome ao Amplitude.
- Não instrumentar page view, sessão, início de preenchimento, submit ou clique em card: o plugin já entrega.
- Não instrumentar busca no catálogo, que não existe.

## Como verificar

O SDK só existe em produção. Para exercitar os eventos localmente, substitua o objeto antes de agir:

```js
window.amplitude = { track: (event, properties) => console.log(event, properties) };
```

Antes de confiar em qualquer número, confirme em produção que a chave da API está configurada e que o SDK carrega de fato. Todo o comportamento descrito aqui foi verificado no código do plugin do Amplitude e no bundle que ele serve, não na instalação.
