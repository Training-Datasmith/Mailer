<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Renderer;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

final class RenderedEmailTest extends TestCase
{
    public function testSubjectAndBodyAreIndependent(): void
    {
        $rendered = new RenderedEmail('Subject A', 'Body A');

        $rendered->setSubject('Subject B');
        self::assertSame('Body A', $rendered->getBody());

        $rendered->setBody('Body B');
        self::assertSame('Subject B', $rendered->getSubject());
    }
}
