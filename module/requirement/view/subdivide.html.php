<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php include '../../common/view/datepicker.html.php'; ?>
<style>
    .bootbox.modal .modal-dialog {
        width: 550px;
        line-height: 1.7;
    }
</style>
    <div id="mainContent" class="main-content fade">
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->requirement->subdivide;?></h2>
            </div>
            <form method='post' class='load-indicator main-form form-ajax' enctype='multipart/form-data' id="batchCreateForm">
                <table class="table table-form">
                    <tbody>

                    <tr>
                        <!--温馨提示 -->
                        <th><?php echo $lang->requirement->reminder;?></th>
                        <td style='color: red;' colspan='4'><?php echo $lang->requirement->reminderDesc;?></td>
                    </tr>
<!--                    <tr id="line">-->
                    <tr>
                        <!--需求条目主题 -->
                        <th><?php echo $lang->requirement->demandTitle;?></th>
                        <td class='required' colspan='4'><?php echo html::input('title0', '', "class='form-control' maxlength='100'");?></td>
                        <td class="c-actions" >
                            <a href="javascript:void(0)" onclick="addItem(this)" data-id='0' id='demandPlus0' class="btn btn-link plus"><i class="icon-plus"></i></a>
                            <a href="javascript:void(0)" onclick="delItem(this)" data-id='0' id='demandClose0' class="btn btn-link clos hidden"><i class="icon-close"></i></a>
                        </td>
                    </tr>
<!--                    <tr>-->
<!--                        期望实现日期 -->
<!--                        <th>--><?php //echo $lang->requirement->endDate;?><!--</th>-->
<!--                        <td class='required' colspan='2'>--><?php //echo html::input('endDate0', $requirement->deadLine, "class='form-control form-date endDate'");?><!--</td>-->
<!--                        <td class='required' colspan='2'>-->
<!--                            <div class='input-group'>-->
<!--                                <span class='input-group-addon'>--><?php //echo $lang->requirement->end; ?><!--</span>-->
<!--                                --><?php //echo html::input('end0', $requirement->end, "class='form-control form-date end'"); ?>
<!--                            </div>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        期望实现日期
                       <th></th>-->
<!--                        <td colspan='2' style="color:#F00010;" id='endDateTip0'>--><?php //echo $this->lang->requirement->endDateTip;?><!--</td>-->
<!--                        <td colspan='2'></td>-->
<!--                    </tr>-->

                    <tr>
                        <!--期望实现日期 -->
                        <th><?php echo $lang->requirement->planEnd;?></th>
                        <td class='required' colspan='2'><?php echo html::input('end0', $requirement->planEnd, "class='form-control form-date end'"); ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->requirement->app; ?></th>
                        <td class='required' colspan='2'><?php echo html::select('app0', $apps, ' ', "class='form-control chosen approw'  onchange='selectApp(this.id)'"); ?></td>
                        <td class='required' colspan='2'>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->requirement->responsiblePerson; ?></span>
                                <?php echo html::select('acceptUser0', $users, '', "class='form-control chosen acceptUserrow'"); ?>
                            </div>
                        </td>
                    </tr>
                    <tr id="productTr">
                        <!--所属产品 -->
                        <th><?php echo $lang->requirement->demandProduct;?></th>
                        <td class='required' colspan='2'><?php echo html::select('product0',$productList,' ',"class='form-control chosen productClass' onchange='selectProduct(this.id, this.value)'");?></td>
                        <!--产品版本 -->

                        <td class='required' colspan='2'>
                            <div class='table-row ' >
                                <div class='table-col ' >
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->requirement->productVersion;?></span>
                                        <?php echo html::select('productPlan0','','',"class='form-control versionClass chosen'");?>
                                        <span class="input-group-btn fix-border "><a href="javascript:;" class="btn addItem proandverClass" style="width:30px" onclick="proandverRefresh(this.id)"><i class="icon-refresh"></i></a></span>
                                        <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:40px"><i class="icon-help" title="<?php echo $lang->requirement->createPlanTips?>"></i></a></span>
