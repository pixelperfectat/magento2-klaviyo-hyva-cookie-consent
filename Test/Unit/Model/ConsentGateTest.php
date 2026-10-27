<?php

declare(strict_types=1);

namespace PixelPerfect\KlaviyoHyvaCookieConsent\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use PixelPerfect\KlaviyoHyvaCookieConsent\Model\ConsentGate;

class ConsentGateTest extends TestCase
{
    /**
     * @dataProvider flagProvider
     */
    public function testKlaviyoServiceFollowsItsStoreScopedFlag(bool $flag): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects(self::once())
            ->method('isSetFlag')
            ->with('hyva_cookie_consent/services/klaviyo/enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn($flag);

        self::assertSame($flag, (new ConsentGate($scopeConfig))->isKlaviyoServiceEnabled());
    }

    /**
     * @dataProvider flagProvider
     */
    public function testConsentManagementFollowsTheConsentModuleFlag(bool $flag): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects(self::once())
            ->method('isSetFlag')
            ->with('web/hyva_cookie_consent/general/enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn($flag);

        self::assertSame($flag, (new ConsentGate($scopeConfig))->isConsentManagementEnabled());
    }

    /**
     * @return array<string, array{bool}>
     */
    public static function flagProvider(): array
    {
        return [
            'on' => [true],
            'off' => [false],
        ];
    }

    /**
     * @dataProvider consentVersionProvider
     */
    public function testConsentVersionMatchesTheConsentModule(mixed $stored, int $expected): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects(self::once())
            ->method('getValue')
            ->with('web/hyva_cookie_consent/general/consent_version', ScopeInterface::SCOPE_STORE)
            ->willReturn($stored);

        self::assertSame($expected, (new ConsentGate($scopeConfig))->getConsentVersion());
    }

    /**
     * @return array<string, array{mixed, int}>
     */
    public static function consentVersionProvider(): array
    {
        return [
            'configured' => ['3', 3],
            'not configured falls back to 1' => [null, 1],
            'empty falls back to 1' => ['', 1],
        ];
    }
}
