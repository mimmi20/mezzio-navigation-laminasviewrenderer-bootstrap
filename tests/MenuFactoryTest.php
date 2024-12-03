<?php
/**
 * This file is part of the mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use Interop\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Helper\HelperInterface;
use Laminas\View\HelperPluginManager as ViewHelperPluginManager;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\MenuFactory;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

use function assert;

final class MenuFactoryTest extends TestCase
{
    private MenuFactory $factory;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->factory = new MenuFactory();
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocation(): void
    {
        $containerParser = $this->createMock(ContainerParserInterface::class);
        $htmlElement     = $this->createMock(HtmlElementInterface::class);
        $htmlify         = $this->createMock(HtmlifyInterface::class);
        $escapeHtmlAttr  = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtml      = $this->createMock(EscapeHtml::class);
        $renderer        = $this->createMock(PartialRendererInterface::class);

        $viewHelperPluginManager = $this->getMockBuilder(ViewHelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher                 = self::exactly(2);
        $viewHelperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $escapeHtmlAttr, $escapeHtml): HelperInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(EscapeHtmlAttr::class, $name),
                        default => self::assertSame(EscapeHtml::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $escapeHtmlAttr,
                        default => $escapeHtml,
                    };
                },
            );
        $viewHelperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);

        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher   = self::exactly(5);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $id) use ($matcher, $viewHelperPluginManager, $htmlify, $containerParser, $renderer, $htmlElement): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(ViewHelperPluginManager::class, $id),
                        2 => self::assertSame(HtmlifyInterface::class, $id),
                        3 => self::assertSame(ContainerParserInterface::class, $id),
                        4 => self::assertSame(PartialRendererInterface::class, $id),
                        default => self::assertSame(HtmlElementInterface::class, $id),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $viewHelperPluginManager,
                        2 => $htmlify,
                        3 => $containerParser,
                        4 => $renderer,
                        default => $htmlElement,
                    };
                },
            );

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(Menu::class, $helper);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocationWithTranslator(): void
    {
        $containerParser = $this->createMock(ContainerParserInterface::class);
        $htmlElement     = $this->createMock(HtmlElementInterface::class);
        $htmlify         = $this->createMock(HtmlifyInterface::class);
        $escapeHtmlAttr  = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtml      = $this->createMock(EscapeHtml::class);
        $renderer        = $this->createMock(PartialRendererInterface::class);
        $translator      = $this->createMock(Translate::class);

        $viewHelperPluginManager = $this->getMockBuilder(ViewHelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher                 = self::exactly(3);
        $viewHelperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $translator, $escapeHtmlAttr, $escapeHtml): HelperInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(Translate::class, $name),
                        2 => self::assertSame(EscapeHtmlAttr::class, $name),
                        default => self::assertSame(EscapeHtml::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $translator,
                        2 => $escapeHtmlAttr,
                        default => $escapeHtml,
                    };
                },
            );
        $viewHelperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);

        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher   = self::exactly(5);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $id) use ($matcher, $viewHelperPluginManager, $htmlify, $containerParser, $renderer, $htmlElement): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(ViewHelperPluginManager::class, $id),
                        2 => self::assertSame(HtmlifyInterface::class, $id),
                        3 => self::assertSame(ContainerParserInterface::class, $id),
                        4 => self::assertSame(PartialRendererInterface::class, $id),
                        default => self::assertSame(HtmlElementInterface::class, $id),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $viewHelperPluginManager,
                        2 => $htmlify,
                        3 => $containerParser,
                        4 => $renderer,
                        default => $htmlElement,
                    };
                },
            );

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(Menu::class, $helper);
    }
}
