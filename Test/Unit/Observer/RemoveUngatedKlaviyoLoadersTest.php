<?php

declare(strict_types=1);

namespace PixelPerfect\KlaviyoHyvaCookieConsent\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;
use PixelPerfect\KlaviyoHyvaCookieConsent\Model\ConsentGate;
use PixelPerfect\KlaviyoHyvaCookieConsent\Observer\RemoveUngatedKlaviyoLoaders;

class RemoveUngatedKlaviyoLoadersTest extends TestCase
{
    public function testRemovesBothKlaviyoLoadersWhileConsentManagementIsOn(): void
    {
        $removed = [];
        $layout = $this->layoutWith(['klaviyo.initialize', 'klaviyo_reclaim.checkout.initialize'], $removed);

        $this->observer(consentManagementEnabled: true)->execute($this->eventWith($layout));

        self::assertSame(['klaviyo.initialize', 'klaviyo_reclaim.checkout.initialize'], $removed);
    }

    public function testOnlyRemovesLoadersPresentOnThePage(): void
    {
        $removed = [];
        $layout = $this->layoutWith(['klaviyo.initialize'], $removed);

        $this->observer(consentManagementEnabled: true)->execute($this->eventWith($layout));

        self::assertSame(['klaviyo.initialize'], $removed);
    }

    public function testKeepsKlaviyoLoadersWhileConsentManagementIsOff(): void
    {
        $removed = [];
        $layout = $this->layoutWith(['klaviyo.initialize', 'klaviyo_reclaim.checkout.initialize'], $removed);

        $this->observer(consentManagementEnabled: false)->execute($this->eventWith($layout));

        self::assertSame([], $removed);
    }

    public function testIgnoresEventsWithoutALayout(): void
    {
        $consentGate = $this->createMock(ConsentGate::class);
        $consentGate->expects(self::never())->method('isConsentManagementEnabled');

        (new RemoveUngatedKlaviyoLoaders($consentGate))->execute($this->eventWith(null));
    }

    private function observer(bool $consentManagementEnabled): RemoveUngatedKlaviyoLoaders
    {
        $consentGate = $this->createMock(ConsentGate::class);
        $consentGate->method('isConsentManagementEnabled')->willReturn($consentManagementEnabled);

        return new RemoveUngatedKlaviyoLoaders($consentGate);
    }

    /**
     * @param string[] $existingBlocks
     * @param string[] $removed collects the names passed to unsetElement()
     */
    private function layoutWith(array $existingBlocks, array &$removed): LayoutInterface
    {
        $layout = $this->createMock(LayoutInterface::class);
        $layout->method('hasElement')->willReturnCallback(
            static fn (string $name): bool => in_array($name, $existingBlocks, true)
        );
        $layout->method('unsetElement')->willReturnCallback(
            static function (string $name) use (&$removed, $layout): LayoutInterface {
                $removed[] = $name;
                return $layout;
            }
        );

        return $layout;
    }

    private function eventWith(?LayoutInterface $layout): Observer
    {
        return new Observer(['event' => new Event(['layout' => $layout])]);
    }
}
