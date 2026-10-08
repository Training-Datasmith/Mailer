<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Factory\EmailFactory;
use Sylius\Component\Mailer\Factory\EmailFactoryInterface;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Model\EmailInterface;

final class EmailFactoryTest extends TestCase
{
    public function testCreateNewReturnsADistinctEnabledEmail(): void
    {
        $factory = new EmailFactory();

        self::assertInstanceOf(EmailFactoryInterface::class, $factory);

        $first = $factory->createNew();
        $second = $factory->createNew();

        self::assertInstanceOf(Email::class, $first);
        self::assertInstanceOf(EmailInterface::class, $first);
        self::assertNotSame($first, $second);

        $first->disable();
        $first->setCode('mutated');

        self::assertTrue($second->isEnabled());
        self::assertNull($second->getCode());
    }
}
