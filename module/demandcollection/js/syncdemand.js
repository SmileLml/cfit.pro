
function appChange(demand = '')
{
   var app = $('#app').val()
   $.get(createLink('demandinside', 'ajaxGetProduct', "app=" + app), function(planList)
   {
      $('#product_chosen').remove();
      $('#product').replaceWith(planList);
      if(demand != ''){
         $('#product').val(demand.product);
      }
      $('#product').chosen();

   });
   var productId = demand == '' ? 0 : demand.product;
   $.get(createLink('demandinside', 'ajaxGetProductPlan', "productID=" + productId), function(planList)
   {
      $('#productPlan_chosen').remove();
      $('#productPlan').replaceWith(planList);
      if(demand != ''){
         $('#productPlan').val(demand.productPlan);
      }else {
         $('#productPlan').val('0');
      }
      $('#productPlan').chosen();
   });
   $.get(createLink('demandinside', 'ajaxGetFixType'), function(data)
   {
      $('#fixType_chosen').remove();
      $('#fixType').replaceWith(data);
      if(demand != ''){
         $('#fixType').val(demand.fixType);
      }else {
         $('#fixType').val('');
      }
      $('#fixType').chosen();
   });
   $.get(createLink('demandinside', 'ajaxGetSecondLine', "fixType="+demand.fixType), function(data)
   {
      $('#project_chosen').remove();
      $('#project').replaceWith(data);
      if(demand != ''){
         $('#project').val(demand.project);
      }
      $('#project').chosen();
   });
   loadProductExecutions(0);
}