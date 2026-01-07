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

namespace Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\Navigation\AbstractBreadcrumbs;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Override;

use function implode;
use function sprintf;
use function str_repeat;

use const PHP_EOL;

/**
 * Helper for printing breadcrumbs.
 *
 * phpcs:disable SlevomatCodingStandard.Classes.TraitUseDeclaration.MultipleTraitsPerDeclaration
 */
final class Breadcrumbs extends AbstractBreadcrumbs
{
    /**
     * Renders breadcrumbs by chaining 'a' elements with the separator
     * registered in the helper.
     *
     * @param ContainerInterface<PageInterface>|string|null $container [optional] container to render. Default is
     *                                                  to render the container registered in the helper.
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    #[Override]
    public function renderStraight(ContainerInterface | string | null $container = null): string
    {
        $content = parent::renderStraight($container);

        if ($content === '') {
            return '';
        }

        $html  = $this->getIndent() . '<nav aria-label="breadcrumb">' . PHP_EOL;
        $html .= str_repeat($this->getIndent(), 2) . '<ul class="breadcrumb">' . PHP_EOL;
        $html .= $content;
        $html .= str_repeat($this->getIndent(), 2) . '</ul>' . PHP_EOL;

        return $html . ($this->getIndent() . '</nav>' . PHP_EOL);
    }

    /** @throws void */
    #[Override]
    protected function renderBreadcrumbItem(string $content, string $liClass = '', bool $active = false): string
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

        $html  = str_repeat($this->getIndent(), 3) . sprintf(
            '<li class="%s"%s>',
            implode(' ', $classes),
            $aria,
        ) . PHP_EOL;
        $html .= str_repeat($this->getIndent(), 4) . $content . PHP_EOL;

        return $html . (str_repeat($this->getIndent(), 3) . '</li>' . PHP_EOL);
    }

    /** @throws void */
    #[Override]
    protected function renderSeparator(): string
    {
        return str_repeat($this->getIndent(), 3) . $this->getSeparator() . PHP_EOL;
    }

    /**
     * @param array<string> $html
     *
     * @throws void
     */
    #[Override]
    protected function combineRendered(array $html): string
    {
        return $html !== [] ? implode($this->renderSeparator(), $html) : '';
    }
}
