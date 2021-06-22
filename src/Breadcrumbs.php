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

namespace Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use Laminas\View\Exception;
use Laminas\View\Helper\AbstractHtmlElement;
use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\BreadcrumbsInterface;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\BreadcrumbsTrait;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\HelperTrait;

use function implode;
use function sprintf;

use const PHP_EOL;

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
     */
    public function renderStraight($container = null): string
    {
        $content = $this->parentRenderStraight($container);

        if ('' === $content) {
            return '';
        }

        $html  = $this->getIndent() . '<nav aria-label="breadcrumb">' . PHP_EOL;
        $html .= $this->getIndent() . $this->getIndent() . '<ul class="breadcrumb">' . PHP_EOL;
        $html .= $content;
        $html .= $this->getIndent() . $this->getIndent() . '</ul>' . PHP_EOL;
        $html .= $this->getIndent() . '</nav>' . PHP_EOL;

        return $html;
    }

    private function renderBreadcrumbItem(string $content, string $liClass = '', bool $active = false): string
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

        $html  = $this->getIndent() . $this->getIndent() . $this->getIndent() . sprintf('<li class="%s"%s>', implode(' ', $classes), $aria) . PHP_EOL;
        $html .= $this->getIndent() . $this->getIndent() . $this->getIndent() . $this->getIndent() . $content . PHP_EOL;
        $html .= $this->getIndent() . $this->getIndent() . $this->getIndent() . '</li>' . PHP_EOL;

        return $html;
    }

    private function renderSeparator(): string
    {
        return $this->getIndent() . $this->getIndent() . $this->getIndent() . $this->getSeparator() . PHP_EOL;
    }

    /**
     * @param array<string> $html
     */
    private function combineRendered(array $html): string
    {
        return [] !== $html ? implode($this->renderSeparator(), $html) : '';
    }
}
