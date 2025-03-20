<?php
/**
 * Plugin Name: Tripleseat Breakdance Form Action
 * Author: Nic Scott
 * Version: 1.0.3
 * Description: This plugin adds a custom form action to the Breakdance form element.
 * Requires Plugins: breakdance
 */

 namespace Creare\Tripleseat;


 add_action('init', function() {
    // fail if Breakdance is not installed and available
    if (!function_exists('\Breakdance\Forms\Actions\registerAction') || !class_exists('\Breakdance\Forms\Actions\Action')) {
        return;
    }
    
    require_once('inc/TripleSeat.class.php');
    
    \Breakdance\Forms\Actions\registerAction(new Tripleseat());

 });