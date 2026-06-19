<?php

use Illuminate\Support\Facades\Blade;

dataset('import_patterns', [
    ["import User from '@/components/User';", "resources/js/components/User.js"],
    ["import { login } from '@/auth.js';", "resources/js/auth.js"],
    ["import {\n    login,\n    logout\n} from '@/auth';", "resources/js/auth.js"],
    ["export { default as User } from '@/User.vue';", "resources/js/User.vue"],
    ["const module = await import('@/utils/helper');", "resources/js/utils/helper.js"],
    ["const {\n  login\n} = await import('@/auth');", "resources/js/auth.js"],
    ["import '@/bootstrap';", "resources/js/bootstrap.js"],
    ["import '@/app.css';", "resources/js/app.css"],
]);

it('transforms blade imports correctly', function (string $input, string $expectedAssetPath) {
    $compiledString = Blade::compileString($input);
    
    $expectedInjectedCode = "Vite::asset('{$expectedAssetPath}')";
    
    expect($compiledString)->toContain($expectedInjectedCode);
})->with('import_patterns');

it('ignores bare specifiers and relative paths', function () {
    $bareImport = "import axios from 'axios';";
    $relativeImport = "import User from './User.js';";

    expect(Blade::compileString($bareImport))->toBe($bareImport)
        ->and(Blade::compileString($relativeImport))->toBe($relativeImport);
});