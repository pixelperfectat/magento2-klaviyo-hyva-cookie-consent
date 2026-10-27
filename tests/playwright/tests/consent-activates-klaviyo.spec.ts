import { test, expect } from '@playwright/test';
import { ACCEPT_ALL, recordKlaviyoTraffic } from '../fixtures/klaviyo';

test.describe('Klaviyo activates on marketing consent', () => {
    test('loads klaviyo.js after "Accept All" and on the next page', async ({ page, context }) => {
        await context.clearCookies();
        const traffic = recordKlaviyoTraffic(page);

        await page.goto('/');
        // A missing banner means the consent module is off or misconfigured: fail, don't skip.
        await expect(page.locator(ACCEPT_ALL)).toBeVisible({ timeout: 5000 });
        expect(traffic.klaviyo).toHaveLength(0);

        const sdkRequest = page.waitForRequest(r => r.url().includes('static.klaviyo.com/onsite/js/'));
        await page.locator(ACCEPT_ALL).click();
        await sdkRequest;

        await page.waitForFunction(() => typeof (window as any).klaviyo?.isIdentified === 'function', null, {
            timeout: 15000,
        });

        const nextPageRequest = page.waitForRequest(r => r.url().includes('static.klaviyo.com/onsite/js/'));
        await page.reload();
        await nextPageRequest;
    });
});
