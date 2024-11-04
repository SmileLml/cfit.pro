<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .demandtip {
        display: inline-block;
        position: absolute;
        width: 663px;
        height: 33px;
        color: lightslategray;
        margin-left: -40px
    }

    .demandtip span {
        position: absolute;
        bottom: 24px;
        width: 100%;
        text-align: center;
    }
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $this->lang->demandinside->copytable; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $this->lang->demandinside->projectrelate; ?></th>
                    <td colspan="2">
                        <div style="color:red"><span> <?php echo $this->lang->demandinside->projectrelatedemand ?></span></div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $this->lang->demandinside->opinionID; ?></th>
                    <td>
                        <?php echo html::select('opinionID', $opinions, $demand->opinionID, "class='form-control chosen'"); ?>
                    </td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $this->lang->demandinside->requirementID; ?></span>
                            <?php echo html::select('requirementID', '', $demand->requirementID, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->demandinside->title; ?></th>
                    <td colspan="2"><?php echo html::input('title', $demand->title, "class='form-control' maxlength='100'"); ?></td>
                </tr>
                <tr>
                    <th class='w-140px'><?php echo $this->lang->demandinside->endDate;?></th>
                    <td class='required'><?php echo html::input('endDate', $demand->endDate, "class='form-control form-date'");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $this->lang->demandinside->end; ?></span>
                            <?php echo html::input('end', $demand->end, "class='form-control form-date'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'></th>
                    <td>
                        <span style="color:#F00010;"><?php echo $this->lang->demandinside->endDateTip;?></span>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->demandinside->app; ?></th>
                    <td><?php echo html::select('app', $apps, $demand->app, "class='form-control chosen'"); ?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $this->lang->demandinside->acceptUser; ?></span>
                            <?php echo  html::select('acceptUser', $users, $demand->acceptUser, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $this->lang->demandinside->product; ?></th>
                    <td class='required'>
                        <?php echo html::select('product', $productList, $demand->product, "class='form-control chosen'"); ?>
                    </td>
                    <td>
                        <!--                        <div class='input-group'>-->
                        <!--                            <span class='input-group-addon'>-->
                        <?php //echo $this->lang->demandinside->productPlan; ?><!--</span>-->
                        <!--                            --><?php //echo html::select('productPlan', $plans,  $demand->productPlan, "class='form-control chosen'"); ?>
                        <!--                        </div>-->
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $this->lang->demandinside->productPlan; ?></span>
                            <?php echo html::select('productPlan', $plans, $demand->productPlan, "class='form-control chosen'"); ?>
                            <span class="input-group-btn fix-border "><a href="javascript:;" class="btn addItem" id="proandver" style="width:30px"><i class="icon-refresh"></i></a></span>
                            <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:40px"><i class="icon-help" title="<?php echo $this->lang->demandinside->createPlanTips ?>"></i></a></span>
<!--                            <span class="input-group-btn fix-border"><?php /*echo html::a($this->createLink('product', 'create'), '<i class="icon-plus"></i>' . $this->lang->demandinside->newproduct, '_blank', 'class="btn btn-info" onclick="return createpro()"') */?></span>-->
                            <span class="input-group-btn fix-border"  onclick="createpro()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $this->lang->demandinside->newproduct?></span></span>
<!--                            <span class="input-group-btn"><?php /*echo html::a($this->createLink('productplan', 'create', "productID =$demand->product"), '<i class="icon-plus" title=""></i>' . $this->lang->demandinside->newversion, '_blank', 'class="btn btn-info" data-app="product" onclick="return createPlan()" id="createp"') */?></span>-->
                            <span class="input-group-btn "  onclick="createPlan()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $this->lang->demandinside->newversion; ?></span></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $this->lang->demandinside->fixType; ?></th>
                    <td><?php echo html::select('fixType', $this->lang->demandinside->fixTypeList, $demand->fixType, "class='form-control chosen' onchange='selectfix()'"); ?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $this->lang->demandinside->project; ?></span>
                            <?php $where = ''; $where = "onchange='loadProductExecutions( this.value,\"$demand->fixType\",\"$demand->app\")'";?>
                            <?php echo html::select('project', $projects,  $demand->project, "class='form-control chosen ' $where");?>
                        </div>
                    </td>
                </tr>
                <!-- <tr>
                    <th><?php /*echo $this->lang->demandinside->execution;*/?></th>
                    <?php /*$ableClass = $demand->fixType == 'second' ? 'disabled' : ''; */?>
                    <td colspan='2'><?php /*echo html::select('execution', $executions, $demand->execution,"class='form-control $ableClass'");*/?></td>
                    <input type="hidden" name="flag" id="flag" value="">
                    <input type="hidden" name="executionid" id="executionid" value="">
                </tr>-->                
                <tr>
                    <th><?php echo $this->lang->demandinside->desc; ?></th>
                    <td colspan='2' class='required'><?php echo html::textarea('desc', $demand->desc, "class='form-control' "); ?></td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->demandinside->reason; ?></th>
                    <td colspan='2'><?php echo html::textarea('reason', $demand->reason, "class='form-control'"); ?></td>
                </tr>
                <tr>
                <tr>
                    <th><?php echo $lang->files; ?></th>
                    <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85'); ?></td>
                </tr>
                <tr>
                    <!--下一节处理人 -->
                    <th class='w-140px'><?php echo $this->lang->demandinside->PO; ?></th>
                    <td><?php echo html::select('dealUser', $users, $demand->dealUser, "class='form-control chosen'"); ?></td>
                    <!--工作量 -->
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $this->lang->demandinside->mailto; ?></span>
                            <?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->demandinside->progress;?></th>
                    <td colspan='2'><?php echo html::textarea('progress', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center'
                        colspan='3'><?php echo html::submitButton($this->lang->demandinside->submitBtn) . html::backButton(); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php js::set('requirementID', $demand->requirement); ?>
<?php js::set('project',$demand->project ? $demand->project : '' ); ?>
<?php js::set('fixType',$demand->fixType ? $demand->fixType : '' ); ?>
<?php js::set('app',$demand->app ? $demand->app : '' ); ?>
<?php js::set('execution',$demand->execution ? $demand->execution : '' ); ?>
<?php js::set('productTip', $lang->demandinside->productTip);?>
<script>
    $(document).ready(function () {
        $('#opinionID').change();
        loadProductExecutions(project, fixType, app);//联动重置阶段
    });
    $('#opinionID').change(function () {
        var opinionID = $(this).val();
        if (opinionID != 0) {
            $.get(createLink('demandinside', 'ajaxGetRequirement', "opinionID=" + opinionID), function (requirement) {
                $('#requirementID_chosen').remove();
                $('#requirementID').replaceWith(requirement);
                $('#requirementID').val(<?php echo $demand->requirementID; ?>);
                $('#requirementID').chosen();

                $('#requirementID').change(function () {
                    var requirementID = $('#requirementID').val();
                    $.get(createLink('demandinside', 'ajaxGetEndDateByRequirementID', "requirementID=" + requirementID), function (data) {
                        var data = eval('(' + data + ')');
                        if (data.endDate != '0000-00-00') {
                            $('#endDate').val(data.endDate);
                        } else {
                            $('#endDate').val('');
                        }
                        if(data.end != '0000-00-00'){
                            $('#end').val(data.end);
                        }else{
                            $('#end').val('');
                        }
                        if(data.desc)
                        {
                            KindEditor.instances[0].focus()
                            KindEditor.html('#desc', data.desc)
                            KindEditor.instances[0].blur()
                        }
                        if(data.reason)
                        {
                            KindEditor.instances[1].focus()
                            KindEditor.html('#reason', data.reason)
                            KindEditor.instances[1].blur()
                        }
                    });
                });
            });
        }
    });

    //新建产品版本
    //$("#createPlan").click(function()
    function createPlan() {
        var flag = "<?php echo $clickable = commonModel::hasPriv('productplan', 'create');?>";
        if (!flag) {
            js:alert('您没有菜单『产品管理』新建产品版本的权限，请联系质量部阮涛添加权限');
            return false;
        }
        var productID = $('#product').val();
        if(productID === "99999" || productID === "0" || productID === ''){
            js:alert('请选择所属产品后,再新增产品版本!');
            return false;
        }
        var url = 'productplan-create-'+productID+'.html';
        window.open(url, "_blank");
        return true;
    };//);

    //新建产品
    function createpro() {
        var flag = "<?php echo $clickable = commonModel::hasPriv('product', 'create');?>";
        if (!flag) {
            js:alert('您没有菜单『产品管理』新建产品的权限，请联系质量部阮涛添加权限');
            return false;
        }
        var app = $('#app').val();
        if(app === "0"){
            js:alert('请选择所属应用系统后，再新增所属产品！');
            return false;
        }

        var url = 'product-create-0-'+app+".html#app=product";
        window.open(url, "_blank");
        return true;
    };
    //刷新操作
    $("#proandver").click(function () {
        var productID = $('#product').val();
        var app = $('#app').val();
        $.get(createLink('demandinside', 'ajaxGetProduct','app='+app), function(productlist)
        {
            $('#product_chosen').remove();
            $('#product').replaceWith(productlist);
            $('#product').val(productlist);
            $('#product').chosen();
        });
        if(productID != 0){
            $.get(createLink('demandinside', 'ajaxGetProductPlan', "productID=" + productID), function(planList)
            {
                $('#productPlan_chosen').remove();
                $('#productPlan').replaceWith(planList);
                $('#productPlan').val(productPlan);
                $('#productPlan').chosen();
            });
        }
    });
    function selectfix(){
        var fixType = $('#fixType').val();
        var app = $('#app').val();
        $.get(createLink('demandinside', 'ajaxGetSecondLine', "fixType=" + fixType + "&app="+app), function(data)
        {
            $('#project_chosen').remove();
            $('#project').replaceWith(data);
            $('#project').chosen();
            loadProductExecutions('0');//联动重置阶段
        });
        if(fixType == ''){
            loadProductExecutions('0');//联动重置阶段
        }


    }
     $('#product').change(function () {
            var productID = $(this).val();
            //当选择产品为 无(99999) 时增加提示语
             if(productID == 99999){
                 bootbox.dialog({
                     title:'提示：',
                     message:productTip,
                     buttons:{
                         red:{
                             label:'确认',
                             className:'btn-danger',
                             callback:function(){
                                 return true;
                             }
                         },
                         blue:{
                             label:'取消',
                             className:'btn-primary',
                             callback:function(){
                                 window.location.href = createLink('demandinside', 'browse');
                             }
                         }
                     }
                 })
             }
            $.get(createLink('demandinside', 'ajaxGetProductPlan', "productID=" + productID), function (planList) {
                $('#productPlan_chosen').remove();
                $('#productPlan').replaceWith(planList);
                $('#productPlan').val(productPlan);
                $('#productPlan').chosen();
            });

     });
    //验证产品版本
    $("form").submit(function(){
        var product = $('#product').val();
        var plan = $('#productPlan').val();
        if(product == '99999' && plan != '1'){
            js:alert('所属产品选无,产品版本只能选择无!');
            return false;
        }
    })
</script>
<?php include '../../common/view/footer.html.php'; ?>
