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

namespace Mimmi20Test\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Compare;

use Laminas\I18n\Translator\Translator;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Exception\InvalidArgumentException;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Role\GenericRole;
use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager as ViewHelperPluginManager;
use Mezzio\Helper\ServerUrlHelper as BaseServerUrlHelper;
use Mezzio\Helper\UrlHelper as BaseUrlHelper;
use Mezzio\LaminasView\HelperPluginManagerFactory;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\LaminasView\LaminasViewRendererFactory;
use Mezzio\LaminasView\ServerUrlHelper;
use Mezzio\LaminasView\UrlHelper;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Mezzio\Router\RouterInterface;
use Mimmi20\Mezzio\GenericAuthorization\Acl\LaminasAcl;
use Mimmi20\Mezzio\Navigation\Config\NavigationConfig;
use Mimmi20\Mezzio\Navigation\Config\NavigationConfigInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\ContainerParserFactory;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\ContainerParserInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlElementFactory;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlifyFactory;
use Mimmi20\Mezzio\Navigation\LaminasView\Helper\HtmlifyInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\NavigationFactory;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\ServerUrlHelperFactory;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\UrlHelperFactory;
use Mimmi20\Mezzio\Navigation\Navigation;
use Mimmi20\Mezzio\Navigation\Page\PageFactory;
use Mimmi20\Mezzio\Navigation\Page\PageFactoryInterface;
use Mimmi20\Mezzio\Navigation\Service\ConstructedNavigationFactory;
use Mimmi20\Mezzio\Navigation\Service\DefaultNavigationFactory;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;

use function assert;
use function file_get_contents;
use function get_debug_type;
use function sprintf;

/**
 * Base class for navigation view helper tests
 */
abstract class AbstractTestCase extends TestCase
{
    protected ServiceManager $serviceManager;

    /**
     * Path to files needed for test
     */
    protected string $files;

    /**
     * The first container in the config file (files/navigation.xml)
     */
    protected Navigation $nav1;

    /**
     * The second container in the config file (files/navigation.xml)
     */
    protected Navigation $nav2;

    /**
     * The third container in the config file (files/navigation.xml)
     */
    protected Navigation $nav3;

    /**
     * The third container in the config file (files/navigation.xml)
     */
    protected Navigation $nav4;