<!--                                        <span class="input-group-btn fix-border"><?php /*echo html::a($this->createLink('product','create'),'<i class="icon-plus"></i>'. $lang->requirement->newproduct ,'_blank','class="btn btn-info newproductClass" onclick="return createpro()"')*/?></span>-->
                                        <span class="input-group-btn fix-border newproductClass"  onclick="createpro(this.id)" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->requirement->newproduct?></span></span>
<!--                                        <span class="input-group-btn"><?php /*echo html::a($this->createLink('productplan','create','productID =0'),'<i class="icon-plus" title=""></i>'. $lang->requirement->newversion,'_blank','class="btn btn-info newversionClass" data-app="product" id="createp" onclick="return createPlan(this.id)"')*/?></span>-->
                                        <span class="input-group-btn newversionClass"  onclick="createPlan(this.id)" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->requirement->newversion; ?></span></span>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class='w-180px'><?php echo $lang->requirement->implementationForm; ?></th>
                        <td class='required' colspan='2'><?php echo html::select('fixType0',$lang->requirement->implementationFormList, '', "class='form-control chosen fixTypeClass' onchange='selectfix(this.id, this.value)'"); ?></td>
                        <td class='required' colspan='2'>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->requirement->project; ?></span>
                                <?php echo html::select('project0', $plans, '', "class='form-control chosen projectClass' onchange='selectproject(this.id,this.value)'");?>
                            </div>
                        </td>
                    </tr>
                   <!-- <tr>
                        <th><?php /*echo $lang->requirement->execution;*/?></th>
                        <td colspan='4' class='required'><?php /*echo html::select('execution0', $executions, '',"class='form-control executionClass'");*/?></td>
                        <input type="hidden" name="flag0" id="flag0" value="" class="flag">
                        <input type="hidden" name="executionid0" id="executionid0" value="" class="executionid">
                    </tr>-->

                    <tr id='descTr'>
                        <!--需求条目概述 -->
                        <th><?php echo $lang->requirement->demandDesc;?></th>
                        <td colspan='4' class='required'><?php echo html::textarea('desc0', $requirement->desc, "class='form-control kindeditor descClass'");?></td>
                    </tr>

                    <tr id='reasonTr'>
                        <th><?php echo $lang->requirement->reason; ?></th>
                        <td colspan='4' class='required'><?php echo html::textarea('reason0', $requirement->analysis, "class='form-control reasonClass'"); ?></td>
                    </tr>
                    <tr id='reasonTip'>
                        <th></th>
                        <td colspan='4' style="color:#F00010;"><?php echo $this->lang->requirement->reasonTip;?></td>
                    </tr>
                    <tr id='progressTr'>
                        <th><?php echo $lang->requirement->commentProgress;?></th>
                        <td colspan='4'><?php echo html::textarea('progress0', '', "class='form-control kindeditor progressClass'");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->files; ?></th>
                        <td colspan='4'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85&filesName=files0'); ?></td>
                    </tr>
                    <tr>
                        <!--下一节处理人 -->
                        <th class='w-140px'><?php echo $lang->requirement->nextUser; ?></th>
                        <td colspan='2'><?php echo html::select('dealUser', $productManagerList, '', "class='form-control chosen'"); ?></td>
                        <!--抄送人 -->
                        <td colspan='2'>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->requirement->mailto; ?></span>
                                <?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple"); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='5'>
                            <?php echo html::submitButton($this->lang->requirement->submitBtn);?>
                            <?php echo html::backButton();?>
                            <input type="hidden" name="descIndex" id="descIndex" value="">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
