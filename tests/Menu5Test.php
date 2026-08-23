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
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\ContainerParserInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlifyInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class Menu5Test extends TestCase
{
    /** @throws void */
    #[Override]
    protected function tearDown(): void
    {
        Menu::setDefaultAuthorization();
        Menu::setDefaultRole();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testDoNotRenderMenuIfNoPageIsActive(): void
    {
        $container = self::createStub(ContainerInterface::class);

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $invokedCount    = self::exactly(3);
        $containerParser->expects($invokedCount)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($invokedCount, $container): ContainerInterface | null {
                    $invocation = $invokedCount->numberOfInvocations();

                    match ($invocation) {
                        2 => self::assertNull($containerParam, (string) $invocation),
                        default => self::assertSame($container, $containerParam, (string) $invocation),
                    };

                    return match ($invocation) {
                        2 => null,
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

        $renderer = $this->createMock(LaminasViewRenderer::class);
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

        $menu->setContainer($container);

        self::assertSame('', $menu->renderMenu());
    }
}
