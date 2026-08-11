import { expect, test } from "@playwright/test";

const expectHomeProofContract = async (page) => {
  const proofSection = page.locator(".pen-proof-section");
  const proofSectionCount = await proofSection.count();

  if (proofSectionCount === 0) {
    await expect(page.getByText(/40 mil|40\.000/i)).toHaveCount(0);
    return false;
  }

  await expect(proofSection).toHaveCount(1);
  await expect(proofSection.locator(".pen-proof-section__strip h2")).toHaveText(
    "+ de 40.000 aprovados em universidades públicas",
  );
  await expect(proofSection.locator(".pen-proof-section__logo")).toHaveCount(6);
  expect(await proofSection.locator(".pen-proof-section__student").count()).toBeGreaterThan(0);

  return true;
};

const expectHomeTestimonialsContract = async (page) => {
  const section = page.locator(".pro-home-testimonials");
  const sectionCount = await section.count();

  await expect(
    page.getByText(
      "Ter um plano claro mudou tudo. Eu sabia o que fazer a cada semana e conseguia medir se estava avançando de verdade.",
      { exact: true },
    ),
  ).toHaveCount(0);

  if (sectionCount === 0) {
    return false;
  }

  await expect(section).toHaveCount(1);
  const cards = section.locator("[data-pro-home-testimonial-card]:not(.is-clone)");
  const cardCount = await cards.count();

  expect(cardCount).toBeGreaterThan(0);
  await expect(cards.locator(".pro-home-testimonial-card__quote p")).toHaveCount(cardCount);
  await expect(cards.locator("footer img")).toHaveCount(cardCount);
  await expect(cards.locator("footer strong")).toHaveCount(cardCount);
  await expect(cards.locator("footer small")).toHaveCount(cardCount);

  for (const card of await cards.all()) {
    expect((await card.locator(".pro-home-testimonial-card__quote p").innerText()).trim()).not.toBe("");
    expect((await card.locator("footer strong").innerText()).trim()).not.toBe("");
    expect((await card.locator("footer small").innerText()).trim()).toContain(" · ");
  }

  return true;
};

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
  await expect(page.getByRole("link", { name: /criar conta grátis/i }).first()).toHaveAttribute(
    "href",
    "https://estude.proenem.com.br/signup",
  );
  await expectHomeProofContract(page);
  await expectHomeTestimonialsContract(page);
  await expect(page.getByText("Pedro Martins", { exact: true })).toHaveCount(0);
  await expect(page.locator(".pro-home-pain-card")).toHaveCount(4);
  await expect(page.getByRole("heading", { level: 3, name: /começa e abandona/i })).toBeVisible();
  await expect(page.locator(".pro-home-platform-guard")).toContainText("Não é um acervo de vídeos. É um sistema que te diz");
  await expect(page.locator(".pro-home-platform-guard strong")).toHaveText("o próximo passo.");
  await expect(page.locator(".pen-question-bank-section h2")).toContainText("60 mil questões");
  await expect(page.getByText("+60 mil questões", { exact: true })).toBeVisible();
  await expect(page.locator(".pen-pricing-section")).toBeVisible();
  await expect(page.getByRole("heading", { level: 2, name: /comece de graça.*evolua quando fizer sentido/i })).toBeVisible();
  await expect(page.getByText(/comece grátis, sem cartão.*7 dias de garantia/i)).toBeVisible();
  await expect(page.locator(".pen-plan-card")).toHaveCount(3);
  await expect(page.locator(".pen-plan-card.is-free")).toContainText("Grátis");
  await expect(page.locator(".pen-plan-card.is-free")).toContainText("Diagnóstico inicial + nota prevista");
  await expect(page.locator(".pen-plan-card.is-free")).toContainText("Banco de +60 mil questões");
  await expect(page.locator(".pen-plan-card.is-free .pen-action-link")).toHaveText(/criar conta grátis/i);
  await expect(page.locator(".pen-plan-card.is-free .pen-action-link")).toHaveAttribute(
    "href",
    "https://estude.proenem.com.br/signup",
  );
  await expect(page.getByRole("heading", { level: 3, name: "Essencial" })).toHaveCount(0);
  await expect(page.getByRole("heading", { level: 3, name: "Método PRO", exact: true })).toBeVisible();
  await expect(page.getByRole("heading", { level: 3, name: "Método PRO Avançado" })).toBeVisible();
  await expect(page.getByRole("heading", { level: 3, name: "Pro Medicina" })).toHaveCount(0);
  const methodPlan = page.locator(".pen-plan-card").nth(1);
  const advancedPlan = page.locator(".pen-plan-card").nth(2);
  await expect(methodPlan).toContainText("Tudo do Grátis e mais...");
  await expect(methodPlan).toContainText("Cronograma personalizado completo até o dia da prova");
  await expect(methodPlan).toContainText("2 correções de redação mensais");
  await expect(methodPlan).toContainText("Aulas gravadas com os melhores professores");
  await expect(methodPlan).toContainText("PDFs completos");
  await expect(methodPlan).toContainText("Simulados com nota TRI");
  await expect(methodPlan.locator(".pro-home-plan-card__price-amount")).toHaveText(/12x de R\$\s*29,90/);
  await expect(methodPlan).not.toContainText("Total parcelado: R$ 358,80.");
  await expect(methodPlan.locator(".pro-home-plan-card__guarantee")).toHaveText(/7 dias de garantia/i);
  await expect(advancedPlan).toContainText("Tudo do PRO e mais...");
  await expect(advancedPlan).toContainText("Aulas ao vivo");
  await expect(advancedPlan).toContainText("Revisões ao vivo");
  await expect(advancedPlan).toContainText("Mentoria em grupo");
  await expect(advancedPlan.locator(".pro-home-plan-card__price-amount")).toHaveText(/12x de R\$\s*39,90/);
  await expect(advancedPlan).not.toContainText("Plano anual. Total parcelado: R$ 478,80.");
  await expect(advancedPlan.locator(".pro-home-plan-card__guarantee")).toHaveText(/7 dias de garantia/i);
  await expect(page.getByRole("link", { name: /^quero o método pro$/i })).toHaveAttribute(
    "href",
    /pay\.hotmart\.com\/W106752534O/,
  );
  await expect(page.getByRole("link", { name: /quero o método pro avançado/i })).toHaveAttribute(
    "href",
    "https://medicina.proenem.com.br/",
  );
  await expect(page.locator(".pen-faq-item", { hasText: "E se eu não gostar?" })).toContainText(
    "pode cancelar dentro desse prazo e usar a garantia",
  );
  const b2bLinks = page
    .locator(".pro-home-school-section, .pro-home__final-cta")
    .getByRole("link", { name: /falar com nossa equipe/i });
  await expect(b2bLinks).toHaveCount(2);
  for (const link of await b2bLinks.all()) {
    await expect(link).toHaveAttribute(
      "href",
      "mailto:pro-receita@questedu.dev?subject=Parceria%20com%20escola",
    );
  }
  await expect(page.locator(".pen-site-footer")).toBeVisible();
});

