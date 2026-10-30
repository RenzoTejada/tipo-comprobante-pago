<?php

/**
 *
 * @link              https://renzotejada.com/
 * @package           Comprobante de Pago Perú
 *
 * @wordpress-plugin
 * Plugin Name:       Comprobante de Pago Perú
 * Plugin URI:        https://renzotejada.com/comprobante-de-pago-peru/
 * Description:       Payment Receipt for Peru where the option to choose bill or Invoice or others is added.
 * Version:           0.3.3
 * Author:            Renzo Tejada
 * Author URI:        https://renzotejada.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rt-tipo-comprobante
 * Domain Path:       /language
 * WC tested up to:   11.2.1
 * Requires at least: 6.8
 * Tested up to: 7.1.4
 * Requires PHP: 7.4
 * WC requires at least: 2.6
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Bootstrap only: this file intentionally contains no Free legacy functions.
 * They live in includes/runtime.php, loaded ONLY when PRO is absent.
 */
function rt_cpp033_pro_is_active() {
    $pro_file = 'comprobante-de-pago-peru-pro/comprobante-de-pago-peru-pro.php';
    $active_plugins = (array) get_option( 'active_plugins', array() );
    if ( in_array( $pro_file, $active_plugins, true ) ) {
        return true;
    }
    if ( is_multisite() ) {
        $network_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
        return isset( $network_plugins[ $pro_file ] );
    }
    return false;
}

if ( rt_cpp033_pro_is_active() ) {
    /** Prevent activation without crashing on duplicate functions. */
    register_activation_hook( __FILE__, function () {
        wp_die(
            '<h2>La versión PRO está activa</h2>' .
            '<p>Para activar <strong>Comprobante de Pago Perú Free</strong>, primero debes desactivar <strong>Comprobante de Pago Perú Pro</strong>. Ambas versiones no pueden utilizarse simultáneamente.</p>' .
            '<p>Este bloqueo no elimina pedidos, comprobantes ni configuraciones.</p>' .
            '<p><a class="button button-primary" href="' . esc_url( admin_url( 'plugins.php' ) ) . '">Volver a Plugins</a></p>',
            'No se puede activar la versión Free',
            array( 'response' => 403, 'back_link' => false )
        );
    } );

    add_action( 'admin_notices', function () {
        if ( ! current_user_can( 'activate_plugins' ) || ! function_exists( 'get_current_screen' ) ) { return; }
        $screen = get_current_screen();
        if ( ! $screen || 'plugins' !== $screen->id ) { return; }
        echo '<div class="notice notice-warning"><p><strong>Comprobante de Pago Perú:</strong> la versión PRO está activa. Para activar Free, primero desactiva PRO. Ningún dato se eliminará.</p></div>';
    } );

    // In rare cases both versions became active, deactivate only Free.
    add_action( 'admin_init', function () {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $free_file = plugin_basename( __FILE__ );
        if ( is_plugin_active( $free_file ) ) {
            deactivate_plugins( $free_file, true );
        }
    }, 1 );

    return;
}

require_once __DIR__ . '/includes/runtime.php';
