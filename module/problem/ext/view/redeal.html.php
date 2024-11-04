<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/datepicker.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <?php if(isset($res['result'])) : ?>
    <div class="center-block">
        <h2 style="word-wrap: break-word;">
              <span class="reviewTip" style="color: red">
                <?php echo $res['message'];?>
              </span>
        </h2>
    </div>
    <?php else:?>
    <div class="center-block">
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $problem->code; ?></span>
                <?php echo isonlybody() ? ("<span title='$problem->code'>" . $lang->problem->redeal . '</span>') : html::a($this->createLink('demand', 'view', "demandID=$problem->id"), $problem->name); ?>
                <?php if (!isonlybody()): ?>
                    <small><?php echo $lang->arrow . $lang->problem->redeal; ?></small>
                <?php endif; ?>
            </h2>
        </div>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
        <table class="table table-form">
            <tbody>
                <tr class="hidden">
                    <th><?php echo $lang->problem->app; ?></th>
                    <input type="hidden" name="app" id="app" value="<?php echo $problem->app; ?>">
                </tr>
                <?php foreach ($details as $key => $deatil):
                $indexKey = $key + 1; ?>
                <tr id="productTab<?php echo $indexKey; ?>">
                    <th class='w-110px'><?php echo $lang->problem->product; ?></th>
                    <td id="productZone">
                        <div class='table-row' style="width:400px">
                            <div class='table-col product-th' data-id='<?php echo $indexKey ?>'>
                                <?php echo html::select('product[]', $productList, $deatil->product, "class='form-control chosen productSelect' data-id = '$indexKey'  id ='product$indexKey' onchange='productChange(this)'"); ?>
                            </div>
                            <div class='table-col' style="width:175px">
                            <div class='input-group required  '>
                                <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()"> <span
                                            class="btn btn-info "><i class="icon-plus"
                                                                     title=""></i><?php echo $lang->problem->newproduct ?></span></span>
                                <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan; ?></span>
                                <?php echo html::select('productPlan[]', $deatil->productPlan, $deatil->plan, "class='form-control chosen w-100px productPlanSelect' id='p-$indexKey'"); ?>
                                <span class="input-group-btn addProductPlan" data-id='<?php echo $deatil->product ?>'
                                      onclick="createPlan(this)"> <span class="btn btn-info "><i class="icon-plus"
                                                                                                 title=""></i>版本</span></span>
                                <span class="input-group-btn addStage " onclick="addProductItem(this)"
                                      data-id='<?php echo $indexKey; ?>'> <span class="btn addItem"><i class="icon-plus"
                                                                                                       title=""></i></span></span>
                                <?php if ($indexKey > 1): ?>
                                    <span class="input-group-btn addStage " onclick="delProductItem(this)"
                                          data-id='<?php echo $indexKey; ?>' id='codeClose0'> <span class="btn addItem"><i
                                                    class="icon-close" title=""></i></span></span>
                                    <span class="input-group-btn fix-border"><a href="javascript:;"
                                                                                onclick="proandver(this)"
                                                                                class="btn addItem " style="width:30px"><i
                                                    class="icon-refresh"></i></a></span>

                                <?php else: ?>
                                    <span class="input-group-btn fix-border"><a href="javascript:;"
                                                                                onclick="proandver(this)"
                                                                                class="btn addItem"
                                                                                style="width:30px"><i
                                                    class="icon-refresh"></i></a></span>
                                    <span class="input-group-btn"><a href="javascript:;" class="btn addItem"
                                                                     style="width:35px"><i class="icon-help"
                                                                                           title="<?php echo $lang->problem->createPlanTips ?>"></i></a></span>
                                <?php endif; ?>
                            </div>
                            </div>
                        </div>
                    </div></td></tr>
                <?php endforeach; ?>
                <tr>
                    <th><?php echo $lang->problem->nextUser; echo "<br>".$lang->problem->nextStatus['assigned'];?></th>
                    <td colspan="3" class="required"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->problem->progress;?></th>
                    <td colspan='3' class="required"><?php echo html::textarea('progress', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <th></th>
                    <td colspan='3'><?php echo $this->lang->problem->redealProgressDesc;?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton('提交') . html::backButton(); ?></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php endif; ?>
<table class="hidden">
    <tbody id="productTable">
    <tr id='productTab0' class="hidden dev dev2 ">
        <th class='w-110px'><?php echo $lang->problem->product; ?></th>
        <td id="productZone">
            <div class='table-row' style="width:400px">
                <div class='table-col product-th' data-id='1'>
                    <?php echo html::select('product[]', $productList, "", "class='form-control productSelect' data-id = '' id= 'product0' onchange='productChange(this)'"); ?>
                </div>
                <div class='table-col' style="width:175px">
                    <div class='input-group'>
                        <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()"> <span
                                    class="btn btn-info "><i class="icon-plus"
                                                             title=""></i><?php echo $lang->problem->newproduct ?></span></span>

                        <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan; ?></span>
                        <?php echo html::select('productPlan[]', $productplan, "", "class='form-control w-100px productPlanSelect '  id=''"); ?>
                        <span class="input-group-btn addProductPlan" data-id='0' onclick="createPlan(this)"> <span
                                    class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                        <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='0'
                              id='codePlus0'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                        <span class="input-group-btn addStage " onclick="delProductItem(this)" data-id='0'
                              id='codeClose0'> <span class="btn addItem"><i class="icon-close"
                                                                            title=""></i></span></span>
                        <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)"
                                                                    class="btn addItem " style="width:30px"><i
                                        class="icon-refresh"></i></a></span>

                    </div>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<?php include '../../../common/view/footer.html.php'; ?>
<?php
echo js::set('app', $problem->app);
echo js::set('productPlan', $problem->productPlan);
?>

<script>

    //添加产品 产品版本列
    function addProductItem(obj) {
        var rowNum = $('#productZone .table-row').size();
        if (rowNum >= 17) {
            alert("添加失败，最多添加15个产品");
            return false;
        }

        var relevantObj = $('#productTable');
        var relevantHtml = relevantObj.clone();

        var x = 10000;
        var y = 0;
        var rand = parseInt(Math.random() * (x - y + 1) + y);
        relevantHtml.find('#codePlus0').attr({'id': 'codePlus' + rand, 'data-id': rand});
        relevantHtml.find('#codeClose0').attr({'id': 'codeClose' + rand, 'data-id': rand});

        relevantHtml.find('#product0').attr({'id': 'product' + rand});
        relevantHtml.find('#productTab0').attr({'id': 'productTab' + rand});

        relevantHtml.find('.productSelect').attr({'data-id': rand});
        relevantHtml.find('.productPlanSelect').attr({'id': 'p-' + rand});

        var objIndex = $(obj).attr('data-id');
        $('#productTab' + objIndex).after(relevantHtml.html());

        $('#productTab' + rand).attr('class', 'addProducthidden');

        $('#product' + rand).attr('class', 'form-control chosen');
        $('#product' + rand).chosen();

        $('#p-' + rand).attr('class', 'form-control chosen');
        $('#p-' + rand).chosen();

        $.get(createLink('problem', 'ajaxGetProduct', 'app=' + app + "&data_id=" + rand), function (productlist) {
            $('#product' + rand + '_chosen').remove();
            $('#product' + rand).replaceWith(productlist);
            $('#product' + rand).val(productlist);
            $('#product' + rand).chosen();
        });
    }

    //删除产品 产品版本列
    function delProductItem(obj) {
        var objIndex = $(obj).attr('data-id');
        $('#productTab' + objIndex).remove();
    }
</script>