test("front page keeps conversion actions compatible with their labels", async ({ page }) => {
  await page.goto("/");

  await expect(page.locator(".pro-home > .pro-site-navbar")).toHaveCSS("position", "sticky");

  const signupActions = page.getByRole("link", { name: /criar conta grátis/i });
  const questionAction = page.getByRole("link", { name: /explorar questões grátis/i });

  expect(await signupActions.count()).toBeGreaterThanOrEqual(4);

  for (const action of await signupActions.all()) {
    await expect(action).toHaveAttribute("href", "https://estude.proenem.com.br/signup");
  }

  await expect(questionAction).toHaveAttribute(
    "href",
    "https://estude.proenem.com.br/treino/questoes",
  );
  await expect(page.locator('.pro-site-navbar a[href="#"]')).toHaveCount(0);
});

test("front page keeps the navbar sticky and reveals the mobile primary action", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const navbar = page.locator(".pro-home > .pro-site-navbar");
  const hero = page.locator(".pen-hero-section");
  const persistentAction = page.locator("[data-pro-mobile-persistent-action]");
  const persistentLink = persistentAction.getByRole("link", { name: /criar conta grátis/i });
  const supportButton = page.locator("#wpp-icon-btn");
  const toggle = navbar.locator(".pro-home-navbar-toggle");

  await expect(navbar).toHaveCSS("position", "sticky");
  await expect(persistentAction).toBeHidden();

  const navbarHeight = await navbar.evaluate((element) => element.getBoundingClientRect().height);
  const heroDocumentTop = await hero.evaluate(
    (element) => element.getBoundingClientRect().top + window.scrollY,
  );

  await page.locator(".pen-question-bank-section").scrollIntoViewIfNeeded();
  await expect(persistentAction).toBeVisible();
  await expect(persistentLink).toHaveAttribute("href", "https://estude.proenem.com.br/signup");

  const stickyBox = await navbar.boundingBox();
  const actionBox = await persistentAction.boundingBox();
  const scrolledNavbarHeight = await navbar.evaluate((element) => element.getBoundingClientRect().height);
  const scrolledHeroDocumentTop = await hero.evaluate(
    (element) => element.getBoundingClientRect().top + window.scrollY,
  );

  expect(stickyBox).not.toBeNull();
  expect(actionBox).not.toBeNull();
  expect(stickyBox.y).toBe(0);
  expect(scrolledNavbarHeight).toBe(navbarHeight);
  expect(scrolledHeroDocumentTop).toBeCloseTo(heroDocumentTop, 0);
  expect(actionBox.x).toBe(0);
  expect(actionBox.width).toBe(390);
  expect(actionBox.y + actionBox.height).toBeCloseTo(844, 0);
  await expect(persistentAction).toHaveCSS("border-radius", "0px");

  if (await supportButton.count()) {
    const supportBox = await supportButton.boundingBox();

    expect(supportBox).not.toBeNull();
    expect(supportBox.y + supportBox.height).toBeLessThanOrEqual(actionBox.y - 8);
  }

  await toggle.click();
  await expect(persistentAction).toBeHidden();
  await toggle.click();
  await expect(persistentAction).toBeVisible();

  await page.locator(".pen-plan-card.is-free .pen-action-link").scrollIntoViewIfNeeded();
  await expect(persistentAction).toBeHidden();
});

