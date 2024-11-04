$('#app').change(function()
{
   var app = $("#app").val();
      $.get(createLink('demand', 'ajaxGetProduct', "app=" + app), function(planList)
      {
         $('#product_chosen').remove();
         $('#product').replaceWith(planList);
         $('#product').chosen();

      });
   $.get(createLink('demand', 'ajaxGetProductPlan', "productID=" + 0), function(planList)
   {
      $('#productPlan_chosen').remove();
      $('#productPlan').replaceWith(planList);
      $('#productPlan').val('0');
      $('#productPlan').chosen();
   });
   $.get(createLink('demand', 'ajaxGetFixType'), function(data)
    {
       $('#fixType_chosen').remove();
       $('#fixType').replaceWith(data);
       $('#fixType').val('');
       $('#fixType').chosen();
    });
   $.get(createLink('demand', 'ajaxGetSecondLine', "fixType="+'project'), function(data)
   {
      $('#project_chosen').remove();
      $('#project').replaceWith(data);
      $('#project').chosen();
   });
   loadProductExecutions(0);
});