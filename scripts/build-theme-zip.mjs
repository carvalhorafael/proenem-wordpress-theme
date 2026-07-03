import { cp, mkdir, rm, writeFile } from "node:fs/promises";
import { existsSync } from "node:fs";
import { basename, resolve } from "node:path";
import { spawnSync } from "node:child_process";

const root = resolve(import.meta.dirname, "..");
const themeName = basename(root);
const distDir = resolve(root, "dist");
const stagingDir = resolve(distDir, themeName);
const zipPath = resolve(distDir, `${themeName}.zip`);

const includePaths = [
  "style.css",
  "functions.php",
  "404.php",
  "archive.php",
  "comments.php",
  "footer.php",
  "front-page.php",
  "header.php",
  "index.php",
  "page.php",
  "search.php",
  "searchform.php",
  "single.php",
  "theme.json",
  "page-templates",
  "README.md",
  "readme.txt",
  "LICENSE.md",
  "screenshot.png",
  "inc",
  "languages",
  "template-parts",
  "assets/images",
  "assets/dist",
];

if (!existsSync(resolve(root, "assets/dist/.vite/manifest.json"))) {
  throw new Error("assets/dist/.vite/manifest.json not found. Run npm run build first.");
}

await rm(distDir, { recursive: true, force: true });
await mkdir(stagingDir, { recursive: true });

for (const item of includePaths) {
  const source = resolve(root, item);

  if (!existsSync(source)) {
    continue;
  }

  await cp(source, resolve(stagingDir, item), {
    recursive: true,
    force: true,
  });
}

await writeFile(resolve(stagingDir, ".distignore"), "Generated package. Do not edit here.\n");

const result = spawnSync("zip", ["-qr", zipPath, themeName], {
  cwd: distDir,
  stdio: "inherit",
});

if (result.status !== 0) {
  throw new Error("Failed to create theme zip.");
}

console.log(`Created ${zipPath}`);
