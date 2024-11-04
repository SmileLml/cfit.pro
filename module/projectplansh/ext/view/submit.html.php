<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 350px;">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->projectplan->submit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-120px'><?php echo $lang->projectplan->submitNotice;?></th>
            <td colspan='3' class="red">
                <?php echo $lang->projectplan->submitNoticeContent;?>
            </td>
          </tr>
          <tr>
              <th class='w-120px'><?php echo $lang->projectplan->submitBy . '/' . $lang->projectplan->dept;?></th>
              <td colspan='3'>
                  <?php echo rtrim($this->app->user->realname . ' / ' . trim(zget($ownerDepts, $this->app->user->dept, $lang->noData),'/'), '/');?>
              </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->depts;?></th>
            <td colspan='3'>
            <?php
            foreach($depts as $dept)
            {
                /*$isChecked = $dept->id == $this->app->user->dept ? 'checked' : '';*/
                if(in_array($dept->id,$filterDepts)){
                    continue;
                }
                $isChecked = '';
//
                if(!$isShangHai && ($dept->name == '质量部' || $dept->name == '系统部'|| $dept->name == '测试部' ||  $dept->name == '平台架构部' || $dept->name == '产品创新部' || $dept->name == zget($deptPairs, zget($deptParent,$this->app->user->dept), $lang->noData))){
                    $isChecked = 'checked onclick ="return false"';
                }
                echo "<div class='checkbox-primary'><input type='checkbox' name='depts[]' value='$dept->id' $isChecked>";
                echo "<label>$dept->name<span class='review-name'>" . zget($users, $dept->manager1, '') . "</span></label>";
                echo "</div>";
            }
            ?>
            </td>
          </tr>
          <tr>
              <th></th>
              <td colspan="3" class="red">请勾选如下选项，若尚未同步需求任务请选择“否”之后直接保存；若已同步需求任务或内部创建请选择“是”并按实际情况关联对应的需求任务（支持多选）（备注：CBP项目需求任务由清总同步）</td>
          </tr>
          <tr>
              <th>是否同步需求任务</th>
              <td colspan="3" id="issyncjobtd"><?php echo html::radio('issyncjob', $lang->projectplan->issyncjob, $issyncjob);?></td>
          </tr>
          <tr id="wenanvisual" <?php if($issyncjob==0){ echo "style='display:none'"; } ?> >
              <th></th>
              <td colspan="3" class="red">请关联正确的需求池需求任务，后续将影响该需求任务下的需求条目（项目实现）项目的可选范围！</td>
          </tr>

          <tr id="requirementvisual" <?php if($issyncjob==0){ echo "style='display:none'"; } ?>>
              <th>需求任务</th>
              <td colspan='3' class="required"><?php echo html::select('requirement[]', $requirement, $requirementvalue, "id='requirement' data-id='0' class='form-control chosen' multiple onchange='setOpinion(this)'");?></td>
          </tr>
          <tr id="opinioninputvisual" <?php if($issyncjob==0){ echo "style='display:none'"; } ?>>
              <th>需求意向</th>
              <td colspan='3' style="width: 573px"  class="required"><input type="text" name="" value="" id="opinioninput" class="form-control" readonly="readonly" disable="disable" autocomplete="off"></td>
          </tr>
          <tr style="display:none">
              <th></th>
              <td colspan="3" id="opinionhidden"></td>
          </tr>



          <!--<tr>
              <th>需求条目</th>
              <td colspan='3'><?php /*echo html::select('demand[]', [], '', "id='demand' data-id='0' class='form-control chosen' multiple ");*/?></td>
          </tr>-->
          <tr>
            <td style="padding-top: 50px;" class='form-actions text-center' colspan='4'><?php echo html::submitButton('','onclick = "return confirmsubmit()"');?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script >
    function confirmsubmit(){
        var issyncjobval = $("#issyncjobtd input[name=issyncjob]:checked").val();
        if(issyncjobval != 0){
           var requirement = $("#requirement").val();

           if(!requirement){
               $.zui.messager.danger("需求任务必选！");
               return false;
           }
        }

        if(confirm("请再次确认需要会签的部门（需要有资源投入项目组的其他部门，本部门无需勾选会签）",'确认窗口')){
            return true;
        }else{
            return false;
        }
    }
$("#issyncjob1").click(function (){

   $("#wenanvisual,#requirementvisual,#opinioninputvisual").show();
})
    $("#issyncjob0").click(function (){

        $("#wenanvisual,#requirementvisual,#opinioninputvisual").hide();
    })
    function setDemand(obj)
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

    function setRequirementOload(obj)
    {

        var opinionId = '<?php echo implode(",",$requirementvalue); ?>';

        $.get(createLink('demand', 'ajaxGetSelectOpinion', "opinionId=" + opinionId), function(data)
        {


            var tempval = <?php echo json_encode($requirementvalue) ?>;
            data = JSON.parse(data);

            var opinionhtml= '';
            if(data){
                for ( opval in data) {
                    $('#opinioninput' ).val(data[opval]);
                    // console.log(opval)

                    opinionhtml += "<input name='opinion[]' value='"+opval+"'></input>";
                }


                $("#opinionhidden").html(opinionhtml)
            }else{
                $('#opinioninput' ).val();
                $("#opinionhidden").html()
            }



        });
    }
    setRequirementOload($("#opinion"));
    function setRequirement(obj)
    {
        var requirementId = $(obj).val();

        $.get(createLink('demand', 'ajaxGetSelectRequirement', "requirementId=" + requirementId), function(data)
        {
            $('#requirement_chosen').remove();
            $('#requirement'  ).replaceWith(data);
            $('#requirement'  ).val('');
            $('#requirement'  ).chosen();
        });
    }
    function setOpinion(obj)
    {
        var requirementId = $(obj).val();

        $.get(createLink('demand', 'ajaxGetSelectOpinion', "requirementId=" + requirementId), function(data)
        {


            data = JSON.parse(data);

            var opinionhtml= '';
            if(data){
                var tempval = ''
                for ( opval in data) {
                    tempval += data[opval]+'  ';

                    opinionhtml += "<input name='opinion[]' value='"+opval+"'></input>";
                    // console.log(opval)
                }
                $('#opinioninput' ).val(tempval);

                $("#opinionhidden").html(opinionhtml)
                // $("#issyncjobtd input[name=issyncjob]").removeAttr("checked")
            }else{
                // $("#issyncjobtd input[name=issyncjob]").attr("checked")
                $('#opinioninput' ).val();
                $("#opinionhidden").html()
            }

        });
    }

</script>
<style>
.review-name {padding-left: 10px; color: #585858;}
</style>
<?php include '../../../common/view/footer.html.php';?>
