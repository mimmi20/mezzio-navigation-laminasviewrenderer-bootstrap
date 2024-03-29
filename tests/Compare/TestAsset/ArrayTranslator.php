<?php
/**
 * This file is part of the mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

/** @see       https://github.com/laminas/laminas-view for the canonical source repository */

namespace Mimmi20Test\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Compare\TestAsset;

use Laminas\I18n\Translator;

/** phpcs:disable SlevomatCodingStandard.Classes.ForbiddenPublicProperty.ForbiddenPublicProperty */
final class ArrayTranslator implements Translator\Loader\FileLoaderInterface
{
    /** @var array<string, string> */
    public array $translations = [];

    /**
     * Load translations from a file.
     *
     * @param string $locale
     * @param string $filename
     *
     * @return Translator\TextDomain<string, string>
     *
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function load($filename, $locale): Translator\TextDomain
    {
        return new Translator\TextDomain($this->translations);
    }
}
