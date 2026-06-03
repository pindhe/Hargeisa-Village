<?php



declare(strict_types=1);



/**

 * Main navbar: Home | About ▾ (About, Gallery) | Menu | Contact

 *

 * @return list<array{type: string, label: string, href?: string, children?: list<array{href: string, label: string}>}>

 */

function site_nav_structure(): array

{

    return [

        ['type' => 'link', 'href' => 'home', 'label' => 'Home'],

        [

            'type' => 'dropdown',

            'label' => 'About',

            'children' => [

                ['href' => 'about.php', 'label' => 'About'],

                ['href' => 'gallery.php', 'label' => 'Gallery'],

            ],

        ],

        ['type' => 'link', 'href' => 'menu.php', 'label' => 'Menu'],

        ['type' => 'link', 'href' => 'contact.php', 'label' => 'Contact'],

    ];

}



/** Flat links for footer and other lists. */

function site_nav_items(): array

{

    return [

        'menu.php' => 'Menu',

        'about.php' => 'About',

        'gallery.php' => 'Gallery',

        'contact.php' => 'Contact',

    ];

}



function nav_is_active(string $href): bool

{

    $current = $GLOBALS['currentPage'] ?? '';

    if ($href === '' || $href === 'home') {

        return $current === 'home' || $current === '';

    }

    return $current === $href;

}



/** @param list<array{href: string, label: string}> $children */

function nav_is_dropdown_active(array $children): bool

{

    foreach ($children as $child) {

        if (nav_is_active($child['href'])) {

            return true;

        }

    }

    return false;

}



function nav_url(string $href): string

{

    return $href === '' || $href === 'home' ? base_url() : base_url($href);

}

