<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Double;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

final class ConfigurableEmailProvider implements EmailProviderInterface
{
    /** @var list<string> */
    public array $requestedCodes = [];

    private EmailInterface $email;

    public function __construct(EmailInterface $email)
    {
        $this->email = $email;
    }

    public function getEmail(string $code): EmailInterface
    {
        $this->requestedCodes[] = $code;

        return $this->email;
    }
}
