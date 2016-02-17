<?php

class TP_WC_2Parale_Tracking extends WC_Integration {

    function __construct() {
        $this->id = 'wc-2parale-tracking';
        $this->method_title = __( '2Parale Sale Tracking Pixel', 'wc-2parale-tracking' );
        $this->method_description = __( 'This is where you set up the parameters for 2Parale\'s sale tracking code:', 'wc-2parale-tracking' );

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Load user variables
        $this->campaign_unique = $this->get_option('campaign_unique');
        $this->campaign_secret = $this->get_option('campaign_secret');

        // Save settings if the we are in the right section
        if ( isset( $_POST[ 'section' ] ) && $this->id === $_POST[ 'section' ] ) {
            add_action( 'woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options') );
        }

        if($this->campaign_unique && $this->campaign_secret)
            add_action('woocommerce_thankyou', array($this, 'add_2parale_code'));
    }

    function init_form_fields() {
        $this->form_fields = array(
            'campaign_unique' => array(
                'title'       => __( 'Campaign unique code', 'wc-2parale-tracking' ),
                'description' => __( 'The campaign unique code you can find in your advertiser interface or in the tracking code as a parameter', 'wc-2parale-tracking' ),
                'desc_tip'    => true,
                'default'     => '',
            ),
            'campaign_secret' => array(
                'title'       => __( 'Confirm code', 'wc-2parale-tracking' ),
                'description' => __( 'This is the "confirm" parameter in your tracking code', 'wc-2parale-tracking' ),
                'desc_tip'    => true,
                'default'     => '',
            ),
        );
    }

    public function add_2parale_code($order_id) {
        $order = $this->parse_order_data($order_id);

        printf("<iframe height='1' width='1' scrolling='no' marginheight='0' marginwidth='0' frameborder='0' src='//event.2parale.ro/events/salecheck?amount=%s&campaign_unique=%s&confirm=%s&transaction_id=%s&description=%s'></iframe>",
            urlencode($order['amount']),
            urlencode($this->campaign_unique),
            urlencode($this->campaign_secret),
            urlencode($order['transaction_id']),
            urlencode($order['description'])
        );
    }

    public function parse_order_data($order_id) {
        $result = array(
            'amount' => 0,
            'transaction_id' => 0,
            'description' => ''
        );

        $f = new WC_Order_Factory();
        $order = $f->get_order($order_id);

        if(!$order)
            return $result;

        $result['amount'] = $order->get_total() - $order->get_total_tax() - $order->get_total_shipping();

        $result['transaction_id'] = $order->get_order_number();

        $result['description'] = array();
        foreach($order->get_items() as $item) {
            $result['description'][] = $item['item_meta']['_qty'][0] . 'x' . $item['name'];
        }
        $result['description'] = implode('|', $result['description']);


        return $result;
    }

}
