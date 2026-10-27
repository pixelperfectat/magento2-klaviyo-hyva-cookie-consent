<?php

declare(strict_types=1);

namespace PixelPerfect\KlaviyoHyvaCookieConsent\Test\Unit\Etc;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class ConfigXmlTest extends TestCase
{
    public function testKlaviyoGateIsEnabledByDefault(): void
    {
        $config = new SimpleXMLElement((string) file_get_contents(__DIR__ . '/../../../src/etc/config.xml'));
        $enabled = $config->xpath('/config/default/hyva_cookie_consent/services/klaviyo/enabled');

        self::assertIsArray($enabled);
        self::assertCount(1, $enabled);
        self::assertSame('1', (string) $enabled[0]);
    }
}
