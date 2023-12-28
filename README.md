# template

[![Latest Stable Version](https://poser.pugx.org/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap/v/stable?format=flat-square)](https://packagist.org/packages/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap)
[![Latest Unstable Version](https://poser.pugx.org/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap/v/unstable?format=flat-square)](https://packagist.org/packages/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap)
[![License](https://poser.pugx.org/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap/license?format=flat-square)](https://packagist.org/packages/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap)

## Code Status

[![codecov](https://codecov.io/gh/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap/branch/master/graph/badge.svg)](https://codecov.io/gh/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap)
[![Average time to resolve an issue](https://isitmaintained.com/badge/resolution/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap.svg)](https://isitmaintained.com/project/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap "Average time to resolve an issue")
[![Percentage of issues still open](https://isitmaintained.com/badge/open/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap.svg)](https://isitmaintained.com/project/mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap "Percentage of issues still open")

## Installation

Run

```shell
composer require mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap
```

### Render the navigation

Calling the view helper for menus in your layout script:

```php
<!-- ... -->

<body>
    <?= $this->navigation('default')->menu() ?>
</body>
<!-- ... -->
```

## Using multiple navigations

Once the mezzio-navigation module is registered, you can create as many navigation
definitions as you wish, and the underlying factories will create navigation
containers automatically.

Add the container definitions to your configuration file, e.g.
`config/autoload/global.php`:

```php
<?php
return [
    // ...

    'navigation' => [

        // Navigation with name default
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
            ],
            [
                'label' => 'Page #1',
                'route' => 'page-1',
                'pages' => [
                    [
                        'label' => 'Child #1',
                        'route' => 'page-1-child',
                    ],
                ],
            ],
            [
                'label' => 'Page #2',
                'route' => 'page-2',
            ],
        ],

        // Navigation with name special
        'special' => [
            [
                'label' => 'Special',
                'route' => 'special',
            ],
            [
                'label' => 'Special Page #2',
                'route' => 'special-2',
            ],
        ],

        // Navigation with name sitemap
        'sitemap' => [
            [
                'label' => 'Sitemap',
                'route' => 'sitemap',
            ],
            [
                'label' => 'Sitemap Page #2',
                'route' => 'sitemap-2',
            ],
        ],
    ],
    // ...
];
```

> ### Container names have a prefix
>
> There is one important point to know when using mezzio-navigation as a module:
> The name of the container in your view script **must** be prefixed with
> `Mezzio\Navigation\`, followed by the name of the configuration key.
> This helps ensure that no naming collisions occur with other services.

The following example demonstrates rendering the navigation menus for the named
`default`, `special`, and `sitemap` containers.

```php
<!-- ... -->

<body>
    <?= $this->navigation('Mimmi20\Mezzio\Navigation\Default')->menu() ?>

    <?= $this->navigation('Mimmi20\Mezzio\Navigation\Special')->menu() ?>

    <?= $this->navigation('Mimmi20\Mezzio\Navigation\Sitemap')->menu() ?>
</body>
<!-- ... -->
```

## View Helpers

The navigation helpers are used for rendering navigational elements from
`Mimmi20\Mezzio\Navigation\Navigation` instances for the use with Bootstrap.

There are 2 built-in helpers:

- Breadcrumbs, used for rendering the path to the currently
  active page.
- Menu, used for rendering menus.

All built-in helpers implements the interface `Mimmi20\Mezzio\Navigation\LaminasView\View\Helper\Navigation\ViewHelperInterface`, which
adds integration with
[laminas-acl](https://docs.laminas.dev/laminas-permissions-acl/) or [laminas-rbac](https://docs.laminas.dev/laminas-permissions-rbac/) and
[laminas-i18n](https://docs.laminas.dev/laminas-i18n/).

If a container is not explicitly set, the helper will create an empty
`Mezzio\Navigation\Navigation` container when calling `$helper->getContainer()`.

### Proxying calls to the navigation container

Navigation view helpers use the magic method `__call()` to proxy method calls to
the navigation container that is registered in the view helper.

```php
$this->navigation()->addPage([
    'type' => 'uri',
    'label' => 'New page',
]);
```

The call above will add a page to the container in the `Navigation` helper.

## Translation of labels and titles

The navigation helpers support translation of page labels and titles.

The proxy helper will inject its own translator to the helper
it proxies to if the proxied helper doesn't already have a translator.

## Integration with Permissions

All navigational view helpers support Permissions. If an
Permission is used in the helper, the role in the helper must be allowed by the Permission to
access a page's `resource` and/or have the page's `privilege` for the page to be
included when rendering.

If a page is not accepted by Permission, any descendant page will also be excluded from
rendering.

The proxy helper will inject its own Permission and role to the helper
it proxies to if the proxied helper doesn't already have any.

## Breadcrumbs

Breadcrumbs are used for indicating where in a sitemap a user is currently browsing, and are
typically rendered like the following:

```text
You are here: Home > Products > FantasticProduct 1.0
```

The `breadcrumbs()` helper follows the [Breadcrumbs Pattern](http://developer.yahoo.com/ypatterns/pattern.php?pattern=breadcrumbs)
as outlined in the Yahoo! Design Pattern Library, and allows simple
customization (minimum/maximum depth, indentation, separator, and whether the
last element should be linked), or rendering using a partial view script.

The Breadcrumbs helper finds the deepest active page in a navigation container,
and renders an upwards path to the root. For Route pages, the "activeness" of a
page is determined by inspecting the request object, as stated in the section on
pages.

The helper sets the `minDepth` property to 1 by default, meaning breadcrumbs
will not be rendered if the deepest active page is a root page. If `maxDepth` is
specified, the helper will stop rendering when at the specified depth (e.g. stop
at level 2 even if the deepest active page is on level 3).

### Basic usage

This example shows how to render breadcrumbs with default settings.

In a view script or layout:

```php
<?= $this->navigation()->breadcrumbs(); ?>
```

The call above takes advantage of the magic `__toString()` method, and is
equivalent to:

```php
<?= $this->navigation()->breadcrumbs()->render(); ?>
```

Output:

```html
<a href="/products">Products</a> &gt; <a href="/products/server">Foo Server</a> &gt; FAQ
```

### Specifying indentation

This example shows how to render breadcrumbs with initial indentation.

Rendering with 8 spaces indentation:

```php
<?= $this->navigation()->breadcrumbs()->setIndent(8) ?>
```

Output:

```html
        <a href="/products">Products</a> &gt; <a href="/products/server">Foo Server</a> &gt; FAQ
```

### Customize output

This example shows how to customize breadcrumbs output by specifying multiple options.

In a view script or layout:

```php
<?= $this->navigation()->breadcrumbs()
    ->setLinkLast(true)                   // link last page
    ->setMaxDepth(1)                      // stop at level 1
    ->setSeparator(' ▶' . PHP_EOL);       // cool separator with newline
?>
```

Output:

```html
<a href="/products">Products</a> ▶
<a href="/products/server">Foo Server</a>
```

Setting minimum depth required to render breadcrumbs:

```php
<?= $this->navigation()->breadcrumbs()->setMinDepth(10) ?>
```

Output: Nothing, because the deepest active page is not at level 10 or deeper.

## Menu

The `menu()` helper is used for rendering menus from navigation containers. By
default, the menu will be rendered using HTML `UL` and `LI` tags, but the helper
also allows using a partial view script.

### Basic usage

This example shows how to render a menu from a container registered/found in the
view helper. Notice how pages are filtered out based on visibility and ACL.

In a view script or layout:

```php
<?= $this->navigation()->menu()->render() ?>
```

Or:

```php
<?= $this->navigation()->menu() ?>
```

Output:

```html
<ul class="navigation">
    <li>
        <a title="Go Home" href="/">Home</a>
    </li>
    <li class="active">
        <a href="/products">Products</a>
        <ul>
            <li class="active">
                <a href="/products/server">Foo Server</a>
                <ul>
                    <li class="active">
                        <a href="/products/server/faq">FAQ</a>
                    </li>
                    <li>
                        <a href="/products/server/editions">Editions</a>
                    </li>
                    <li>
                        <a href="/products/server/requirements">System Requirements</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/products/studio">Foo Studio</a>
                <ul>
                    <li>
                        <a href="/products/studio/customers">Customer Stories</a>
                    </li>
                    <li>
                        <a href="/products/studio/support">Support</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        <a title="About us" href="/company/about">Company</a>
        <ul>
            <li>
                <a href="/company/about/investors">Investor Relations</a>
            </li>
            <li>
                <a class="rss" href="/company/news">News</a>
                <ul>
                    <li>
                        <a href="/company/news/press">Press Releases</a>
                    </li>
                    <li>
                        <a href="/archive">Archive</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        <a href="/community">Community</a>
        <ul>
            <li>
                <a href="/community/account">My Account</a>
            </li>
            <li>
                <a class="external" href="http://forums.example.com/">Forums</a>
            </li>
        </ul>
    </li>
</ul>
```

### Calling renderMenu() directly

This example shows how to render a menu that is not registered in the view
helper by calling `renderMenu()` directly and specifying options.

```php
<?php
// render only the 'Community' menu
$community = $this->navigation()->findOneByLabel('Community');
$options = [
    'indent'  => 16,
    'ulClass' => 'community'
];
echo $this->navigation()
          ->menu()
          ->renderMenu($community, $options);
?>
```

Output:

```html
<ul class="community">
    <li>
        <a href="/community/account">My Account</a>
    </li>
    <li>
        <a class="external" href="http://forums.example.com/">Forums</a>
    </li>
</ul>
```

### Rendering the deepest active menu

This example shows how `renderSubMenu()` will render the deepest sub menu of
the active branch.

Calling `renderSubMenu($container, $ulClass, $indent)` is equivalent to calling
`renderMenu($container, $options)` with the following options:

```php
[
    'ulClass'          => $ulClass,
    'indent'           => $indent,
    'minDepth'         => null,
    'maxDepth'         => null,
    'onlyActiveBranch' => true,
    'renderParents'    => false,
]
```

Usage of `renderSubMenu` method:

```php
<?= $this->navigation()
    ->menu()
    ->renderSubMenu(null, 'sidebar', 4) ?>
```

The output will be the same if 'FAQ' or 'Foo Server' is active:

```html
<ul class="sidebar">
    <li class="active">
        <a href="/products/server/faq">FAQ</a>
    </li>
    <li>
        <a href="/products/server/editions">Editions</a>
    </li>
    <li>
        <a href="/products/server/requirements">System Requirements</a>
    </li>
</ul>
```

### Rendering with maximum depth

```php
<?= $this->navigation()
    ->menu()
    ->setMaxDepth(1) ?>
```

Output:

```html
<ul class="navigation">
    <li>
        <a title="Go Home" href="/">Home</a>
    </li>
    <li class="active">
        <a href="/products">Products</a>
        <ul>
            <li class="active">
                <a href="/products/server">Foo Server</a>
            </li>
            <li>
                <a href="/products/studio">Foo Studio</a>
            </li>
        </ul>
    </li>
    <li>
        <a title="About us" href="/company/about">Company</a>
        <ul>
            <li>
                <a href="/company/about/investors">Investor Relations</a>
            </li>
            <li>
                <a class="rss" href="/company/news">News</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="/community">Community</a>
        <ul>
            <li>
                <a href="/community/account">My Account</a>
            </li>
            <li>
                <a class="external" href="http://forums.example.com/">Forums</a>
            </li>
        </ul>
    </li>
</ul>
```

### Rendering with minimum depth

```php
<?= $this->navigation()
    ->menu()
    ->setMinDepth(1) ?>
```

Output:

```html
<ul class="navigation">
    <li class="active">
        <a href="/products/server">Foo Server</a>
        <ul>
            <li class="active">
                <a href="/products/server/faq">FAQ</a>
            </li>
            <li>
                <a href="/products/server/editions">Editions</a>
            </li>
            <li>
                <a href="/products/server/requirements">System Requirements</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="/products/studio">Foo Studio</a>
        <ul>
            <li>
                <a href="/products/studio/customers">Customer Stories</a>
            </li>
            <li>
                <a href="/products/studio/support">Support</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="/company/about/investors">Investor Relations</a>
    </li>
    <li>
        <a class="rss" href="/company/news">News</a>
        <ul>
            <li>
                <a href="/company/news/press">Press Releases</a>
            </li>
            <li>
                <a href="/archive">Archive</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="/community/account">My Account</a>
    </li>
    <li>
        <a class="external" href="http://forums.example.com/">Forums</a>
    </li>
</ul>
```

### Rendering only the active branch

```php
<?= $this->navigation()
    ->menu()
    ->setOnlyActiveBranch(true) ?>
```

Output:

```html
<ul class="navigation">
    <li class="active">
        <a href="/products">Products</a>
        <ul>
            <li class="active">
                <a href="/products/server">Foo Server</a>
                <ul>
                    <li class="active">
                        <a href="/products/server/faq">FAQ</a>
                    </li>
                    <li>
                        <a href="/products/server/editions">Editions</a>
                    </li>
                    <li>
                        <a href="/products/server/requirements">System Requirements</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
</ul>
```

### Rendering only the active branch with minimum depth

```php
<?= $this->navigation()
    ->menu()
    ->setOnlyActiveBranch(true)
    ->setMinDepth(1) ?>
```

Output:

```html
<ul class="navigation">
    <li class="active">
        <a href="/products/server">Foo Server</a>
        <ul>
            <li class="active">
                <a href="/products/server/faq">FAQ</a>
            </li>
            <li>
                <a href="/products/server/editions">Editions</a>
            </li>
            <li>
                <a href="/products/server/requirements">System Requirements</a>
            </li>
        </ul>
    </li>
</ul>
```

### Rendering only the active branch with maximum depth

```php
<?= $this->navigation()
    ->menu()
    ->setOnlyActiveBranch(true)
    ->setMaxDepth(1) ?>
```

Output:

```html
<ul class="navigation">
    <li class="active">
        <a href="/products">Products</a>
        <ul>
            <li class="active">
                <a href="/products/server">Foo Server</a>
            </li>
            <li>
                <a href="/products/studio">Foo Studio</a>
            </li>
        </ul>
    </li>
</ul>
```

### Rendering only the active branch with maximum depth and no parents

```php
<?= $this->navigation()
    ->menu()
    ->setOnlyActiveBranch(true)
    ->setRenderParents(false)
    ->setMaxDepth(1) ?>
```

Output:

```html
<ul class="navigation">
    <li class="active">
        <a href="/products/server">Foo Server</a>
    </li>
    <li>
        <a href="/products/studio">Foo Studio</a>
    </li>
</ul>
```

### Rendering a custom menu using a partial view script

This example shows how to render a custom menu using a partial view script. By
calling `setPartial()`, you can specify a partial view script that will be used
when calling `render()`; when a partial is specified, that method will proxy to
the `renderPartial()` method.

The `renderPartial()`  method will assign the container to the view with the key
`container`.

In a layout:

```php
$this->navigation()->menu()->setPartial('my-module/partials/menu');
echo $this->navigation()->menu()->render();
```

In `module/MyModule/view/my-module/partials/menu.phtml`:

```php
foreach ($this->container as $page) {
    echo $this->navigation()->menu()->htmlify($page) . PHP_EOL;
}
```

Output:

```html
<a title="Go Home" href="/">Home</a>
<a href="/products">Products</a>
<a title="About us" href="/company/about">Company</a>
<a href="/community">Community</a>
```

#### Using additional parameters in partial view scripts

Starting with version 2.6.0, you can assign custom variables to a
partial script.

In a layout:

```php
// Set partial
$this->navigation()->menu()->setPartial('my-module/partials/menu');

// Output menu
echo $this->navigation()->menu()->renderPartialWithParams(
    [
        'headline' => 'Links',
    ]
);
```

In `module/MyModule/view/my-module/partials/menu.phtml`:

```php
<h1><?= $headline ?></h1>

<?php
foreach ($this->container as $page) {
    echo $this->navigation()->menu()->htmlify($page) . PHP_EOL;
}
?>
```

Output:

```html
<h1>Links</h1>
<a title="Go Home" href="/">Home</a>
<a href="/products">Products</a>
<a title="About us" href="/company/about">Company</a>
<a href="/community">Community</a>
```

#### Using menu options in partial view scripts

In a layout:

```php
// Set options
$this->navigation()->menu()
    ->setUlClass('my-nav')
    ->setPartial('my-module/partials/menu');

// Output menu
echo $this->navigation()->menu()->render();
```

In `module/MyModule/view/my-module/partials/menu.phtml`:

```php
<div class"<?= $this->navigation()->menu()->getUlClass() ?>">
    <?php
    foreach ($this->container as $page) {
        echo $this->navigation()->menu()->htmlify($page) . PHP_EOL;
    }
    ?>
</div>
```

Output:

```html
<div class="my-nav">
    <a title="Go Home" href="/">Home</a>
    <a href="/products">Products</a>
    <a title="About us" href="/company/about">Company</a>
    <a href="/community">Community</a>
</div>
```

#### Using Permissions with partial view scripts

If you want to use a Permission within your partial view script, then you will have to
check the access to a page manually.

In `module/MyModule/view/my-module/partials/menu.phtml`:

```php
foreach ($this->container as $page) {
    if ($this->navigation()->accept($page)) {
        echo $this->navigation()->menu()->htmlify($page) . PHP_EOL;
    }
}
```

> ### UTF-8 encoding used by default
>
> By default, laminas-view uses UTF-8 as its default encoding.  If you want to use
> another encoding with `Sitemap`, you will have do three things:
>
> 1. Create a custom renderer and implement a `getEncoding()` method.
> 2. Create a custom rendering strategy that will return an instance of your custom renderer.
> 3. Attach the custom strategy in the `ViewEvent`.
>
> See the [example from the HeadStyle documentation](https://github.com/laminas/laminas-view)
> to see how you can achieve this.

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).
