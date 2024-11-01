<?php

if ( ! defined('ABSPATH') ) {
	die;
}

add_action('admin_menu', 'turtle_ads_network_menu_pages');
function turtle_ads_network_menu_pages() {
	    // Add the top-level admin menu
	    $page_title = 'Settings - Turtle Ad Network';
	    $menu_title = 'Turtle Ad Network';
	    $capability = 'manage_options';
	    $menu_slug = 'tan-settings';
	    $function = 'turtle_ads_network_settings';
	    add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);

	    // Add submenu page with same slug as parent to ensure no duplicates
	    $sub_menu_title = 'Settings';
	    add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);

	   
	    // Now add the submenu page for Add wallet address
	    $submenu_page_title = 'Wallet Address - Turtle Ad Network';
	    $submenu_title = 'Wallet Address';
	    $submenu_slug = 'tan-add-wallet-address';
	    $submenu_function = 'turtle_ads_network_add_wallet_address';
	    add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

	     // Now add the submenu page for Help
	    $submenu_page_title = 'Ad Segments - Turtle Ads Network';
	    $submenu_title = 'Ad Segments';
	    $submenu_slug = 'tan-add-ad-slots';
	    $submenu_function = 'turtle_ads_network_add_ad_slots';
	    add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

	     // Now add the submenu page for Help
	    $submenu_page_title = 'Ad Manager - Turtle Ads Network';
	    $submenu_title = 'Ad Manager';
	    $submenu_slug = 'tan-approve-ad';
	    $submenu_function = 'turtle_ads_network_approve_ad';
	    add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
}

function turtle_ads_network_settings() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    include( plugin_dir_path( __FILE__ ). '/ad-settings.php');

    // Render the HTML for the Settings page or include a file that does
}

function turtle_ads_network_add_ad_slots() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    include( plugin_dir_path( __FILE__ ). '/add-ad-slots.php');
    // Render the HTML for the Help page or include a file that does
}

function turtle_ads_network_add_wallet_address(){
	if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    include( plugin_dir_path( __FILE__ ). '/add-wallet-address.php');

}

function turtle_ads_network_approve_ad(){
	if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    include( plugin_dir_path( __FILE__ ). '/ad-approval.php');

}