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

namespace Sylius\Component\Mailer\Sender;

use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface as SenderAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface;
use Webmozart\Assert\Assert;

final class Sender implements SenderInterface
{
    /**
     * Creates the email sender with all required collaborators.
     *
     * @param RendererAdapterInterface        $rendererAdapter        Adapter that renders the email template to HTML/text
     * @param SenderAdapterInterface          $senderAdapter          Adapter that delivers the rendered email via a transport
     * @param EmailProviderInterface          $provider               Provides Email model instances by code
     * @param DefaultSettingsProviderInterface $defaultSettingsProvider Fallback sender address and name when not set on the email
     */
    public function __construct(
        private readonly RendererAdapterInterface $rendererAdapter,
        private readonly SenderAdapterInterface $senderAdapter,
        private readonly EmailProviderInterface $provider,
        private readonly DefaultSettingsProviderInterface $defaultSettingsProvider,
    ) {
    }

    /**
     * Renders and sends an email identified by code to one or more recipients.
     *
     * If the email is disabled in configuration, the method returns without sending.
     * When more than 5 arguments are provided and the sender adapter implements
     * CcAwareAdapterInterface, CC and BCC lists from arguments 6 and 7 are forwarded.
     *
     * @param string   $code        Email code identifying the template and settings to use
     * @param string[] $recipients  Non-empty list of recipient email addresses
     * @param array    $data        Template variables passed to the renderer
     * @param array    $attachments File attachments to include in the email
     * @param array    $replyTo     Reply-to email addresses
     *
     * @return void
     *
     * @throws \InvalidArgumentException If any recipient address is an empty string
     */
    public function send(
        string $code,
        array $recipients,
        array $data = [],
        array $attachments = [],
        array $replyTo = [],
    ): void {
        $arguments = func_get_args();

        Assert::allStringNotEmpty($recipients);

        $email = $this->provider->getEmail($code);

        if (!$email->isEnabled()) {
            return;
        }

        $senderAddress = $email->getSenderAddress() ?: $this->defaultSettingsProvider->getSenderAddress();
        $senderName = $email->getSenderName() ?: $this->defaultSettingsProvider->getSenderName();

        $renderedEmail = $this->rendererAdapter->render($email, $data);

        if (count($arguments) > 5 && $this->senderAdapter instanceof CcAwareAdapterInterface) {
            $this->senderAdapter->sendWithCC(
                $recipients,
                $senderAddress,
                $senderName,
                $renderedEmail,
                $email,
                $data,
                $attachments,
                $replyTo,
                $arguments[5] ?? [],
                $arguments[6] ?? [],
            );

            return;
        }

        $this->senderAdapter->send(
            $recipients,
            $senderAddress,
            $senderName,
            $renderedEmail,
            $email,
            $data,
            $attachments,
            $replyTo,
        );
    }
}
