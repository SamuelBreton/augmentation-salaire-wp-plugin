<?php
/*
Plugin Name: Augmentation Salaire
Description: Plugin pour calculer l'augmentation de salaire en fonction de l'inflation et de l'évolution du cout de la vie
Version: 1.5
Author: Samuel Breton
Text Domain: augmentation-salaire
Domain Path: /languages
*/

// Define constants for plugin paths
define('AUGMENTATION_SALAIRE_PATH', plugin_dir_path(__FILE__));
define('AUGMENTATION_SALAIRE_URL', plugin_dir_url(__FILE__));

// Load Text Domain for i18n
function augmentation_salaire_load_textdomain() {
    load_plugin_textdomain('augmentation-salaire', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'augmentation_salaire_load_textdomain');

// Include required files
require_once AUGMENTATION_SALAIRE_PATH . 'admin/augmentation-salaire-admin.php';
require_once AUGMENTATION_SALAIRE_PATH . 'public/augmentation-salaire-form.php';
require_once AUGMENTATION_SALAIRE_PATH . 'widgets/augmentation-salaire-widget.php';
