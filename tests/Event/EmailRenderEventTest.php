<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Event;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use SyliusLabs\Polyfill\Symfony\EventDispatcher\Event;

final class EmailRenderEventTest extends TestCase
{
    public function testStoresAndReplacesRenderedEmailAndRecipients(): void
    {
        $rendered = new RenderedEmail('Subject', 'Body');
        $event = new EmailRenderEvent($rendered, ['a@example.com']);

        self::assertInstanceOf(Event::class, $event);
        self::assertSame($rendered, $event->getRenderedEmail());
        self::assertSame(['a@example.com'], $event->getRecipients());

        $replacement = new RenderedEmail('Other', 'Other body');
        $event->setRenderedEmail($replacement);
        self::assertSame($replacement, $event->getRenderedEmail());

        $event->setRecipients(['b@example.com']);
        self::assertSame(['b@example.com'], $event->getRecipients());

        $defaultRecipients = new EmailRenderEvent($rendered);
        self::assertSame([], $defaultRecipients->getRecipients());
    }
}
