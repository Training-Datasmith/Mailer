<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Double;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;

final class ThrowingSenderAdapter implements AdapterInterface
{
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
        throw new \RuntimeException('Sender adapter must not be called on this path.');
    }
}
