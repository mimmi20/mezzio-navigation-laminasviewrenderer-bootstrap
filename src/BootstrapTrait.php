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

trait BootstrapTrait
{
    /**
     * Allowed sizes
     *
     * @var string[]
     */
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
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function getSizeClass(string $size, string $prefix): string
    {
        if (!in_array($size, $this->getSizes(), true)) {
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
        if (!mb_strstr($prefix, '%s')) {
            return $prefix . '-' . $class;
        }

        return sprintf($prefix, $class);
    }
}
