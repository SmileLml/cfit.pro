<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 400px;">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->projectplan->productplanRelation;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-150px'><?php echo $lang->projectplan->product;?></th>
            <td colspan=''><?php echo html::select('products[]', $products, '', "id='productIndex0' data-id='0' class='form-control chosen' onchange='setProductField(this)'");?></td>
            <th><?php echo $lang->projectplan->productPlan;?></th>
            <td colspan=''><?php echo html::select('plans[]',  array(),   '', "id='planIndex0'  data-id='0' class='form-control chosen'");?></td>
            <td class="c-actions">
              <a href="javascript:void(0)" onclick="addItem(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
              <a href="javascript:void(0)" onclick="delItem(this)" data-id='0' id='delItem0' class="btn btn-link"><i class="icon-close"></i></a>
            </td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='5'><?php echo html::submitButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
var productIndex = 0;
function addItem(obj)
{
    var originIndex = $(obj).attr('data-id');
    productIndex++;

    var $currentRow = $(obj).closest('tr').clone();

    $currentRow.find('#addItem' + originIndex).attr({'data-id': productIndex, 'id':'addItem' + productIndex});

    $currentRow.find('#productIndex' + originIndex + '_chosen').remove();
    $currentRow.find('#productIndex' + originIndex).attr({'id':'productIndex' + productIndex, 'data-id': productIndex});

    $currentRow.find('#planIndex' + originIndex + '_chosen').remove();
    $currentRow.find('#planIndex' + originIndex).attr({'id':'planIndex' + productIndex, 'data-id': productIndex});

    $(obj).closest('tr').after($currentRow);

    $('#productIndex' + productIndex).attr('class','form-control chosen');
    $('#productIndex' + productIndex).chosen();

    $('#planIndex' + productIndex).attr('class','form-control chosen');
    $('#planIndex' + productIndex).chosen();
}

function delItem(obj)
{
    var $currentRow = $(obj).closest('tr');

    if($("select[name*='products']").length > 1)
    {
        $currentRow.remove();
    }
}

function setProductField(obj)
{
    var dataID    = $(obj).attr('data-id');
    var productID = $(obj).val();

    $.get(createLink('projectplan', 'ajaxGetProductplans', "productID=" + productID + '&dataID=' + dataID), function(data)
    {
        $('#planIndex' + dataID + '_chosen').remove();
        $('#planIndex' + dataID).replaceWith(data);
        $('#planIndex' + dataID).val('');
        $('#planIndex' + dataID).chosen();
    });
}
</script>
<?php include '../../common/view/footer.html.php';?>
