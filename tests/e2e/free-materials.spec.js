import { readFileSync } from "node:fs";
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
  const status = response ? response.status() : 0;

  // Only a 404 means the plugin is not mounted. Skipping on any other status
  // would turn a slow start or a 5xx into a silently missing test.
  test.skip(status === 404, "O catalogo de materiais gratuitos nao esta disponivel neste ambiente.");

  expect(status, `${url} respondeu ${status}`).toBe(200);

  return true;
};

test("catalog lists every material and reports the count", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const cards = page.locator("[data-pro-material-card]");

  await expect(cards).toHaveCount(3);
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("3 materiais disponíveis");
  await expect(page.locator("[data-pro-materials-count]")).toHaveAttribute("aria-live", "polite");
});

test("catalog hero has its own look, not the approved students one", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await gotoMaterials(page, CATALOG);

  const hero = page.locator(".pro-materials-hero--catalog");

  // Light ground with an ink headline, not the solid red band.
  const ground = await hero.evaluate((el) => getComputedStyle(el).backgroundColor);
  expect(ground).toBe("rgb(255, 255, 255)");

  // The photo stage belongs to the approved students page.
  await expect(page.locator(".pro-materials-hero__stage")).toHaveCount(0);

  // The categories are inside the hero, and they carry the colour.
  const tones = await page
    .locator(".pro-materials-hero .pen-blog-category-tabs__item[data-tone]")
    .evaluateAll((els) => els.map((el) => getComputedStyle(el).backgroundColor));

  expect(tones.length).toBeGreaterThan(1);
  expect(new Set(tones).size).toBeGreaterThan(1);
});

test("the category row scrolls from its first chip on mobile", async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 812 });
  await gotoMaterials(page, CATALOG);

  const row = page.locator(".pro-materials-tabs");

  const geometry = await row.evaluate((el) => ({
    clientWidth: el.clientWidth,
    scrollWidth: el.scrollWidth,
    firstChipLeft: Math.round(el.firstElementChild.getBoundingClientRect().left),
  }));

  // Centring a row that overflows pushes its first chip out of reach.
  expect(geometry.firstChipLeft).toBeGreaterThanOrEqual(0);
  expect(geometry.scrollWidth).toBeGreaterThan(geometry.clientWidth);

  const overflows = await page.evaluate(
    () => document.documentElement.scrollWidth > window.innerWidth,
  );
  expect(overflows).toBe(false);

  // And the first material still lands inside the first screen.
  const top = await page.evaluate(() => {
    const el = document.querySelector(".pro-materials-featured, .pro-material-card");

    return Math.round(el.getBoundingClientRect().top + window.scrollY);
  });
  expect(top).toBeLessThan(700);
});

test("Todos lands on the list, not back at the top of the hero", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await gotoMaterials(page, CATALOG);

  await page.locator(".pro-materials-tabs").getByRole("link", { name: "Todos" }).click();

  await expect(page).toHaveURL(/#materiais$/);

  const heading = page.locator("#pro-materials-results-title");

  await expect(heading).toBeInViewport();

  // The sticky header must not cover it.
  const top = await heading.evaluate((el) => Math.round(el.getBoundingClientRect().top));
  const header = await page
    .locator(".site-header")
    .evaluate((el) => Math.round(el.getBoundingClientRect().height));

  expect(top).toBeGreaterThanOrEqual(header);
});

