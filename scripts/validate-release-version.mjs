import { readFile } from "node:fs/promises";
import { resolve } from "node:path";

const root = resolve(import.meta.dirname, "..");
const tag = process.argv[2] ?? process.env.TAG_NAME;

if (!tag || !/^v\d+\.\d+\.\d+$/.test(tag)) {
  throw new Error("Expected a semver tag in the format vX.Y.Z.");
}

const expectedVersion = tag.slice(1);
const packageJson = JSON.parse(await readFile(resolve(root, "package.json"), "utf8"));
const styleCss = await readFile(resolve(root, "style.css"), "utf8");
const readme = await readFile(resolve(root, "readme.txt"), "utf8");

const styleVersion = styleCss.match(/^Version:\s*(.+)$/m)?.[1]?.trim();
const stableTag = readme.match(/^Stable tag:\s*(.+)$/m)?.[1]?.trim();

const mismatches = [
  ["package.json", packageJson.version],
  ["style.css", styleVersion],
  ["readme.txt", stableTag],
].filter(([, version]) => version !== expectedVersion);

if (mismatches.length > 0) {
  console.error(`Release version mismatch for ${tag}:`);
  for (const [file, version] of mismatches) {
    console.error(`- ${file}: ${version ?? "missing"}`);
  }
  process.exit(1);
}

console.log(`Release version ${expectedVersion} matches ${tag}.`);
