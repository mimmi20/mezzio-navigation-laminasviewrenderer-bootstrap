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

use Laminas\View\Exception;
use Laminas\View\Helper\AbstractHtmlElement;
use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\Page\PageInterface;
use RecursiveIteratorIterator;

/**
 * Helper for rendering menus from navigation containers.
 */
final class Menu extends AbstractHtmlElement implements MenuInterface
{
    use BootstrapTrait, HelperTrait, MenuTrait {
        MenuTrait::htmlify insteadof HelperTrait;
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
     * @throws \InvalidArgumentException
     * @throws Exception\RuntimeException
     *
     * @return string
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
     * @param ContainerInterface|string|null $container [optional] container to create menu from.
     *                                                  Default is to use the container retrieved from {@link getContainer()}.
     * @param array                          $options   [optional] options for controlling rendering
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function renderMenu($container = null, array $options = []): string
    {
        $container = $this->containerParser->parseContainer($container);

        if (null === $container) {
            $container = $this->getContainer();
        }

        $options = $this->normalizeOptions($options);

        $ulClasses   = ['nav', $options['ulClass']];
        $itemClasses = [];

        foreach (
            [
                'tabs' => 'nav-tabs',
                'pills' => 'nav-pills',
                'fill' => 'nav-fill',
                'justified' => 'nav-justified',
                'centered' => 'justify-content-center',
                'right_aligned' => 'justify-content-end',
                'vertical' => 'flex-column',
            ] as $optionname => $optionvalue
        ) {
            if (empty($options[$optionname])) {
                continue;
            }

            $ulClasses[] = $optionvalue;
        }

        if (isset($options['vertical']) && is_string($options['vertical'])) {
            $ulClasses[]   = 'flex-column';
            $ulClasses[]   = $this->getSizeClass($options['vertical'], 'flex-%s-row');
            $itemClasses[] = $this->getSizeClass($options['vertical'], 'flex-%s-fill');
            $itemClasses[] = $this->getSizeClass($options['vertical'], 'text-%s-center');
        }

        $ulClass = implode(' ', $ulClasses);
        $ulRole  = null;
        $liRole  = null;
        $role    = null;

        if (!empty($options['tabs']) || !empty($options['pills'])) {
            $ulRole = 'tablist';
            $liRole = 'presentation';
            $role   = 'tab';
        }

        $indent = $options['indent'] ?? $this->getIndent();

        if ($options['onlyActiveBranch'] && !$options['renderParents']) {
            return $this->renderDeepestMenu(
                $container,
                $ulClass,
                $options['liClass'],
                $indent,
                $options['minDepth'] ?? 0,
                $options['maxDepth'],
                $options['escapeLabels'],
                $options['addClassToListItem'],
                $options['liActiveClass'],
                $ulRole,
                $liRole,
                $role
            );
        }

        return $this->renderNormalMenu(
            $container,
            $ulClass,
            $options['liClass'],
            $indent,
            $options['minDepth'] ?? 0,
            $options['maxDepth'],
            $options['onlyActiveBranch'],
            $options['escapeLabels'],
            $options['addClassToListItem'],
            $options['liActiveClass'],
            $ulRole,
            $liRole,
            $role
        );
    }

    /**
     * Renders the deepest active menu within [$minDepth, $maxDepth], (called from {@link renderMenu()}).
     *
     * @param ContainerInterface $container          container to render
     * @param string             $ulClass            CSS class for first UL
     * @param string             $liCssClass         CSS class for all LI
     * @param string             $indent             initial indentation
     * @param int                $minDepth           minimum depth
     * @param int|null           $maxDepth           maximum depth
     * @param bool               $escapeLabels       Whether or not to escape the labels
     * @param bool               $addClassToListItem Whether or not page class applied to <li> element
     * @param string             $liActiveClass      CSS class for active LI
     * @param string|null        $ulRole             Role attribute for the UL-Element
     * @param string|null        $liRole             Role attribute for the LI-Element
     * @param string|null        $role               Role attribute for the Link-Element
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return string
     */
    private function renderDeepestMenu(
        ContainerInterface $container,
        string $ulClass,
        string $liCssClass,
        string $indent,
        int $minDepth,
        ?int $maxDepth,
        bool $escapeLabels,
        bool $addClassToListItem,
        string $liActiveClass,
        ?string $ulRole = null,
        ?string $liRole = null,
        ?string $role = null
    ): string {
        $active = $this->findActive($container, $minDepth - 1, $maxDepth);

        if (!$active) {
            return '';
        }

        // special case if active page is one below minDepth
        if ($active['depth'] < $minDepth) {
            if (!$active['page']->hasPages(!$this->renderInvisible)) {
                return '';
            }
        } elseif (!$active['page']->hasPages(!$this->renderInvisible)) {
            // found pages has no children; render siblings
            $active['page'] = $active['page']->getParent();
        } elseif (is_int($maxDepth) && $active['depth'] + 1 > $maxDepth) {
            // children are below max depth; render siblings
            $active['page'] = $active['page']->getParent();
        }

        $html = $indent . '<ul';
        if ($ulClass) {
            $html .= ' class="' . ($this->escaper)($ulClass) . '"';
        }

        if ($ulRole) {
            $html .= ' role="' . ($this->escaper)($ulRole) . '"';
        }

        $html .= '>' . PHP_EOL;

        foreach ($active['page'] as $subPage) {
            if (!$this->accept($subPage)) {
                continue;
            }

            // render li tag and page
            $liClasses = [];

            // Is page active?
            if ($subPage->isActive(true)) {
                $liClasses[] = $liActiveClass;
            }

            if ($liCssClass) {
                $liClasses[] = $liCssClass;
            }

            if ($subPage->getLiClass()) {
                $liClasses[] = $subPage->getLiClass();
            }

            // Add CSS class from page to <li>
            if ($addClassToListItem && $subPage->getClass()) {
                $liClasses[] = $subPage->getClass();
            }

            $html .= $indent . $indent . '<li';
            if ([] !== $liClasses) {
                $html .= ' class="' . ($this->escaper)(implode(' ', $liClasses)) . '"';
            }

            if ($liRole) {
                $html .= ' role="' . ($this->escaper)($liRole) . '"';
            }

            $html .= '>' . PHP_EOL;
            $html .= $indent . $indent . $indent . $this->htmlify->toHtml(self::class, $subPage, $escapeLabels, $addClassToListItem) . PHP_EOL;
            $html .= $indent . $indent . '</li>' . PHP_EOL;
        }

        $html .= $indent . '</ul>';

        return $html;
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()}).
     *
     * @param ContainerInterface $container          container to render
     * @param string             $ulClass            CSS class for first UL
     * @param string             $liCssClass         CSS class for all LI
     * @param string             $indent             initial indentation
     * @param int|null           $minDepth           minimum depth
     * @param int|null           $maxDepth           maximum depth
     * @param bool               $onlyActive         render only active branch?
     * @param bool               $escapeLabels       Whether or not to escape the labels
     * @param bool               $addClassToListItem Whether or not page class applied to <li> element
     * @param string             $liActiveClass      CSS class for active LI
     * @param string|null        $ulRole             Role attribute for the UL-Element
     * @param string|null        $liRole             Role attribute for the LI-Element
     * @param string|null        $role               Role attribute for the Link-Element
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return string
     */
    private function renderNormalMenu(
        ContainerInterface $container,
        string $ulClass,
        string $liCssClass,
        string $indent,
        ?int $minDepth,
        ?int $maxDepth,
        bool $onlyActive,
        bool $escapeLabels,
        bool $addClassToListItem,
        string $liActiveClass,
        ?string $ulRole = null,
        ?string $liRole = null,
        ?string $role = null
    ): string {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);

