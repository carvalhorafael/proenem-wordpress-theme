import AxeBuilder from "@axe-core/playwright";
import { expect, test } from "@playwright/test";

const CATALOG = "/materiais-gratuitos/";
const CATEGORY = "/materiais-gratuitos/categoria/redacao/";
const MATERIAL = "/materiais-gratuitos/mapa-de-analise-de-simulados/";

/**
 * The catalog fixture depends on the free-materials plugin, which the base
 * wp-env config does not mount. Skip instead of failing when it is absent.
 */
const gotoMaterials = async (page, url) => {
  const response = await page.goto(url);
  const available = Boolean(response) && response.status() === 200;

  test.skip(!available, "O catalogo de materiais gratuitos nao esta disponivel neste ambiente.");

  return available;
};

test("catalog lists every material and reports the count", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const cards = page.locator("[data-pro-material-card]");

  await expect(cards).toHaveCount(3);
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("3 materiais disponíveis");
  await expect(page.locator("[data-pro-materials-count]")).toHaveAttribute("aria-live", "polite");
});

test("category tabs link to the real category archives", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const tabs = page.locator(".pro-materials-tabs .pen-blog-category-tabs__item");

  // "Todos" plus one tab per category that has material.
  await expect(tabs).toHaveCount(4);
  await expect(tabs.first()).toHaveText(/Todos/);
  await expect(tabs.first()).toHaveClass(/is-active/);

  await tabs.filter({ hasText: "Redação" }).click();

  await expect(page).toHaveURL(new RegExp(`${CATEGORY}$`));

  const cards = page.locator("[data-pro-material-card]");

  await expect(cards).toHaveCount(1);
  await expect(cards.first()).toContainText("Modelo de rotina de redação");
  // Singular, not "1 materiais".
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("1 material disponível");
  await expect(
    page.locator(".pro-materials-tabs .pen-blog-category-tabs__item.is-active"),
  ).toHaveText(/Redação/);
});

test("category tabs scroll sideways on mobile instead of stacking", async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 812 });
  await gotoMaterials(page, CATALOG);

  const tabs = page.locator(".pro-materials-tabs");

  // Wrapping the tabs used to stack several rows and push the first material
  // off the screen.
  const height = await tabs.evaluate((el) => el.getBoundingClientRect().height);
  expect(height).toBeLessThan(80);

  // The page itself must not scroll sideways.
  const overflows = await page.evaluate(
    () => document.documentElement.scrollWidth > window.innerWidth,
  );
  expect(overflows).toBe(false);
});

test("legacy query argument still filters on the server, without JavaScript", async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();

  const response = await page.goto(`${CATALOG}?material_categoria%5B%5D=redacao`);
  const available = Boolean(response) && response.status() === 200;

  if (!available) {
    await context.close();
  }

  test.skip(!available, "O catalogo de materiais gratuitos nao esta disponivel neste ambiente.");

  await expect(page.locator("[data-pro-material-card]")).toHaveCount(1);
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("1 material disponível");

  await context.close();
});

test("category URL renders the catalog, not the blog index", async ({ page }) => {
  await gotoMaterials(page, CATEGORY);

  await expect(page.locator(".pro-materials-page")).toHaveCount(1);
  await expect(page.locator(".pro-blog-index-page")).toHaveCount(0);
  await expect(page.locator(".pen-post-card")).toHaveCount(0);

  // The English plugin label used to leak through get_the_archive_title().
  await expect(page.locator("h1")).toHaveText("Redação");
  await expect(page.getByText("Material category:")).toHaveCount(0);

  await expect(page.locator("[data-pro-material-card]")).toHaveCount(1);
});

test("catalog hero leaves the first material inside the first mobile screen", async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 812 });
  await gotoMaterials(page, CATALOG);

  const geometry = await page.evaluate(() => {
    const box = (selector) => {
      const el = document.querySelector(selector);
      const rect = el.getBoundingClientRect();

      return { height: Math.round(rect.height), top: Math.round(rect.top + window.scrollY) };
    };

    return { hero: box(".pro-materials-hero"), card: box(".pro-material-card") };
  });

  // The hero used to take 492px and pushed the first card to y=1030.
  expect(geometry.hero.height).toBeLessThan(320);
  expect(geometry.card.top).toBeLessThan(700);
});

test("material card states what the visitor gets", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const cards = page.locator("[data-pro-material-card]");

  // Every card promises a download instead of "acessar material".
  for (const text of await cards.locator(".pro-material-card__action").allInnerTexts()) {
    expect(text.toLowerCase()).toContain("baixar");
  }

  // Cards keep an even height whether or not the metadata is filled in.
  const heights = await cards.evaluateAll((els) =>
    els.map((el) => Math.round(el.getBoundingClientRect().height)),
  );
  expect(new Set(heights).size).toBe(1);
});

test("material card headings sit below the results heading", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  await expect(page.locator(".pro-material-card h2")).toHaveCount(0);
  await expect(page.locator(".pro-material-card h3")).toHaveCount(3);
});

test("capture form blocks an empty submit and explains each field", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const form = page.locator("[data-pro-material-capture-form]");

  await form.getByRole("button", { name: "Baixar material gratuito" }).click();

  await expect(page).toHaveURL(new RegExp(`${MATERIAL}$`));
  await expect(page.locator("#pro-material-capture-name-error")).toBeVisible();
  await expect(page.locator("#pro-material-capture-email-error")).toBeVisible();
  await expect(page.locator("#pro-material-capture-name")).toHaveAttribute("aria-invalid", "true");
  await expect(page.locator("#pro-material-capture-name")).toBeFocused();

  // WhatsApp is optional, so it must not be flagged.
  await expect(page.locator("#pro-material-capture-whatsapp-error")).toBeHidden();
});

test("capture form masks the WhatsApp number", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const phone = page.locator("#pro-material-capture-whatsapp");

  await phone.fill("");
  await phone.pressSequentially("11987654321");

  await expect(phone).toHaveValue("(11) 98765-4321");
});

test("capture feedback carries the Proenem identity, not Executive Signal", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const message = page.locator("[data-crm-leads-capture-message]");

  await expect(message).toHaveCount(1);
  await expect(page.locator(".es-panel, .es-badge, .es-operational-feedback")).toHaveCount(0);
  await expect(message.locator(".crm-leads-capture-message__text")).toHaveCount(1);
});

test("free materials surfaces have no critical accessibility violations", async ({ page }) => {
  for (const url of [CATALOG, CATEGORY, MATERIAL]) {
    await gotoMaterials(page, url);

    const results = await new AxeBuilder({ page })
      .withTags(["wcag2a", "wcag2aa"])
      .analyze();

    const critical = results.violations.filter((violation) =>
      ["critical", "serious"].includes(violation.impact),
    );

    expect(critical, `${url}: ${critical.map((v) => v.id).join(", ")}`).toEqual([]);
  }
});
