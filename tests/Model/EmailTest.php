<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Model\EmailInterface;

final class EmailTest extends TestCase
{
    public function testFreshEmailMatchesTheInterfaceDefaults(): void
    {
        $email = new Email();

        self::assertInstanceOf(EmailInterface::class, $email);
        self::assertNull($email->getId());
        self::assertNull($email->getCode());
        self::assertNull($email->getSubject());
        self::assertNull($email->getContent());
        self::assertNull($email->getTemplate());
        self::assertNull($email->getSenderName());
        self::assertNull($email->getSenderAddress());
        self::assertTrue($email->isEnabled());
    }

    public function testAccessorsRoundTripIndependentValues(): void
    {
        $email = new Email();

        $email->setCode('order_confirmation');
        $email->setSubject('Subject A');
        $email->setContent('Body A');
        $email->setTemplate('@App/order.html.twig');
        $email->setSenderName('Ada');
        $email->setSenderAddress('ada@example.com');

        self::assertSame('order_confirmation', $email->getCode());
        self::assertSame('Subject A', $email->getSubject());
        self::assertSame('Body A', $email->getContent());
        self::assertSame('@App/order.html.twig', $email->getTemplate());
        self::assertSame('Ada', $email->getSenderName());
        self::assertSame('ada@example.com', $email->getSenderAddress());
    }

    public function testEnableDisableAndSetEnabled(): void
    {
        $email = new Email();

        $email->disable();
        self::assertFalse($email->isEnabled());

        $email->enable();
        self::assertTrue($email->isEnabled());

        $email->setEnabled(false);
        self::assertFalse($email->isEnabled());

        $email->setEnabled(true);
        self::assertTrue($email->isEnabled());
    }
}
