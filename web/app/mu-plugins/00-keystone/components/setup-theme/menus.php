<?php
namespace App;

const MENU_PRIMARY_NAVIGATION = 'primary_navigation';

/**
 * Register navigation menus
 * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
 */
register_nav_menus([
	MENU_PRIMARY_NAVIGATION => __('Primary Navigation', 'sage')
]);
