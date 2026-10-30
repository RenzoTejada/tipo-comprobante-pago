jQuery(function($){
  function refresh(){
    var factura=$('#billing_comprobante').val()==='factura';
    ['billing_dni','billing_first_name','billing_last_name'].forEach(function(k){$('#'+k+'_field').toggle(!factura);});
    ['billing_ruc','billing_responsable','billing_company'].forEach(function(k){$('#'+k+'_field').toggle(factura);});
  }
  $(document.body).on('change','#billing_comprobante',refresh);
  $(document.body).on('updated_checkout',refresh);
  refresh();
});
