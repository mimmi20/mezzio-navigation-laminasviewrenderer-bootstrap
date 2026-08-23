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

use AssertionError;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Renderer\PhpRenderer;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\ContainerParserInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlifyInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Mimmi20\Mezzio\Navigation\Page\Uri;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

use const PHP_EOL;

final class Menu14Test extends TestCase
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
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderMenu8(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $liActiveClass = 'li-active';
        $ulClass       = 'ul-class ul';

        $parentLabel      = 'parent-label';
        $parentTextDomain = 'parent-text-domain';
        $parentTitle      = 'parent-title';

        $parentParentLabel      = 'parent-parent-label';
        $parentParentTextDomain = 'parent-parent-text-domain';
        $parentParentTitle      = 'parent-parent-title';

        $parentPage = new Uri();
        $parentPage->setVisible(visible: true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);
        $parentPage->setId('parent-id');
        $parentPage->setClass('parent-class');
        $parentPage->setUri('##');
        $parentPage->setTarget('self');
        $parentPage->setLabel($parentLabel);
        $parentPage->setTitle($parentTitle);
        $parentPage->setTextDomain($parentTextDomain);

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(visible: true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

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
        $page->expects(self::never())
            ->method('getLabel');
        $page->expects(self::never())
            ->method('getTextDomain');
        $page->expects(self::never())
            ->method('getTitle');
        $page->expects(self::never())
            ->method('getId');
        $page->expects(self::never())
            ->method('getClass');
        $page->expects(self::never())
            ->method('getHref');
        $page->expects(self::never())
            ->method('getTarget');
        $page->expects(self::never())
            ->method('hasPage');
        $page->expects(self::never())
            ->method('hasPages');
        $page->expects(self::never())
            ->method('getLiClass');
        $page->expects(self::once())
            ->method('hashCode')
            ->willReturn('page');

        $page2 = $this->createMock(PageInterface::class);
        $page2->expects(self::never())
            ->method('isVisible');
        $page2->expects(self::never())
            ->method('getResource');
        $page2->expects(self::never())
            ->method('getPrivilege');
        $page2->expects(self::never())
            ->method('getParent');
        $page2->expects(self::never())
            ->method('isActive');
        $page2->expects(self::never())
            ->method('getLabel');
        $page2->expects(self::never())
            ->method('getTextDomain');
        $page2->expects(self::never())
            ->method('getTitle');
        $page2->expects(self::never())
            ->method('getId');
        $page2->expects(self::never())
            ->method('getClass');
        $page2->expects(self::never())
            ->method('getHref');
        $page2->expects(self::never())
            ->method('getTarget');
        $page2->expects(self::never())
            ->method('hasPage');
        $page2->expects(self::never())
            ->method('hasPages');
        $page2->expects(self::never())
            ->method('getLiClass');
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3 = $this->createMock(PageInterface::class);
        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::never())
            ->method('isActive');
        $page3->expects(self::never())
            ->method('getLabel');
        $page3->expects(self::never())
            ->method('getTextDomain');
        $page3->expects(self::never())
            ->method('getTitle');
        $page3->expects(self::never())
            ->method('getId');
        $page3->expects(self::never())
            ->method('getClass');
        $page3->expects(self::never())
            ->method('getHref');
        $page3->expects(self::never())
            ->method('getTarget');
        $page3->expects(self::never())
            ->method('hasPage');
        $page3->expects(self::never())
            ->method('hasPages');
        $page3->expects(self::never())
            ->method('getLiClass');
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $navigation = new Navigation();
        $navigation->addPage($parentParentPage);

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

        $expected = '<ul class="nav-escaped ul-class-escaped ul-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropup-escaped li-active-escaped">' . PHP_EOL . '        <details>' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropup-escaped li-active-escaped">' . PHP_EOL . '                <details>' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="li-active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '                </details>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '        </details>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

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

        $view = $this->createMock(PhpRenderer::class);
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $menu->setView($view);

        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage(
            'assert(is_int($options[\'maxDepth\']) || $options[\'maxDepth\'] === null)',
        );
        $this->expectExceptionCode(1);

        self::assertSame(
            $expected,
            $menu->renderMenu(
                $name,
                ['direction' => Menu::DROP_ORIENTATION_UP, 'sublink' => Menu::STYLE_SUBLINK_DETAILS, 'ulClass' => $ulClass, 'liActiveClass' => $liActiveClass, 'maxDepth' => true],
            ),
        );
    }
}
