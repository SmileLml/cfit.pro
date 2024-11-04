<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/datepicker.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
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
        .kindeditor-ph{
            display: none;
        }
    </style>
    <div id="mainContent" class="main-content fade">
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->demandcollection->syncDemand; ?></h2>
            </div>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->demandcollection->syncType;?></th>
                        <td colspan="2"><?php
                            $disabled = 'edit' == $syncType ? ' disabled' : '';
                            echo html::radio('syncType',$lang->demandcollection->syncTypeList, $syncType,"onchange='syncTypeChange(this.value)' class='text-center' $disabled");
                            echo html::hidden('collectionId', $collection->id, '');
                            ?></td>
                    </tr>
                    <tr id='syncTypeTip'>
                        <th></th>
                        <td colspan='2' style="color:#F00010;"><?php echo $this->lang->demandcollection->syncTypeTip;?></td>
                    </tr>
                    <tr id="demandIDHidden">
                        <th class='w-180px'><?php echo $lang->demandcollection->demand->demandID; ?></th>
                        <td class="required" colspan="2">
                            <?php echo html::select('demandId', $demands, $collection->demandId, "class='form-control chosen' $disabled onChange = demandChange(this.value)"); ?>
                        </td>
                    </tr>
                    <tr id="opinionIDHidden">
                        <th class='w-180px'><?php echo $lang->demandcollection->demand->opinionID; ?></th>
                        <td class="required" colspan="2">
                            <?php echo html::input('opinionName','',"class='form-control' disabled"); ?>
                            <?php echo html::input('opinionID','',"class='form-control hidden'"); ?></td>
                    </tr>
                    <tr id="requirementIDHidden">
                        <th class='w-180px'><?php echo $lang->demandcollection->demand->requirementID; ?></th>
                        <td class="required" colspan="2">
                            <?php echo html::select('requirementID', $requirements, $demand->requirementID, "class='form-control chosen'"); ?>
                        </td>
                    </tr>
                    <tr id="titleHidden">
                        <th><?php echo $lang->demandcollection->demand->title;?></th>
                        <td class="required" colspan="2"><?php echo html::input('title', $demand->title, "class='form-control' maxlength='100' ");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->demandcollection->demand->desc; ?></th>
                        <td colspan='2' class='required'><?php echo html::textarea('desc', ' ', "class='form-control' "); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->demandcollection->demand->reason; ?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('reason', ' ', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <th class='w-140px'><?php echo $lang->demandcollection->demand->endDate;?></th>
                        <td class='required'><?php echo html::input('endDate', $demand->endDate, "class='form-control form-date'");?></td>
                        <td class="required">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->demandcollection->demand->end; ?></span>
                                <?php echo html::input('end', $demand->end, "class='form-control form-date'"); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class='w-180px'></th>
                        <td><span style="color:#F00010;"><?php echo $this->lang->demandinside->endDateTip;?></span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->demandcollection->demand->app; ?></th>
                        <td class="required"><?php echo html::select('app', $apps, $demand->app, "class='form-control chosen' "); ?></td>
                        <td class="required">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->demandcollection->demand->acceptUser; ?></span>
                                <?php echo  html::select('acceptUser', $users, $demand->acceptUser, "class='form-control chosen'"); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class='w-180px'><?php echo $lang->demandcollection->demand->product; ?></th>
                        <td class='required'>
                            <?php echo html::select('product', $productList, $demand->product, "class='form-control chosen'"); ?>
                        </td>
                        <td class="required">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->demandcollection->demand->productPlan; ?></span>
                                <?php echo html::select('productPlan', $plans, $demand->productPlan, "class='form-control chosen'"); ?>
                                <span class="input-group-btn fix-border ">
                                    <a href="javascript:;" class="btn addItem" id="proandver" style="width:30px"><i class="icon-refresh"></i></a>
                                </span>
                                <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:40px">
                                        <i class="icon-help" title="<?php echo $lang->demandcollection->demand->createPlanTips ?>"></i>
                                    </a></span>
                                <span class="input-group-btn fix-border"  onclick="createpro()" >
                                    <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->demandcollection->demand->newproduct?></span>
                                </span>
                                <span class="input-group-btn "  onclick="createPlan()" >
                                    <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->demandcollection->demand->newversion; ?></span>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class='w-180px'><?php echo $lang->demandcollection->demand->fixType; ?></th>
                        <td class="required"><?php echo html::select('fixType', $lang->demandcollection->demand->fixTypeList, $demand->fixType, "class='form-control chosen' onchange='selectfix()'"); ?></td>
                        <td class="required">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->demandcollection->demand->project; ?></span>
                                <?php $where = ''; $where = "onchange='loadProductExecutions( this.value,\"$demand->fixType\",\"$demand->app\")'";?>
                                <?php echo html::select('project', $projects,  $demand->project, "class='form-control chosen ' $where");?>
                            </div>
                        </td>
                    </tr>
                    <?php if($demand->files) : ?>
                    <tr>
                        <th><?php echo $lang->demandcollection->demand->filelist; ?></th>
                        <td>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                        echo $this->fetch('file', 'printFiles', array('files' => $demand->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php echo $lang->files; ?></th>
                        <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85'); ?></td>
                    </tr>
                    <tr>
                        <th class='w-140px'><?php echo $lang->demandcollection->demand->PO; ?></th>
                        <td class="required"><?php echo html::select('dealUser', $users, $demand->dealUser, "class='form-control chosen'"); ?></td>
                        <td>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->demandcollection->demand->mailto; ?></span>
                                <?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple"); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->demandcollection->demand->progress;?></th>
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
<?php $this->app->loadLang('demandinside'); ?>
<?php js::set('syncType', $syncType); ?>
<?php js::set('collectionId', $collection->id); ?>
<?php js::set('collection', $collection); ?>
<?php js::set('requirementID', $demand->requirementID); ?>
<?php js::set('project',$demand->project ? $demand->project : '' ); ?>
<?php js::set('fixType',$demand->fixType ? $demand->fixType : '' ); ?>
<?php js::set('app',$demand->app ? $demand->app : '' ); ?>
<?php js::set('execution',$demand->execution ? $demand->execution : '' ); ?>
<?php js::set('productTip', $lang->demandinside->productTip);?>
<?php js::set('dealUserSelect', html::select('dealUser', $users, '', "class='form-control chosen'")); ?>
<?php js::set('mailtoSelect', html::select('mailto[]', $users, '', "class='form-control chosen' multiple")); ?>
<?php js::set('appSelect',  html::select('app', $apps, $demand->app, "class='form-control chosen' ")); ?>
<?php js::set('acceptUserSelect',  html::select('acceptUser', $users, '', "class='form-control chosen'")); ?>
    <script>
        $(document).ready(function () {
            syncTypeChange(syncType);
            $('#opinionID').change();
            loadProductExecutions(project, fixType, app);//联动重置阶段

        });
        $('#requirementID').change(function (){
            var requirementID = $(this).val()
            $.get(createLink('demandinside', 'ajaxGetOpinionByRequirement', "requirementID=" + requirementID), function (data) {
                data = JSON.parse(data)
                $('#opinionID').val(data.opinionID)
                $('#opinionName').val(data.opinionName)
            });
            $.get(createLink('demandinside', 'ajaxGetEndDateByRequirementID', "requirementID=" + requirementID), function (data) {
                var data = eval('('+data+')');
                if(data.endDate != '0000-00-00'){
                    $('#endDate').val(data.endDate);
                }else{
                    $('#endDate').val('');
                }
                if(data.end != '0000-00-00'){
                    $('#end').val(data.end);
                }else{
                    $('#end').val('');
                }
            });
        })

        $('#product').change(function () {
            var productID = $(this).val();
            alert(productID)
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
                                window.location.href = createLink('demandcollection', 'browse');
                            }
                        }
                    }
                })
            }
            if (productID == 0) {
                var inputPlan = '<input type="text" name="productPlan" id="productPlan" value="" class="form-control "readonly >';
                $('#productPlan_chosen').remove();
                $('#productPlan').replaceWith(inputPlan);
                var href = $("#createp").attr('href');
            } else {
                $.get(createLink('demandinside', 'ajaxGetProductPlan', "productID=" + productID), function (planList) {
                    $('#productPlan_chosen').remove();
                    $('#productPlan').replaceWith(planList);
                    $('#productPlan').val(productPlan);
                    $('#productPlan').chosen();
                });
                var href = $("#createp").attr('href');
            }
            var alink = '<a href= "' + href + '" class="btn btn-info" data-app="product" id="createp" onclick="return createPlan()" target="_blank" rel="noopener noreferrer" style="border-radius: 0px 2px 2px 0px; border-left-color: transparent;"><i class="icon-plus" title=""></i>版本</a>;'
            $('#createp').replaceWith(alink);

        });
        //新建产品版本
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
        }

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
        }
        //刷新操作
        $("#proandver").click(function () {
            var productID = $('#product').val();
            var app = $('#app').val();
            $.get(createLink('demandinside', 'ajaxGetProduct','app='+app), function(productlist) {
                $('#product_chosen').remove();
                $('#product').replaceWith(productlist);
                $('#product').val(productlist);
                $('#product').chosen();
            });
            if(productID != 0){
                $.get(createLink('demandinside', 'ajaxGetProductPlan', "productID=" + productID), function(planList) {
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
            $.get(createLink('demandinside', 'ajaxGetSecondLine', "fixType=" + fixType + "&app="+app), function(data) {
                $('#project_chosen').remove();
                $('#project').replaceWith(data);
                $('#project').chosen();
                loadProductExecutions('0');//联动重置阶段
            });
            if(fixType == ''){
                loadProductExecutions('0');//联动重置阶段
            }
        }
        //验证产品版本
        $("form").submit(function(){
            var product = $('#product').val();
            var plan = $('#productPlan').val();
            if(product == '99999' && plan != '1'){
                js:alert('所属产品选无,产品版本只能选择无!');
                return false;
            }
        })

        function syncTypeChange(syncType)
        {
            if('created' == syncType){
                $('#demandIDHidden').addClass('hidden');
                $('#title').attr('disabled', false);
                $('#opinionIDHidden').removeClass('hidden');
                $('#requirementIDHidden').removeClass('hidden');

                $('#app_chosen').remove();
                $('#app').replaceWith(appSelect);
                $('#app').chosen();

                $('#acceptUser_chosen').remove();
                $('#acceptUser').replaceWith(acceptUserSelect);
                $('#acceptUser').chosen();

                appChange();
            }else {
                $('#demandIDHidden').removeClass('hidden');
                $('#opinionIDHidden').addClass('hidden');
                $('#requirementIDHidden').addClass('hidden');

                $('#title').val('');
                KindEditor.html('#desc', '')
                KindEditor.html('#reason', '')
                KindEditor.sync('#desc');
                KindEditor.sync('#reason');
            }
            if('created' == syncType || collection.demandId > 0){
                getChecked(collectionId, collection.demandId)
            }
        }

        function demandChange(demandId){
            getChecked(collectionId, demandId)
        }

        function getChecked(collectionId, demandId = '')
        {
            $.get(createLink('demandcollection', 'ajaxGetChecked', "collectionId=" + collectionId + "&demandId=" + demandId), function (data) {
                console.log(data)
                data = JSON.parse(data);
                $('#title').val(data.title);

                KindEditor.html('#desc', data.desc)
                KindEditor.html('#reason', data.reason)
                KindEditor.sync('#desc');
                KindEditor.sync('#reason');

                $('#endDate').val(data.endDate);
                $('#end').val(data.end);

                $('#dealUser_chosen').remove();
                $('#dealUser').replaceWith(dealUserSelect);
                $('#dealUser').val(data.PO);
                $('#dealUser').chosen();

                $('#mailto_chosen').remove();
                $('#mailto').replaceWith(mailtoSelect);
                $('#mailto').val(data.mailto);
                $('#mailto').chosen();

                if(demandId > 0){
                    $('#app_chosen').remove();
                    $('#app').replaceWith(appSelect);
                    $('#app').val(data.app);
                    $('#app').chosen();

                    $('#acceptUser_chosen').remove();
                    $('#acceptUser').replaceWith(acceptUserSelect);
                    $('#acceptUser').val(data.acceptUser);
                    $('#acceptUser').chosen();

                    appChange(data);
                }

            });
        }

        $('body').delegate('#app','change',function (){
            appChange();
        })

    </script>
<?php include '../../../common/view/footer.html.php'; ?>