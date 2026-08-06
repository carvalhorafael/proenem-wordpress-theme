import { expect, test } from "@playwright/test";

const installNavbarSubmenuFixture = async (page) => {
  await page.route(
    "**/",
    async (route) => {
      const response = await route.fetch();
      const body = await response.text();
      const menuLinks = '<div class="pen-navbar__links">';
      const submenuFixture = `
      <div class="pen-navbar__item pen-navbar__item--has-submenu">
        <a class="pen-navbar__link" href="#e2e-submenu" aria-haspopup="true">
          <span class="pen-navbar__label" data-label="Teste principal">
            <span class="pen-navbar__label-text">Teste principal</span>
          </span>
        </a>
        <button class="pro-home-navbar-submenu-toggle" type="button" aria-controls="e2e-navbar-submenu" aria-expanded="false">
          <span class="screen-reader-text">Alternar submenu de teste</span>
          <span aria-hidden="true">⌄</span>
        </button>
        <ul id="e2e-navbar-submenu" class="pen-navbar__submenu">
          <li class="pen-navbar__submenu-item">
            <a class="pen-navbar__submenu-link" href="#e2e-submenu-item">Item de teste</a>
          </li>
        </ul>
      </div>
      <div class="pen-navbar__item">
        <a class="pen-navbar__link" href="#e2e-next-item">
          <span class="pen-navbar__label" data-label="Próximo item">
            <span class="pen-navbar__label-text">Próximo item</span>
          </span>
        </a>
      </div>
    `;

      expect(body).toContain(menuLinks);

      await route.fulfill({
        response,
        body: body.replace(menuLinks, `${menuLinks}${submenuFixture}`),
      });
    },
    { times: 1 },
  );
};

test("front page renders the Proenem home", async ({ page }) => {
  await page.goto("/");

  await expect(page.locator("body")).toBeVisible();
  await expect(page.locator(".pen-navbar")).toBeVisible();
  await expect(page.getByRole("heading", { level: 1, name: /sua aprovação/i })).toBeVisible();
  await expect(page.locator(".pen-hero-section__title-line")).toHaveCount(2);
  await expect(page.locator(".pen-hero-section__title-line").nth(0)).toHaveText("Sua aprovação não");
  await expect(page.locator(".pen-hero-section__title-line").nth(1)).toHaveText("é sorte é método");
  await expect(page.getByText(/a escola te ensina o conteúdo/i)).toBeVisible();
  await expect(page.getByRole("link", { name: /começar grátis/i }).first()).toHaveAttribute("href", "#planos");
  await expect(page.getByText(/alunos reais, aprovados em algumas das universidades/i)).toBeVisible();
  await expect(page.locator(".pro-home-pain-card")).toHaveCount(4);
  await expect(page.getByRole("heading", { level: 3, name: /começa e abandona/i })).toBeVisible();
  await expect(page.locator(".pro-home-platform-guard")).toContainText("Não é um acervo de vídeos. É um sistema que te diz");
  await expect(page.locator(".pro-home-platform-guard strong")).toHaveText("o próximo passo.");
  await expect(page.locator(".pen-pricing-section")).toBeVisible();
  await expect(page.getByRole("heading", { level: 2, name: /comece de graça.*evolua quando fizer sentido/i })).toBeVisible();
  await expect(page.getByText(/comece grátis, sem cartão.*cancele quando quiser/i)).toBeVisible();
  await expect(page.locator(".pen-plan-card")).toHaveCount(3);
  await expect(page.locator(".pen-plan-card.is-free")).toContainText("Grátis");
  await expect(page.locator(".pen-plan-card.is-free")).toContainText("Diagnóstico inicial + nota prevista");
  await expect(page.locator(".pen-plan-card.is-free .pen-action-link")).toHaveText(/criar conta grátis/i);
  await expect(page.locator(".pen-plan-card.is-free .pen-action-link")).toHaveAttribute("href", "https://estude.proenem.com.br/");
  await expect(page.getByRole("heading", { level: 3, name: "Essencial" })).toHaveCount(0);
  await expect(page.getByRole("heading", { level: 3, name: "Método PRO" })).toBeVisible();
  await expect(page.getByRole("heading", { level: 3, name: "Pro Medicina" })).toBeVisible();
  await expect(page.getByRole("link", { name: /quero o método pro/i })).toHaveAttribute("href", /pay\.hotmart\.com\/W106752534O/);
  await expect(page.getByRole("link", { name: /quero o pro medicina/i })).toHaveAttribute("href", /pay\.hotmart\.com\/X99453521F/);
  await expect(page.locator(".pro-home-school-section").getByRole("link", { name: /falar com nossa equipe/i })).toHaveAttribute("href", "#faq");
  await expect(page.locator(".pen-site-footer")).toBeVisible();
});

