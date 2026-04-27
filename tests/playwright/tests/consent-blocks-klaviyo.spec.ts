import { test, expect } from '@playwright/test';

test.describe('Klaviyo blocked without marketing consent', () => {
    test('does not load klaviyo.js when marketing consent denied', async ({ page }) => {
        const klaviyoRequests: string[] = [];

        page.on('request', request => {
            if (request.url().includes('static.klaviyo.com')) {
                klaviyoRequests.push(request.url());
            }
        });

        // Clear any existing consent cookie to start fresh
        await page.context().clearCookies();

        await page.goto('/');
        await page.waitForTimeout(3000);

        // Verify no Klaviyo CDN request was made
        expect(klaviyoRequests).toHaveLength(0);

        // Verify window.klaviyo is not the SDK (just an array or undefined)
        const klaviyoType = await page.evaluate(() => {
            if (typeof window.klaviyo === 'undefined') return 'undefined';
            if (Array.isArray(window.klaviyo)) return 'array';
            if (typeof window.klaviyo?.isIdentified === 'function') return 'sdk';
            return 'other';
        });

        expect(klaviyoType).not.toBe('sdk');
    });
});
