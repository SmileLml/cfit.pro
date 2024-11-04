<?php include '../../../common/view/header.html.php';?>
<div id="mainContent" class="main-content" style="display:inline-block;">
    <p style="color:red"><?php echo $lang->my->notice;?></p>
    <?php if($app->user->account == 'admin'): ?>
        <table class="table table-data">
            <tbody>
                <tr>
                    <th class='text-center' style="font-weight:bolder"><?php echo $lang->my->authorizer;?></th>
                    <td colspan='2'><?php echo html::select('authorizer', $users, empty($authorizer)?'':$authorizer, "class='form-control chosen'");?></td>
                    <td colspan='8'><button class="btn" onclick="searchAuthorization()"><span class="text"><?php echo $lang->searchAB; ?></span></button></td>
                </tr>
            </tbody>
        </table>
    <?php endif;?>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
        <table class="table table-form">

            <thead>
                <tr>
                    <th class='text-center w-60px'><?php echo $lang->my->num;?></th>
                    <th class='text-center w-45px'><?php echo $lang->my->enabled;?></th>
                    <th class='text-center w-45px'><?php echo $lang->my->permanently;?></th>
                    <th class='text-center w-100px'><?php echo $lang->my->startTime;?></th>
                    <th class='text-center w-100px'><?php echo $lang->my->endTime;?></th>
                    <th class='text-center w-300px'><?php echo $lang->my->authorizedPerson;?></th>
                    <th class='text-center w-300px'><?php echo $lang->my->objectType;?></th>
                    <?php if($app->user->account == 'admin'): ?>
                        <th class='text-center w-40px'><?php echo $lang->my->authorizer;?></th>
                    <?php endif;?>
                    <th class='text-center w-50px'><?php echo $lang->my->operate;?></th>
                </tr>
            </thead>
            <tbody id="aid">
            <?php if(empty($authorizationArray)):?>
            <tr>
                <td class="hidden" class='text-center' id = 'codeTd'><?php echo html::input('code[1]', 1, "class='form-control' ");?></td>
                <td class='text-center' id = 'numTd'><?php echo 1;?></td>
                <td class='text-center' id = 'checkChosenTd'><?php echo html::checkbox('enabled[1]',["2" => ''],'',"");?></td>
                <td id='permanentlyTd'><?php echo html::checkbox('permanently[1]',["2" => ''],'',"onclick='permanentlyChange(this.checked,this.name,this)'");?></td>
                <td class='text-center' id = 'startTimeTd'><?php echo html::input('startTime[1]', '', "class='form-control form-date beginDateSelect' readonly='readonly' onclick='changeDate()'");?></td>
                <td class='text-center' id = 'endTimeTd'><?php echo html::input('endTime[1]', '', "class='form-control form-date beginDateSelect' readonly='readonly' onclick='changeDate()'");?></td>
                <td id='authorizedPersonTd'><?php echo html::select('authorizedPerson[1][]', $users, '', "class='form-control chosen' multiple");?></td>
                <td class='text-center' id='objectTypeTd'><?php echo html::select('objectType[1][]', $lang->my->objectTypeList , '', "class='form-control chosen' multiple");?></td>
                <?php if($app->user->account == 'admin'): ?>
                    <td class='authorizerText text-center'"><?php echo zget($users, $authorizer, '');?></td>
                <?php endif;?>
                <td>
                    <a class="input-group-btn" href="javascript:void(0)" onclick="addRelate(this)" data-id='1' id='addRelateItem1' class="btn btn-link"><i class="icon-plus"></i></a>
                    <a class="input-group-btn" href="javascript:void(0)" onclick="delRelate(this)" class="btn btn-link"><i class="icon-close"></i></a>
                </td>
            </tr>
            <?php else:?>

                <?php foreach ($authorizationArray as $authorizationObject): ?>
                <tr>
                    <td class='text-center' id = 'numTd'><?php echo $authorizationObject->id;?></td>
                    <td class="hidden" class='text-center' id = 'codeTd'><?php echo html::input('code['.$authorizationObject->num.']', $authorizationObject->num, "class='form-control' ");?></td>
                    <td class='text-center' id = 'checkChosenTd'><?php echo html::checkbox('enabled['.$authorizationObject->num.']',["2" => ''],$authorizationObject->enabled,"");?></td>
                    <td id='permanentlyTd'><?php echo html::checkbox('permanently['.$authorizationObject->num.']',["2" => ''],$authorizationObject->permanently,"onclick='permanentlyChange(this.checked,this.name,this)'");?></td>
                    <td class='text-center' id = 'startTimeTd'><?php echo $authorizationObject->permanently=='2' ? html::input('startTime['.$authorizationObject->num.']', '', "class='form-control form-date beginDateSelect' readonly='readonly' disabled") : html::input('startTime['.$authorizationObject->num.']', $authorizationObject->startTime == '-0001-11-30'?'':$authorizationObject->startTime, "class='form-control form-date' readonly='readonly' onclick='changeDate()'");?></td>
                    <td class='text-center' id = 'endTimeTd'><?php echo $authorizationObject->permanently=='2' ? html::input('endTime['.$authorizationObject->num.']', '', "class='form-control form-date beginDateSelect' readonly='readonly' disabled") :html::input('endTime['.$authorizationObject->num.']', $authorizationObject->endTime == '-0001-11-30'?'':$authorizationObject->endTime, "class='form-control form-date' readonly='readonly' onclick='changeDate()'");?></td>
                    <td id='authorizedPersonTd'><?php echo html::select('authorizedPerson['.$authorizationObject->num.'][]', $users, $authorizationObject->authorizedPerson, "class='form-control chosen' multiple");?></td>
                    <td class='text-center' id='objectTypeTd'><?php echo html::select('objectType['.$authorizationObject->num.'][]', $lang->my->objectTypeList , $authorizationObject->objectType, "class='form-control chosen' multiple");?></td>
                    <?php if($app->user->account == 'admin'): ?>
                        <td class='authorizerText text-center'"><?php echo zget($users, $authorizer, '');?></td>
                    <?php endif;?>
                    <td>
                        <a class="input-group-btn" href="javascript:void(0)" onclick="addRelate(this)" data-id='<?php echo $authorizationObject->num; ?>' id='addRelateItem<?php echo $authorizationObject->num; ?>' class="btn btn-link"><i class="icon-plus"></i></a>
                        <a class="input-group-btn" href="javascript:void(0)" onclick="delRelate(this)" class="btn btn-link"><i class="icon-close"></i></a>
                    </td>
                    <td class="hidden" id='idTd'><?php echo html::input('id['.$authorizationObject->num.']', $authorizationObject->id, "class='form-control' ");?></td>
                </tr>
                <?php endforeach;?>
            <?php endif;?>
            <tr>
                <td class="hidden"><?php echo html::input('authorizerAccount', $account, "class='form-control' ");?></td>
                <td class='form-actions text-center' colspan='8'><?php echo html::submitButton();?></td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<table class="hidden">
    <tbody id="lineDemo">
    <tr>
        <td class='text-center' id = 'numTd'></td>
        <td class="hidden" class='text-center' id = 'codeTd'><?php echo html::input('code[]', '', "class='form-control' ");?></td>
        <td class='text-center' id = 'checkChosenTd'><?php echo html::checkbox('enabled[]',["2" => ''],'',"");?></td>
        <td id='permanentlyTd'><?php echo html::checkbox('permanently[]',["2" => ''],'',"onclick='permanentlyChange(this.checked,this.name,this)'");?></td>
        <td class='text-center' id = 'startTimeTd'><?php echo html::input('startTime[]', '', "class='form-control form-date beginDateSelect' readonly='readonly' onclick='changeDate()'");?></td>
        <td class='text-center' id = 'endTimeTd'><?php echo html::input('endTime[]', '', "class='form-control form-date beginDateSelect' readonly='readonly' onclick='changeDate()'");?></td>
        <td id='authorizedPersonTd'><?php echo html::select('authorizedPerson[][]', $users, '', "class='form-control chosen' multiple");?></td>
        <td class='text-center' id='objectTypeTd'><?php echo html::select('objectType[][]', $lang->my->objectTypeList , '', "class='form-control chosen' multiple");?></td>
        <?php if($app->user->account == 'admin'): ?>
            <td class='authorizerText text-center'"><?php echo zget($users, $authorizer, '');?></td>
        <?php endif;?>
        <td>
            <a class="input-group-btn" href="javascript:void(0)" onclick="addRelate(this)" class="btn btn-link"><i class="icon-plus"></i></a>
            <a class="input-group-btn" href="javascript:void(0)" onclick="delRelate(this)" class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>
