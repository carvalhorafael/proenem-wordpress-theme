# Variantes de hero da home

Este documento cobre os templates de home criados para testar conversão na primeira dobra. Ele complementa `docs/home-cta-map.md`, que continua sendo o contrato de destinos de CTA.

## O que existe

| Template | Nome no admin | Papel |
| --- | --- | --- |
| `page-templates/home.php` | Proenem Home | Controle. Não foi alterado. |
| `page-templates/home-variant-oferta.php` | Proenem Home - Variante A (Oferta direta) | Variante de teste |
| `page-templates/home-variant-prova.php` | Proenem Home - Variante B (Prova social) | Variante de teste |

As três páginas renderizam exatamente o mesmo conteúdo abaixo da primeira dobra. Só o hero muda.

- O corpo compartilhado vive em `template-parts/home/sections.php`, que hoje é uma cópia fiel do corpo do controle.
- Os helpers compartilhados vivem em `inc/home-shared.php`: metadados de imagem, detecção de superfície de home, resumo da oferta e listas de prova social.
- O CSS local está em `src/styles/theme.css`, no bloco `Home hero variants`.

O controle continua com o próprio corpo embutido. Isso é intencional: o objetivo era não tocar no template atual enquanto o teste roda. Quando um vencedor for promovido, o controle passa a consumir o partial e a duplicação some.

## Hipóteses

**Variante A - Oferta direta.** Layout em duas colunas. A oferta, o preço parcelado, a garantia e a ação de checkout ficam dentro da primeira dobra, num cartão branco que é a única superfície clara do bloco vermelho.

> Hipótese: o controle promete resultado mas não mostra preço nem oferta antes do scroll, e a ação primária apenas rola até a âncora de planos. Antecipar preço e garantia, com um único botão que leva ao checkout, deve aumentar o início de checkout.

**Variante B - Prova social.** Layout centralizado. Selo de aprovados, título de resultado, uma única ação, preço e garantia como microcópia sob o botão, fotos de aprovados e faixa de universidades.

> Hipótese: tráfego frio trava em confiança antes de travar em preço. Uma primeira dobra liderada por aprovação verificada deve converter melhor do que uma dobra que só promete.

## Boas práticas aplicadas nas duas variantes

- Uma única ação primária por dobra, sem CTA concorrente de mesmo peso.
- Ação primária em amarelo sobre vermelho, o par de maior contraste da paleta, para o botão não competir com o fundo nem com a navbar.
- Ação primária vai direto ao checkout aprovado, em vez de rolar até a âncora de planos.
- Preço e forma de pagamento visíveis antes do scroll.
- Reversão de risco explícita: garantia de 7 dias e cancelamento.
- Prova social específica: número de aprovados, fotos de alunos e logotipos de universidades.
- Benefícios escritos como entrega concreta, não como adjetivo.
- Em telas estreitas o bloco de ação sobe para logo abaixo do subtítulo, para preço e botão caberem na primeira tela; benefícios e foto vêm depois.
- A barra móvel persistente de cada variante repete o rótulo da ação primária daquela variante.

## Destinos de CTA das variantes

As duas variantes vendem a mesma oferta e usam o mesmo rótulo e o mesmo destino na ação primária. O teste isola o desenho da dobra, não a oferta.

| Posição | Label | Intenção | Destino |
| --- | --- | --- | --- |
| Hero das duas variantes | QUERO MÉTODO PRO | Contratação | `https://pay.hotmart.com/T102416176R?off=5na5b8bl&checkoutMode=10` |
| Hero da variante A, secundário | Ver tudo o que está incluído | Secundário | `/#planos` |
| Hero da variante B, secundário | Ver o que está incluído | Secundário | `/#planos` |
| Barra móvel da variante A | Quero Método PRO | Contratação | Mesmo checkout do hero |
| Barra móvel da variante B | Começar agora | Contratação | Mesmo checkout do hero |

A oferta vive em `proenem_get_home_offer()`. O código de checkout dela é diferente do da Turma Intensiva e não entra em `proenem_get_home_cta_destination()` de propósito: aquele mapa é o contrato durável da home, e este destino só existe enquanto o teste roda.

O rótulo é escrito em caixa natural no catálogo e sobe para maiúsculas por `text-transform`, para o leitor de tela não soletrar a sigla.

Ir direto ao checkout na primeira dobra é a diferença deliberada de contrato entre variante e controle. Ela é parte do que está sendo medido.

Cada link do hero carrega `data-pro-hero-variant` e `data-pro-hero-action`, para a ferramenta de medição distinguir os cliques sem depender do texto do botão.

## Como publicar as páginas de teste

1. Crie uma página no WordPress para cada variante.
2. Em **Atributos da página > Modelo**, escolha `Proenem Home - Variante A (Oferta direta)` ou `Proenem Home - Variante B (Prova social)`.
3. Publique e aponte o tráfego do teste para as URLs criadas.

O tema não faz o split de tráfego. A divisão fica com a ferramenta de teste ou com a campanha de mídia, que envia cada braço para uma URL.

`front-page.php` continua renderizando o controle. Nenhuma variante substitui a home enquanto o teste roda.

## Antes de ligar o teste

- Abra o checkout e confirme nome da oferta, preço, garantia e ausência de mensagem de oferta expirada.
- Confirme que o preço do hero bate com o cartão de plano da seção de preços. Os dois saem de fontes diferentes: o hero usa `proenem_get_home_offer()`, o cartão usa a lista de planos do corpo compartilhado, que ainda aponta para o checkout da Turma Intensiva.
- O link secundário `/#planos` leva ao cartão da Turma Intensiva, que é outra oferta. A seção de planos ainda vai ser trabalhada.
- Confirme o número de aprovados divulgado. As variantes exibem `+ de 40.000`, mesmo número já usado na seção de prova da home.

## Ajustes por filtro, sem release

- `proenem_home_offer` altera nome, preço, detalhe, garantia e destinos da oferta exibida nos dois heros.
- `proenem_home_exam_date` define a data da prova como `AAAA-MM-DD` e liga a linha de contagem regressiva da variante B. Sem o filtro, a linha não aparece: uma contagem inventada ou vencida é pior do que nenhuma.
- `proenem_home_templates` inclui outros templates na detecção de superfície de home, usada por `header.php`, `footer.php`, classes de body e descarte de assets.

## Quando o teste terminar

1. Promova o hero vencedor para `page-templates/home.php`.
2. Faça o controle consumir `template-parts/home/sections.php` e apague o corpo duplicado.
3. Apague os templates de variante e o CSS local que ficou sem uso.
4. Atualize `docs/home-cta-map.md` com o destino vencedor da ação primária.

## Gap de design system

O hero publicado em `@carvalhorafael/proenem-css` (`pen-hero-section`) assume uma promessa centralizada com a foto do estudante como palco. Nenhuma das duas variantes cabe nesse contrato: a A precisa de layout em duas colunas com bloco de oferta, e a B de uma pilha liderada por prova.

As variantes continuam consumindo do pacote o fundo, os `pen-hero-section__emphasis`, os `pen-hero-sticker` e o `pen-button`. Só o layout é local.

Enquanto isso, as classes `pro-hero-offer__*`, `pro-hero-proof__*` e `pro-hero-cta__label` são adaptação local no tema, e precisam das duas issues de tracking previstas em `AGENTS.md` caso o hero vencedor seja promovido em vez de descartado.
