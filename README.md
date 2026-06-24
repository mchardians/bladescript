# Laravel Bladescript

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mchardians/bladescript.svg?style=flat-square)](https://packagist.org/packages/mchardians/bladescript)
[![Total Downloads](https://img.shields.io/packagist/dt/mchardians/bladescript.svg?style=flat-square)](https://packagist.org/packages/mchardians/bladescript)
[![License](https://img.shields.io/packagist/l/mchardians/bladescript.svg?style=flat-square)](https://packagist.org/packages/mchardians/bladescript)

A smart precompiler for Laravel 11 that enables the use of native ES6 `import` syntax directly within Blade templates (`.blade.php`).

This package bridges the gap between the server-side Blade ecosystem and client-side bundlers (Vite/Rollup), allowing you to build pure monolith applications with Vanilla JavaScript that operate as smoothly as an SPA framework—without the overhead of complex build tools.

## Features

* **Native ES6 in Blade:** Use `import` and `export` directly inside `<script type="module">` tags.
* **Multi-Asset Support:** Seamlessly import JavaScript, CSS, and Media (Images/SVG).
* **Smart DOM Injection:** Imported CSS is automatically injected into the document head, bypassing strict MIME-type browser errors.
* **Dynamic Media Variables:** Imported media files are automatically transformed into pure JS variables containing the resolved Vite URL.
* **Highly Customizable:** Easily configure custom aliases and resolution paths to fit your project structure.

## Requirements

* PHP 8.2+
* Laravel 11.0+
* [vite-plugin-blade-script](https://www.npmjs.com/package/vite-plugin-blade-script) (Companion NPM package)

## Installation

You can install the package via composer:

```bash

composer require mchardians/bladescript

```

You can publish the config file with:

```bash

php artisan vendor:publish --tag="bladescript"

```

## Configuration

By default, the package uses the following aliases mapped to the resources/ directory:

* @/ resolves to resources/js/

* #/ resolves to resources/css/

* ~/ resolves to resources/media/

If you published the configuration file (config/bladescript.php), you can override these defaults:

```php

return [
    'aliases' => [
        'js'    => ['prefix' => 'js:', 'path' => 'assets/scripts/'],
        'css'   => ['prefix' => 'css:', 'path' => 'assets/styles/'],
        'media' => ['prefix' => 'img:', 'path' => 'assets/images/'],
    ],
];

```

## Usage

Write your code inside a ```<script type="module">``` tag within your Blade views or components.

1. **Javascript Imports**
Supports static, dynamic, and side-effect imports out of the box.

    ```javascript

    <script type="module">
        // Static Import (supports multiline destructuring)
        import { CardBottomSheet } from '@/components/CardBottomSheet';
        import { 
            initGrafana, 
            fetchMetrics 
        } from '@/monitoring/k6-dashboard';

        // Dynamic Import
        const module = await import('@/utils/math-helper');

        // Side-effect Import
        import '@/bootstrap';

        CardBottomSheet.init();
    </script>

    ```

2. **CSS Imports**
Useful for building isolated Vanilla JS Custom Elements. The package safely transforms this into native DOM manipulation instructions to prevent browser blocking.

    ```javascript

    <script type="module">
        import '#/components/bottom-sheet.css';
        import '#/animations/fade-in-up.css';
    </script>

    ```

3. **Media Imports**
Media imports are automatically compiled into constant variables ready to be used in your logic.

    ```javascript

    <script type="module">
        import heroImage from '~/hero/grapol-banner.jpg';
        import logoSipp from '~/logo.svg';

        const imgEl = document.createElement('img');
        imgEl.src = logoSipp;
        document.body.appendChild(imgEl);
    </script>

    ```

## License

The MIT License (MIT). Please see License File for more information.
