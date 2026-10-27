import { test, expect } from '@playwright/test';
import { REJECT_ALL, addProductToCart, expectNoKlaviyoCookie, recordKlaviyoTraffic } from '../fixtures/klaviyo';

const productUrl = process.env.TEST_PRODUCT_URL || '/fusion-backpack.html';

test.describe('Klaviyo on the checkout page without marketing consent', () => {
    test('checkout loads nothing from Klaviyo and posts no email', async ({ page, context }) => {
        await context.clearCookies();
        await page.goto('/');
        await page.locator(REJECT_ALL).click();
        await addProductToCart(page, productUrl);

        const traffic = recordKlaviyoTraffic(page);
        await page.goto('/checkout/');
        await page.waitForLoadState('networkidle');

        // Luma-rendered checkout: typing the guest email triggers Klaviyo's email step.
        const emailField = page.locator('#customer-email');
        if (await emailField.count()) {
            await emailField.fill('jane.doe@example.com');
            await emailField.blur();
            await page.waitForLoadState('networkidle');
        }

        expect(traffic.klaviyo).toHaveLength(0);
        expect(traffic.emailPosts).toHaveLength(0);
        await expectNoKlaviyoCookie(context);
    });
});