<?php js::set('descIndex', 0);?>
<?php //js::set('endDate', $requirement->deadLine);?>
<?php js::set('end', $requirement->planEnd);?>
<?php js::set('reason', $requirement->analysis); ?>
<?php js::set('productTip', $lang->demand->productCreateTip);?>
<script type="text/javascript">

        function createpro(createpId)
        {
            var array = createpId.split("-");
            var i = array[1];
            var flag = "<?php echo $clickable = commonModel::hasPriv('product', 'create');?>";
            if(!flag){
                js:alert('您没有菜单『产品管理』新建产品的权限，请联系质量部阮涛添加权限');
                return false;
            }
            var app = $('#app'+i).val();
            console.log('#app'+i);
            console.log(app);
            if(app === "0" || app == ''){
                js:alert('请选择所属应用系统后，再新增所属产品！');
                return false;
            }

            var url = 'product-create-0-'+app+".html#app=product";
            window.open(url, "_blank");
            return true;
        };
        $(document).ready(function(){
            $('#descTr').find('.form-control').attr('id', 'desc0');
            $('#desc0').kindeditor();

            $('#reasonTr').find('.form-control').attr('id', 'reason0');
            $('#reason0').kindeditor();

            $('#progressTr').find('.form-control').attr('id', 'progress0');
            $('#progress0').kindeditor();

    // $('#productTr').find(".productClass").attr('id', 'product');
            $('#productTr').find(".proandverClass").attr('id', 'proandver-0');
            $('#productTr').find(".newproductClass").attr('id', 'createproduct-0');
            $('#productTr').find(".newversionClass").attr('id', 'createp-0');
        //初次加载分割线隐藏
    // $('#productTr').find(".versionClass").attr('id', 'productPlan-0');
    // $('#productPlan-0').prop('disabled', true).trigger("chosen:updated");
    // $('#productPlan_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
});

