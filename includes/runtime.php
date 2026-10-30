<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$plugin_tipo_comprobante_version = get_file_data( dirname( __DIR__ ) . '/comprobante-de-pago-peru.php', array('Version' => 'Version'), false);

if ( ! defined( 'Version_RT_Tipo_Comprobante' ) ) {
    define( 'Version_RT_Tipo_Comprobante', $plugin_tipo_comprobante_version['Version'] );
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', dirname( __DIR__ ) . '/comprobante-de-pago-peru.php', true );
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', dirname( __DIR__ ) . '/comprobante-de-pago-peru.php', true );
    }
} );

function rt_tipo_comprobante_load_textdomain()
{
    load_plugin_textdomain('rt-tipo-comprobante', false, basename( dirname( __DIR__ ) ) . '/language/');
}

add_action('init', 'rt_tipo_comprobante_load_textdomain');

function rt_comprobante_add_plugin_page_settings_link( $links )
{
    $links2[] = '<a href="' . esc_url( admin_url( 'admin.php?page=comprobante_settings' ) ) . '">' . esc_html__( 'Ajustes', 'rt-tipo-comprobante' ) . '</a>';
    $links = array_merge($links2,$links);
    return $links;
}

add_filter('plugin_action_links_'.plugin_basename( dirname( __DIR__ ) . '/comprobante-de-pago-peru.php' ), 'rt_comprobante_add_plugin_page_settings_link');


/*
 * ADMIN
 */

require dirname(__DIR__)."/comprobante_admin.php";

/*
 * CHECKOUT
 */

require dirname(__DIR__)."/comprobante_checkout.php";
/** Additional checkout fields for WooCommerce Checkout Blocks. */
add_action( 'woocommerce_init', function () {
    if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) { return; }
    $choices = array();
    if ( 'on' === get_option( 'comprobante_checkbox_be' ) ) { $choices[] = array( 'value' => 'boleta', 'label' => 'Boleta' ); }
    if ( 'on' === get_option( 'comprobante_checkbox_fe' ) ) { $choices[] = array( 'value' => 'factura', 'label' => 'Factura' ); }
    if ( ! $choices ) { return; }
    woocommerce_register_additional_checkout_field( array(
        'id' => 'rt-comprobante/tipo', 'label' => 'Tipo de comprobante',
        'location' => 'order', 'type' => 'select', 'required' => true, 'options' => $choices,
    ) );
    foreach ( array( 'dni' => 'DNI (boleta, 8 dígitos)', 'ruc' => 'RUC (factura, 11 dígitos)', 'responsable' => 'Responsable (factura)' ) as $key => $label ) {
        woocommerce_register_additional_checkout_field( array(
            'id' => 'rt-comprobante/' . $key, 'label' => $label,
            'optionalLabel' => $label, 'location' => 'order', 'type' => 'text', 'required' => false,
        ) );
    }
} );
add_action( 'woocommerce_set_additional_field_value', function( $key, $value, $group, $object ) {
    if ( 'other' !== $group || ! $object instanceof WC_Order ) { return; }
    $map = array( 'rt-comprobante/tipo' => '_comprobante', 'rt-comprobante/dni' => '_dni', 'rt-comprobante/ruc' => '_ruc', 'rt-comprobante/responsable' => '_responsable' );
    if ( isset( $map[ $key ] ) ) { $object->update_meta_data( $map[ $key ], sanitize_text_field( (string) $value ) ); }
}, 10, 4 );

add_action( 'admin_enqueue_scripts', function() {
    if ( isset( $_GET['page'] ) && 'comprobante_settings' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
        wp_enqueue_style( 'rt-comprobante-admin', plugin_dir_url( dirname( __DIR__ ) . '/comprobante-de-pago-peru.php' ) . 'admin.css', array(), Version_RT_Tipo_Comprobante );
    }
} );