test("Elementor home fixture uses the same conversion and persistent action contracts", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/?pagename=e2e-elementor-home");

  const navbarWidget = page.locator(".elementor-widget-pro_navbar");
  const navbar = navbarWidget.locator(".pro-site-navbar");
  const persistentAction = page.locator("[data-pro-mobile-persistent-action]");

  await expect(navbarWidget).toBeVisible();
  await expect(navbarWidget).toHaveCSS("position", "sticky");
  await expect(navbar).toHaveCSS("flex-direction", "row");

  const navbarBox = await navbar.boundingBox();
  const logoBox = await navbar.locator(".pen-brand-logo").boundingBox();
  const toggleBox = await navbar.locator(".pro-home-navbar-toggle").boundingBox();

  expect(navbarBox).not.toBeNull();
  expect(logoBox).not.toBeNull();
  expect(toggleBox).not.toBeNull();
  expect(navbarBox.height).toBeLessThanOrEqual(80);
  expect(Math.abs(logoBox.y + logoBox.height / 2 - (toggleBox.y + toggleBox.height / 2))).toBeLessThanOrEqual(1);
  await expect(page.locator('.pro-site-navbar a[href="#"]')).toHaveCount(0);
  await expect(page.getByRole("link", { name: /explorar questões grátis/i })).toHaveAttribute(
    "href",
    "https://estude.proenem.com.br/treino/questoes",
  );
  await expect(page.locator(".pen-question-bank-section h2")).toContainText("60 mil questões");
  await expect(page.locator(".pro-home-subject-card__meta")).toHaveCount(0);
  await expectHomeProofContract(page);
  await expectHomeTestimonialsContract(page);

  await page.locator(".pen-question-bank-section").scrollIntoViewIfNeeded();
  await expect(persistentAction).toBeVisible();
  const persistentActionBox = await persistentAction.boundingBox();

  expect(persistentActionBox).not.toBeNull();
  expect(persistentActionBox.x).toBe(0);
  expect(persistentActionBox.width).toBe(390);
  expect(persistentActionBox.y + persistentActionBox.height).toBeCloseTo(844, 0);
  await expect(persistentAction.getByRole("link", { name: /criar conta grátis/i })).toHaveAttribute(
    "href",
    "https://estude.proenem.com.br/signup",
  );
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
  await page.setViewportSize({ width: 390, height: 720 });
  await page.goto("/");
  await page.addStyleTag({
    content:
      '.pro-home .pen-hero-section__title, .pro-home .pro-home-hero-section__subtitle { font-family: "Arial Black", sans-serif !important; }',
  });
  await page.evaluate(() => document.fonts.ready);

  const stage = await page.locator(".pen-hero-section__stage").boundingBox();
  const image = await page.locator(".pen-hero-section__image").boundingBox();
  const title = await page.locator(".pen-hero-section__title").boundingBox();
  const subtitle = await page.locator(".pro-home-hero-section__subtitle").boundingBox();
  const support = page.locator(".pro-home-hero-action-bar__support");
  const supportBox = await support.boundingBox();
  const supportLineHeight = await support.evaluate((element) =>
    Number.parseFloat(window.getComputedStyle(element).lineHeight),
  );
  const cta = await page
    .locator(".pro-home-hero-action-bar__actions .pen-button")
    .boundingBox();

  expect(stage).not.toBeNull();
  expect(image).not.toBeNull();
  expect(title).not.toBeNull();
  expect(subtitle).not.toBeNull();
  expect(supportBox).not.toBeNull();
  expect(cta).not.toBeNull();
  expect(stage.height).toBeLessThanOrEqual(400);
  expect(image.y).toBeGreaterThanOrEqual(stage.y - 24);
  await expect(page.locator(".pro-home-hero-section__subtitle")).toHaveCSS("font-size", "18px");
  await expect(support).toHaveCSS("font-size", "16px");
  expect(
    await page.locator(".pen-hero-section__title-line").evaluateAll((lines) =>
      Math.max(...lines.map((line) => line.scrollWidth - line.clientWidth)),
    ),
  ).toBeLessThanOrEqual(1);
  expect(supportBox.height).toBeLessThanOrEqual(supportLineHeight * 4.05);
  expect(subtitle.y - (title.y + title.height)).toBeGreaterThanOrEqual(12);
  expect(subtitle.y - (title.y + title.height)).toBeLessThanOrEqual(48);
  expect(cta.y + cta.height).toBeLessThanOrEqual(720);

  await page.setViewportSize({ width: 360, height: 720 });

  const narrowSupportBox = await support.boundingBox();
  expect(narrowSupportBox).not.toBeNull();
  expect(narrowSupportBox.height).toBeLessThanOrEqual(supportLineHeight * 4.05);
});

