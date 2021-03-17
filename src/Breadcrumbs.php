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

use Laminas\View\Exception;
use Laminas\View\Helper\AbstractHtmlElement;
use Mezzio\Navigation\ContainerInterface;

/**
 * Helper for printing breadcrumbs.
 */
final class Breadcrumbs extends AbstractHtmlElement implements BreadcrumbsInterface
{
    use BreadcrumbsTrait, HelperTrait{
        BreadcrumbsTrait::getMinDepth insteadof HelperTrait;
        renderStraight as parentRenderStraight;
    }

    /**
     * Renders breadcrumbs by chaining 'a' elements with the separator
     * registered in the helper.
     *
     * @param ContainerInterface|string|null $container [optional] container to render. Default is
     *                                                  to render the container registered in the helper.
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return string
     */
    public function renderStraight($container = null): string
    {
        $content = $this->parentRenderStraight($container);

        if ('' === $content) {
            return '';
        }

        $html = $this->getIndent() . '<nav aria-label="breadcrumb">' . PHP_EOL;
        $html .= $this->getIndent() . $this->getIndent() . '<ul class="breadcrumb">' . PHP_EOL;
        $html .= $this->getIndent() . $this->getIndent() . $content;
        $html .= $this->getIndent() . $this->getIndent() . '</ul>' . PHP_EOL;
        $html .= $this->getIndent() . '</nav>' . PHP_EOL;

        return $html;
    }

    /**
     * @param string $content
     * @param string $liClass
     * @param bool   $active
     *
     * @return string
     */
    private function renderBreadcrumbItem(string $content, string $liClass = '', bool $active = false)
    {
        $classes = ['breadcrumb-item'];
        $aria    = '';

        if ($liClass) {
            $classes[] = $liClass;
        }

        if ($active) {
            $classes[] = 'active';
            $aria      = ' aria-current="page"';
        }

        $html = $this->getIndent() . $this->getIndent() . $this->getIndent() . sprintf('<li class="%s"%s>', implode(' ', $classes), $aria) . PHP_EOL;
        $html .= $this->getIndent() . $this->getIndent() . $this->getIndent() . $this->getIndent() . $content . PHP_EOL;
        $html .= $this->getIndent() . $this->getIndent() . $this->getIndent() . '</li>' . PHP_EOL;

        return $html;
    }
}
