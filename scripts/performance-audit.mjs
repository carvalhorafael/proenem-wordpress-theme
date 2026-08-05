import { readFileSync } from "node:fs";
import { mkdir, writeFile } from "node:fs/promises";
import { basename, resolve } from "node:path";

const apiEndpoint = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed";
const defaultCategories = ["performance", "accessibility", "best-practices", "seo"];
const defaultStrategies = ["mobile", "desktop"];
const reportDir = resolve("reports/performance");

function loadLocalEnv() {
  if (process.env.GOOGLE_PSI_API_KEY) {
    return;
  }

  try {
    const envFile = readFileSync(resolve(".env"), "utf8");
    for (const line of envFile.split(/\r?\n/)) {
      const trimmed = line.trim();
      if (!trimmed || trimmed.startsWith("#")) {
        continue;
      }

      const separator = trimmed.indexOf("=");
      if (separator === -1) {
        continue;
      }

      const key = trimmed.slice(0, separator).trim();
      const value = trimmed.slice(separator + 1).trim().replace(/^['"]|['"]$/g, "");
      if (key && process.env[key] === undefined) {
        process.env[key] = value;
      }
    }
  } catch {
    // .env is optional. The API can still be called with an exported env var.
  }
}

function parseArgs(argv) {
  const options = {
    categories: defaultCategories,
    strategies: defaultStrategies,
    locale: process.env.PERF_LOCALE || "pt-BR",
    urls: [],
  };

  for (const arg of argv) {
    if (arg.startsWith("--strategy=")) {
      options.strategies = arg.slice("--strategy=".length).split(",").filter(Boolean);
      continue;
    }

    if (arg.startsWith("--category=")) {
      options.categories = arg.slice("--category=".length).split(",").filter(Boolean);
      continue;
    }

    if (arg.startsWith("--locale=")) {
      options.locale = arg.slice("--locale=".length);
      continue;
    }

    options.urls.push(arg);
  }

  if (process.env.PERF_URLS) {
    options.urls.push(...process.env.PERF_URLS.split(",").map((url) => url.trim()).filter(Boolean));
  }

  return options;
}

function validateOptions(options) {
  if (options.urls.length === 0) {
    throw new Error(
      "Informe pelo menos uma URL publica. Exemplo: npm run perf:pagespeed -- https://www.exemplo.com/"
    );
  }

  for (const strategy of options.strategies) {
    if (!["mobile", "desktop"].includes(strategy)) {
      throw new Error(`Estrategia invalida: ${strategy}. Use mobile, desktop ou mobile,desktop.`);
    }
  }

  for (const url of options.urls) {
    const parsed = new URL(url);
    if (!["http:", "https:"].includes(parsed.protocol)) {
      throw new Error(`URL invalida para PageSpeed Insights: ${url}`);
    }
  }
}

function score(category) {
  if (!category || typeof category.score !== "number") {
    return null;
  }

  return Math.round(category.score * 100);
}

function auditValue(lhr, id) {
  const audit = lhr.audits?.[id];
  if (!audit) {
    return null;
  }

  return {
    id,
    title: audit.title,
    displayValue: audit.displayValue || "",
    numericValue: typeof audit.numericValue === "number" ? audit.numericValue : null,
    score: typeof audit.score === "number" ? audit.score : null,
  };
}

function summarize(result) {
  const lhr = result.lighthouseResult || {};
  const categories = lhr.categories || {};
  const metrics = [
    "first-contentful-paint",
    "largest-contentful-paint",
    "cumulative-layout-shift",
    "total-blocking-time",
    "speed-index",
    "server-response-time",
  ].map((id) => auditValue(lhr, id)).filter(Boolean);

  const opportunities = Object.values(lhr.audits || {})
    .filter((audit) => audit?.details?.type === "opportunity" && typeof audit.score === "number" && audit.score < 0.9)
    .map((audit) => ({
      id: audit.id,
      title: audit.title,
      displayValue: audit.displayValue || "",
      numericValue: typeof audit.numericValue === "number" ? audit.numericValue : null,
      score: audit.score,
    }))
    .sort((a, b) => (b.numericValue || 0) - (a.numericValue || 0))
    .slice(0, 12);

  const failingAudits = Object.values(lhr.audits || {})
    .filter((audit) => audit && typeof audit.score === "number" && audit.score < 0.9)
    .map((audit) => ({
      id: audit.id,
      title: audit.title,
      displayValue: audit.displayValue || "",
      score: audit.score,
    }))
    .slice(0, 30);

  return {
    requestedUrl: lhr.requestedUrl,
    finalDisplayedUrl: lhr.finalDisplayedUrl,
    fetchTime: lhr.fetchTime,
    categories: {
      performance: score(categories.performance),
      accessibility: score(categories.accessibility),
      bestPractices: score(categories["best-practices"]),
      seo: score(categories.seo),
    },
    metrics,
    opportunities,
    failingAudits,
  };
}

function buildApiUrl(url, strategy, options) {
  const apiUrl = new URL(apiEndpoint);
  apiUrl.searchParams.set("url", url);
  apiUrl.searchParams.set("strategy", strategy);
  apiUrl.searchParams.set("locale", options.locale);

  for (const category of options.categories) {
    apiUrl.searchParams.append("category", category);
  }

  if (process.env.GOOGLE_PSI_API_KEY) {
    apiUrl.searchParams.set("key", process.env.GOOGLE_PSI_API_KEY);
  }

  return apiUrl;
}

async function runOne(url, strategy, options) {
  const apiUrl = buildApiUrl(url, strategy, options);
  const response = await fetch(apiUrl);
  const body = await response.text();

  if (!response.ok) {
    const hint = response.status === 429 && !process.env.GOOGLE_PSI_API_KEY
      ? " Configure GOOGLE_PSI_API_KEY para usar a cota do projeto Google Cloud da Proenem."
      : "";

    throw new Error(`PageSpeed Insights falhou para ${url} (${strategy}): HTTP ${response.status}.${hint} ${body.slice(0, 500)}`);
  }

  const raw = JSON.parse(body);
  return {
    url,
    strategy,
    analyzedAt: new Date().toISOString(),
    summary: summarize(raw),
    raw,
  };
}

function slugifyUrl(url) {
  const parsed = new URL(url);
  const path = parsed.pathname === "/" ? "home" : parsed.pathname.replace(/^\/|\/$/g, "").replaceAll("/", "-");
  return `${parsed.hostname}-${path}`.replace(/[^a-z0-9.-]+/gi, "-").toLowerCase();
}

function markdownReport(results) {
  const lines = [
    "# Performance Audit",
    "",
    `Generated at: ${new Date().toISOString()}`,
    "",
    "## Scores",
    "",
    "| URL | Strategy | Performance | Accessibility | Best Practices | SEO |",
    "| --- | --- | ---: | ---: | ---: | ---: |",
  ];

  for (const result of results) {
    const categories = result.summary.categories;
    lines.push(
      `| ${result.url} | ${result.strategy} | ${categories.performance ?? "n/a"} | ${categories.accessibility ?? "n/a"} | ${categories.bestPractices ?? "n/a"} | ${categories.seo ?? "n/a"} |`
    );
  }

  for (const result of results) {
    lines.push("", `## ${result.url} (${result.strategy})`, "", "### Metrics", "");
    for (const metric of result.summary.metrics) {
      lines.push(`- ${metric.title}: ${metric.displayValue || metric.numericValue || "n/a"}`);
    }

    lines.push("", "### Top Opportunities", "");
    if (result.summary.opportunities.length === 0) {
      lines.push("- No PageSpeed opportunity audit failed.");
    } else {
      for (const opportunity of result.summary.opportunities.slice(0, 8)) {
        const value = opportunity.displayValue ? ` (${opportunity.displayValue})` : "";
        lines.push(`- ${opportunity.title}${value} [${opportunity.id}]`);
      }
    }

    lines.push("", "### Failing Audits", "");
    if (result.summary.failingAudits.length === 0) {
      lines.push("- No failing audit under score 0.9.");
    } else {
      for (const audit of result.summary.failingAudits.slice(0, 12)) {
        const value = audit.displayValue ? ` (${audit.displayValue})` : "";
        lines.push(`- ${audit.title}${value} [${audit.id}]`);
      }
    }
  }

  return `${lines.join("\n")}\n`;
}

async function main() {
  loadLocalEnv();

  const options = parseArgs(process.argv.slice(2));
  validateOptions(options);
  await mkdir(reportDir, { recursive: true });

  const results = [];
  for (const url of options.urls) {
    for (const strategy of options.strategies) {
      console.log(`Auditing ${url} (${strategy})...`);
      results.push(await runOne(url, strategy, options));
    }
  }

  const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
  const suffix = options.urls.length === 1 ? slugifyUrl(options.urls[0]) : "batch";
  const jsonPath = resolve(reportDir, `pagespeed-${timestamp}-${suffix}.json`);
  const mdPath = resolve(reportDir, `pagespeed-${timestamp}-${suffix}.md`);

  await writeFile(jsonPath, `${JSON.stringify({ results }, null, 2)}\n`);
  await writeFile(mdPath, markdownReport(results));

  console.log(`Wrote ${basename(jsonPath)}`);
  console.log(`Wrote ${basename(mdPath)}`);
}

main().catch((error) => {
  console.error(error.message);
  process.exitCode = 1;
});
