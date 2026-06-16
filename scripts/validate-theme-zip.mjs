import { existsSync } from "node:fs";
import { basename, resolve } from "node:path";
import { spawnSync } from "node:child_process";

const root = resolve(import.meta.dirname, "..");
const themeName = basename(root);
const zipPath = resolve(root, "dist", `${themeName}.zip`);

if (!existsSync(zipPath)) {
  throw new Error(`Theme zip not found: ${zipPath}`);
}

const result = spawnSync("unzip", ["-Z1", zipPath], {
  encoding: "utf8",
});

if (result.status !== 0) {
  process.stderr.write(result.stderr);
  throw new Error("Failed to inspect theme zip.");
}

const entries = result.stdout.trim().split("\n").filter(Boolean);
const required = [
  `${themeName}/style.css`,
  `${themeName}/functions.php`,
  `${themeName}/404.php`,
  `${themeName}/archive.php`,
  `${themeName}/comments.php`,
  `${themeName}/footer.php`,
  `${themeName}/front-page.php`,
  `${themeName}/header.php`,
  `${themeName}/index.php`,
  `${themeName}/page.php`,
  `${themeName}/search.php`,
  `${themeName}/searchform.php`,
  `${themeName}/single.php`,
  `${themeName}/theme.json`,
  `${themeName}/LICENSE.md`,
  `${themeName}/readme.txt`,
  `${themeName}/screenshot.png`,
  `${themeName}/languages/proenem-wordpress-theme.pot`,
  `${themeName}/languages/pt_BR.po`,
  `${themeName}/assets/dist/.vite/manifest.json`,
];
const forbidden = [
  "/node_modules/",
  "/vendor/",
  "/src/",
  "/docs/",
  "/scripts/",
  ".npmrc",
  ".wp-env.json",
  "package.json",
  "package-lock.json",
  "composer.json",
  "composer.lock",
  "vite.config.js",
];

const missing = required.filter((entry) => !entries.includes(entry));
const includedForbidden = entries.filter((entry) =>
  forbidden.some((fragment) => entry.includes(fragment)),
);

if (missing.length > 0 || includedForbidden.length > 0) {
  if (missing.length > 0) {
    console.error("Missing required zip entries:");
    missing.forEach((entry) => console.error(`- ${entry}`));
  }

  if (includedForbidden.length > 0) {
    console.error("Forbidden zip entries:");
    includedForbidden.forEach((entry) => console.error(`- ${entry}`));
  }

  process.exit(1);
}

console.log(`Validated ${zipPath}`);
