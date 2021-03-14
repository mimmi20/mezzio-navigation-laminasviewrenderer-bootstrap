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

use Laminas\Log\Logger;
use Laminas\View\Exception;
use Laminas\View\Helper\AbstractHtmlElement;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\Helper\ContainerParserInterface;
use Mezzio\Navigation\Helper\HtmlifyInterface;
use Mezzio\Navigation\Page\PageInterface;
use RecursiveIteratorIterator;

trait BootstrapTrait
{
    // Allowed sizes
    private static $sizes = [
        'sm',
        'md',
        'lg',
        'xl',
        'xxl', // added in Bootstrap 5
    ];

    /**
     * @param string $size
     * @param string $prefix
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getSizeClass(string $size, string $prefix): string
    {
        if (!in_array($size, $this->getSizes())) {
            throw new \InvalidArgumentException('Size "' . $size . '" does not exist');
        }
        return $this->getPrefixedClass($size, $prefix);
    }

    /**
     * @return string[]
     */
    private function getSizes(): array
    {
        return static::$sizes;
    }

    /**
     * @param string $class
     * @param string $prefix
     *
     * @return string
     */
    private function getPrefixedClass(string $class, string $prefix): string
    {
        if (!strstr($prefix, '%s')) {
            return $prefix . '-' . $class;
        }

        return sprintf($prefix, $class);
    }
}
