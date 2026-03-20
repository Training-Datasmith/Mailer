<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Mailer\Model;

/**
 * Value object representing an email template configuration.
 *
 * Stores the template code, subject, content, Twig template path, and
 * per-email sender overrides. When sender fields are null the Sender service
 * falls back to the DefaultSettingsProvider configured globally.
 *
 * @see EmailInterface For the full property contract
 */
final class Email implements EmailInterface
{
    /** @var mixed Primary key (set by persistence layer; null for transient instances) */
    private $id;

    /** @var string|null Unique code used to look up this email (e.g. 'sylius.user.welcome') */
    private ?string $code = null;

    /** @var bool Whether this email template is active; disabled emails are silently dropped by Sender */
    private bool $enabled = true;

    private ?string $subject = null;

    private ?string $content = null;

    private ?string $template = null;

    private ?string $senderName = null;

    private ?string $senderAddress = null;

    /**
     * Returns the primary key of this email entity.
     *
     * @return mixed The ID value as assigned by the persistence layer, or null for transient instances
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderName(string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderAddress(): ?string
    {
        return $this->senderAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderAddress(string $senderAddress): void
    {
        $this->senderAddress = $senderAddress;
    }
}