test("front page separates the hero subtitle on wide and short screens", async ({ page }) => {
  await page.setViewportSize({ width: 1600, height: 650 });
  await page.goto("/");
  await page.addStyleTag({
    content:
      '.pro-home .pen-hero-section__title, .pro-home .pro-home-hero-section__subtitle { font-family: "Arial Black", sans-serif !important; }',
  });
  await page.evaluate(() => document.fonts.ready);

  const hero = await page.locator(".pen-hero-section").boundingBox();
  const title = await page.locator(".pen-hero-section__title").boundingBox();
  const subtitle = await page.locator(".pro-home-hero-section__subtitle").boundingBox();

  expect(hero).not.toBeNull();
  expect(title).not.toBeNull();
  expect(subtitle).not.toBeNull();
  expect(subtitle.y - (title.y + title.height)).toBeGreaterThanOrEqual(12);
  expect(subtitle.y + subtitle.height).toBeLessThanOrEqual(hero.y + hero.height);
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
  await expect(cards.nth(2).locator(":scope > span")).toHaveCSS("color", "rgb(26, 26, 26)");
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
  await page.evaluate(() => document.fonts.ready);

  const slider = page.locator("[data-pro-home-pillars-slider]");
  const cards = slider.locator("[data-pro-home-pillar-card]");
  const cta = page.locator(".pen-pillars-section__copy > .pen-button");
  const pill = page.locator(".pen-pillars-section__copy > .pen-section-pill");
  const title = page.locator(".pen-pillars-section__copy > h2");
  const paragraphs = page.locator(
    ".pen-pillars-section__copy > p:not(.pen-section-pill)",
  );

  await expect(cards.nth(0)).toHaveClass(/is-active/);
  await expect(cards.nth(0)).toContainText("Meta");
  await expect(cards.nth(0).locator(".pen-step-card__image")).toHaveAttribute("src", /pillar-meta\.webp$/);
  await expect(cards.nth(3).locator(".pen-step-card__image")).toHaveAttribute("src", /student_school_2\.webp$/);
  await expect(cards.nth(3).locator(".pen-step-card__image")).toHaveCSS("object-position", "50% 0%");

  const pillBox = await pill.boundingBox();
  const titleBox = await title.boundingBox();
  const firstParagraphBox = await paragraphs.nth(0).boundingBox();
  const secondParagraphBox = await paragraphs.nth(1).boundingBox();
  const sliderBox = await slider.boundingBox();
  const ctaBox = await cta.boundingBox();

  expect(pillBox).not.toBeNull();
  expect(titleBox).not.toBeNull();
  expect(firstParagraphBox).not.toBeNull();
  expect(secondParagraphBox).not.toBeNull();
  expect(sliderBox).not.toBeNull();
  expect(ctaBox).not.toBeNull();
  expect(titleBox.y - (pillBox.y + pillBox.height)).toBeLessThanOrEqual(24);
  expect(firstParagraphBox.y - (titleBox.y + titleBox.height)).toBeLessThanOrEqual(16);
  expect(secondParagraphBox.y - (firstParagraphBox.y + firstParagraphBox.height)).toBeLessThanOrEqual(20);
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

test("front page pricing intro keeps a compact mobile rhythm", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");
  await page.evaluate(() => document.fonts.ready);

  const section = page.locator(".pen-pricing-section");
  const title = section.locator(".pro-home-pricing__intro h2");
  const support = section.locator(".pro-home-pricing__intro p").first();
  const plans = section.locator(".pen-plan-grid");
  const freePlanButton = section.locator(".pen-plan-card.is-free .pen-action-link");
  const featuredPlanButton = section.locator(".pen-plan-card.is-featured .pen-action-link");

  const titleBox = await title.boundingBox();
  const supportBox = await support.boundingBox();
  const plansBox = await plans.boundingBox();

  expect(titleBox).not.toBeNull();
  expect(supportBox).not.toBeNull();
  expect(plansBox).not.toBeNull();
  await expect(section.locator(".pro-home-pricing__seal")).toHaveCount(0);
  await expect(title).toHaveCSS("text-align", "center");
  await expect(support).toHaveCSS("font-size", "16px");
  await expect(support).toHaveCSS("text-align", "left");
  await expect(featuredPlanButton).toHaveCSS(
    "background-color",
    await freePlanButton.evaluate((element) => window.getComputedStyle(element).backgroundColor),
  );
  expect(supportBox.y - (titleBox.y + titleBox.height)).toBeLessThanOrEqual(16);
  expect(plansBox.y - (supportBox.y + supportBox.height)).toBeLessThanOrEqual(32);

  await page.setViewportSize({ width: 1440, height: 900 });
  await expect(featuredPlanButton).toHaveCSS(
    "background-color",
    await freePlanButton.evaluate((element) => window.getComputedStyle(element).backgroundColor),
  );
});

test("front page pricing keeps centered prices and guarantees below paid CTAs without overlap", async ({ page }) => {
  for (const viewport of [
    { width: 390, height: 844 },
    { width: 1366, height: 768 },
    { width: 1600, height: 900 },
  ]) {
    await page.setViewportSize(viewport);
    await page.goto("/");
    await page.evaluate(() => document.fonts.ready);

    const cards = page.locator(".pen-pricing-section .pen-plan-card");

    for (let index = 0; index < 3; index += 1) {
      const card = cards.nth(index);
      const featuresBox = await card.locator("ul").boundingBox();
      const priceBox = await card.locator(".pro-home-plan-card__price").boundingBox();
      const ctaBox = await card.locator(".pen-action-link").boundingBox();
      const cardBox = await card.boundingBox();
      const priceAlignment = await card
        .locator(".pro-home-plan-card__price")
        .evaluate((element) => window.getComputedStyle(element).textAlign);

      expect(featuresBox).not.toBeNull();
      expect(priceBox).not.toBeNull();
      expect(ctaBox).not.toBeNull();
      expect(cardBox).not.toBeNull();
      expect(priceAlignment).toBe("center");
      expect(priceBox.y).toBeGreaterThanOrEqual(featuresBox.y + featuresBox.height - 1);
      expect(ctaBox.y).toBeGreaterThanOrEqual(priceBox.y + priceBox.height - 1);

      if (index > 0) {
        const guaranteeBox = await card.locator(".pro-home-plan-card__guarantee").boundingBox();

        expect(guaranteeBox).not.toBeNull();
        expect(guaranteeBox.y).toBeGreaterThanOrEqual(ctaBox.y + ctaBox.height - 1);
        expect(guaranteeBox.y + guaranteeBox.height).toBeLessThanOrEqual(cardBox.y + cardBox.height + 1);
      } else {
        expect(ctaBox.y + ctaBox.height).toBeLessThanOrEqual(cardBox.y + cardBox.height + 1);
      }
    }
  }
});

test("front page keeps optional verified proof inside the mobile viewport", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const hasProofSection = await expectHomeProofContract(page);

  if (!hasProofSection) {
    return;
  }

  const proofGrid = page.locator(".pen-proof-section__students");
  const gridMetrics = await proofGrid.evaluate((element) => {
    const rect = element.getBoundingClientRect();

    return {
      left: rect.left,
      right: rect.right,
      viewportWidth: window.innerWidth,
      documentWidth: document.documentElement.scrollWidth,
    };
  });

  expect(gridMetrics.left).toBe(0);
  expect(gridMetrics.right).toBeCloseTo(gridMetrics.viewportWidth, 0);
  expect(gridMetrics.documentWidth).toBe(gridMetrics.viewportWidth);
});

