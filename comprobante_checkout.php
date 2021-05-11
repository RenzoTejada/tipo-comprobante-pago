<?php
if (!defined('ABSPATH'))
    exit;

function rt_comprobante_add_checkout_field( $checkout )
{
    $opciones = array();

    // Default primero
    if ((get_option('comprobante_checkbox_be') == "on") && (get_option('comprobante_docs_default') == "boleta")) {
        $opciones['boleta'] = __('BILL', 'rt-tipo-comprobante');
    } elseif ((get_option('comprobante_checkbox_fe') == "on") && (get_option('comprobante_docs_default') == "factura")) {
        $opciones['factura'] = __('INVOICE', 'rt-tipo-comprobante');
    }

    if ((get_option('comprobante_checkbox_be') == "on") && (get_option('comprobante_docs_default') != "boleta")) {
        $opciones['boleta'] = __('BILL', 'rt-tipo-comprobante');
    }

    if ((get_option('comprobante_checkbox_fe') == "on") && (get_option('comprobante_docs_default') != "factura")) {
        $opciones['factura'] = __('INVOICE', 'rt-tipo-comprobante');
    }

    woocommerce_form_field( 'billing_comprobante', array(
        'type' => 'select',
        'class' => array( 'form-row-wide' ),
        'label' => __('Type of receipt', 'rt-tipo-comprobante'),
        'required' => true,
        'clear' => true,
        'options' => $opciones,
        'priority' => 1
    ), $checkout->get_value( 'billing_comprobante' ) );

    woocommerce_form_field( 'billing_dni', array(
        'type' => 'number',
        'class' => array( 'form-row-wide' ),
        'label' => __('DNI', 'rt-tipo-comprobante'),
        'required' => false,
    ), $checkout->get_value( 'billing_dni' ) );

    woocommerce_form_field( 'billing_ruc', array(
        'type' => 'number',
        'class' => array( 'form-row-wide' ),
        'label' => __('RUC', 'rt-tipo-comprobante'),
        'required' => false,
    ), $checkout->get_value( 'billing_ruc' ) );

    woocommerce_form_field( 'billing_responsable', array(
        'type' => 'text',
        'class' => array( 'form-row-wide' ),
        'label' => __('Responsible', 'rt-tipo-comprobante'),
        'required' => false,
    ), $checkout->get_value( 'billing_responsable' ) );

}

add_action( 'woocommerce_before_checkout_billing_form', 'rt_comprobante_add_checkout_field' );

function rt_comprobante_able_woocommerce_loading_css_js()
{
    // Check if WooCommerce plugin is active
    if( function_exists( 'is_woocommerce' ) ) {
        // Check if it's any of WooCommerce page
        global $wp;
        if ( is_checkout() && empty( $wp->query_vars['order-received'] ) ) {
                wp_register_script('comprobante_script', plugins_url('js/comprobante.js', __FILE__), array(), Version_RT_Tipo_Comprobante, true);
                wp_enqueue_script('comprobante_script');
        }
    }
}

add_action( 'wp_enqueue_scripts', 'rt_comprobante_able_woocommerce_loading_css_js',99 );

function rt_comprobante_custom_wc_default_address_fields($fields)
{
    $fields['billing']['billing_first_name']['required'] = false;
    $fields['billing']['billing_last_name']['required'] = false;
    $fields['billing']['billing_company']['required'] = false;
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'rt_comprobante_custom_wc_default_address_fields');


function rt_comprobante_validate_checkout_field( $fields )
{
    if ( ! $_POST['billing_comprobante'] ) {
        wc_add_notice( '<b>'. __('Please enter your Type of Receipt', 'rt-tipo-comprobante') .'</b> is a required field.', 'error' );
    }

    if($_POST['billing_comprobante'] == 'boleta'){
        if ( ! $_POST['billing_first_name'] ) {
            wc_add_notice( '<b>'. __('Please enter your First Name', 'rt-tipo-comprobante') .'</b> is a required field.', 'error' );
        }
        if ( ! $_POST['billing_last_name'] ) {
            wc_add_notice( '<b>'. __('Please enter your Last Name', 'rt-tipo-comprobante') .'</b> is a required field.', 'error' );

        }
        if ( ! $_POST['billing_dni'] ) {
            wc_add_notice( '<b>'. __('Please enter your DNI', 'rt-tipo-comprobante') .'</b> is a required field.', 'error' );
        }
    }

    if($_POST['billing_comprobante'] == 'factura') {

        if ( ! $_POST['billing_ruc'] ) {
            wc_add_notice( '<b>'. __('Please enter your RUC', 'rt-tipo-comprobante') .'</b> is a required field.', 'error' );
        }

        if ( ! $_POST['billing_responsable'] ) {
            wc_add_notice( '<b>'. __('Please enter your Responsible', 'rt-tipo-comprobante') .'</b> is a required field.', 'error' );
        }

        if ( ! $_POST['billing_company'] ) {
            wc_add_notice( '<b>'. __('Please enter your Company', 'rt-tipo-comprobante') .'</b> is a required field.', 'error' );
        }
    }
}

