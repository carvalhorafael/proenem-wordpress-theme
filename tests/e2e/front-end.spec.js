import { expect, test } from "@playwright/test";

test("front page renders the Proenem home", async ({ page }) => {
  await page.goto("/");

  await expect(page.locator("body")).toBeVisible();
  await expect(page.locator(".pen-navbar")).toBeVisible();
  await expect(page.getByRole("heading", { level: 1, name: /sua aprovação/i })).toBeVisible();
  await expect(page.locator(".pen-pricing-section")).toBeVisible();
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