test("front page platform uses a compact horizontal menu on mobile", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const tabs = page.locator(".pro-home-platform-tabs");
  const controls = page.locator(".pro-home-platform-tabs__controls");
  const previousButton = controls.locator("[data-pro-home-platform-prev]");
  const nextButton = controls.locator("[data-pro-home-platform-next]");

  await expect(tabs.getByRole("tab")).toHaveCount(5);
  await expect(controls).toBeVisible();
  await expect(page.locator("[data-pro-home-platform-image]")).toBeVisible();

  const activeTabBox = await tabs.locator(".is-active").boundingBox();
  const tabsBox = await tabs.boundingBox();
  const controlsBox = await controls.boundingBox();
  const tabSizes = await tabs.evaluate((element) => ({
    clientWidth: element.clientWidth,
    scrollWidth: element.scrollWidth,
  }));

  expect(activeTabBox).not.toBeNull();
  expect(tabsBox).not.toBeNull();
  expect(controlsBox).not.toBeNull();
  expect(tabsBox.height).toBeLessThan(90);
  expect(tabSizes.scrollWidth).toBeGreaterThan(tabSizes.clientWidth);
  expect(activeTabBox.x).toBeGreaterThanOrEqual(tabsBox.x - 1);
  expect(activeTabBox.x + activeTabBox.width).toBeLessThanOrEqual(tabsBox.x + tabsBox.width + 1);
  expect(Math.abs(controlsBox.x + controlsBox.width / 2 - (tabsBox.x + tabsBox.width / 2))).toBeLessThan(1);

  const viewportOverflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
  expect(viewportOverflow).toBeLessThanOrEqual(1);

  await tabs.evaluate((element) => element.scrollTo({ behavior: "auto", left: 0 }));
  await expect(previousButton).toBeDisabled();
  await expect(nextButton).toBeEnabled();

  const activeTitle = await page.locator("[data-pro-home-platform-title]").textContent();
  await nextButton.click();
  await expect.poll(() => tabs.evaluate((element) => element.scrollLeft)).toBeGreaterThan(0);
  await expect(previousButton).toBeEnabled();
  await expect(page.locator("[data-pro-home-platform-title]")).toHaveText(activeTitle);

  await page.setViewportSize({ width: 1024, height: 844 });
  await expect(controls).toBeHidden();
});

