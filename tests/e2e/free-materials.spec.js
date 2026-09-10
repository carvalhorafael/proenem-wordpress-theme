import AxeBuilder from "@axe-core/playwright";
import { expect, test } from "@playwright/test";

const CATALOG = "/materiais-gratuitos/";
const CATEGORY = "/materiais-gratuitos/categoria/redacao/";
const MATERIAL = "/materiais-gratuitos/mapa-de-analise-de-simulados/";

test("catalog lists every material and reports the count", async ({ page }) => {
  await page.goto(CATALOG);

  const cards = page.locator("[data-pro-material-card]");

  await expect(cards).toHaveCount(3);
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("3 materiais disponíveis");
  await expect(page.locator("[data-pro-materials-count]")).toHaveAttribute("aria-live", "polite");
});

test("catalog filters on the server, so the count and the cards agree", async ({ page }) => {
  await page.goto(CATALOG);

  await page.getByRole("checkbox", { name: "Redação" }).check();
  await page.getByRole("button", { name: "Filtrar materiais" }).click();

  await expect(page).toHaveURL(/material_categoria/);

  const cards = page.locator("[data-pro-material-card]");

  await expect(cards).toHaveCount(1);
  await expect(cards.first()).toContainText("Modelo de rotina de redação");
  // Singular, not "1 materiais".
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("1 material disponível");
});

test("catalog filter survives with JavaScript disabled", async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();

  await page.goto(`${CATALOG}?material_categoria%5B%5D=redacao`);

  await expect(page.locator("[data-pro-material-card]")).toHaveCount(1);
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("1 material disponível");

  await context.close();
});

test("category URL renders the catalog, not the blog index", async ({ page }) => {
  await page.goto(CATEGORY);

  await expect(page.locator(".pro-materials-page")).toHaveCount(1);
  await expect(page.locator(".pro-blog-index-page")).toHaveCount(0);
  await expect(page.locator(".pen-post-card")).toHaveCount(0);

  // The English plugin label used to leak through get_the_archive_title().
  await expect(page.locator("h1")).toHaveText("Redação");
  await expect(page.getByText("Material category:")).toHaveCount(0);

  await expect(page.locator("[data-pro-material-card]")).toHaveCount(1);
});

test("material card headings sit below the results heading", async ({ page }) => {
  await page.goto(CATALOG);

  await expect(page.locator(".pro-material-card h2")).toHaveCount(0);
  await expect(page.locator(".pro-material-card h3")).toHaveCount(3);
});

test("capture form blocks an empty submit and explains each field", async ({ page }) => {
  await page.goto(MATERIAL);

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
  await page.goto(MATERIAL);

  const phone = page.locator("#pro-material-capture-whatsapp");

  await phone.fill("");
  await phone.pressSequentially("11987654321");

  await expect(phone).toHaveValue("(11) 98765-4321");
});

test("capture feedback carries the Proenem identity, not Executive Signal", async ({ page }) => {
  await page.goto(MATERIAL);

  const message = page.locator("[data-crm-leads-capture-message]");

  await expect(message).toHaveCount(1);
  await expect(page.locator(".es-panel, .es-badge, .es-operational-feedback")).toHaveCount(0);
  await expect(message.locator(".crm-leads-capture-message__text")).toHaveCount(1);
});

test("free materials surfaces have no critical accessibility violations", async ({ page }) => {
  for (const url of [CATALOG, CATEGORY, MATERIAL]) {
    await page.goto(url);

    const results = await new AxeBuilder({ page })
      .withTags(["wcag2a", "wcag2aa"])
      .analyze();

    const critical = results.violations.filter((violation) =>
      ["critical", "serious"].includes(violation.impact),
    );

    expect(critical, `${url}: ${critical.map((v) => v.id).join(", ")}`).toEqual([]);
  }
});
