# Klaviyo Cookie Consent Bridge

Bridges [Klaviyo Hyva compatibility](../magento2-klaviyo-hyva-compat) with [Pixelperfect Cookie Consent](https://github.com/pixelperfectat/module-hyva-cookie-consent).

When installed, Klaviyo tracking is gated behind the **marketing** consent category. Without marketing consent, the Klaviyo CDN script does not load and no tracking calls are made.

## Requirements

- pixelperfectat/module-hyva-cookie-consent
- pixelperfectat/magento2-klaviyo-hyva-compat
- klaviyo/magento2-extension ^4.0

## How It Works

1. Registers Klaviyo as a marketing service in the cookie consent system
2. Overrides `window.klaviyoConsentGranted()` to check `cookie_consent_groups.marketing`
3. Removes the base Klaviyo CDN script block and replaces it with a consent-gated version
4. When user grants marketing consent, Klaviyo activates automatically
