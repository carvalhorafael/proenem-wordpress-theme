# Mapa de CTAs da home

Este documento define o contrato de conversão da home da Proenem. Ele cobre o template PHP, os widgets Elementor, o JSON de importação e os dados persistidos do WordPress.

## Taxonomia

| Tipo | Intenção | Regra de destino |
| --- | --- | --- |
| Primário | Conhecer a Turma Intensiva | `/#planos` |
| Secundário | Consultar a oferta e o preço | `/#planos` |
| Exploração | Experimentar uma parte específica da plataforma | Destino funcional correspondente na plataforma |
| Contratação | Comprar um plano pago | Checkout aprovado da oferta correspondente |
| B2B | Falar sobre parceria com escola | Fluxo institucional separado |

Em um mesmo contexto deve existir no máximo uma ação primária e uma secundária para estudantes. CTAs B2B não compartilham agrupamento nem linguagem com a jornada do estudante.

## Navbar

Os itens e seus destinos são definidos pelo menu WordPress `primary`. O renderer preserva literalmente a URL configurada para cada item, inclusive `#`; scripts operacionais do tema não reescrevem esse menu.

| Label | Intenção | Destino |
| --- | --- | --- |
| Planos | Secundário | `/#planos` |
| Questões | Exploração | `https://estude.proenem.com.br/treino/questoes` |
| Aprovados | Navegação | `/#aprovados` |
| FAQ | Navegação | `/#faq` |
| Materiais gratuitos | Exploração | Página WordPress de materiais gratuitos |
| Conheça a Turma Intensiva | Primário | `/#planos` |
| Entrar | Acesso | Destino persistido no menu WordPress |
| Acessar Proenem | Acesso | Destino persistido no submenu |
| Acessar Promedicina | Acesso | Destino persistido no submenu |

## Jornada do estudante

| Posição | Label | Intenção | Destino | Fontes |
| --- | --- | --- | --- | --- |
| Barra de ação do hero | Conheça a Turma Intensiva | Primário | `/#planos` | PHP, widget, JSON e dados persistidos |
| Método PRO, pilares | Ver a Turma Intensiva | Primário | `/#planos` | PHP, widget, JSON e dados persistidos |
| Dores, após método e acompanhamento | Comece agora | Primário | `/#planos` | PHP, widget, JSON e dados persistidos |
| Barra mobile após 600 px | Ver plano e preço | Primário persistente | `/#planos` | Renderer compartilhado do navbar e widget Elementor |
| Cards de disciplinas | Nome da disciplina | Exploração | Página funcional da disciplina | PHP, repeater Elementor e JSON |
| Banco de questões | Conheça a Turma Intensiva | Primário | `/#planos` | PHP, widget, JSON e dados persistidos |
| Turma Intensiva 2026 | Quero a Turma Intensiva | Contratação | `https://pay.hotmart.com/W106752534O?off=jg51ayrs&checkoutMode=10` | PHP, defaults do widget e dados persistidos |
| Depoimentos | Ver mais | Prova social | `https://aprovados.proenem.com.br/` | PHP, widget e JSON |

O plano Grátis e o Método PRO Avançado estão fora da oferta atual da home. O destino `advanced` permanece no contrato interno somente para compatibilidade e uma possível reativação futura; o renderer e a sincronização operacional não exibem dados persistidos dessas ofertas.

O checkout da Turma Intensiva mantém somente o código da oferta e o modo de checkout. Parâmetros de campanha, UTMs, `src` e identificadores de sessão não fazem parte do destino persistido no tema. Uma futura atribuição dinâmica deve seguir o contrato de mensuração da issue #35.

## Variantes de hero em teste

As páginas de teste de conversão da home têm contrato próprio para a ação da primeira dobra: ela vai direto ao checkout, em vez de rolar até `/#planos`. Essa divergência é deliberada e faz parte do que está sendo medido.

Os destinos, os rótulos e o ciclo de vida dessas páginas estão em [`home-hero-variants.md`](home-hero-variants.md). Nada aqui muda para o controle `page-templates/home.php`.

## Jornada B2B

| Posição | Label | Intenção | Destino |
| --- | --- | --- | --- |
| Seção para escolas | Falar com nossa equipe | B2B | `mailto:pro-receita@questedu.dev?subject=Parceria%20com%20escola` |
| CTA institucional final | Falar com nossa equipe | B2B | `mailto:pro-receita@questedu.dev?subject=Parceria%20com%20escola` |

## Sincronização operacional

Após publicar o tema em um ambiente que já possua menu ou home Elementor persistidos, execute:

```bash
wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/sync-home-conversion.php
wp eval-file wp-content/themes/proenem-wordpress-theme/scripts/sync-home-plans.php
```

Os scripts são idempotentes. O primeiro atualiza os destinos de conversão conhecidos nas páginas Elementor e troca no menu `primary` somente itens com os rótulos legados `Comece grátis` ou `Criar conta grátis`; o segundo remove a oferta avançada e atualiza a FAQ somente em páginas Elementor que ainda tenham os valores editoriais conhecidos. Depois da execução, revise visualmente a home e confirme os links antes de promover o ambiente.

Antes da publicação, abra o checkout aprovado e confirme o nome da Turma Intensiva, preço, garantia e ausência de mensagens de oferta expirada.

## Gap temporário do design system

A barra mobile persistente é uma adaptação local acompanhada em:

- design system: [proenem-design-system-brand-guide#38](https://github.com/carvalhorafael/proenem-design-system-brand-guide/issues/38);
- tema: [proenem-wordpress-theme#110](https://github.com/carvalhorafael/proenem-wordpress-theme/issues/110).

Quando o design system publicar o pattern, atualizar os pacotes, migrar o renderer compartilhado e remover as classes `pro-mobile-persistent-action` e o comportamento local correspondente.
