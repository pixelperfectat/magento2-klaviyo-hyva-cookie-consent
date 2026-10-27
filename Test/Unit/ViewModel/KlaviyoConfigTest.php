<?php

declare(strict_types=1);

namespace PixelPerfect\KlaviyoHyvaCookieConsent\Test\Unit\ViewModel;

use Klaviyo\Reclaim\Helper\ScopeSetting;
use PHPUnit\Framework\TestCase;
use PixelPerfect\KlaviyoHyvaCookieConsent\ViewModel\KlaviyoConfig;

class KlaviyoConfigTest extends TestCase
{
    /**
     * @dataProvider enabledValueProvider
     */
    public function testIsEnabledCastsTheStoredValue(mixed $stored, bool $expected): void
    {
        self::assertSame($expected, $this->viewModel(enabled: $stored)->isEnabled());
    }

    /**
     * @return array<string, array{mixed, bool}>
     */
    public static function enabledValueProvider(): array
    {
        return [
            'enabled' => ['1', true],
            'disabled' => ['0', false],
            'not configured' => [null, false],
        ];
    }

    /**
     * @dataProvider missingKeyProvider
     */
    public function testMissingPublicKeyIsNull(?string $stored): void
    {
        $viewModel = $this->viewModel(key: $stored);

        self::assertNull($viewModel->getPublicApiKey());
        self::assertNull($viewModel->getCdnUrl());
    }

    /**
     * @return array<string, array{?string}>
     */
    public static function missingKeyProvider(): array
    {
        return [
            'not configured' => [null],
            'empty string' => [''],
        ];
    }

    public function testConfiguredKeyIsReturnedAndBuildsTheSdkUrl(): void
    {
        $viewModel = $this->viewModel(key: 'AbC123');

        self::assertSame('AbC123', $viewModel->getPublicApiKey());
        self::assertSame('https://static.klaviyo.com/onsite/js/AbC123/klaviyo.js', $viewModel->getCdnUrl());
    }

    public function testNumericKeyFromConfigPhpIsReturnedAsString(): void
    {
        // app/etc/config.php can hold the key as an integer literal.
        self::assertSame('123456', $this->viewModel(key: 123456)->getPublicApiKey());
    }

    private function viewModel(mixed $enabled = null, mixed $key = null): KlaviyoConfig
    {
        $scopeSetting = $this->createMock(ScopeSetting::class);
        $scopeSetting->method('isEnabled')->willReturn($enabled);
        $scopeSetting->method('getPublicApiKey')->willReturn($key);

        return new KlaviyoConfig($scopeSetting);
    }
}
