<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 400px;">
  <div class="center-block">
    <div class="main-header">
      <h2>确认立项</h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
            <tbody>
            <!--<tr>
                <th>需求意向</th>
                <td style="width: 573px" class="required"><?php /*echo html::select('opinion[]', $opinions, '', "id='opinion' data-id='0' class='form-control chosen' multiple onchange='setRequirement(this)'");*/?></td>
            </tr>

            <tr>
                <th>需求任务</th>
                <td ><?php /*echo html::select('requirement[]', [], '', "id='requirement' data-id='0' class='form-control chosen' multiple onchange='setDemand(this)'");*/?></td>
            </tr>
            <tr>
                <th>需求条目</th>
                <td><?php /*echo html::select('demand[]', [], '', "id='demand' data-id='0' class='form-control chosen' multiple ");*/?></td>
            </tr>-->
            <tr>
                <th></th>
                <td class="red" style="width: 573px" >将创建项目工作区（项目管理视图下），并请进行项目初始化（如添加团队成员、白名单等），点击保存按钮完成</td>
            </tr>
            <tr>
                <th>操作备注</th>
                <td  style="width: 573px"><?php echo html::textarea('comment', "", "class='form-control'");?></td>
            </tr>
            <tr>
                <td class='form-actions text-center ' colspan='5'><?php echo html::submitButton('','onclick = "return confirmsubmit()"');?></td>
            </tr>
            </tbody>
        </table>
    </form>
  </div>
</div>
<script>
var productIndex = 0;
function confirmsubmit(){
    return true;
   /* if(confirm("将创建项目工作区（项目管理试图下），并请进行项目初始化（如添加团队成员、白名单等），点击保存按钮完成",'确认窗口')){
        return true;
    }else{
        return false;
    }*/
}

/*function setDemand(obj)
{

    var demandId = $(obj).val();

    $.get(createLink('demand', 'ajaxGetDemand', "demandId=" + demandId), function(data)
    {
        $('#demand_chosen').remove();
        $('#demand' ).replaceWith(data);
        $('#demand' ).val('');
        $('#demand' ).chosen();
    });
}
function setRequirement(obj)
{
    var opinionId = $(obj).val();

    $.get(createLink('demand', 'ajaxGetSelectRequirement', "opinionId=" + opinionId), function(data)
    {
        $('#requirement_chosen').remove();
        $('#requirement'  ).replaceWith(data);
        $('#requirement'  ).val('');
        $('#requirement'  ).chosen();
    });
}*/
</script>
<?php include '../../../common/view/footer.html.php';?>