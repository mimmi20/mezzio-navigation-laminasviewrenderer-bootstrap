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
use Laminas\View\HelperPluginManager as ViewHelperPluginManager;
use Mezzio\GenericAuthorization\AuthorizationInterface;
use Mezzio\Navigation\LaminasView\View\Helper\BootstrapNavigation\Breadcrumbs;
use Mezzio\Navigation\LaminasView\View\Helper\Navigation\ViewHelperInterface;
use Mezzio\Navigation\Navigation;
use Mezzio\Navigation\Page\PageFactory;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\NavigationHelper\ContainerParser\ContainerParserInterface;
use Mimmi20\NavigationHelper\Htmlify\HtmlifyInterface;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;
use function is_string;
use function mb_strlen;
use function mb_substr;
use function str_replace;
use function trim;

use const PHP_EOL;

/**
 * Tests Mezzio\Navigation\LaminasView\View\Helper\Navigation\Breadcrumbs.
 *
 * @group Laminas_View
 * @group Laminas_View_Helper
 * @group Compare
 */
final class BreadcrumbsTest extends AbstractTest
{
    /**
     * Class name for view helper to test.
     */
    protected string $helperName = Breadcrumbs::class;

    /**
     * View helper
     *
     * @var Breadcrumbs
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

        $plugin       = $this->serviceManager->get(ViewHelperPluginManager::class);
        $renderer     = $this->serviceManager->get(PartialRendererInterface::class);
        $escapeHelper = $plugin->get(EscapeHtml::class);
        $translator   = null;

        // create helper
        $this->helper = new Breadcrumbs(
            $this->serviceManager,
            $this->serviceManager->get(Logger::class),
            $this->serviceManager->get(HtmlifyInterface::class),
            $this->serviceManager->get(ContainerParserInterface::class),
            $escapeHelper,
            $renderer,
            $translator
        );

        // set nav1 in helper as default
        $this->helper->setContainer($this->nav1);
        $this->helper->setSeparator('&gt;');
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testHelperEntryPointWithoutAnyParams(): void
    {
        $returned = $this->helper->__invoke();
        self::assertSame($this->helper, $returned);
        self::assertSame($this->nav1, $returned->getContainer());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testHelperEntryPointWithContainerParam(): void
    {
        $returned = $this->helper->__invoke($this->nav2);

        self::assertSame($this->helper, $returned);
        self::assertSame($this->nav2, $returned->getContainer());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testNullOutContainer(): void
    {
        $old = $this->helper->getContainer();
        $this->helper->setContainer();
        $new = $this->helper->getContainer();

        self::assertNotSame($old, $new);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testSetSeparator(): void
    {
        $this->helper->setSeparator('foo');

        $expected = $this->getExpected('bc/separator.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testSetMaxDepth(): void
    {
        $this->helper->setMaxDepth(1);

        $expected = $this->getExpected('bc/maxdepth.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testSetMinDepth(): void
    {
        $this->helper->setMinDepth(1);

        $expected = '';
        $actual   = $this->helper->render($this->nav2);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testLinkLastElement(): void
    {
        $this->helper->setLinkLast(true);

        $expected = $this->getExpected('bc/linklast.html');
        $actual   = $this->helper->render();

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testSetIndent(): void
    {
        $this->helper->setIndent(8);

        $expected = '        <n';
        $actual   = mb_substr($this->helper->render(), 0, mb_strlen($expected));

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderSuppliedContainerWithoutInterfering(): void
    {
        $this->helper->setMinDepth(0);
        self::assertSame(0, $this->helper->getMinDepth());

        $rendered1 = $this->getExpected('bc/default.html');
        $rendered2 = $this->getExpected('bc/default2.html');

        $expected = [
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
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Permissions\Acl\Exception\InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testUseAclResourceFromPages(): void
    {
        $acl = $this->getAcl();
        assert($acl['acl'] instanceof AuthorizationInterface);
        $this->helper->setAuthorization($acl['acl']);
        assert(is_string($acl['role']));
        $this->helper->setRole($acl['role']);

        $expected = $this->getExpected('bc/acl.html');
        self::assertSame($expected, $this->helper->render());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderingPartial(): void
    {
        $this->helper->setPartial('test::bc');

        $expected = $this->getExpected('bc/partial.html');
        self::assertSame($expected, $this->helper->render());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderingPartialWithSeparator(): void
    {
        $this->helper->setPartial('test::bc-separator')->setSeparator(' / ');

        $expected = trim($this->getExpected('bc/partialwithseparator.html'));
        self::assertSame($expected, $this->helper->render());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderingPartialBySpecifyingAnArrayAsPartial(): void
    {
        $this->helper->setPartial(['test::bc', 'application']);

        $expected = $this->getExpected('bc/partial.html');
        self::assertSame($expected, $this->helper->render());
    }

    /**
     * @throws Exception
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderingPartialShouldFailOnInvalidPartialArray(): void
    {
        $this->helper->setPartial(['bc.phtml']);

        try {
            $this->helper->render();

            self::fail(
                '$partial was invalid, but no Laminas\View\Exception\ExceptionInterface was thrown'
            );
        } catch (ExceptionInterface $e) {
        }
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testRenderingPartialWithParams(): void
    {
        $this->helper->setPartial('test::bc-with-partials')->setSeparator(' / ');

        $expected = $this->getExpected('bc/partial_with_params.html');
        $actual   = $this->helper->renderPartialWithParams(['variable' => 'test value']);

        self::assertSame($expected, $actual);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws \Mezzio\Navigation\Exception\InvalidArgumentException
     * @throws ExceptionInterface
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testLastBreadcrumbShouldBeEscaped(): void
    {
        $container = new Navigation();

        $page = (new PageFactory())->factory(
            [
                'label' => 'Live & Learn',
                'uri' => '#',
                'active' => true,
            ]
        );

        $container->addPage($page);

        $expected = $this->getExpected('bc/escaped.html');
        $actual   = $this->helper->setMinDepth(0)->render($container);

        self::assertSame($expected, $actual);
    }

    /**
     * Returns the contens of the expected $file, normalizes newlines.
     */
    protected function getExpected(string $file): string
    {
        return str_replace(["\r\n", "\n", "\r", '##lb##'], ['##lb##', '##lb##', '##lb##', PHP_EOL], parent::getExpected($file));
    }
}
