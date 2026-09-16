#!/usr/bin/env node
/**
 * Fail when the theme uses a design system custom property that does not exist.
 *
 * A `var()` pointing at an undefined property with no fallback makes the whole
 * declaration invalid at computed-value time, and CSS drops it silently. That
 * is how `--pen-space-7`, which is not on the scale, left the material hero
 * with no horizontal padding at all without any build or lint complaining.
 */

import { readFileSync, readdirSync } from "node:fs";
import { join } from "node:path";

const TOKENS_DIR = "node_modules/@carvalhorafael/proenem-tokens";
const STYLESHEETS = ["src/styles/theme.css", "src/styles/main.css", "src/styles/editor.css"];

const declared = new Set();

for (const file of readdirSync(TOKENS_DIR).filter((name) => name.endsWith(".css"))) {
  const css = readFileSync(join(TOKENS_DIR, file), "utf8");

  for (const [, name] of css.matchAll(/(--pen-[a-z0-9-]+)\s*:/g)) {
    declared.add(name);
  }
}

let failures = 0;

for (const stylesheet of STYLESHEETS) {
  let css;

  try {
    css = readFileSync(stylesheet, "utf8");
  } catch {
    continue;
  }

  // Properties the theme defines itself are just as valid.
  for (const [, name] of css.matchAll(/^\s*(--[a-z0-9-]+)\s*:/gm)) {
    declared.add(name);
  }

  const lines = css.split("\n");

  lines.forEach((line, index) => {
    // Only flag var() without a fallback: with one, the declaration survives.
    for (const [, name] of line.matchAll(/var\(\s*(--pen-[a-z0-9-]+)\s*\)/g)) {
      if (!declared.has(name)) {
        failures += 1;
        console.error(`${stylesheet}:${index + 1}  ${name} não existe nos tokens`);
        console.error(`  ${line.trim()}`);
      }
    }
  });
}

if (failures > 0) {
  console.error(`\n${failures} uso(s) de token inexistente sem fallback.`);
  process.exit(1);
}

console.log("Tokens do design system: OK");
