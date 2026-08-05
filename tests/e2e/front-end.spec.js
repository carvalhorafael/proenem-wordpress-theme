import { expect, test } from "@playwright/test";

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
  await expect(page.locator(".pen-site-footer")).toBeVisible();
});

test("front page navbar starts closed on mobile", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
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
});
