// @ts-check
/* 
import { test, expect } from '@playwright/test';

test('has title', async ({ page }) => {
  await page.goto('https://playwright.dev/');

  // Expect a title "to contain" a substring.
  await expect(page).toHaveTitle(/Playwright/);
});

test('get started link', async ({ page }) => {
  await page.goto('https://playwright.dev/');

  // Click the get started link.
  await page.getByRole('link', { name: 'Get started' }).click();

  // Expects page to have a heading with the name of Installation.
  await expect(page.getByRole('heading', { name: 'Installation' })).toBeVisible();
});


test('find locators', async ({ page }) => {
  test.slow();
  await page.goto('https://playwright.dev/');

  // 1. Open search modal
  await page.getByRole('button', { name: 'Search' }).click();

  // 2. Type in the modal searchbox
  const searchInput = page.getByRole('searchbox', { name: 'Search' });
  await expect(searchInput).toBeVisible();
  await searchInput.fill('locators');

  // 3. Open exact Locators result from modal
  // const locatorsLink = page.locator('a[href="/docs/locators"]');
  const locatorsLink = page.getByRole('link', { name: 'Locators', exact: true });

  // await expect(locatorsLink.first()).toBeVisible();
  await expect(locatorsLink).toBeVisible();
  // await locatorsLink.first().click();
  await locatorsLink.click();

  // 4. Assertions for destination page
  await expect(page).toHaveURL(/.*locators/);
  // await expect(page.getByRole('heading', { name: 'Locators', exact: true })).toBeVisible();
  await expect(page.locator('h1', { hasText: 'Locators' })).toBeVisible();
});
*/