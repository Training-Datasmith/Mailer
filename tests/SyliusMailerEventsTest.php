<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\SyliusMailerEvents;

final class SyliusMailerEventsTest extends TestCase
{
    public function testEventNamesAreTheBundleContract(): void
    {
        self::assertSame('sylius.email_rendered', SyliusMailerEvents::EMAIL_PRE_RENDER);
        self::assertSame('sylius.email_send.pre_send', SyliusMailerEvents::EMAIL_PRE_SEND);
        self::assertSame('sylius.email_send.post_send', SyliusMailerEvents::EMAIL_POST_SEND);

        self::assertNotSame(SyliusMailerEvents::EMAIL_PRE_RENDER, SyliusMailerEvents::EMAIL_PRE_SEND);
        self::assertNotSame(SyliusMailerEvents::EMAIL_PRE_RENDER, SyliusMailerEvents::EMAIL_POST_SEND);
        self::assertNotSame(SyliusMailerEvents::EMAIL_PRE_SEND, SyliusMailerEvents::EMAIL_POST_SEND);
    }
}
