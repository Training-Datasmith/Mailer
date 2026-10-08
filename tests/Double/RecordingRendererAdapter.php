<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Double;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

final class RecordingRendererAdapter implements AdapterInterface
{
    public ?EmailInterface $lastEmail = null;

    /** @var array<string, mixed> */
    public array $lastData = [];

    private RenderedEmail $renderedEmail;

    public function __construct(RenderedEmail $renderedEmail)
    {
        $this->renderedEmail = $renderedEmail;
    }

    public function render(EmailInterface $email, array $data = []): RenderedEmail
    {
        $this->lastEmail = $email;
        $this->lastData = $data;

        return $this->renderedEmail;
    }
}
