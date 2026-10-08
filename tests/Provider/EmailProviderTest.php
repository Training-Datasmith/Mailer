<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Factory\EmailFactory;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProvider;

final class EmailProviderTest extends TestCase
{
    public function testMapsAFullConfigurationEntry(): void
    {
        $provider = $this->createProvider([
            'user_confirmation' => [
                'enabled' => false,
                'subject' => 'Hello',
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => [
                    'name' => 'John Doe',
                    'address' => 'john@doe.com',
                ],
            ],
        ]);

        $email = $provider->getEmail('user_confirmation');

        self::assertSame('user_confirmation', $email->getCode());
        self::assertSame('Hello', $email->getSubject());
        self::assertSame('@SyliusMailer/default.html.twig', $email->getTemplate());
        self::assertFalse($email->isEnabled());
        self::assertSame('John Doe', $email->getSenderName());
        self::assertSame('john@doe.com', $email->getSenderAddress());
        self::assertNull($email->getContent());
    }

    public function testOmittedSubjectStaysNullAndOmittedEnabledStaysEnabled(): void
    {
        $provider = $this->createProvider([
            'minimal' => [
                'template' => '@SyliusMailer/default.html.twig',
            ],
        ]);

        $email = $provider->getEmail('minimal');

        self::assertNull($email->getSubject());
        self::assertTrue($email->isEnabled());
        self::assertSame('@SyliusMailer/default.html.twig', $email->getTemplate());
    }

    public function testExplicitEnabledTrueStaysEnabled(): void
    {
        $provider = $this->createProvider([
            'on' => [
                'enabled' => true,
                'template' => '@SyliusMailer/default.html.twig',
            ],
        ]);

        self::assertTrue($provider->getEmail('on')->isEnabled());
    }

    public function testSenderNameAndAddressAreIndependent(): void
    {
        $nameOnly = $this->createProvider([
            'name_only' => [
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => ['name' => 'Only Name'],
            ],
        ])->getEmail('name_only');

        self::assertSame('Only Name', $nameOnly->getSenderName());
        self::assertNull($nameOnly->getSenderAddress());

        $addressOnly = $this->createProvider([
            'address_only' => [
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => ['address' => 'only@example.com'],
            ],
        ])->getEmail('address_only');

        self::assertNull($addressOnly->getSenderName());
        self::assertSame('only@example.com', $addressOnly->getSenderAddress());
    }

    public function testMissingSenderLeavesBothNull(): void
    {
        $email = $this->createProvider([
            'no_sender' => [
                'template' => '@SyliusMailer/default.html.twig',
            ],
        ])->getEmail('no_sender');

        self::assertNull($email->getSenderName());
        self::assertNull($email->getSenderAddress());
    }

    public function testEmptyTemplateStringIsStored(): void
    {
        $email = $this->createProvider([
            'empty_template' => [
                'template' => '',
            ],
        ])->getEmail('empty_template');

        self::assertSame('', $email->getTemplate());
    }

    public function testEachLookupIsANewInstance(): void
    {
        $provider = $this->createProvider([
            'same' => [
                'template' => '@SyliusMailer/default.html.twig',
                'subject' => 'Original',
            ],
        ]);

        $first = $provider->getEmail('same');
        $second = $provider->getEmail('same');

        self::assertNotSame($first, $second);
        $first->setSubject('Mutated');
        self::assertSame('Original', $second->getSubject());
    }

    public function testUnknownCodeThrowsTheConfiguredMessage(): void
    {
        $provider = $this->createProvider([]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email with code "missing" does not exist!');

        $provider->getEmail('missing');
    }

    public function testTwoCodesDoNotLeak(): void
    {
        $provider = $this->createProvider([
            'welcome' => [
                'template' => '@SyliusMailer/default.html.twig',
                'subject' => 'Welcome',
            ],
            'cancel' => [
                'template' => '@SyliusMailer/default.html.twig',
                'subject' => 'Cancelled',
            ],
        ]);

        self::assertSame('Welcome', $provider->getEmail('welcome')->getSubject());
        self::assertSame('Cancelled', $provider->getEmail('cancel')->getSubject());
    }

    /**
     * @param array<string, array<string, mixed>> $configuration
     */
    private function createProvider(array $configuration): EmailProvider
    {
        return new EmailProvider(new EmailFactory(), $configuration);
    }
}
