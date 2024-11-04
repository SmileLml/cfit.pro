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
        <?php $i = 0; foreach ($allProducts as $item) {
            $productIndex = 'productIndex' . $i;
            $planIndex = 'planIndex' . $i;
            ?>
            <tr>
                <th>产品名称</th>
                <td colspan=''><?php echo html::select('products[]', $products, $item['id'], "id=". $productIndex ." data-id=". $i ." class='form-control chosen' onchange='setProductField(this)'");?></td>
                <th>产品版本</th>
                <td colspan=''><?php
                    $item['allPlans']   = $item['allPlans'] ?? []; //无计划版本
                    $item['plan']       = $item['plan'] ?? "";//无选择版本
                    echo html::select('plans[]',  $item['allPlans'],   $item['plan'], "id=". $planIndex ."  data-id=". $i ." class='form-control chosen'");
                    ?></td>
                <td class="c-actions">
                    <a href="javascript:void(0)" onclick="addItemNow(this)" data-id='<?php echo $i;?>' id='addItem<?php echo $i;?>' class="btn btn-link"><i class="icon-plus"></i></a>
                    <a href="javascript:void(0)" onclick="addItemNow(this)" data-id='<?php echo $i;?>' id='delItem<?php echo $i;?>' class="btn btn-link"><i class="icon-close"></i></a>
                </td>
            </tr>
        <?php  $i++; } if(empty($allProducts)) {?>
          <tr>
            <th>产品编码</th>
            <td colspan=''><?php echo html::select('products[]', $products, '', "id='productIndex0' data-id='0' class='form-control chosen' onchange='setProductField(this)'");?></td>
            <th>计划版本</th>
            <td colspan=''><?php echo html::select('plans[]',  array(),   '', "id='planIndex0'  data-id='0' class='form-control chosen'");?></td>
            <td class="c-actions">
              <a href="javascript:void(0)" onclick="addItemNow(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
              <a href="javascript:void(0)" onclick="delItemNow(this)" data-id='0' id='delItem0' class="btn btn-link"><i class="icon-close"></i></a>
            </td>
          </tr>
        <?php }?>
          <tr>
            <td class='form-actions text-center' colspan='5'><?php echo html::submitButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
var productIndex = <?php echo $i;?>;

function addItemNow(obj)
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

function delItemNow(obj)
{
    var $currentRow = $(obj).closest('tr');

    if($("select[name*='products']").length > 1)
    {
        $currentRow.remove();
    }
}

function setProductField(obj)
{
    // console.log(obj);
    var dataID    = $(obj).attr('data-id');
    var productID = $(obj).val();

    $.get(createLink('projectplan', 'ajaxGetProductplansRelation', "productID=" + productID + '&dataID=' + dataID), function(data)
    {
        $('#planIndex' + dataID + '_chosen').remove();
        $('#planIndex' + dataID).replaceWith(data);
        $('#planIndex' + dataID).val('');
        $('#planIndex' + dataID).chosen();
    });
}
</script>
<?php include '../../common/view/footer.html.php';?>
