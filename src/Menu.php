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
use Laminas\I18n\View\Helper\Translate;
use Laminas\Log\Logger;
use Laminas\View\Exception;
use Laminas\View\Helper\AbstractHtmlElement;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\Helper\ContainerParserInterface;
use Mezzio\Navigation\Helper\HtmlElementInterface;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\HelperTrait;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\MenuInterface;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\MenuTrait;
use Mezzio\Navigation\Page\PageInterface;
use RecursiveIteratorIterator;

use function array_diff_key;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function get_class;
use function gettype;
use function implode;
use function is_bool;
use function is_int;
use function is_object;
use function is_string;
use function rtrim;
use function sprintf;
use function str_repeat;

use const PHP_EOL;

/**
 * Helper for rendering menus from navigation containers.
 */
final class Menu extends AbstractHtmlElement implements MenuInterface
{
    use BootstrapTrait, HelperTrait, MenuTrait {
        MenuTrait::htmlify insteadof HelperTrait;
        MenuTrait::normalizeOptions as parentNormalizeOptions;
    }

    public const STYLE_UL = 'ul';
    public const STYLE_OL = 'ol';

    public const STYLE_SUBLINK_LINK    = 'link';
    public const STYLE_SUBLINK_SPAN    = 'span';
    public const STYLE_SUBLINK_BUTTON  = 'button';
    public const STYLE_SUBLINK_DETAILS = 'details';

    public const DROP_ORIENTATION_DOWN  = 'down';
    public const DROP_ORIENTATION_UP    = 'up';
    public const DROP_ORIENTATION_START = 'start';
    public const DROP_ORIENTATION_END   = 'end';

    private ?Translate $translator = null;

    private EscapeHtml $escapeHtml;

    private HtmlElementInterface $htmlElement;

