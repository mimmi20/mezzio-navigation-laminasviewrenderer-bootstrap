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

final class Breadcrumbs3Test extends TestCase
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
    public function testFindActiveOneActivePageRecursive2(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(visible: true);
        $parentPage->setActive(active: true);
        $parentPage->setUri('parent');
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);

        $page1 = new Uri();
        $page1->setActive(active: true);
        $page1->setUri('test1');

        $page2 = new Uri();
        $page2->setActive(active: true);
        $page1->setUri('test2');

        $parentPage->addPage($page1);
        $parentPage->addPage($page2);

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(visible: true);
        $parentParentPage->setActive(active: true);
        $parentParentPage->setUri('parentParent');

        $parentParentParentPage = new Uri();
        $parentParentParentPage->setVisible(visible: true);
        $parentParentParentPage->setActive(active: true);
        $parentParentParentPage->setUri('parentParentParent');

        $parentParentPage->addPage($parentPage);
        $parentParentParentPage->addPage($parentParentPage);

        $navigation = new Navigation();
        $navigation->addPage($parentParentParentPage);

        $role     = 'testRole';
        $maxDepth = 1;
        $minDepth = 2;

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($navigation);

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

        $breadcrumbs->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $breadcrumbs->setAuthorization($auth);

        $expected = [];

        self::assertSame($expected, $breadcrumbs->findActive($name, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testFindActiveOneActivePageRecursive3(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(visible: true);
        $parentPage->setActive(active: true);
        $parentPage->setUri('parent');
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);

        $page1 = new Uri();
        $page1->setActive(active: true);
        $page1->setUri('test1');

        $page2 = new Uri();
        $page2->setActive(active: true);
        $page1->setUri('test2');

        $parentPage->addPage($page1);
        $parentPage->addPage($page2);

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(visible: true);
        $parentParentPage->setActive(active: true);
        $parentParentPage->setUri('parentParent');

        $parentParentParentPage = new Uri();
        $parentParentParentPage->setVisible(visible: true);
        $parentParentParentPage->setActive(active: true);
        $parentParentParentPage->setUri('parentParentParent');

        $parentParentPage->addPage($parentPage);
        $parentParentParentPage->addPage($parentParentPage);

        $navigation = new Navigation();
        $navigation->addPage($parentParentParentPage);

        $role     = 'testRole';
        $maxDepth = -1;

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $htmlify = $this->createMock(HtmlifyInterface::class);
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($navigation);

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

        $breadcrumbs->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $breadcrumbs->setAuthorization($auth);

        $breadcrumbs->setMinDepth(-1);
        $breadcrumbs->setMaxDepth($maxDepth);

        $expected = [];

        self::assertSame($expected, $breadcrumbs->findActive($name));
    }

    /** @throws Exception */
    public function testSetPartial(): void
    {
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

        self::assertNull($breadcrumbs->getPartial());

        $breadcrumbs->setPartial('test');

        self::assertSame('test', $breadcrumbs->getPartial());

        $breadcrumbs->setPartial(1);

        self::assertSame('test', $breadcrumbs->getPartial());
    }

    /** @throws Exception */
    public function testSetLinkLast(): void
    {
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

        self::assertFalse($breadcrumbs->getLinkLast());

        $breadcrumbs->setLinkLast(linkLast: true);

        self::assertTrue($breadcrumbs->getLinkLast());

        $breadcrumbs->setLinkLast(linkLast: false);

        self::assertFalse($breadcrumbs->getLinkLast());
    }

    /** @throws Exception */
    public function testSetSeparator(): void
    {
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

        self::assertSame(' &gt; ', $breadcrumbs->getSeparator());

        $breadcrumbs->setSeparator('/');

        self::assertSame('/', $breadcrumbs->getSeparator());
    }

    /** @throws ExceptionInterface */
    public function testRenderPartialWithParamsWithoutPartial(): void
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

        $breadcrumbs->renderPartialWithParams(['abc' => 'test'], $name);
    }

    /** @throws ExceptionInterface */
    public function testRenderPartialWithParamsWithWrongPartial(): void
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

        $breadcrumbs->renderPartialWithParams(['abc' => 'test'], $name);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderPartialWithParams(): void
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
                ['abc' => 'test', 'pages' => [$uri, $page], 'separator' => $seperator, 'layout' => false],
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

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderPartialWithParamsAndArrayPartial(): void
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
                ['pages' => [$uri, $page], 'separator' => $seperator, 'abc' => 'test', 'layout' => false],
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

        self::assertSame(
            $expected,
            $breadcrumbs->renderPartialWithParams(['abc' => 'test'], partial: [$partial, 'test']),
        );
    }
}
