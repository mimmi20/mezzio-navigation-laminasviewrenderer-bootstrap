<?php
/**
 * This file is part of the mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use InvalidArgumentException;

use function in_array;
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
        // added in Bootstrap 5
        'xxl',
    ];

    /** @throws InvalidArgumentException */
    private function getSizeClass(string $size, string $prefix): string
    {
        if (!in_array($size, static::$sizes, true)) {
            throw new InvalidArgumentException('Size "' . $size . '" does not exist');
        }

        return sprintf($prefix, $size);
    }
}
