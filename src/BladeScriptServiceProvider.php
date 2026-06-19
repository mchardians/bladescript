<?php

namespace Mchardians\Bladescript;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeScriptServiceProvider extends ServiceProvider 
{
    public function boot(): void
    {
        Blade::precompiler(function ($string) {
            $formatAsset = function ($path) {
                if (!preg_match('/\.[a-zA-Z0-9]+$/', $path)) {
                    $path .= '.js';
                }
                return "{{ Vite::asset('resources/js/{$path}') }}";
            };

            $string = preg_replace_callback(
                '/(import|export)\s+(.*?)\s+from\s+[\'"]@\/(.*?)[\'"]/s',
                function ($matches) use ($formatAsset) {
                    return $matches[1] . ' ' . $matches[2] . ' from "' . $formatAsset($matches[3]) . '"';
                },
                $string
            );

            $string = preg_replace_callback(
                '/import\(\s*[\'"]@\/(.*?)[\'"]/s',
                function ($matches) use ($formatAsset) {
                    return 'import("' . $formatAsset($matches[1]) . '"';
                },
                $string
            );

            $string = preg_replace_callback(
                '/import\s+[\'"]@\/(.*?)[\'"]/s',
                function ($matches) use ($formatAsset) {
                    return 'import "' . $formatAsset($matches[1]) . '"';
                },
                $string
            );

            return $string;
        });
    }
}