test("ticking a category updates the cards without reloading", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const panel = page.locator(".pro-materials-filter--panel");
  const cards = page.locator("[data-pro-material-card]");
  const count = page.locator("[data-pro-materials-count]");

  await expect(panel).toHaveCount(1);
  await expect(cards).toHaveCount(3);

  // With JavaScript the tick applies straight away, so the button goes.
  await expect(panel.getByRole("button", { name: "Ver materiais" })).toHaveCount(0);

  await page.evaluate(() => {
    window.__navegacoes = performance.getEntriesByType("navigation").length;
  });

  await panel.getByRole("checkbox", { name: /Redação/ }).check();

  await expect(cards).toHaveCount(1);
  await expect(count).toHaveText("1 material disponível");
  await expect(page).toHaveURL(/material_categoria/);

  // Combining adds to the selection instead of replacing it.
  await panel.getByRole("checkbox", { name: /Simulados/ }).check();

  await expect(cards).toHaveCount(2);
  await expect(count).toHaveText("2 materiais disponíveis");
  await expect(panel.getByRole("link", { name: "Limpar filtros" })).toBeVisible();

  // Unticking everything brings the whole catalog back.
  await panel.getByRole("checkbox", { name: /Redação/ }).uncheck();
  await panel.getByRole("checkbox", { name: /Simulados/ }).uncheck();

  await expect(cards).toHaveCount(3);
  await expect(panel.getByRole("link", { name: "Limpar filtros" })).toHaveCount(0);

  // None of it cost a page load.
  const navegou = await page.evaluate(
    () => performance.getEntriesByType("navigation").length !== window.__navegacoes,
  );
  expect(navegou).toBe(false);
});

test("the filter still works without JavaScript", async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();

  const response = await page.goto(CATALOG);
  const status = response ? response.status() : 0;

  if (status === 404) {
    await context.close();
  }

  test.skip(status === 404, "O catalogo de materiais gratuitos nao esta disponivel neste ambiente.");
  expect(status).toBe(200);

  const panel = page.locator(".pro-materials-filter--panel");

  // Without JavaScript the button is the way to apply it, so it stays.
  await panel.getByRole("checkbox", { name: /Redação/ }).check();
  await panel.getByRole("button", { name: "Ver materiais" }).click();

  await expect(page.locator("[data-pro-material-card]")).toHaveCount(1);
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("1 material disponível");

  await context.close();
});

test("combining categories keeps the chosen order", async ({ page }) => {
  await gotoMaterials(page, `${CATALOG}?ordenar=az`);

  await page.locator(".pro-materials-filter--panel").getByRole("checkbox", { name: /Redação/ }).check();

  await expect(page).toHaveURL(/ordenar=az/);
  await expect(page).toHaveURL(/material_categoria/);
});

test("category tabs link to the real category archives", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const tabs = page.locator(".pro-materials-tabs .pen-blog-category-tabs__item");

  // "Todos" plus one tab per category that has material.
  expect(await tabs.count()).toBeGreaterThan(1);
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
  const status = response ? response.status() : 0;

  if (status === 404) {
    await context.close();
  }

  test.skip(status === 404, "O catalogo de materiais gratuitos nao esta disponivel neste ambiente.");
  expect(status).toBe(200);

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
    const box = (el) => ({
      height: Math.round(el.getBoundingClientRect().height),
      top: Math.round(el.getBoundingClientRect().top + window.scrollY),
    });

    // The first material is the highlight when there is one, otherwise the
    // first card.
    const first = document.querySelector(".pro-materials-featured, .pro-material-card");

    return { hero: box(document.querySelector(".pro-materials-hero")), material: box(first) };
  });

  // The hero now carries the category chips too, so its own height is not the
  // measure any more. What matters is where the first material lands: it used
  // to be y=1030, with a 492px hero and the chips below it.
  expect(geometry.hero.height).toBeLessThan(420);
  expect(geometry.material.top).toBeLessThan(700);
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

test("featured material is promoted without leaving the catalog", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const band = page.locator(".pro-materials-featured");

  await expect(band).toHaveCount(1);
  await expect(band.locator("h3")).toHaveText("Mapa de análise de simulados");
  await expect(band.locator(".pro-materials-featured__highlights li")).toHaveCount(3);

  // Promoting a material must not remove it from the list, or the heading and
  // the count would lie.
  await expect(page.locator("[data-pro-material-card]")).toHaveCount(3);
  await expect(page.locator("[data-pro-materials-count]")).toHaveText("3 materiais disponíveis");
  await expect(page.locator("[data-pro-material-featured]")).toHaveCount(1);
});

