<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Double;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;

final class RecordingSenderAdapter implements AdapterInterface
{
    /** @var list<string> */
    public array $recipients = [];

    public ?string $senderAddress = null;

    public ?string $senderName = null;

    public ?RenderedEmail $renderedEmail = null;

    public ?EmailInterface $email = null;

    /** @var array<string, mixed> */
    public array $data = [];

    /** @var list<string> */
    public array $attachments = [];

    /** @var list<string> */
    public array $replyTo = [];

    public int $sendCallCount = 0;

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
        ++$this->sendCallCount;
        $this->recipients = $recipients;
        $this->senderAddress = $senderAddress;
        $this->senderName = $senderName;
        $this->renderedEmail = $renderedEmail;
        $this->email = $email;
        $this->data = $data;
        $this->attachments = $attachments;
        $this->replyTo = $replyTo;
    }
}
