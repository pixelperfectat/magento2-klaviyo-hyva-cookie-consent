<?php

declare(strict_types=1);

namespace PixelPerfect\KlaviyoHyvaCookieConsent\ViewModel;

use Klaviyo\Reclaim\Helper\ScopeSetting;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Wraps Klaviyo_Reclaim's ScopeSetting helper so service templates can read
 * the Klaviyo extension's admin config without instantiating the
 * ObjectManager directly.
 */
class KlaviyoConfig implements ArgumentInterface
{
    /**
     * @param ScopeSetting $scopeSetting
     */
    public function __construct(
        private readonly ScopeSetting $scopeSetting
    ) {
    }

    /**
     * Whether the Klaviyo extension itself is enabled for the current store.
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeSetting->isEnabled();
    }

    /**
     * Klaviyo public API key, or null when none is configured.
     */
    public function getPublicApiKey(): ?string
    {
        $key = $this->scopeSetting->getPublicApiKey();
        return $key !== null && $key !== '' ? (string) $key : null;
    }

    /**
     * URL of Klaviyo's onsite SDK (klaviyo.js), or null without a public API key.
     */
    public function getCdnUrl(): ?string
    {
        $key = $this->getPublicApiKey();
        if ($key === null) {
            return null;
        }

        return 'https://static.klaviyo.com/onsite/js/' . $key . '/klaviyo.js';
    }
}
