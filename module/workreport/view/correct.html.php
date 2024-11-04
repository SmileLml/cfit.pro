<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
 <div class="center-block">
    <div class="main-header">
        <h2>
            <span class='label label-id'><?php echo $workreport->id;?></span>
            <?php echo $lang->workreport->correct;?>
        </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' >

        <table class='table table-form  ' style='margin-bottom:20px;border:1px solid #ddd;overflow: auto;height:200px' id="taskTable">
             <thead>
                 <tr>
                     <span style="color: red;"><?php echo $this->lang->workreport->workTip  ?></span>
                     <th class="w-100px"><?php echo $this->lang->workreport->beginDate ;?></th>
                     <th class="w-200px"><?php echo $this->lang->workreport->projectSpace ;?></th>
                     <th class="w-200px"><?php echo $this->lang->workreport->activity ;?></th>
                     <th class="w-200px"><?php echo $this->lang->workreport->stage ;?></th>
                     <th class="w-300px"><?php echo $this->lang->workreport->object ;?></th>
                     <!--<th><?php /*echo $this->lang->workreport->beginDate ;*/?></th>
                         <th><?php /*echo $this->lang->workreport->endDate ;*/?></th>-->
                     <th class="w-70px"><?php echo $this->lang->workreport->consumed ;?></th>
                     <th class="w-100px"><?php echo $this->lang->workreport->workType ;?></th>
                     <th class="w-120px"><?php echo $this->lang->workreport->workContent ;?></th>

                 </tr>
             </thead>
             <tbody>
             <?php $key = 1;?>
                 <tr id="workTab<?php echo $key;?>" >
                     <?php echo html::hidden("id[$key]", $key);?>
                     <td><?php echo html::input("beginDate[$key]",   date('Y-m-d',strtotime($workreport->beginDate)), "class='form-control form-date beginDateSelect' data-id = '$key' readonly");?></td>
                     <td><?php echo html::select("project[$key]", $projects, $workreport->project, "class='form-control chosen projectSelect' data-id = '$key' onchange ='getActivity(this)' data-drop_direction='down'");?></td>
                     <td><?php echo html::select("activity[$key]", '' , '', "class='form-control chosen activitySelect' data-id = '$key' onchange ='getApps(this)' data-drop_direction='down'");?></td>
                     <td><?php echo html::select("apps[$key]", '', '', "class='form-control chosen appsSelect' data-id = '$key' onchange ='getTasks(this)' data-drop_direction='down'");?></td>
                     <td><?php echo html::select("objects[$key]", '', '', "class='form-control chosen objectsSelect' data-id = '$key' data-drop_direction='down'");?></td>

<!--                     <td><?php /*echo html::input("endDate[$key]",  date('Y-m-d',strtotime($workreport->endDate)), "class='form-control form-date endDateSelect' data-id = '$key' onchange ='checkEndDate(this)' ");*/?></td>
-->                  <td><?php echo html::input("consumed[$key]",  $workreport->consumed, "class='form-control consumedSelect' data-id = '$key' ");?></td>
                     <td><?php echo html::select("workType[$key]", $workType, $workreport->workType, "class='form-control  picker-select workTypeSelect' data-id = '$key' ");?></td>
                     <td><?php echo html::textarea("workContent[$key]", $workreport->workContent, "class='form-control workContentSelect' ' rows='1' data-id = '$key'") ;?></td>

                 </tr>

             <tr>
                 <td class='form-actions text-center' colspan='9'><?php echo html::submitButton('','','btn btn-primary') . html::closeModalButton('取消');?>
                     <div class='text-left' style="color: red"><span> <?php echo $this->lang->workreport->tips?></span></div>
                 </td>

             </tr>
             </tbody>
        </table>

    </form>
    <hr class='small' />
    <div class='main'><?php include '../../common/view/action.html.php';?></div>
</div>
</div>
<?php
$w = date('w',strtotime(date(DT_DATE1)));
$dn = $w ? $w - 1 : 6;
js::set('start',date('Y-m-d',strtotime('-'.$dn."day",strtotime(date(DT_DATE1)))));
js::set('end',date(DT_DATE1));

js::set('activity',$workreport->activity);
js::set('apps',$workreport->apps);
js::set('task',$workreport->objects);
js::set('taskName',$taskName->name);
?>

<script>
    $(function() {
         //$(".form-date").datetimepicker('setStartDate', start);
         $(".form-date").datetimepicker('setEndDate', '<?php echo date(DT_DATE1)?>');

    })
    $("form").submit(function(){
       var  m = 1;
        if(taskName.indexOf('已') >= 0){
           // $('#project'+m).prop('disabled', false).trigger("chosen:updated");
            $('#project'+m).removeAttr('disabled');
            $('#activity'+m).removeAttr('disabled');
            $('#apps'+m).removeAttr('disabled');
            $('#objects'+m).removeAttr('disabled');
        }
    })
</script>
<?php include '../../common/view/footer.modal.html.php';?>

