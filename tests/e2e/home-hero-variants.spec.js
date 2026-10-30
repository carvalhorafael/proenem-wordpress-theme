import AxeBuilder from "@axe-core/playwright";
import { expect, test } from "@playwright/test";

const CHECKOUT_HOST = "pay.hotmart.com";

// Addressed by query string, not by pretty permalink: CI runs WordPress with
// the default structure, so /e2e-home-variant-oferta/ resolves to a 404 there.
const VARIANTS = [
  {
    name: "oferta",
    path: "/?pagename=e2e-home-variant-oferta",
    heroSelector: ".pro-hero-offer",
    ctaSelector: ".pro-hero-offer__cta",
  },
  {
    name: "prova",
    path: "/?pagename=e2e-home-variant-prova",
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

    // #aprovados is deliberately not asserted: the proof section renders only
    // when the testimonials plugin has eligible records, so it is absent in a
    // clean install. The suite treats it as optional everywhere else too.

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

  test(`home variant ${variant.name} offers ENEM PRO and MED Performance`, async ({
    page,
  }) => {
    await page.goto(variant.path);

    const cards = page.locator(".pen-pricing-section .pen-plan-card");

    await expect(cards).toHaveCount(2);
    await expect(page.locator(".pen-plan-card.pro-home-plan-card--stack")).toHaveCount(2);
    await expect(page.locator(".pen-plan-card.pro-home-plan-card--accent")).toHaveCount(0);
    await expect(page.locator(".pen-plan-card.is-featured")).toHaveCount(1);
    await expect(page.locator(".pen-plan-card.pro-home-plan-card--split")).toHaveCount(0);

    const enemPro = cards.nth(0);
    await expect(enemPro).toHaveClass(/is-featured/);
    await expect(enemPro.getByRole("heading", { level: 3 })).toHaveText("ENEM PRO");
    await expect(enemPro.locator(".pro-home-plan-card__label")).toHaveText("Estude no seu ritmo");
    await expect(enemPro.locator(".pro-home-plan-card__price-amount")).toHaveText(/12x de R\$\s*32,89/);
    await expect(enemPro.locator(".pro-home-plan-card__price")).toContainText("ou R$ 394,68 à vista");
    await expect(enemPro.getByRole("link", { name: /quero o enem pro/i })).toHaveAttribute(
      "href",
      "https://pay.hotmart.com/T102416176R?off=5na5b8bl&checkoutMode=10",
    );

    const medPerformance = cards.nth(1);
    await expect(medPerformance.getByRole("heading", { level: 3 })).toHaveText("MED Performance");
    await expect(medPerformance.locator(".pro-home-plan-card__label")).toHaveText("Foco no resultado");
    await expect(medPerformance.locator(".pro-home-plan-card__price-amount")).toHaveText(/12x de R\$\s*59,88/);
    await expect(medPerformance.locator(".pro-home-plan-card__price")).toContainText("ou R$ 718,56 à vista");
    await expect(medPerformance.getByRole("link", { name: /quero a med performance/i })).toHaveAttribute(
      "href",
      "https://pay.hotmart.com/E108194423D?utm_source=site&utm_medium=home&utm_campaign=botao-home",
    );

    await expect(page.locator(".pro-home-plan-card__guarantee")).toHaveCount(2);
    await expect(page.locator(".pro-home-pricing__intro p")).toHaveText(
      /plataforma completa por 12 meses.*7 dias de garantia/i,
    );
  });

  test(`home variant ${variant.name} answers the ENEM PRO questions in the FAQ`, async ({
    page,
  }) => {
    await page.goto(variant.path);

    const questions = page.locator(".pen-faq-section .pen-faq-item summary");

    await expect(questions).toHaveText([
      "O que é o ENEM PRO?",
      "O que está incluído no ENEM PRO?",
      "Por quanto tempo tenho acesso?",
      "Posso entrar em qualquer época do ano?",
      "Quanto custa e como posso pagar?",
      "E se eu não gostar?",
    ]);
    await expect(page.locator(".pen-faq-section")).toContainText("12x de R$ 32,89 ou R$ 394,68 à vista");
    await expect(page.locator(".pen-faq-section")).not.toContainText("Turma Intensiva");
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
