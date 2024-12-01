<?php

class Augmentation_Salaire_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'augmentation_salaire_widget',
            __('Augmentation Salaire Calculator', 'augmentation-salaire'),
            array('description' => __('A widget for the Augmentation Salaire calculator.', 'augmentation-salaire'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo do_shortcode('[augmentation_salaire]');
        echo $args['after_widget'];
    }

    public function form($instance) {
        echo '<p>' . __('No settings for this widget.', 'augmentation-salaire') . '</p>';
    }

    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
}

// Register the widget
function register_augmentation_salaire_widget() {
    register_widget('Augmentation_Salaire_Widget');
}
add_action('widgets_init', 'register_augmentation_salaire_widget');
