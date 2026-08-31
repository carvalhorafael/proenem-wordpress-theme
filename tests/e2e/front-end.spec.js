import AxeBuilder from "@axe-core/playwright";
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
  await expect(proofSection.locator('.pen-proof-section__logo[alt="UFMG"]')).toBeVisible();
  await expect(proofSection.locator('.pen-proof-section__logo[alt="UERJ"]')).toHaveCount(0);
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

const expectMinimumTouchTargets = async (targets, minimumSize = 44) => {
  const boxes = await targets.evaluateAll((elements) =>
    elements.map((element) => {
      const bounds = element.getBoundingClientRect();

      return {
        height: bounds.height,
        label: element.getAttribute("aria-label") || element.textContent.trim(),
        width: bounds.width,
        x: bounds.x,
        y: bounds.y,
      };
    }),
  );

  expect(boxes.length).toBeGreaterThan(0);

  for (const box of boxes) {
    expect(box.label).not.toBe("");
    expect(box.width).toBeGreaterThanOrEqual(minimumSize);
    expect(box.height).toBeGreaterThanOrEqual(minimumSize);
  }

  return boxes;
};

const expectTargetsNotToOverlap = (boxes) => {
  for (const [index, current] of boxes.entries()) {
    for (const next of boxes.slice(index + 1)) {
      const overlapWidth = Math.max(
        0,
        Math.min(current.x + current.width, next.x + next.width) - Math.max(current.x, next.x),
      );
      const overlapHeight = Math.max(
        0,
        Math.min(current.y + current.height, next.y + next.height) - Math.max(current.y, next.y),
      );

      expect(overlapWidth * overlapHeight).toBeLessThanOrEqual(0.5);
    }
  }
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
        <a class="pen-navbar__action pen-navbar__action--secondary" href="#" aria-haspopup="true" data-e2e-navbar-submenu-trigger>
          <span class="pen-navbar__label" data-label="Entrar">
            <span class="pen-navbar__label-text">Entrar</span>
          </span>
        </a>
        <button class="pro-home-navbar-submenu-toggle" type="button" aria-controls="e2e-navbar-submenu" aria-expanded="false">
          <span class="screen-reader-text">Alternar submenu de Entrar</span>
          <span aria-hidden="true">⌄</span>
        </button>
        <ul id="e2e-navbar-submenu" class="pen-navbar__submenu">
          <li class="pen-navbar__submenu-item">
            <a class="pen-navbar__submenu-link" href="https://estude.proenem.com.br/">Acesse Proenem</a>
          </li>
          <li class="pen-navbar__submenu-item">
            <a class="pen-navbar__submenu-link" href="https://medicina.proenem.com.br/">Acesse Promedicina</a>
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
  await expect(page.locator(".pen-hero-section__title-line").nth(0)).toHaveText("Sua aprovação no");
  await expect(page.locator(".pen-hero-section__title-line").nth(1)).toHaveText("ENEM começa aqui.");
  await expect(page.getByText(/a Proenem orienta sua preparação/i)).toBeVisible();
  await expect(page.locator(".pro-home-hero-action-bar__support")).toHaveText(
    "Diagnóstico, plano personalizado, aulas, mais de 60 mil questões, simulados com TRI e redação corrigida para você evoluir até a prova.",
  );
  await expect(page.getByRole("link", { name: /conheça a turma intensiva/i }).first()).toHaveAttribute(
    "href",
    "http://localhost:8898/#planos",
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
  await expect(page.getByRole("heading", { level: 2, name: /sua preparação completa.*do diagnóstico até a prova/i })).toBeVisible();
  await expect(page.getByText(/turma intensiva 2026.*7 dias de garantia/i)).toBeVisible();
  await expect(page.locator(".pen-plan-card")).toHaveCount(2);
  await expect(page.locator(".pen-plan-card.pro-home-plan-card--stack")).toHaveCount(2);
  await expect(page.locator(".pen-plan-card.is-free")).toHaveCount(1);
  await expect(page.locator(".pen-plan-card.is-featured")).toHaveCount(1);
  await expect(page.getByRole("heading", { level: 3, name: "Essencial" })).toHaveCount(0);
  await expect(page.getByRole("heading", { level: 3, name: "Turma Intensiva 2026", exact: true })).toBeVisible();
  await expect(page.getByRole("heading", { level: 3, name: "Método PRO Avançado" })).toHaveCount(0);
  await expect(page.getByRole("heading", { level: 3, name: "Pro Medicina" })).toHaveCount(0);

  const freePlan = page.locator(".pen-plan-card.is-free");
  await expect(freePlan.getByRole("heading", { level: 3 })).toHaveText("Grátis");
  await expect(freePlan).toContainText("Diagnóstico inicial + nota prevista");
  await expect(freePlan).toContainText("Banco de +60 mil questões");
  await expect(freePlan).toContainText("Sem cartão");
  await expect(freePlan.locator(".pro-home-plan-card__price-amount")).toHaveText(/R\$\s*0/);
  await expect(freePlan.getByRole("link", { name: /criar conta grátis/i })).toHaveAttribute(
    "href",
    /estude\.proenem\.com\.br\/signup/,
  );

  const methodPlan = page.locator(".pen-plan-card.is-featured");
  await expect(methodPlan).toContainText("Cronograma semanal");
  await expect(methodPlan).toContainText("Correção de redação");
  await expect(methodPlan).toContainText("Aulas e pdfs com os melhores professores");
  await expect(methodPlan).toContainText("Simulados corrigidos no padrão ENEM");
  await expect(methodPlan).toContainText("Revisões inteligentes por matéria");
  await expect(methodPlan).toContainText("Mais de 50 mil questões para praticar");
  await expect(methodPlan).toContainText("6 meses de acesso");
  await expect(methodPlan.locator(".pro-home-plan-card__price-amount")).toHaveText(/12x de R\$\s*29,90/);
  await expect(methodPlan.locator(".pro-home-plan-card__price")).toContainText("ou R$ 306,90 à vista");
  await expect(methodPlan).not.toContainText("Total parcelado: R$ 358,80.");
  // The stacked card has no trust list; the guarantee carries that reassurance.
  await expect(methodPlan.locator(".pro-home-plan-card__trust")).toHaveCount(0);
  await expect(methodPlan.locator(".pro-home-plan-card__guarantee")).toContainText("7 dias de garantia.");
  await expect(page.getByRole("link", { name: /^quero a turma intensiva$/i })).toHaveAttribute(
    "href",
    /pay\.hotmart\.com\/W106752534O/,
  );
  await expect(page.getByRole("link", { name: /quero o método pro avançado/i })).toHaveCount(0);
  await expect(page.locator(".pen-faq-section")).not.toContainText("Método PRO Avançado");
  await expect(page.locator(".pen-faq-section")).not.toContainText("Posso começar de graça?");
  await expect(page.locator(".pen-faq-section")).toContainText("O que está incluído na Turma Intensiva?");
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

  const planActions = page.locator(
    ".pro-home > .pro-site-navbar a[href*='#planos'], .pro-home-hero-action-bar a, .pen-pillars-section__copy > a, .pro-home-pain-section__cta, .pro-home-question-bank__cta",
  );

  expect(await planActions.count()).toBeGreaterThanOrEqual(5);

  for (const action of await planActions.all()) {
    await expect(action).toHaveAttribute("href", "http://localhost:8898/#planos");
  }

  await page.locator(".pro-home-hero-action-bar").getByRole("link").click();
  await expect(page).toHaveURL(/#planos$/);

  const navbarBox = await page.locator(".pro-home > .pro-site-navbar").boundingBox();
  const pricingBox = await page.locator("#planos").boundingBox();

  expect(navbarBox).not.toBeNull();
  expect(pricingBox).not.toBeNull();
  expect(pricingBox.y).toBeGreaterThanOrEqual(navbarBox.height - 2);

  const freeSignupLinks = page.getByRole("link", { name: /criar conta grátis/i });
  await expect(freeSignupLinks).toHaveCount(1);
  await expect(page.locator(".pen-plan-card.is-free").getByRole("link", { name: /criar conta grátis/i })).toHaveCount(1);
  await expect(page.getByRole("link", { name: /explorar questões grátis/i })).toHaveCount(0);
  await expect(
    page.locator(".pro-home > .pro-site-navbar").getByRole("link", { name: "Entrar", exact: true }),
  ).toHaveAttribute("href", "#");
});

test("front page keeps the navbar sticky and reveals the mobile primary action", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");

  const navbar = page.locator(".pro-home > .pro-site-navbar");
  const hero = page.locator(".pen-hero-section");
  const persistentAction = page.locator("[data-pro-mobile-persistent-action]");
  const persistentLink = persistentAction.getByRole("link", { name: /ver plano e preço/i });
  const supportButton = page.locator("#wpp-icon-btn");
  const toggle = navbar.locator(".pro-home-navbar-toggle");

  await expect(navbar).toHaveCSS("position", "sticky");
  await expect(persistentAction).toBeHidden();

  const navbarHeight = await navbar.evaluate((element) => element.getBoundingClientRect().height);
  const heroDocumentTop = await hero.evaluate(
    (element) => element.getBoundingClientRect().top + window.scrollY,
  );

  await page.locator(".pen-platform-showcase").scrollIntoViewIfNeeded();
  await expect(persistentAction).toBeVisible();
  await expect(persistentLink).toHaveAttribute("href", "http://localhost:8898/#planos");

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

  await page.locator(".pen-plan-card.is-featured .pen-action-link").scrollIntoViewIfNeeded();
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
  await expect(
    navbar.getByRole("link", { name: "Entrar", exact: true, includeHidden: true }),
  ).toHaveAttribute("href", "#");
  await expect(page.getByRole("link", { name: /conheça a turma intensiva/i }).last()).toHaveAttribute(
    "href",
    "http://localhost:8898/#planos",
  );
  await expect(page.locator(".pen-question-bank-section h2")).toContainText("60 mil questões");
  await expect(page.locator(".pro-home-subject-card__meta")).toHaveCount(0);
  await expectHomeProofContract(page);
  await expectHomeTestimonialsContract(page);
  await expect(page.locator(".pen-pricing-section .pen-plan-card")).toHaveCount(1);
  await expect(page.getByRole("heading", { level: 3, name: "Turma Intensiva 2026" })).toBeVisible();
  await expect(page.getByRole("heading", { level: 3, name: "Método PRO Avançado" })).toHaveCount(0);
  await expect(page.locator(".pen-faq-section")).not.toContainText("Método PRO Avançado");

  await page.locator(".pen-platform-showcase").scrollIntoViewIfNeeded();
  await expect(persistentAction).toBeVisible();
  const persistentActionBox = await persistentAction.boundingBox();

  expect(persistentActionBox).not.toBeNull();
  expect(persistentActionBox.x).toBe(0);
  expect(persistentActionBox.width).toBe(390);
  expect(persistentActionBox.y + persistentActionBox.height).toBeCloseTo(844, 0);
  await expect(persistentAction.getByRole("link", { name: /ver plano e preço/i })).toHaveAttribute(
    "href",
    "http://localhost:8898/#planos",
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

  const hoveredLink = page.locator("[data-e2e-navbar-submenu-trigger]");
  const adjacentLink = page.getByRole("link", { name: "Próximo item" });
  const initialPosition = await adjacentLink.boundingBox();

  await expect(hoveredLink).toHaveAttribute("href", "#");
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
  const submenuPrimaryTrigger = menu.locator("[data-e2e-navbar-submenu-trigger]");
  const submenu = menu.locator(".pen-navbar__submenu").first();
  const initialUrl = page.url();

  await expect(submenuPrimaryTrigger).toHaveAttribute("href", "#");
  await expect(submenuToggle).toHaveAttribute("aria-expanded", "false");
  await expect(submenuPrimaryTrigger).toHaveAttribute("aria-expanded", "false");
  await expect(submenu).toBeHidden();

  await submenuPrimaryTrigger.click();

  expect(page.url()).toBe(initialUrl);
  await expect(submenuToggle).toHaveAttribute("aria-expanded", "true");
  await expect(submenuPrimaryTrigger).toHaveAttribute("aria-expanded", "true");
  await expect(submenu).toBeVisible();

  await submenuToggle.click();

  await expect(submenuToggle).toHaveAttribute("aria-expanded", "false");
  await expect(submenuPrimaryTrigger).toHaveAttribute("aria-expanded", "false");
  await expect(submenu).toBeHidden();
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

  await page.addStyleTag({
    content:
      ".pro-home .pen-hero-section__title, .pro-home .pro-home-hero-section__subtitle { font-family: sans-serif !important; }",
  });
  await page.evaluate(() => document.fonts.ready);

  const fallbackTitle = await page.locator(".pen-hero-section__title").boundingBox();
  const fallbackSubtitle = await page
    .locator(".pro-home-hero-section__subtitle")
    .boundingBox();

  expect(fallbackTitle).not.toBeNull();
  expect(fallbackSubtitle).not.toBeNull();
  expect(fallbackSubtitle.y - (fallbackTitle.y + fallbackTitle.height)).toBeGreaterThanOrEqual(12);
  expect(fallbackSubtitle.y - (fallbackTitle.y + fallbackTitle.height)).toBeLessThanOrEqual(48);

  await page.setViewportSize({ width: 360, height: 720 });

  const narrowCta = await page
    .locator(".pro-home-hero-action-bar__actions .pen-button")
    .boundingBox();
  expect(narrowCta).not.toBeNull();
  expect(narrowCta.y + narrowCta.height).toBeLessThanOrEqual(720);
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
  await nextButton.focus();
  await nextButton.press("Enter");
  await expect(cards.nth(1)).toHaveClass(/is-active/);
  await previousButton.focus();
  await previousButton.press("Enter");
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
  const featuredPlanButton = section.locator(".pen-plan-card.is-featured .pen-action-link");

  const { titleBox, supportBox, plansBox } = await section.evaluate((element) => ({
    titleBox: element.querySelector(".pro-home-pricing__intro h2")?.getBoundingClientRect().toJSON(),
    supportBox: element.querySelector(".pro-home-pricing__intro p")?.getBoundingClientRect().toJSON(),
    plansBox: element.querySelector(".pen-plan-grid")?.getBoundingClientRect().toJSON(),
  }));

  expect(titleBox).not.toBeNull();
  expect(supportBox).not.toBeNull();
  expect(plansBox).not.toBeNull();
  await expect(section.locator(".pro-home-pricing__seal")).toHaveCount(0);
  await expect(section.locator(".pro-home-pricing__intro p")).toHaveCount(1);
  await expect(support.locator("br")).toHaveCount(0);
  await expect(featuredPlanButton).toHaveClass(/pen-action-link--primary/);
  await expect(title).toHaveCSS("text-align", "center");
  await expect(support).toHaveCSS("font-size", "16px");
  await expect(support).toHaveCSS("text-align", "left");
  await expect(featuredPlanButton).toHaveCSS("background-color", "rgb(255, 214, 0)");
  expect(supportBox.y - (titleBox.y + titleBox.height)).toBeLessThanOrEqual(16);
  expect(plansBox.y - (supportBox.y + supportBox.height)).toBeLessThanOrEqual(32);

  await page.setViewportSize({ width: 1440, height: 900 });
  await expect(featuredPlanButton).toHaveCSS("background-color", "rgb(255, 214, 0)");
  const desktopSupportMetrics = await support.evaluate((element) => {
    const styles = window.getComputedStyle(element);

    return {
      height: element.getBoundingClientRect().height,
      lineHeight: Number.parseFloat(styles.lineHeight),
    };
  });
  expect(desktopSupportMetrics.height).toBeLessThanOrEqual(desktopSupportMetrics.lineHeight * 2.1);
});

test("front page pricing stacks two plan cards without overlap", async ({ page }) => {
  for (const viewport of [
    { width: 390, height: 844 },
    { width: 1366, height: 768 },
    { width: 1600, height: 900 },
  ]) {
    await page.setViewportSize(viewport);
    await page.goto("/");
    await page.evaluate(() => document.fonts.ready);

    const section = page.locator(".pen-pricing-section");
    const grid = section.locator(".pen-plan-grid");
    const cards = page.locator(".pen-pricing-section .pen-plan-card");
    const sectionBox = await section.boundingBox();
    const gridBox = await grid.boundingBox();

    await expect(cards).toHaveCount(2);
    expect(sectionBox).not.toBeNull();
    expect(gridBox).not.toBeNull();
    expect(Math.abs(gridBox.x + gridBox.width / 2 - (sectionBox.x + sectionBox.width / 2))).toBeLessThanOrEqual(1);

    const boxes = [];

    for (let index = 0; index < 2; index += 1) {
      const card = cards.nth(index);
      const { priceBox, ctaBox, listBox, cardBox } = await card.evaluate((element) => {
        const rect = (selector) => element.querySelector(selector)?.getBoundingClientRect().toJSON();

        return {
          priceBox: rect(".pro-home-plan-card__price"),
          ctaBox: rect(".pen-action-link"),
          listBox: rect("ul"),
          cardBox: element.getBoundingClientRect().toJSON(),
        };
      });

      expect(listBox).not.toBeNull();
      expect(priceBox).not.toBeNull();
      expect(ctaBox).not.toBeNull();
      expect(cardBox).not.toBeNull();

      // Inside a card the order is always features, then price, then action.
      expect(priceBox.y).toBeGreaterThanOrEqual(listBox.y + listBox.height - 1);
      expect(ctaBox.y).toBeGreaterThanOrEqual(priceBox.y + priceBox.height - 1);
      expect(ctaBox.y + ctaBox.height).toBeLessThanOrEqual(cardBox.y + cardBox.height + 1);
      expect(cardBox.x).toBeGreaterThanOrEqual(-1);
      expect(cardBox.x + cardBox.width).toBeLessThanOrEqual(viewport.width + 1);

      boxes.push(cardBox);
    }

    if (viewport.width <= 760) {
      // Stacked: the second card starts below the first.
      expect(boxes[1].y).toBeGreaterThanOrEqual(boxes[0].y + boxes[0].height - 1);
    } else {
      // Side by side: no horizontal overlap between the two columns.
      expect(boxes[1].x).toBeGreaterThanOrEqual(boxes[0].x + boxes[0].width - 1);
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
  const proofBadge = proofGrid.locator(".pen-proof-section__badge");
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

  const gridBox = await proofGrid.boundingBox();
  const badgeBox = await proofBadge.boundingBox();

  expect(gridBox).not.toBeNull();
  expect(badgeBox).not.toBeNull();
  expect(badgeBox.y).toBeGreaterThanOrEqual(gridBox.y);
});

test("front page keeps verified proof photos compact on desktop", async ({ page }) => {
  await page.setViewportSize({ width: 2934, height: 900 });
  await page.goto("/");

  const hasProofSection = await expectHomeProofContract(page);

  if (!hasProofSection) {
    return;
  }

  const photos = page.locator(".pro-home-proof-student .pen-proof-section__image");
  const proofGrid = page.locator(".pen-proof-section__students");
  const photoCount = await photos.count();
  const studentCount = await page.locator(".pro-home-proof-student").count();
  const cardPositions = await page.locator(".pro-home-proof-student").evaluateAll((cards) =>
    cards.map((card) => Math.round(card.getBoundingClientRect().top)),
  );

  expect(photoCount).toBe(studentCount);
  expect(photoCount).toBeLessThanOrEqual(6);
  expect(new Set(cardPositions).size).toBe(1);
  await expect(proofGrid).toHaveCSS("overflow-x", "visible");

  for (const photo of await photos.all()) {
    const box = await photo.boundingBox();
    const cardBox = await photo.locator("xpath=ancestor::figure[1]").boundingBox();

    expect(box).not.toBeNull();
    expect(cardBox).not.toBeNull();
    expect(box.height / box.width).toBeGreaterThanOrEqual(1.32);
    expect(box.height / box.width).toBeLessThanOrEqual(1.34);
    expect(box.width).toBeLessThanOrEqual(cardBox.width);
    expect(cardBox.height).toBeLessThanOrEqual(box.height + 180);
  }
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

  const scrollPositionBeforeKeyboard = await tabs.evaluate((element) => element.scrollLeft);

  await previousButton.focus();
  await previousButton.press("Enter");
  await expect.poll(() => tabs.evaluate((element) => element.scrollLeft)).toBeLessThan(
    scrollPositionBeforeKeyboard,
  );

  await page.setViewportSize({ width: 1024, height: 844 });
  await expect(controls).toBeHidden();
});

test("front page mobile priority actions keep stable 44px touch targets", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.emulateMedia({ reducedMotion: "reduce" });
  await page.goto("/");
  await page.evaluate(() => document.fonts.ready);

  const footer = page.locator(".pen-site-footer");

  await footer.evaluate((element) => {
    const links = element.querySelector(".pen-site-footer__links");

    if (links && !links.querySelector(".pen-site-footer__column a")) {
      links.insertAdjacentHTML(
        "beforeend",
        '<section class="pen-site-footer__column"><ul class="pen-site-footer__menu"><li><a href="#e2e-footer-class">Turma E2E</a></li></ul></section>',
      );
    }

    if (!element.querySelector(".pen-site-footer__legal-menu a")) {
      element.querySelector(".pen-site-footer__meta")?.insertAdjacentHTML(
        "beforebegin",
        '<nav class="pen-site-footer__legal"><ul class="pen-site-footer__legal-menu"><li><a href="#e2e-footer-legal">Termos E2E</a></li></ul></nav>',
      );
    }

    if (!element.querySelector(".pen-site-footer__social a")) {
      element.querySelector(".pen-site-footer__top-widgets")?.insertAdjacentHTML(
        "beforeend",
        '<div class="pen-site-footer__social"><a href="#e2e-footer-social" aria-label="Instagram da Proenem"><span aria-hidden="true">◎</span></a></div>',
      );
    }
  });

  const targetGroups = [
    page.locator(".pro-home-pillars-control button"),
    page.locator(".pro-home-platform-tabs__controls button"),
    footer.locator(
      ".pen-site-footer__column a, .pen-site-footer__legal-menu a, .pen-site-footer__social a, .pen-site-footer__copyright",
    ),
  ];
  const testimonialTargets = page.locator(
    ".pro-home-testimonials__controls button, .pro-home-testimonials__controls a",
  );

  if ((await testimonialTargets.count()) > 0) {
    targetGroups.push(testimonialTargets);
  }

  for (const targets of targetGroups) {
    expectTargetsNotToOverlap(await expectMinimumTouchTargets(targets));
  }

  const focusTargets = [
    page.locator("[data-pro-home-pillars-next]"),
    page.locator(".pro-home-platform-tabs__controls button:not(:disabled)").first(),
    footer.locator(".pen-site-footer__column a").first(),
  ];

  if ((await testimonialTargets.count()) > 0) {
    focusTargets.push(testimonialTargets.first());
  }

  for (const target of focusTargets) {
    await target.scrollIntoViewIfNeeded();

    const beforeFocus = await target.boundingBox();

    await target.focus();
    await expect(target).toBeFocused();
    await expect(target).toHaveCSS("outline-style", "solid");

    const afterFocus = await target.boundingBox();

    expect(beforeFocus).not.toBeNull();
    expect(afterFocus).not.toBeNull();
    expect(afterFocus.width).toBe(beforeFocus.width);
    expect(afterFocus.height).toBe(beforeFocus.height);
    expect(Math.abs(afterFocus.x - beforeFocus.x)).toBeLessThanOrEqual(0.5);
    expect(Math.abs(afterFocus.y - beforeFocus.y)).toBeLessThanOrEqual(0.5);
  }

  const accessibilityScan = await new AxeBuilder({ page })
    .include(
      ".pen-pillars-section, .pen-platform-showcase, .pro-home-testimonials, .pen-site-footer",
    )
    .analyze();

  expect(
    accessibilityScan.violations.map(({ id, nodes }) => ({
      id,
      targets: nodes.map((node) => node.target),
    })),
  ).toEqual([]);
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

test("approved-students final CTA fits narrow mobile viewports", async ({ page }) => {
  await page.goto("/");

  const main = page.locator("main").first();

  await main.evaluate((element) => {
    element.insertAdjacentHTML(
      "beforeend",
      `
        <section class="pro-testimonials-next" data-e2e-testimonials-next>
          <div class="pro-testimonials-next__inner">
            <div class="pro-testimonials-next__copy">
              <span class="pro-testimonials-next__eyebrow">Seu próximo capítulo</span>
              <h2>Agora é a sua vez de construir uma história para este mural.</h2>
              <p>Você não precisa ter todo o caminho resolvido. Precisa de um plano e coragem para dar o próximo passo.</p>
              <div class="pro-testimonials-next__actions">
                <a class="pen-button pen-button--secondary pen-button--lg" href="#planos">
                  Quero começar minha preparação
                  <span aria-hidden="true">→</span>
                </a>
                <a class="pro-testimonials-next__back" href="#">Rever as histórias</a>
              </div>
            </div>
            <div class="pro-testimonials-next__mural" aria-hidden="true">
              <span>Próxima aprovação</span>
              <strong>Seu nome</strong>
              <p>pode estar aqui.</p>
              <small>✦ 2026 ✦</small>
            </div>
          </div>
        </section>
      `,
    );
  });

  for (const viewport of [
    { height: 700, width: 320 },
    { height: 844, width: 390 },
  ]) {
    await page.setViewportSize(viewport);

    const section = page.locator("[data-e2e-testimonials-next]");
    const copy = await section.locator(".pro-testimonials-next__copy").boundingBox();
    const button = await section.locator(".pen-button").boundingBox();

    expect(copy).not.toBeNull();
    expect(button).not.toBeNull();
    expect(copy.x).toBeGreaterThanOrEqual(0);
    expect(copy.x + copy.width).toBeLessThanOrEqual(viewport.width);
    expect(button.x).toBeGreaterThanOrEqual(0);
    expect(button.x + button.width).toBeLessThanOrEqual(viewport.width);
    expect(button.height).toBeGreaterThanOrEqual(44);
  }
});

test("approved-students mobile links keep 44px touch targets", async ({ page }) => {
  await page.goto("/");

  const main = page.locator("main").first();

  await main.evaluate((element) => {
    element.insertAdjacentHTML(
      "beforeend",
      `
        <section data-e2e-testimonial-touch-targets>
          <div class="pro-testimonial-single__hero">
            <a class="pen-section-pill" href="#mural">Mural de aprovados</a>
          </div>
          <article class="pro-testimonial-card">
            <a class="testimonials-card__action" href="#historia">Conheça esta história</a>
          </article>
          <div class="pro-testimonial-single__share-links">
            <a href="#whatsapp">WhatsApp</a>
            <a href="#facebook">Facebook</a>
            <button type="button">Copiar link</button>
          </div>
          <a class="pro-testimonial-single__all-link" href="#outros">Ver outros aprovados</a>
          <div class="pro-testimonial-single__related-header">
            <a href="#todos">Ver todo o mural</a>
          </div>
          <div class="pro-testimonials-next__actions">
            <a class="pro-testimonials-next__back" href="#rever">Rever as histórias</a>
          </div>
        </section>
      `,
    );
  });

  const fixture = page.locator("[data-e2e-testimonial-touch-targets]");
  const targets = fixture.locator("a, button");

  for (const viewport of [
    { height: 700, width: 320 },
    { height: 844, width: 390 },
  ]) {
    await page.setViewportSize(viewport);
    expectTargetsNotToOverlap(await expectMinimumTouchTargets(targets));
    expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBe(viewport.width);
  }
});

test("site footer keeps every navigation column inside tablet viewports", async ({ page }) => {
  await page.goto("/");

  const footer = page.locator(".pen-site-footer");
  const links = footer.locator(".pen-site-footer__links");

  await expect(links).toHaveCount(1);
  await links.evaluate((element) => {
    element.innerHTML = `
      <section class="pen-site-footer__column pen-site-footer__column--footer-subjects">
        <h3 class="pen-site-footer__column-title">Matérias lecionadas</h3>
        <ul class="pen-site-footer__menu"><li><a href="#materias">Matérias</a></li></ul>
      </section>
      <section class="pen-site-footer__column pen-site-footer__column--footer-answer-keys">
        <h3 class="pen-site-footer__column-title">Gabaritos</h3>
        <ul class="pen-site-footer__menu"><li><a href="#gabaritos">Gabaritos</a></li></ul>
      </section>
      <section class="pen-site-footer__column pen-site-footer__column--footer-tools">
        <h3 class="pen-site-footer__column-title">Ferramentas</h3>
        <ul class="pen-site-footer__menu"><li><a href="#ferramentas">Ferramentas</a></li></ul>
      </section>
    `;
  });

  for (const viewport of [
    { height: 1024, width: 768 },
    { height: 1180, width: 980 },
  ]) {
    await page.setViewportSize(viewport);

    const subjects = footer.locator(".pen-site-footer__column--footer-subjects");
    const tools = footer.locator(".pen-site-footer__column--footer-tools");
    const [linksBox, toolsBox] = await Promise.all([links.boundingBox(), tools.boundingBox()]);

    expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBe(viewport.width);
    expect(linksBox).not.toBeNull();
    expect(toolsBox).not.toBeNull();
    expect(linksBox.x).toBeGreaterThanOrEqual(0);
    expect(linksBox.x + linksBox.width).toBeLessThanOrEqual(viewport.width);
    expect(toolsBox.x + toolsBox.width).toBeLessThanOrEqual(viewport.width);
    await expect(subjects).toHaveCSS("grid-column-start", "1");
    await expect(subjects).toHaveCSS("grid-column-end", "-1");
  }
});

test("approved-students filters expose vertical overflow on mobile", async ({ page }) => {
  await page.setViewportSize({ height: 844, width: 390 });
  await page.goto("/");

  await page.locator("main").first().evaluate((element) => {
    const options = Array.from({ length: 7 }, (_, index) => `
      <label class="pro-materials-filter__option">
        <input type="checkbox" name="category[]" value="category-${index + 1}">
        <span>Conquista de referência ${index + 1}</span>
        <small>${index + 1}</small>
      </label>
    `).join("");

    element.insertAdjacentHTML(
      "beforeend",
      `
        <form class="pro-materials-filter pro-testimonials-filter" data-e2e-testimonials-filter>
          <div class="pro-materials-filter__header">
            <strong>Filtre por tipo de conquista</strong>
          </div>
          <div class="pro-materials-filter__options">${options}</div>
          <button class="pen-button pen-button--primary pen-button--sm pro-materials-filter__submit" type="button">
            Ver histórias
          </button>
        </form>
      `,
    );
  });

  const options = page.locator("[data-e2e-testimonials-filter] .pro-materials-filter__options");
  const metrics = await options.evaluate((element) => {
    const bounds = element.getBoundingClientRect();
    const items = Array.from(element.children).map((item) => {
      const itemBounds = item.getBoundingClientRect();

      return {
        bottom: itemBounds.bottom,
        top: itemBounds.top,
      };
    });

    return {
      clientHeight: element.clientHeight,
      clientWidth: element.clientWidth,
      fourthItem: items[3],
      optionsBottom: bounds.bottom,
      overflowX: getComputedStyle(element).overflowX,
      overflowY: getComputedStyle(element).overflowY,
      scrollHeight: element.scrollHeight,
      scrollWidth: element.scrollWidth,
      thirdItem: items[2],
    };
  });

  expect(metrics.overflowX).toBe("hidden");
  expect(metrics.overflowY).toBe("auto");
  expect(metrics.scrollHeight).toBeGreaterThan(metrics.clientHeight);
  expect(metrics.scrollWidth).toBe(metrics.clientWidth);
  expect(metrics.thirdItem.bottom).toBeLessThanOrEqual(metrics.optionsBottom);
  expect(metrics.fourthItem.top).toBeLessThan(metrics.optionsBottom);
  expect(metrics.fourthItem.bottom).toBeGreaterThan(metrics.optionsBottom);
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
