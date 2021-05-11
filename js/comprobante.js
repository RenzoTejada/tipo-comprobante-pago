jQuery("#billing_comprobante").select2();

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
    if (jQuery('#billing_tipo_comprobante option:selected').val() == "Boleta")
	{
    	jQuery('#billing_tipo_comprobante option:selected').val("boleta");
        document.getElementById("billing_dni_field").style.display = "block";
        document.getElementById("billing_first_name_field").style.display = "block";
        document.getElementById("billing_last_name_field").style.display = "block";
        document.getElementById("billing_ruc_field").style.display = "none";
        document.getElementById("billing_responsable_field").style.display = "none";
        document.getElementById("billing_company_field").style.display = "none";
	}
    else if (jQuery('#billing_tipo_comprobante option:selected').val() == "factura")
	{
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
    
    jQuery('#billing_ruc').Rut({
        on_error: function () {
            alert('El ruc ingresado es incorrecto');
            jQuery('#billing_ruc').val('');
            jQuery('#billing_ruc').focus();
        },
        format_on: 'keyup'
    });
}

jQuery(document).ready(function () {
    rt_comprobante_cambiar();
});

(function ($) {
    jQuery.fn.Rut = function (options) {
        var defaults = {
            digito_verificador: null,
            on_error: function () {
            },
            on_success: function () {
            },
            validation: true,
        };

        var opts = $.extend(defaults, options);

        return this.each(function () {
            if (defaults.validation) {
                if (defaults.digito_verificador == null) {
                    jQuery(this).bind('blur', function () {
                        var rut = jQuery(this).val();
                        if (jQuery(this).val() != "" && !jQuery.Rut.validar(rut)) {
                            defaults.on_error();
                        } else if (jQuery(this).val() != "") {
                            defaults.on_success();
                        }
                    });
                } else {
                    var id = jQuery(this).attr("id");
                    jQuery(defaults.digito_verificador).bind('blur', function () {
                        var rut = jQuery("#" + id).val() + "-" + jQuery(this).val();
                        if (jQuery(this).val() != "" && !jQuery.Rut.validar(rut)) {
                            defaults.on_error();
                        } else if (jQuery(this).val() != "") {
                            defaults.on_success();
                        }
                    });
                }
            }
        });
    }
})(jQuery);

jQuery.Rut = {

    validar: function (ruc) {
        //11 dígitos y empieza en 10,15,16,17 o 20
        if (!(ruc >= 1e10 && ruc < 11e9
                || ruc >= 15e9 && ruc < 18e9
                || ruc >= 2e10 && ruc < 21e9))
            return false;

        for (var suma = -(ruc % 10 < 2), i = 0; i < 11; i++, ruc = ruc / 10 | 0)
            suma += (ruc % 10) * (i % 7 + (i / 7 | 0) + 1);
        return suma % 11 === 0;
    }
};