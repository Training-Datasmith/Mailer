<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Sender\Adapter;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class AbstractSenderAdapterTest extends TestCase
{
    public function testDispatcherIsNullUntilSet(): void
    {
        $adapter = new TestSenderAdapter();
        self::assertNull($adapter->dispatcher());

        $dispatcher = new EventDispatcher();
        $adapter->setEventDispatcher($dispatcher);

        self::assertSame($dispatcher, $adapter->dispatcher());
    }

    public function testSendAndSendWithCcSignatures(): void
    {
        $send = new ReflectionMethod(AdapterInterface::class, 'send');
        self::assertTrue($send->isPublic());
        self::assertSame('void', $send->getReturnType()?->getName());

        $sendParameters = $send->getParameters();
        self::assertSame(
            ['recipients', 'senderAddress', 'senderName', 'renderedEmail', 'email', 'data', 'attachments', 'replyTo'],
            array_map(static fn ($parameter) => $parameter->getName(), $sendParameters),
        );
        self::assertTrue($sendParameters[6]->isOptional());
        self::assertTrue($sendParameters[7]->isOptional());

        $ccAware = new ReflectionClass(CcAwareAdapterInterface::class);
        self::assertTrue($ccAware->isInterface());
        self::assertTrue($ccAware->isSubclassOf(AdapterInterface::class));

        $sendWithCc = new ReflectionMethod(CcAwareAdapterInterface::class, 'sendWithCC');
        $ccParameters = $sendWithCc->getParameters();
        self::assertSame('ccRecipients', $ccParameters[8]->getName());
        self::assertSame('array', $ccParameters[8]->getType()?->getName());
        self::assertTrue($ccParameters[8]->isOptional());
        self::assertSame('bccRecipients', $ccParameters[9]->getName());
        self::assertSame('array', $ccParameters[9]->getType()?->getName());
        self::assertTrue($ccParameters[9]->isOptional());
    }
}

final class TestSenderAdapter extends AbstractAdapter
{
    public function dispatcher(): ?EventDispatcher
    {
        return $this->dispatcher;
    }

    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = [],
    ): void {
        throw new \LogicException('Not used in abstract adapter tests.');
    }
}
