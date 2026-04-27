import { test, expect } from '@playwright/test';

test.describe('Klaviyo activates on marketing consent', () => {
    test('loads klaviyo.js after granting marketing consent', async ({ page }) => {
        // Clear cookies to start without consent
        await page.context().clearCookies();

        await page.goto('/');
        await page.waitForTimeout(2000);

        // Verify Klaviyo not loaded yet
        const beforeConsent = await page.evaluate(() => {
            return typeof window.klaviyo?.isIdentified === 'function';
        });
        expect(beforeConsent).toBe(false);

        // Listen for Klaviyo CDN request
        const klaviyoLoadPromise = page.waitForRequest(
            request => request.url().includes('static.klaviyo.com'),
            { timeout: 15000 }
        );

        // Grant marketing consent by accepting all cookies
        const acceptButton = page.locator('[data-action="accept-all"], button:has-text("Accept All")');
        const isVisible = await acceptButton.isVisible({ timeout: 5000 }).catch(() => false);

        if (!isVisible) {
            test.skip(true, 'Cookie consent banner not visible — consent may already be granted or module not active');
            return;
        }

        await acceptButton.click();

        // Wait for Klaviyo CDN to load
        await klaviyoLoadPromise;

        // Give SDK time to initialize
        await page.waitForTimeout(3000);

        // Verify Klaviyo SDK is now loaded
        const afterConsent = await page.evaluate(() => {
            return typeof window.klaviyo?.isIdentified === 'function';
        });
        expect(afterConsent).toBe(true);
    });
});
