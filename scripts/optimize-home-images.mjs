import { mkdir, stat, writeFile } from "node:fs/promises";
import { resolve } from "node:path";
import sharp from "sharp";

const imageDir = resolve("assets/images/home");
const reportDir = resolve("reports/images");

const targets = [
  {
    input: "student_school_1.png",
    output: "student_school_1.webp",
    maxWidth: 1280,
    quality: 84,
  },
  {
    input: "student_school_2.png",
    output: "student_school_2.webp",
    maxWidth: 900,
    quality: 84,
  },
  {
    input: "proof-students-2.png",
    output: "proof-students-2.webp",
    maxWidth: 720,
    quality: 84,
  },
  {
    input: "proof-students-3.png",
    output: "proof-students-3.webp",
    maxWidth: 720,
    quality: 84,
  },
  {
    input: "proof-students-4.png",
    output: "proof-students-4.webp",
    maxWidth: 720,
    quality: 84,
  },
  {
    input: "proof-students-5.png",
    output: "proof-students-5.webp",
    maxWidth: 720,
    quality: 84,
  },
  {
    input: "proof-students-6.png",
    output: "proof-students-6.webp",
    maxWidth: 720,
    quality: 84,
  },
];

function formatBytes(bytes) {
  if (bytes < 1024) {
    return `${bytes} B`;
  }

  if (bytes < 1024 * 1024) {
    return `${(bytes / 1024).toFixed(1)} KiB`;
  }

  return `${(bytes / 1024 / 1024).toFixed(2)} MiB`;
}

async function optimizeImage(target) {
  const inputPath = resolve(imageDir, target.input);
  const outputPath = resolve(imageDir, target.output);
  const inputStats = await stat(inputPath);
  const metadata = await sharp(inputPath).metadata();

  let pipeline = sharp(inputPath).rotate();
  if (metadata.width && metadata.width > target.maxWidth) {
    pipeline = pipeline.resize({
      width: target.maxWidth,
      withoutEnlargement: true,
    });
  }

  await pipeline
    .webp({
      quality: target.quality,
      effort: 6,
      smartSubsample: true,
    })
    .toFile(outputPath);

  const outputStats = await stat(outputPath);
  const outputMetadata = await sharp(outputPath).metadata();
  const savings = 1 - outputStats.size / inputStats.size;

  return {
    input: target.input,
    output: target.output,
    originalSize: inputStats.size,
    optimizedSize: outputStats.size,
    originalDimensions: `${metadata.width || "?"}x${metadata.height || "?"}`,
    optimizedDimensions: `${outputMetadata.width || "?"}x${outputMetadata.height || "?"}`,
    quality: target.quality,
    savingsPercent: Number((savings * 100).toFixed(1)),
  };
}

function markdownReport(results) {
  const lines = [
    "# Home Image Optimization",
    "",
    `Generated at: ${new Date().toISOString()}`,
    "",
    "| Source | Output | Dimensions | Size Before | Size After | Savings |",
    "| --- | --- | --- | ---: | ---: | ---: |",
  ];

  for (const result of results) {
    lines.push(
      `| ${result.input} | ${result.output} | ${result.originalDimensions} -> ${result.optimizedDimensions} | ${formatBytes(result.originalSize)} | ${formatBytes(result.optimizedSize)} | ${result.savingsPercent}% |`
    );
  }

  return `${lines.join("\n")}\n`;
}

async function main() {
  await mkdir(reportDir, { recursive: true });

  const results = [];
  for (const target of targets) {
    const result = await optimizeImage(target);
    results.push(result);
    console.log(
      `${result.input} -> ${result.output}: ${formatBytes(result.originalSize)} -> ${formatBytes(result.optimizedSize)} (${result.savingsPercent}% smaller)`
    );
  }

  const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
  const jsonPath = resolve(reportDir, `home-image-optimization-${timestamp}.json`);
  const mdPath = resolve(reportDir, `home-image-optimization-${timestamp}.md`);
  await writeFile(jsonPath, `${JSON.stringify({ results }, null, 2)}\n`);
  await writeFile(mdPath, markdownReport(results));

  console.log(`Wrote ${jsonPath}`);
  console.log(`Wrote ${mdPath}`);
}

main().catch((error) => {
  console.error(error.message);
  process.exitCode = 1;
});