test("front page platform keeps benefit lists informational across every tab", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 1000 });
  await page.goto("/");

  const tabs = page.locator("[data-pro-home-platform-tab]");
  const bullets = page.locator("[data-pro-home-platform-bullets]");

  await expect(tabs).toHaveCount(5);
  await expect(bullets.locator("a, button")).toHaveCount(0);

  for (const tab of await tabs.all()) {
    await tab.click();

    const items = bullets.locator("li");

    await expect(items).toHaveCount(3);
    await expect(items.first()).toHaveCSS("cursor", "default");
    await expect(items.first()).toHaveCSS("box-shadow", "none");
    await expect(items.first()).toHaveCSS("border-radius", "0px");
  }
});

test("front page platform swaps real screenshots without layout shift", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 1000 });
  await page.goto("/");

  const tabs = page.locator("[data-pro-home-platform-tab]");
  const image = page.locator("[data-pro-home-platform-image]");
  const media = page.locator(".pro-home-platform-mock__media");
  const expectedImages = [
    "live-960.webp",
    "question-bank-960.webp",
    "study-plan-960.webp",
    "essay-feedback-960.webp",
    "simulations-960.webp",
  ];

  await expect(tabs).toHaveCount(expectedImages.length);
  await expect(image).toHaveAttribute("loading", "lazy");
  await expect(image).toHaveAttribute("decoding", "async");
  await expect(image).toHaveAttribute("width", /\d+/);
  await expect(image).toHaveAttribute("height", /\d+/);

  const initialMediaBox = await media.boundingBox();
  expect(initialMediaBox).not.toBeNull();

  for (const [index, expectedImage] of expectedImages.entries()) {
    await tabs.nth(index).click();
    await expect(image).toHaveAttribute("src", new RegExp(expectedImage.replace(".", "\\.")));
    await expect(image).not.toHaveAttribute("alt", "");

    const currentMediaBox = await media.boundingBox();
    expect(currentMediaBox).not.toBeNull();
    expect(Math.abs(currentMediaBox.height - initialMediaBox.height)).toBeLessThanOrEqual(1);
  }

  await tabs.first().focus();
  await tabs.first().press("ArrowRight");
  await expect(tabs.nth(1)).toHaveAttribute("aria-selected", "true");
  await expect(tabs.nth(1)).toBeFocused();
  await expect(image).toHaveAttribute("src", /question-bank-960\.webp/);
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

