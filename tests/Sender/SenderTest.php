<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Sender;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Factory\EmailFactory;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Provider\EmailProvider;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Sender;
use Sylius\Component\Mailer\Tests\Double\ConfigurableEmailProvider;
use Sylius\Component\Mailer\Tests\Double\RecordingCcAwareSenderAdapter;
use Sylius\Component\Mailer\Tests\Double\RecordingRendererAdapter;
use Sylius\Component\Mailer\Tests\Double\RecordingSenderAdapter;
use Sylius\Component\Mailer\Tests\Double\ThrowingDefaultSettingsProvider;
use Sylius\Component\Mailer\Tests\Double\ThrowingEmailProvider;
use Sylius\Component\Mailer\Tests\Double\ThrowingRendererAdapter;
use Sylius\Component\Mailer\Tests\Double\ThrowingSenderAdapter;

final class SenderTest extends TestCase
{
    public function testSendsRenderedEmailWithTheAddressFromTheEmail(): void
    {
        $provider = new EmailProvider(new EmailFactory(), [
            'order_confirmation' => [
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => [
                    'name' => 'Ada',
                    'address' => 'ada@example.com',
                ],
            ],
        ]);

        $rendered = new RenderedEmail('rendered-subject', 'rendered-body');
        $renderer = new RecordingRendererAdapter($rendered);
        $senderAdapter = new RecordingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider();

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);

        $data = ['order' => 7];
        $attachments = ['/tmp/invoice.pdf'];
        $replyTo = ['reply@example.com'];

        $sender->send('order_confirmation', ['pat@example.com'], $data, $attachments, $replyTo);

        self::assertSame(['pat@example.com'], $senderAdapter->recipients);
        self::assertSame('ada@example.com', $senderAdapter->senderAddress);
        self::assertSame('Ada', $senderAdapter->senderName);
        self::assertSame($rendered, $senderAdapter->renderedEmail);
        self::assertSame('order_confirmation', $senderAdapter->email?->getCode());
        self::assertSame($data, $senderAdapter->data);
        self::assertSame($attachments, $senderAdapter->attachments);
        self::assertSame($replyTo, $senderAdapter->replyTo);
        self::assertSame(1, $senderAdapter->sendCallCount);

