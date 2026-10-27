<?php

declare(strict_types=1);

namespace PixelPerfect\KlaviyoHyvaCookieConsent\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Reads the cookie consent settings that decide whether and how Klaviyo is gated.
 *
 * Only configuration paths of pixelperfectat/module-hyva-cookie-consent are read, no classes.
 * Implements ArgumentInterface so the templates can receive it as a block argument.
 */
class ConsentGate implements ArgumentInterface
{
    private const XML_PATH_SERVICE_ENABLED = 'hyva_cookie_consent/services/klaviyo/enabled';
    private const XML_PATH_CONSENT_VERSION = 'web/hyva_cookie_consent/general/consent_version';
    private const DEFAULT_CONSENT_VERSION = 1;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Whether the Klaviyo service is switched on in the cookie consent settings.
     */
    public function isKlaviyoServiceEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SERVICE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Consent version a saved consent cookie must carry to count, as the consent module applies it.
     */
    public function getConsentVersion(): int
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_CONSENT_VERSION, ScopeInterface::SCOPE_STORE);

        return (int) ($value ?: self::DEFAULT_CONSENT_VERSION);
    }
}
