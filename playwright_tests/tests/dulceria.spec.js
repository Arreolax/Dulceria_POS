import { test, expect } from '@playwright/test';

test('test Index', async ({ page }) => {
  await page.goto('http://localhost/Dulceria/views/index.php');
  await expect(page.getByRole('link', { name: 'Inicio' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Productos' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Crear Cuenta' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Iniciar sesión' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Bienvenido a Sugar Rush' })).toBeVisible();
});

test('test Login Admin Correcto', async ({ page }) => {
  await page.goto('http://localhost/Dulceria/views/index.php');
  await page.getByRole('link', { name: 'Iniciar sesión' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).fill('admin@dulceria.com');
  await page.getByRole('textbox', { name: '••••••••' }).click();
  await page.getByRole('textbox', { name: '••••••••' }).fill('admin');
  await page.getByRole('button', { name: 'Ingresar' }).click();

  await expect(page.locator('#nav').getByText('Rol: admin')).toBeVisible();
});

test('test Login Seller Correcto', async ({ page }) => {
  await page.goto('http://localhost/Dulceria/views/index.php');
  await page.getByRole('link', { name: 'Iniciar sesión' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).fill('juan.perez@dulceria.com');
  await page.getByRole('textbox', { name: '••••••••' }).click();
  await page.getByRole('textbox', { name: '••••••••' }).fill('vendedor');
  await page.getByRole('button', { name: 'Ingresar' }).click();

  await expect(page.locator('#nav').getByText('Rol: vendedor')).toBeVisible();
});

test('test Login User Correcto', async ({ page }) => {
  await page.goto('http://localhost/Dulceria/views/index.php');
  await page.getByRole('link', { name: 'Iniciar sesión' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).fill('maria.lopez@dulceria.com');
  await page.getByRole('textbox', { name: '••••••••' }).click();
  await page.getByRole('textbox', { name: '••••••••' }).fill('cliente');
  await page.getByRole('button', { name: 'Ingresar' }).click();

  await expect(page.locator('#nav').getByText('Rol: cliente')).toBeVisible();
});

test('test Login Incorrecto', async ({ page }) => {
  await page.goto('http://localhost/Dulceria/views/index.php');
  await page.getByRole('link', { name: 'Iniciar sesión' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).click();
  await page.getByRole('textbox', { name: 'correo@ejemplo.com' }).fill('admin@dulceria.com');
  await page.getByRole('textbox', { name: '••••••••' }).click();
  await page.getByRole('textbox', { name: '••••••••' }).fill('admi');
  await page.getByRole('textbox', { name: '••••••••' }).press('CapsLock');
  await page.getByRole('textbox', { name: '••••••••' }).fill('admiN');
  await page.getByRole('textbox', { name: '••••••••' }).press('CapsLock');
  page.once('dialog', dialog => {
    console.log(`Dialog message: ${dialog.message()}`);
    dialog.dismiss().catch(() => { });
  });
  await page.getByRole('button', { name: 'Ingresar' }).click();
  await expect(page.getByRole('heading', { name: 'Iniciar Sesión' })).toBeVisible();

});

test('test Products View', async ({ page }) => {
  await page.goto('http://localhost/Dulceria/views/index.php');
  await page.getByRole('link', { name: 'Productos' }).click();
  await expect(page.locator('#header').getByText('Productos')).toBeVisible();
  await expect(page.locator('#main')).toBeVisible();
});