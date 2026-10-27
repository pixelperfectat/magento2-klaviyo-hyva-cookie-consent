import { BrowserContext, Page, expect } from '@playwright/test';

export const ACCEPT_ALL = '[data-testid="cookie-accept-all"]';
export const REJECT_ALL = '[data-testid="cookie-reject-all"]';

/** Records every request to a Klaviyo host and every POST to Klaviyo's checkout email endpoint. */
export function recordKlaviyoTraffic(page: Page): { klaviyo: string[]; emailPosts: string[] } {
    const traffic = { klaviyo: [] as string[], emailPosts: [] as string[] };
    page.on('request', request => {
        const url = request.url();
        if (/(^|\.)klaviyo\.com\//.test(new URL(url).host + '/')) {
            traffic.klaviyo.push(url);
        }
        if (request.method() === 'POST' && url.includes('/reclaim/checkout/email')) {
            traffic.emailPosts.push(url);
        }
    });
    return traffic;
}

export async function expectNoKlaviyoCookie(context: BrowserContext): Promise<void> {
    const cookies = await context.cookies();
    expect(cookies.map(c => c.name)).not.toContain('__kla_id');
}

export async function addProductToCart(page: Page, productUrl: string): Promise<void> {
    await page.goto(productUrl);
    await page.click('#product-addtocart-button');
    await page.waitForSelector('.message-success, [data-ui-id="message-success"]', { timeout: 10000 });
}
