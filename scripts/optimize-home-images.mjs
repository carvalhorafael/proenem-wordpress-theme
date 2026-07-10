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

const responsiveVariants = [
  "proof-students-1.webp",
  "proof-students-2.webp",
  "proof-students-3.webp",
  "proof-students-4.webp",
  "proof-students-5.webp",
  "proof-students-6.webp",
];

const sizedVariants = [
  {
    input: "hero-student.webp",
    output: "hero-student-720.webp",
    width: 720,
    quality: 82,
  },
  {
    input: "hero-student.webp",
    output: "hero-student-820.webp",
    width: 820,
    quality: 82,
  },
  {
    input: "hero-student.webp",
    output: "hero-student-780.webp",
    width: 780,
    quality: 82,
  },
  {
    input: "pillar-meta.webp",
    output: "pillar-meta-520.webp",
    width: 520,
    quality: 82,
  },
  {
    input: "pillar-execucao.webp",
    output: "pillar-execucao-280.webp",
    width: 280,
    quality: 82,
  },
  {
    input: "pillar-execucao.webp",
    output: "pillar-execucao-320.webp",
    width: 320,
    quality: 82,
  },
  {
    input: "pillar-execucao.webp",
    output: "pillar-execucao-360.webp",
    width: 360,
    quality: 82,
  },
  {
    input: "pillar-diagnostico.webp",
    output: "pillar-diagnostico-280.webp",
    width: 280,
    quality: 82,
  },
  {
    input: "pillar-diagnostico.webp",
    output: "pillar-diagnostico-320.webp",
    width: 320,
    quality: 82,
  },
  {
    input: "pillar-diagnostico.webp",
    output: "pillar-diagnostico-360.webp",
    width: 360,
    quality: 82,
  },
  {
    input: "proof-students-1.webp",
    output: "proof-students-1-240.webp",
    width: 240,
    quality: 80,
  },
  {
    input: "proof-students-2.webp",
    output: "proof-students-2-240.webp",
    width: 240,
    quality: 80,
  },
  {
    input: "proof-students-3.webp",
    output: "proof-students-3-240.webp",
    width: 240,
    quality: 80,
  },
  {
    input: "proof-students-4.webp",
    output: "proof-students-4-240.webp",
    width: 240,
    quality: 80,
  },
  {
    input: "proof-students-5.webp",
    output: "proof-students-5-240.webp",
    width: 240,
    quality: 80,
  },
  {
    input: "proof-students-6.webp",
    output: "proof-students-6-240.webp",
    width: 240,
    quality: 80,
  },
];

const logoVariants = [
  "proof-logo-uerj.png",
  "proof-logo-ufrgs.png",
  "proof-logo-ufrj.png",
  "proof-logo-unicamp.png",
  "proof-logo-unifesp.png",
  "proof-logo-usp.png",
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

async function createResponsiveVariant(filename) {
  const inputPath = resolve(imageDir, filename);
  const output = filename.replace(/\.webp$/u, "-360.webp");
  const outputPath = resolve(imageDir, output);
  const inputStats = await stat(inputPath);
  const inputMetadata = await sharp(inputPath).metadata();

  await sharp(inputPath)
    .rotate()
    .resize({
      width: 360,
      withoutEnlargement: true,
    })
    .webp({
      quality: 82,
      effort: 6,
      smartSubsample: true,
    })
    .toFile(outputPath);

  const outputStats = await stat(outputPath);
  const outputMetadata = await sharp(outputPath).metadata();
  const savings = 1 - outputStats.size / inputStats.size;

  return {
    input: filename,
    output,
    originalSize: inputStats.size,
    optimizedSize: outputStats.size,
    originalDimensions: `${inputMetadata.width || "?"}x${inputMetadata.height || "?"}`,
    optimizedDimensions: `${outputMetadata.width || "?"}x${outputMetadata.height || "?"}`,
    quality: 82,
    savingsPercent: Number((savings * 100).toFixed(1)),
  };
}

async function createSizedVariant(target) {
  const inputPath = resolve(imageDir, target.input);
  const outputPath = resolve(imageDir, target.output);
  const inputStats = await stat(inputPath);
  const inputMetadata = await sharp(inputPath).metadata();

  await sharp(inputPath)
    .rotate()
    .resize({
      width: target.width,
      withoutEnlargement: true,
    })
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
    originalDimensions: `${inputMetadata.width || "?"}x${inputMetadata.height || "?"}`,
    optimizedDimensions: `${outputMetadata.width || "?"}x${outputMetadata.height || "?"}`,
    quality: target.quality,
    savingsPercent: Number((savings * 100).toFixed(1)),
  };
}

async function createLogoVariant(filename) {
  const inputPath = resolve(imageDir, filename);
  const output = filename.replace(/\.png$/u, ".webp");
  const outputPath = resolve(imageDir, output);
  const inputStats = await stat(inputPath);
  const inputMetadata = await sharp(inputPath).metadata();

  await sharp(inputPath)
    .rotate()
    .webp({
      quality: 82,
      effort: 6,
      smartSubsample: true,
    })
    .toFile(outputPath);

  const outputStats = await stat(outputPath);
  const outputMetadata = await sharp(outputPath).metadata();
  const savings = 1 - outputStats.size / inputStats.size;

  return {
    input: filename,
    output,
    originalSize: inputStats.size,
    optimizedSize: outputStats.size,
    originalDimensions: `${inputMetadata.width || "?"}x${inputMetadata.height || "?"}`,
    optimizedDimensions: `${outputMetadata.width || "?"}x${outputMetadata.height || "?"}`,
    quality: 82,
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

  for (const filename of responsiveVariants) {
    const result = await createResponsiveVariant(filename);
    results.push(result);
    console.log(
      `${result.input} -> ${result.output}: ${formatBytes(result.originalSize)} -> ${formatBytes(result.optimizedSize)} (${result.savingsPercent}% smaller)`
    );
  }

  for (const target of sizedVariants) {
    const result = await createSizedVariant(target);
    results.push(result);
    console.log(
      `${result.input} -> ${result.output}: ${formatBytes(result.originalSize)} -> ${formatBytes(result.optimizedSize)} (${result.savingsPercent}% smaller)`
    );
  }

  for (const filename of logoVariants) {
    const result = await createLogoVariant(filename);
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