test("category archive does not promote a material from another category", async ({ page }) => {
  await gotoMaterials(page, CATEGORY);

  await expect(page.locator(".pro-materials-featured")).toHaveCount(0);
  await expect(page.locator("[data-pro-material-featured]")).toHaveCount(0);
});

test("catalog offers an order that survives without JavaScript", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  const titles = () =>
    page.locator("[data-pro-material-card] h3").allInnerTexts();

  const byDate = await titles();

  await page.locator("[data-pro-materials-order] select").selectOption("az");
  await expect(page).toHaveURL(/ordenar=az/);

  const alphabetical = await titles();

  expect(alphabetical).not.toEqual(byDate);
  expect(alphabetical).toEqual([...alphabetical].sort((a, b) => a.localeCompare(b, "pt-BR")));
});

test("the order control works without JavaScript", async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();

  const response = await page.goto(CATALOG);
  const status = response ? response.status() : 0;

  if (status === 404) {
    await context.close();
  }

  test.skip(status === 404, "O catalogo de materiais gratuitos nao esta disponivel neste ambiente.");
  expect(status).toBe(200);

  // With JavaScript the button is removed and the select submits on change.
  await page.locator("[data-pro-materials-order] select").selectOption("az");
  await page.locator('[data-pro-materials-order] [type="submit"]').click();

  await expect(page).toHaveURL(/ordenar=az/);

  const titles = await page.locator("[data-pro-material-card] h3").allInnerTexts();
  expect(titles).toEqual([...titles].sort((a, b) => a.localeCompare(b, "pt-BR")));

  await context.close();
});

test("catalog stops loading every material at once", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  // posts_per_page was -1, so the page loaded the whole catalog and every
  // cover with it.
  const perPage = await page.evaluate(() => {
    return document.querySelectorAll("[data-pro-material-card]").length;
  });

  expect(perPage).toBeLessThanOrEqual(18);

  // A page out of range must not 404 or blow up.
  const response = await page.goto(`${CATALOG}?pagina=99`);
  expect(response.status()).toBe(200);
  await expect(page.locator(".pro-materials-empty")).toHaveCount(1);
});

test("material card headings sit below the results heading", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  await expect(page.locator(".pro-material-card h2")).toHaveCount(0);
  await expect(page.locator(".pro-material-card h3")).toHaveCount(3);
});

test("capture form blocks an empty submit and explains each field", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const form = page.locator(".pro-material-single__hero [data-pro-material-capture-form]");

  await form.getByRole("button", { name: "Baixar material gratuito" }).click();

  await expect(page).toHaveURL(new RegExp(`${MATERIAL}$`));
  await expect(page.locator("#pro-material-capture-hero-name-error")).toBeVisible();
  await expect(page.locator("#pro-material-capture-hero-email-error")).toBeVisible();
  await expect(page.locator("#pro-material-capture-hero-name")).toHaveAttribute("aria-invalid", "true");
  await expect(page.locator("#pro-material-capture-hero-name")).toBeFocused();

  // WhatsApp is optional, so it must not be flagged.
  await expect(page.locator("#pro-material-capture-hero-whatsapp-error")).toBeHidden();
});

test("material hero keeps the submit button inside a laptop screen", async ({ page }) => {
  await page.setViewportSize({ width: 1366, height: 768 });
  await gotoMaterials(page, MATERIAL);

  const button = page.locator(".pro-material-capture--hero .pro-material-capture__button");
  const box = await button.boundingBox();

  // The hero used to be 887px tall and pushed the button to y=818.
  expect(box.y + box.height).toBeLessThanOrEqual(768);
});

test("material hero states what is inside instead of repeating the content", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const hero = page.locator(".pro-material-single__hero");

  await expect(hero.locator(".pro-material-single__highlights li").first()).toBeVisible();
  await expect(hero.locator(".pro-material-single__specs")).toContainText("PDF");

  // The hero paragraph used to be a truncated copy of the body text.
  const heroText = await hero.locator(".pro-material-single__hero-copy p").allInnerTexts();
  for (const text of heroText) {
    expect(text).not.toContain("…");
  }
});

