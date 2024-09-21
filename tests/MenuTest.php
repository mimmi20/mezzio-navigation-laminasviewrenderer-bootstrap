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

use AssertionError;
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
use Laminas\View\Renderer\RendererInterface;
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
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionProperty;

use function assert;
use function sprintf;

use const PHP_EOL;

final class MenuTest extends TestCase
{
    /** @throws void */
    protected function tearDown(): void
    {
        Menu::setDefaultAuthorization(null);
        Menu::setDefaultRole(null);
    }

    /** @throws Exception */
    public function testSetMaxDepth(): void
    {
        $maxDepth = 4;

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertNull($helper->getMaxDepth());

        $helper->setMaxDepth($maxDepth);

        self::assertSame($maxDepth, $helper->getMaxDepth());
    }

    /** @throws Exception */
    public function testSetMinDepth(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertSame(0, $helper->getMinDepth());

        $helper->setMinDepth(4);

        self::assertSame(4, $helper->getMinDepth());

        $helper->setMinDepth(-1);

        self::assertSame(0, $helper->getMinDepth());

        $helper->setMinDepth(0);

        self::assertSame(0, $helper->getMinDepth());

        $helper->setMinDepth(1);

        self::assertSame(1, $helper->getMinDepth());

        $helper->setMinDepth(4);

        self::assertSame(4, $helper->getMinDepth());
    }

    /** @throws Exception */
    public function testSetRenderInvisible(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertFalse($helper->getRenderInvisible());

        $helper->setRenderInvisible(true);

        self::assertTrue($helper->getRenderInvisible());
    }

    /** @throws Exception */
    public function testSetRole(): void
    {
        $role        = 'testRole';
        $defaultRole = 'testDefaultRole';

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertNull($helper->getRole());
        self::assertFalse($helper->hasRole());

        Menu::setDefaultRole($defaultRole);

        self::assertSame($defaultRole, $helper->getRole());
        self::assertTrue($helper->hasRole());

        $helper->setRole($role);

        self::assertSame($role, $helper->getRole());
        self::assertTrue($helper->hasRole());
    }

    /** @throws Exception */
    public function testSetUseAuthorization(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertTrue($helper->getUseAuthorization());

        $helper->setUseAuthorization(false);

        self::assertFalse($helper->getUseAuthorization());

        $helper->setUseAuthorization();

        self::assertTrue($helper->getUseAuthorization());
    }

    /** @throws Exception */
    public function testSetAuthorization(): void
    {
        $auth        = $this->createMock(AuthorizationInterface::class);
        $defaultAuth = $this->createMock(AuthorizationInterface::class);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertNull($helper->getAuthorization());
        self::assertFalse($helper->hasAuthorization());

        assert($defaultAuth instanceof AuthorizationInterface);
        Menu::setDefaultAuthorization($defaultAuth);

        self::assertSame($defaultAuth, $helper->getAuthorization());
        self::assertTrue($helper->hasAuthorization());

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        self::assertSame($auth, $helper->getAuthorization());
        self::assertTrue($helper->hasAuthorization());
    }

