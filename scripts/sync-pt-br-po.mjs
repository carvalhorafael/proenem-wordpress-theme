import { mkdir, readFile, writeFile } from "node:fs/promises";
import { dirname, resolve } from "node:path";

const root = resolve(import.meta.dirname, "..");
const potPath = resolve(root, "languages/proenem-wordpress-theme.pot");
const poPath = resolve(root, "languages/pt_BR.po");

const pot = await readFile(potPath, "utf8");

const translations = new Map([
  ["Primary menu", "Menu principal"],
  ["Footer menu", "Menu do rodape"],
  ["Proenem primary", "Proenem principal"],
  ["Proenem panel", "Painel Proenem"],
  ["Footer column 1", "Coluna 1 do rodape"],
  ["Footer column 2", "Coluna 2 do rodape"],
  ["Footer column 3", "Coluna 3 do rodape"],
  ["Footer bottom", "Linha final do rodape"],
  ["Widgets added here appear in %s.", "Widgets adicionados aqui aparecem em %s."],
  ["Post information", "Informacoes do post"],
  ["Previous", "Anterior"],
  ["Next", "Proxima"],
  ["Posts pagination", "Paginacao de posts"],
  ["Skip to content", "Pular para o conteudo"],
  ["Footer column %d", "Coluna %d do rodape"],
  ["Copyright %s Proenem.", "Copyright %s Proenem."],
  ["Latest posts", "Posts recentes"],
  ["Search results for: %s", "Resultados de busca para: %s"],
  ["Page not found", "Pagina nao encontrada"],
  [
    "The requested page could not be found. Try searching for what you need.",
    "A pagina solicitada nao foi encontrada. Tente buscar pelo que voce precisa.",
  ],
  ["Return to homepage", "Voltar para a pagina inicial"],
  ["Read more", "Ler mais"],
  ["Post tags", "Tags do post"],
  ["Page navigation", "Navegacao da pagina"],
  ["Nothing found", "Nada encontrado"],
  ["No posts yet", "Ainda nao ha posts"],
  ["Try a different search or return to the homepage.", "Tente uma busca diferente ou volte para a pagina inicial."],
  ["This archive does not have published posts yet.", "Este arquivo ainda nao tem posts publicados."],
  ["Search", "Busca"],
  ["Search...", "Buscar..."],
  ["One comment", "Um comentario"],
  ["%s comments", "%s comentarios"],
  ["Comments are closed.", "Os comentarios estao fechados."],
]);

const header = `msgid ""
msgstr ""
"Project-Id-Version: Proenem 0.1.0\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: \\n"
"PO-Revision-Date: \\n"
"Last-Translator: \\n"
"Language-Team: Proenem\\n"
"Language: pt_BR\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n > 1);\\n"
"X-Generator: Proenem scaffold\\n"
`;

const blocks = pot.split(/\n\n+/).filter((block) => block.includes("msgid "));
const rendered = [header];

for (const block of blocks) {
  if (block.startsWith('msgid ""')) {
    continue;
  }

  const msgid = block.match(/^msgid "((?:[^"\\]|\\.)*)"$/m)?.[1];

  if (!msgid) {
    rendered.push(block);
    continue;
  }

  const msgidPlural = block.match(/^msgid_plural "((?:[^"\\]|\\.)*)"$/m)?.[1];

  if (msgidPlural) {
    const singular = translations.get(unescapePo(msgid)) ?? unescapePo(msgid);
    const plural = translations.get(unescapePo(msgidPlural)) ?? unescapePo(msgidPlural);

    rendered.push(
      block
        .replace(/msgstr\[0\] "((?:[^"\\]|\\.)*)"$/m, `msgstr[0] "${escapePo(singular)}"`)
        .replace(/msgstr\[1\] "((?:[^"\\]|\\.)*)"$/m, `msgstr[1] "${escapePo(plural)}"`),
    );
    continue;
  }

  const translation = translations.get(unescapePo(msgid)) ?? unescapePo(msgid);
  rendered.push(block.replace(/msgstr "((?:[^"\\]|\\.)*)"$/m, `msgstr "${escapePo(translation)}"`));
}

await mkdir(dirname(poPath), { recursive: true });
await writeFile(poPath, `${rendered.map((block) => block.trimEnd()).join("\n\n")}\n`);

function escapePo(value) {
  return value.replace(/\\/g, "\\\\").replace(/"/g, '\\"');
}

function unescapePo(value) {
  return value.replace(/\\"/g, '"').replace(/\\\\/g, "\\");
}