test("mobile action bar appears only while the form is off screen", async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 812 });
  await gotoMaterials(page, MATERIAL);

  const bar = page.locator("[data-pro-material-sticky-cta]");

  // The form is in the first screen, so the bar must stay out of the way.
  await expect(bar).not.toHaveClass(/is-visible/);

  // Scroll into the stretch between the two forms, where neither is on screen.
  const gap = await page.evaluate(() => {
    const panels = [...document.querySelectorAll("[data-pro-material-capture]")];
    const rects = panels.map((p) => p.getBoundingClientRect());

    return Math.round(rects[0].bottom + window.scrollY + 40);
  });

  await page.evaluate((y) => window.scrollTo(0, y), gap);
  await expect(bar).toHaveClass(/is-visible/);
  await expect(bar).toBeInViewport();

  // It steps aside again once the closing form arrives.
  await page.locator(".pro-material-single__closing").scrollIntoViewIfNeeded();
  await expect(bar).not.toHaveClass(/is-visible/);

  await page.evaluate((y) => window.scrollTo(0, y), gap);
  await expect(bar).toHaveClass(/is-visible/);

  await bar.getByRole("link").click();

  // It scrolls to a form and hands over the first field.
  await expect(page.locator("input:focus")).toHaveAttribute("name", "name");
  await expect(bar).not.toHaveClass(/is-visible/);
});

test("the action bar stays out of the desktop layout", async ({ page }) => {
  await page.setViewportSize({ width: 1280, height: 900 });
  await gotoMaterials(page, MATERIAL);

  await page.evaluate(() => window.scrollTo(0, 2500));

  await expect(page.locator("[data-pro-material-sticky-cta]")).toBeHidden();
});

test("the page ends with a form instead of a link back up", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  await expect(page.locator("[data-pro-material-capture-form]")).toHaveCount(2);

  // The yellow box and the footer banner both pointed at the form above them,
  // with the arrow drawn as up.
  await expect(page.locator(".pro-material-download")).toHaveCount(0);
  await expect(page.locator(".pro-material-footer-cta")).toHaveCount(0);

  const closing = page.locator(".pro-material-single__closing");
  await expect(closing.locator("[data-pro-material-capture-form]")).toHaveCount(1);
});

test("the two forms validate independently", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  await page
    .locator(".pro-material-single__closing")
    .getByRole("button", { name: /baixar/i })
    .click();

  await expect(page.locator("#pro-material-capture-footer-name-error")).toBeVisible();
  await expect(page.locator("#pro-material-capture-footer-name")).toBeFocused();

  // The form in the hero must be untouched.
  await expect(page.locator("#pro-material-capture-hero-name-error")).toBeHidden();
  await expect(page.locator("#pro-material-capture-hero-name")).not.toHaveAttribute("aria-invalid", "true");
});

test("every id on the material page is unique", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  // Two capture panels means every id has to carry its instance, or the label
  // and aria wiring silently points at the wrong field.
  const duplicates = await page.evaluate(() => {
    const seen = new Map();

    document.querySelectorAll("[id]").forEach((el) => {
      seen.set(el.id, (seen.get(el.id) || 0) + 1);
    });

    return [...seen].filter(([, count]) => count > 1).map(([id]) => id);
  });

  expect(duplicates).toEqual([]);
});

test("both forms explain what happens to the data", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const notices = page.locator(".pro-material-capture__privacy");

  await expect(notices).toHaveCount(2);

  for (let i = 0; i < 2; i += 1) {
    await expect(notices.nth(i)).toContainText("dados");
    await expect(notices.nth(i).getByRole("link")).toHaveAttribute("href", /privacy|privacidade/);
  }
});

