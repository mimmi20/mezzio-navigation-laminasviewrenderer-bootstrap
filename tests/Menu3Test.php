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
use Mezzio\LaminasView\LaminasViewRenderer;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\ContainerParserInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlifyInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\Mezzio\Navigation\Page\Uri;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class Menu3Test extends TestCase
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

        self::assertSame($expected, $menu->findActive($name, $minDepth, $maxDepth));
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

        $menu->setMinDepth(-1);
        $menu->setMaxDepth($maxDepth);

        $expected = [];

        self::assertSame($expected, $menu->findActive($name));
    }

    /** @throws Exception */
    public function testEscapeLabels(): void
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

        self::assertTrue($menu->getEscapeLabels());

        $menu->escapeLabels(flag: false);

        self::assertFalse($menu->getEscapeLabels());
    }

    /** @throws Exception */
    public function testSetAddClassToListItem(): void
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

        self::assertFalse($menu->getAddClassToListItem());

        $menu->setAddClassToListItem(flag: true);

        self::assertTrue($menu->getAddClassToListItem());
    }

    /** @throws Exception */
    public function testSetOnlyActiveBranch(): void
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

        self::assertFalse($menu->getOnlyActiveBranch());

        $menu->setOnlyActiveBranch(flag: true);

        self::assertTrue($menu->getOnlyActiveBranch());
    }

    /** @throws Exception */
    public function testSetPartial(): void
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

        self::assertNull($menu->getPartial());

        $menu->setPartial('test');

        self::assertSame('test', $menu->getPartial());

        $menu->setPartial(1);

        self::assertSame('test', $menu->getPartial());
    }

    /** @throws Exception */
    public function testSetRenderParents(): void
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

        self::assertTrue($menu->getRenderParents());

        $menu->setRenderParents(flag: false);

        self::assertFalse($menu->getRenderParents());
    }

    /** @throws Exception */
    public function testSetUlClass(): void
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

        self::assertSame('navigation', $menu->getUlClass());

        $menu->setUlClass('test');

        self::assertSame('test', $menu->getUlClass());
    }
}
