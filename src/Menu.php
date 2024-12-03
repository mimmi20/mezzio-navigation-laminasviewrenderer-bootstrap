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

namespace Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation;

use Laminas\I18n;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Laminas\View;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\Navigation\AbstractMenu;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use Override;
use RecursiveIteratorIterator;

use function array_diff_key;
use function array_filter;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function array_splice;
use function array_unique;
use function assert;
use function count;
use function explode;
use function get_debug_type;
use function implode;
use function is_bool;
use function is_int;
use function is_string;
use function rtrim;
use function sprintf;
use function str_repeat;

use const PHP_EOL;

/**
 * Helper for rendering menus from navigation containers.
 *
 * phpcs:disable SlevomatCodingStandard.Classes.TraitUseDeclaration.MultipleTraitsPerDeclaration
 */
final class Menu extends AbstractMenu
{
    use BootstrapTrait;

    /** @api */
    public const string STYLE_UL = 'ul';

    /** @api */
    public const string STYLE_OL = 'ol';

    /** @api */
    public const string STYLE_SUBLINK_LINK = 'link';

    /** @api */
    public const string STYLE_SUBLINK_SPAN = 'span';

    /** @api */
    public const string STYLE_SUBLINK_BUTTON = 'button';

    /** @api */
    public const string STYLE_SUBLINK_DETAILS = 'details';

    /** @api */
    public const string DROP_ORIENTATION_DOWN = 'down';

    /** @api */
    public const string DROP_ORIENTATION_DOWN_CENTERED = 'down-centered';

    /** @api */
    public const string DROP_ORIENTATION_UP = 'up';

    /** @api */
    public const string DROP_ORIENTATION_UP_CENTERED = 'up-centered';

    /** @api */
    public const string DROP_ORIENTATION_START = 'start';

    /** @api */
    public const string DROP_ORIENTATION_END = 'end';

    /**
     * @return void
     *
     * @throws void
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        HtmlifyInterface $htmlify,
        ContainerParserInterface $containerParser,
        View\Helper\EscapeHtmlAttr $escaper,
        PartialRendererInterface $renderer,
        private readonly View\Helper\EscapeHtml $escapeHtml,
        private readonly HtmlElementInterface $htmlElement,
        private readonly I18n\View\Helper\Translate | null $translator = null,
    ) {
        parent::__construct($serviceLocator, $htmlify, $containerParser, $escaper, $renderer);
    }

    /**
     * Renders menu.
     *
     * Implements {@link ViewHelperInterface::render()}.
     *
     * If a partial view is registered in the helper, the menu will be rendered
     * using the given partial script. If no partial is registered, the menu
     * will be rendered as an 'ul' element by the helper's internal method.
     *
     * @param ContainerInterface<PageInterface>|string|null $container [optional] container to render.
     *                                                  Default is null, which indicates
     *                                                  that the helper should render
     *                                                  the container returned by {@link getContainer()}.
     *
     * @throws View\Exception\RuntimeException
     * @throws View\Exception\InvalidArgumentException
     */
    #[Override]
    public function render(ContainerInterface | string | null $container = null): string
    {
        $partial = $this->getPartial();

        if ($partial) {
            return $this->renderPartial($container, $partial);
        }

        return $this->renderMenu($container);
    }

