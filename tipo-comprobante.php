<?php

/**
 *
 * @link              https://renzotejada.com/
 * @package           Comprobante de Pago Perú
 *
 * @wordpress-plugin
 * Plugin Name:       Comprobante de Pago Perú
 * Plugin URI:        https://renzotejada.com/comprobante-de-pago/
 * Description:       Payment Receipt for Peru where the option to choose bill or Invoice or others is added.
 * Version:           0.0.6
 * Author:            Renzo Tejada
 * Author URI:        https://renzotejada.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       rt-tipo-comprobante
 * Domain Path:       /language
 * WC tested up to:   5.2.2
 * WC requires at least: 2.6
 */
if (!defined('ABSPATH')) {
    exit;
}

$plugin_tipo_comprobante_version = get_file_data(__FILE__, array('Version' => 'Version'), false);

define('Version_RT_Tipo_Comprobante', $plugin_tipo_comprobante_version['Version']);

function rt_tipo_comprobante_load_textdomain()
{
    load_plugin_textdomain('rt-tipo-comprobante', false, basename(dirname(__FILE__)) . '/language/');
}

add_action('init', 'rt_tipo_comprobante_load_textdomain');

function rt_comprobante_add_plugin_page_settings_link( $links )
{
    $links2[] = '<a href="' . admin_url( 'admin.php?page=comprobante_settings' ) . '">' . __('Settings', 'rt-tipo-comprobante') . '</a>';
    $links = array_merge($links2,$links);
    return $links;
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'rt_comprobante_add_plugin_page_settings_link');


/*
 * ADMIN
 */

require dirname(__FILE__)."/comprobante_admin.php";

/*
 * CHECKOUT
 */

require dirname(__FILE__)."/comprobante_checkout.php";