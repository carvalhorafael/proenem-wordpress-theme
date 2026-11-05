import AxeBuilder from "@axe-core/playwright";
import { expect, test } from "@playwright/test";

const PATH = "/?pagename=e2e-home-promoblack";
const CHECKOUT_HOST = "pay.hotmart.com";

test("home PROMOBLACK renders as a home surface with one h1", async ({ page }) => {
  await page.goto(PATH);

  await expect(page.locator(".pro-pb-hero")).toBeVisible();
  await expect(page.locator("h1")).toHaveCount(1);
  await expect(page.locator("header.site-header")).toHaveCount(0);
  await expect(page.locator("#planos")).toHaveCount(1);
  await expect(page.locator("#faq")).toHaveCount(1);
});

test("home PROMOBLACK sends both plan cards to the Hotmart checkout", async ({ page }) => {
  await page.goto(PATH);

  for (const plan of ["enem-pro", "med"]) {
    const cta = page.locator(`[data-pro-promoblack-action="checkout-${plan}"]`);

    await expect(cta).toHaveCount(1);
    expect(new URL(await cta.getAttribute("href")).host).toBe(CHECKOUT_HOST);
  }
});

test("home PROMOBLACK countdown ticks every second", async ({ page }) => {
  await page.goto(PATH);

  const countdown = page.locator(".pro-pb-lots [data-pro-countdown]");
  const seconds = countdown.locator("[data-pro-countdown-seconds]");

  await expect(countdown.locator("[data-pro-countdown-units]")).toBeVisible();
  await expect(countdown.locator("[data-pro-countdown-fallback]")).toBeHidden();

  const first = await seconds.textContent();

  await expect(seconds).not.toHaveText(first, { timeout: 2500 });
  await expect(countdown.locator("[data-pro-countdown-days]")).toHaveText(/^\d{2}$/);
});

test("home PROMOBLACK shows the Login button next to the wordmark", async ({ page }) => {
  for (const viewport of [{ width: 1440, height: 900 }, { width: 390, height: 844 }]) {
    await page.setViewportSize(viewport);
    await page.goto(PATH);

    const login = page.locator('[data-pro-promoblack-action="login"]');

    await expect(login).toBeVisible();
    await expect(login).toHaveText("Login");
    expect(new URL(await login.getAttribute("href")).host).toBe("app.proenem.com.br");
  }
});

test("home PROMOBLACK links the SISU asterisk to the rules", async ({ page }) => {
  await page.goto(PATH);

  await expect(page.locator('.pro-pb-sisu a[href="#pro-pb-sisu-note"]')).toHaveCount(1);
  await expect(page.locator("#pro-pb-sisu-note a")).toHaveAttribute(
    "href",
    "https://proenem.com.br/termos-de-uso/",
  );
});

test("home PROMOBLACK fits the mobile viewport", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto(PATH);

  const overflow = await page.evaluate(
    () => document.documentElement.scrollWidth - window.innerWidth,
  );

  expect(overflow).toBe(0);
  await expect(page.locator(".pro-pb-button--hero")).toBeInViewport();
});

test("home PROMOBLACK has no serious accessibility violations", async ({ page }) => {
  await page.goto(PATH);

  const results = await new AxeBuilder({ page })
    .withTags(["wcag2a", "wcag2aa", "wcag21a", "wcag21aa"])
    .analyze();

  const blocking = results.violations.filter((violation) =>
    ["critical", "serious"].includes(violation.impact),
  );

  expect(blocking.map((violation) => `${violation.id}: ${violation.nodes.map((node) => node.target.join(" ")).join(", ")}`)).toEqual([]);
});
