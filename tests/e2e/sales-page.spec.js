import AxeBuilder from "@axe-core/playwright";
import { expect, test } from "@playwright/test";

const SALES_PAGE = "/lp/homologacao-widgets-lp/";

/**
 * The sales page fixture depends on the sales-page plugin, which the base
 * wp-env config does not mount. Skip instead of failing when it is absent.
 */
const gotoSalesPage = async (page) => {
  const response = await page.goto(SALES_PAGE);
  const available = Boolean(response) && response.status() === 200;

  test.skip(!available, "A pagina de venda de fixture nao esta disponivel neste ambiente.");

  return available;
};

const relativeLuminance = ([r, g, b]) => {
  const channel = (value) => {
    const ratio = value / 255;

    return ratio <= 0.03928 ? ratio / 12.92 : ((ratio + 0.055) / 1.055) ** 2.4;
  };

  return 0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b);
};

const contrastRatio = (foreground, background) => {
  const first = relativeLuminance(foreground);
  const second = relativeLuminance(background);
  const lighter = Math.max(first, second);
  const darker = Math.min(first, second);

  return (lighter + 0.05) / (darker + 0.05);
};

test("sales page bands span the full width of the viewport", async ({ page }) => {
  await gotoSalesPage(page);

  const bands = await page.evaluate(() =>
    [...document.querySelectorAll(".pro-sales-section")].map((band) => {
      const rect = band.getBoundingClientRect();

      return { left: Math.round(rect.left), width: Math.round(rect.width) };
    }),
  );

  expect(bands.length).toBeGreaterThan(0);

  const viewport = page.viewportSize();

  for (const band of bands) {
    expect(band.left).toBe(0);
    expect(band.width).toBe(viewport.width);
  }

  const overflows = await page.evaluate(
    () => document.documentElement.scrollWidth > window.innerWidth,
  );

  expect(overflows).toBe(false);
});

test("sales page bands carry their own vertical rhythm and stay contiguous", async ({ page }) => {
  await gotoSalesPage(page);

  const report = await page.evaluate(() => {
    const widgets = [...document.querySelectorAll(".elementor-widget")];

    const bandOf = (widget) => widget.querySelector(":scope > .pro-sales-section");

    const rhythm = widgets
      .map(bandOf)
      .filter(Boolean)
      .map((band) => {
        const styles = window.getComputedStyle(band);

        return {
          paddingTop: Number.parseFloat(styles.paddingTop) || 0,
          paddingBottom: Number.parseFloat(styles.paddingBottom) || 0,
        };
      });

    // Only bands that are neighbours in the document may be compared: the page
    // also holds widgets that are cards, not bands, and those create real gaps.
    const seams = [];

    for (let index = 1; index < widgets.length; index += 1) {
      const previous = bandOf(widgets[index - 1]);
      const current = bandOf(widgets[index]);

      if (!previous || !current) {
        continue;
      }

      const previousBottom = Math.round(previous.getBoundingClientRect().bottom + window.scrollY);
      const currentTop = Math.round(current.getBoundingClientRect().top + window.scrollY);

      if (previousBottom !== currentTop) {
        seams.push(`${previousBottom} -> ${currentTop}`);
      }
    }

    return { rhythm, seams };
  });

  expect(report.rhythm.length).toBeGreaterThan(0);

  for (const band of report.rhythm) {
    expect(band.paddingTop).toBeGreaterThan(0);
    expect(band.paddingBottom).toBeGreaterThan(0);
  }

  // A gap between neighbouring bands would show the page canvas as a seam.
  expect(report.seams).toEqual([]);
});

test("sales page keeps one h1 and unique element ids", async ({ page }) => {
  await gotoSalesPage(page);

  await expect(page.locator("h1")).toHaveCount(1);

  const report = await page.evaluate(() => {
    const ids = [...document.querySelectorAll("[id]")].map((element) => element.id);

    return {
      duplicated: ids.filter((id, index) => ids.indexOf(id) !== index),
      orphanLabels: [...document.querySelectorAll("[aria-labelledby]")]
        .map((element) => element.getAttribute("aria-labelledby"))
        .filter((id) => !document.getElementById(id)),
    };
  });

  expect(report.duplicated).toEqual([]);
  expect(report.orphanLabels).toEqual([]);
});

test("every component inside a brand band keeps a readable colour pair", async ({ page }) => {
  await gotoSalesPage(page);

  const samples = await page.evaluate(() => {
    const parse = (value) => (value.match(/[\d.]+/g) || []).slice(0, 3).map(Number);
    const surfaceOf = (element) => {
      let node = element;

      while (node) {
        const background = window.getComputedStyle(node).backgroundColor;

        if (background && background !== "rgba(0, 0, 0, 0)") {
          return parse(background);
        }

        node = node.parentElement;
      }

      return [255, 255, 255];
    };

    return [...document.querySelectorAll(".pro-sales-section--tone-brand")].flatMap((band) =>
      [...band.querySelectorAll("h1, h2, h3, p, li, a, summary, strong")]
        .filter((element) => {
          const rect = element.getBoundingClientRect();

          return element.textContent.trim() !== "" && rect.width > 0 && rect.height > 0;
        })
        .map((element) => ({
          label: `${element.tagName}.${(element.className || "-").toString().split(" ")[0]}`,
          color: parse(window.getComputedStyle(element).color),
          surface: surfaceOf(element),
        })),
    );
  });

  expect(samples.length).toBeGreaterThan(0);

  const failures = samples
    .map((sample) => ({ ...sample, ratio: contrastRatio(sample.color, sample.surface) }))
    .filter((sample) => sample.ratio < 4.5)
    .map((sample) => `${sample.label} -> ${sample.ratio.toFixed(2)}`);

  expect(failures).toEqual([]);
});

test("video facade does not contact the provider before the click", async ({ page }) => {
  const providerRequests = [];

  page.on("request", (request) => {
    if (/youtube\.com|youtu\.be|player\.vimeo\.com/.test(request.url())) {
      providerRequests.push(request.url());
    }
  });

  await gotoSalesPage(page);

  const play = page.locator("[data-pro-lp-video-play]").first();

  test.skip((await play.count()) === 0, "A fixture nao tem widget de video com link preenchido.");

  await expect(page.locator("[data-pro-lp-video] iframe")).toHaveCount(0);
  expect(providerRequests).toEqual([]);

  const embedRequest = page.waitForRequest(/youtube\.com\/embed\//, { timeout: 10_000 });

  await play.click();

  await expect(page.locator("[data-pro-lp-video] iframe").first()).toHaveAttribute(
    "src",
    /youtube\.com\/embed\//,
  );
  await embedRequest;
});

test("sales page has no critical accessibility violations", async ({ page }) => {
  await gotoSalesPage(page);

  const results = await new AxeBuilder({ page })
    .withTags(["wcag2a", "wcag2aa", "wcag21a", "wcag21aa"])
    .analyze();

  const blocking = results.violations.filter((violation) =>
    ["critical", "serious"].includes(violation.impact),
  );

  expect(
    blocking.map((violation) => `${violation.id}: ${violation.nodes.length} no(s)`),
  ).toEqual([]);
});
