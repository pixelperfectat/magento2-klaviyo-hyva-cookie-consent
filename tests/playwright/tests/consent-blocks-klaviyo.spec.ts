import { test, expect } from '@playwright/test';
import { REJECT_ALL, expectNoKlaviyoCookie, recordKlaviyoTraffic } from '../fixtures/klaviyo';

test.describe('Klaviyo blocked without marketing consent', () => {
    test('loads nothing from Klaviyo before a choice and after "Reject All"', async ({ page, context }) => {
        await context.clearCookies();
        const traffic = recordKlaviyoTraffic(page);

        await page.goto('/');
        await page.waitForLoadState('networkidle');
        expect(traffic.klaviyo).toHaveLength(0);
        await expectNoKlaviyoCookie(context);

        await page.locator(REJECT_ALL).click();
        await page.waitForLoadState('networkidle');
        await page.reload();
        await page.waitForLoadState('networkidle');

        expect(traffic.klaviyo).toHaveLength(0);
        await expectNoKlaviyoCookie(context);
        const sdkLoaded = await page.evaluate(() => typeof (window as any).klaviyo?.isIdentified === 'function');
        expect(sdkLoaded).toBe(false);
    });
});
