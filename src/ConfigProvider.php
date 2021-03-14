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
namespace Mezzio\Navigation\LaminasView\View\Helper\Navigation;

final class ConfigProvider
{
    /**
     * Return general-purpose laminas-navigation configuration.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'navigation_helpers' => $this->getNavigationHelperConfig(),
        ];
    }

    /**
     * @return array
     */
    public function getNavigationHelperConfig(): array
    {
        return [
            'factories' => [
                Breadcrumbs::class => BreadcrumbsFactory::class,
                Menu::class => MenuFactory::class,
            ],
        ];
    }
}