<div id="notice" class="hidden" style="text-align: center;"><span style="font-size: 28px;text-align:center;">暂无</span></div>
<div class="cell" id = "action"><?php include '../../../common/view/action.html.php';?></div>
<?php js::set('objectTypeList', $lang->my->objectTypeList); ?>
<?php js::set('userAccount', $app->user->account); ?>
<?php js::set('authorizerCode', $authorizer); ?>
<?php js::set('maxNum', $maxNum); ?>
<script>
    $(function() {
        var curDate = new Date();
        var nextDate = new Date(curDate.getTime() + 24*60*60*1000); //后一天
        $(".form-date").datetimepicker('setStartDate', nextDate);
        $(".day").removeClass('today');
        $(".today").addClass('hidden');
        var newData = new Array();
    });
    $(document).ready(function() {
        if(userAccount == 'admin' && authorizerCode == ''){
            $('#dataform').addClass('hidden');
            $('#action').addClass('hidden');
            $('#notice').removeClass('hidden');
        }
    });

    function changeDate(){
        $(".day").removeClass('today');
        $(".today").addClass('hidden');
    }

    function permanentlyChange(checked, name, obj){
        if(checked == true){
            $(obj).parent().parent().nextAll('#startTimeTd').children().attr("disabled", true);
            $(obj).parent().parent().nextAll('#startTimeTd').children().val('');
            $(obj).parent().parent().nextAll('#endTimeTd').children().attr("disabled", true);
            $(obj).parent().parent().nextAll('#endTimeTd').children().val('');
        }else{
            $(obj).parent().parent().nextAll('#startTimeTd').children().attr("disabled", false);
            $(obj).parent().parent().nextAll('#endTimeTd').children().attr("disabled", false);
        }
    }

    function addRelate(obj)
    {
        $(obj).parent().parent().after($('#lineDemo').children(':first-child').clone());

        $(obj).parent().parent().next().find('#authorizedPersonTd').find('.picker').remove();
        $(obj).parent().parent().next().find('#authorizedPersonTd').find('#authorizedPerson_chosen').remove();
        $(obj).parent().parent().next().find('#authorizedPerson').attr('class','form-control chosen');
        $(obj).parent().parent().next().find('#authorizedPerson').chosen();
        $(obj).parent().parent().next().find('#authorizedPerson').val('').trigger("chosen:updated");

        $(obj).parent().parent().next().find('#objectTypeTd').find('#objectType_chosen').remove();
        $(obj).parent().parent().next().find('#objectType').attr('class','form-control chosen');
        $(obj).parent().parent().next().find('#objectType').chosen();
        $(obj).parent().parent().next().find('#objectType').val('').trigger("chosen:updated");

        $(obj).parent().parent().next().find('#startTimeTd').children().val('').datepicker();
        $(obj).parent().parent().next().find('#endTimeTd').children().val('').datepicker();

        maxNum = maxNum+1;
        $(obj).parent().parent().next().find('#numTd').html(maxNum);
        $(obj).parent().parent().next().find('#codeTd').children().attr('name','code['+maxNum+']');
        $(obj).parent().parent().next().find('#codeTd').children().attr('value', maxNum);


        sortline();
        var curDate = new Date();
        var nextDate = new Date(curDate.getTime() + 24*60*60*1000); //后一天
        $(".form-date").datetimepicker('setStartDate', nextDate);

        $(".day").removeClass('today');
        $(".today").addClass('hidden');
    }

    function delRelate(obj)
    {
        if($(obj).parent().parent().parent().children().length>2){
            $(obj).parent().parent().remove()
        }else{
            $(obj).parent().parent().find('#checkChosenTd').children().children().prop("checked", false);
            $(obj).parent().parent().find('#permanentlyTd').children().children().prop("checked", false);
            $(obj).parent().parent().find('#authorizedPersonTd').children().val('').trigger("chosen:updated");
            $(obj).parent().parent().find('#objectTypeTd').children().val('').trigger("chosen:updated");


            $(obj).parent().parent().find('#startTimeTd').children().val('').datepicker();
            $(obj).parent().parent().find('#endTimeTd').children().val('').datepicker();
        }
        sortline();
    }

    function sortline()
    {
        $('#aid').children('tr').each(function (index){
            var num = $(this).find('#codeTd').children().val();
            $(this).find('#checkChosenTd').children().children().attr('name','enabled['+num+']');
            $(this).find('#startTimeTd').children().attr('name','startTime['+num+']');
            $(this).find('#endTimeTd').children().attr('name','endTime['+num+']');
            $(this).find('#permanentlyTd').children().children().attr('name','permanently['+num+']');
            $(this).find('#authorizedPersonTd').children().attr('name','authorizedPerson['+num+'][]');
            $(this).find('#objectTypeTd').children().attr('name','objectType['+num+'][]');
            $(this).find('#idTd').children().attr('name','id['+num+']');
        })
    }

    function searchAuthorization(){
        var authorizer = $('#authorizer').val();
        window.location.href = createLink('my', 'authorization', 'authorizer=' + authorizer);

        /*$('#dataform').removeClass('hidden');
        $('#action').removeClass('hidden');
        $('#notice').addClass('hidden');
        var authorizer = $('#authorizer').val();
        var label = $('#authorizer').find("option:selected").text();
        $('.authorizerText').text(label);
        $('#authorizerAccount').val(authorizer);
        if(authorizer == ''){
            $('#dataform').addClass('hidden');
            $('#action').addClass('hidden');
            $('#notice').removeClass('hidden');
        }else{
            $.get(createLink('my', 'ajaxGetAuthorizer', "authorizer=" + authorizer), function(data){
                if(data == undefined || data == '' || data == '[]'){
                    for(var key in objectTypeList){
                        $('#'+key).children().each(function (){
                            if($(this)[0].id == 'authorizedPerson'){
                                $(this).children().each(function (){
                                    $(this).val('').trigger("chosen:updated");
                                });
                            }
                            if($(this)[0].id == 'startTime'){
                                $(this).children().each(function (){
                                    $(this).val('');
                                });
                            }
                            if($(this)[0].id == 'endTime'){
                                $(this).children().each(function (){
                                    $(this).val('');
                                });
                            }
                            if($(this)[0].id == 'permanently'){
                                $(this).children().each(function (){
                                    $(this).find("input[type='checkbox']").prop("checked",false);
                                });
                                permanentlyChange($(this).find("input[type='checkbox']:checked").val() == 2 ? true : false, $(this).find("input[type='checkbox']").prop("name"));
                            }

                            if($(this)[0].id == 'checkChosen'){
                                $(this).children().each(function (){
                                    $(this).find("input[type='checkbox']").prop("checked",false);
                                });
                            }
                        });
                    }
                }else{
                    var array = JSON.parse(data);
                    for(var key in objectTypeList){
                        for(var obj of array){
                            if(key == obj.objectType){
                                $('#'+key).children().each(function (){
                                    if($(this)[0].id == 'authorizedPerson'){
                                        $(this).children().each(function (){
                                            $(this).val(obj.authorizedPerson.split(',')).trigger("chosen:updated");;
                                        });
                                    }
                                    if($(this)[0].id == 'startTime'){
                                        $(this).children().each(function (){
                                            $(this).val(obj.startTime == '-0001-11-30'?'':obj.startTime);
                                        });
                                    }
                                    if($(this)[0].id == 'endTime'){
                                        $(this).children().each(function (){
                                            $(this).val(obj.endTime == '-0001-11-30'?'':obj.endTime);
                                        });
                                    }
                                    if($(this)[0].id == 'permanently'){
                                        $(this).children().each(function (){
                                            if(obj.permanently == '2'){
                                                $(this).find("input[type='checkbox']").prop("checked",true);
                                            }else{
                                                $(this).find("input[type='checkbox']").prop("checked",false);
                                            }
                                            permanentlyChange($(this).find("input[type='checkbox']:checked").val() == 2 ? true : false, $(this).find("input[type='checkbox']").prop("name"));
                                        });
                                    }

                                    if($(this)[0].id == 'checkChosen'){
                                        $(this).children().each(function (){
                                            if(obj.enabled == '2'){
                                                $(this).find("input[type='checkbox']").prop("checked",true);
                                            }else{
                                                $(this).find("input[type='checkbox']").prop("checked",false);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    }
                }
            });
            $.get(createLink('my', 'ajaxGetAction', "authorizer=" + authorizer), function(data){
                var actionList = JSON.parse(data);
                var html = '';
                var i = 1;
                $.each(actionList, function (key ,action){
                    html = '<li value="'+i+'"> '+action.date+'，由 <strong>'+action.actor+'</strong> 编辑 <div class="article-content comment"> <div class="comment-content">'
                        + action.comment + '</div></div></li>'+html;
                    i++;
                });
                $('.histories-list').html(html);
            });
        }*/
    }

</script>
<?php include '../../../common/view/footer.html.php';?>