test("front page subject icons turn yellow only on hover or focus", async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 1000 });
  await page.goto("/");

  const cards = page.locator(".pen-subject-grid .pro-home-subject-card");
  const icons = cards.locator(".pro-home-subject-card__icon");
  const destinations = [
    "https://estude.proenem.com.br/treino/questoes/s/uimica-rganica/natureza/sa",
    "https://estude.proenem.com.br/treino/questoes/s/iologia-como-ciencia/natureza/sa",
    "https://estude.proenem.com.br/treino/questoes/s/matematica/a",
    "https://estude.proenem.com.br/treino/questoes/s/istiria-eral/humanas/sa",
    "https://estude.proenem.com.br/treino/questoes/s/nsino-da-ingua-strangeira-nglesa/linguagens/sa",
    "https://estude.proenem.com.br/treino/questoes/s/linguagens/a",
  ];
  await expect(cards).toHaveCount(6);
  await expect(cards.locator(".pro-home-subject-card__meta")).toHaveCount(0);

  for (const [index, icon] of (await icons.all()).entries()) {
    const card = cards.nth(index);

    await expect(icon).toHaveCSS("background-color", "rgb(250, 157, 205)");
    await expect(card).toHaveAttribute("href", destinations[index]);
    await expect(card).toHaveAttribute("target", "_blank");
    await expect(card).toHaveAttribute("rel", "noopener noreferrer");

    const centerOffsets = await card.evaluate((element) => {
      const cardBox = element.getBoundingClientRect();
      const cardCenter = cardBox.top + cardBox.height / 2;

      return [
        ".pro-home-subject-card__icon",
        ".pro-home-subject-card__body",
        ".pro-home-subject-card__arrow",
      ].map((selector) => {
        const childBox = element.querySelector(selector).getBoundingClientRect();
        return Math.abs(childBox.top + childBox.height / 2 - cardCenter);
      });
    });

    for (const offset of centerOffsets) {
      expect(offset).toBeLessThanOrEqual(1);
    }
  }

  await cards.nth(1).hover();
  await expect(icons.nth(1)).toHaveCSS("background-color", "rgb(255, 230, 128)");
  await expect(icons.nth(0)).toHaveCSS("background-color", "rgb(250, 157, 205)");

  await cards.nth(2).focus();
  await expect(icons.nth(2)).toHaveCSS("background-color", "rgb(255, 230, 128)");
});