    /**
     * Renders helper.
     *
     * Renders a HTML 'ul' for the given $container. If $container is not given,
     * the container registered in the helper will be used.
     *
     * Available $options:
     *
     * @param ContainerInterface<PageInterface>|string|null $container [optional] container to create menu from.
     *                                                                 Default is to use the container retrieved from {@link getContainer()}.
     * @param array<string, bool|int|string|null>           $options   [optional] options for controlling rendering
     * @phpstan-param array{ulClass?: string|null, liClass?: string|null, indent?: int|string|null, minDepth?: int|null, maxDepth?: int|null, onlyActiveBranch?: bool, escapeLabels?: bool, renderParents?: bool, addClassToListItem?: bool, liActiveClass?: string|null, tabs?: bool, pills?: bool, fill?: bool, justified?: bool, centered?: bool, right-aligned?: bool, vertical?: string, direction?: string, style?: string, substyle?: string, sublink?: string, in-navbar?: bool} $options
     *
     * @throws View\Exception\RuntimeException
     * @throws View\Exception\InvalidArgumentException
     */
    #[Override]
    public function renderMenu(ContainerInterface | string | null $container = null, array $options = []): string
    {
        try {
            $container = $this->containerParser->parseContainer($container);
        } catch (InvalidArgumentException $e) {
            throw new View\Exception\InvalidArgumentException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        if ($container === null) {
            $container = $this->getContainer();
        }

        $options = $this->normalizeOptions($options);

        assert($container instanceof ContainerInterface);

        if ($options['onlyActiveBranch'] && !$options['renderParents']) {
            return $this->renderDeepestMenu($container, $options);
        }

        return $this->renderNormalMenu($container, $options);
    }

    /**
     * Renders the inner-most sub menu for the active page in the $container.
     *
     * This is a convenience method which is equivalent to the following call:
     * <code>
     * renderMenu($container, array(
     *     'indent'           => $indent,
     *     'ulClass'          => $ulClass,
     *     'liClass'          => $liClass,
     *     'minDepth'         => null,
     *     'maxDepth'         => null,
     *     'onlyActiveBranch' => true,
     *     'renderParents'    => false,
     *     'liActiveClass'    => $liActiveClass
     * ));
     * </code>
     *
     * @param ContainerInterface<PageInterface>|string|null $container     [optional] container to create menu from.
     *                                                                     Default is to use the container retrieved from {@link getContainer()}.
     * @param string|null                                   $ulClass       [optional] CSS class to use for UL element.
     *                                                                     Default is to use the value from {@link getUlClass()}.
     * @param string|null                                   $liClass       [optional] CSS class to use for LI elements.
     *                                                                     Default is to use the value from {@link getLiClass()}.
     * @param int|string|null                               $indent        [optional] indentation as a string or number
     *                                                                     of spaces. Default is to use the value retrieved from
     *                                                                     {@link getIndent()}.
     * @param string|null                                   $liActiveClass [optional] CSS class to use for UL
     *                                                                     element. Default is to use the value from {@link getUlClass()}.
     *
     * @throws View\Exception\RuntimeException
     * @throws View\Exception\InvalidArgumentException
     */
    #[Override]
    public function renderSubMenu(
        ContainerInterface | string | null $container = null,
        string | null $ulClass = null,
        string | null $liClass = null,
        int | string | null $indent = null,
        string | null $liActiveClass = null,
    ): string {
        $this->setMaxDepth(null);
        $this->setMinDepth(null);
        $this->setRenderParents(false);
        $this->setAddClassToListItem(false);

        return $this->renderMenu(
            $container,
            [
                'indent' => $indent,
                'ulClass' => $ulClass,
                'liClass' => $liClass,
                'onlyActiveBranch' => true,
                'escapeLabels' => true,
                'liActiveClass' => $liActiveClass,
            ],
        );
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty.
     *
     * Overrides {@link AbstractHelper::htmlify()}.
     *
     * @param PageInterface $page               page to generate HTML for
     * @param bool          $escapeLabel        Whether to escape the label
     * @param bool          $addClassToListItem Whether to add the page class to the list item
     *
     * @throws View\Exception\InvalidArgumentException
     * @throws View\Exception\RuntimeException
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    #[Override]
    public function htmlify(PageInterface $page, bool $escapeLabel = true, bool $addClassToListItem = false): string
    {
        return $this->toHtml($page, ['escapeLabels' => $escapeLabel, 'sublink' => null], [], true);
    }

    /**
     * Normalizes given render options
     *
     * @param array<string, bool|int|string|null> $options [optional] options to normalize
     * @phpstan-param array{ulClass?: string|null, liClass?: string|null, indent?: int|string|null, minDepth?: int|null, maxDepth?: int|null, onlyActiveBranch?: bool, escapeLabels?: bool, renderParents?: bool, addClassToListItem?: bool, liActiveClass?: string|null, tabs?: bool, pills?: bool, fill?: bool, justified?: bool, centered?: bool, right-aligned?: bool, vertical?: string, direction?: string, style?: string, substyle?: string, sublink?: string, in-navbar?: bool} $options
     *
     * @return array<string, bool|int|string|null>
     * @phpstan-return array{ulClass: string, liClass: string, indent: string, minDepth: int, maxDepth: int|null, onlyActiveBranch: bool, escapeLabels: bool, renderParents: bool, addClassToListItem: bool, liActiveClass: string, role: string|null, style: string, substyle: string, sublink: string, class: string, ulRole: string|null, liRole: string|null, direction: string}
     *
     * @throws View\Exception\InvalidArgumentException
     */
    #[Override]
    protected function normalizeOptions(array $options = []): array
    {
        if (isset($options['indent'])) {
            assert(is_int($options['indent']) || is_string($options['indent']));
            $options['indent'] = $this->getWhitespace($options['indent']);
        } else {
            $options['indent'] = $this->getIndent();
        }

        if (!array_key_exists('liClass', $options) || $options['liClass'] === null) {
            $options['liClass'] = $this->getLiClass();
        }

        if (!array_key_exists('minDepth', $options) || $options['minDepth'] === null) {
            $options['minDepth'] = $this->getMinDepth();
        }

        if ($options['minDepth'] < 0 || $options['minDepth'] === null) {
            $options['minDepth'] = 0;
        }

        if (!array_key_exists('maxDepth', $options) || $options['maxDepth'] === null) {
            $options['maxDepth'] = $this->getMaxDepth();
        }

        if (!array_key_exists('onlyActiveBranch', $options)) {
            $options['onlyActiveBranch'] = $this->getOnlyActiveBranch();
        }

        if (!array_key_exists('escapeLabels', $options)) {
            $options['escapeLabels'] = $this->getEscapeLabels();
        }

        if (!array_key_exists('renderParents', $options)) {
            $options['renderParents'] = $this->getRenderParents();
        }

        if (!array_key_exists('addClassToListItem', $options)) {
            $options['addClassToListItem'] = $this->getAddClassToListItem();
        }

        if (!array_key_exists('liActiveClass', $options) || $options['liActiveClass'] === null) {
            $options['liActiveClass'] = $this->getLiActiveClass();
        }

        if (
            array_key_exists('vertical', $options)
            && is_string($options['vertical'])
            && !array_key_exists('direction', $options)
        ) {
            $options['direction'] = self::DROP_ORIENTATION_END;
        } elseif (!array_key_exists('direction', $options)) {
            $options['direction'] = self::DROP_ORIENTATION_DOWN;
        }

        $options['ulClass'] = $this->normalizeUlClass($options);
        $options['class']   = $this->normalizeItemClass($options);
        $options['ulRole']  = null;
        $options['liRole']  = null;
        $options['role']    = null;

        if (array_key_exists('tabs', $options) || array_key_exists('pills', $options)) {
            $options['ulRole'] = 'tablist';
            $options['liRole'] = 'presentation';
            $options['role']   = 'tab';
        }

        if (!array_key_exists('style', $options)) {
            $options['style'] = self::STYLE_UL;
        }

        if (!array_key_exists('substyle', $options)) {
            $options['substyle'] = self::STYLE_UL;
        }

        if (!array_key_exists('sublink', $options)) {
            $options['sublink'] = self::STYLE_SUBLINK_LINK;
        }

        return $options;
    }

    /**
     * Renders the deepest active menu within [minDepth, maxDepth], (called from {@link renderMenu()}).
     *
     * @param ContainerInterface<PageInterface>   $container container to render
     * @param array<string, bool|int|string|null> $options   options for controlling rendering
     * @phpstan-param array{ulClass: string, liClass: string, indent: string, minDepth: int, maxDepth: int|null, onlyActiveBranch: bool, escapeLabels: bool, renderParents: bool, addClassToListItem: bool, liActiveClass: string, role: string|null, style: string, substyle: string, sublink: string, class: string, ulRole: string|null, liRole: string|null, direction: string} $options
     *
     * @throws View\Exception\InvalidArgumentException
     * @throws View\Exception\RuntimeException
     */
    private function renderDeepestMenu(ContainerInterface $container, array $options): string
    {
        assert(is_string($options['ulClass']));
        assert(is_string($options['liClass']));
        assert(is_string($options['indent']));
        assert(is_int($options['minDepth']));
        assert(is_bool($options['onlyActiveBranch']));
        assert(is_bool($options['escapeLabels']));
        assert(is_bool($options['addClassToListItem']));
        assert(is_string($options['liActiveClass']));

        $active = $this->findActive($container, $options['minDepth'] - 1, $options['maxDepth']);

        if (!array_key_exists('page', $active) || !($active['page'] instanceof PageInterface)) {
            return '';
        }

        $activePage = $active['page'];

        // special case if active page is one below minDepth
        if (!array_key_exists('depth', $active) || $active['depth'] < $options['minDepth']) {
            if (!$activePage->hasPages(!$this->renderInvisible)) {
                return '';
            }
        } elseif (!$active['page']->hasPages(!$this->renderInvisible)) {
            // found pages has no children; render siblings
            $activePage = $active['page']->getParent();
        } elseif (is_int($options['maxDepth']) && $active['depth'] + 1 > $options['maxDepth']) {
            // children are below max depth; render siblings
            $activePage = $active['page']->getParent();
        }

        assert(
            $activePage instanceof ContainerInterface,
            sprintf(
                '$activePage should be an Instance of %s, but was %s',
                ContainerInterface::class,
                get_debug_type($activePage),
            ),
        );

        $subHtml = '';

        foreach ($activePage as $subPage) {
            if (!$this->accept($subPage)) {
                continue;
            }

            $isActive = $subPage->isActive(true);

            // render li tag and page
            $liClasses      = [];
            $pageAttributes = [];

            $this->setAttributes($subPage, $options, 0, false, $isActive, $liClasses, $pageAttributes);

            $subHtml .= $options['indent'] . '    <li';

            if ($liClasses !== []) {
                $subHtml .= ' class="' . ($this->escaper)(implode(' ', $liClasses)) . '"';
            }

            if (!empty($options['liRole'])) {
                $subHtml .= ' role="' . ($this->escaper)($options['liRole']) . '"';
            }

            $subHtml .= '>' . PHP_EOL;
            $subHtml .= $options['indent'] . '        ';
            $subHtml .= $this->toHtml($subPage, $options, $pageAttributes, false);
            $subHtml .= PHP_EOL;
            $subHtml .= $options['indent'] . '    </li>' . PHP_EOL;
        }

        if ($subHtml === '') {
            return '';
        }

        $html = $options['indent'] . '<ul';

        if ($options['ulClass']) {
            $html .= ' class="' . ($this->escaper)($options['ulClass']) . '"';
        }

        if (!empty($options['ulRole'])) {
            $html .= ' role="' . ($this->escaper)($options['ulRole']) . '"';
        }

        $html .= '>' . PHP_EOL;

        return $html . ($subHtml . $options['indent'] . '</ul>');
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()}).
     *
     * @param ContainerInterface<PageInterface>   $container container to render
     * @param array<string, bool|int|string|null> $options   options for controlling rendering
     * @phpstan-param array{ulClass: string, liClass: string, indent: string, minDepth: int, maxDepth: int|null, onlyActiveBranch: bool, escapeLabels: bool, renderParents: bool, addClassToListItem: bool, liActiveClass: string, role: string|null, style: string, substyle: string, sublink: string, class: string, ulRole: string|null, liRole: string|null, direction: string} $options
     *
     * @throws View\Exception\InvalidArgumentException
     * @throws View\Exception\RuntimeException
     */
    private function renderNormalMenu(ContainerInterface $container, array $options): string
    {
        $html = '';

        assert(is_string($options['ulClass']));
        assert(is_string($options['liClass']));
        assert(is_string($options['indent']));
        assert(is_int($options['minDepth']));
        assert(is_int($options['maxDepth']) || $options['maxDepth'] === null);
        assert(is_bool($options['onlyActiveBranch']));
        assert(is_bool($options['escapeLabels']));
        assert(is_bool($options['addClassToListItem']));
        assert(is_string($options['liActiveClass']));
        assert(is_string($options['role']) || $options['role'] === null);

        // find deepest active
        $found = $this->findActive($container, $options['minDepth'], $options['maxDepth']);

        // create iterator
        $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);

        if (is_int($options['maxDepth'])) {
            $iterator->setMaxDepth($options['maxDepth']);
        }

        // iterate container
        $prevDepth = -1;
        $prevPage  = null;

        $element = match ($options['style']) {
            self::STYLE_OL => 'ol',
            default => 'ul',
        };

        foreach ($iterator as $page) {
            assert($page instanceof PageInterface);

            $depth = $iterator->getDepth();

            [$accept, $isActive] = $this->isPageAccepted($page, $options, $depth, $found);

            if (!$accept) {
                continue;
            }

            // make sure indentation is correct
            $iteratorDepth = $depth;

            assert(is_int($options['minDepth']));

            $depth   -= $options['minDepth'];
            $myIndent = $options['indent'] . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($depth === 0) {
                    $ulClass = ' class="' . ($this->escaper)($options['ulClass']) . '"';

                    if (!empty($options['ulRole'])) {
                        $ulClass .= ' role="' . ($this->escaper)($options['ulRole']) . '"';
                    }
                } else {
                    $ulClasses = ['dropdown-menu'];

                    if ($options['sublink'] === self::STYLE_SUBLINK_DETAILS) {
                        $ulClasses[] = 'dropdown-details-menu';
                    }

                    if (array_key_exists('dark', $options)) {
                        $ulClasses[] = 'dropdown-menu-dark';
                    }

                    $ulClass = ' class="' . ($this->escaper)(implode(' ', $ulClasses)) . '"';

                    if ($prevPage?->getId() !== null) {
                        $ulClass .= ' aria-labelledby="' . ($this->escaper)($prevPage->getId()) . '"';
                    }
                }

                $html .= $myIndent . '<' . $element . $ulClass . '>' . PHP_EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; --$i) {
                    $ind   = $options['indent'] . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . PHP_EOL;
                    $html .= $ind . '</' . $element . '>' . PHP_EOL;

                    if ($options['sublink'] !== self::STYLE_SUBLINK_DETAILS) {
                        continue;
                    }

                    $html .= $ind . '</details>' . PHP_EOL;
                }

                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            }