    /**
     * Prepares the environment before running a test
     *
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $cwd = __DIR__;

        // read navigation config
        $this->files = $cwd . '/_files';
        $config      = require $this->files . '/navigation.php';

        $sm = $this->serviceManager = new ServiceManager();
        $sm->setAllowOverride(true);

        $sm->setFactory('Navigation', DefaultNavigationFactory::class);
        $sm->setFactory('navigation', DefaultNavigationFactory::class);
        $sm->setFactory('default', DefaultNavigationFactory::class);
        $sm->setFactory('nav_test1', new ConstructedNavigationFactory('nav_test1'));
        $sm->setFactory('nav_test2', new ConstructedNavigationFactory('nav_test2'));
        $sm->setFactory('nav_test3', new ConstructedNavigationFactory('nav_test3'));
        $sm->setFactory('nav_test4', new ConstructedNavigationFactory('nav_test4'));
        $sm->setFactory(
            NavigationConfigInterface::class,
            function () use ($config): NavigationConfig {
                $route = new Route(
                    '/test.html',
                    $this->createStub(MiddlewareInterface::class),
                );

                $pages            = $config;
                $pages['default'] = $pages['nav_test1'];

                $navConfig = new NavigationConfig();
                $navConfig->setPages($pages);
                $navConfig->setRouteResult(RouteResult::fromRoute(
                    $route,
                    [
                        'route' => 'post',
                        'id' => '1337',
                    ],
                ));
                $navConfig->setRouter(new class () implements RouterInterface {
                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function addRoute(Route $route): void
                    {
                        // nothing to do
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function match(ServerRequestInterface $request): RouteResult
                    {
                        return RouteResult::fromRouteFailure([]);
                    }

                    /**
                     * @param array<mixed> $substitutions
                     * @param array<mixed> $options
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function generateUri(string $name, array $substitutions = [], array $options = []): string
                    {
                        return '';
                    }
                });
                $navConfig->setRequest(new class () implements ServerRequestInterface {
                    /** @throws void */
                    #[Override]
                    public function getProtocolVersion(): string
                    {
                        return '';
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withProtocolVersion(string $version): MessageInterface
                    {
                        return $this;
                    }

                    /** @throws void */
                    #[Override]
                    public function getHeaders(): array
                    {
                        return [];
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function hasHeader(string $name): bool
                    {
                        return false;
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function getHeader(string $name): array
                    {
                        return [];
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function getHeaderLine(string $name): string
                    {
                        return '';
                    }

                    /**
                     * @param array<string>|string $value header value(s)
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withHeader(string $name, $value): MessageInterface
                    {
                        return $this;
                    }

                    /**
                     * @param array<string>|string $value header value(s)
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withAddedHeader(string $name, $value): MessageInterface
                    {
                        return $this;
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withoutHeader(string $name): MessageInterface
                    {
                        return $this;
                    }

                    /** @throws \Exception */
                    #[Override]
                    public function getBody(): StreamInterface
                    {
                        throw new \Exception('not implemented');
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withBody(StreamInterface $body): MessageInterface
                    {
                        return $this;
                    }

                    /** @throws void */
                    #[Override]
                    public function getRequestTarget(): string
                    {
                        return '';
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withRequestTarget(string $requestTarget): RequestInterface
                    {
                        return $this;
                    }

                    /** @throws void */
                    #[Override]
                    public function getMethod(): string
                    {
                        return '';
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withMethod(string $method): RequestInterface
                    {
                        return $this;
                    }

                    /** @throws void */
                    #[Override]
                    public function getUri(): UriInterface
                    {
                        return new class () implements UriInterface {
                            /** @throws void */
                            #[Override]
                            public function getScheme(): string
                            {
                                return '';
                            }

                            /** @throws void */
                            #[Override]
                            public function getAuthority(): string
                            {
                                return '';
                            }

                            /** @throws void */
                            #[Override]
                            public function getUserInfo(): string
                            {
                                return '';
                            }

                            /** @throws void */
                            #[Override]
                            public function getHost(): string
                            {
                                return '';
                            }

                            /** @throws void */
                            #[Override]
                            public function getPort(): int | null
                            {
                                return null;
                            }

                            /** @throws void */
                            #[Override]
                            public function getPath(): string
                            {
                                return '/test.html';
                            }

                            /** @throws void */
                            #[Override]
                            public function getQuery(): string
                            {
                                return '';
                            }

                            /** @throws void */
                            #[Override]
                            public function getFragment(): string
                            {
                                return '';
                            }

                            /**
                             * @throws void
                             *
                             * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                             */
                            #[Override]
                            public function withScheme(string $scheme): UriInterface
                            {
                                return $this;
                            }

                            /**
                             * @throws void
                             *
                             * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                             */
                            #[Override]
                            public function withUserInfo(string $user, string | null $password = null): UriInterface
                            {
                                return $this;
                            }

                            /**
                             * @throws void
                             *
                             * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                             */
                            #[Override]
                            public function withHost(string $host): UriInterface
                            {
                                return $this;
                            }

                            /**
                             * @throws void
                             *
                             * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                             */
                            #[Override]
                            public function withPort(int | null $port): UriInterface
                            {
                                return $this;
                            }

                            /**
                             * @throws void
                             *
                             * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                             */
                            #[Override]
                            public function withPath(string $path): UriInterface
                            {
                                return $this;
                            }

                            /**
                             * @throws void
                             *
                             * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                             */
                            #[Override]
                            public function withQuery(string $query): UriInterface
                            {
                                return $this;
                            }

                            /**
                             * @throws void
                             *
                             * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                             */
                            #[Override]
                            public function withFragment(string $fragment): UriInterface
                            {
                                return $this;
                            }

                            /** @throws void */
                            #[Override]
                            public function __toString(): string
                            {
                                return '';
                            }
                        };
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
                    {
                        return $this;
                    }

                    /**
                     * @return array<mixed>
                     *
                     * @throws void
                     */
                    #[Override]
                    public function getServerParams(): array
                    {
                        return [];
                    }

                    /**
                     * @return array<mixed>
                     *
                     * @throws void
                     */
                    #[Override]
                    public function getCookieParams(): array
                    {
                        return [];
                    }

                    /**
                     * @param array<mixed> $cookies array of key/value pairs representing cookies
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withCookieParams(array $cookies): ServerRequestInterface
                    {
                        return $this;
                    }

                    /**
                     * @return array<mixed>
                     *
                     * @throws void
                     */
                    #[Override]
                    public function getQueryParams(): array
                    {
                        return [];
                    }

                    /**
                     * @param array<mixed> $query array of query string arguments, typically from $_GET
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withQueryParams(array $query): ServerRequestInterface
                    {
                        return $this;
                    }

                    /**
                     * @return array<mixed> an array tree of UploadedFileInterface instances; an empty
                     *      array MUST be returned if no data is present
                     *
                     * @throws void
                     */
                    #[Override]
                    public function getUploadedFiles(): array
                    {
                        return [];
                    }

                    /**
                     * @param array<mixed> $uploadedFiles an array tree of UploadedFileInterface instances
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
                    {
                        return $this;
                    }

                    /**
                     * @return array<mixed>|object|null The deserialized body parameters, if any.
                     *      These will typically be an array or object.
                     *
                     * @throws void
                     */
                    #[Override]
                    public function getParsedBody(): array | object | null
                    {
                        return null;
                    }

                    /**
                     * @param array<mixed>|object|null $data The deserialized body data. This will
                     *      typically be in an array or object.
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withParsedBody($data): ServerRequestInterface
                    {
                        return $this;
                    }

                    /**
                     * @return array<mixed> attributes derived from the request
                     *
                     * @throws void
                     */
                    #[Override]
                    public function getAttributes(): array
                    {
                        return [];
                    }

                    /**
                     * @param mixed $default default value to return if the attribute does not exist
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function getAttribute(string $name, $default = null): mixed
                    {
                        return null;
                    }

                    /**
                     * @param mixed $value the value of the attribute
                     *
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withAttribute(string $name, $value): ServerRequestInterface
                    {
                        return $this;
                    }

                    /**
                     * @throws void
                     *
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
                     */
                    #[Override]
                    public function withoutAttribute(string $name): ServerRequestInterface
                    {
                        return $this;
                    }
                });

                return $navConfig;
            },
        );
        $sm->setFactory(PageFactory::class, InvokableFactory::class);
        $sm->setAlias(PageFactoryInterface::class, PageFactory::class);
        $sm->setFactory(HtmlElementInterface::class, HtmlElementFactory::class);
        $sm->setFactory(HtmlifyInterface::class, HtmlifyFactory::class);
        $sm->setFactory(ContainerParserInterface::class, ContainerParserFactory::class);
        $sm->setFactory(
            'config',
            static fn (): array => [
                'navigation' => [
                    'default' => $config['nav_test1'],
                ],
                'view_helpers' => [
                    'aliases' => [
                        'navigation' => \Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\Navigation::class,
                        'Navigation' => Navigation::class,
                        BaseServerUrlHelper::class => ServerUrlHelper::class,
                        'serverurl' => ServerUrlHelper::class,
                        'serverUrl' => ServerUrlHelper::class,
                        'ServerUrl' => ServerUrlHelper::class,
                        BaseUrlHelper::class => UrlHelper::class,
                        'url' => UrlHelper::class,
                        'Url' => UrlHelper::class,
                    ],
                    'factories' => [
                        Navigation::class => NavigationFactory::class,
                        UrlHelper::class => UrlHelperFactory::class,
                        ServerUrlHelper::class => ServerUrlHelperFactory::class,
                    ],
                ],
                'templates' => [
                    'map' => [
                        'test::menu' => __DIR__ . '/_files/template/views/menu.phtml',
                        'test::menu-with-partials' => __DIR__ . '/_files/template/views/menu_with_partial_params.phtml',
                        'test::bc' => __DIR__ . '/_files/template/views/bc.phtml',
                        'test::bc-separator' => __DIR__ . '/_files/template/views/bc_separator.phtml',
                        'test::bc-with-partials' => __DIR__ . '/_files/template/views/bc_with_partial_params.phtml',
                    ],
                ],
            ],
        );
        $sm->setFactory(ViewHelperPluginManager::class, HelperPluginManagerFactory::class);
        $sm->setFactory(LaminasViewRenderer::class, LaminasViewRendererFactory::class);
        $sm->setFactory(BaseServerUrlHelper::class, InvokableFactory::class);

        // setup containers from config
        $nav1 = $sm->get('nav_test1');
        $nav2 = $sm->get('nav_test2');
        $nav3 = $sm->get('nav_test3');
        $nav4 = $sm->get('nav_test4');

        assert(
            $nav1 instanceof Navigation,
            sprintf(
                '$nav1 should be an Instance of %s, but was %s',
                Navigation::class,
                get_debug_type($nav1),
            ),
        );
        assert(
            $nav2 instanceof Navigation,
            sprintf(
                '$nav2 should be an Instance of %s, but was %s',
                Navigation::class,
                get_debug_type($nav2),
            ),
        );
        assert(
            $nav3 instanceof Navigation,
            sprintf(
                '$nav3 should be an Instance of %s, but was %s',
                Navigation::class,
                get_debug_type($nav3),
            ),
        );
        assert(
            $nav4 instanceof Navigation,
            sprintf(
                '$nav4 should be an Instance of %s, but was %s',
                Navigation::class,
                get_debug_type($nav4),
            ),
        );

        $this->nav1 = $nav1;
        $this->nav2 = $nav2;
        $this->nav3 = $nav3;
        $this->nav4 = $nav4;

        $sm->setService('nav1', $nav1);
        $sm->setService('nav2', $nav2);

        $sm->setAllowOverride(false);
    }

    /**
     * Returns the contens of the expected $file
     *
     * @throws Exception
     */
    protected function getExpected(string $file): string
    {
        $content = file_get_contents($this->files . '/expected/' . $file);

        static::assertIsString(
            $content,
            sprintf('could not load file %s', $this->files . '/expected/' . $file),
        );

        return $content;
    }

    /**
     * Sets up ACL
     *
     * @return array<string, LaminasAcl|string>
     *
     * @throws InvalidArgumentException
     */
    protected function getAcl(): array
    {
        $acl = new Acl();

        $acl->addRole(new GenericRole('guest'));
        $acl->addRole(new GenericRole('member'), 'guest');
        $acl->addRole(new GenericRole('admin'), 'member');
        $acl->addRole(new GenericRole('special'), 'member');

        $acl->addResource(new GenericResource('guest_foo'));
        $acl->addResource(new GenericResource('member_foo'), 'guest_foo');
        $acl->addResource(new GenericResource('admin_foo'));
        $acl->addResource(new GenericResource('special_foo'), 'member_foo');

        $acl->allow('guest', 'guest_foo');
        $acl->allow('member', 'member_foo');
        $acl->allow('admin', 'admin_foo');
        $acl->allow('special', 'special_foo');
        $acl->allow('special', 'admin_foo', 'read');

        return ['acl' => new LaminasAcl($acl), 'role' => 'special'];
    }

    /**
     * Returns translator
     *
     * @throws ContainerModificationsNotAllowedException
     */
    protected function getTranslator(): Translator
    {
        $loader = new TestAsset\ArrayTranslator(
            [
                'Page 1' => 'Side 1',
                'Page 1.1' => 'Side 1.1',
                'Page 2' => 'Side 2',
                'Page 2.3' => 'Side 2.3',
                'Page 2.3.3.1' => 'Side 2.3.3.1',
                'Home' => 'Hjem',
                'Go home' => 'Gå hjem',
            ],
        );

        $translator = new Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', '');

        return $translator;
    }

    /**
     * Returns translator with text domain
     *
     * @throws ContainerModificationsNotAllowedException
     */
    protected function getTranslatorWithTextDomain(): Translator
    {
        $loader1 = new TestAsset\ArrayTranslator(
            [
                'Page 1' => 'TextDomain1 1',
                'Page 1.1' => 'TextDomain1 1.1',
                'Page 2' => 'TextDomain1 2',
                'Page 2.3' => 'TextDomain1 2.3',
                'Page 2.3.3' => 'TextDomain1 2.3.3',
                'Page 2.3.3.1' => 'TextDomain1 2.3.3.1',
            ],
        );

        $loader2 = new TestAsset\ArrayTranslator(
            [
                'Page 1' => 'TextDomain2 1',
                'Page 1.1' => 'TextDomain2 1.1',
                'Page 2' => 'TextDomain2 2',
                'Page 2.3' => 'TextDomain2 2.3',
                'Page 2.3.3' => 'TextDomain2 2.3.3',
                'Page 2.3.3.1' => 'TextDomain2 2.3.3.1',
            ],
        );

        $translator = new Translator();
        $translator->getPluginManager()->setService('default1', $loader1);
        $translator->getPluginManager()->setService('default2', $loader2);
        $translator->addTranslationFile('default1', '', 'LaminasTest_1');
        $translator->addTranslationFile('default2', 'null', 'LaminasTest_2');

        return $translator;
    }
}
