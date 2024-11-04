$('#app').change(function()
{
   var product = $("#product").val();
   var productPlan = $("#productPlan").val();
   var project = $("#project").val();
  // if(product != 0 && productPlan != 0 &&  project != 0){
      $.get(createLink('demandinside', 'ajaxGetProduct', "productID=" + 0), function(planList)
      {
         $('#product_chosen').remove();
         $('#product').replaceWith(planList);
         $('#product').val('0');
         $('#product').chosen();

      });
   $.get(createLink('demandinside', 'ajaxGetProductPlan', "productID=" + 0), function(planList)
   {
      $('#productPlan_chosen').remove();
      $('#productPlan').replaceWith(planList);
      $('#productPlan').val('0');
      $('#productPlan').chosen();

   });
   $.get(createLink('demandinside', 'ajaxGetFixType'), function(data)
    {
       $('#fixType_chosen').remove();
       $('#fixType').replaceWith(data);
       $('#fixType').val('');
       $('#fixType').chosen();
    });
   $.get(createLink('demandinside', 'ajaxGetSecondLine', "fixType="+'project'), function(data)
   {
      $('#project_chosen').remove();
      $('#project').replaceWith(data);
      $('#project').chosen();
   });
   loadProductExecutions(0);
  // }
});