    public function __construct(
        \Interop\Container\ContainerInterface $serviceLocator,
        Logger $logger,
        ContainerParserInterface $containerParser,
        EscapeHtmlAttr $escapeHtmlAttr,
        LaminasViewRenderer $renderer,
        EscapeHtml $escapeHtml,
        HtmlElementInterface $htmlElement,
        ?Translate $translator = null
    ) {
        $this->serviceLocator  = $serviceLocator;
        $this->logger          = $logger;
        $this->containerParser = $containerParser;
        $this->escaper         = $escapeHtmlAttr;
        $this->renderer        = $renderer;
        $this->escapeHtml      = $escapeHtml;
        $this->translator      = $translator;
        $this->htmlElement     = $htmlElement;
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
     * @see renderPartial()
     * @see renderMenu()
     *
     * @param ContainerInterface|string|null $container [optional] container to render.
     *                                                  Default is null, which indicates
     *                                                  that the helper should render
     *                                                  the container returned by {@link getContainer()}.
     *
     * @throws InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function render($container = null): string
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
     * @param ContainerInterface|string|null      $container [optional] container to create menu from.
     *                                                       Default is to use the container retrieved from {@link getContainer()}.
     * @param array<string, bool|int|string|null> $options   [optional] options for controlling rendering
     *
     * @throws InvalidArgumentException
     */
    public function renderMenu($container = null, array $options = []): string
    {
        $container = $this->containerParser->parseContainer($container);

        if (null === $container) {
            $container = $this->getContainer();
        }

        $options = $this->normalizeOptions($options);

        if ($options['onlyActiveBranch'] && !$options['renderParents']) {
            return $this->renderDeepestMenu(
                $container,
                $options
            );
        }

        return $this->renderNormalMenu(
            $container,
            $options
        );
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
     * @param ContainerInterface|string|null $container     [optional] container to create menu from.
     *                                                      Default is to use the container retrieved from {@link getContainer()}.
     * @param string|null                    $ulClass       [optional] CSS class to use for UL element.
     *                                                      Default is to use the value from {@link getUlClass()}.
     * @param string|null                    $liClass       [optional] CSS class to use for LI elements.
     *                                                      Default is to use the value from {@link getLiClass()}.
     * @param int|string|null                $indent        [optional] indentation as a string or number
     *                                                      of spaces. Default is to use the value retrieved from
     *                                                      {@link getIndent()}.
     * @param string|null                    $liActiveClass [optional] CSS class to use for UL
     *                                                      element. Default is to use the value from {@link getUlClass()}.
     *
     * @throws InvalidArgumentException
     */
    public function renderSubMenu(
        $container = null,
        ?string $ulClass = null,
        ?string $liClass = null,
        $indent = null,
        ?string $liActiveClass = null
    ): string {
        return $this->renderMenu(
            $container,
            [
                'indent' => $indent,
                'ulClass' => $ulClass,
                'liClass' => $liClass,
                'minDepth' => null,
                'maxDepth' => null,
                'onlyActiveBranch' => true,
                'renderParents' => false,
                'escapeLabels' => true,
                'addClassToListItem' => false,
                'liActiveClass' => $liActiveClass,
            ]
        );
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty.
     *
     * Overrides {@link AbstractHelper::htmlify()}.
     *
     * @param PageInterface $page               page to generate HTML for
     * @param bool          $escapeLabel        Whether or not to escape the label
     * @param bool          $addClassToListItem Whether or not to add the page class to the list item
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function htmlify(PageInterface $page, bool $escapeLabel = true, bool $addClassToListItem = false): string
    {
        return $this->toHtml(self::class, $page, ['escapeLabels' => $escapeLabel, 'sublink' => null], [], true);
    }

    /**
     * Renders the deepest active menu within [minDepth, maxDepth], (called from {@link renderMenu()}).
     *
     * @param ContainerInterface                  $container container to render
     * @param array<string, bool|int|string|null> $options   options for controlling rendering
     *
     * @throws Exception\InvalidArgumentException
     */
    private function renderDeepestMenu(
        ContainerInterface $container,
        array $options
    ): string {
        assert(is_string($options['ulClass']));
        assert(is_string($options['liClass']));
        assert(is_string($options['indent']));
        assert(is_int($options['minDepth']));
        assert(is_int($options['maxDepth']) || null === $options['maxDepth']);
        assert(is_bool($options['onlyActiveBranch']));
        assert(is_bool($options['escapeLabels']));
        assert(is_bool($options['addClassToListItem']));
        assert(is_string($options['liActiveClass']));
        assert(is_string($options['role']) || null === $options['role']);

        $active = $this->findActive($container, $options['minDepth'] - 1, $options['maxDepth']);

        if (!$active) {
            return '';
        }

        assert(
            $active['page'] instanceof PageInterface,
            sprintf(
                '$active[\'page\'] should be an Instance of %s, but was %s',
                PageInterface::class,
                is_object($active['page']) ? get_class($active['page']) : gettype($active['page'])
            )
        );

        assert(is_int($active['depth']));

        // special case if active page is one below minDepth
        if ($active['depth'] < $options['minDepth']) {
            if (!$active['page']->hasPages(!$this->renderInvisible)) {
                return '';
            }
        } elseif (!$active['page']->hasPages(!$this->renderInvisible)) {
            // found pages has no children; render siblings
            $active['page'] = $active['page']->getParent();
        } elseif (is_int($options['maxDepth']) && $active['depth'] + 1 > $options['maxDepth']) {
            // children are below max depth; render siblings
            $active['page'] = $active['page']->getParent();
        }

        assert(
            $active['page'] instanceof ContainerInterface,
            sprintf(
                '$active[\'page\'] should be an Instance of %s, but was %s',
                ContainerInterface::class,
                is_object($active['page']) ? get_class($active['page']) : gettype($active['page'])
            )
        );

        $subHtml = '';

        foreach ($active['page'] as $subPage) {
            assert($subPage instanceof PageInterface);

            if (!$this->accept($subPage)) {
                continue;
            }

            $isActive = $subPage->isActive(true);

            // render li tag and page
            $liClasses      = [];
            $pageAttributes = [];

            $this->setAttributes(
                $subPage,
                $options,
                0,
                false,
                $isActive,
                $liClasses,
                $pageAttributes
            );

            $subHtml .= $options['indent'] . '    <li';
            if ([] !== $liClasses) {
                $subHtml .= ' class="' . ($this->escaper)(implode(' ', $liClasses)) . '"';
            }

            if (!empty($options['liRole'])) {
                $subHtml .= ' role="' . ($this->escaper)($options['liRole']) . '"';
            }

            $subHtml .= '>' . PHP_EOL;
            $subHtml .= $options['indent'] . '        ';
            $subHtml .= $this->toHtml(
                self::class,
                $subPage,
                $options,
                $pageAttributes,
                false
            );
            $subHtml .= PHP_EOL;
            $subHtml .= $options['indent'] . '    </li>' . PHP_EOL;
        }

        if ('' === $subHtml) {
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
        $html .= $subHtml . $options['indent'] . '</ul>';

        return $html;
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()}).
     *
     * @param ContainerInterface                  $container container to render
     * @param array<string, bool|int|string|null> $options   options for controlling rendering
     *
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    private function renderNormalMenu(
        ContainerInterface $container,
        array $options
    ): string {
        $html = '';

        assert(is_string($options['ulClass']));
        assert(is_string($options['liClass']));
        assert(is_string($options['indent']));
        assert(is_int($options['minDepth']));
        assert(is_int($options['maxDepth']) || null === $options['maxDepth']);
        assert(is_bool($options['onlyActiveBranch']));
        assert(is_bool($options['escapeLabels']));
        assert(is_bool($options['addClassToListItem']));
        assert(is_string($options['liActiveClass']));
        assert(is_string($options['role']) || null === $options['role']);

        // find deepest active
        $found = $this->findActive($container, $options['minDepth'], $options['maxDepth']);

        // create iterator
        $iterator = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::SELF_FIRST
        );

        if (is_int($options['maxDepth'])) {
            $iterator->setMaxDepth($options['maxDepth']);
        }

        // iterate container
        $prevDepth = -1;
        $prevPage  = null;

        switch ($options['style']) {
            case self::STYLE_OL:
                $element = 'ol';
                break;
            case self::STYLE_UL:
            default:
                $element = 'ul';
        }

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
                if (0 === $depth) {
                    $ulClass = ' class="' . ($this->escaper)($options['ulClass']) . '"';

                    if (!empty($options['ulRole'])) {
                        $ulClass .= ' role="' . ($this->escaper)($options['ulRole']) . '"';
                    }
                } else {
                    $ulClasses = [];

                    if (self::STYLE_SUBLINK_DETAILS === $options['sublink']) {
                        $ulClasses[] = 'dropdown-details-menu';
                    } else {
                        $ulClasses[] = 'dropdown-menu';
                    }

                    if (array_key_exists('dark', $options)) {
                        $ulClasses[] = 'dropdown-menu-dark';
                    }

                    $ulClass = ' class="' . ($this->escaper)(implode(' ', $ulClasses)) . '"';

                    if (null !== $prevPage && null !== $prevPage->getId()) {
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

                    if (self::STYLE_SUBLINK_DETAILS !== $options['sublink']) {
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
                $pageAttributes
            );

            if ([] === $liClasses) {
                $liClass = '';
            } else {
                $liClass = ' class="' . ($this->escaper)(implode(' ', array_unique($liClasses))) . '"';
            }

            if (0 === $depth && !empty($options['liRole'])) {
                $liClass .= ' role="' . ($this->escaper)($options['liRole']) . '"';
            }

            $html .= $myIndent . '    <li' . $liClass . '>' . PHP_EOL;

            if ($anySubpageAccepted && self::STYLE_SUBLINK_DETAILS === $options['sublink']) {
                $html .= $myIndent . '        <details>' . PHP_EOL;
            }

            $html .= $myIndent . '        ';
            $html .= $this->toHtml(
                self::class,
                $page,
                $options,
                $pageAttributes,
                $anySubpageAccepted
            );
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

                if (1 >= $i || self::STYLE_SUBLINK_DETAILS !== $options['sublink']) {
                    continue;
                }

                $html .= $myIndent . '</details>' . PHP_EOL;
            }

            $html = rtrim($html, PHP_EOL);
        }

        return $html;
    }

    /**
     * Normalizes given render options.
     *
     * @param array<string, bool|int|string|null> $options [optional] options to normalize
     *
     * @return array<string, bool|int|string|null>
     *
     * @throws InvalidArgumentException
     */
    private function normalizeOptions(array $options = []): array
    {
        $options = $this->parentNormalizeOptions($options);

        if (array_key_exists('in-navbar', $options)) {
            $ulClasses = ['navbar-nav'];
        } else {
            $ulClasses = ['nav'];
        }

        $ulClasses[] = $options['ulClass'];
        $itemClasses = [];

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
            $ulClasses[]   = 'flex-column';
            $ulClasses[]   = $this->getSizeClass($options['vertical'], 'flex-%s-row');
            $itemClasses[] = $this->getSizeClass($options['vertical'], 'flex-%s-fill');
            $itemClasses[] = $this->getSizeClass($options['vertical'], 'text-%s-center');

            if (!array_key_exists('direction', $options)) {
                $options['direction'] = self::DROP_ORIENTATION_END;
            }
        } elseif (!array_key_exists('direction', $options)) {
            $options['direction'] = self::DROP_ORIENTATION_DOWN;
        }

        $options['ulClass'] = implode(' ', $ulClasses);
        $options['class']   = implode(' ', $itemClasses);
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
     * @param PageInterface                       $page    current page to check
     * @param array<string, bool|int|string|null> $options options for controlling rendering
     * @param int                                 $level   current level of rendering
     */
    private function hasAcceptedSubpages(PageInterface $page, array $options, int $level): bool
    {
        $hasVisiblePages    = $page->hasPages(true);
        $anySubpageAccepted = false;

        assert(is_int($options['maxDepth']) || null === $options['maxDepth']);

        if ($hasVisiblePages && (null === $options['maxDepth'] || $level + 1 <= $options['maxDepth'])) {
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
     *
     * @return array<bool>
     */
    private function isPageAccepted(PageInterface $page, array $options, int $level, array $found): array
    {
        if ($level < $options['minDepth'] || !$this->accept($page)) {
            // page is below minDepth or not accepted by acl/visibility
            return [false, false];
        }

        $isActive = $page->isActive(true);
        $accept   = true;

        assert(is_int($options['maxDepth']) || null === $options['maxDepth']);

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
     */
    private function setAttributes(
        PageInterface $page,
        array $options,
        int $level,
        bool $anySubpageAccepted,
        bool $isActive,
        array &$liClasses,
        array &$pageAttributes
    ): void {
        $pageClasses = [];

        if (0 === $level) {
            $liClasses[]   = 'nav-item';
            $pageClasses[] = 'nav-link';

            if (!empty($options['role']) && !$anySubpageAccepted) {
                $pageAttributes['role'] = $options['role'];
            }
        } else {
            $pageClasses[] = 'dropdown-item';
        }

        if ($anySubpageAccepted) {
            switch ($options['direction']) {
                case self::DROP_ORIENTATION_UP:
                    $liClasses[] = 'dropup';
                    break;
                case self::DROP_ORIENTATION_END:
                    $liClasses[] = 'dropend';
                    break;
                case self::DROP_ORIENTATION_START:
                    $liClasses[] = 'dropstart';
                    break;
                case self::DROP_ORIENTATION_DOWN:
                default:
                    $liClasses[] = 'dropdown';
            }

            if (self::STYLE_SUBLINK_BUTTON === $options['sublink'] || self::STYLE_SUBLINK_DETAILS === $options['sublink']) {
                $pageClasses[] = 'btn';
            }

            if (self::STYLE_SUBLINK_DETAILS !== $options['sublink']) {
                $pageClasses[]                    = 'dropdown-toggle';
                $pageAttributes['data-bs-toggle'] = 'dropdown';
            }

            $pageAttributes['aria-expanded'] = 'false';
            $pageAttributes['role']          = 'button';
        }

        // Is page active?
        if ($isActive) {
            $liClasses[] = $options['liActiveClass'];

            if (0 === $level) {
                $pageAttributes['aria-current'] = 'page';
            }
        }

        if ($options['liClass']) {
            $liClasses[] = $options['liClass'];
        }

        if ($page->getLiClass()) {
            $liClasses[] = $page->getLiClass();
        }

        // Add CSS class from page to <li>
        if ($options['addClassToListItem'] && $page->getClass()) {
            $liClasses[] = $page->getClass();
        } elseif ($page->getClass()) {
            $pageClasses[] = $page->getClass();
        }

        $pageAttributes['class'] = implode(' ', array_unique($pageClasses));
    }

    /**
     * Returns an HTML string for the given page
     *
     * @param string                              $prefix     prefix to normalize the id attribute
     * @param PageInterface                       $page       page to generate HTML for
     * @param array<string, bool|int|string|null> $options    options for controlling rendering
     * @param array<string, string>               $attributes
     *
     * @return string HTML string
     */
    private function toHtml(
        string $prefix,
        PageInterface $page,
        array $options,
        array $attributes,
        bool $anySubpageAccepted
    ): string {
        $label = (string) $page->getLabel();
        $title = $page->getTitle();

        if (null !== $this->translator) {
            $label = ($this->translator)($label, $page->getTextDomain());

            if (null !== $title) {
                $title = ($this->translator)($title, $page->getTextDomain());
            }
        }

        // get attribs for element

        $attributes['id']    = $page->getId();
        $attributes['title'] = $title;

        if ($anySubpageAccepted && self::STYLE_SUBLINK_DETAILS === $options['sublink']) {
            $element = 'summary';
        } elseif ($anySubpageAccepted && self::STYLE_SUBLINK_BUTTON === $options['sublink']) {
            $element            = 'button';
            $attributes['type'] = 'button';
        } elseif (($anySubpageAccepted && self::STYLE_SUBLINK_SPAN === $options['sublink']) || !$page->getHref()) {
            $element = 'span';
        } else {
            $element              = 'a';
            $attributes['href']   = $page->getHref();
            $attributes['target'] = $page->getTarget();
        }

        // remove sitemap specific attributes
        $attributes = array_diff_key(
            array_merge($attributes, $page->getCustomProperties()),
            array_flip(['lastmod', 'changefreq', 'priority'])
        );

        if ($options['escapeLabels']) {
            $label = ($this->escapeHtml)($label);
        }

        return $this->htmlElement->toHtml($element, $attributes, $label, $prefix);
    }
}
