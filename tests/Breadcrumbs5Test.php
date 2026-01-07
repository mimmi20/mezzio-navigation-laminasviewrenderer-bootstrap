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
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Breadcrumbs;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Mimmi20\Mezzio\Navigation\Page\Uri;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

use const PHP_EOL;

final class Breadcrumbs5Test extends TestCase
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
    public function testRenderPartialWithArrayPartialRenderingPage(): void
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
        $subPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $subPage->expects(self::once())
            ->method('getResource')
            ->willReturn(null);
        $subPage->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(null);
        $subPage->expects(self::exactly(2))
            ->method('getParent')
            ->willReturn($parentPage);
        $subPage->expects(self::once())
            ->method('isActive')
            ->with(false)
            ->willReturn(true);

        assert($subPage instanceof PageInterface);
        $page->addPage($subPage);
        $parentPage->addPage($page);

        $role = 'testRole';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        $htmlify = $this->getMockBuilder(HtmlifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher         = self::exactly(3);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($matcher, $parentPage): ContainerInterface | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertNull($containerParam),
                        default => self::assertSame($parentPage, $containerParam),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        default => $parentPage,
                    };
                },
            );

        $escapePlugin = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $expected  = 'renderedPartial';
        $partial   = 'testPartial';
        $seperator = '/';

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                ['pages' => [$parentPage, $subPage], 'separator' => $seperator, 'layout' => false],
            )
            ->willReturn($expected);

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::never())
            ->method('__invoke');

        $helper = new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapePlugin,
            renderer: $renderer,
            translator: $translatePlugin,
        );

        $helper->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $helper->setSeparator($seperator);
        $helper->setLinkLast(true);
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

        self::assertSame($expected, $helper->renderPartial(null, [$partial, 'test']));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderStraightNoActivePage(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

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
            ->method('isActive');
        $page->expects(self::never())
            ->method('getParent');

        $container = new Navigation();
        $container->addPage($page);

        $role = 'testRole';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        $htmlify = $this->getMockBuilder(HtmlifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlify->expects(self::never())
            ->method('toHtml');

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher         = self::exactly(2);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | string | null $containerParam = null) use ($matcher, $name, $container): ContainerInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($name, $containerParam),
                        default => self::assertSame($container, $containerParam),
                    };

                    return $container;
                },
            );

        $escapePlugin = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::never())
            ->method('__invoke');

        $helper = new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapePlugin,
            renderer: $renderer,
            translator: $translatePlugin,
        );

        $helper->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $expected  = '';
        $partial   = 'testPartial';
        $seperator = '/';

        $helper->setSeparator($seperator);
        $helper->setLinkLast(true);
        $helper->setPartial($partial);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        self::assertSame($expected, $helper->renderStraight($name));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     */
    public function testRenderStraight(): void
    {
        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);
        $parentPage->setId('parent-id');
        $parentPage->setClass('parent-class');
        $parentPage->setUri('##');
        $parentPage->setTarget('self');
        $parentPage->setLabel('parent-label');
        $parentPage->setTitle('parent-title');
        $parentPage->setTextDomain('parent-text-domain');

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(null);
        $page->expects(self::exactly(2))
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::exactly(2))
            ->method('isActive')
            ->with(false)
            ->willReturn(true);
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
        $page->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);

        $parentPage->addPage($page);

        $container = new Navigation();
        $container->addPage($parentPage);

        $role = 'testRole';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';

        $htmlify = $this->getMockBuilder(HtmlifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = self::exactly(2);
        $htmlify->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (
                    string $prefix,
                    PageInterface $pageParam,
                    bool $escapeLabel = true,
                    bool $addClassToListItem = false,
                    array $attributes = [],
                    bool $convertToButton = false,
                ) use (
                    $matcher,
                    $page,
                    $parentPage,
                    $expected2,
                    $expected1,
                ): string {
                    self::assertSame(Breadcrumbs::class, $prefix);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($page, $pageParam),
                        default => self::assertSame($parentPage, $pageParam),
                    };

                    self::assertTrue($escapeLabel);
                    self::assertFalse($addClassToListItem);
                    self::assertSame([], $attributes);
                    self::assertFalse($convertToButton);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expected2,
                        default => $expected1,
                    };
                },
            );

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher         = self::exactly(2);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | string | null $containerParam = null) use ($matcher, $name, $container): ContainerInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($name, $containerParam),
                        default => self::assertSame($container, $containerParam),
                    };

                    return $container;
                },
            );

        $escapePlugin = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapePlugin->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::never())
            ->method('__invoke');

        $helper = new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapePlugin,
            renderer: $renderer,
            translator: $translatePlugin,
        );

        $helper->setRoles([$role]);

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $expected  = '<nav aria-label="breadcrumb">'
            . PHP_EOL . '<ul class="breadcrumb">'
            . PHP_EOL . '<li class="breadcrumb-item">'
            . PHP_EOL . '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>'
            . PHP_EOL . '</li>'
            . PHP_EOL . '/'
            . PHP_EOL . '<li class="breadcrumb-item active" aria-current="page">'
            . PHP_EOL . '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>'
            . PHP_EOL . '</li>'
            . PHP_EOL . '</ul>'
            . PHP_EOL . '</nav>'
            . PHP_EOL;
        $seperator = '/';

        $helper->setSeparator($seperator);
        $helper->setLinkLast(true);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        self::assertSame($expected, $helper->renderStraight($name));
    }
}
