<?php

namespace Mchardians\Bladescript;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Override;

class BladeScriptServiceProvider extends ServiceProvider 
{
    #[Override]
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__. '/../config/bladescript.php', 'bladescript'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/bladescript.php' => config_path('bladescript.php'),
        ], 'bladescript');

        Blade::precompiler(function ($string) {
            if (!str_contains($string, 'import') && !str_contains($string, 'export')) {
                return $string;
            }

            $jsPrefix = config('bladescript.aliases.js.prefix', '@/');
            $jsPath = rtrim(config('bladescript.aliases.js.path', 'resources/js/'), '/');
            
            $cssPrefix = config('bladescript.aliases.css.prefix', '#/');
            $cssPath = rtrim(config('bladescript.aliases.css.path', 'resources/css/'), '/');
            
            $mediaPrefix = config('bladescript.aliases.media.prefix', '~/');
            $mediaPath = rtrim(config('bladescript.aliases.media.path', 'resources/media/'), '/');

            $jsQ = preg_quote($jsPrefix, '/');
            $cssQ = preg_quote($cssPrefix, '/');
            $mediaQ = preg_quote($mediaPrefix, '/');

            $formatJsAsset = function ($path) use ($jsPath) {
                if (!preg_match('/\.[a-zA-Z0-9]+$/', $path)) {
                    $path .= '.js';
                }
                return "{{ Vite::asset('{$jsPath}/{$path}') }}";
            };

            $string = preg_replace_callback(
                '/import\s+([a-zA-Z0-9_]+)\s+from\s+[\'"]' . $mediaQ . '(.*?)[\'"]/u',
                function ($matches) use ($mediaPath) {
                    $varName = $matches[1];
                    $path = ltrim($matches[2], '/');
                    return "const {$varName} = \"{{ Vite::asset('{$mediaPath}/{$path}') }}\";";
                },
                $string
            );

            $string = preg_replace_callback(
                '/import\s+[\'"]' . $cssQ . '(.*?)[\'"]/u',
                function ($matches) use ($cssPath) {
                    $path = ltrim($matches[1], '/');
                    return "document.head.insertAdjacentHTML('beforeend', '<link rel=\"stylesheet\" href=\"{{ Vite::asset('{$cssPath}/{$path}') }}\">');";
                },
                $string
            );

            $string = preg_replace_callback(
                '/(import|export)\s+([^"\']+?)\s+from\s+[\'"]' . $jsQ . '(.*?)[\'"]/u',
                function ($matches) use ($formatJsAsset) {
                    return $matches[1] . ' ' . $matches[2] . ' from "' . $formatJsAsset(ltrim($matches[3], '/')) . '"';
                },
                $string
            );

            $string = preg_replace_callback(
                '/import\(\s*[\'"]' . $jsQ . '(.*?)[\'"]/u',
                function ($matches) use ($formatJsAsset) {
                    return 'import("' . $formatJsAsset(ltrim($matches[1], '/')) . '"';
                },
                $string
            );

            $string = preg_replace_callback(
                '/import\s+[\'"]' . $jsQ . '(.*?)[\'"]/u',
                function ($matches) use ($formatJsAsset) {
                    return 'import "' . $formatJsAsset(ltrim($matches[1], '/')) . '"';
                },
                $string
            );

            return $string;
        });
    }
}