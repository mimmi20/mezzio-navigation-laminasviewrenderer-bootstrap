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

use InvalidArgumentException;

use function in_array;
use function mb_strstr;
use function sprintf;

trait BootstrapTrait
{
    /**
     * Allowed sizes
     *
     * @var array<string>
     */
    private static array $sizes = [
        'sm',
        'md',
        'lg',
        'xl',
        'xxl', // added in Bootstrap 5
    ];

    /**
     * @throws InvalidArgumentException
     */
    private function getSizeClass(string $size, string $prefix): string
    {
        if (!in_array($size, $this->getSizes(), true)) {
            throw new InvalidArgumentException('Size "' . $size . '" does not exist');
        }

        return $this->getPrefixedClass($size, $prefix);
    }

    /**
     * @return array<string>
     */
    private function getSizes(): array
    {
        return static::$sizes;
    }

    private function getPrefixedClass(string $class, string $prefix): string
    {
        if (!mb_strstr($prefix, '%s')) {
            return $prefix . '-' . $class;
        }

        return sprintf($prefix, $class);
    }
}
