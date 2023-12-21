jQuery(document).ready(function(  ) {
  jQuery("#billing_comprobante").select2();
});

var obj = document.getElementById("billing_comprobante_field");
if (obj != null) {
    if (obj.value == "factura") {
        document.getElementById("billing_dni_field").style.display = "none";
        document.getElementById("billing_first_name_field").style.display = "none";
        document.getElementById("billing_last_name_field").style.display = "none";
        document.getElementById("billing_ruc_field").style.display = "block";
        document.getElementById("billing_responsable_field").style.display = "block";
        document.getElementById("billing_company_field").style.display = "block";

    } else {
        document.getElementById("billing_dni_field").style.display = "block";
        document.getElementById("billing_first_name_field").style.display = "block";
        document.getElementById("billing_last_name_field").style.display = "block";
        document.getElementById("billing_ruc_field").style.display = "none";
        document.getElementById("billing_responsable_field").style.display = "none";
        document.getElementById("billing_company_field").style.display = "none";
    }

}

document.getElementById("billing_comprobante").onchange = function () {
    rt_comprobante_cambiar();
};

function rt_comprobante_cambiar()
{
    // Si el value es incorrecto, lo ajustamos
    if (jQuery('#billing_tipo_comprobante option:selected').val() == "Boleta") {
        jQuery('#billing_tipo_comprobante option:selected').val("boleta");
        document.getElementById("billing_dni_field").style.display = "block";
        document.getElementById("billing_first_name_field").style.display = "block";
        document.getElementById("billing_last_name_field").style.display = "block";
        document.getElementById("billing_ruc_field").style.display = "none";
        document.getElementById("billing_responsable_field").style.display = "none";
        document.getElementById("billing_company_field").style.display = "none";
    } else if (jQuery('#billing_tipo_comprobante option:selected').val() == "factura") {
        jQuery('#billing_tipo_comprobante option:selected').val("factura");
        document.getElementById("billing_dni_field").style.display = "none";
        document.getElementById("billing_first_name_field").style.display = "none";
        document.getElementById("billing_last_name_field").style.display = "none";
        document.getElementById("billing_ruc_field").style.display = "block";
        document.getElementById("billing_responsable_field").style.display = "block";
        document.getElementById("billing_company_field").style.display = "block";
    }

    if (document.getElementById("billing_comprobante").value == "factura") {
        document.getElementById("billing_dni_field").style.display = "none";
        document.getElementById("billing_first_name_field").style.display = "none";
        document.getElementById("billing_last_name_field").style.display = "none";
        document.getElementById("billing_dni").value = "";
        document.getElementById("billing_first_name").value = "";
        document.getElementById("billing_last_name").value = "";
        document.getElementById("billing_ruc_field").style.display = "block";
        document.getElementById("billing_company_field").style.display = "block";
        document.getElementById("billing_responsable_field").style.display = "block";
    } else {
        document.getElementById("billing_ruc_field").style.display = "none";
        document.getElementById("billing_company_field").style.display = "none";
        document.getElementById("billing_responsable_field").style.display = "none";
        document.getElementById("billing_ruc").value = "";
        document.getElementById("billing_company").value = "";
        document.getElementById("billing_responsable").value = "";
        document.getElementById("billing_dni_field").style.display = "block";
        document.getElementById("billing_first_name_field").style.display = "block";
        document.getElementById("billing_last_name_field").style.display = "block";
    }

}

jQuery(document).ready(function ()
{
    rt_comprobante_cambiar();
});

jQuery('#billing_dni').blur(function (event)
{
    var dni = jQuery('#billing_dni').val();
    rt_comprobante_validar_dni(dni);
});

function rt_comprobante_validar_dni(dni)
{
    var rpt = true;
    if (isNaN(dni)) {
        jQuery('#billing_nro').val('');
        jQuery('label[for="billing_dni"] .msj').remove();
        jQuery('#billing_dni_field').addClass('woocommerce-invalid');
        jQuery('label[for="billing_dni"]').append('<abbr class="required msj" title="required"> El dni no debe tener letras</abbr>');
        rpt = false;
    } else if (dni.length > 8) {
        jQuery('#billing_dni').val('');
        jQuery('label[for="billing_dni"] .msj').remove();
        jQuery('#billing_dni_field').addClass('woocommerce-invalid');
        jQuery('label[for="billing_dni"]').append('<abbr class="required msj" title="required"> El dni ingresado es incorrecto</abbr>');
        rpt = false;
    } else if (dni.length < 8) {
        jQuery('#billing_dni').val('');
        jQuery('label[for="billing_dni"] .msj').remove();
        jQuery('#billing_dni_field').addClass('woocommerce-invalid');
        jQuery('label[for="billing_dni"]').append('<abbr class="required msj" title="required"> El dni ingresado es incorrecto</abbr>');
        rpt = false;
    } else {
        jQuery('label[for="billing_dni"] .msj').remove();
    }
    return rpt;
}

jQuery('#billing_ruc').blur(function (event)
{
    if (jQuery('#billing_comprobante option:selected').val() == "factura") {
        var ruc = jQuery('#billing_ruc').val();
        if (rt_comprobante_validar_ruc(ruc)) {
            if (jQuery('#billing_comprobante option:selected').val() == "factura") {
                jQuery('label[for="billing_ruc"] .msj').remove();
                jQuery(document.body).trigger("update_checkout");
            }
        } else {
            jQuery('#billing_ruc').val('');
            jQuery('label[for="billing_ruc"] .msj').remove();
            jQuery('#billing_ruc_field').addClass('woocommerce-invalid');
            jQuery('label[for="billing_ruc"]').append('<abbr class="required msj" title="required"> El ruc ingresado es incorrecto</abbr>');
        }
    }
});

function rt_comprobante_validar_ruc(ruc)
{
    var rpt = false;
    valor = trim(ruc)
    if (esnumero(valor)) {
        if (valor.length == 8) {
            suma = 0
            for (i = 0; i < valor.length - 1; i++) {
                digito = valor.charAt(i) - '0';
                if (i == 0) suma += (digito * 2)
                else suma += (digito * (valor.length - i))
            }
            resto = suma % 11;
            if (resto == 1) resto = 11;
            if (resto + (valor.charAt(valor.length - 1) - '0') == 11) {
                rpt = true;
            }
        } else if (valor.length == 11) {
            suma = 0
            x = 6
            for (i = 0; i < valor.length - 1; i++) {
                if (i == 4) x = 8
                digito = valor.charAt(i) - '0';
                x--
                if (i == 0) suma += (digito * x)
                else suma += (digito * x)
            }
            resto = suma % 11;
            resto = 11 - resto

            if (resto >= 10) resto = resto - 10;
            if (resto == valor.charAt(valor.length - 1) - '0') {
                rpt = true;
            }
        }
    }

    return rpt;
}

function trim(cadena)
{
    cadena2 = "";
    len = cadena.length;
    for (var i = 0; i <= len; i++)
        if (cadena.charAt(i) != " ") {
            cadena2 += cadena.charAt(i);
        }
    return cadena2;
}

function esnumero(campo) {
    return (!(isNaN(campo)));
}