test("the capture page reduces the header and shows where you are", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  // Seven ways out of a page whose only job is the form.
  await expect(page.locator(".site-header .pro-site-navbar--logo-only")).toHaveCount(1);
  await expect(page.locator(".site-header .pro-home-navbar-toggle")).toHaveCount(0);
  await expect(page.locator(".site-header .pro-home-navbar-menu")).toHaveCount(0);

  // Brand plus a single action.
  await expect(page.locator(".site-header").getByRole("link")).toHaveCount(2);

  const crumbs = page.locator(".pro-material-breadcrumb li");

  // The logo is already the way home and the h1 already names the material,
  // so the trail is only the catalog and the category.
  await expect(crumbs).toHaveCount(2);
  await expect(crumbs.nth(0)).toHaveText("Materiais gratuitos");
  await expect(crumbs.nth(1)).toHaveText("Simulados");
  await expect(page.locator(".pro-material-breadcrumb [aria-current]")).toHaveCount(0);

  await crumbs.nth(0).getByRole("link").click();
  await expect(page).toHaveURL(new RegExp(`${CATALOG}$`));
});

test("the catalog keeps the full navigation", async ({ page }) => {
  await gotoMaterials(page, CATALOG);

  await expect(page.locator(".site-header .pro-site-navbar--logo-only")).toHaveCount(0);

  // The menu and its mobile toggle only exist in the full navbar. Asserting on
  // the links themselves would depend on a menu being assigned.
  await expect(page.locator(".site-header .pro-home-navbar-toggle")).toHaveCount(1);
  await expect(page.locator(".site-header .pro-home-navbar-menu")).toHaveCount(1);
});

test("the material hero keeps its horizontal padding", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  // An undefined token inside clamp() made the whole padding shorthand invalid
  // and CSS dropped it without a word.
  const padding = await page
    .locator(".pro-material-single__hero")
    .evaluate((el) => Number.parseFloat(getComputedStyle(el).paddingLeft));

  expect(padding).toBeGreaterThan(16);
});

test("the material hero fills its column and centres the preview", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await gotoMaterials(page, MATERIAL);

  const box = (selector) => page.locator(selector).evaluate((el) => el.getBoundingClientRect().toJSON());

  const copy = await box(".pro-material-single__hero-copy");
  const title = await box(".pro-material-single__hero h1");

  // The title used to stop at 20ch, leaving a third of its own column empty.
  expect(title.width).toBeCloseTo(copy.width, 0);

  const preview = await box(".pro-material-single__preview");
  const cover = await box(".pro-material-single__cover");
  const inside = await box(".pro-material-single__inside");

  // The cover is rotated, so its client rect is wider than its layout box on
  // both sides. Measuring the gaps against each other absorbs that.
  const left = cover.left - preview.left;
  const right = preview.right - inside.right;

  expect(Math.abs(left - right)).toBeLessThan(12);

  // And the two sit on the same optical line rather than both hugging the top.
  expect(Math.abs((cover.top + cover.height / 2) - (inside.top + inside.height / 2))).toBeLessThan(12);

  // The cover carries the product. It was the smallest thing in the hero.
  expect(cover.width).toBeGreaterThan(260);
});

test("the material page offers other materials instead of only the exit", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const related = page.locator(".pro-material-related");

  await expect(related).toHaveCount(1);

  const cards = related.locator("[data-pro-material-card]");

  expect(await cards.count()).toBeGreaterThan(0);

  // The material being read must not be offered back to the reader.
  for (const href of await cards.locator("h3 a").evaluateAll((els) => els.map((el) => el.href))) {
    expect(href).not.toContain("mapa-de-analise-de-simulados");
  }

  await related.getByRole("link", { name: /ver todos/i }).click();
  await expect(page).toHaveURL(new RegExp(`${CATALOG}$`));
});

test("the page answers the doubts that come before a download", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const faq = page.locator(".pro-material-faq .pen-faq-item");

  await expect(faq).toHaveCount(4);
  await expect(faq.first()).toHaveAttribute("open", "");
  await expect(faq.first().locator("summary")).toContainText("gratuito");

  // A closed question opens on click, with no JavaScript of ours involved.
  await expect(faq.nth(1)).not.toHaveAttribute("open", "");
  await faq.nth(1).locator("summary").click();
  await expect(faq.nth(1)).toHaveAttribute("open", "");
});

