# Klaviyo Cookie Consent Bridge

[![CI](https://github.com/pixelperfectat/magento2-klaviyo-hyva-cookie-consent/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/pixelperfectat/magento2-klaviyo-hyva-cookie-consent/actions/workflows/ci.yml)
[![Packagist version](https://img.shields.io/packagist/v/pixelperfectat/magento2-klaviyo-hyva-cookie-consent)](https://packagist.org/packages/pixelperfectat/magento2-klaviyo-hyva-cookie-consent)
[![PHP](https://img.shields.io/packagist/dependency-v/pixelperfectat/magento2-klaviyo-hyva-cookie-consent/php)](composer.json)
[![License: MIT](https://img.shields.io/packagist/l/pixelperfectat/magento2-klaviyo-hyva-cookie-consent)](LICENSE)

Gates Klaviyo (`klaviyo/magento2-extension`) behind the **marketing** category of
[`pixelperfectat/module-hyva-cookie-consent`](https://github.com/pixelperfectat/module-hyva-cookie-consent)
on Hyvä stores that use
[`pixelperfectat/magento2-klaviyo-hyva-compat`](https://github.com/pixelperfectat/magento2-klaviyo-hyva-compat).

While cookie consent is enabled, `klaviyo.js` doesn't load, the `__kla_id` cookie isn't set and no
customer is identified until the visitor grants marketing consent.

## Requirements

| Package | Version |
|---|---|
| PHP | 8.2, 8.3, 8.4 or 8.5 |
| Magento Open Source / Adobe Commerce | 2.4 with `magento/framework` 103.x |
| `hyva-themes/magento2-theme-module` | 1.3.11 or later |
| `pixelperfectat/module-hyva-cookie-consent` | 0.3.x or 0.4.x |
| `pixelperfectat/magento2-klaviyo-hyva-compat` | 0.3.x |
| `klaviyo/magento2-extension` | 4.4.x or 5.x |

## How it works

1. Registers Klaviyo as a `marketing` service of the consent module, with the `__kla_id` cookie, so
   it shows up in the consent banner.
2. Defines `window.klaviyoConsentGranted()` in the page head (block `klaviyo.hyva.cookie.consent.check`).
   It returns `true` when `window.cookie_consent_groups.marketing` is `true`, or, on pages without
   Alpine, when the saved consent cookie of the consent module grants marketing for the configured
   consent version. The compat module's product view and cart templates use the same function.
3. Removes Klaviyo's own loader blocks (`klaviyo.initialize` on every page and
   `klaviyo_reclaim.checkout.initialize` on the checkout) and loads `klaviyo.js` itself once consent
   is given, either on page load or on the consent module's `cookie-consent-updated` event.
4. Identifies a logged-in customer (`klaviyo.identify()` with email, first and last name) after
   consent, once the SDK is ready.
5. On a Luma-rendered checkout (e.g. `hyva-themes/magento2-luma-checkout`), a RequireJS mixin makes
   Klaviyo's checkout email step wait for the same consent check before it identifies the visitor
   and saves the typed email on the quote.

## Installation

```bash
composer require pixelperfectat/magento2-klaviyo-hyva-cookie-consent
bin/magento module:enable Pixelperfect_HyvaCookieConsent PixelPerfect_KlaviyoHyvaCompat PixelPerfect_KlaviyoHyvaCookieConsent
bin/magento setup:upgrade
```

Composer pulls in the consent module, the compat module and Klaviyo.

## Configuration

| Setting | Path | Default | Effect |
|---|---|---|---|
| **Stores > Configuration > General > Web > Cookie Consent (Hyva) > General Settings > Enable Cookie Consent** | `web/hyva_cookie_consent/general/enabled` | No (set by the consent module) | Turns consent gating on. See the table below. |
| **Stores > Configuration > General > Cookie Consent Services > Tracking Services > Klaviyo > Enable** | `hyva_cookie_consent/services/klaviyo/enabled` | Yes | With cookie consent enabled: Yes loads Klaviyo after marketing consent, No keeps Klaviyo off. |

The Klaviyo API keys and Klaviyo's own enable switch stay in **Stores > Configuration > Klaviyo > General**.

| Cookie consent | Klaviyo service | Result |
|---|---|---|
| Enabled | Yes | Klaviyo loads after marketing consent (on every page, including checkout) |
| Enabled | No | Klaviyo stays off, like any other disabled consent service |
| Disabled | any | The bridge stays out of the way: Klaviyo and the compat module behave as without the bridge |

With cookie consent disabled, the compat module's own check applies: Klaviyo loads when Magento's
cookie restriction mode is off, or after the visitor has accepted cookies when it is on. Compat 0.3
is required because older compat versions treated a missing cookie as consent.

From cookie consent 0.4 on, a disabled consent module changes nothing in Magento, so Magento's own
cookie notice and restriction setting apply. Cookie consent 0.3 forced cookie restriction mode on and
removed Magento's cookie notice even while disabled; in that combination nobody can accept cookies, so
Klaviyo stays off until you enable cookie consent, set cookie restriction mode to No, or upgrade the
consent module.

## Consent contract

The bridge relies on these parts of the consent module: `window.cookie_consent_groups`, the
`cookie-consent-updated` window event, the config paths above, the service XML
(`cookie_consent.xml`), and, for pages without Alpine, the `hyva_cookie_consent` cookie
(`{"version": <int>, "categories": {"marketing": <bool>}}`). Towards the compat module it provides
`window.klaviyoConsentGranted()`, defined before any Klaviyo template runs.

## Known limitations

- **Withdrawing consent** deletes the `__kla_id` cookie (the consent module does this), but a
  `klaviyo.js` that is already running keeps running until the next page load and may set it again.
  Klaviyo's browser storage keys aren't declared or cleared.
- **Server-side events:** Klaviyo's `SalesQuoteSaveAfter` observer sends "Added To Cart" server-side
  when the `__kla_id` cookie exists. It follows browser consent only through that cookie. Newsletter
  sign-ups and Klaviyo's checkout marketing opt-ins are separate consents and are not gated.
- **Customer identify** listens for the first `cookie-consent-updated` event of a page only. If the
  visitor saves without marketing and grants it later on the same page, identification happens on the
  next page.
- On a Luma-rendered checkout the consent banner doesn't work (it needs Alpine), so consent given on an
  earlier Hyvä page applies there through the saved cookie. Logged-in customers aren't identified on
  that page; they already are on the Hyvä pages.
- Hyvä Checkout hasn't been tested. The loader removal doesn't depend on the checkout's layout
  handles, and Klaviyo's Knockout email step doesn't run there.

## Uninstall

```bash
bin/magento module:disable PixelPerfect_KlaviyoHyvaCookieConsent
composer remove pixelperfectat/magento2-klaviyo-hyva-cookie-consent
bin/magento setup:upgrade
```

Then flush the cache. Klaviyo's own loader comes back. The setting
`hyva_cookie_consent/services/klaviyo/enabled` stays in `core_config_data` if it was saved in the admin.

## Development

The checks run in Docker with the repository mounted at `/app`:

```bash
composer install
composer check          # validate, lint, phpcs, phpstan, phpunit, infection
composer ci:normalize   # composer.json normalisation (dry run)
```

The PHP tests cover `ConsentGate`, `KlaviyoConfig`, the loader-removal observer and the config default.
The JavaScript is covered by the Playwright suite in `tests/playwright`. It needs a running store with
Hyvä, the consent module, Klaviyo and Luma sample data, and it isn't part of CI:

```bash
cd tests/playwright
npm ci
npx playwright install chromium
MAGENTO_BASE_URL=https://shop.example.test npm test
```

Optional variable: `TEST_PRODUCT_URL`.

## License

[MIT](LICENSE)