test("front page testimonial heading stays inside the mobile viewport", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  if (!(await expectHomeTestimonialsContract(page))) {
    return;
  }

  const heading = await page.locator("#pro-testimonials-title").boundingBox();

  expect(heading).not.toBeNull();
  expect(heading.x).toBeGreaterThanOrEqual(0);
  expect(heading.x + heading.width).toBeLessThanOrEqual(390);
  expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBe(390);
});

test("front page testimonial controls include the external approved-students link", async ({
  page,
}) => {
  await page.setViewportSize({ width: 1440, height: 1000 });
  await page.goto("/");

  if (!(await expectHomeTestimonialsContract(page))) {
    return;
  }

  const controls = page.locator(".pro-home-testimonials__controls");
  const previous = controls.getByRole("button", { name: "Depoimento anterior" });
  const next = controls.getByRole("button", { name: "Próximo depoimento" });
  const more = controls.getByRole("link", { name: "Ver mais" });

  await expect(more).toHaveAttribute("href", "https://aprovados.proenem.com.br/");
  await expect(more).toHaveAttribute("target", "_blank");
  await expect(more).toHaveAttribute("rel", "noopener noreferrer");
  await expect(more).toHaveClass(/pen-button--secondary/);
  await expect(more).toHaveCSS("background-color", "rgb(255, 255, 255)");

  const [previousBox, nextBox, moreBox] = await Promise.all([
    previous.boundingBox(),
    next.boundingBox(),
    more.boundingBox(),
  ]);

  expect(previousBox).not.toBeNull();
  expect(nextBox).not.toBeNull();
  expect(moreBox).not.toBeNull();
  expect(Math.abs(previousBox.y - nextBox.y)).toBeLessThan(1);
  expect(Math.abs(nextBox.y - moreBox.y)).toBeLessThan(1);
  expect(moreBox.x).toBeGreaterThan(nextBox.x + nextBox.width);
  expect(moreBox.x - (nextBox.x + nextBox.width)).toBeGreaterThan(
    nextBox.x - (previousBox.x + previousBox.width),
  );

  const activeCard = page.locator("[data-pro-home-testimonial-card].is-active");
  const activeQuote = await activeCard.locator(".pro-home-testimonial-card__quote p").innerText();

  await next.focus();
  await next.press("Enter");
  await expect(page.locator("[data-pro-home-testimonial-card].is-active .pro-home-testimonial-card__quote p")).not.toHaveText(activeQuote);
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
