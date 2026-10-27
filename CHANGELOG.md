# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Support PHP 8.2, 8.3 and 8.4 (`~8.2.0 || ~8.3.0 || ~8.4.0`); the code has no 8.3-only syntax.
- Allow `klaviyo/magento2-extension` 5.x (`^4.4 || ^5.0`).
- Allow `pixelperfectat/magento2-klaviyo-hyva-compat` 0.2 (`^0.1 || ^0.2`).
- Require `hyva-themes/magento2-theme-module ^1.3.11`, the first release with the `HyvaCsp` view model the template uses, and `magento/module-config`, whose source model the admin setting uses.
- `window.klaviyoConsentGranted()` is now defined in the page head (block `klaviyo.hyva.cookie.consent.check`), before any Klaviyo template runs.
- The Klaviyo service is now enabled by default (`hyva_cookie_consent/services/klaviyo/enabled = 1`), so Klaviyo works consent-gated right after installation instead of staying off until an admin switches the service on.

### Fixed
- Klaviyo could load before marketing consent was given.

## [0.2.0] - 2026-07-06

### Added
- MIT license file.
- PixelPerfect coding standard, PHPStan, PHPUnit and the GitHub CI workflow.
- Renovate configuration.

### Changed
- Require PHP `^8.3`, `magento/framework ^103.0`, `klaviyo/magento2-extension ^4.0`,
  `pixelperfectat/magento2-klaviyo-hyva-compat ^0.1` and `pixelperfectat/module-hyva-cookie-consent ^0.3`
  instead of open constraints.

### Fixed
- README: the base consent module is a required package, and the link to the compat module works.

## [0.1.0] - 2026-05-06

### Added
- Klaviyo registered as a `marketing` service of `pixelperfectat/module-hyva-cookie-consent`, with the
  `__kla_id` cookie declared.
- `window.klaviyoConsentGranted()` override that follows the marketing consent category.
- Consent-gated loading of `klaviyo.js`, replacing Klaviyo's unconditional loader block.
- Consent-gated customer identification after marketing consent.
- Admin toggle under the cookie consent module's Services section.

### Fixed
- Root element name in `cookie_consent.xml`.
- CSP-safe Alpine directive on the customer-identify component.

[Unreleased]: https://github.com/pixelperfectat/magento2-klaviyo-hyva-cookie-consent/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/pixelperfectat/magento2-klaviyo-hyva-cookie-consent/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/pixelperfectat/magento2-klaviyo-hyva-cookie-consent/releases/tag/v0.1.0
