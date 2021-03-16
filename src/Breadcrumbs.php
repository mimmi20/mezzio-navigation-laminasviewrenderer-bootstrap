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

use Laminas\I18n\View\Helper\Translate;
use Laminas\Log\Logger;
use Laminas\View\Exception;
use Laminas\View\Helper\AbstractHtmlElement;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Model\ModelInterface;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\Helper\ContainerParserInterface;
use Mezzio\Navigation\Helper\HtmlifyInterface;
use Mezzio\Navigation\Page\PageInterface;

/**
 * Helper for printing breadcrumbs.
 */
final class Breadcrumbs extends AbstractHtmlElement implements BreadcrumbsInterface
{
    use BreadcrumbsTrait, HelperTrait{
        BreadcrumbsTrait::getMinDepth insteadof HelperTrait;
    }

    /**
     * @param string $html
     * @param string $liClass
     * @param bool   $active
     *
     * @return string
     */
    private function renderBreadcrumbItem(string $html, string $liClass = '', bool $active = false)
    {
        $classes = ['breadcrumb-item', $liClass];
        $aria    = '';

        if ($active) {
            $classes[] = 'active';
            $aria      = ' aria-current="page"';
        }

        $html = sprintf(
            '<li class="%s"%s>%s</li>',
            implode(' ', $classes),
            $aria,
            $html
        );

        return '        ' . $html;
    }
}