add_action( 'woocommerce_checkout_process', 'rt_comprobante_validate_checkout_field' );

function rt_comprobante_remove_checkout_optional_fields_label( $field, $key, $args, $value )
{
    // Only on checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        switch ($key) {
            case 'billing_comprobante':
            case 'billing_first_name':
            case 'billing_last_name':
            case 'billing_dni':
            case 'billing_ruc':
            case 'billing_responsable':
            case 'billing_company':
            $field = str_replace( $optional, ' <abbr class="required">*</abbr>', $field );
                break;
        }
    }
    return $field;
}

add_filter( 'woocommerce_form_field' , 'rt_comprobante_remove_checkout_optional_fields_label', 10, 4 );

function rt_comprobante_save_checkout_field( $order_id )
{
    if ( $_POST['billing_comprobante'] ) update_post_meta( $order_id, '_comprobante', sanitize_text_field( $_POST['billing_comprobante'] ) );
    if ( $_POST['billing_dni'] ) update_post_meta( $order_id, '_dni', sanitize_text_field($_POST['billing_dni'] ) );
    if ( $_POST['billing_ruc'] ) update_post_meta( $order_id, '_ruc', sanitize_text_field($_POST['billing_ruc'] ) );
    if ( $_POST['billing_responsable'] ) update_post_meta( $order_id, '_responsable', sanitize_text_field($_POST['billing_responsable'] ) );
}
add_action( 'woocommerce_checkout_update_order_meta', 'rt_comprobante_save_checkout_field' );

function rt_comprobante_show_checkout_field_order( $order )
{
    $order_id = $order->get_id();
    if ( get_post_meta( $order_id, '_comprobante', true ) ) echo '<p><strong>'.__('Type of Receipt', 'rt-tipo-comprobante').':</strong> ' . strtoupper(get_post_meta( $order_id, '_comprobante', true )) . '</p>';
    if ( get_post_meta( $order_id, '_dni', true ) ) echo '<p><strong>'.__('DNI', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order_id, '_dni', true ) . '</p>';
    if ( get_post_meta( $order_id, '_ruc', true ) ) echo '<p><strong>'.__('RUC', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order_id, '_ruc', true ) . '</p>';
    if ( get_post_meta( $order_id, '_responsable', true ) ) echo '<p><strong>'.__('Responsible', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order_id, '_responsable', true ) . '</p>';
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'rt_comprobante_show_checkout_field_order', 1, 1 );

function rt_comprobante_show_checkout_field_emails( $order, $sent_to_admin, $plain_text, $email )
{
    if ( get_post_meta( $order->get_id(), '_comprobante', true ) ) echo '<p><strong>'.__('Type of Receipt', 'rt-tipo-comprobante').':</strong> ' . strtoupper(get_post_meta( $order->get_id(), '_comprobante', true )) . '</p>';
    if ( get_post_meta( $order->get_id(), '_dni', true ) ) echo '<p><strong>'.__('DNI', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order->get_id(), '_dni', true ) . '</p>';
    if ( get_post_meta( $order->get_id(), '_ruc', true ) ) echo '<p><strong>'.__('RUC', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order->get_id(), '_ruc', true ) . '</p>';
    if ( get_post_meta( $order->get_id(), '_responsable', true ) ) echo '<p><strong>'.__('Responsible', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order->get_id(), '_responsable', true ) . '</p>';
}
add_action( 'woocommerce_email_after_order_table', 'rt_comprobante_show_checkout_field_emails', 20, 4 );

function rt_comprobante_show_custom_fields_thankyou($order_id)
{
    if ( get_post_meta( $order_id, '_comprobante', true ) ) echo '<p><strong>'.__('Type of Receipt', 'rt-tipo-comprobante').':</strong> ' . strtoupper(get_post_meta( $order_id, '_comprobante', true )) . '</p>';
    if ( get_post_meta( $order_id, '_dni', true ) ) echo '<p><strong>'.__('DNI', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order_id, '_dni', true ) . '</p>';
    if ( get_post_meta( $order_id, '_ruc', true ) ) echo '<p><strong>'.__('RUC', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order_id, '_ruc', true ) . '</p>';
    if ( get_post_meta( $order_id, '_responsable', true ) ) echo '<p><strong>'.__('Responsible', 'rt-tipo-comprobante').':</strong> ' . get_post_meta( $order_id, '_responsable', true ) . '</p>';
}

add_action('woocommerce_thankyou', 'rt_comprobante_show_custom_fields_thankyou', 20);