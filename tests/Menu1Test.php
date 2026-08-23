<?php

/**
 * This file is part of the mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2026, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Exception\ExceptionInterface;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Renderer\RendererInterface;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class Menu1Test extends TestCase
{
    /** @throws void */
    #[Override]
    protected function tearDown(): void
    {
        Menu::setDefaultAuthorization();
        Menu::setDefaultRole();
    }

    /** @throws Exception */
    public function testSetMaxDepth(): void
    {
        $maxDepth = 4;

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        self::assertNull($menu->getMaxDepth());

        $menu->setMaxDepth($maxDepth);

        self::assertSame($maxDepth, $menu->getMaxDepth());
    }

    /** @throws Exception */
    public function testSetMinDepth(): void
    {
        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        self::assertSame(0, $menu->getMinDepth());

        $menu->setMinDepth(4);

        self::assertSame(4, $menu->getMinDepth());

        $menu->setMinDepth(-1);

        self::assertSame(0, $menu->getMinDepth());

        $menu->setMinDepth(0);

        self::assertSame(0, $menu->getMinDepth());

        $menu->setMinDepth(1);

        self::assertSame(1, $menu->getMinDepth());

        $menu->setMinDepth(4);

        self::assertSame(4, $menu->getMinDepth());
    }

    /** @throws Exception */
    public function testSetRenderInvisible(): void
    {
        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        self::assertFalse($menu->getRenderInvisible());

        $menu->setRenderInvisible(renderInvisible: true);

        self::assertTrue($menu->getRenderInvisible());
    }

    /** @throws Exception */
    public function testSetRoles(): void
    {
        $role        = 'testRole';
        $defaultRole = 'testDefaultRole';

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        self::assertSame([], $menu->getRoles());
        self::assertFalse($menu->hasRoles());

        Menu::setDefaultRole($defaultRole);

        self::assertSame([$defaultRole], $menu->getRoles());
        self::assertTrue($menu->hasRoles());

        $menu->setRoles([$role]);

        self::assertSame([$role], $menu->getRoles());
        self::assertTrue($menu->hasRoles());
    }

    /** @throws Exception */
    public function testSetUseAuthorization(): void
    {
        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        self::assertFalse($menu->getUseAuthorization());

        $menu->setUseAuthorization();

        self::assertTrue($menu->getUseAuthorization());

        $menu->setUseAuthorization(useAuthorization: false);

        self::assertFalse($menu->getUseAuthorization());
    }

    /** @throws Exception */
    public function testSetAuthorization(): void
    {
        $auth        = self::createStub(AuthorizationInterface::class);
        $defaultAuth = self::createStub(AuthorizationInterface::class);

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        self::assertNull($menu->getAuthorization());
        self::assertFalse($menu->hasAuthorization());

        assert($defaultAuth instanceof AuthorizationInterface);
        Menu::setDefaultAuthorization($defaultAuth);

        self::assertSame($defaultAuth, $menu->getAuthorization());
        self::assertTrue($menu->hasAuthorization());

        assert($auth instanceof AuthorizationInterface);
        $menu->setAuthorization($auth);

        self::assertSame($auth, $menu->getAuthorization());
        self::assertTrue($menu->hasAuthorization());
    }

    /** @throws Exception */
    public function testSetView(): void
    {
        $view = self::createStub(RendererInterface::class);

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        self::assertNull($menu->getView());

        assert($view instanceof RendererInterface);
        $menu->setView($view);

        self::assertSame($view, $menu->getView());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testSetContainer(): void
    {
        $container = self::createStub(ContainerInterface::class);

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $invokedCount    = self::exactly(2);
        $containerParser->expects($invokedCount)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($invokedCount, $container): ContainerInterface | null {
                    $invocation = $invokedCount->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertNull($containerParam, (string) $invocation),
                        default => self::assertSame($container, $containerParam, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        default => $container,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        $container1 = $menu->getContainer();

        self::assertInstanceOf(Navigation::class, $container1);

        $menu->setContainer();

        $container2 = $menu->getContainer();

        self::assertInstanceOf(Navigation::class, $container2);
        self::assertNotSame($container1, $container2);

        $menu->setContainer($container);

        self::assertSame($container, $menu->getContainer());
    }

    /** @throws ExceptionInterface */
    public function testSetContainerWithStringDefaultAndNavigationNotFound(): void
    {
        $name = 'default';

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willThrowException(new InvalidArgumentException('test'));

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('test');
        $this->expectExceptionCode(0);

        $menu->setContainer($name);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testSetContainerWithStringFound(): void
    {
        $container = self::createStub(ContainerInterface::class);
        $name      = 'Mimmi20\Mezzio\Navigation\Top';

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($container);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $menu = new Menu(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtmlAttr,
            renderer: $renderer,
            escapeHtml: $escapeHtml,
            htmlElement: $htmlElement,
            translator: $translator,
        );

        $menu->setContainer($name);

        self::assertSame($container, $menu->getContainer());
    }
}
