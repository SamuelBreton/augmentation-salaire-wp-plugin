<?php

// Function for the shortcode
function augmentation_salaire_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'submit_text' => __('Calculate', 'augmentation-salaire'), // Default button text
            'output_format' => 'text' // Default output format
        ),
        $atts
    );

    // Get GET parameters and validate them
    $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 2000; // Valeur par défaut : 2000
    $year = isset($_GET['year']) ? intval($_GET['year']) : ''; // Ensure it's an integer

    $start_year = get_option('augmentation_salaire_start_year', '2010');
    $current_year = date('Y');

    // Get custom CSS from settings
    $custom_css = get_option('augmentation_salaire_custom_css', '');

    // Enqueue jQuery and custom script for AJAX
    wp_enqueue_script('jquery');
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array('jquery'), null, true);
    wp_enqueue_script('augmentation-salaire-script', AUGMENTATION_SALAIRE_URL . 'public/js/augmentation-salaire.js', array('jquery', 'chartjs'), null, true);
    wp_localize_script('augmentation-salaire-script', 'augmentation_salaire_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('augmentation_salaire_nonce')
    ));

    ob_start();
    ?>
	 <style type="text/css">
	 #btn-salaire-submit {
		display: block;
		position: relative;
		margin-left: auto;
		margin-right: auto; 
	 }
     #augmentation-salaire-result {
        text-align:center;
     }
	</style>
    <?php if (!empty($custom_css)) : ?>
    <style type="text/css">
        <?php echo esc_html($custom_css); ?>
    </style>
    <?php endif; ?>

    <style type="text/css">
        #btn-salaire-submit {
            display: block;
            position: relative;
            margin-left: auto;
            margin-right: auto; 
        }
        #augmentation-salaire-result {
            text-align: center;
            margin: 20px 0;
        }
        .result-text {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .show-more-link {
            margin: 15px 0;
        }
        .show-more-link a {
            color: #0073aa;
            text-decoration: none;
            cursor: pointer;
        }
        .show-more-link a:hover {
            color: #00a0d2;
            text-decoration: underline;
        }
        #salary-chart-container {
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>

    <div id="form-augmentation-salaire">
        <form id="augmentation-salaire-form">
            <?php wp_nonce_field('augmentation_salaire_nonce', 'augmentation_salaire_nonce_field'); ?>
            <label for="amount"><?php _e('Votre salaire mensuel :', 'augmentation-salaire'); ?> </label>
            <input type="number" id="amount" name="amount" value="<?php echo esc_attr($amount); ?>" required>
            <label for="year"><?php _e('Année de début :', 'augmentation-salaire'); ?></label>
            <select id="year" name="year" required>
                <?php
                for ($i = $start_year; $i <= $current_year; $i++) {
                    $selected = ($i == $year) ? 'selected' : '';
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>
			<br />
            <button type="submit" id="btn-salaire-submit"><?php echo esc_html($atts['submit_text']); ?></button>
        </form>
        <div id="augmentation-salaire-result"></div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('augmentation_salaire', 'augmentation_salaire_shortcode');

// AJAX handler for form submission
function augmentation_salaire_ajax_handler() {
    check_ajax_referer('augmentation_salaire_nonce', 'security');

    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $year = isset($_POST['year']) ? intval($_POST['year']) : 0;

    if ($amount <= 0 || $year <= 0) {
        wp_send_json_error(__('Invalid input values.', 'augmentation-salaire'));
        wp_die();
    }

    $api_url = get_option('augmentation_salaire_api_url', 'https://api.augmentation-salaire.com/v1/calcul');
    
    // Ajout des paramètres pour obtenir les données historiques
    $response = wp_remote_get("$api_url?amount=$amount&year=$year&includeHistory=true");

    if (is_wp_error($response)) {
        wp_send_json_error(__('Error contacting the API.', 'augmentation-salaire'));
    } else {
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($data['adjustedAmount'])) {
            wp_send_json_success($data);
        } else {
            wp_send_json_error(__('Unexpected API response format.', 'augmentation-salaire'));
        }
    }
    wp_die();
}
add_action('wp_ajax_augmentation_salaire', 'augmentation_salaire_ajax_handler');
add_action('wp_ajax_nopriv_augmentation_salaire', 'augmentation_salaire_ajax_handler');
