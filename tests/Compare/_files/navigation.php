<?php

/**
 * This file is part of the mimmi20/mezzio-navigation-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2026, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

return [
    'nav_test1' => ['zym' => ['label' => 'Zym', 'uri' => 'http://www.zym-project.com/', 'order' => '100'], 'page1' => ['label' => 'Page 1', 'uri' => 'page1', 'pages' => ['page1_1' => ['label' => 'Page 1.1', 'uri' => 'page1/page1_1']]], 'page2' => ['label' => 'Page 2', 'uri' => 'page2', 'pages' => ['page2_1' => ['label' => 'Page 2.1', 'uri' => 'page2/page2_1'], 'page2_2' => ['label' => 'Page 2.2', 'uri' => 'page2/page2_2', 'pages' => ['page2_2_1' => ['label' => 'Page 2.2.1', 'uri' => 'page2/page2_2/page2_2_1'], 'page2_2_2' => ['label' => 'Page 2.2.2', 'uri' => 'page2/page2_2/page2_2_2', 'active' => '1']]], 'page2_3' => ['label' => 'Page 2.3', 'uri' => 'page2/page2_3', 'pages' => ['page2_3_1' => ['label' => 'Page 2.3.1', 'uri' => 'page2/page2_3/page2_3_1'], 'page2_3_2' => ['label' => 'Page 2.3.2', 'uri' => 'page2/page2_3/page2_3_2', 'visible' => '0', 'pages' => ['page2_3_2_1' => ['label' => 'Page 2.3.2.1', 'uri' => 'page2/page2_3/page2_3_2/1', 'active' => '1'], 'page2_3_2_2' => ['label' => 'Page 2.3.2.2', 'uri' => 'page2/page2_3/page2_3_2/2', 'active' => '1', 'pages' => ['page_2_3_2_2_1' => ['label' => 'Ignore', 'uri' => '#', 'active' => '1']]]]], 'page2_3_3' => ['label' => 'Page 2.3.3', 'uri' => 'page2/page2_3/page2_3_3', 'resource' => 'admin_foo', 'pages' => ['page2_3_3_1' => ['label' => 'Page 2.3.3.1', 'uri' => 'page2/page2_3/page2_3_3/1', 'active' => '1'], 'page2_3_3_2' => ['label' => 'Page 2.3.3.2', 'uri' => 'page2/page2_3/page2_3_3/2', 'resource' => 'guest_foo', 'active' => '1']]]]]]], 'page3' => ['label' => 'Page 3', 'uri' => 'page3', 'pages' => ['page3_1' => ['label' => 'Page 3.1', 'uri' => 'page3/page3_1', 'resource' => 'guest_foo'], 'page3_2' => ['label' => 'Page 3.2', 'uri' => 'page3/page3_2', 'resource' => 'member_foo', 'pages' => ['page3_2_1' => ['label' => 'Page 3.2.1', 'uri' => 'page3/page3_2/page3_2_1'], 'page3_2_2' => ['label' => 'Page 3.2.2', 'uri' => 'page3/page3_2/page3_2_2', 'resource' => 'admin_foo', 'privilege' => 'read']]], 'page3_3' => ['label' => 'Page 3.3', 'uri' => 'page3/page3_3', 'resource' => 'special_foo', 'pages' => ['page3_3_1' => ['label' => 'Page 3.3.1', 'uri' => 'page3/page3_3/page3_3_1', 'visible' => '0'], 'page3_3_2' => ['label' => 'Page 3.3.2', 'uri' => 'page3/page3_3/page3_3_2', 'resource' => 'admin_foo']]]]], 'home' => ['label' => 'Home', 'uri' => 'index', 'title' => 'Go home', 'order' => '-100']],
    'nav_test2' => ['site1' => ['label' => 'Site 1', 'uri' => 'site1', 'changefreq' => 'daily', 'priority' => '0.9'], 'site2' => ['label' => 'Site 2', 'uri' => 'site2', 'active' => '1', 'lastmod' => 'earlier'], 'site3' => ['label' => 'Site 3', 'uri' => 'site3', 'changefreq' => 'often']],
    'nav_test3' => ['page1' => ['label' => 'Page 1', 'uri' => 'page1', 'pages' => ['page1_1' => ['label' => 'Page 1.1', 'uri' => 'page1/page1_1', 'textdomain' => 'LaminasTest_1']]], 'page2' => ['label' => 'Page 2', 'uri' => 'page2', 'textdomain' => 'LaminasTest_1', 'pages' => ['page2_1' => ['label' => 'Page 2.1', 'uri' => 'page2/page2_1'], 'page2_2' => ['label' => 'Page 2.2', 'uri' => 'page2/page2_2', 'pages' => ['page2_2_1' => ['label' => 'Page 2.2.1', 'uri' => 'page2/page2_2/page2_2_1'], 'page2_2_2' => ['label' => 'Page 2.2.2', 'uri' => 'page2/page2_2/page2_2_2', 'active' => '1']]], 'page2_3' => ['label' => 'Page 2.3', 'uri' => 'page2/page2_3', 'textdomain' => 'LaminasTest_No', 'pages' => ['page2_3_1' => ['label' => 'Page 2.3.1', 'uri' => 'page2/page2_3/page2_3_1'], 'page2_3_2' => ['label' => 'Page 2.3.2', 'uri' => 'page2/page2_3/page2_3_2', 'visible' => '0', 'pages' => ['page2_3_2_1' => ['label' => 'Page 2.3.2.1', 'uri' => 'page2/page2_3/page2_3_2/1', 'active' => '1'], 'page2_3_2_2' => ['label' => 'Page 2.3.2.2', 'uri' => 'page2/page2_3/page2_3_2/2', 'active' => '1', 'pages' => ['page_2_3_2_2_1' => ['label' => 'Ignore', 'uri' => '#', 'active' => '1']]]]], 'page2_3_3' => ['label' => 'Page 2.3.3', 'uri' => 'page2/page2_3/page2_3_3', 'resource' => 'admin_foo', 'textdomain' => 'LaminasTest_1', 'pages' => ['page2_3_3_1' => ['label' => 'Page 2.3.3.1', 'uri' => 'page2/page2_3/page2_3_3/1', 'active' => '1', 'textdomain' => 'LaminasTest_2'], 'page2_3_3_2' => ['label' => 'Page 2.3.3.2', 'uri' => 'page2/page2_3/page2_3_3/2', 'resource' => 'guest_foo', 'active' => '1']]]]]]]],
    'nav_test4' => [
        'submenu' => [
            'label' => 'Themes',
            'title' => 'Themes',
            'uri' => '#',
            'id' => 'sub-id',
            'class' => 'btn-secondary',
            'pages' => [
                'bs' => [
                    'label' => 'Bootstrap',
                    'title' => 'Bootstrap',
                    'route' => 'info',
                    'params' => ['id' => 'bs'],
                    'id' => 'bs-id',
                    'class' => 'btn-secondary',
                ],
                'default' => [
                    'label' => 'Default',
                    'title' => 'Default',
                    'uri' => '/test.html',
                    'params' => ['id' => 'default'],
                    'id' => 'default-id',
                    'class' => 'btn-secondary',
                ],
                'acio' => [
                    'label' => 'acio',
                    'title' => 'acio',
                    'route' => 'info',
                    'params' => ['id' => 'acio'],
                    'id' => 'acio-id',
                    'class' => 'btn-secondary',
                ],
                'bmw' => [
                    'label' => 'bmw',
                    'title' => 'bmw',
                    'route' => 'info',
                    'params' => ['id' => 'bmw'],
                    'id' => 'bmw-id',
                    'class' => 'btn-secondary',
                ],
                'brinkhoff' => [
                    'label' => 'brinkhoff',
                    'title' => 'brinkhoff',
                    'route' => 'info',
                    'params' => ['id' => 'brinkhoff'],
                    'id' => 'brinkhoff-id',
                    'class' => 'btn-secondary',
                ],
                'clevertom' => [
                    'label' => 'clevertom',
                    'title' => 'clevertom',
                    'route' => 'info',
                    'params' => ['id' => 'clevertom'],
                    'id' => 'clevertom-id',
                    'class' => 'btn-secondary',
                ],
                'contrinity' => [
                    'label' => 'contrinity',
                    'title' => 'contrinity',
                    'route' => 'info',
                    'params' => ['id' => 'contrinity'],
                    'id' => 'contrinity-id',
                    'class' => 'btn-secondary',
                ],
                'disbro' => [
                    'label' => 'disbro',
                    'title' => 'disbro',
                    'route' => 'info',
                    'params' => ['id' => 'disbro'],
                    'id' => 'disbro-id',
                    'class' => 'btn-secondary',
                ],
                'easymize' => [
                    'label' => 'easymize',
                    'title' => 'easymize',
                    'route' => 'info',
                    'params' => ['id' => 'easymize'],
                    'id' => 'easymize-id',
                    'class' => 'btn-secondary',
                ],
                'empathy' => [
                    'label' => 'empathy',
                    'title' => 'empathy',
                    'route' => 'info',
                    'params' => ['id' => 'empathy'],
                    'id' => 'empathy-id',
                    'class' => 'btn-secondary',
                ],
                'finumpriv' => [
                    'label' => 'finumpriv',
                    'title' => 'finumpriv',
                    'route' => 'info',
                    'params' => ['id' => 'finumpriv'],
                    'id' => 'finumpriv-id',
                    'class' => 'btn-secondary',
                ],
                'jdcplus' => [
                    'label' => 'jdcplus',
                    'title' => 'jdcplus',
                    'route' => 'info',
                    'params' => ['id' => 'jdcplus'],
                    'id' => 'jdcplus-id',
                    'class' => 'btn-secondary',
                ],
                'mv24' => [
                    'label' => 'mv24',
                    'title' => 'mv24',
                    'route' => 'info',
                    'params' => ['id' => 'mv24'],
                    'id' => 'mv24-id',
                    'class' => 'btn-secondary',
                ],
                'plus' => [
                    'label' => 'plus',
                    'title' => 'plus',
                    'route' => 'info',
                    'params' => ['id' => 'plus'],
                    'id' => 'plus-id',
                    'class' => 'btn-secondary',
                ],
                'smobile' => [
                    'label' => 'smobile',
                    'title' => 'smobile',
                    'route' => 'info',
                    'params' => ['id' => 'smobile'],
                    'id' => 'smobile-id',
                    'class' => 'btn-secondary',
                ],
                'sparda' => [
                    'label' => 'sparda',
                    'title' => 'sparda',
                    'route' => 'info',
                    'params' => ['id' => 'sparda'],
                    'id' => 'sparda-id',
                    'class' => 'btn-secondary',
                ],
            ],
        ],
    ],
];
