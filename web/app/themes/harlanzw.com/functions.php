<?php
/**
 * Setup our application
 */


use Roots\Sage\Config;
use Roots\Sage\Container;

/**
 * Sage required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
$preload_files = collect(scandir(ROOT_DIR . '/app/components'))->map(function($file) {
    // Always preload the component files
    if ($file == '.' || $file == '..') {
        return false;
    }
    $file = str_replace('.php', '', $file);
    return 'components/' . $file;
})->filter(function($f) {
    return $f !== false;
})->merge([
    // Load extra explicit files
    'helpers',
    'setup',
    'filters',
])->toArray();

array_map(function ($file) {
    $file = str_replace('.php', '', $file);
   require_once ROOT_DIR . "/app/{$file}.php";

}, $preload_files);


Container::getInstance()
         ->bindIf('config', function () {
	         return new Config([
		         'assets' => require ROOT_DIR .'/config/app/assets.php',
		         'theme' => require ROOT_DIR .'/config/app/theme.php',
		         'view' => require ROOT_DIR .'/config/app/view.php',
		         'filesystems' => require ROOT_DIR .'/config/app/filesystems.php',
		         'rewrites' => require ROOT_DIR .'/config/app/rewrites.php',
	         ]);
         }, true);
