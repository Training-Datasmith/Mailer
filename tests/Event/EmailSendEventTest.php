<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Event;

use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\Email;
use SyliusLabs\Polyfill\Symfony\EventDispatcher\Event;

final class EmailSendEventTest extends TestCase
{
    public function testExposesConstructorValues(): void
    {
        $message = new stdClass();
        $email = new Email();
        $email->setCode('welcome');

        $event = new EmailSendEvent(
            $message,
            $email,
            ['n' => 1],
            ['a@example.com'],
            ['r@example.com'],
        );

        self::assertInstanceOf(Event::class, $event);
        self::assertSame($message, $event->getMessage());
        self::assertSame($email, $event->getEmail());
        self::assertSame('welcome', $event->getEmail()->getCode());
        self::assertSame(['n' => 1], $event->getData());
        self::assertSame(['a@example.com'], $event->getRecipients());
        self::assertSame(['r@example.com'], $event->getReplyTo());

        $minimal = new EmailSendEvent($message, $email, ['n' => 1]);
        self::assertSame([], $minimal->getRecipients());
        self::assertSame([], $minimal->getReplyTo());
    }
}
