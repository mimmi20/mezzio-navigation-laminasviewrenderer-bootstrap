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
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Renderer\PhpRenderer;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\ContainerParserInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlifyInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Breadcrumbs;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class Breadcrumbs6Test extends TestCase
{
    /** @throws void */
    #[Override]
    protected function tearDown(): void
    {
        Breadcrumbs::setDefaultAuthorization();
        Breadcrumbs::setDefaultRole();
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderWithPartial(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $page = $this->createMock(PageInterface::class);
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::never())
            ->method('getParent');

        $navigation = new Navigation();
        $navigation->addPage($page);

        $role = 'testRole';

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $invokedCount    = self::exactly(2);
        $containerParser->expects($invokedCount)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | string | null $containerParam = null) use ($invokedCount, $name, $navigation): ContainerInterface {
                    match ($invokedCount->numberOfInvocations()) {
                        1 => self::assertSame($name, $containerParam),
                        default => self::assertSame($navigation, $containerParam),
                    };

                    return $navigation;
                },
            );

        $escapePlugin = $this->createMock(EscapeHtml::class);
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $expected  = 'renderedPartial';
        $partial   = 'testPartial';
        $seperator = '/';

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with($partial, ['pages' => [], 'separator' => $seperator, 'layout' => false])
            ->willReturn($expected);

        $translatePlugin = $this->createMock(Translate::class);
        $translatePlugin->expects(self::never())
            ->method('__invoke');

        $breadcrumbs = new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapePlugin,
            renderer: $renderer,
            translator: $translatePlugin,
        );

        $breadcrumbs->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $breadcrumbs->setAuthorization($auth);

        $breadcrumbs->setSeparator($seperator);
        $breadcrumbs->setLinkLast(linkLast: true);
        $breadcrumbs->setPartial($partial);

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $breadcrumbs->setView($view);

        self::assertSame($expected, $breadcrumbs->render($name));
    }

    /**
     * @throws Exception
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testToStringWithPartial(): void
    {
        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');
        assert($auth instanceof AuthorizationInterface);

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $page = $this->createMock(PageInterface::class);
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::never())
            ->method('getParent');

        $navigation = new Navigation();
        $navigation->addPage($page);

        $role = 'testRole';

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $invokedCount    = self::exactly(3);
        $containerParser->expects($invokedCount)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | string | null $containerParam = null) use ($invokedCount, $name, $navigation): ContainerInterface | null {
                    match ($invokedCount->numberOfInvocations()) {
                        1 => self::assertSame($name, $containerParam),
                        2 => self::assertNull($containerParam),
                        default => self::assertSame($navigation, $containerParam),
                    };

                    return match ($invokedCount->numberOfInvocations()) {
                        2 => null,
                        default => $navigation,
                    };
                },
            );

        $escapePlugin = $this->createMock(EscapeHtml::class);
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $expected  = 'renderedPartial';
        $partial   = 'testPartial';
        $seperator = '/';

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with($partial, ['pages' => [], 'separator' => $seperator, 'layout' => false])
            ->willReturn($expected);

        $translatePlugin = $this->createMock(Translate::class);
        $translatePlugin->expects(self::never())
            ->method('__invoke');

        $breadcrumbs = new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapePlugin,
            renderer: $renderer,
            translator: $translatePlugin,
        );

        $breadcrumbs->setRoles([$role]);
        $breadcrumbs->setAuthorization($auth);
        $breadcrumbs->setSeparator($seperator);
        $breadcrumbs->setLinkLast(linkLast: true);
        $breadcrumbs->setPartial($partial);

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $breadcrumbs->setView($view);

        self::assertSame($expected, (string) $breadcrumbs($name));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvoke(): void
    {
        $container = self::createStub(ContainerInterface::class);

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($container)
            ->willReturn($container);

        $escapePlugin = $this->createMock(EscapeHtml::class);
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::never())
            ->method('render');

        $translatePlugin = $this->createMock(Translate::class);
        $translatePlugin->expects(self::never())
            ->method('__invoke');

        $breadcrumbs = new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapePlugin,
            renderer: $renderer,
            translator: $translatePlugin,
        );

        $container1 = $breadcrumbs->getContainer();

        self::assertInstanceOf(Navigation::class, $container1);

        $breadcrumbs($container);

        self::assertSame($container, $breadcrumbs->getContainer());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testDoNotRenderIfNoPageIsActive(): void
    {
        $page = $this->createMock(PageInterface::class);
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::never())
            ->method('getParent');

        $navigation = new Navigation();
        $navigation->addPage($page);

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $invokedCount    = self::exactly(3);
        $containerParser->expects($invokedCount)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($invokedCount, $navigation): ContainerInterface | null {
                    match ($invokedCount->numberOfInvocations()) {
                        2 => self::assertNull($containerParam),
                        default => self::assertSame($navigation, $containerParam),
                    };

                    return match ($invokedCount->numberOfInvocations()) {
                        2 => null,
                        default => $navigation,
                    };
                },
            );

        $escapePlugin = $this->createMock(EscapeHtml::class);
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::never())
            ->method('render');

        $translatePlugin = $this->createMock(Translate::class);
        $translatePlugin->expects(self::never())
            ->method('__invoke');

        $breadcrumbs = new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapePlugin,
            renderer: $renderer,
            translator: $translatePlugin,
        );

        $breadcrumbs->setContainer($navigation);

        self::assertSame('', $breadcrumbs->render());
    }
}
