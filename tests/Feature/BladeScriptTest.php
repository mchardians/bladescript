<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;

dataset('default_js_patterns', [
    ["import User from '@/components/User';", "resources/js/components/User.js"],
    ["import { login } from '@/auth.js';", "resources/js/auth.js"],
    ["import {\n    login,\n    logout\n} from '@/auth';", "resources/js/auth.js"],
    ["export { default as User } from '@/User.vue';", "resources/js/User.vue"],
    ["const module = await import('@/utils/helper');", "resources/js/utils/helper.js"],
    ["const {\n  login\n} = await import('@/auth');", "resources/js/auth.js"],
    ["import '@/bootstrap';", "resources/js/bootstrap.js"],
    ["import '@/app.css';", "resources/js/app.css"],
]);

dataset('default_css_patterns', [
    ["import '#/style.css';", 'document.head.insertAdjacentHTML(\'beforeend\', \'<link rel="stylesheet" href="<?php echo e(Vite::asset(\'resources/css/style.css\')); ?>">\');'],
]);

dataset('default_media_patterns', [
    ["import logo from '~/logo.png';", 'const logo = "<?php echo e(Vite::asset(\'resources/media/logo.png\')); ?>";'],
]);

it('transforms blade imports correctly', function (string $input, string $expectedAssetPath) {
    $compiledString = Blade::compileString($input);
    
    $expectedInjectedCode = "Vite::asset('{$expectedAssetPath}')";
    
    expect($compiledString)->toContain($expectedInjectedCode);
})->with('default_js_patterns');

it('transforms default css imports into legal dom injections', function (string $input, string $expectedOutput) {
    expect(Blade::compileString($input))->toContain($expectedOutput);
})->with('default_css_patterns');

it('transforms default media imports into javascript variables', function (string $input, string $expectedOutput) {
    expect(Blade::compileString($input))->toContain($expectedOutput);
})->with('default_media_patterns');

it('ignores bare specifiers and relative paths', function () {
    $bareImport = "import axios from 'axios';";
    $relativeImport = "import User from './User.js';";

    expect(Blade::compileString($bareImport))->toBe($bareImport)
        ->and(Blade::compileString($relativeImport))->toBe($relativeImport);
});

it('respects custom user configuration for aliases and paths', function () {
    Config::set('bladescript.aliases', [
        'js'    => ['prefix' => 'js:', 'path' => 'assets/scripts/'],
        'css'   => ['prefix' => 'css:', 'path' => 'assets/styles/'],
        'media' => ['prefix' => 'img:', 'path' => 'assets/images/'],
    ]);

    $jsInput = "import User from 'js:components/User';";
    $cssInput = "import 'css:theme.css';";
    $mediaInput = "import logo from 'img:logo.png';";

    $compiledJs = Blade::compileString($jsInput);
    $compiledCss = Blade::compileString($cssInput);
    $compiledMedia = Blade::compileString($mediaInput);

    expect($compiledJs)->toContain("Vite::asset('assets/scripts/components/User.js')")
        ->and($compiledCss)->toContain("Vite::asset('assets/styles/theme.css')")
        ->and($compiledMedia)->toContain("Vite::asset('assets/images/logo.png')");
});