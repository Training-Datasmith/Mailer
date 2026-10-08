<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Provider\DefaultSettingsProvider;
use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;

final class DefaultSettingsProviderTest extends TestCase
{
    public function testReturnsConstructorStrings(): void
    {
        $provider = new DefaultSettingsProvider('Shop', 'shop@example.com');

        self::assertInstanceOf(DefaultSettingsProviderInterface::class, $provider);
        self::assertSame('Shop', $provider->getSenderName());
        self::assertSame('shop@example.com', $provider->getSenderAddress());

        $other = new DefaultSettingsProvider('Other', 'other@example.com');
        self::assertSame('Other', $other->getSenderName());
        self::assertSame('other@example.com', $other->getSenderAddress());
    }
}
