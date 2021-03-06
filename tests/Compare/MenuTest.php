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

namespace MezzioTest\Navigation\LaminasView\View\Helper\BootstrapNavigation\Compare;

use Laminas\Config\Exception\RuntimeException;
use Laminas\Log\Logger;
use Laminas\View\Exception\ExceptionInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager as ViewHelperPluginManager;
use Mezzio\GenericAuthorization\AuthorizationInterface;
use Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Menu;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\ViewHelperInterface;
use Mezzio\Navigation\Page\PageFactory;
use Mezzio\Navigation\Page\PageInterface;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;
use function get_class;
use function is_string;
use function rtrim;
use function sprintf;
use function str_replace;
use function trim;

use const PHP_EOL;

/**
 * Tests Mezzio\Navigation\LaminasView\View\Helper\Navigation\Menu.
 *
 * @group Laminas_View
 * @group Laminas_View_Helper
 * @group Compare
 */
final class MenuTest extends AbstractTest
{
    /**
     * Class name for view helper to test.
     */
    protected string $helperName = Menu::class;

    /**
     * View helper
     *
     * @var Menu
     */
    protected ViewHelperInterface $helper;

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws \Laminas\Config\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $plugin   = $this->serviceManager->get(ViewHelperPluginManager::class);
        $renderer = $this->serviceManager->get(PartialRendererInterface::class);

        // create helper
        $this->helper = new Menu(
            $this->serviceManager,
            $this->serviceManager->get(Logger::class),
            $this->serviceManager->get(HtmlifyInterface::class),
            $this->serviceManager->get(ContainerParserInterface::class),
            $plugin->get(EscapeHtmlAttr::class),
            $renderer,
            $plugin->get(EscapeHtml::class),
            $this->serviceManager->get(HtmlElementInterface::class),
            null
        );

