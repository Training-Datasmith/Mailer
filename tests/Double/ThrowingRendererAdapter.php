<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Double;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

final class ThrowingRendererAdapter implements AdapterInterface
{
    public function render(EmailInterface $email, array $data = []): RenderedEmail
    {
        throw new \RuntimeException('Renderer must not be called on this path.');
    }
}