test("social proof shows only where there is data", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  // The fixture carries a placeholder count.
  await expect(page.locator(".pro-material-proof__count")).toHaveCount(1);

  // The number leads as a badge with its label underneath, not as a line of
  // text with a caption beside it.
  const number = await page.locator(".pro-material-proof__count strong").evaluate((el) => ({
    ...el.getBoundingClientRect().toJSON(),
    background: getComputedStyle(el).backgroundColor,
  }));
  const label = await page.locator(".pro-material-proof__count span").evaluate((el) => el.getBoundingClientRect().toJSON());

  expect(label.top).toBeGreaterThanOrEqual(number.bottom);
  expect(number.background).not.toBe("rgba(0, 0, 0, 0)");

  // Both centred on the same axis.
  expect(Math.abs((number.left + number.width / 2) - (label.left + label.width / 2))).toBeLessThan(4);

  // A material without the field must not render an empty proof block.
  await gotoMaterials(page, "/materiais-gratuitos/checklist-de-revisao-para-o-enem/");

  await expect(page.locator(".pro-material-proof__count")).toHaveCount(0);
  await expect(page.locator(".pro-material-faq .pen-faq-item")).toHaveCount(4);
});

test("proof and questions ride beside the content on desktop", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await gotoMaterials(page, MATERIAL);

  const box = (selector) => page.locator(selector).evaluate((el) => el.getBoundingClientRect().toJSON());

  const content = await box(".pro-material-single__content");
  const proof = await box(".pro-material-proof");
  const faq = await box(".pro-material-faq");

  // They used to be a full width band a screen below the content.
  expect(proof.left).toBeGreaterThanOrEqual(content.right);
  expect(faq.left).toBeGreaterThanOrEqual(content.right);

  // Stacked, proof first.
  expect(faq.top).toBeGreaterThanOrEqual(proof.bottom);

  // And the column is narrow, so both have to be sized for it.
  expect(proof.width).toBeLessThan(420);

  // Below the breakpoint they stack under the content at full width.
  await page.setViewportSize({ width: 375, height: 812 });

  const narrowContent = await box(".pro-material-single__content");
  const narrowFaq = await box(".pro-material-faq");

  expect(narrowFaq.top).toBeGreaterThan(narrowContent.bottom);
  expect(Math.abs(narrowFaq.width - narrowContent.width)).toBeLessThan(8);
});

test("sharing a material leads with WhatsApp", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const share = page.locator(".pro-material-share");

  await expect(share).toHaveCount(1);

  const links = share.locator("a");

  await expect(links).toHaveCount(3);
  await expect(links.first()).toHaveAttribute("href", /wa\.me/);

  for (let i = 0; i < 3; i += 1) {
    await expect(links.nth(i)).toHaveAttribute("target", "_blank");
    await expect(links.nth(i)).toHaveAttribute("rel", "noopener noreferrer");
  }
});

test("capture form masks the WhatsApp number", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const phone = page.locator("#pro-material-capture-hero-whatsapp");

  await phone.fill("");
  await phone.pressSequentially("11987654321");

  await expect(phone).toHaveValue("(11) 98765-4321");
});

test("capture feedback carries the Proenem identity, not Executive Signal", async ({ page }) => {
  await gotoMaterials(page, MATERIAL);

  const message = page.locator(".pro-material-capture--hero [data-crm-leads-capture-message]");

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

test("form patterns compile in the strict regex mode browsers use", () => {
  // Browsers compile the pattern attribute in `v` mode, which rejects
  // character classes that older modes accept. Playwright's Chromium still
  // accepts them, so this has to be checked in Node.
  const template = readFileSync("template-parts/materials/capture.php", "utf8");
  const patterns = [...template.matchAll(/pattern="([^"]+)"/g)].map((match) => match[1]);

  for (const pattern of patterns) {
    expect(() => new RegExp(pattern, "v"), `pattern ${pattern}`).not.toThrow();
  }
});
