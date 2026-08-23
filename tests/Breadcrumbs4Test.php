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
use Mimmi20\Mezzio\Navigation\Page\Uri;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class Breadcrumbs4Test extends TestCase
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
    public function testRenderPartialWithParamsAndArrayPartialRenderingPage(): void
    {
        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(visible: true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);
        $parentPage->setActive(active: true);

        $page = new Uri();
        $page->setVisible(visible: true);
        $page->setResource($resource);
        $page->setPrivilege($privilege);
        $page->setActive(active: true);

        $subPage = $this->createMock(PageInterface::class);
        $subPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(value: true);
        $subPage->expects(self::once())
            ->method('getResource')
            ->willReturn(value: null);
        $subPage->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(value: null);
        $subPage->expects(self::exactly(2))
            ->method('getParent')
            ->willReturn($parentPage);
        $subPage->expects(self::once())
            ->method('isActive')
            ->with(false)
            ->willReturn(value: true);

        assert($subPage instanceof PageInterface);
        $page->addPage($subPage);
        $parentPage->addPage($page);

        $role = 'testRole';

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $invokedCount    = self::exactly(3);
        $containerParser->expects($invokedCount)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($invokedCount, $parentPage): ContainerInterface | null {
                    match ($invokedCount->numberOfInvocations()) {
                        2 => self::assertNull($containerParam),
                        default => self::assertSame($parentPage, $containerParam),
                    };

                    return match ($invokedCount->numberOfInvocations()) {
                        2 => null,
                        default => $parentPage,
                    };
                },
            );

        $escapePlugin = $this->createMock(EscapeHtml::class);
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $partial   = 'testPartial';
        $expected  = 'renderedPartial';
        $seperator = '/';

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                ['pages' => [$parentPage, $subPage], 'separator' => $seperator, 'abc' => 'test', 'layout' => false],
            )
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
        $breadcrumbs->setContainer($parentPage);

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $breadcrumbs->setView($view);

        self::assertSame(
            $expected,
            $breadcrumbs->renderPartialWithParams(['abc' => 'test'], partial: [$partial, 'test']),
        );
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderPartialWithParamsNoActivePage(): void
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

        $partial   = 'testPartial';
        $expected  = 'renderedPartial';
        $seperator = '/';

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                ['pages' => [], 'separator' => $seperator, 'abc' => 'test', 'layout' => false],
            )
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

        self::assertSame($expected, $breadcrumbs->renderPartialWithParams(['abc' => 'test'], $name));
    }

    /** @throws ExceptionInterface */
    public function testRenderPartialWithoutPartial(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

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

        $role = 'testRole';

        $breadcrumbs->setRoles([$role]);

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        assert($auth instanceof AuthorizationInterface);
        $breadcrumbs->setAuthorization($auth);

        $breadcrumbs->setSeparator('/');
        $breadcrumbs->setLinkLast(linkLast: true);

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');

        assert($view instanceof PhpRenderer);
        $breadcrumbs->setView($view);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to render breadcrumbs: No partial view script provided');
        $this->expectExceptionCode(0);

        $breadcrumbs->renderPartial($name);
    }

    /** @throws ExceptionInterface */
    public function testRenderPartialWithWrongPartial(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::never())
            ->method('parseContainer');

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

        $role = 'testRole';

        $breadcrumbs->setRoles([$role]);

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        assert($auth instanceof AuthorizationInterface);
        $breadcrumbs->setAuthorization($auth);

        $breadcrumbs->setSeparator('/');
        $breadcrumbs->setLinkLast(linkLast: true);

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');

        assert($view instanceof PhpRenderer);
        $breadcrumbs->setView($view);

        $breadcrumbs->setPartial(['a', 'b', 'c']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unable to render breadcrumbs: A view partial supplied as an array must contain one value: the partial view script',
        );
        $this->expectExceptionCode(0);

        $breadcrumbs->renderPartial($name);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderPartial(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $uri = new Uri();
        $uri->setVisible(visible: true);
        $uri->setResource($resource);
        $uri->setPrivilege($privilege);

        $page = $this->createMock(PageInterface::class);
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(value: true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn(value: null);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(value: null);
        $page->expects(self::exactly(2))
            ->method('getParent')
            ->willReturn($uri);
        $page->expects(self::once())
            ->method('isActive')
            ->with(false)
            ->willReturn(value: true);

        $uri->addPage($page);

        $navigation = new Navigation();
        $navigation->addPage($uri);

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

        $partial   = 'testPartial';
        $expected  = 'renderedPartial';
        $seperator = '/';

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                ['pages' => [$uri, $page], 'separator' => $seperator, 'layout' => false],
            )
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

        self::assertSame($expected, $breadcrumbs->renderPartial($name));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderPartialNoActivePage(): void
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

        $partial   = 'testPartial';
        $expected  = 'renderedPartial';
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

        self::assertSame($expected, $breadcrumbs->renderPartial($name));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderPartialWithArrayPartial(): void
    {
        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $uri = new Uri();
        $uri->setVisible(visible: true);
        $uri->setResource($resource);
        $uri->setPrivilege($privilege);

        $page = $this->createMock(PageInterface::class);
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(value: true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn(value: null);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(value: null);
        $page->expects(self::exactly(2))
            ->method('getParent')
            ->willReturn($uri);
        $page->expects(self::once())
            ->method('isActive')
            ->with(false)
            ->willReturn(value: true);

        $uri->addPage($page);

        $navigation = new Navigation();
        $navigation->addPage($uri);

        $role = 'testRole';

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

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

        $partial   = 'testPartial';
        $expected  = 'renderedPartial';
        $seperator = '/';

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                ['pages' => [$uri, $page], 'separator' => $seperator, 'layout' => false],
            )
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
        $breadcrumbs->setContainer($navigation);

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $breadcrumbs->setView($view);

        self::assertSame($expected, $breadcrumbs->renderPartial(partial: [$partial, 'test']));
    }
}