    /** @throws Exception */
    public function testSetView(): void
    {
        $view = $this->createMock(RendererInterface::class);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertNull($helper->getView());

        assert($view instanceof RendererInterface);
        $helper->setView($view);

        self::assertSame($view, $helper->getView());
        self::assertSame($serviceLocator, $helper->getServiceLocator());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testSetContainer(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
                static function (ContainerInterface | null $containerParam = null) use ($matcher, $container): ContainerInterface | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertNull($containerParam, (string) $invocation),
                        default => self::assertSame($container, $containerParam, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        default => $container,
                    };
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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $container1 = $helper->getContainer();

        self::assertInstanceOf(Navigation::class, $container1);

        $helper->setContainer();

        $container2 = $helper->getContainer();

        self::assertInstanceOf(Navigation::class, $container2);
        self::assertNotSame($container1, $container2);

        $helper->setContainer($container);

        self::assertSame($container, $helper->getContainer());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testSetContainerWithStringDefaultAndNavigationNotFound(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'default';

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
            ->willThrowException(new InvalidArgumentException('test'));

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('test');
        $this->expectExceptionCode(0);

        $helper->setContainer($name);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testSetContainerWithStringFound(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $container = $this->createMock(ContainerInterface::class);
        $name      = 'Mimmi20\Mezzio\Navigation\Top';

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setContainer($name);

        self::assertSame($container, $helper->getContainer());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testDoNotAccept(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $container = $this->createMock(ContainerInterface::class);
        $name      = 'Mimmi20\Mezzio\Navigation\Top';

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::once())
            ->method('accept')
            ->with($page)
            ->willReturn(false);

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $role = 'testRole';

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
                AcceptHelperInterface::class,
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($acceptHelper);

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setContainer($name);
        $helper->setRole($role);

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHtmlify(): void
    {
        $expected = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped">testLabelTranslatedAndEscaped</a>';

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $container = $this->createMock(ContainerInterface::class);
        $name      = 'Mimmi20\Mezzio\Navigation\Top';

        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceLocator->expects(self::never())
            ->method('has');
        $serviceLocator->expects(self::never())
            ->method('get');
        $serviceLocator->expects(self::never())
            ->method('build');

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

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

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                $pageLabelTranslatedEscaped,
            )
            ->willReturn($expected);

        $htmlify = $this->getMockBuilder(HtmlifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlify->expects(self::never())
            ->method('toHtml');

        $helper = new Menu(
            $serviceLocator,
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setContainer($name);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        assert($page instanceof PageInterface);
        self::assertSame($expected, $helper->htmlify($page));
    }

    /** @throws Exception */
    public function testSetIndent(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertSame('', $helper->getIndent());

        $helper->setIndent(1);

        self::assertSame(' ', $helper->getIndent());

        $helper->setIndent('    ');

        self::assertSame('    ', $helper->getIndent());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveNoActivePages(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::never())
            ->method('isVisible');
        $parentPage->expects(self::never())
            ->method('getResource');
        $parentPage->expects(self::never())
            ->method('getPrivilege');
        $parentPage->expects(self::never())
            ->method('getParent');
        $parentPage->expects(self::never())
            ->method('isActive');

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

        $container = new Navigation();
        $container->addPage($page);

        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 0;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, $minDepth, $maxDepth)
            ->willReturn([]);

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
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

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
            $logger,
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

        self::assertSame([], $helper->findActive($name, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveOneActivePage(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::never())
            ->method('isVisible');
        $parentPage->expects(self::never())
            ->method('getResource');
        $parentPage->expects(self::never())
            ->method('getPrivilege');
        $parentPage->expects(self::never())
            ->method('getParent');
        $parentPage->expects(self::never())
            ->method('isActive');

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

        $container = new Navigation();
        $container->addPage($page);

        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 0;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, $minDepth, $maxDepth)
            ->willReturn(
                [
                    'page' => $page,
                    'depth' => 0,
                ],
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
        $serviceLocator->expects(self::once())
            ->method('build')
            ->with(
                FindActiveInterface::class,
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

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
            $logger,
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

        $expected = [
            'page' => $page,
            'depth' => 0,
        ];

        self::assertSame($expected, $helper->findActive($name, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveWithoutContainer(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 0;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with(new IsInstanceOf(Navigation::class), $minDepth, $maxDepth)
            ->willReturn([]);

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
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $containerParser->expects(self::once())
            ->method('parseContainer')
            ->with(null)
            ->willReturn(null);

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
            $logger,
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

        $expected = [];

        self::assertSame($expected, $helper->findActive(null, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveOneActivePageWithoutDepth(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::never())
            ->method('isVisible');
        $parentPage->expects(self::never())
            ->method('getResource');
        $parentPage->expects(self::never())
            ->method('getPrivilege');
        $parentPage->expects(self::never())
            ->method('getParent');
        $parentPage->expects(self::never())
            ->method('isActive');

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

        $container = new Navigation();
        $container->addPage($page);

        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 0;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, $minDepth, $maxDepth)
            ->willReturn(
                [
                    'page' => $page,
                    'depth' => 0,
                ],
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
        $serviceLocator->expects(self::once())
            ->method('build')
            ->with(
                FindActiveInterface::class,
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

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
            $logger,
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

        $expected = [
            'page' => $page,
            'depth' => 0,
        ];

        $helper->setMinDepth($minDepth);
        $helper->setMaxDepth($maxDepth);

        self::assertSame($expected, $helper->findActive($name));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveOneActivePageOutOfRange(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            ->method('getParent');
        $page->expects(self::never())
            ->method('isActive');

        $container = new Navigation();
        $container->addPage($page);

        $role     = 'testRole';
        $maxDepth = 42;
        $minDepth = 2;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, $minDepth, $maxDepth)
            ->willReturn([]);

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
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

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
            $logger,
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

        $expected = [];

        self::assertSame($expected, $helper->findActive($name, 2, 42));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveOneActivePageRecursive(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);

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

        $parentPage->addPage($page);

        $container = new Navigation();
        $container->addPage($parentPage);

        $role     = 'testRole';
        $maxDepth = 0;
        $minDepth = 0;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, $minDepth, $maxDepth)
            ->willReturn(
                [
                    'page' => $parentPage,
                    'depth' => 0,
                ],
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
        $serviceLocator->expects(self::once())
            ->method('build')
            ->with(
                FindActiveInterface::class,
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

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
            $logger,
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

        $expected = [
            'page' => $parentPage,
            'depth' => 0,
        ];

        self::assertSame($expected, $helper->findActive($name, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveOneActivePageRecursive2(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setActive(true);
        $parentPage->setUri('parent');
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);

        $page1 = new Uri();
        $page1->setActive(true);
        $page1->setUri('test1');

        $page2 = new Uri();
        $page2->setActive(true);
        $page1->setUri('test2');

        $parentPage->addPage($page1);
        $parentPage->addPage($page2);

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setActive(true);
        $parentParentPage->setUri('parentParent');

        $parentParentParentPage = new Uri();
        $parentParentParentPage->setVisible(true);
        $parentParentParentPage->setActive(true);
        $parentParentParentPage->setUri('parentParentParent');

        $parentParentPage->addPage($parentPage);
        $parentParentParentPage->addPage($parentParentPage);

        $container = new Navigation();
        $container->addPage($parentParentParentPage);

        $role     = 'testRole';
        $maxDepth = 1;
        $minDepth = 2;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, $minDepth, $maxDepth)
            ->willReturn([]);

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
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

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
            $logger,
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

        $expected = [];

        self::assertSame($expected, $helper->findActive($name, $minDepth, $maxDepth));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFindActiveOneActivePageRecursive3(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setActive(true);
        $parentPage->setUri('parent');
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);

        $page1 = new Uri();
        $page1->setActive(true);
        $page1->setUri('test1');

        $page2 = new Uri();
        $page2->setActive(true);
        $page1->setUri('test2');

        $parentPage->addPage($page1);
        $parentPage->addPage($page2);

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setActive(true);
        $parentParentPage->setUri('parentParent');

        $parentParentParentPage = new Uri();
        $parentParentParentPage->setVisible(true);
        $parentParentParentPage->setActive(true);
        $parentParentParentPage->setUri('parentParentParent');

        $parentParentPage->addPage($parentPage);
        $parentParentParentPage->addPage($parentParentPage);

        $container = new Navigation();
        $container->addPage($parentParentParentPage);

        $role     = 'testRole';
        $maxDepth = -1;

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, 0, $maxDepth)
            ->willReturn([]);

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
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
            )
            ->willReturn($findActiveHelper);

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
            $logger,
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

        $helper->setMinDepth(-1);
        $helper->setMaxDepth($maxDepth);

        $expected = [];

        self::assertSame($expected, $helper->findActive($name));
    }

    /** @throws Exception */
    public function testEscapeLabels(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertTrue($helper->getEscapeLabels());

        $helper->escapeLabels(false);

        self::assertFalse($helper->getEscapeLabels());
    }

    /** @throws Exception */
    public function testSetAddClassToListItem(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertFalse($helper->getAddClassToListItem());

        $helper->setAddClassToListItem(true);

        self::assertTrue($helper->getAddClassToListItem());
    }

    /** @throws Exception */
    public function testSetOnlyActiveBranch(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertFalse($helper->getOnlyActiveBranch());

        $helper->setOnlyActiveBranch(true);

        self::assertTrue($helper->getOnlyActiveBranch());
    }

    /** @throws Exception */
    public function testSetPartial(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertNull($helper->getPartial());

        $helper->setPartial('test');

        self::assertSame('test', $helper->getPartial());

        $helper->setPartial(1);

        self::assertSame('test', $helper->getPartial());
    }

    /** @throws Exception */
    public function testSetRenderParents(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertTrue($helper->getRenderParents());

        $helper->setRenderParents(false);

        self::assertFalse($helper->getRenderParents());
    }

    /** @throws Exception */
    public function testSetUlClass(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertSame('navigation', $helper->getUlClass());

        $helper->setUlClass('test');

        self::assertSame('test', $helper->getUlClass());
    }

    /** @throws Exception */
    public function testSetLiClass(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertSame('', $helper->getLiClass());

        $helper->setLiClass('test');

        self::assertSame('test', $helper->getLiClass());
    }

    /** @throws Exception */
    public function testSetLiActiveClass(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        self::assertSame('active', $helper->getLiActiveClass());

        $helper->setLiActiveClass('test');

        self::assertSame('test', $helper->getLiActiveClass());
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testDoNotRenderIfNoPageIsActive(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $container = $this->createMock(ContainerInterface::class);

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, 0, null)
            ->willReturn([]);

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
                [
                    'authorization' => null,
                    'renderInvisible' => false,
                    'role' => null,
                ],
            )
            ->willReturn($findActiveHelper);

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher         = self::exactly(3);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($matcher, $container): ContainerInterface | null {
                    $invocation = $matcher->numberOfInvocations();

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setContainer($container);

        self::assertSame('', $helper->render());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderPartialWithoutPartial(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $role = 'testRole';

        $helper->setRole($role);

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to render menu: No partial view script provided');
        $this->expectExceptionCode(0);

        $helper->renderPartial($name);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderPartialWithWrongPartial(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

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
        $containerParser->expects(self::never())
            ->method('parseContainer');

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $role = 'testRole';

        $helper->setRole($role);

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        assert($auth instanceof AuthorizationInterface);
        $helper->setAuthorization($auth);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        $helper->setPartial(['a', 'b', 'c']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unable to render menu: A view partial supplied as an array must contain one value: the partial view script',
        );
        $this->expectExceptionCode(0);

        $helper->renderPartial($name);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderPartial(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);

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

        $partial  = 'testPartial';
        $expected = 'renderedPartial';

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::once())
            ->method('render')
            ->with($partial, ['container' => $container])
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
            $logger,
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

        $helper->setPartial($partial);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        self::assertSame($expected, $helper->renderPartial($name));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderPartialNoActivePage(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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

        $partial  = 'testPartial';
        $expected = 'renderedPartial';

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::once())
            ->method('render')
            ->with($partial, ['container' => $container])
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
            $logger,
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

        $helper->setPartial($partial);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');
        $view->expects(self::never())
            ->method('getHelperPluginManager');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        self::assertSame($expected, $helper->renderPartial($name));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderPartialWithArrayPartial(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentPage = new Uri();
        $parentPage->setVisible(true);
        $parentPage->setResource($resource);
        $parentPage->setPrivilege($privilege);

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
        $matcher         = self::exactly(2);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($matcher, $container): ContainerInterface | null {
                    $invocation = $matcher->numberOfInvocations();

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

        $partial  = 'testPartial';
        $expected = 'renderedPartial';

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::once())
            ->method('render')
            ->with($partial, ['container' => $container])
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
            $logger,
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

        $helper->setContainer($container);

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
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderPartialWithArrayPartialRenderingPage(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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

        assert($subPage instanceof PageInterface);
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

        $expected = 'renderedPartial';
        $partial  = 'testPartial';

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::once())
            ->method('render')
            ->with($partial, ['container' => $parentPage])
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
            $logger,
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

        self::assertSame($expected, $helper->renderPartial(null, [$partial, 'test']));
    }

    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testDoNotRenderMenuIfNoPageIsActive(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $container = $this->createMock(ContainerInterface::class);

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, 0, null)
            ->willReturn([]);

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
                [
                    'authorization' => null,
                    'renderInvisible' => false,
                    'role' => null,
                ],
            )
            ->willReturn($findActiveHelper);

        $containerParser = $this->getMockBuilder(ContainerParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher         = self::exactly(3);
        $containerParser->expects($matcher)
            ->method('parseContainer')
            ->willReturnCallback(
                static function (ContainerInterface | null $containerParam = null) use ($matcher, $container): ContainerInterface | null {
                    $invocation = $matcher->numberOfInvocations();

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
            $logger,
            $htmlify,
            $containerParser,
            $escapeHtmlAttr,
            $renderer,
            $escapeHtml,
            $htmlElement,
            $translator,
        );

        $helper->setContainer($container);

        self::assertSame('', $helper->renderMenu());
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuNoActivePage(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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

        $findActiveHelper = $this->getMockBuilder(FindActiveInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $findActiveHelper->expects(self::once())
            ->method('find')
            ->with($container, 0, null)
            ->willReturn([]);

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::once())
            ->method('accept')
            ->with($page)
            ->willReturn(false);

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
            $logger,
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

        $expected = '';
        $partial  = 'testPartial';

        $helper->setPartial($partial);

        $view = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects(self::never())
            ->method('plugin');

        assert($view instanceof PhpRenderer);
        $helper->setView($view);

        self::assertSame($expected, $helper->renderMenu($name));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
                        1 => self::assertSame('nav navigation', $value, (string) $invocation),
                        2 => self::assertSame('nav-item active', $value, (string) $invocation),
                        3 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
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

        $expected = '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

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
            $logger,
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

        self::assertSame($expected, $helper->renderMenu($name));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithhIndent(): void
    {
        $indent = '    ';

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
                        1 => self::assertSame('nav navigation', $value, (string) $invocation),
                        2 => self::assertSame('nav-item active', $value, (string) $invocation),
                        3 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
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

        $expected = $indent . '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . $indent . '    <li class="nav-item-escaped active-escaped">' . PHP_EOL . $indent . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . $indent . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . $indent . '            <li class="active-escaped">' . PHP_EOL . $indent . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '            </li>' . PHP_EOL . $indent . '        </ul>' . PHP_EOL . $indent . '    </li>' . PHP_EOL . $indent . '</ul>';

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
            $logger,
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

        self::assertSame($expected, $helper->renderMenu($name));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderVerticalMenuException(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderVerticalMenu(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderVerticalMenu2(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderOlMenuWithMaxDepth(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';
        $maxDepth  = 1;

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
        $page->expects(self::exactly(2))
            ->method('getLiClass')
            ->willReturn('li-class');
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
            ->with($container, 0, $maxDepth)
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
                        1 => self::assertSame('nav navigation', $value, (string) $invocation),
                        2 => self::assertSame('nav-item active', $value, (string) $invocation),
                        3 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active li-class', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
                        2 => 'nav-item-escaped active-escaped',
                        3 => 'dropdown-menu-escaped',
                        4 => 'parent-id-escaped',
                        default => 'active-escaped li-class-escaped',
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

        $expected = '<ol class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ol class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '            <li class="active-escaped li-class-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ol>' . PHP_EOL . '    </li>' . PHP_EOL . '</ol>';

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
            $logger,
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
            $helper->renderMenu($name, ['style' => Menu::STYLE_OL, 'maxDepth' => $maxDepth]),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderUlMenuWithTabs(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $page->expects(self::exactly(2))
            ->method('getLiClass')
            ->willReturn('li-class');
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
        $matcher        = self::exactly(7);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            'navbar-nav navigation nav-tabs',
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame('tablist', $value, (string) $invocation),
                        3 => self::assertSame('nav-item active', $value, (string) $invocation),
                        4 => self::assertSame('presentation', $value, (string) $invocation),
                        5 => self::assertSame(
                            'dropdown-menu dropdown-menu-dark',
                            $value,
                            (string) $invocation,
                        ),
                        6 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active li-class', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'navbar-nav-escaped navigation-escaped nav-tabs-escaped',
                        2 => 'tablist-escaped',
                        3 => 'nav-item-escaped active-escaped',
                        4 => 'presentation-escaped',
                        5 => 'dropdown-menu-escaped dropdown-menu-dark-escaped',
                        6 => 'parent-id-escaped',
                        default => 'active-escaped li-class-escaped',
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

        $expected = '<ul class="navbar-nav-escaped navigation-escaped nav-tabs-escaped" role="tablist-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped" role="presentation-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped dropdown-menu-dark-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '            <li class="active-escaped li-class-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

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
                            ['role' => 'tab', 'aria-current' => 'page', 'class' => 'nav-link btn parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle, 'href' => '##', 'target' => 'self'],
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
            $logger,
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
            $helper->renderMenu($name, ['tabs' => true, 'dark' => true, 'in-navbar' => true]),
        );
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Mimmi20\Mezzio\Navigation\Exception\ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderPartialWithPartialModel(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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

        self::assertSame($expected, $helper->renderPartial(null, $model));
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithPartialModel(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParents(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParentsWithIndent(): void
    {
        $indent = '    ';
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParentsWithIndent2(): void
    {
        $indent = '    ';
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParentsWithIndent3(): void
    {
        $indent = '    ';
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            $logger,
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
                ['tabs' => true, 'dark' => true, 'in-navbar' => true, 'onlyActiveBranch' => true, 'renderParents' => false, 'sublink' => Menu::STYLE_SUBLINK_DETAILS],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     * @throws ReflectionException
     */
    public function testRenderSubMenuWithTabsOnlyActiveBranchWithoutParents(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $page->expects(self::exactly(2))
            ->method('getLiClass')
            ->willReturn('li-class');
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
        $matcher        = self::exactly(2);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav-item active li-class', $value, (string) $invocation),
                        default => self::assertSame('nav navigation', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-item-escaped active-escaped li-class-escaped',
                        default => 'nav-escaped navigation-escaped',
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

        $expected = '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped li-class-escaped">' . PHP_EOL . '        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['aria-current' => 'page', 'class' => 'nav-link btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
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
            $logger,
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

        $helper->setMaxDepth(1);
        $helper->setMinDepth(1);
        $helper->setRenderParents(true);
        $helper->setAddClassToListItem(true);

        self::assertSame($expected, $helper->renderSubMenu($name));

        $propMinDepth = new ReflectionProperty($helper, 'minDepth');

        self::assertNull($propMinDepth->getValue($helper));

        $propMaxDepth = new ReflectionProperty($helper, 'maxDepth');

        self::assertNull($propMaxDepth->getValue($helper));

        $propRenderParents = new ReflectionProperty($helper, 'renderParents');

        self::assertFalse($propRenderParents->getValue($helper));

        $propAddClassToListItem = new ReflectionProperty($helper, 'addClassToListItem');

        self::assertFalse($propAddClassToListItem->getValue($helper));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderSubMenuWithTabsOnlyActiveBranchWithoutParents2(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            ->willReturn([]);

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
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
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
            $logger,
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

        self::assertSame($expected, $helper->renderSubMenu($name));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderSubMenuWithTabsOnlyActiveBranchWithoutParents3(): void
    {
        $indent = '    ';
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            ->willReturn([]);

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
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
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
            $logger,
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

        self::assertSame($expected, $helper->renderSubMenu($name));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderSubMenuWithTabsOnlyActiveBranchWithoutParents4(): void
    {
        $indent = '    ';
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $page->expects(self::exactly(2))
            ->method('getLiClass')
            ->willReturn('li-class');
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
        $matcher        = self::exactly(2);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav-item active li-class', $value, (string) $invocation),
                        default => self::assertSame('nav navigation', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-item-escaped active-escaped li-class-escaped',
                        default => 'nav-escaped navigation-escaped',
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

        $expected = $indent . '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . $indent . '    <li class="nav-item-escaped active-escaped li-class-escaped">' . PHP_EOL . $indent . '        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '    </li>' . PHP_EOL . $indent . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['aria-current' => 'page', 'class' => 'nav-link btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
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
            $logger,
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

        self::assertSame($expected, $helper->renderSubMenu($name));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderSubMenuWithTabsOnlyActiveBranchWithoutParents5(): void
    {
        $indent = '<--  -->';
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $page->expects(self::exactly(2))
            ->method('getLiClass')
            ->willReturn('li-class');
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
        $matcher        = self::exactly(2);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav-item active li-class', $value, (string) $invocation),
                        default => self::assertSame('nav navigation', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-item-escaped active-escaped li-class-escaped',
                        default => 'nav-escaped navigation-escaped',
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

        $expected = $indent . '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . $indent . '    <li class="nav-item-escaped active-escaped li-class-escaped">' . PHP_EOL . $indent . '        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '    </li>' . PHP_EOL . $indent . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['aria-current' => 'page', 'class' => 'nav-link btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
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
            $logger,
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

        self::assertSame($expected, $helper->renderSubMenu($name, indent: $indent));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParents2(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $page->expects(self::once())
            ->method('hasPages')
            ->with(true)
            ->willReturn(false);
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
            ->with($container, 1, null)
            ->willReturn(
                [
                    'page' => $page,
                    'depth' => 1,
                ],
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
        $serviceLocator->expects(self::once())
            ->method('build')
            ->with(
                FindActiveInterface::class,
                [
                    'authorization' => $auth,
                    'renderInvisible' => false,
                    'role' => $role,
                ],
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
            $logger,
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
                ['tabs' => true, 'dark' => true, 'in-navbar' => true, 'onlyActiveBranch' => true, 'renderParents' => false, 'minDepth' => 2],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParents3(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
            ->willReturn(true);
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
            ->with($container, -1, 1)
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
            $logger,
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
                ['tabs' => true, 'dark' => true, 'in-navbar' => true, 'onlyActiveBranch' => true, 'renderParents' => false, 'minDepth' => 0, 'maxDepth' => 1],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParents4(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
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
        $page->expects(self::once())
            ->method('hasPages')
            ->with(true)
            ->willReturn(false);
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
            ->willReturn(false);

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
            $logger,
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
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenuWithTabsOnlyActiveBranchWithoutParents5(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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
                        1 => self::assertSame(
                            'nav-item active li-class xxxx',
                            $value,
                            (string) $invocation,
                        ),
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
                        1 => 'nav-item-escaped active-escaped li-class-escaped xxxx-escaped',
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

        $expected = '<ul class="navbar-nav-escaped navigation-escaped nav-tabs-escaped" role="tablist-escaped">' . PHP_EOL . '    <li class="nav-item-escaped active-escaped li-class-escaped xxxx-escaped" role="presentation-escaped">' . PHP_EOL . '        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'a',
                ['aria-current' => 'page', 'class' => 'nav-link btn', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget, 'role' => 'tab'],
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
            $logger,
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
                ['tabs' => true, 'dark' => true, 'in-navbar' => true, 'onlyActiveBranch' => true, 'renderParents' => false, 'liClass' => 'li-class', 'addClassToListItem' => true],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu2(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page2Label                  = 'page2-label';
        $page2LabelTranslated        = 'page2-label-translated';
        $page2LabelTranslatedEscaped = 'page2-label-translated-escaped';
        $page2Title                  = 'page2-title';
        $page2TitleTranslated        = 'page2-title-translated';
        $page2TextDomain             = 'page2-text-domain';
        $page2Id                     = 'page2-id';
        $page2Href                   = 'http://page2';
        $page2Target                 = 'page2-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page2->expects(self::never())
            ->method('isVisible');
        $page2->expects(self::never())
            ->method('getResource');
        $page2->expects(self::never())
            ->method('getPrivilege');
        $page2->expects(self::never())
            ->method('getParent');
        $page2->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page2->expects(self::once())
            ->method('getLabel')
            ->willReturn($page2Label);
        $page2->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page2TextDomain);
        $page2->expects(self::once())
            ->method('getTitle')
            ->willReturn($page2Title);
        $page2->expects(self::once())
            ->method('getId')
            ->willReturn($page2Id);
        $page2->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx2');
        $page2->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page2Href);
        $page2->expects(self::once())
            ->method('getTarget')
            ->willReturn($page2Target);
        $page2->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page2->expects($matcher)
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
        $page2->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        2, 5 => self::assertSame(
                            $parentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        6, 7 => self::assertSame(
                            $page,
                            $pageParam,
                            (string) $invocation,
                        ),
                        3, 8 => self::assertSame(
                            $page2,
                            $pageParam,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3,
                            $pageParam,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(10);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav navigation', $value, (string) $invocation),
                        2 => self::assertSame('nav-item dropdown active', $value, (string) $invocation),
                        3, 6 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-parent-id', $value, (string) $invocation),
                        5 => self::assertSame('dropdown active', $value, (string) $invocation),
                        7 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
                        2 => 'nav-item-escaped dropdown-escaped active-escaped',
                        3, 6 => 'dropdown-menu-escaped',
                        4 => 'parent-parent-id-escaped',
                        5 => 'dropdown-escaped active-escaped',
                        7 => 'parent-id-escaped',
                        default => 'active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(5);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page2LabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        4 => self::assertSame($page2LabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        4 => $page2LabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(10);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page2Label, $page2Title, $page2TextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page2LabelTranslated, $page2TitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page2Label, $message, (string) $invocation),
                        8 => self::assertSame($page2Title, $message, (string) $invocation),
                        9 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        7, 8 => self::assertSame($page2TextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page2LabelTranslated,
                        8 => $page2TitleTranslated,
                        9 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropdown-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropdown-escaped active-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected4 = '<a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(5);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page2Id, $page3Id, $pageTitleTranslated, $page2TitleTranslated, $page3TitleTranslated, $pageHref, $page2Href, $page3Href, $pageTarget, $page2Target, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected4, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    self::assertSame('a', $element, (string) $invocation);

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle, 'href' => '###', 'target' => 'self-parent'],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle, 'href' => '##', 'target' => 'self'],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx2', 'id' => $page2Id, 'title' => $page2TitleTranslated, 'href' => $page2Href, 'target' => $page2Target],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            $page2LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        4 => $expected4,
                        default => $expected5,
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
            $logger,
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

        self::assertSame($expected, $helper->renderMenu($name));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu3(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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
        $page->expects(self::once())
            ->method('hasPage')
            ->with($page3)
            ->willReturn(true);
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        $page2->expects(self::once())
            ->method('hasPages')
            ->with(false)
            ->willReturn(false);
        $page2->expects(self::never())
            ->method('getLiClass');
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(false);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        2, 5 => self::assertSame(
                            $parentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        6, 7 => self::assertSame(
                            $page,
                            $pageParam,
                            (string) $invocation,
                        ),
                        3, 8 => self::assertSame(
                            $page2,
                            $pageParam,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3,
                            $pageParam,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        3, 8 => false,
                        default => true,
                    };
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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(8);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav navigation', $value, (string) $invocation),
                        2 => self::assertSame(
                            'nav-item dropstart active',
                            $value,
                            (string) $invocation,
                        ),
                        3 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-parent-id', $value, (string) $invocation),
                        5 => self::assertSame('dropstart active', $value, (string) $invocation),
                        6 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        7 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
                        2 => 'nav-item-escaped dropdown-escaped active-escaped',
                        3 => 'dropdown-menu-escaped',
                        4 => 'parent-parent-id-escaped',
                        5 => 'dropdown-escaped active-escaped',
                        6 => 'dropdown-menu-escaped',
                        7 => 'parent-id-escaped',
                        default => 'active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(4);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(8);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropdown-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropdown-escaped active-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li>' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page3Id, $pageTitleTranslated, $page3TitleTranslated, $pageHref, $page3Href, $pageTarget, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 2 => self::assertSame('span', $element, (string) $invocation),
                        default => self::assertSame('a', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        default => $expected5,
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
            $logger,
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
                ['onlyActiveBranch' => true, 'direction' => Menu::DROP_ORIENTATION_START, 'sublink' => Menu::STYLE_SUBLINK_SPAN],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu4(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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
        $page->expects(self::once())
            ->method('hasPage')
            ->with($page3)
            ->willReturn(true);
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        $page2->expects(self::once())
            ->method('hasPages')
            ->with(false)
            ->willReturn(false);
        $page2->expects(self::never())
            ->method('getLiClass');
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(false);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        2, 5 => self::assertSame(
                            $parentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        6, 7 => self::assertSame(
                            $page,
                            $pageParam,
                            (string) $invocation,
                        ),
                        3, 8 => self::assertSame(
                            $page2,
                            $pageParam,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3,
                            $pageParam,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        3, 8 => false,
                        default => true,
                    };
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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(8);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            'nav navigation',
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            'nav-item dropend active',
                            $value,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            'dropdown-menu',
                            $value,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            'parent-parent-id',
                            $value,
                            (string) $invocation,
                        ),
                        5 => self::assertSame(
                            'dropend active',
                            $value,
                            (string) $invocation,
                        ),
                        6 => self::assertSame(
                            'dropdown-menu',
                            $value,
                            (string) $invocation,
                        ),
                        7 => self::assertSame(
                            'parent-id',
                            $value,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            'active',
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
                        2 => 'nav-item-escaped dropend-escaped active-escaped',
                        3 => 'dropdown-menu-escaped',
                        4 => 'parent-parent-id-escaped',
                        5 => 'dropend-escaped active-escaped',
                        6 => 'dropdown-menu-escaped',
                        7 => 'parent-id-escaped',
                        default => 'active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(4);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(8);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropend-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropend-escaped active-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li>' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page3Id, $pageTitleTranslated, $page3TitleTranslated, $pageHref, $page3Href, $pageTarget, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 2 => self::assertSame('button', $element, (string) $invocation),
                        default => self::assertSame('a', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle, 'type' => 'button'],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle, 'type' => 'button'],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        default => $expected5,
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
            $logger,
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
                ['onlyActiveBranch' => true, 'direction' => Menu::DROP_ORIENTATION_END, 'sublink' => Menu::STYLE_SUBLINK_BUTTON],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu5(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page2Label                  = 'page2-label';
        $page2LabelTranslated        = 'page2-label-translated';
        $page2LabelTranslatedEscaped = 'page2-label-translated-escaped';
        $page2Title                  = 'page2-title';
        $page2TitleTranslated        = 'page2-title-translated';
        $page2TextDomain             = 'page2-text-domain';
        $page2Id                     = 'page2-id';
        $page2Href                   = 'http://page2';
        $page2Target                 = 'page2-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page2->expects(self::never())
            ->method('isVisible');
        $page2->expects(self::never())
            ->method('getResource');
        $page2->expects(self::never())
            ->method('getPrivilege');
        $page2->expects(self::never())
            ->method('getParent');
        $page2->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page2->expects(self::once())
            ->method('getLabel')
            ->willReturn($page2Label);
        $page2->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page2TextDomain);
        $page2->expects(self::once())
            ->method('getTitle')
            ->willReturn($page2Title);
        $page2->expects(self::once())
            ->method('getId')
            ->willReturn($page2Id);
        $page2->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx2');
        $page2->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page2Href);
        $page2->expects(self::once())
            ->method('getTarget')
            ->willReturn($page2Target);
        $page2->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page2->expects($matcher)
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
        $page2->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        2, 5 => self::assertSame(
                            $parentPage,
                            $pageParam,
                            (string) $invocation,
                        ),
                        6, 7 => self::assertSame(
                            $page,
                            $pageParam,
                            (string) $invocation,
                        ),
                        3, 8 => self::assertSame(
                            $page2,
                            $pageParam,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3,
                            $pageParam,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(10);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav navigation', $value, (string) $invocation),
                        2 => self::assertSame('nav-item dropend active', $value, (string) $invocation),
                        3, 6 => self::assertSame('dropdown-menu', $value, (string) $invocation),
                        4 => self::assertSame('parent-parent-id', $value, (string) $invocation),
                        5 => self::assertSame('dropend active', $value, (string) $invocation),
                        7 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
                        2 => 'nav-item-escaped dropend-escaped active-escaped',
                        3, 6 => 'dropdown-menu-escaped',
                        4 => 'parent-parent-id-escaped',
                        5 => 'dropend-escaped active-escaped',
                        7 => 'parent-id-escaped',
                        default => 'active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(5);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page2LabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        4 => self::assertSame($page2LabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        4 => $page2LabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(10);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page2Label, $page2Title, $page2TextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page2LabelTranslated, $page2TitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page2Label, $message, (string) $invocation),
                        8 => self::assertSame($page2Title, $message, (string) $invocation),
                        9 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        7, 8 => self::assertSame($page2TextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page2LabelTranslated,
                        8 => $page2TitleTranslated,
                        9 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropend-escaped active-escaped">' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropend-escaped active-escaped">' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected4 = '<a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(5);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page2Id, $page3Id, $pageTitleTranslated, $page2TitleTranslated, $page3TitleTranslated, $pageHref, $page2Href, $page3Href, $pageTarget, $page2Target, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected4, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 2 => self::assertSame('button', $element, (string) $invocation),
                        default => self::assertSame('a', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle, 'type' => 'button'],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle, 'type' => 'button'],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx2', 'id' => $page2Id, 'title' => $page2TitleTranslated, 'href' => $page2Href, 'target' => $page2Target],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            $page2LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        4 => $expected4,
                        default => $expected5,
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
            $logger,
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
                ['direction' => Menu::DROP_ORIENTATION_END, 'sublink' => Menu::STYLE_SUBLINK_BUTTON],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu6(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page2Label                  = 'page2-label';
        $page2LabelTranslated        = 'page2-label-translated';
        $page2LabelTranslatedEscaped = 'page2-label-translated-escaped';
        $page2Title                  = 'page2-title';
        $page2TitleTranslated        = 'page2-title-translated';
        $page2TextDomain             = 'page2-text-domain';
        $page2Id                     = 'page2-id';
        $page2Href                   = 'http://page2';
        $page2Target                 = 'page2-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page2->expects(self::never())
            ->method('isVisible');
        $page2->expects(self::never())
            ->method('getResource');
        $page2->expects(self::never())
            ->method('getPrivilege');
        $page2->expects(self::never())
            ->method('getParent');
        $page2->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page2->expects(self::once())
            ->method('getLabel')
            ->willReturn($page2Label);
        $page2->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page2TextDomain);
        $page2->expects(self::once())
            ->method('getTitle')
            ->willReturn($page2Title);
        $page2->expects(self::once())
            ->method('getId')
            ->willReturn($page2Id);
        $page2->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx2');
        $page2->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page2Href);
        $page2->expects(self::once())
            ->method('getTarget')
            ->willReturn($page2Target);
        $page2->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page2->expects($matcher)
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
        $page2->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentPage, $pageParam, (string) $invocation),
                        2, 5 => self::assertSame($parentPage, $pageParam, (string) $invocation),
                        6, 7 => self::assertSame($page, $pageParam, (string) $invocation),
                        3, 8 => self::assertSame($page2, $pageParam, (string) $invocation),
                        default => self::assertSame($page3, $pageParam, (string) $invocation),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(10);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav navigation', $value, (string) $invocation),
                        2 => self::assertSame('nav-item dropup active', $value, (string) $invocation),
                        3, 6 => self::assertSame(
                            'dropdown-menu dropdown-details-menu',
                            $value,
                            (string) $invocation,
                        ),
                        4 => self::assertSame('parent-parent-id', $value, (string) $invocation),
                        5 => self::assertSame('dropup active', $value, (string) $invocation),
                        7 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped navigation-escaped',
                        2 => 'nav-item-escaped dropup-escaped active-escaped',
                        3, 6 => 'dropdown-details-menu-escaped',
                        4 => 'parent-parent-id-escaped',
                        5 => 'dropup-escaped active-escaped',
                        7 => 'parent-id-escaped',
                        default => 'active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(5);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page2LabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        4 => self::assertSame($page2LabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        4 => $page2LabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(10);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page2Label, $page2Title, $page2TextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page2LabelTranslated, $page2TitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page2Label, $message, (string) $invocation),
                        8 => self::assertSame($page2Title, $message, (string) $invocation),
                        9 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        7, 8 => self::assertSame($page2TextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page2LabelTranslated,
                        8 => $page2TitleTranslated,
                        9 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped navigation-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropup-escaped active-escaped">' . PHP_EOL . '        <details>' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropup-escaped active-escaped">' . PHP_EOL . '                <details>' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '                </details>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="active-escaped">' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '        </details>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected4 = '<a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(5);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page2Id, $page3Id, $pageTitleTranslated, $page2TitleTranslated, $page3TitleTranslated, $pageHref, $page2Href, $page3Href, $pageTarget, $page2Target, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected4, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 2 => self::assertSame('summary', $element, (string) $invocation),
                        default => self::assertSame('a', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx2', 'id' => $page2Id, 'title' => $page2TitleTranslated, 'href' => $page2Href, 'target' => $page2Target],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            $page2LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        4 => $expected4,
                        default => $expected5,
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
            $logger,
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
                ['direction' => Menu::DROP_ORIENTATION_UP, 'sublink' => Menu::STYLE_SUBLINK_DETAILS],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu7(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $liActiveClass = 'li-active';
        $ulClass       = 'ul-class ul';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page2Label                  = 'page2-label';
        $page2LabelTranslated        = 'page2-label-translated';
        $page2LabelTranslatedEscaped = 'page2-label-translated-escaped';
        $page2Title                  = 'page2-title';
        $page2TitleTranslated        = 'page2-title-translated';
        $page2TextDomain             = 'page2-text-domain';
        $page2Id                     = 'page2-id';
        $page2Href                   = 'http://page2';
        $page2Target                 = 'page2-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page2->expects(self::never())
            ->method('isVisible');
        $page2->expects(self::never())
            ->method('getResource');
        $page2->expects(self::never())
            ->method('getPrivilege');
        $page2->expects(self::never())
            ->method('getParent');
        $page2->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page2->expects(self::once())
            ->method('getLabel')
            ->willReturn($page2Label);
        $page2->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page2TextDomain);
        $page2->expects(self::once())
            ->method('getTitle')
            ->willReturn($page2Title);
        $page2->expects(self::once())
            ->method('getId')
            ->willReturn($page2Id);
        $page2->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx2');
        $page2->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page2Href);
        $page2->expects(self::once())
            ->method('getTarget')
            ->willReturn($page2Target);
        $page2->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page2->expects($matcher)
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
        $page2->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentPage, $pageParam, (string) $invocation),
                        2, 5 => self::assertSame($parentPage, $pageParam, (string) $invocation),
                        6, 7 => self::assertSame($page, $pageParam, (string) $invocation),
                        3, 8 => self::assertSame($page2, $pageParam, (string) $invocation),
                        default => self::assertSame($page3, $pageParam, (string) $invocation),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(10);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav ul-class ul', $value, (string) $invocation),
                        2 => self::assertSame(
                            'nav-item dropup li-active',
                            $value,
                            (string) $invocation,
                        ),
                        3, 6 => self::assertSame(
                            'dropdown-menu dropdown-details-menu',
                            $value,
                            (string) $invocation,
                        ),
                        4 => self::assertSame('parent-parent-id', $value, (string) $invocation),
                        5 => self::assertSame('dropup li-active', $value, (string) $invocation),
                        7 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('li-active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped ul-class-escaped ul-escaped',
                        2 => 'nav-item-escaped dropup-escaped li-active-escaped',
                        3, 6 => 'dropdown-details-menu-escaped',
                        4 => 'parent-parent-id-escaped',
                        5 => 'dropup-escaped li-active-escaped',
                        7 => 'parent-id-escaped',
                        default => 'li-active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(5);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page2LabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        4 => self::assertSame($page2LabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        4 => $page2LabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(10);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page2Label, $page2Title, $page2TextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page2LabelTranslated, $page2TitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page2Label, $message, (string) $invocation),
                        8 => self::assertSame($page2Title, $message, (string) $invocation),
                        9 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        7, 8 => self::assertSame($page2TextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page2LabelTranslated,
                        8 => $page2TitleTranslated,
                        9 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped ul-class-escaped ul-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropup-escaped li-active-escaped">' . PHP_EOL . '        <details>' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropup-escaped li-active-escaped">' . PHP_EOL . '                <details>' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="li-active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '                </details>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '        </details>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected4 = '<a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(5);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page2Id, $page3Id, $pageTitleTranslated, $page2TitleTranslated, $page3TitleTranslated, $pageHref, $page2Href, $page3Href, $pageTarget, $page2Target, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected4, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 2 => self::assertSame('summary', $element, (string) $invocation),
                        default => self::assertSame('a', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx2', 'id' => $page2Id, 'title' => $page2TitleTranslated, 'href' => $page2Href, 'target' => $page2Target],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            $page2LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        4 => $expected4,
                        default => $expected5,
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
            $logger,
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
                ['direction' => Menu::DROP_ORIENTATION_UP, 'sublink' => Menu::STYLE_SUBLINK_DETAILS, 'ulClass' => $ulClass, 'liActiveClass' => $liActiveClass],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu8(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $container = new Navigation();
        $container->addPage($parentParentPage);

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

        $expected = '<ul class="nav-escaped ul-class-escaped ul-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropup-escaped li-active-escaped">' . PHP_EOL . '        <details>' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropup-escaped li-active-escaped">' . PHP_EOL . '                <details>' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="li-active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '                </details>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '        </details>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

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
            $logger,
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

        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage(
            'assert(is_int($options[\'maxDepth\']) || $options[\'maxDepth\'] === null)',
        );
        $this->expectExceptionCode(1);

        self::assertSame(
            $expected,
            $helper->renderMenu(
                $name,
                ['direction' => Menu::DROP_ORIENTATION_UP, 'sublink' => Menu::STYLE_SUBLINK_DETAILS, 'ulClass' => $ulClass, 'liActiveClass' => $liActiveClass, 'maxDepth' => true],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu9(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $liActiveClass = 'li-active';
        $ulClass       = 'ul-class ul';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page2Label                  = 'page2-label';
        $page2LabelTranslated        = 'page2-label-translated';
        $page2LabelTranslatedEscaped = 'page2-label-translated-escaped';
        $page2Title                  = 'page2-title';
        $page2TitleTranslated        = 'page2-title-translated';
        $page2TextDomain             = 'page2-text-domain';
        $page2Id                     = 'page2-id';
        $page2Href                   = 'http://page2';
        $page2Target                 = 'page2-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page2->expects(self::never())
            ->method('isVisible');
        $page2->expects(self::never())
            ->method('getResource');
        $page2->expects(self::never())
            ->method('getPrivilege');
        $page2->expects(self::never())
            ->method('getParent');
        $page2->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page2->expects(self::once())
            ->method('getLabel')
            ->willReturn($page2Label);
        $page2->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page2TextDomain);
        $page2->expects(self::once())
            ->method('getTitle')
            ->willReturn($page2Title);
        $page2->expects(self::once())
            ->method('getId')
            ->willReturn($page2Id);
        $page2->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx2');
        $page2->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page2Href);
        $page2->expects(self::once())
            ->method('getTarget')
            ->willReturn($page2Target);
        $page2->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page2->expects($matcher)
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
        $page2->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentPage, $pageParam, (string) $invocation),
                        2, 5 => self::assertSame($parentPage, $pageParam, (string) $invocation),
                        6, 7 => self::assertSame($page, $pageParam, (string) $invocation),
                        3, 8 => self::assertSame($page2, $pageParam, (string) $invocation),
                        default => self::assertSame($page3, $pageParam, (string) $invocation),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(12);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav ul-class ul nav-tabs', $value, (string) $invocation),
                        2 => self::assertSame('tablist', $value, (string) $invocation),
                        3 => self::assertSame(
                            'nav-item dropup li-active',
                            $value,
                            (string) $invocation,
                        ),
                        4 => self::assertSame('presentation', $value, (string) $invocation),
                        5, 8 => self::assertSame(
                            'dropdown-menu dropdown-details-menu',
                            $value,
                            (string) $invocation,
                        ),
                        6 => self::assertSame('parent-parent-id', $value, (string) $invocation),
                        7 => self::assertSame('dropup li-active', $value, (string) $invocation),
                        9 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('li-active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped ul-class-escaped ul-escaped nav-tabs-escaped',
                        2 => 'tablist-escaped',
                        3 => 'nav-item-escaped dropup-escaped li-active-escaped',
                        4 => 'presentation-escaped',
                        5, 8 => 'dropdown-details-menu-escaped',
                        6 => 'parent-parent-id-escaped',
                        7 => 'dropup-escaped li-active-escaped',
                        9 => 'parent-id-escaped',
                        default => 'li-active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(5);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page2LabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        4 => self::assertSame($page2LabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        4 => $page2LabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(10);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page2Label, $page2Title, $page2TextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page2LabelTranslated, $page2TitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page2Label, $message, (string) $invocation),
                        8 => self::assertSame($page2Title, $message, (string) $invocation),
                        9 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        7, 8 => self::assertSame($page2TextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page2LabelTranslated,
                        8 => $page2TitleTranslated,
                        9 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = '<ul class="nav-escaped ul-class-escaped ul-escaped nav-tabs-escaped" role="tablist-escaped">' . PHP_EOL . '    <li class="nav-item-escaped dropup-escaped li-active-escaped" role="presentation-escaped">' . PHP_EOL . '        <details>' . PHP_EOL . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . '        <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . '            <li class="dropup-escaped li-active-escaped">' . PHP_EOL . '                <details>' . PHP_EOL . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . '                    <li class="li-active-escaped">' . PHP_EOL . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . '                    </li>' . PHP_EOL . '                </ul>' . PHP_EOL . '                </details>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '            <li class="li-active-escaped">' . PHP_EOL . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . '            </li>' . PHP_EOL . '        </ul>' . PHP_EOL . '        </details>' . PHP_EOL . '    </li>' . PHP_EOL . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected4 = '<a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(5);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page2Id, $page3Id, $pageTitleTranslated, $page2TitleTranslated, $page3TitleTranslated, $pageHref, $page2Href, $page3Href, $pageTarget, $page2Target, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected4, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 2 => self::assertSame('summary', $element, (string) $invocation),
                        default => self::assertSame('a', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx2', 'id' => $page2Id, 'title' => $page2TitleTranslated, 'href' => $page2Href, 'target' => $page2Target],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            $page2LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        4 => $expected4,
                        default => $expected5,
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
            $logger,
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
                ['direction' => Menu::DROP_ORIENTATION_UP, 'sublink' => Menu::STYLE_SUBLINK_DETAILS, 'ulClass' => $ulClass, 'liActiveClass' => $liActiveClass, 'tabs' => true],
            ),
        );
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderMenu10(): void
    {
        $indent = '<--  -->';

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::never())
            ->method('critical');
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');

        $name = 'Mimmi20\Mezzio\Navigation\Top';

        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $liActiveClass = 'li-active';
        $ulClass       = 'ul-class ul';

        $parentLabel                  = 'parent-label';
        $parentTranslatedLabel        = 'parent-label-translated';
        $parentTranslatedLabelEscaped = 'parent-label-translated-escaped';
        $parentTextDomain             = 'parent-text-domain';
        $parentTitle                  = 'parent-title';
        $parentTranslatedTitle        = 'parent-title-translated';

        $parentParentLabel                  = 'parent-parent-label';
        $parentParentTranslatedLabel        = 'parent-parent-label-translated';
        $parentParentTranslatedLabelEscaped = 'parent-parent-label-translated-escaped';
        $parentParentTextDomain             = 'parent-parent-text-domain';
        $parentParentTitle                  = 'parent-parent-title';
        $parentParentTranslatedTitle        = 'parent-parent-title-translated';

        $pageLabel                  = 'page-label';
        $pageLabelTranslated        = 'page-label-translated';
        $pageLabelTranslatedEscaped = 'page-label-translated-escaped';
        $pageTitle                  = 'page-title';
        $pageTitleTranslated        = 'page-title-translated';
        $pageTextDomain             = 'page-text-domain';
        $pageId                     = 'page-id';
        $pageHref                   = 'http://page';
        $pageTarget                 = 'page-target';

        $page2Label                  = 'page2-label';
        $page2LabelTranslated        = 'page2-label-translated';
        $page2LabelTranslatedEscaped = 'page2-label-translated-escaped';
        $page2Title                  = 'page2-title';
        $page2TitleTranslated        = 'page2-title-translated';
        $page2TextDomain             = 'page2-text-domain';
        $page2Id                     = 'page2-id';
        $page2Href                   = 'http://page2';
        $page2Target                 = 'page2-target';

        $page3Label                  = 'page3-label';
        $page3LabelTranslated        = 'page3-label-translated';
        $page3LabelTranslatedEscaped = 'page3-label-translated-escaped';
        $page3Title                  = 'page3-title';
        $page3TitleTranslated        = 'page3-title-translated';
        $page3TextDomain             = 'page3-text-domain';
        $page3Id                     = 'page3-id';
        $page3Href                   = 'http://page3';
        $page3Target                 = 'page3-target';

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

        $parentParentPage = new Uri();
        $parentParentPage->setVisible(true);
        $parentParentPage->setResource($resource);
        $parentParentPage->setPrivilege($privilege);
        $parentParentPage->setId('parent-parent-id');
        $parentParentPage->setClass('parent-parent-class');
        $parentParentPage->setUri('###');
        $parentParentPage->setTarget('self-parent');
        $parentParentPage->setLabel($parentParentLabel);
        $parentParentPage->setTitle($parentParentTitle);
        $parentParentPage->setTextDomain($parentParentTextDomain);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::exactly(3))
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

        $page2 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page2->expects(self::never())
            ->method('isVisible');
        $page2->expects(self::never())
            ->method('getResource');
        $page2->expects(self::never())
            ->method('getPrivilege');
        $page2->expects(self::never())
            ->method('getParent');
        $page2->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page2->expects(self::once())
            ->method('getLabel')
            ->willReturn($page2Label);
        $page2->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page2TextDomain);
        $page2->expects(self::once())
            ->method('getTitle')
            ->willReturn($page2Title);
        $page2->expects(self::once())
            ->method('getId')
            ->willReturn($page2Id);
        $page2->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx2');
        $page2->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page2Href);
        $page2->expects(self::once())
            ->method('getTarget')
            ->willReturn($page2Target);
        $page2->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page2->expects($matcher)
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
        $page2->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page2->expects(self::once())
            ->method('hashCode')
            ->willReturn('page2');

        $page3 = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page3->expects(self::never())
            ->method('isVisible');
        $page3->expects(self::never())
            ->method('getResource');
        $page3->expects(self::never())
            ->method('getPrivilege');
        $page3->expects(self::never())
            ->method('getParent');
        $page3->expects(self::once())
            ->method('isActive')
            ->with(true)
            ->willReturn(true);
        $page3->expects(self::once())
            ->method('getLabel')
            ->willReturn($page3Label);
        $page3->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($page3TextDomain);
        $page3->expects(self::once())
            ->method('getTitle')
            ->willReturn($page3Title);
        $page3->expects(self::once())
            ->method('getId')
            ->willReturn($page3Id);
        $page3->expects(self::exactly(2))
            ->method('getClass')
            ->willReturn('xxxx3');
        $page3->expects(self::exactly(2))
            ->method('getHref')
            ->willReturn($page3Href);
        $page3->expects(self::once())
            ->method('getTarget')
            ->willReturn($page3Target);
        $page3->expects(self::never())
            ->method('hasPage');
        $matcher = self::exactly(2);
        $page3->expects($matcher)
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
        $page3->expects(self::once())
            ->method('getLiClass')
            ->willReturn(null);
        $page3->expects(self::once())
            ->method('hashCode')
            ->willReturn('page3');

        $parentPage->addPage($page);
        $parentParentPage->addPage($parentPage);
        $parentParentPage->addPage($page2);
        $parentParentPage->addPage($page3);

        $container = new Navigation();
        $container->addPage($parentParentPage);

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
        $matcher      = self::exactly(9);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (PageInterface $pageParam, bool $recursive = true) use ($matcher, $parentParentPage, $parentPage, $page, $page2, $page3): bool {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentPage, $pageParam, (string) $invocation),
                        2, 5 => self::assertSame($parentPage, $pageParam, (string) $invocation),
                        6, 7 => self::assertSame($page, $pageParam, (string) $invocation),
                        3, 8 => self::assertSame($page2, $pageParam, (string) $invocation),
                        default => self::assertSame($page3, $pageParam, (string) $invocation),
                    };

                    match ($invocation) {
                        2, 3, 4, 6 => self::assertFalse(
                            $recursive,
                            (string) $invocation,
                        ),
                        default => self::assertTrue(
                            $recursive,
                            (string) $invocation,
                        ),
                    };

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
        $matcher = self::exactly(10);
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
        $matcher        = self::exactly(12);
        $escapeHtmlAttr->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('nav ul-class ul nav-tabs', $value, (string) $invocation),
                        2 => self::assertSame('tablist', $value, (string) $invocation),
                        3 => self::assertSame(
                            'nav-item dropup li-active',
                            $value,
                            (string) $invocation,
                        ),
                        4 => self::assertSame('presentation', $value, (string) $invocation),
                        5, 8 => self::assertSame(
                            'dropdown-menu dropdown-details-menu',
                            $value,
                            (string) $invocation,
                        ),
                        6 => self::assertSame('parent-parent-id', $value, (string) $invocation),
                        7 => self::assertSame('dropup li-active', $value, (string) $invocation),
                        9 => self::assertSame('parent-id', $value, (string) $invocation),
                        default => self::assertSame('li-active', $value, (string) $invocation),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => 'nav-escaped ul-class-escaped ul-escaped nav-tabs-escaped',
                        2 => 'tablist-escaped',
                        3 => 'nav-item-escaped dropup-escaped li-active-escaped',
                        4 => 'presentation-escaped',
                        5, 8 => 'dropdown-details-menu-escaped',
                        6 => 'parent-parent-id-escaped',
                        7 => 'dropup-escaped li-active-escaped',
                        9 => 'parent-id-escaped',
                        default => 'li-active-escaped',
                    };
                },
            );

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher    = self::exactly(5);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $parentParentTranslatedLabel, $parentTranslatedLabel, $pageLabelTranslated, $page2LabelTranslated, $page3LabelTranslated, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabel,
                            $value,
                            (string) $invocation,
                        ),
                        2 => self::assertSame($parentTranslatedLabel, $value, (string) $invocation),
                        3 => self::assertSame($pageLabelTranslated, $value, (string) $invocation),
                        4 => self::assertSame($page2LabelTranslated, $value, (string) $invocation),
                        default => self::assertSame(
                            $page3LabelTranslated,
                            $value,
                            (string) $invocation,
                        ),
                    };

                    self::assertSame(0, $recurse, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabelEscaped,
                        2 => $parentTranslatedLabelEscaped,
                        3 => $pageLabelTranslatedEscaped,
                        4 => $page2LabelTranslatedEscaped,
                        default => $page3LabelTranslatedEscaped,
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
        $matcher    = self::exactly(10);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomain = null, string | null $locale = null) use ($matcher, $parentParentLabel, $parentParentTitle, $parentParentTextDomain, $parentLabel, $parentTitle, $parentTextDomain, $pageLabel, $pageTitle, $pageTextDomain, $page2Label, $page2Title, $page2TextDomain, $page3Label, $page3Title, $page3TextDomain, $parentParentTranslatedLabel, $parentParentTranslatedTitle, $parentTranslatedLabel, $parentTranslatedTitle, $pageLabelTranslated, $pageTitleTranslated, $page2LabelTranslated, $page2TitleTranslated, $page3LabelTranslated, $page3TitleTranslated): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame($parentParentLabel, $message, (string) $invocation),
                        2 => self::assertSame($parentParentTitle, $message, (string) $invocation),
                        3 => self::assertSame($parentLabel, $message, (string) $invocation),
                        4 => self::assertSame($parentTitle, $message, (string) $invocation),
                        5 => self::assertSame($pageLabel, $message, (string) $invocation),
                        6 => self::assertSame($pageTitle, $message, (string) $invocation),
                        7 => self::assertSame($page2Label, $message, (string) $invocation),
                        8 => self::assertSame($page2Title, $message, (string) $invocation),
                        9 => self::assertSame($page3Label, $message, (string) $invocation),
                        default => self::assertSame($page3Title, $message, (string) $invocation),
                    };

                    match ($invocation) {
                        1, 2 => self::assertSame(
                            $parentParentTextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                        3, 4 => self::assertSame($parentTextDomain, $textDomain, (string) $invocation),
                        5, 6 => self::assertSame($pageTextDomain, $textDomain, (string) $invocation),
                        7, 8 => self::assertSame($page2TextDomain, $textDomain, (string) $invocation),
                        default => self::assertSame(
                            $page3TextDomain,
                            $textDomain,
                            (string) $invocation,
                        ),
                    };

                    self::assertNull($locale, (string) $invocation);

                    return match ($invocation) {
                        1 => $parentParentTranslatedLabel,
                        2 => $parentParentTranslatedTitle,
                        3 => $parentTranslatedLabel,
                        4 => $parentTranslatedTitle,
                        5 => $pageLabelTranslated,
                        6 => $pageTitleTranslated,
                        7 => $page2LabelTranslated,
                        8 => $page2TitleTranslated,
                        9 => $page3LabelTranslated,
                        default => $page3TitleTranslated,
                    };
                },
            );

        $expected = $indent . '<ul class="nav-escaped ul-class-escaped ul-escaped nav-tabs-escaped" role="tablist-escaped">' . PHP_EOL . $indent . '    <li class="nav-item-escaped dropup-escaped li-active-escaped" role="presentation-escaped">' . PHP_EOL . $indent . '        <details>' . PHP_EOL . $indent . '        <a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>' . PHP_EOL . $indent . '        <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-parent-id-escaped">' . PHP_EOL . $indent . '            <li class="dropup-escaped li-active-escaped">' . PHP_EOL . $indent . '                <details>' . PHP_EOL . $indent . '                <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '                <ul class="dropdown-details-menu-escaped" aria-labelledby="parent-id-escaped">' . PHP_EOL . $indent . '                    <li class="li-active-escaped">' . PHP_EOL . $indent . '                        <a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '                    </li>' . PHP_EOL . $indent . '                </ul>' . PHP_EOL . $indent . '                </details>' . PHP_EOL . $indent . '            </li>' . PHP_EOL . $indent . '            <li class="li-active-escaped">' . PHP_EOL . $indent . '                <a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '            </li>' . PHP_EOL . $indent . '            <li class="li-active-escaped">' . PHP_EOL . $indent . '                <a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>' . PHP_EOL . $indent . '            </li>' . PHP_EOL . $indent . '        </ul>' . PHP_EOL . $indent . '        </details>' . PHP_EOL . $indent . '    </li>' . PHP_EOL . $indent . '</ul>';

        $expected1 = '<a parent-id-escaped="parent-id-escaped" parent-title-escaped="parent-title-escaped" parent-class-escaped="parent-class-escaped" parent-href-escaped="##-escaped" parent-target-escaped="self-escaped">parent-label-escaped</a>';
        $expected2 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected3 = '<a idEscaped="testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelTranslatedAndEscaped</a>';
        $expected4 = '<a idEscaped="test2IdEscaped" titleEscaped="test2TitleTranslatedAndEscaped" classEscaped="test2ClassEscaped" hrefEscaped="#2Escaped">test2LabelTranslatedAndEscaped</a>';
        $expected5 = '<a idEscaped="test3IdEscaped" titleEscaped="test3TitleTranslatedAndEscaped" classEscaped="test3ClassEscaped" hrefEscaped="#3Escaped">test3LabelTranslatedAndEscaped</a>';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher     = self::exactly(5);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $parentParentTranslatedTitle, $parentTranslatedTitle, $pageId, $page2Id, $page3Id, $pageTitleTranslated, $page2TitleTranslated, $page3TitleTranslated, $pageHref, $page2Href, $page3Href, $pageTarget, $page2Target, $page3Target, $parentParentTranslatedLabelEscaped, $parentTranslatedLabelEscaped, $pageLabelTranslatedEscaped, $page2LabelTranslatedEscaped, $page3LabelTranslatedEscaped, $expected1, $expected2, $expected3, $expected4, $expected5): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 2 => self::assertSame('summary', $element, (string) $invocation),
                        default => self::assertSame('a', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'aria-current' => 'page', 'class' => 'nav-link btn dropdown-toggle parent-parent-class', 'id' => 'parent-parent-id', 'title' => $parentParentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            ['data-popper-placement' => 'top-start', 'aria-expanded' => 'false', 'role' => 'button', 'class' => 'dropdown-item btn dropdown-toggle parent-class', 'id' => 'parent-id', 'title' => $parentTranslatedTitle],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx', 'id' => $pageId, 'title' => $pageTitleTranslated, 'href' => $pageHref, 'target' => $pageTarget],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx2', 'id' => $page2Id, 'title' => $page2TitleTranslated, 'href' => $page2Href, 'target' => $page2Target],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            ['class' => 'dropdown-item btn xxxx3', 'id' => $page3Id, 'title' => $page3TitleTranslated, 'href' => $page3Href, 'target' => $page3Target],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $parentParentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $parentTranslatedLabelEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            $pageLabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            $page2LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            $page3LabelTranslatedEscaped,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expected1,
                        2 => $expected2,
                        3 => $expected3,
                        4 => $expected4,
                        default => $expected5,
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
            $logger,
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
                ['direction' => Menu::DROP_ORIENTATION_UP, 'sublink' => Menu::STYLE_SUBLINK_DETAILS, 'ulClass' => $ulClass, 'liActiveClass' => $liActiveClass, 'tabs' => true, 'indent' => $indent],
            ),
        );
    }
}
