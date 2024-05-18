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

namespace Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

final class ConfigProvider
{
    /**
     * Return general-purpose laminas-navigation configuration.
     *
     * @return array<string, array<string, array<string, string>>>
     *
     * @throws void
     */
    public function __invoke(): array
    {
        return [
            'navigation_helpers' => $this->getNavigationHelperConfig(),
        ];
    }

    /**
     * @return array<string, array<string, string>>
     *
     * @throws void
     *
     * @api
     */
    public function getNavigationHelperConfig(): array
    {
        return [
            'aliases' => [
                'breadcrumbs' => Breadcrumbs::class,
                'menu' => Menu::class,
            ],
            'factories' => [
                Breadcrumbs::class => BreadcrumbsFactory::class,
                Menu::class => MenuFactory::class,
            ],
        ];
    }
}
