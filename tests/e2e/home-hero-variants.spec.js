import AxeBuilder from "@axe-core/playwright";
import { expect, test } from "@playwright/test";

const CHECKOUT_HOST = "pay.hotmart.com";

const VARIANTS = [
  {
    name: "oferta",
    path: "/e2e-home-variant-oferta/",
    heroSelector: ".pro-hero-offer",
    ctaSelector: ".pro-hero-offer__cta",
  },
  {
    name: "prova",
    path: "/e2e-home-variant-prova/",
    heroSelector: ".pro-hero-proof",
    ctaSelector: ".pro-hero-proof__cta",
  },
];

for (const variant of VARIANTS) {
  test(`home variant ${variant.name} renders the hero over the shared page body`, async ({
    page,
  }) => {
    await page.goto(variant.path);

    await expect(page.locator(variant.heroSelector)).toBeVisible();
    await expect(page.locator("h1")).toHaveCount(1);

    // The sections below the fold come from the shared partial. Their anchors
    // are the contract the navbar and every secondary CTA depend on.
    await expect(page.locator("#planos")).toHaveCount(1);
    await expect(page.locator("#faq")).toHaveCount(1);
    await expect(page.locator("#aprovados")).toHaveCount(1);

    // The variant renders its own navbar, so the generic theme header and
    // footer must stay out. This is what proenem_is_home_surface() protects.
    await expect(page.locator("header.site-header")).toHaveCount(0);
    await expect(page.locator(".pen-navbar")).toHaveCount(1);
    await expect(page.locator(".pen-site-footer")).toHaveCount(1);
  });

  test(`home variant ${variant.name} points its primary action at the checkout`, async ({
    page,
  }) => {
    await page.goto(variant.path);

    const cta = page.locator(variant.ctaSelector);
    const href = await cta.getAttribute("href");

    expect(new URL(href).host).toBe(CHECKOUT_HOST);
    await expect(cta).toHaveAttribute("data-pro-hero-action", "checkout");
    await expect(cta).toHaveAttribute("data-pro-hero-variant", variant.name);
  });

  test(`home variant ${variant.name} keeps the primary action in the first mobile screen`, async ({
    page,
  }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(variant.path);

    const cta = page.locator(variant.ctaSelector);
    const box = await cta.boundingBox();

    expect(box).not.toBeNull();
    expect(box.y + box.height).toBeLessThanOrEqual(844);

    // The variant overrides the persistent bar label, so it must carry the
    // same destination as the hero action instead of the shared default.
    const persistentAction = page.locator(
      "[data-pro-mobile-persistent-action] a",
    );
    expect(new URL(await persistentAction.getAttribute("href")).host).toBe(
      CHECKOUT_HOST,
    );
  });

  test(`home variant ${variant.name} has no critical accessibility violations`, async ({
    page,
  }) => {
    await page.goto(variant.path);

    const results = await new AxeBuilder({ page })
      .withTags(["wcag2a", "wcag2aa", "wcag21a", "wcag21aa"])
      .analyze();

    const blocking = results.violations.filter((violation) =>
      ["critical", "serious"].includes(violation.impact),
    );

    expect(
      blocking.map(
        (violation) => `${violation.id}: ${violation.nodes.length} no(s)`,
      ),
    ).toEqual([]);
  });
}
