<?php

/*
Plugin Name: 2Parale for WooCommerce
Plugin URI:  https://wordpress.org/plugins/woo-2parale
Description: An integration plugin for 2Parale's tracking code in WooCommerce based sites
Version:     1.0
Author:      Tudor Sandu (tetele)
Author URI:  http://tudorsandu.ro
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'Go away!' );

class TP_Woo2Parale {
    public static function init() {
        static $instance;

        if(!$instance) {
            $instance = new self;
        }

        add_filter( 'woocommerce_integrations', array($instance, 'register_integration') );

        return $instance;
    }

    public function register_integration($integrations) {
        include dirname( __FILE__ ) . '/integration.php';

        $integrations[] = 'TP_WC_2Parale_Tracking';

        return $integrations;
    }
}

$tp_wc_tracking = TP_Woo2Parale::init();