        // set nav1 in helper as default
        $this->helper->setContainer($this->nav1);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testCanRenderMenuFromServiceAlias(): void
    {
        $returned = $this->helper->renderMenu('Navigation');
        $expected = $this->getExpected('menu/default1.html');

        self::assertSame($expected, $returned);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testCanRenderPartialFromServiceAlias(): void
    {
        $this->helper->setPartial('test::menu');

        $returned = $this->helper->renderPartial('Navigation');
        $expected = $this->getExpected('menu/partial.html');

        self::assertSame($expected, $returned);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testHelperEntryPointWithoutAnyParams(): void
    {
        $returned = $this->helper->__invoke();
        self::assertSame($this->helper, $returned);
        self::assertSame($this->nav1, $returned->getContainer());
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testHelperEntryPointWithContainerParam(): void
    {
        $returned = $this->helper->__invoke($this->nav2);
        self::assertSame($this->helper, $returned);
        self::assertSame($this->nav2, $returned->getContainer());
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testNullingOutContainerInHelper(): void
    {
        $this->helper->setContainer();
        self::assertCount(0, $this->helper->getContainer());
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSetIndentAndOverrideInRenderMenu(): void
    {
        $this->helper->setIndent(8);

        $expected = [
            'indent4' => $this->getExpected('menu/indent4.html'),
            'indent8' => $this->getExpected('menu/indent8.html'),
        ];

        $actual = [
            'indent4' => rtrim($this->helper->renderMenu(null, ['indent' => 4]), PHP_EOL),
            'indent8' => rtrim($this->helper->renderMenu(), PHP_EOL),
        ];

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testRenderSuppliedContainerWithoutInterfering(): void
    {
        $rendered1 = $this->getExpected('menu/default1.html');
        $rendered2 = $this->getExpected('menu/default2.html');
        $expected  = [
            'registered' => $rendered1,
            'supplied' => $rendered2,
            'registered_again' => $rendered1,
        ];

        $actual = [
            'registered' => $this->helper->render(),
            'supplied' => $this->helper->render($this->nav2),
            'registered_again' => $this->helper->render(),
        ];

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testUseAclRoleAsString(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole('member');

        $expected = $this->getExpected('menu/acl_string.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testFilterOutPagesBasedOnAcl(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole($acl['role']);

        $expected = $this->getExpected('menu/acl.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testDisablingAcl(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole($acl['role']);
        $this->helper->setUseAuthorization(false);

        $expected = $this->getExpected('menu/default1.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testDisablingAclWhenUsingUl(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole($acl['role']);
        $this->helper->setUseAuthorization(false);

        $expected = $this->getExpected('menu/default1.html');
        $actual   = $this->helper->renderMenu(null, ['style' => Menu::STYLE_UL]);

        self::assertSame($expected, trim($actual));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testDisablingAclWhenUsingOl(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole($acl['role']);
        $this->helper->setUseAuthorization(false);

        $expected = $this->getExpected('menu/default1_ol.html');
        $actual   = $this->helper->renderMenu(null, ['style' => Menu::STYLE_OL]);

        self::assertSame($expected, trim($actual));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testDisablingAclWhenUsingButton(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole($acl['role']);
        $this->helper->setUseAuthorization(false);

        $expected = $this->getExpected('menu/default1_button.html');
        $actual   = $this->helper->renderMenu(null, ['style' => Menu::STYLE_UL, 'sublink' => Menu::STYLE_SUBLINK_BUTTON]);

        self::assertSame($expected, trim($actual));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testDisablingAclWhenUsingDetails(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole($acl['role']);
        $this->helper->setUseAuthorization(false);

        $expected = $this->getExpected('menu/default1_details.html');
        $actual   = $this->helper->renderMenu(null, ['style' => Menu::STYLE_UL, 'sublink' => Menu::STYLE_SUBLINK_DETAILS]);

        self::assertSame($expected, trim($actual));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testSetUlCssClass(): void
    {
        $this->helper->setUlClass('My_Nav');

        $expected = $this->getExpected('menu/css.html');
        $actual   = $this->helper->render($this->nav2);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testSetLiActiveCssClass(): void
    {
        $this->helper->setLiActiveClass('activated');

        $expected = $this->getExpected('menu/css2.html');
        $actual   = $this->helper->render($this->nav2);

        self::assertSame(trim($expected), $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionEscapeLabelsAsTrue(): void
    {
        $options = ['escapeLabels' => true];

        $nav2 = clone $this->nav2;
        $page = (new PageFactory())->factory(
            [
                'label' => 'Badges <span class="badge">1</span>',
                'uri' => 'badges',
            ]
        );

        $nav2->addPage($page);

        $expected = $this->getExpected('menu/escapelabels_as_true.html');
        $actual   = $this->helper->renderMenu($nav2, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionEscapeLabelsAsFalse(): void
    {
        $options = ['escapeLabels' => false];

        $nav2 = clone $this->nav2;
        $page = (new PageFactory())->factory(
            [
                'label' => 'Badges <span class="badge">1</span>',
                'uri' => 'badges',
            ]
        );

        $nav2->addPage($page);

        $expected = $this->getExpected('menu/escapelabels_as_false.html');
        $actual   = $this->helper->renderMenu($nav2, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testRenderingPartial(): void
    {
        $this->helper->setPartial('test::menu');

        $expected = $this->getExpected('menu/partial.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \Laminas\View\Exception\RuntimeException
     * @throws \InvalidArgumentException
     */
    public function testRenderingPartialBySpecifyingAnArrayAsPartial(): void
    {
        $this->helper->setPartial(['test::menu', 'application']);

        $expected = $this->getExpected('menu/partial.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \Laminas\View\Exception\RuntimeException
     */
    public function testRenderingPartialWithParams(): void
    {
        $this->helper->setPartial(['test::menu-with-partials', 'application']);

        $expected = $this->getExpected('menu/partial_with_params.html');
        $actual   = $this->helper->renderPartialWithParams(['variable' => 'test value']);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws AssertionFailedError
     * @throws \InvalidArgumentException
     */
    public function testRenderingPartialShouldFailOnInvalidPartialArray(): void
    {
        $this->helper->setPartial(['menu.phtml']);

        try {
            $this->helper->render();
            self::fail('invalid $partial should throw Laminas\View\Exception\InvalidArgumentException');
        } catch (ExceptionInterface $e) {
        }
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSetMaxDepth(): void
    {
        $this->helper->setMaxDepth(1);

        $expected = $this->getExpected('menu/maxdepth.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSetMinDepth(): void
    {
        $this->helper->setMinDepth(1);

        $expected = $this->getExpected('menu/mindepth.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSetBothDepts(): void
    {
        $this->helper->setMinDepth(1)->setMaxDepth(2);

        $expected = $this->getExpected('menu/bothdepts.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSetOnlyActiveBranch(): void
    {
        $this->helper->setOnlyActiveBranch(true);

        $expected = $this->getExpected('menu/onlyactivebranch.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSetRenderParents(): void
    {
        $this->helper->setOnlyActiveBranch(true)->setRenderParents(false);

        $expected = $this->getExpected('menu/onlyactivebranch_noparents.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSetOnlyActiveBranchAndMinDepth(): void
    {
        $this->helper->setOnlyActiveBranch()->setMinDepth(1);

        $expected = $this->getExpected('menu/onlyactivebranch_mindepth.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOnlyActiveBranchAndMaxDepth(): void
    {
        $this->helper->setOnlyActiveBranch()->setMaxDepth(2);

        $expected = $this->getExpected('menu/onlyactivebranch_maxdepth.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOnlyActiveBranchAndBothDepthsSpecified(): void
    {
        $this->helper->setOnlyActiveBranch()->setMinDepth(1)->setMaxDepth(2);

        $expected = $this->getExpected('menu/onlyactivebranch_bothdepts.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOnlyActiveBranchNoParentsAndBothDepthsSpecified(): void
    {
        $this->helper->setOnlyActiveBranch()
            ->setMinDepth(1)
            ->setMaxDepth(2)
            ->setRenderParents(false);

        $expected = $this->getExpected('menu/onlyactivebranch_np_bd.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOnlyActiveBranchNoParentsActiveOneBelowMinDepth(): void
    {
        $this->setActive('Page 2');

        $this->helper->setOnlyActiveBranch()
            ->setMinDepth(1)
            ->setMaxDepth(1)
            ->setRenderParents(false);

        $expected = $this->getExpected('menu/onlyactivebranch_np_bd2.html');
        $actual   = $this->helper->renderMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testRenderSubMenuShouldOverrideOptions(): void
    {
        $this->helper->setOnlyActiveBranch(false)
            ->setMinDepth(1)
            ->setMaxDepth(2)
            ->setRenderParents(true);

        $expected = $this->getExpected('menu/onlyactivebranch_noparents.html');
        $actual   = $this->helper->renderSubMenu();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionMaxDepth(): void
    {
        $options = ['maxDepth' => 1];

        $expected = $this->getExpected('menu/maxdepth.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionMinDepth(): void
    {
        $options = ['minDepth' => 1];

        $expected = $this->getExpected('menu/mindepth.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionBothDepts(): void
    {
        $options = [
            'minDepth' => 1,
            'maxDepth' => 2,
        ];

        $expected = $this->getExpected('menu/bothdepts.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionOnlyActiveBranch(): void
    {
        $options = ['onlyActiveBranch' => true];

        $expected = $this->getExpected('menu/onlyactivebranch.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionOnlyActiveBranchNoParents(): void
    {
        $options = [
            'onlyActiveBranch' => true,
            'renderParents' => false,
        ];

        $expected = $this->getExpected('menu/onlyactivebranch_noparents.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionOnlyActiveBranchAndMinDepth(): void
    {
        $options = [
            'minDepth' => 1,
            'onlyActiveBranch' => true,
        ];

        $expected = $this->getExpected('menu/onlyactivebranch_mindepth.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionOnlyActiveBranchAndMaxDepth(): void
    {
        $options = [
            'maxDepth' => 2,
            'onlyActiveBranch' => true,
        ];

        $expected = $this->getExpected('menu/onlyactivebranch_maxdepth.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionOnlyActiveBranchAndBothDepthsSpecified(): void
    {
        $options = [
            'minDepth' => 1,
            'maxDepth' => 2,
            'onlyActiveBranch' => true,
        ];

        $expected = $this->getExpected('menu/onlyactivebranch_bothdepts.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testOptionOnlyActiveBranchNoParentsAndBothDepthsSpecified(): void
    {
        $options = [
            'minDepth' => 2,
            'maxDepth' => 2,
            'onlyActiveBranch' => true,
            'renderParents' => false,
        ];

        $expected = $this->getExpected('menu/onlyactivebranch_np_bd.html');
        $actual   = $this->helper->renderMenu(null, $options);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testRenderingWithoutPageClassToLi(): void
    {
        $nav2 = clone $this->nav2;
        $page = (new PageFactory())->factory(
            [
                'label' => 'Class test',
                'uri' => 'test',
                'class' => 'foobar',
            ]
        );

        $nav2->addPage($page);

        $expected = $this->getExpected('menu/addclasstolistitem_as_false.html');
        $actual   = $this->helper->renderMenu($nav2);

        self::assertSame(trim($expected), trim($actual));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testRenderingWithPageClassToLi(): void
    {
        $options = ['addClassToListItem' => true];

        $nav2 = clone $this->nav2;
        $page = (new PageFactory())->factory(
            [
                'label' => 'Class test',
                'uri' => 'test',
                'class' => 'foobar',
            ]
        );
        $nav2->addPage($page);

        $expected = $this->getExpected('menu/addclasstolistitem_as_true.html');
        $actual   = $this->helper->renderMenu($nav2, $options);

        self::assertSame(trim($expected), trim($actual));
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testRenderDeepestMenuWithPageClassToLi(): void
    {
        $options = [
            'addClassToListItem' => true,
            'onlyActiveBranch' => true,
            'renderParents' => false,
        ];

        $nav2 = clone $this->nav2;

        $page = $nav2->findOneByLabel('Site 2');
        assert(
            $page instanceof PageInterface,
            sprintf(
                '$page should be an Instance of %s, but was %s',
                PageInterface::class,
                get_class($page)
            )
        );

        self::assertInstanceOf(PageInterface::class, $page);
        $page->setClass('foobar');

        $expected = $this->getExpected('menu/onlyactivebranch_addclasstolistitem.html');
        $actual   = $this->helper->renderMenu($nav2, $options);

        self::assertSame(trim($expected), trim($actual));
    }

    /**
     * Returns the contens of the expected $file, normalizes newlines.
     */
    protected function getExpected(string $file): string
    {
        return str_replace(["\r\n", "\n", "\r", '##lb##'], ['##lb##', '##lb##', '##lb##', PHP_EOL], parent::getExpected($file));
    }

    private function setActive(string $label): void
    {
        $container = $this->helper->getContainer();

        foreach ($container->findAllByActive(true) as $page) {
            $page->setActive(false);
        }

        $p = $container->findOneByLabel($label);

        if (!$p) {
            return;
        }

        $p->setActive(true);
    }
}
