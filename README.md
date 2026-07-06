# Klaviyo Cookie Consent Bridge

Bridges [Klaviyo Hyvä compatibility](https://github.com/pixelperfectat/magento2-klaviyo-hyva-compat) with [Pixelperfect Cookie Consent](https://github.com/pixelperfectat/module-hyva-cookie-consent).

**Requires two packages:** this bridge plus its base module `pixelperfectat/module-hyva-cookie-consent`.

When installed, Klaviyo tracking is gated behind the **marketing** consent category. Without marketing consent, the Klaviyo CDN script does not load and no tracking calls are made.

## How It Works

1. Registers Klaviyo as a marketing service in the cookie consent system
2. Overrides `window.klaviyoConsentGranted()` to check `cookie_consent_groups.marketing`
3. Removes the base Klaviyo CDN script block and replaces it with a consent-gated version
4. When user grants marketing consent, Klaviyo activates automatically

## Requirements

- PHP 8.3+
- Magento 2.4.x
- pixelperfectat/module-hyva-cookie-consent ^0.3
- pixelperfectat/magento2-klaviyo-hyva-compat ^0.1
- klaviyo/magento2-extension ^4.0

## Installation

```bash
composer require pixelperfectat/module-hyva-cookie-consent pixelperfectat/magento2-klaviyo-hyva-cookie-consent
bin/magento module:enable PixelPerfect_KlaviyoHyvaCookieConsent
bin/magento setup:upgrade
```

## Configuration

No configuration required. Once enabled, Klaviyo tracking is automatically gated behind the marketing consent category.

## License
[MIT](LICENSE)
