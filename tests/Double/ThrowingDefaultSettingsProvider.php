<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Double;

use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;

final class ThrowingDefaultSettingsProvider implements DefaultSettingsProviderInterface
{
    private bool $throwOnName;

    private bool $throwOnAddress;

    private string $senderName;

    private string $senderAddress;

    public function __construct(
        string $senderName = 'unused',
        string $senderAddress = 'unused@example.com',
        bool $throwOnName = true,
        bool $throwOnAddress = true,
    ) {
        $this->senderName = $senderName;
        $this->senderAddress = $senderAddress;
        $this->throwOnName = $throwOnName;
        $this->throwOnAddress = $throwOnAddress;
    }

    public function getSenderName(): string
    {
        if ($this->throwOnName) {
            throw new \RuntimeException('Default sender name must not be requested on this path.');
        }

        return $this->senderName;
    }

    public function getSenderAddress(): string
    {
        if ($this->throwOnAddress) {
            throw new \RuntimeException('Default sender address must not be requested on this path.');
        }

        return $this->senderAddress;
    }
}