        self::assertSame($senderAdapter->email, $renderer->lastEmail);
        self::assertSame($data, $renderer->lastData);
    }

    public function testFallsBackPerFieldForAddressWhenNameIsSetOnEmail(): void
    {
        $email = new Email();
        $email->setCode('order_confirmation');
        $email->setSenderName('Ada');
        $email->enable();

        $provider = new ConfigurableEmailProvider($email);
        $rendered = new RenderedEmail('rendered-subject', 'rendered-body');
        $renderer = new RecordingRendererAdapter($rendered);
        $senderAdapter = new RecordingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider('Shop', 'shop@example.com', true, false);

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);
        $sender->send('order_confirmation', ['pat@example.com']);

        self::assertSame('Ada', $senderAdapter->senderName);
        self::assertSame('shop@example.com', $senderAdapter->senderAddress);
    }

    public function testFallsBackPerFieldForNameWhenAddressIsSetOnEmail(): void
    {
        $email = new Email();
        $email->setCode('order_confirmation');
        $email->setSenderAddress('ada@example.com');
        $email->enable();

        $provider = new ConfigurableEmailProvider($email);
        $rendered = new RenderedEmail('rendered-subject', 'rendered-body');
        $renderer = new RecordingRendererAdapter($rendered);
        $senderAdapter = new RecordingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider('Shop', 'shop@example.com', false, true);

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);
        $sender->send('order_confirmation', ['pat@example.com']);

        self::assertSame('Shop', $senderAdapter->senderName);
        self::assertSame('ada@example.com', $senderAdapter->senderAddress);
    }

    public function testDisabledEmailDoesNotRenderOrSend(): void
    {
        $email = new Email();
        $email->setCode('off');
        $email->disable();

        $provider = new ConfigurableEmailProvider($email);
        $renderer = new ThrowingRendererAdapter();
        $senderAdapter = new ThrowingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider();

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);
        $sender->send('off', ['pat@example.com']);

        self::assertSame(['off'], $provider->requestedCodes);
    }

    public function testUnknownCodeDoesNotRender(): void
    {
        $provider = new EmailProvider(new EmailFactory(), []);
        $renderer = new ThrowingRendererAdapter();
        $senderAdapter = new ThrowingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider();

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email with code "missing" does not exist!');

        $sender->send('missing', ['pat@example.com']);
    }

    public function testInvalidRecipientsNeverReachTheProvider(): void
    {
        $provider = new ThrowingEmailProvider();
        $renderer = new ThrowingRendererAdapter();
        $senderAdapter = new ThrowingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider();

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);

        try {
            $sender->send('bar', ['pat@example.com', null]);
            self::fail('Expected InvalidArgumentException for null recipient.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame([], $provider->requestedCodes);
        }

        try {
            $sender->send('bar', [5]);
            self::fail('Expected InvalidArgumentException for non-string recipient.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame([], $provider->requestedCodes);
        }

        try {
            $sender->send('bar', ['']);
            self::fail('Expected InvalidArgumentException for empty recipient.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame([], $provider->requestedCodes);
        }
    }

    public function testOptionalArgumentsDefaultToEmptyArrays(): void
    {
        $email = new Email();
        $email->setCode('welcome');
        $email->enable();

        $provider = new ConfigurableEmailProvider($email);
        $rendered = new RenderedEmail('rendered-subject', 'rendered-body');
        $renderer = new RecordingRendererAdapter($rendered);
        $senderAdapter = new RecordingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider('Shop', 'shop@example.com', false, false);

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);
        $sender->send('welcome', ['pat@example.com']);

        self::assertSame([], $renderer->lastData);
        self::assertSame([], $senderAdapter->attachments);
        self::assertSame([], $senderAdapter->replyTo);
        self::assertSame(1, $senderAdapter->sendCallCount);
    }

    public function testExtraArgumentsUseSendWithCc(): void
    {
        $email = new Email();
        $email->setCode('welcome');
        $email->setSenderName('Ada');
        $email->setSenderAddress('ada@example.com');
        $email->enable();

        $provider = new ConfigurableEmailProvider($email);
        $rendered = new RenderedEmail('rendered-subject', 'rendered-body');
        $renderer = new RecordingRendererAdapter($rendered);
        $senderAdapter = new RecordingCcAwareSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider();

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);

        $sender->send('welcome', ['pat@example.com'], [], [], [], ['cc@example.com']);
        self::assertSame(1, $senderAdapter->sendWithCcCallCount);
        self::assertSame(['cc@example.com'], $senderAdapter->ccRecipients);
        self::assertSame([], $senderAdapter->bccRecipients);
        self::assertSame(0, $senderAdapter->sendCallCount);

        $sender->send(
            'welcome',
            ['pat@example.com'],
            [],
            [],
            [],
            ['cc@example.com'],
            ['bcc@example.com'],
        );
        self::assertSame(['cc@example.com'], $senderAdapter->ccRecipients);
        self::assertSame(['bcc@example.com'], $senderAdapter->bccRecipients);
    }

    public function testExtraArgumentsOnAPlainAdapterStillCallSend(): void
    {
        $email = new Email();
        $email->setCode('welcome');
        $email->setSenderName('Ada');
        $email->setSenderAddress('ada@example.com');
        $email->enable();

        $provider = new ConfigurableEmailProvider($email);
        $rendered = new RenderedEmail('rendered-subject', 'rendered-body');
        $renderer = new RecordingRendererAdapter($rendered);
        $senderAdapter = new RecordingSenderAdapter();
        $defaults = new ThrowingDefaultSettingsProvider();

        $sender = new Sender($renderer, $senderAdapter, $provider, $defaults);

        $sender->send(
            'welcome',
            ['pat@example.com'],
            ['n' => 1],
            ['/tmp/a.pdf'],
            ['reply@example.com'],
            ['cc@example.com'],
            ['bcc@example.com'],
        );

        self::assertSame(1, $senderAdapter->sendCallCount);
        self::assertSame(['pat@example.com'], $senderAdapter->recipients);
        self::assertSame('ada@example.com', $senderAdapter->senderAddress);
        self::assertSame('Ada', $senderAdapter->senderName);
    }
}
