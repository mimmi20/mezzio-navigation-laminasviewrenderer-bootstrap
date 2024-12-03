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
use Laminas\View\Exception\ExceptionInterface;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Helper\Escaper\AbstractHelper;
use Laminas\View\Model\ModelInterface;
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
use function sprintf;

use const PHP_EOL;

final class Menu8Test extends TestCase
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
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderWithPartialModel(): void
    {
        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);
        $parentPage->setActive(true);

        $page = new Uri();
        $page->setVisible(true);
        $page->setResource($resource);
        $page->setPrivilege($privilege);
        $page->setActive(true);

        $subPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $subPage->expects(self::never())
            ->method('isVisible');
        $subPage->expects(self::never())
            ->method('getResource');
        $subPage->expects(self::never())
            ->method('getPrivilege');
        $subPage->expects(self::never())
            ->method('getParent');
        $subPage->expects(self::never())
            ->method('isActive');
        $subPage->expects(self::never())
            ->method('getLabel');
        $subPage->expects(self::never())
            ->method('getTextDomain');
        $subPage->expects(self::never())
            ->method('getTitle');
        $subPage->expects(self::never())
            ->method('getId');
        $subPage->expects(self::never())
            ->method('getClass');
        $subPage->expects(self::never())
            ->method('getHref');
        $subPage->expects(self::never())
            ->method('getTarget');
        $subPage->expects(self::never())
            ->method('hasPage');
        $subPage->expects(self::never())
            ->method('hasPages');
        $subPage->expects(self::never())
            ->method('getLiClass');
        $subPage->expects(self::once())
            ->method('hashCode')
            ->willReturn('sub-page');

        assert(
            $subPage instanceof PageInterface,
            sprintf(
                '$subPage should be an Instance of %s, but was %s',
                PageInterface::class,
                $subPage::class,
            ),
        );
        $page->addPage($subPage);
        $parentPage->addPage($page);

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
        $matcher         = self::exactly(2);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($matcher, $parentPage): ContainerInterface | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        2 => self::assertNull($containerParam, (string) $invocation),
                        default => self::assertSame($parentPage, $containerParam, (string) $invocation),
                    };

                    return match ($invocation) {
                        2 => null,
                        default => $parentPage,
                    };
                },
            );

        $escapePlugin = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $expected = 'renderedPartial';
        $data     = ['container' => $parentPage];

        $model = $this->getMockBuilder(ModelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects(self::never())
            ->method('setVariables');
        $model->expects(self::never())
            ->method('getTemplate');

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
        $renderer->expects(self::once())
            ->method('render')
            ->with($model, $data)
            ->willReturn($expected);

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

        $helper->setContainer($parentPage);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);
        $helper->setPartial($model);

        self::assertSame($expected, $helper->render());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParents(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel      = 'parent-label';
        $parentTextDomain = 'parent-text-domain';
        $parentTitle      = 'parent-title';

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
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::once())
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
        $page->expects(self::once())
            ->method('hasPages')
            ->with(true)
            ->willReturn(false);
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
            ->with($container, -1, null)
            ->willReturn(
                [
                    'page' => $page,
                    'depth' => 1,
                ],
            );

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::once())
            ->method('accept')
            ->with($page)
            ->willReturn(true);

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
        $matcher = self::exactly(2);
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
        $matcher        = self::exactly(4);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav-item active', $value, (string) $invocation),
                        2 => self::assertSame('presentation', $value, (string) $invocation),
                        3 => self::assertSame(
                            'navbar-nav navigation nav-tabs',
                            $value,
                            (string) $invocation,
                        ),
                        default => self::assertSame('tablist', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-item-escaped active-escaped',
                        2 => 'presentation-escaped',
                        3 => 'navbar-nav-escaped navigation-escaped nav-tabs-escaped',
                        default => 'tablist-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($pageLabelTranslated)
            ->willReturn($pageLabelTranslatedEscaped);

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $pageLabel, $pageTitle, $pageTextDomain, $pageLabelTranslated, $pageTitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

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

        $expected = '<ul class="navbar-nav-escaped navigation-escaped nav-tabs-escaped" role="tablist-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped" role="presentation-escaped">' . PHP_EOL . '        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['aria-current' => 'page', 'class' => 'nav-link btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget, 'role' => 'tab'],
                $pageLabelTranslatedEscaped,
            )
            ->willReturn($expected2);

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
                ['tabs' => true, 'dark' => true, 'in-navbar' => true, 'onlyActiveBranch' => true, 'renderParents' => false],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParentsWithIndent(): void
    {
        $indent = '    ';

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel      = 'parent-label';
        $parentTextDomain = 'parent-text-domain';
        $parentTitle      = 'parent-title';

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
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::once())
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
        $page->expects(self::once())
            ->method('hasPages')
            ->with(true)
            ->willReturn(false);
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
            ->with($container, -1, null)
            ->willReturn(
                [
                    'page' => $page,
                    'depth' => 1,
                ],
            );

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::once())
            ->method('accept')
            ->with($page)
            ->willReturn(true);

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
        $matcher = self::exactly(2);
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
        $matcher        = self::exactly(4);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav-item active', $value, (string) $invocation),
                        2 => self::assertSame('presentation', $value, (string) $invocation),
                        3 => self::assertSame(
                            'navbar-nav navigation nav-tabs',
                            $value,
                            (string) $invocation,
                        ),
                        default => self::assertSame('tablist', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-item-escaped active-escaped',
                        2 => 'presentation-escaped',
                        3 => 'navbar-nav-escaped navigation-escaped nav-tabs-escaped',
                        default => 'tablist-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($pageLabelTranslated)
            ->willReturn($pageLabelTranslatedEscaped);

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $pageLabel, $pageTitle, $pageTextDomain, $pageLabelTranslated, $pageTitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

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

        $expected = $indent . '<ul class="navbar-nav-escaped navigation-escaped nav-tabs-escaped" role="tablist-escaped">' . PHP_EOL . $indent . '    <li class="nav-item-escaped active-escaped" role="presentation-escaped">' . PHP_EOL . $indent . '        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '    </li>' . PHP_EOL . $indent . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['aria-current' => 'page', 'class' => 'nav-link btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget, 'role' => 'tab'],
                $pageLabelTranslatedEscaped,
            )
            ->willReturn($expected2);

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
        $helper->setIndent($indent);

        self::assertSame(
            $expected,
            $helper->renderMenu(
                $name,
                ['tabs' => true, 'dark' => true, 'in-navbar' => true, 'onlyActiveBranch' => true, 'renderParents' => false],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Mimmi20\Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParentsWithIndent2(): void
    {
        $indent = '    ';

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

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, -1, null)
            ->willReturn(
                [
                    'page' => null,
                    'depth' => 1,
                ],
            );

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

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
        $serviceLocator->expects(self::once())
            ->method('build')
            ->with(
                FindActiveInterface::class,
                ['authorization' => $auth, 'renderInvisible' => false, 'role' => $role],
            )
            ->willReturn($findActiveHelper);

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

        $expected = '';

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
        $helper->setIndent($indent);

        self::assertSame(
            $expected,
            $helper->renderMenu(
                $name,
                ['tabs' => true, 'dark' => true, 'in-navbar' => true, 'onlyActiveBranch' => true, 'renderParents' => false],
            ),
        );
    }
}
