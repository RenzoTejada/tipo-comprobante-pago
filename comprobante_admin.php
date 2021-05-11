<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/************************* ADMIN PAGE **********************************
 ***********************************************************************/

add_action('admin_menu', 'comprobante_register_admin_page');

function comprobante_register_admin_page()
{
    add_submenu_page('woocommerce', 'Configuraciones', __('Peru Receipt', 'rt-tipo-comprobante'), 'manage_options', 'comprobante_settings', 'comprobante_submenu_settings_callback');
    add_action('admin_init', 'comprobante_register_comprobante_settings');
}

function rt_comprobante_success_notice()
{
    ?>
    <div class="updated notice">
        <p><?php _e('Was saved successfully', 'rt-tipo-comprobante') ?></p>
    </div>
    <?php
}

function comprobante_submenu_settings_callback()
{
    if (isset($_REQUEST["settings-updated"]) && sanitize_text_field($_REQUEST["settings-updated"] == true)) {
        rt_comprobante_success_notice();
    }
    ?>
    <style>
        input[type=text], select {
            width: 400px;
            margin: 0;
            padding: 6px !important;
            box-sizing: border-box;
            vertical-align: top;
            height: auto;
            line-height: 2;
            min-height: 30px;
        }
    </style>
    <div class="wrap woocommerce" >

        <h1><?php _e('Peru receipt type | invoice or bill integration in Woocommerce checkout', 'rt-tipo-comprobante') ?></h1>

        <hr>
        <h2 class="nav-tab-wrapper">
            <a href="?page=comprobante_settings&tab=docs" class="nav-tab <?php
            if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
                print " nav-tab-active";
            }
            ?>"><?php _e('Receipt', 'rt-tipo-comprobante') ?></a>
            <a href="?page=comprobante_settings&tab=help" class="nav-tab <?php
            if ($_REQUEST['tab'] == "help") {
                print " nav-tab-active";
            } ?>"><?php _e('Help', 'rt-tipo-comprobante') ?></a>
        </h2>

        <?php
        if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
            comprobante_submenu_settings_docs();
        } elseif ($_REQUEST['tab'] == "help") {
            comprobante_submenu_settings_help();
        }
        ?>
    </div>
    <?php
}

function comprobante_submenu_settings_docs()
{
    ?>

    <form method="post" action="options.php" id="comprobante_formulario">
        <?php settings_fields('comprobante_settings_group_docs'); ?>
        <?php do_settings_sections('comprobante_settings_group_docs'); ?>

        <h2><?php _e('Type of receipt enabled in your store', 'rt-tipo-comprobante') ?></h2>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="comprobante_checkbox_be" ><?php _e('Bill', 'rt-tipo-comprobante') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <input type="checkbox" name="comprobante_checkbox_be" id="comprobante_checkbox_be" value="on"
                        <?php if (esc_attr(get_option('comprobante_checkbox_be')) == "on") echo "checked"; ?> />
                </td>
            </tr>
            <tr>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="comprobante_checkbox_fe"><?php _e('Invoice', 'rt-tipo-comprobante') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <input type="checkbox" name="comprobante_checkbox_fe" id="comprobante_checkbox_fe" value="on"
                        <?php if (esc_attr(get_option('comprobante_checkbox_fe')) == "on") echo "checked"; ?> />
                </td>
            </tr>
            </tbody>
        </table>

        <h2><?php _e('Default document at purchase', 'rt-tipo-comprobante') ?></h2>

        <table class="form-table">
            <tbody>
            <tr valign="top" id="comprobante_tr_user">
                <th scope="row" class="titledesc">
                    <label for="comprobante_docs_default"><?php _e('Default document on purchase', 'rt-tipo-comprobante') ?></label>
                </th>
                <td class="forminp forminp-text forminp-text-facto">
                    <select name="comprobante_docs_default" id="comprobante_docs_default">
                        <?php
                        print "<option value='boleta'";
                        if (get_option('comprobante_docs_default') == "boleta") {
                            print " selected";
                        }
                        print ">".__('Bill', 'rt-tipo-comprobante')."</option>";

                        print "<option value='factura'";

                        if (get_option('comprobante_docs_default') == "factura") {
                            print " selected";
                        }

                        print ">".__('Invoice', 'rt-tipo-comprobante')."</option>";
                        ?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <?php submit_button(__( 'Save Changes', 'rt-tipo-comprobante' )); ?>
    </form>
    <?php
}

function comprobante_register_comprobante_settings()
{
    register_setting('comprobante_settings_group_docs', 'comprobante_docs_default');
    register_setting('comprobante_settings_group_docs', 'comprobante_checkbox_fe');
    register_setting('comprobante_settings_group_docs', 'comprobante_checkbox_be');

    if (!class_exists('woocommerce')) {
        add_action('admin_notices', 'rt_comprobante_error_no_woocommerce');
    }
    // Veamos si no tuvieramos ningún tipo de documento activo
    if (
            (get_option('comprobante_checkbox_fe') == "") &&
            (get_option('comprobante_checkbox_be') == "")
    ) {
        add_action('admin_notices', 'comprobante_fe_errornosetup');
    }
}


function rt_comprobante_error_no_woocommerce()
{
    ?>
    <div class="error notice">
        <p><?php _e("Peru receipt type for WooCommerce: The module needs to have WooCommerce installed to operate correctly.", 'rt-tipo-comprobante'); ?></p>
    </div>
    <?php
}

function comprobante_fe_errornosetup()
{
    ?>
    <div class="error notice">
        <p><?php _e('receipt type: You have not yet configured any type of document (invoice, receipt) to make purchases. Configure it ','rt-tipo-comprobante'); ?> <a href="<?php admin_url('admin.php?page=comprobante_settings'); ?>"><?php _e('Here','rt-tipo-comprobante'); ?></a></p>
    </div>
    <?php
}

function comprobante_submenu_settings_help()
{
    ?>
    <h2><?php _e('Help', 'rt-tipo-comprobante'); ?></h2>

    <h3><?php _e('What does this module do?', 'rt-tipo-comprobante'); ?></h3>

    <p><?php _e('It allows you to integrate your Woocommerce the type of document of the customers in the checkout.', 'rt-tipo-comprobante'); ?></p>

    <h3><?php _e('What is the cost of the module?', 'rt-tipo-comprobante'); ?></h3>

    <p><?php _e('This plugin is totally free.', 'rt-tipo-comprobante'); ?></p>

    <h3><?php _e('I have other questions', 'rt-tipo-comprobante'); ?></h3>

    <p><?php _e('Go to', 'rt-tipo-comprobante'); ?> <a href="https://renzotejada.com/contacto?url=dashboard-wodpress" target="_blank"><?php _e('RT - Contact', 'rt-tipo-comprobante'); ?></a></p>
    <?php
}