function addItem(obj)
{
    descIndex++;
    var length =document.getElementsByClassName('lineClass');

    $('#descIndex').val(descIndex);
    var currentRow = $(obj).closest('tr').clone();
    currentRow.find('#demandClose0').css('display','inline');
    currentRow.find('.form-control').val('');
    currentRow.find('.form-control').attr({'id':'title' + descIndex, 'name':'title' + descIndex});

    currentRow.find('.plus').attr({'id':'demandPlus' + descIndex, 'data-id':+ descIndex});
    currentRow.find('.clos').attr({'id':'demandClose' + descIndex, 'data-id':+ descIndex});
    if(descIndex > 0){
        currentRow.find('#demandClose'+descIndex).removeClass('hidden');
    }

    var tr0 = $(obj).closest('tr').nextAll('tr').eq(0).clone();
    var tr1 = $(obj).closest('tr').nextAll('tr').eq(1).clone();
    var tr2 = $(obj).closest('tr').nextAll('tr').eq(2).clone();
    var tr3 = $(obj).closest('tr').nextAll('tr').eq(3).clone();
    var tr4 = $(obj).closest('tr').nextAll('tr').eq(4).clone();
    var tr5 = $(obj).closest('tr').nextAll('tr').eq(5).clone();
    var tr6 = $(obj).closest('tr').nextAll('tr').eq(6).clone();
    var tr7 = $(obj).closest('tr').nextAll('tr').eq(7).clone();
    var tr8 = $(obj).closest('tr').nextAll('tr').eq(8).clone();
    // var tr9 = $(obj).closest('tr').nextAll('tr').eq(9).clone();
    //var tr10 = $(obj).closest('tr').nextAll('tr').eq(10).clone();
    tr0.find('.end').val(end);
    tr0.find('.end').attr({'id':'end' + descIndex, 'name':'end' + descIndex});
    // tr0.find('.endDate').val(endDate);
    // tr0.find('.endDate').attr({'id':'endDate' + descIndex, 'name':'endDate' + descIndex});

    // tr1.find('.endDateTip0').val();
    // tr1.find('.endDateTip0').attr({'id':'endDateTip' + descIndex, 'name':'endDateTip' + descIndex});

    tr1.find('.approw').attr({'id':'app' + descIndex, 'name':'app' + descIndex });
    tr1.find('.acceptUserrow').attr({'id':'acceptUser' + descIndex, 'name':'acceptUser' + descIndex});
    tr1.find('.chosen-container').remove();

    tr2.find('.productClass').attr({'id': 'product' + descIndex, 'name':'product' + descIndex});
    tr2.find('.versionClass').attr({'id':'productPlan' + descIndex, 'name':'productPlan' + descIndex});
    tr2.find('.proandverClass').attr({'id':'proandver-' + descIndex});
    tr2.find('.newproductClass').attr({'id':'createproduct-' + descIndex});
    tr2.find('.newversionClass').attr({'id':'createp-' + descIndex});
    tr2.find('.chosen-container').remove();

    tr3.find('.fixTypeClass').attr({'id': 'fixType' + descIndex, 'name':'fixType' + descIndex});
    tr3.find('.projectClass').attr({'id': 'project' + descIndex, 'name':'project' + descIndex});
    tr3.find('.chosen-container').remove();

  /*  tr5.find('.executionClass').attr({'id': 'execution' + descIndex, 'name':'execution' + descIndex});
    tr5.find('.flag').attr({'id': 'flag' + descIndex, 'name':'flag' + descIndex});
    tr5.find('.executionid').attr({'id': 'executionid' + descIndex, 'name':'executionid' + descIndex});
    tr5.find('.chosen-container').remove();*/

    tr4.find('.ke-container').remove();
    tr4.find('.form-control').attr({'id': 'desc' + descIndex, 'name': 'desc' + descIndex});

    tr5.find('.ke-container').remove();
    tr5.find('.form-control').attr({'id': 'reason' + descIndex, 'name': 'reason' + descIndex});

    tr6.find('.reasonTip').val();
    tr6.find('.reasonTip').attr({'id':'reasonTip' + descIndex, 'name':'reasonTip' + descIndex});

    tr7.find('.ke-container').remove();
    tr7.find('.form-control').attr({'id': 'progress' + descIndex, 'name': 'progress' + descIndex});

    tr8.find('.form-control').attr('name', 'files' + descIndex + '[]');
    tr8.find('.form-control').attr('id', 'files' + descIndex);


    // $(obj).closest('tr').nextAll('tr').eq(9).after(tr8);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr8);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr7);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr6);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr5);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr4);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr3);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr2);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr1);
    $(obj).closest('tr').nextAll('tr').eq(8).after(tr0);
    $(obj).closest('tr').nextAll('tr').eq(8).after(currentRow);
    $(obj).closest('tr').nextAll('tr').eq(8).after("<tr  class = 'dashLine'><td colspan='6'><div style='width:80%;margin:10px 0 10px 5%;border:1px dashed'></div></td></tr>");
    $('#app' + descIndex).attr('class','form-control chosen approw');
    $('#app' + descIndex).chosen();
    $('#acceptUser' + descIndex).attr('class','form-control chosen acceptUserrow');
    $('#acceptUser' + descIndex).chosen();
   /* $('#product' + descIndex).attr('class','form-control chosen productClass');
    $('#product' + descIndex).chosen();*/

    $.get(createLink('requirement', 'ajaxGetProductCode', "app=" + 0 +"&data_id="+descIndex), function(planList)
    {
         $('#product' + descIndex).replaceWith(planList);
         $('#product' + descIndex).chosen();
    });
    $('#productPlan'+descIndex).attr('class','form-control versionClass chosen');
    $('#productPlan'+descIndex).chosen();
    $('#productPlan'+descIndex).prop('disabled', true).trigger("chosen:updated");
    $('#productPlan'+descIndex+'_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
    $('#fixType'+descIndex).attr('class','form-control fixTypeClass chosen');
    $('#fixType'+descIndex).chosen();
    $('#project'+descIndex).attr('class','form-control projectClass chosen');
    $('#project'+descIndex).chosen();
    /*$('#execution'+descIndex).attr('class','form-control executionClass chosen');
    if(!$('#execution'+descIndex).is('input')){
        $('#execution'+descIndex).chosen();
    }*/
    // $('#desc' + descIndex).val('');

    // if(reason == ';'){
    //     reason = '';
    // }

    $('#reason' + descIndex).val(reason);
    $('#progress' + descIndex).val('');
    $('#desc' + descIndex).kindeditor();
    $('#reason' + descIndex).kindeditor();
    $('#progress' + descIndex).kindeditor();
    // $('#files' + descIndex).val('');
    $(".form-date").datetimepicker(
    {
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            format: "yyyy-mm-dd"
    });
    loadProductExecutions(0, null, null, descIndex);
}

function delItem(obj)
{
    $(obj).parent().parent().prev('.dashLine').remove()

    var $currentRow = $(obj).closest('tr');
    var tr0    = $(obj).closest('tr').nextAll('tr').eq(0);
    var tr1 = $(obj).closest('tr').nextAll('tr').eq(1);
    var tr2 = $(obj).closest('tr').nextAll('tr').eq(2);
    var tr3 = $(obj).closest('tr').nextAll('tr').eq(3);
    var tr4 = $(obj).closest('tr').nextAll('tr').eq(4);
    var tr5 = $(obj).closest('tr').nextAll('tr').eq(5);
    var tr6 = $(obj).closest('tr').nextAll('tr').eq(6);
    var tr7 = $(obj).closest('tr').nextAll('tr').eq(7);
    var tr8 = $(obj).closest('tr').nextAll('tr').eq(8);
    // var tr9 = $(obj).closest('tr').nextAll('tr').eq(9);
    //var tr10 = $(obj).closest('tr').nextAll('tr').eq(10);

    if($("input[name*='title']").length > 1)
    {
        $currentRow.remove();
        tr0.remove();
        tr1.remove();
        tr2.remove();
        tr3.remove();
        tr4.remove();
        tr5.remove();
        tr6.remove();
        tr7.remove();
        tr8.remove();
        // tr9.remove();
        //tr10.remove();
    }

    descIndex--;
    // $(obj).parent().parent().prev('.dashLine').remove()

    $('#descIndex').val(descIndex);

    $("input[id^=title]").each((index, item)=>
    {
        $(item).attr({'id':'title' + index, 'name':'title' + index});
    })

    // $("input[id^=endDate]").each((index, item)=>
    // {
    //     $(item).attr({'id':'endDate' + index, 'name':'endDate' + index});
    // })

    $(".end").each((index, item)=>
    {
        $(item).attr({'id':'end' + index, 'name':'end' + index});
    })

    $("select[id^=app]").each((index, item)=>
    {
        $(item).attr({'id':'app' + index, 'name':'app' + index });
        $(item).parent().find('.chosen-container').attr({'id':'app' + index+'_chosen'});
    })

    $("select[id^=acceptUser]").each((index, item)=>
    {
        $(item).attr({'id':'acceptUser' + index, 'name':'acceptUser' + index});
        $(item).parent().find('.chosen-container').attr({'id':'acceptUser' + index+'_chosen'});
    })

    $(".productClass").each((index, item)=>
    {
        $(item).attr({'id':'product' + index, 'name':'product' + index});
        $(item).parent().find('.chosen-container').attr({'id':'product' + index+'_chosen'});
    })

    $(".versionClass").each((index, item)=>
    {
        $(item).attr({'id':'productPlan' + index, 'name':'productPlan' + index});
        $(item).parent().find('.chosen-container').attr({'id':'productPlan' + index+'_chosen'});
    })
    $(".proandverClass").each((index, item)=>
    {
        $(item).attr({'id':'proandver-' + index, 'name':'proandver-' + index});
    })
    $(".newproductClass").each((index, item)=>
    {
        $(item).attr({'id':'createproduct-' + index, 'name':'createproduct-' + index});
    })
    $(".newversionClass").each((index, item)=>
    {
        $(item).attr({'id':'createp-' + index, 'name':'createp-' + index});
    })
    $("select[id^=fixType]").each((index, item)=>
    {
        $(item).attr({'id':'fixType' + index, 'name':'fixType' + index});
        $(item).parent().find('.chosen-container').attr({'id':'fixType' + index+'_chosen'});
    })

    $("select[id^=project]").each((index, item)=>
    {
        $(item).attr({'id':'project' + index, 'name':'project' + index});
        $(item).parent().find('.chosen-container').attr({'id':'project' + index+'_chosen'});
    })

   /* $("select[id^=execution]").each((index, item)=>
    {
        $(item).attr({'id':'execution' + index, 'name':'execution' + index});
        $(item).parent().find('.chosen-container').attr({'id':'execution' + index+'_chosen'});
    })

    $("input[id^=flag]").each((index, item)=>
    {
        $(item).attr({'id':'flag' + index, 'name':'flag' + index});
    })

    $("input[id^=executionid]").each((index, item)=>
    {
        $(item).attr({'id':'executionid' + index, 'name':'executionid' + index});
    })*/

    $("textarea[id^=desc]").each((index, item)=>
    {
        $(item).attr({'id':'desc' + index, 'name':'desc' + index});
        if( $('#desc' + index).val() == ''){
         $('#desc' + index).val('');
        }

    })

    $("textarea[id^=reason]").each((index, item)=>
    {
        $(item).attr({'id':'reason' + index, 'name':'reason' + index});
        if($('#reason' + index).val() == ''){
            $('#reason' + index).val('');
        }
    })

    $("textarea[id^=progress]").each((index, item)=>
    {
        $(item).attr({'id':'progress' + index, 'name':'progress' + index});
    })

    $("input[type^=file]").each((index, item)=>
    {
        $(item).attr({'id':'files' + index, 'name':'files' + index + '[]'});
        $('#files' + index).val('');
    })

}

       //所属产品下拉框选择事件
       function selectProduct(productid, productvalue){
             var i = productid.slice(7);
            // if(productvalue == '99999'){
            //     $('#productPlan'+i).attr("disabled","disabled");
            //     $('#productPlan'+i).val('1').trigger("chosen:updated");
            //     var href=$("#createp-"+i).attr('href').slice(0,-19).concat(productvalue+".html?onlybody=yes");
            // }else{
               if(productvalue == 99999){
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
                                   window.location.href = createLink('requirement', 'browse');
                               }
                           }
                       }
                   })
               }
               $.get(createLink('demand', 'ajaxGetProductPlan', "productID=" + productvalue), function(data){
                $('#productPlan' + i + '_chosen').remove();
                $('#productPlan' + i).parent().addClass('productPlanParent' + i);
                $('#productPlan'+i).replaceWith(data);
                $('.productPlanParent' + i).find('#productPlan').attr({'id':'productPlan' + i, 'name':'productPlan' + i});
                $('.productPlanParent' + i).find('.text-danger').remove();
                // $('#productPlan').attr({'id':'productPlan' + i});
                $('#productPlan'+i).attr("class","versionClass chosen form-control");
                if(productvalue == '99999'){
                    $('#productPlan'+i).val('1').trigger("chosen:updated");
                }else {
                    $('#productPlan'+i).val('').trigger("chosen:updated");
                }
                $('#productPlan'+i).chosen();
            })

            // });
            if(productvalue === '0'){
                var href=$("#createp-"+i).attr('href').slice(0,-19).concat(productvalue+".html?onlybody=yes");
            }else {
                var href=$("#createp-"+i).attr('href');
                if(href.indexOf('cfitpmp') !== -1){
                    var href=$("#createp-"+i).attr('href').slice(0,28).concat(productvalue+".html?onlybody=yes");
                }else{
                    var href=$("#createp-"+i).attr('href').slice(0,20).concat(productvalue+".html?onlybody=yes");
                }
                // }
                var alink = '<a href= "'+href+'" class="btn btn-info newversionClass" data-app="product" id="createp-'+i+'" onclick="return createPlan(this.id)" target="_blank" rel="noopener noreferrer" style="border-radius: 0px 2px 2px 0px; border-left-color: transparent;"><i class="icon-plus" title=""></i>版本</a>;'
            }
            $("#createp-"+i).replaceWith(alink);

        }

        function createPlan(createpId)
        {
            var array = createpId.split("-");
            var i = array[1];
            var flag = "<?php echo $clickable = commonModel::hasPriv('productplan', 'create');?>";
            if(!flag){
                js:alert('您没有菜单『产品管理』新建产品版本的权限，请联系质量部阮涛添加权限');
                return false;
            }
            var productID = $('#product'+i).val();
            if(productID === "99999" || productID === "0" || productID === ''){
                js:alert('请选择所属产品后,再新增产品版本!');


                return false;
            }
            var url = 'productplan-create-'+productID+'.html';
            window.open(url, "_blank");
            return true;
        };

        function proandverRefresh(proandverId){
            var array = proandverId.split("-");
            var i = array[1];
            var productId = $('#product'+i).val();
            var app = $('#app'+i).val();
            $.get(createLink('demand', 'ajaxGetProductPlan', "productID=" + productId), function(planList)
            {
                $('#productPlan' + i + '_chosen').remove();
                $('#productPlan' + i).parent().addClass('productPlanParent' + i);
                $('#productPlan'+i).replaceWith(planList);
                $('.productPlanParent' + i).find('#productPlan').attr({'id':'productPlan' + i, 'name':'productPlan' + i});
                $('.productPlanParent' + i).find('.text-danger').remove();
                // $('#productPlan').attr({'id':'productPlan' + i});
                $('#productPlan'+i).attr("class","versionClass chosen form-control");
                $('#productPlan'+i).chosen();
                $('#productPlan'+i).val('').trigger("chosen:updated");
            });
            $.get(createLink('requirement', 'ajaxGetProductCode', "app=" + app +"&data_id="+i), function(productlist)
            {
                $('#product'+i+ '_chosen').remove();
                $('#product' + i).parent().addClass('productParent' + i);
                $('#product'+i).replaceWith(productlist);
                $('.productParent' + i).find('#product').attr({'id':'product' + i, 'name':'product' + i});
                $('#product'+i).attr("class","form-control chosen productClass");
                $('#product'+i).chosen();

            });
        }

        function selectApp(id){
            var idstr = id.slice(3)
            var app = $("#app" + idstr).val();
            // if(product != 0 && productPlan != 0 &&  project != 0){
            $.get(createLink('requirement', 'ajaxGetProductCode', "app=" + app +"&data_id="+idstr), function(planList)
            {
                $('#product' + idstr + '_chosen').remove();
                $('#product' + idstr).parent().addClass('productParent' + idstr);
                $('#product' + idstr).replaceWith(planList);
                $('.productParent' + idstr).find('#product').attr({'id':'product' + idstr, 'name':'product' + idstr});
                $('.productParent' + idstr).find('.text-danger').remove();

                $('#product' + idstr).chosen();

    });
    $.get(createLink('requirement', 'ajaxGetProductPlan', "productID=" + 0), function(planList)
    {
        $('#productPlan' + idstr + '_chosen').remove();
        $('#productPlan' + idstr).parent().addClass('productPlanParent' + idstr);
        $('#productPlan' + idstr).replaceWith(planList);
        $('.productPlanParent' + idstr).find('#productPlan').attr({'id':'productPlan' + idstr, 'name':'productPlan' + idstr});
        $('.productPlanParent' + idstr).parent().parent().parent().find('.text-danger').remove();

                $('#productPlan' + idstr).val('');
                $('#productPlan' + idstr).chosen();

            });
            $.get(createLink('demand', 'ajaxGetFixType'), function(data)
            {
                $('#fixType' + idstr + '_chosen').remove();
                $('#fixType' + idstr).parent().addClass('fixTypeParent' + idstr);
                $('#fixType' + idstr).replaceWith(data);
                $('.fixTypeParent' + idstr).find('#fixType').attr({'id':'fixType' + idstr, 'name':'fixType' + idstr});
                $('.fixTypeParent' + idstr).find('.text-danger').remove();

                $('#fixType' + idstr).val('');
                $('#fixType' + idstr).chosen();
            });
            $.get(createLink('demand', 'ajaxGetSecondLine', "fixType="+'project' + "&app=" + null + "&sub=" + '1'), function(data)
            {
                $('#project' + idstr + '_chosen').remove();
                $('#project' + idstr).parent().addClass('projectParent' + idstr);
                $('#project' + idstr).replaceWith(data);
                $('.projectParent' + idstr).find('#project').attr({'id':'project' + idstr, 'name':'project' + idstr});
                $('.projectParent' + idstr).parent().find('.text-danger').remove();

                $('#project' + idstr).val(0);
                $('#project' + idstr).chosen();
            });
            loadProductExecutions(0, null, null, idstr);
            // }
        }

        function loadProductExecutions(projectID = 0,fixtype = null,app = null, idstr = '')
        {
            var execution = $('#execution' + idstr).val();
            if(typeof(bugExecutionID) !== 'undefined') var executionID = bugExecutionID;
            var link = createLink('problem', 'ajaxGetExecutionSelect', 'projectID=' + projectID + '&executionID=' + executionID + '&fixtype=' + fixtype + '&app=' + app);
            $.ajaxSettings.async = false;
            $.post(link, function(data)
            {
                $('#execution' + idstr).parent().addClass('executionParent' + idstr);
                $('#execution' + idstr).replaceWith(data);
                $('.executionParent' + idstr).find('#execution').attr({'id':'execution' + idstr, 'name':'execution' + idstr});
                $('.executionParent' + idstr).find('.text-danger').remove();

                execution = $('#execution' + idstr).val();
                if(execution && $('#execution' + idstr).attr("notype") != '1'){
                    $('#execution' + idstr).val(execution);
                    $('#executionid' + idstr).val(execution);
                }else{
                    $('#executionid' + idstr).val( $('#execution' + idstr).val());
                }
                $('#execution' + idstr + '_chosen').remove();
                if($('#execution' + idstr).attr("notype") == '2'){
                    $('#execution' + idstr).chosen();
                }
                // $('#execution').chosen();
            })
            $.ajaxSettings.async = true;
            var fixtype = $('#fixType' + idstr).val();
            if($('#execution' + idstr).attr("notype") == '1' && fixtype == 'second'){
                $('#execution' + idstr).parent().removeClass('required');
                $('#flag' + idstr).val('1');
            }else {
                $('#flag' + idstr).val('2');
            }
            if($('#execution' + idstr).attr("app") != '' && fixtype == 'second'){
                $('#application' + idstr).val($('#execution' + idstr).attr("app"));
            }
            if((fixtype == 'second' && execution) || $('#fixType' + idstr).val() == 'second'){
                $('#execution' + idstr).prop('disabled', true).trigger("chosen:updated");
            }else{
                $('#execution' + idstr).prop('disabled', false).trigger("chosen:updated");
            }
        }

        function selectfix(id, value){
            var idstr = id.slice(7)
            var fixType = $('#fixType' + idstr).val();
            var app = $('#app' + idstr).val();
            $.get(createLink('demand', 'ajaxGetSecondLine', "fixType=" + fixType + "&app=" + app + "&sub=" + '1'), function(data)
            {
                $('#project' + idstr + '_chosen').remove();
                $('#project' + idstr).parent().addClass('projectParent' + idstr);
                $('#project' + idstr).replaceWith(data);
                $('.projectParent' + idstr).find('#project').attr({'id':'project' + idstr, 'name':'project' + idstr});
                $('.projectParent' + idstr).parent().parent().parent().find('.text-danger').remove();
                $('#project' + idstr).val(0);
                $('#project' + idstr).chosen();
                // loadProductExecutions('0', null, null, idstr);//联动重置阶段
            });
            if(fixType == ''){
                loadProductExecutions('0', null, null, idstr);//联动重置阶段
            }
        }

        function selectproject(id, value){
            var idstr = id.slice(7);
            var fixType = $('#fixType' + idstr).val();
            var app = $('#app' + idstr).val();
            var project = $('#project' + idstr).val();
            loadProductExecutions(project, fixType, app, idstr);
        }

    </script>
<?php include '../../common/view/footer.html.php';?>