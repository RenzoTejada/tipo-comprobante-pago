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
    <div class="wrap woocommerce rt-comprobante-admin" >
        <div class="rt-comprobante-header">
            <div class="rt-comprobante-brand"><img src="<?php echo esc_url( plugins_url( 'assets/renzo-tejada-oficial.png', __FILE__ ) ); ?>" alt="Renzo Tejada Web Developer" /></div>
            <div class="rt-comprobante-heading">
                <h1>Comprobante de Pago Perú</h1>
                <p>Configura los tipos de comprobantes disponibles en tu tienda WooCommerce.</p>
                <span class="rt-comprobante-version">v<?php echo esc_html( Version_RT_Tipo_Comprobante ); ?></span>
            </div>
            <a class="button rt-comprobante-support" href="https://renzotejada.com/contacto/" target="_blank" rel="noopener noreferrer">Contacto y soporte</a>
        </div>
        <hr>
        <h2 class="nav-tab-wrapper">
            <a href="?page=comprobante_settings&tab=docs" class="nav-tab <?php
            if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
                print " nav-tab-active";
            }
            ?>"><?php esc_html_e('Comprobante', 'rt-tipo-comprobante') ?></a>
            <a href="?page=comprobante_settings&tab=help" class="nav-tab <?php
            if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == "help") {
                print " nav-tab-active";
            } ?>"><?php esc_html_e('Ayuda', 'rt-tipo-comprobante') ?></a>
        </h2>

        <?php
        if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
            comprobante_submenu_settings_docs();
        } elseif (isset($_REQUEST['tab']) && $_REQUEST['tab'] == "help") {
            comprobante_submenu_settings_help();
        }
        ?>
    </div>
    <?php
}

function comprobante_submenu_settings_docs()
{
    ?>

    <form method="post" action="options.php" id="comprobante_formulario" class="rt-comprobante-card">
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
        <?php submit_button(__( 'Guardar cambios', 'rt-tipo-comprobante' )); ?>
        <div class="rt-comprobante-pro"><strong>¿Quieres conocer la versión PRO?</strong><p>Explora las funciones avanzadas de Comprobante de Pago Perú PRO.</p><a href="https://renzotejada.com/plugins/comprobante-de-pago-peru-pro-para-woocommerce/" target="_blank" rel="noopener noreferrer" class="button">Conocer la versión PRO</a></div>
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
        <p><?php _e('receipt type: You have not yet configured any type of document (invoice, receipt) to make purchases. Configure it ','rt-tipo-comprobante'); ?> <a href="<?php echo admin_url('admin.php?page=comprobante_settings'); ?>"><?php _e('Here','rt-tipo-comprobante'); ?></a></p>
    </div>
    <?php
}

function comprobante_submenu_settings_help()
{
    ?>
    <div class="rt-comprobante-card rt-comprobante-help"><h2>Ayuda y documentación</h2><p>Conoce las funciones del plugin y cómo ofrecer Boleta o Factura en tu tienda.</p>

    <h3><?php _e('What does this module do?', 'rt-tipo-comprobante'); ?></h3>

    <p><?php _e('It allows you to integrate your Woocommerce the type of document of the customers in the checkout.', 'rt-tipo-comprobante'); ?></p>

    <h3><?php _e('What is the cost of the module?', 'rt-tipo-comprobante'); ?></h3>

    <p><?php _e('This plugin is totally free.', 'rt-tipo-comprobante'); ?></p>

    <h3><?php _e('I have other questions', 'rt-tipo-comprobante'); ?></h3>

    <p><a class="button" href="https://renzotejada.com/contacto/" target="_blank" rel="noopener noreferrer">Contactar con soporte</a></p>
    <div class="rt-comprobante-pro"><strong>¿Necesitas más funcionalidades?</strong><p>Conoce Comprobante de Pago Perú PRO para WooCommerce.</p><a class="button button-primary" href="https://renzotejada.com/plugins/comprobante-de-pago-peru-pro-para-woocommerce/" target="_blank" rel="noopener noreferrer">Ver versión PRO</a></div></div>
    <?php
}
