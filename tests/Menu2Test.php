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
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Renderer\PhpRenderer;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\ContainerParserInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlifyInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class Menu2Test extends TestCase
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
     * @throws ExceptionInterface
     */
    public function testHtmlify(): void
    {
        $expected = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped">testLabelTranslatedAndEscaped</a>';

        $container = self::createStub(ContainerInterface::class);
        $name      = 'Mimmi20\Mezzio\Navigation\Top';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page = $this->createMock(PageInterface::class);
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($pageLabel);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($pageTitle);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($pageTextDomain);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($pageId);
        $page->expects(self::never())
            ->method('getClass');
        $page->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($pageHref);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($pageTarget);
        $page->expects(self::never())
            ->method('hasPage');
        $page->expects(self::never())
            ->method('hasPages');
        $page->expects(self::never())
            ->method('getLiClass');
        $page->expects(self::never())
            ->method('hashCode')
            ->willReturn('page');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($container);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($pageLabelTranslated)
            ->willReturn($pageLabelTranslatedEscaped);

        $renderer = $this->createMock(LaminasViewRenderer::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator   = $this->createMock(Translate::class);
        $invokedCount = self::exactly(2);
        $translator->expects($invokedCount)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($invokedCount, $pageLabel, $pageTitle, $pageTextDomain, $pageLabelTranslated, $pageTitleTranslated): string {
                    $invocation = $invokedCount->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($pageLabel, $message, (string) $invocation),
                        default => self::assertSame($pageTitle, $message, (string) $invocation),
                    };

                    self::assertSame($pageTextDomain, $textDomain, (string) $invocation);
                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $pageLabelTranslated,
                        default => $pageTitleTranslated,
                    };
                },
            );

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                $pageLabelTranslatedEscaped,
            )
            ->willReturn($expected);

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

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $menu->setView($view);

        assert($page instanceof PageInterface);
        self::assertSame($expected, $menu->htmlify($page));
    }

    /** @throws Exception */
    public function testSetIndent(): void
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

        self::assertSame('', $menu->getIndent());

        $menu->setIndent(1);

        self::assertSame(' ', $menu->getIndent());

        $menu->setIndent('    ');

        self::assertSame('    ', $menu->getIndent());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testFindActiveNoActivePages(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $parentPage = $this->createMock(PageInterface::class);
        $parentPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(value: false);
        $parentPage->expects(self::never())
            ->method('getResource');
        $parentPage->expects(self::never())
            ->method('getPrivilege');
        $parentPage->expects(self::never())
            ->method('getParent');
        $parentPage->expects(self::never())
            ->method('isActive');

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
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::never())
            ->method('isActive');

        $navigation = new Navigation();
        $navigation->addPage($page);

        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 0;

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($navigation);

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

        $menu->setRoles([$role]);
        $menu->setUseAuthorization();

        assert($auth instanceof AuthorizationInterface);
        $menu->setAuthorization($auth);

        self::assertSame([], $menu->findActive($name, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testFindActiveOneActivePage(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $parentPage = $this->createMock(PageInterface::class);
        $parentPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(value: true);
        $parentPage->expects(self::once())
            ->method('getResource')
            ->willReturn(value: null);
        $parentPage->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(value: null);
        $parentPage->expects(self::once())
            ->method('getParent')
            ->willReturn(value: null);
        $parentPage->expects(self::never())
            ->method('isActive');

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
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::once())
            ->method('isActive')
            ->with(false)
            ->willReturn(value: true);

        $navigation = new Navigation();
        $navigation->addPage($page);

        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 0;

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($navigation);

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

        $menu->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $menu->setAuthorization($auth);

        $expected = [
            'page' => $page,
            'depth' => 0,
        ];

        self::assertSame($expected, $menu->findActive($name, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testFindActiveWithoutContainer(): void
    {
        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 0;

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with(null)
            ->willReturn(value: null);

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

        $menu->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $menu->setAuthorization($auth);

        $expected = [];

        self::assertSame(
            $expected,
            $menu->findActive(container: null, minDepth: $minDepth, maxDepth: $maxDepth),
        );
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testFindActiveOneActivePageOutOfRange(): void
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
            ->method('getParent');
        $page->expects(self::never())
            ->method('isActive');

        $navigation = new Navigation();
        $navigation->addPage($page);

        $role = 'testRole';

        $auth = $this->createMock(AuthorizationInterface::class);
        $auth->expects(self::never())
            ->method('isGranted');

        $containerParser = $this->createMock(ContainerParserInterface::class);
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($navigation);

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

        $menu->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $menu->setAuthorization($auth);

        $expected = [];

        self::assertSame($expected, $menu->findActive($name, 2, 42));
    }
}