            $anySubpageAccepted = $this->hasAcceptedSubpages($page, $options, $iteratorDepth);

            // render li tag and page
            $liClasses      = [];
            $pageAttributes = [];

            $this->setAttributes(
                $page,
                $options,
                $depth,
                $anySubpageAccepted,
                $isActive,
                $liClasses,
                $pageAttributes,
            );

            $liClass = $liClasses === []
                ? ''
                : ' class="' . ($this->escaper)(implode(
                    ' ',
                    array_unique($liClasses),
                )) . '"';

            if ($depth === 0 && !empty($options['liRole'])) {
                $liClass .= ' role="' . ($this->escaper)($options['liRole']) . '"';
            }

            $html .= $myIndent . '    <li' . $liClass . '>' . PHP_EOL;

            if ($anySubpageAccepted && $options['sublink'] === self::STYLE_SUBLINK_DETAILS) {
                $html .= $myIndent . '        <details>' . PHP_EOL;
            }

            $html .= $myIndent . '        ';
            $html .= $this->toHtml($page, $options, $pageAttributes, $anySubpageAccepted);
            $html .= PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
            $prevPage  = $page;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; 0 < $i; --$i) {
                $myIndent = $options['indent'] . str_repeat('        ', $i - 1);
                $html    .= $myIndent . '    </li>' . PHP_EOL;
                $html    .= $myIndent . '</' . $element . '>' . PHP_EOL;

                if (1 >= $i || $options['sublink'] !== self::STYLE_SUBLINK_DETAILS) {
                    continue;
                }

                $html .= $myIndent . '</details>' . PHP_EOL;
            }

            $html = rtrim($html, PHP_EOL);
        }

        return $html;
    }

    /**
     * @param PageInterface                       $page    current page to check
     * @param array<string, bool|int|string|null> $options options for controlling rendering
     * @param int                                 $level   current level of rendering
     * @phpstan-param array{ulClass: string, liClass: string, indent: string, minDepth: int, maxDepth: int|null, onlyActiveBranch: bool, escapeLabels: bool, renderParents: bool, addClassToListItem: bool, liActiveClass: string, role: string|null, style: string, substyle: string, sublink: string, class: string, ulRole: string|null, liRole: string|null, direction: string} $options
     *
     * @throws View\Exception\RuntimeException
     */
    private function hasAcceptedSubpages(PageInterface $page, array $options, int $level): bool
    {
        $hasVisiblePages    = $page->hasPages(true);
        $anySubpageAccepted = false;

        assert(is_int($options['maxDepth']) || $options['maxDepth'] === null);

        if ($hasVisiblePages && ($options['maxDepth'] === null || $level + 1 <= $options['maxDepth'])) {
            foreach ($page->getPages() as $subpage) {
                if (!$this->accept($subpage, false)) {
                    continue;
                }

                $anySubpageAccepted = true;
            }
        }

        return $anySubpageAccepted;
    }

    /**
     * @param PageInterface                         $page    current page to check
     * @param array<string, bool|int|string|null>   $options options for controlling rendering
     * @param int                                   $level   current level of rendering
     * @param array<string, int|PageInterface|null> $found
     * @phpstan-param array{ulClass: string, liClass: string, indent: string, minDepth: int, maxDepth: int|null, onlyActiveBranch: bool, escapeLabels: bool, renderParents: bool, addClassToListItem: bool, liActiveClass: string, role: string|null, style: string, substyle: string, sublink: string, class: string, ulRole: string|null, liRole: string|null, direction: string} $options
     * @phpstan-param array{page?: PageInterface|null, depth?: int|null} $found
     *
     * @return array<bool>
     *
     * @throws View\Exception\RuntimeException
     */
    private function isPageAccepted(PageInterface $page, array $options, int $level, array $found): array
    {
        if ($level < $options['minDepth'] || !$this->accept($page)) {
            // page is below minDepth or not accepted by acl/visibility
            return [false, false];
        }

        $isActive = $page->isActive(true);
        $accept   = true;

        assert(is_int($options['maxDepth']) || $options['maxDepth'] === null);

        if ($options['onlyActiveBranch'] && !$isActive) {
            // page is not active itself, but might be in the active branch
            $accept = $this->isActiveBranch($found, $page, $options['maxDepth']);
        }

        return [$accept, $isActive];
    }

    /**
     * @param PageInterface                       $page           current page to check
     * @param array<string, bool|int|string|null> $options        options for controlling rendering
     * @param int                                 $level          current level of rendering
     * @param array<int, string>                  $liClasses
     * @param array<string, string>               $pageAttributes
     * @phpstan-param array{ulClass: string, liClass: string, indent: string, minDepth: int, maxDepth: int|null, onlyActiveBranch: bool, escapeLabels: bool, renderParents: bool, addClassToListItem: bool, liActiveClass: string, role: string|null, style: string, substyle: string, sublink: string, class: string, ulRole: string|null, liRole: string|null, direction: string} $options
     *
     * @throws void
     */
    private function setAttributes(
        PageInterface $page,
        array $options,
        int $level,
        bool $anySubpageAccepted,
        bool $isActive,
        array &$liClasses,
        array &$pageAttributes,
    ): void {
        $pageClasses = [];

        if ($level === 0) {
            $liClasses[]   = 'nav-item';
            $pageClasses[] = 'nav-link';

            if (!empty($options['role']) && !$anySubpageAccepted) {
                $pageAttributes['role'] = $options['role'];
            }
        } else {
            $pageClasses[] = 'dropdown-item';
        }

        $pageClasses[] = 'btn';

        if ($anySubpageAccepted) {
            $liClasses[] = match ($options['direction']) {
                self::DROP_ORIENTATION_UP_CENTERED => $options['sublink'] === self::STYLE_SUBLINK_DETAILS ? 'dropup' : 'dropup-center',
                self::DROP_ORIENTATION_UP => 'dropup',
                self::DROP_ORIENTATION_END => 'dropend',
                self::DROP_ORIENTATION_START => 'dropstart',
                default => 'dropdown',
            };

            if (
                $options['sublink'] === self::STYLE_SUBLINK_DETAILS
                && $options['direction'] !== self::DROP_ORIENTATION_DOWN
            ) {
                $pageAttributes['data-popper-placement'] = match ($options['direction']) {
                    self::DROP_ORIENTATION_UP_CENTERED => 'top',
                    self::DROP_ORIENTATION_UP => 'top-start',
                    self::DROP_ORIENTATION_END => 'right-start',
                    self::DROP_ORIENTATION_START => 'left-start',
                    self::DROP_ORIENTATION_DOWN_CENTERED => 'bottom',
                    default => null,
                };
            }

            $pageClasses[] = 'dropdown-toggle';

            if ($options['sublink'] !== self::STYLE_SUBLINK_DETAILS) {
                $pageAttributes['data-bs-toggle'] = 'dropdown';
            }

            $pageAttributes['aria-expanded'] = 'false';
            $pageAttributes['role']          = 'button';
        }

        // Is page active?
        if ($isActive) {
            array_splice($liClasses, count($liClasses), 0, explode(' ', $options['liActiveClass']));

            if ($level === 0) {
                $pageAttributes['aria-current'] = 'page';
            }
        }

        if ($options['liClass']) {
            array_splice($liClasses, count($liClasses), 0, explode(' ', $options['liClass']));
        }

        if ($page->getLiClass()) {
            array_splice($liClasses, count($liClasses), 0, explode(' ', $page->getLiClass()));
        }

        // Add CSS class from page to <li>
        if ($options['addClassToListItem'] && $page->getClass()) {
            array_splice($liClasses, count($liClasses), 0, explode(' ', $page->getClass()));
        } elseif ($page->getClass()) {
            array_splice($pageClasses, count($pageClasses), 0, explode(' ', $page->getClass()));
        }

        $pageAttributes['class'] = implode(' ', array_unique($pageClasses));
    }

    /**
     * Returns an HTML string for the given page
     *
     * @param PageInterface                       $page       page to generate HTML for
     * @param array<string, bool|int|string|null> $options    options for controlling rendering
     * @param array<string, string>               $attributes
     * @phpstan-param array{ulClass?: string, liClass?: string, indent?: string, minDepth?: int, maxDepth?: int|null, onlyActiveBranch?: bool, escapeLabels: bool, renderParents?: bool, addClassToListItem?: bool, liActiveClass?: string, role?: string|null, style?: string, substyle?: string, sublink: string|null, class?: string, ulRole?: string|null, liRole?: string|null, direction?: string} $options
     *
     * @return string HTML string
     *
     * @throws View\Exception\RuntimeException
     * @throws View\Exception\InvalidArgumentException
     */
    private function toHtml(PageInterface $page, array $options, array $attributes, bool $anySubpageAccepted): string
    {
        $label = (string) $page->getLabel();
        $title = $page->getTitle();

        if ($this->translator !== null) {
            try {
                $label = ($this->translator)($label, $page->getTextDomain());

                if ($title !== null) {
                    $title = ($this->translator)($title, $page->getTextDomain());
                }
            } catch (RuntimeException $e) {
                throw new View\Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        }

        // get attribs for element

        $attributes['id']    = $page->getId();
        $attributes['title'] = $title;

        if ($anySubpageAccepted && $options['sublink'] === self::STYLE_SUBLINK_DETAILS) {
            $element = 'summary';
        } elseif ($anySubpageAccepted && $options['sublink'] === self::STYLE_SUBLINK_BUTTON) {
            $element            = 'button';
            $attributes['type'] = 'button';
        } elseif (
            (
                $anySubpageAccepted
                && $options['sublink'] === self::STYLE_SUBLINK_SPAN
            )
            || !$page->getHref()
        ) {
            $element = 'span';
        } else {
            $element              = 'a';
            $attributes['href']   = $page->getHref();
            $attributes['target'] = $page->getTarget();
        }

        // remove sitemap specific attributes
        $attributes = array_diff_key(
            array_merge(
                $attributes,
                array_filter(
                    $page->getCustomProperties(),
                    static fn ($propertyValue): bool => is_string($propertyValue),
                ),
            ),
            array_flip(['lastmod', 'changefreq', 'priority']),
        );

        if ($options['escapeLabels']) {
            $label = ($this->escapeHtml)($label);
            assert(is_string($label));
        }

        return $this->htmlElement->toHtml($element, $attributes, $label);
    }

    /**
     * @param array<string, bool|int|string|null> $options [optional] options to normalize
     * @phpstan-param array{ulClass?: string|null, liClass?: string|null, indent?: int|string|null, minDepth?: int|null, maxDepth?: int|null, onlyActiveBranch?: bool, escapeLabels?: bool, renderParents?: bool, addClassToListItem?: bool, liActiveClass?: string|null, tabs?: bool, pills?: bool, fill?: bool, justified?: bool, centered?: bool, right-aligned?: bool, vertical?: string, direction?: string, style?: string, substyle?: string, sublink?: string, in-navbar?: bool, style?: string|null, sublink?: string|null} $options
     *
     * @throws View\Exception\InvalidArgumentException
     */
    private function normalizeUlClass(array $options): string
    {
        $ulClasses = array_key_exists('in-navbar', $options) ? ['navbar-nav'] : ['nav'];

        array_splice(
            $ulClasses,
            count($ulClasses),
            0,
            explode(' ', array_key_exists('ulClass', $options) && $options['ulClass'] !== null
                ? $options['ulClass']
                : $this->getUlClass()),
        );

        foreach (
            [
                'tabs' => 'nav-tabs',
                'pills' => 'nav-pills',
                'fill' => 'nav-fill',
                'justified' => 'nav-justified',
                'centered' => 'justify-content-center',
                'right-aligned' => 'justify-content-end',
            ] as $optionname => $optionvalue
        ) {
            if (!array_key_exists($optionname, $options)) {
                continue;
            }

            $ulClasses[] = $optionvalue;
        }

        if (array_key_exists('vertical', $options) && is_string($options['vertical'])) {
            $ulClasses[] = 'flex-column';
            $ulClasses[] = $this->getSizeClass($options['vertical'], 'flex-%s-row');
        }

        return implode(' ', $ulClasses);
    }

    /**
     * @param array<string, bool|int|string|null> $options [optional] options to normalize
     * @phpstan-param array{ulClass?: string|null, liClass?: string|null, indent?: int|string|null, minDepth?: int|null, maxDepth?: int|null, onlyActiveBranch?: bool, escapeLabels?: bool, renderParents?: bool, addClassToListItem?: bool, liActiveClass?: string|null, tabs?: bool, pills?: bool, fill?: bool, justified?: bool, centered?: bool, right-aligned?: bool, vertical?: string, direction?: string, style?: string, substyle?: string, sublink?: string, in-navbar?: bool, style?: string|null, sublink?: string|null} $options
     *
     * @throws View\Exception\InvalidArgumentException
     */
    private function normalizeItemClass(array $options): string
    {
        $itemClasses = [];

        if (array_key_exists('vertical', $options) && is_string($options['vertical'])) {
            $itemClasses[] = $this->getSizeClass($options['vertical'], 'flex-%s-fill');
            $itemClasses[] = $this->getSizeClass($options['vertical'], 'text-%s-center');
        }

        return implode(' ', $itemClasses);
    }
}
