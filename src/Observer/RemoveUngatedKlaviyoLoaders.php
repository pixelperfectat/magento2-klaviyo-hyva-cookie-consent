<?php

declare(strict_types=1);

namespace PixelPerfect\KlaviyoHyvaCookieConsent\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use PixelPerfect\KlaviyoHyvaCookieConsent\Model\ConsentGate;

/**
 * Removes Klaviyo's own loader blocks while the cookie consent module is on.
 *
 * The consent-gated loader of this module replaces them. While the consent module is off, Klaviyo
 * (and the compat module's templates) keep their default behaviour instead of going silent.
 */
class RemoveUngatedKlaviyoLoaders implements ObserverInterface
{
    /**
     * Klaviyo_Reclaim's loader blocks: every page (default) and the checkout page.
     */
    private const KLAVIYO_LOADER_BLOCKS = [
        'klaviyo.initialize',
        'klaviyo_reclaim.checkout.initialize',
    ];

    /**
     * @param ConsentGate $consentGate
     */
    public function __construct(
        private readonly ConsentGate $consentGate
    ) {
    }

    /**
     * Handles layout_generate_blocks_after.
     */
    public function execute(Observer $observer): void
    {
        $layout = $observer->getEvent()->getData('layout');
        if (!$layout instanceof LayoutInterface || !$this->consentGate->isConsentManagementEnabled()) {
            return;
        }

        foreach (self::KLAVIYO_LOADER_BLOCKS as $blockName) {
            if ($layout->hasElement($blockName)) {
                $layout->unsetElement($blockName);
            }
        }
    }
}