test("content pages center the Gutenberg layout without centering text", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.goto("/?pagename=e2e-content-layout");

  const article = page.locator(".entry--page");
  const header = article.locator(".entry__header");
  const content = article.locator(".entry__content");
  const firstBlock = content.locator(":scope > *").first();

  await expect(content).toHaveClass(/is-layout-constrained/);
  await expect(firstBlock).toHaveCSS("text-align", "start");

  const articleBox = await article.boundingBox();
  const headerBox = await header.boundingBox();
  const contentBox = await content.boundingBox();
  const firstBlockBox = await firstBlock.boundingBox();

  expect(articleBox).not.toBeNull();
  expect(headerBox).not.toBeNull();
  expect(contentBox).not.toBeNull();
  expect(firstBlockBox).not.toBeNull();
  expect(contentBox.width).toBeCloseTo(articleBox.width, 0);
  expect(firstBlockBox.width).toBeLessThan(contentBox.width);
  expect(firstBlockBox.x - contentBox.x).toBeCloseTo(
    contentBox.x + contentBox.width - (firstBlockBox.x + firstBlockBox.width),
    0,
  );
  expect(headerBox.x).toBeCloseTo(firstBlockBox.x, 0);
});

test("front page FAQ keeps its width when every item is closed", async ({ page }) => {
  await page.goto("/");

  const items = page.locator(".pen-faq-section__items");
  const openItem = items.locator("details[open]");
  const openWidth = await items.evaluate((element) => element.getBoundingClientRect().width);

  await expect(openItem).toHaveCount(1);
  await openItem.locator("summary").click();
  await expect(openItem).toHaveCount(0);

  const closedWidth = await items.evaluate((element) => element.getBoundingClientRect().width);

  expect(closedWidth).toBeCloseTo(openWidth, 0);
});

test("front page navbar hover keeps adjacent items in place", async ({ page }) => {
  await installNavbarSubmenuFixture(page);
  await page.goto("/");

  const hoveredLink = page.getByRole("link", { name: "Teste principal" });
  const adjacentLink = page.getByRole("link", { name: "Próximo item" });
  const initialPosition = await adjacentLink.boundingBox();

  await hoveredLink.hover();

  const hoveredPosition = await adjacentLink.boundingBox();

  expect(initialPosition).not.toBeNull();
  expect(hoveredPosition).not.toBeNull();
  expect(hoveredPosition.x).toBeCloseTo(initialPosition.x, 1);
  await expect(hoveredLink).toHaveCSS("font-weight", "800");
});

test("front page navbar starts closed on mobile", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await installNavbarSubmenuFixture(page);
  await page.goto("/");

  const toggle = page.locator(".pro-home-navbar-toggle");
  const menu = page.locator(".pro-home-navbar-menu");

  await expect(page.locator(".pen-brand-logo img")).toBeVisible();
  await expect(toggle).toBeVisible();
  await expect(toggle).toHaveAttribute("aria-expanded", "false");
  await expect(menu).toBeHidden();

  await toggle.click();

  await expect(toggle).toHaveAttribute("aria-expanded", "true");
  await expect(menu).toBeVisible();

  const submenuToggle = menu.locator(".pro-home-navbar-submenu-toggle").first();
  const submenu = menu.locator(".pen-navbar__submenu").first();

  await expect(submenuToggle).toHaveAttribute("aria-expanded", "false");
  await expect(submenu).toBeHidden();

  await submenuToggle.click();

  await expect(submenuToggle).toHaveAttribute("aria-expanded", "true");
  await expect(submenu).toBeVisible();
});

