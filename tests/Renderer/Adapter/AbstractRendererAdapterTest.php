<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Tests\Renderer\Adapter;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class AbstractRendererAdapterTest extends TestCase
{
    public function testDispatcherIsNullUntilSet(): void
    {
        $adapter = new TestRendererAdapter();
        self::assertNull($adapter->dispatcher());

        $dispatcher = new EventDispatcher();
        $adapter->setEventDispatcher($dispatcher);

        self::assertSame($dispatcher, $adapter->dispatcher());
    }

    public function testRenderSignature(): void
    {
        $method = new ReflectionMethod(AdapterInterface::class, 'render');

        self::assertTrue($method->isPublic());
        self::assertSame('render', $method->getName());

        $returnType = $method->getReturnType();
        self::assertNotNull($returnType);
        self::assertSame(RenderedEmail::class, $returnType->getName());

        $parameters = $method->getParameters();
        self::assertCount(2, $parameters);
        self::assertSame('email', $parameters[0]->getName());
        self::assertSame(EmailInterface::class, $parameters[0]->getType()?->getName());
        self::assertSame('data', $parameters[1]->getName());
        self::assertTrue($parameters[1]->isOptional());
        self::assertSame('array', $parameters[1]->getType()?->getName());
    }
}

final class TestRendererAdapter extends AbstractAdapter
{
    public function dispatcher(): ?EventDispatcher
    {
        return $this->dispatcher;
    }

    public function render(EmailInterface $email, array $data = []): RenderedEmail
    {
        throw new \LogicException('Not used in abstract adapter tests.');
    }
}
