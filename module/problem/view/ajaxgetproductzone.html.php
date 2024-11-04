<td id="productZone">
    <div class='table-row' style="width:400px">
        <div class='table-col product-th' data-id = '1'>
            <?php echo html::select('product[]', $productList, "", "class='form-control chosen chosen-controled productSelect' data-id = '1' onchange='productChange(this)'");?>
        </div>
        <div class='table-col' style="width:160px">
            <div class='input-group'>
            <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct?></span></span>
                <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan;?></span>
                <?php echo html::select('productPlan[]', $productPlan, "", "class='form-control w-100px productPlanSelect' id='p-1'");?>
                <span class="input-group-btn addProductPlan" data-id='0' onclick="createPlan(this)" > <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='1'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem" style="width:30px"><i class="icon-refresh"></i></a></span>
                <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:35px"><i class="icon-help" title="<?php echo $lang->problem->createPlanTips?>"></i></a></span>
            </div>
        </div>
    </div>
</td>