        // create iterator
        $iterator = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::SELF_FIRST
        );

        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            \assert($page instanceof PageInterface);

            $depth = $iterator->getDepth();

            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            }

            $isActive = $page->isActive(true);

            if ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = $this->isActiveBranch($found, $page, $maxDepth);

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = str_repeat($indent, $depth + 1);
            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && 0 === $depth) {
                    $ulClass = ' class="' . ($this->escaper)($ulClass) . '"';

                    if (null !== $ulRole) {
                        $ulClass .= ' role="' . ($this->escaper)($ulRole) . '""';
                    }
                } else {
                    $ulClass = ' class="dropdown-menu"';
                }

                $html .= $myIndent . '<ul' . $ulClass . '>' . PHP_EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; --$i) {
                    $ind = str_repeat($indent, $i + 1);
                    $html .= $ind . $indent . '</li>' . PHP_EOL;
                    $html .= $ind . '</ul>' . PHP_EOL;
                }

                // close previous li tag
                $html .= $myIndent . $indent . '</li>' . PHP_EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . $indent . '</li>' . PHP_EOL;
            }

            // render li tag and page
            $liClasses     = [];
            $pageClasses   = [];
            $pageAttrbutes = [];

            if (0 === $depth) {
                $liClasses[]   = 'nav-item';
                $pageClasses[] = 'nav-link';

                if ($role) {
                    $pageAttrbutes['role'] = $role;
                }
            } else {
                $pageClasses[] = 'dropdown-item';
            }

            // Is page active?
            if ($isActive) {
                $liClasses[] = $liActiveClass;

                if (0 === $depth) {
                    $pageAttrbutes['aria-current'] = 'page';
                }
            }

            if ($liCssClass) {
                $liClasses[] = $liCssClass;
            }

            if ($page->getLiClass()) {
                $liClasses[] = $page->getLiClass();
            }

            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            } elseif ($page->getClass()) {
                $pageClasses[] = $page->getClass();
            }

            if ($page->hasPages(true)) {
                $liClasses[]   = 'dropdown';
                $pageClasses[] = 'dropdown-toggle';

                $pageAttrbutes['data-bs-toggle'] = 'dropdown';
                $pageAttrbutes['aria-expanded']  = 'false';
                $pageAttrbutes['role']           = 'button';
            }

            if ([] === $liClasses) {
                $liClass = '';
            } else {
                $liClass = ' class="' . ($this->escaper)(implode(' ', $liClasses)) . '"';
            }

            if (0 === $depth && $liRole) {
                $liClass .= ' role="' . ($this->escaper)($liRole) . '"';
            }

            if ([] !== $pageClasses) {
                $page->setClass(implode(' ', $pageClasses));
            }

            $html .= $myIndent . $indent . '<li' . $liClass . '>' . PHP_EOL . $myIndent . '        ';
            $html .= $this->htmlify->toHtml(self::class, $page, $escapeLabels, $addClassToListItem, $pageAttrbutes);
            $html .= PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; 0 < $i; --$i) {
                $myIndent = str_repeat($indent, $i);
                $html .= $myIndent . $indent . '</li>' . PHP_EOL
                    . $myIndent . '</ul>' . PHP_EOL;
            }

            $html = rtrim($html, PHP_EOL);
        }

        return $html;
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
     * @param ContainerInterface|null $container     [optional] container to render.
     *                                               Default is to render the container registered in the helper.
     * @param string|null             $ulClass       [optional] CSS class to use for UL element.
     *                                               Default is to use the value from {@link getUlClass()}.
     * @param string|null             $liClass       [optional] CSS class to use for LI elements.
     *                                               Default is to use the value from {@link getLiClass()}.
     * @param int|string|null         $indent        [optional] indentation as a string or number
     *                                               of spaces. Default is to use the value retrieved from
     *                                               {@link getIndent()}.
     * @param string|null             $liActiveClass [optional] CSS class to use for UL
     *                                               element. Default is to use the value from {@link getUlClass()}.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function renderSubMenu(
        ?ContainerInterface $container = null,
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
}
