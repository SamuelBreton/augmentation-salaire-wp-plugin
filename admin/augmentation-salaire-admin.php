<?php

// Add menu for settings in the admin area
add_action('admin_menu', 'augmentation_salaire_menu');
function augmentation_salaire_menu() {
    add_options_page(
        __('Augmentation Salaire Settings', 'augmentation-salaire'),
        __('Augmentation Salaire', 'augmentation-salaire'),
        'manage_options',
        'augmentation-salaire',
        'augmentation_salaire_options_page'
    );
}

// Display the settings page
function augmentation_salaire_options_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Augmentation Salaire Settings', 'augmentation-salaire'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('augmentation_salaire_options');
            do_settings_sections('augmentation-salaire');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'augmentation_salaire_settings_init');
function augmentation_salaire_settings_init() {
    register_setting('augmentation_salaire_options', 'augmentation_salaire_start_year');
    register_setting('augmentation_salaire_options', 'augmentation_salaire_custom_css');
    register_setting('augmentation_salaire_options', 'augmentation_salaire_output_format');

    add_settings_section(
        'augmentation_salaire_settings_section',
        __('Plugin Settings', 'augmentation-salaire'),
        null,
        'augmentation-salaire'
    );

    add_settings_field(
        'augmentation_salaire_start_year',
        __('Start Year', 'augmentation-salaire'),
        'augmentation_salaire_start_year_render',
        'augmentation-salaire',
        'augmentation_salaire_settings_section'
    );

    add_settings_field(
        'augmentation_salaire_custom_css',
        __('Custom CSS', 'augmentation-salaire'),
        'augmentation_salaire_custom_css_render',
        'augmentation-salaire',
        'augmentation_salaire_settings_section'
    );

    add_settings_field(
        'augmentation_salaire_output_format',
        __('Output Format', 'augmentation-salaire'),
        'augmentation_salaire_output_format_render',
        'augmentation-salaire',
        'augmentation_salaire_settings_section'
    );

    add_settings_field(
        'augmentation_salaire_api_url',
        __('URL de l\'API', 'augmentation-salaire'),
        function() {
            $api_url = get_option('augmentation_salaire_api_url', 'https://api.augmentation-salaire.com/v1/calcul');
            echo "<input type='text' name='augmentation_salaire_api_url' value='" . esc_attr($api_url) . "' class='regular-text'>";
        },
        'augmentation-salaire',
        'augmentation_salaire_settings_section'
    );

    register_setting('augmentation-salaire', 'augmentation_salaire_api_url');
}

function augmentation_salaire_start_year_render() {
    $year = get_option('augmentation_salaire_start_year', '2010');
    ?>
    <input type="number" name="augmentation_salaire_start_year" value="<?php echo esc_attr($year); ?>" min="1900" max="<?php echo date('Y'); ?>">
    <p class="description"><?php _e('Set the start year for the year selection field.', 'augmentation-salaire'); ?></p>
    <?php
}

function augmentation_salaire_custom_css_render() {
    $css = get_option('augmentation_salaire_custom_css', '');
    ?>
    <textarea name="augmentation_salaire_custom_css" rows="5" cols="50"><?php echo esc_textarea($css); ?></textarea>
    <p class="description"><?php _e('Add custom CSS for the Augmentation Salaire form.', 'augmentation-salaire'); ?></p>
    <?php
}

function augmentation_salaire_output_format_render() {
    $format = get_option('augmentation_salaire_output_format', 'text');
    ?>
    <select name="augmentation_salaire_output_format">
        <option value="text" <?php selected($format, 'text'); ?>><?php _e('Text', 'augmentation-salaire'); ?></option>
        <option value="json" <?php selected($format, 'json'); ?>><?php _e('JSON', 'augmentation-salaire'); ?></option>
    </select>
    <p class="description"><?php _e('Choose the output format for the results.', 'augmentation-salaire'); ?></p>
    <?php
}
