# Instrucoes para agentes

## Contexto do projeto

Este repositorio contem o tema WordPress da Proenem.

O tema deve ser tratado como uma camada de apresentacao e adaptacao WordPress. Nao coloque regra de negocio duravel dentro do tema sem decisao explicita registrada em `docs/theme-decisions.md`. Quando surgirem tipos de conteudo, taxonomias, metadados ou fluxos que precisem sobreviver a troca de tema, prefira plugin dedicado.

O projeto `/Users/rafaelcarvalho/Development/executive-signal-wordpress-theme` pode ser usado como referencia de arquitetura, organizacao, qualidade, testes, empacotamento e boas praticas WordPress. Ele nao deve ser usado como referencia visual, textual ou de identidade para a Proenem.

## Design system e identidade visual

Nao invente direcao visual, tokens de marca, componentes proprietarios ou conteudo editorial definitivo antes de haver especificacao da Proenem.

Enquanto nao houver pacote de design system Proenem publicado para consumo, mantenha a UI local como fundacao neutra, acessivel e facil de substituir. Quando um design system Proenem estiver disponivel:

1. verifique se tokens, CSS, componentes, patterns ou contratos existentes cobrem o caso;
2. consuma o contrato existente sempre que possivel;
3. registre em `docs/theme-decisions.md` qualquer adaptacao local relevante;
4. se o gap parecer reutilizavel fora do tema, documente o workaround e abra o fluxo apropriado no repositorio do design system antes de consolidar a solucao.

## Ambiente local WordPress

O ambiente local usa `@wordpress/env`, que roda WordPress via Docker.

Comandos principais:

```bash
npm install
npm run dev
```

`npm run dev` deve ser o comando padrao para trabalhar localmente. Ele sobe o WordPress local, instala dependencias PHP dentro do container e inicia o Vite.

URLs locais:

- Site: http://localhost:8898/
- Admin: http://localhost:8898/wp-admin
- Login: http://localhost:8898/wp-login.php

Credenciais locais padrao do `wp-env`:

- Usuario: `admin`
- Senha: `password`

Essas credenciais sao apenas do ambiente local de desenvolvimento. Nao usar como referencia para producao, staging ou qualquer ambiente real.

## Fluxo de branches

Regra padrao:

- nao desenvolver diretamente em `main`;
- criar uma branch de trabalho antes de alterar codigo;
- usar prefixo `codex/` para branches criadas por agentes;
- fazer commits pequenos e intencionais;
- fazer push da branch para `origin` quando solicitado;
- abrir PRs pequenos por padrao.

Antes de comecar uma nova tarefa, sempre verificar:

```bash
git status --short --branch
git branch -vv
```

Se o checkout estiver em `main`, crie uma branch de trabalho antes de editar arquivos, salvo quando a tarefa for explicitamente uma operacao de release ou manutencao em `main`.

## Internacionalizacao

O tema deve respeitar as boas praticas de internacionalizacao do WordPress.

Regras para novos textos:

- usar sempre o text domain `proenem-wordpress-theme`;
- nao introduzir texto visivel hardcoded em PHP, templates ou patterns sem funcao de traducao;
- para texto HTML visivel, usar `esc_html__()` ou `esc_html_e()`;
- para atributos, usar `esc_attr__()` ou `esc_attr_e()`;
- para strings que precisam de contexto, usar `_x()`, `esc_html_x()` ou `esc_attr_x()`;
- para plural, usar `_n()` ou `_nx()`;
- para strings com placeholders, adicionar comentario `translators`;
- quando a mudanca adicionar, remover ou alterar strings traduziveis, rodar `npm run i18n` e commitar as alteracoes em `languages/`;
- antes de considerar uma mudanca pronta, usar `npm run i18n:check` ou `npm run validate`.

O idioma base inicial e `pt_BR`. O arquivo `languages/pt_BR.po` funciona como catalogo identidade ate que outro fluxo de traducao seja decidido.

## Politica de testes automatizados

Nao tente testar tudo. A suite deve proteger contratos, fluxos criticos e bugs reais, sem virar uma colecao fragil de testes de detalhe visual.

Camadas esperadas:

- `npm run test:static`: build Vite, i18n, sintaxe PHP, PHPCS e Theme Check;
- `npm run test:php`: PHPUnit dentro do WordPress de testes do `wp-env`;
- `npm run test:e2e`: Playwright para smoke do front-end e do editor;
- `npm test`: gate automatizado padrao para PRs;
- `npm run validate`: gate completo de release e empacotamento.

O que deve ser testado:

- setup WordPress do tema: theme supports, menus, text domain, enqueues e bootstrap basico;
- contratos com o editor de blocos: `theme.json`, styles editoriais, classes e carregamento de assets;
- helpers PHP proprios: escaping, fallback e geracao de markup;
- interacoes criticas no navegador: homepage, menu, ausencia de erros graves de console e acessibilidade automatizada basica;
- empacotamento: ZIP gerado, allowlist do pacote e instalacao limpa em WordPress de testes.

O que nao deve ser testado aqui:

- regras de negocio de produto que pertencam a plugins ou sistemas externos;
- cada classe CSS, token ou variacao visual;
- snapshots visuais amplos para mudancas cosmeticas pequenas;
- cada texto estatico, salvo quando o texto fizer parte de contrato funcional.

## Releases e tags

A decisao de criar uma nova release e humana. O usuario deve avisar explicitamente quando quiser preparar uma release, por exemplo: "preparar release 0.1.0".

Rotina padrao de release:

1. atualizar a versao em `package.json`;
2. atualizar `Version` em `style.css`;
3. atualizar `Stable tag` em `readme.txt`;
4. rodar `npm run validate`;
5. abrir PR com as mudancas de release.

Nao crie tags manualmente por padrao. A tag manual so deve ser usada se houver decisao explicita de recuperacao.

## Validade de distribuicao

Se o tema for distribuido fora do WordPress.org, use `Update URI` em `style.css` e mantenha uma validacao especifica para esse caso no Theme Check. Nao embuta credenciais no tema para acessar releases privadas. Distribuicao privada precisa de intermediario seguro.
