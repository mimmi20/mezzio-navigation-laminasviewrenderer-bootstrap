<?php
/**
 * This file is part of the mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace MezzioTest\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Breadcrumbs;
use Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\ConfigProvider;
use Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

final class ConfigProviderTest extends TestCase
{
    private ConfigProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new ConfigProvider();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testProviderDefinesExpectedFactoryServices(): void
    {
        $navigationHelperConfig = $this->provider->getNavigationHelperConfig();
        self::assertIsArray($navigationHelperConfig);

        self::assertArrayHasKey('factories', $navigationHelperConfig);
        $factories = $navigationHelperConfig['factories'];
        self::assertIsArray($factories);
        self::assertArrayHasKey(Breadcrumbs::class, $factories);
        self::assertArrayHasKey(Menu::class, $factories);

        self::assertArrayHasKey('aliases', $navigationHelperConfig);
        $aliases = $navigationHelperConfig['aliases'];
        self::assertIsArray($aliases);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationReturnsArrayWithDependencies(): void
    {
        $config = ($this->provider)();

        self::assertIsArray($config);
        self::assertArrayHasKey('navigation_helpers', $config);

        $navigationHelperConfig = $config['navigation_helpers'];
        self::assertIsArray($navigationHelperConfig);

        self::assertArrayHasKey('factories', $navigationHelperConfig);
        $factories = $navigationHelperConfig['factories'];
        self::assertIsArray($factories);
        self::assertArrayHasKey(Breadcrumbs::class, $factories);
        self::assertArrayHasKey(Menu::class, $factories);

        self::assertArrayHasKey('aliases', $navigationHelperConfig);
        $aliases = $navigationHelperConfig['aliases'];
        self::assertIsArray($aliases);
    }
}