test("front page keeps the hero CTA inside the first mobile fold", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const title = await page.locator(".pen-hero-section__title").boundingBox();
  const subtitle = await page.locator(".pro-home-hero-section__subtitle").boundingBox();
  const cta = await page
    .locator(".pro-home-hero-action-bar__actions .pen-button")
    .boundingBox();

  expect(title).not.toBeNull();
  expect(subtitle).not.toBeNull();
  expect(cta).not.toBeNull();
  expect(title.y + title.height).toBeLessThan(subtitle.y);
  expect(cta.y + cta.height).toBeLessThanOrEqual(844);
});

test("front page pillar controls move only the cards without shifting the section", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 1000 });
  await page.emulateMedia({ reducedMotion: "reduce" });
  await page.goto("/");

  const slider = page.locator("[data-pro-home-pillars-slider]");
  const section = page.locator(".pen-pillars-section");
  const cards = slider.locator("[data-pro-home-pillar-card]");
  const previousButton = slider.locator("[data-pro-home-pillars-prev]");
  const nextButton = slider.locator("[data-pro-home-pillars-next]");

  await expect(cards.nth(0)).toHaveClass(/is-active/);
  await nextButton.click();
  await expect(cards.nth(1)).toHaveClass(/is-active/);
  await previousButton.click();
  await expect(cards.nth(0)).toHaveClass(/is-active/);

  const heightSamples = await section.evaluate(async (element) => {
    const samples = [element.getBoundingClientRect().height];
    const next = element.querySelector("[data-pro-home-pillars-next]");

    next.click();

    for (let frame = 0; frame < 14; frame += 1) {
      await new Promise(requestAnimationFrame);
      samples.push(element.getBoundingClientRect().height);
    }

    return samples;
  });

  expect(Math.max(...heightSamples) - Math.min(...heightSamples)).toBeLessThan(1);
});

test("front page pillars start at the first card and accept a mobile swipe", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.emulateMedia({ reducedMotion: "reduce" });
  await page.goto("/");

  const slider = page.locator("[data-pro-home-pillars-slider]");
  const cards = slider.locator("[data-pro-home-pillar-card]");
  const cta = page.locator(".pen-pillars-section__copy > .pen-button");

  await expect(cards.nth(0)).toHaveClass(/is-active/);
  await expect(cards.nth(0)).toContainText("Meta");

  const sliderBox = await slider.boundingBox();
  const ctaBox = await cta.boundingBox();

  expect(sliderBox).not.toBeNull();
  expect(ctaBox).not.toBeNull();
  expect(ctaBox.y).toBeGreaterThan(sliderBox.y + sliderBox.height);

  await slider.dispatchEvent("pointerdown", {
    clientX: 300,
    isPrimary: true,
    pointerId: 1,
  });
  await slider.dispatchEvent("pointerup", {
    clientX: 200,
    isPrimary: true,
    pointerId: 1,
  });

  await expect(cards.nth(1)).toHaveClass(/is-active/);
  await expect(cards.nth(1)).toContainText("Diagnóstico");
});

test("front page keeps the student badge above the mobile portraits", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const badge = await page.locator(".pen-proof-section__badge").boundingBox();
  const firstPortrait = await page.locator(".pen-proof-section__image").first().boundingBox();

  expect(badge).not.toBeNull();
  expect(firstPortrait).not.toBeNull();
  expect(badge.y + badge.height).toBeLessThanOrEqual(firstPortrait.y);
});

