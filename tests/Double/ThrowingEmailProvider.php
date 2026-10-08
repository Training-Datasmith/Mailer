<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Double;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

final class ThrowingEmailProvider implements EmailProviderInterface
{
    /** @var list<string> */
    public array $requestedCodes = [];

    public function getEmail(string $code): EmailInterface
    {
        $this->requestedCodes[] = $code;

        throw new \LogicException('Email provider must not be called on this path.');
    }
}
