<?php

/**
 * This file is part of the mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use Interop\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager as ViewHelperPluginManager;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function get_debug_type;
use function sprintf;

final class BreadcrumbsFactory
{
    /**
     * Create and return a navigation view helper instance.
     *
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Breadcrumbs
    {
        assert($container instanceof ServiceLocatorInterface);

        $plugin = $container->get(ViewHelperPluginManager::class);
        assert(
            $plugin instanceof ViewHelperPluginManager,
            sprintf(
                '$plugin should be an Instance of %s, but was %s',
                ViewHelperPluginManager::class,
                get_debug_type($plugin),
            ),
        );

        $translator = null;

        if ($plugin->has(Translate::class)) {
            $translator = $plugin->get(Translate::class);

            assert($translator instanceof Translate);
        }

        $htmlify         = $container->get(HtmlifyInterface::class);
        $containerParser = $container->get(ContainerParserInterface::class);
        $escapeHtml      = $plugin->get(EscapeHtml::class);
        $renderer        = $container->get(PartialRendererInterface::class);

        assert($htmlify instanceof HtmlifyInterface);
        assert($containerParser instanceof ContainerParserInterface);
        assert($escapeHtml instanceof EscapeHtml);
        assert($renderer instanceof PartialRendererInterface);

        return new Breadcrumbs(
            htmlify: $htmlify,
            containerParser: $containerParser,
            escaper: $escapeHtml,
            renderer: $renderer,
            translator: $translator,
        );
    }
}