test("front page platform uses a compact horizontal menu on mobile", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const section = page.locator(".pen-platform-showcase");
  const tabs = page.locator(".pro-home-platform-tabs");

  await expect(tabs.getByRole("tab")).toHaveCount(6);
  await expect(page.locator(".pro-home-platform-mock__dashboard")).toBeHidden();

  const sectionBox = await section.boundingBox();
  const activeTabBox = await tabs.locator(".is-active").boundingBox();
  const tabsBox = await tabs.boundingBox();
  const tabSizes = await tabs.evaluate((element) => ({
    clientWidth: element.clientWidth,
    scrollWidth: element.scrollWidth,
  }));

  expect(sectionBox).not.toBeNull();
  expect(activeTabBox).not.toBeNull();
  expect(tabsBox).not.toBeNull();
  expect(sectionBox.height).toBeLessThan(1150);
  expect(tabSizes.scrollWidth).toBeGreaterThan(tabSizes.clientWidth);
  expect(activeTabBox.x).toBeGreaterThanOrEqual(tabsBox.x - 1);
  expect(activeTabBox.x + activeTabBox.width).toBeLessThanOrEqual(tabsBox.x + tabsBox.width + 1);
});

test("front page question cards form two mobile rows without stretching the stamp", async ({
  page,
}) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const background = page.locator(".pro-home-question-bank__background");
  const cards = page.locator(".pen-subject-grid .pro-home-subject-card");
  const firstCard = await cards.nth(0).boundingBox();
  const secondCard = await cards.nth(1).boundingBox();
  const thirdCard = await cards.nth(2).boundingBox();

  await expect(background).toBeHidden();
  expect(firstCard).not.toBeNull();
  expect(secondCard).not.toBeNull();
  expect(thirdCard).not.toBeNull();
  expect(Math.abs(firstCard.x - secondCard.x)).toBeLessThan(1);
  expect(secondCard.y).toBeGreaterThan(firstCard.y);
  expect(thirdCard.x).toBeGreaterThan(firstCard.x);
  expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBe(390);
});

test("front page testimonial heading stays inside the mobile viewport", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const heading = await page.locator("#pro-testimonials-title").boundingBox();

  expect(heading).not.toBeNull();
  expect(heading.x).toBeGreaterThanOrEqual(0);
  expect(heading.x + heading.width).toBeLessThanOrEqual(390);
  expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBe(390);
});

test("front page school photo anchors to the mobile card edge below the CTA", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const intro = await page.locator(".pro-home-school-section__intro").boundingBox();
  const photo = await page.locator(".pro-home-school-section__photo-secondary").boundingBox();
  const cta = await page.locator(".pro-home-school-section__cta").boundingBox();

  expect(intro).not.toBeNull();
  expect(photo).not.toBeNull();
  expect(cta).not.toBeNull();
  expect(photo.y).toBeGreaterThanOrEqual(cta.y + cta.height);
  expect(Math.abs(photo.x + photo.width - (intro.x + intro.width))).toBeLessThan(1);
  expect(photo.width / intro.width).toBeGreaterThan(0.5);
});

test("front page trust badges do not overlap in the WordPress grid", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const footerMeta = page.locator(".pen-site-footer__meta");

  await footerMeta.evaluate((element) => {
    if (!element.querySelector(".pen-site-footer__trust")) {
      const trustArea = document.createElement("div");

      trustArea.className =
        "pen-site-footer__widget-area pen-site-footer__trust";
      element.prepend(trustArea);
    }
  });

  const trustArea = footerMeta.locator(".pen-site-footer__trust");

  await trustArea.evaluate((element) => {
    element.innerHTML = `
      <div class="wp-block-group is-layout-grid">
        <div id="reputation-ra" style="min-height: 80px; width: 160px;"></div>
        <script type="application/json"></script>
        <figure class="wp-block-image size-large">
          <svg width="209" height="50" aria-label="Site seguro"></svg>
        </figure>
      </div>
    `;
  });

  const reputation = await trustArea.locator("#reputation-ra").boundingBox();
  const secureSite = await trustArea.locator(".wp-block-image").boundingBox();

  expect(reputation).not.toBeNull();
  expect(secureSite).not.toBeNull();

  const overlaps =
    reputation.x < secureSite.x + secureSite.width &&
    reputation.x + reputation.width > secureSite.x &&
    reputation.y < secureSite.y + secureSite.height &&
    reputation.y + reputation.height > secureSite.y;

  expect(overlaps).toBe(false);
});
