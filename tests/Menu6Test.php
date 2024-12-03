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

use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Helper\Escaper\AbstractHelper;
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Mimmi20\Mezzio\Navigation\Page\Uri;
use Mimmi20\NavigationHelper\Accept\AcceptHelperInterface;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\FindActive\FindActiveInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

use const PHP_EOL;

final class Menu6Test extends TestCase
{
    /** @throws void */
    #[Override]
    protected function tearDown(): void
    {
        Menu::setDefaultAuthorization(null);
        Menu::setDefaultRole(null);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderVerticalMenuException(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel      = 'parent-label';
        $parentTextDomain = 'parent-text-domain';
        $parentTitle      = 'parent-title';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);
        $parentPage->setId('parent-id');
        $parentPage->setClass('parent-class');
        $parentPage->setUri('##');
        $parentPage->setTarget('self');
        $parentPage->setLabel($parentLabel);
        $parentPage->setTitle($parentTitle);
        $parentPage->setTextDomain($parentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $parentPage->addPage($page);

        $container = new Navigation();
        $container->addPage($parentPage);

        $role = 'testRole';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceLocator->expects(self::never())
            ->method('has');
        $serviceLocator->expects(self::never())
            ->method('get');
        $serviceLocator->expects(self::never())
            ->method('build');

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with($name)
            ->willReturn($container);

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $htmlify = $this->getMockBuilder(HtmlifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlify->expects(self::never())
            ->method('toHtml');

        $helper = new Menu(
            $serviceLocator,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setRole($role);

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Size "xy" does not exist');
        $this->expectExceptionCode(0);

        $helper->renderMenu($name, ['vertical' => 'xy']);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderVerticalMenu(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);
        $parentPage->setId('parent-id');
        $parentPage->setClass('parent-class');
        $parentPage->setUri('##');
        $parentPage->setTarget('self');
        $parentPage->setLabel($parentLabel);
        $parentPage->setTitle($parentTitle);
        $parentPage->setTextDomain($parentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(false);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(2))
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($pageLabel);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($pageTextDomain);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($pageTitle);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($pageId);
        $page->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx');
        $page->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($pageHref);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($pageTarget);
        $page->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page->expects($matcher)
            ->method('hasPages')
            ->willReturnCallback(
                static function (bool $onlyVisible = false) use ($matcher): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertFalse($onlyVisible, (string) $invocation),
                        default => self::assertTrue($onlyVisible, (string) $invocation),
                    };

                    return false;
                },
            );
        $page->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('hashCode')
            ->willReturn('page');

        $parentPage->addPage($page);

        $container = new Navigation();
        $container->addPage($parentPage);

        $role = 'testRole';

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, 0, null)
            ->willReturn(
                [
                    'page' => $page,
                    'depth' => 1,
                ],
            );

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher      = self::exactly(2);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentPage, $page): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentPage, $pageParam, (string) $invocation),
                        default => self::assertSame($page, $pageParam, (string) $invocation),
                    };

                    self::assertTrue($recursive, (string) $invocation);

                    return true;
                },
            );

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceLocator->expects(self::never())
            ->method('has');
        $serviceLocator->expects(self::never())
            ->method('get');
        $matcher = self::exactly(3);
        $serviceLocator->expects($matcher)
            ->method('build')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $auth, $role, $findActiveHelper, $acceptHelper): mixed {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(FindActiveInterface::class, $name, (string) $invocation),
                        default => self::assertSame(AcceptHelperInterface::class, $name),
                    };

                    self::assertSame(
                        [
                            'authorization' => $auth,
                            'renderInvisible' => false,
                            'role' => $role,
                        ],
                        $options,
                        (string) $invocation,
                    );

                    return match ($invocation) {
                        1 => $findActiveHelper,
                        default => $acceptHelper,
                    };
                },
            );

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher         = self::exactly(2);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | string | null $containerParam = null) use ($matcher, $name, $container): ContainerInterface {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($name, $containerParam, (string) $invocation),
                        default => self::assertSame($container, $containerParam, (string) $invocation),
                    };

                    return $container;
                },
            );

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher        = self::exactly(5);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            'nav navigation flex-column flex-md-row',
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame('nav-item active', $value, (string) $invocation),
                        3 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped flex-column-escaped flex-md-row-escaped',
                        2 => 'nav-item-escaped active-escaped',
                        3 => 'dropdown-menu-escaped',
                        4 => 'parent-id-escaped',
                        default => 'active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(2);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentTranslatedLabel, $pageLabelTranslated, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        default => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentTranslatedLabelEscaped,
                        default => $pageLabelTranslatedEscaped,
                    };
                },
            );

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(4);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentTitle, $message, (string) $invocation),
                        3 => self::assertSame($pageLabel, $message, (string) $invocation),
                        default => self::assertSame($pageTitle, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1,2 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentTranslatedLabel,
                        2 => $parentTranslatedTitle,
                        3 => $pageLabelTranslated,
                        default => $pageTitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped navigation-escaped flex-column-escaped flex-md-row-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(2);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentTranslatedTitle, $pageId, $pageTitleTranslated, $pageHref, $pageTarget, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $expected1, $expected2): string {
                    $invocation = $matcher->numberOfInvocations();

                    self::assertSame('a', $element, (string) $invocation);

                    match ($invocation) {
                        1 => self::assertSame(
                            ['aria-current' => 'page', 'class' => 'nav-link btn parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle, 'href' => '##', 'target' => 'self'],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        default => $expected2,
                    };
                },
            );

        $htmlify = $this->getMockBuilder(HtmlifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlify->expects(self::never())
            ->method('toHtml');

        $helper = new Menu(
            $serviceLocator,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setRole($role);

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        self::assertSame($expected, $helper->renderMenu($name, ['vertical' => 'md']));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderVerticalMenu2(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);
        $parentPage->setId('parent-id');
        $parentPage->setClass('parent-class');
        $parentPage->setUri('##');
        $parentPage->setTarget('self');
        $parentPage->setLabel($parentLabel);
        $parentPage->setTitle($parentTitle);
        $parentPage->setTextDomain($parentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(false);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(2))
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($pageLabel);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($pageTextDomain);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($pageTitle);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($pageId);
        $page->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx');
        $page->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($pageHref);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($pageTarget);
        $page->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page->expects($matcher)
            ->method('hasPages')
            ->willReturnCallback(
                static function (bool $onlyVisible = false) use ($matcher): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertFalse($onlyVisible, (string) $invocation),
                        default => self::assertTrue($onlyVisible, (string) $invocation),
                    };

                    return false;
                },
            );
        $page->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('hashCode')
            ->willReturn('page');

        $parentPage->addPage($page);

        $container = new Navigation();
        $container->addPage($parentPage);

        $role = 'testRole';

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, 0, null)
            ->willReturn(
                [
                    'page' => $page,
                    'depth' => 1,
                ],
            );

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher      = self::exactly(2);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentPage, $page): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentPage, $pageParam, (string) $invocation),
                        default => self::assertSame($page, $pageParam, (string) $invocation),
                    };

                    self::assertTrue($recursive, (string) $invocation);

                    return true;
                },
            );

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceLocator->expects(self::never())
            ->method('has');
        $serviceLocator->expects(self::never())
            ->method('get');
        $matcher = self::exactly(3);
        $serviceLocator->expects($matcher)
            ->method('build')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $auth, $role, $findActiveHelper, $acceptHelper): mixed {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(FindActiveInterface::class, $name, (string) $invocation),
                        default => self::assertSame(
                            AcceptHelperInterface::class,
                            $name,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(
                        [
                            'authorization' => $auth,
                            'renderInvisible' => false,
                            'role' => $role,
                        ],
                        $options,
                        (string) $invocation,
                    );

                    return match ($invocation) {
                        1 => $findActiveHelper,
                        default => $acceptHelper,
                    };
                },
            );

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher         = self::exactly(2);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | string | null $containerParam = null) use ($matcher, $name, $container): ContainerInterface {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($name, $containerParam, (string) $invocation),
                        default => self::assertSame($container, $containerParam, (string) $invocation),
                    };

                    return $container;
                },
            );

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher        = self::exactly(5);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            'nav navigation flex-column flex-md-row',
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame('nav-item active', $value, (string) $invocation),
                        3 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped flex-column-escaped flex-md-row-escaped',
                        2 => 'nav-item-escaped active-escaped',
                        3 => 'dropdown-menu-escaped',
                        4 => 'parent-id-escaped',
                        default => 'active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(2);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentTranslatedLabel, $pageLabelTranslated, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        default => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentTranslatedLabelEscaped,
                        default => $pageLabelTranslatedEscaped,
                    };
                },
            );

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(4);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentTitle, $message, (string) $invocation),
                        3 => self::assertSame($pageLabel, $message, (string) $invocation),
                        default => self::assertSame($pageTitle, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1,2 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentTranslatedLabel,
                        2 => $parentTranslatedTitle,
                        3 => $pageLabelTranslated,
                        default => $pageTitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped navigation-escaped flex-column-escaped flex-md-row-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(2);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentTranslatedTitle, $pageId, $pageTitleTranslated, $pageHref, $pageTarget, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $expected1, $expected2): string {
                    $invocation = $matcher->numberOfInvocations();

                    self::assertSame('a', $element, (string) $invocation);

                    match ($invocation) {
                        1 => self::assertSame(
                            ['aria-current' => 'page', 'class' => 'nav-link btn parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle, 'href' => '##', 'target' => 'self'],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        default => $expected2,
                    };
                },
            );

        $htmlify = $this->getMockBuilder(HtmlifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlify->expects(self::never())
            ->method('toHtml');

        $helper = new Menu(
            $serviceLocator,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setRole($role);

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        self::assertSame(
            $expected,
            $helper->renderMenu(
                $name,
                ['vertical' => 'md', 'direction' => Menu::DROP_ORIENTATION_START],
            ),
        );
    }
}
