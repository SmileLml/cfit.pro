<?php

?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
    <div class="main-header">
        <h2><?php echo $lang->workreport->supplement;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' >

       <!-- <table class='table table-fixed has-sort-head table-nested disable-empty-nest-row with-footer-fixed ' style='margin-bottom:20px;border:1px solid #ddd;' id="taskTable">-->
        <table class='table table-form table-header-fixed ' style='margin-bottom:20px;border:1px solid #ddd;overflow:hidden;overflow: auto;' id="taskTable">
             <thead>
                 <tr>
                     <span style="color: red;"><?php echo $this->lang->workreport->workTip  ?></span>
                     <th class="w-200px"><?php echo $this->lang->workreport->projectSpace ;?></th>
                     <th class="w-200px"><?php echo $this->lang->workreport->activity ;?></th>
                     <th class="w-200px"><?php echo $this->lang->workreport->stage ;?></th>
                     <th class="w-200px"><?php echo $this->lang->workreport->object ;?></th>
                     <th class="w-100px"><?php echo $this->lang->workreport->beginDate ;?></th>
                     <!--<th><?php /*echo $this->lang->workreport->beginDate ;*/?></th>
                     <th><?php /*echo $this->lang->workreport->endDate ;*/?></th>-->
                     <th class="w-70px"><?php echo $this->lang->workreport->consumed ;?></th>
                     <th class="w-100px"><?php echo $this->lang->workreport->workType ;?></th>
                     <th class="w-120px"><?php echo $this->lang->workreport->workContent ;?></th>
                     <th class="w-100px" ></th>
                 </tr>
             </thead>
             <tbody>
             <?php for($i = 1; $i <= 5; $i++):?>
                 <tr id="workTab<?php echo $i;?>" >
                     <?php echo html::hidden("id[$i]", $i);?>
                     <td><?php echo html::select("project[$i]", $projects, '', "class='form-control chosen projectSelect' data-id = '$i' onchange ='getActivity(this)' ");?></td>
                     <td><?php echo html::select("activity[$i]", '', '', "class='form-control chosen activitySelect' data-id = '$i' onchange ='getApps(this)' ");?></td>
                     <td><?php echo html::select("apps[$i]", '', '', "class='form-control chosen appsSelect' data-id = '$i' onchange ='getTasks(this)' ");?></td>
                     <td><?php echo html::select("objects[$i]", '', '', "class='form-control chosen objectsSelect' data-id = '$i' ");?></td>
                     <td><?php echo html::input("beginDate[$i]", '', "class='form-control form-date beginDateSelect' data-id = '$i' readonly");?></td>
                    <!-- <td><?php /*echo html::input("endDate[$i]", '', "class='form-control form-date endDateSelect' data-id = '$i' onchange ='checkEndDate(this)' ");*/?></td>-->
                     <td><?php echo html::input("consumed[$i]", '', "class='form-control consumedSelect' data-id = '$i'  ");?></td>
                     <td><?php echo html::select("workType[$i]", $workType, '', "class='form-control  picker-select workTypeSelect' data-id = '$i' ");?></td>
                     <td><?php echo html::textarea("workContent[$i]", '', "class='form-control workContentSelect' ' rows='1' data-id = '$i'") ;?></td>
                     <td>
                         <div class='table-row' style="width:120px">

                             <div class='table-col' >
                                 <div class='input-group'>
                                     <span class="input-group-btn addStage " onclick="addTaskItem(this)" data-id='<?php echo $i;?>' id="codePlus<?php echo $i;?>"> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                                     <?php if($i > 1):?>
                                         <span class=" delStage " onclick="delTaskItem(this)" data-id='<?php echo $i;?>' id='codeClose<?php echo $i;?>'> <span class="btn addItem"><i class="icon-close" title=""></i></span></span>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                     </td>
                 </tr>
             <?php endfor;?>
             <tr>
                 <td class='form-actions text-center' colspan='9'><?php echo html::submitButton('','','btn btn-primary') . html::closeModalButton('取消');?>
                     <div class='text-left' style="color: red"><span> <?php echo $this->lang->workreport->tips?></span></div>
                 </td>

             </tr>
             </tbody>
        </table>

    </form>
</div>
<table class="hidden">
    <tbody  id="workTable">
    <tr id='workTab0' class="workTab" >
        <?php echo html::hidden("id[]", '','class= "idSelect" data-id=""');?>
        <td><?php echo html::select("project[]", $projects, '', "class='form-control chosen projectSelect' data-id = '' id= 'project0' onchange ='getActivity(this)' ");?></td>
        <td><?php echo html::select("activity[]", '', '', "class='form-control chosen activitySelect' data-id = '' id= 'activity0' onchange ='getApps(this)' ");?></td>
        <td><?php echo html::select("apps[]", '', '', "class='form-control chosen appsSelect' data-id = '' id= 'apps0' onchange ='getTasks(this)' ");?></td>
        <td><?php echo html::select("objects[]", '', '', "class='form-control chosen objectsSelect' data-id = '' id= 'objects0' ");?></td>
        <td><?php echo html::input("beginDate[]", '', "class='form-control form-date beginDateSelect' data-id = '' readonly");?></td>
      <!--  <td><?php /*echo html::input("endDate[]", '', "class='form-control form-date endDateSelect' data-id = '' onchange ='checkEndDate(this)' ");*/?></td>-->
        <td><?php echo html::input("consumed[]", '', "class='form-control consumedSelect' data-id = ''  ");?></td>
        <td><?php echo html::select("workType[]", $workType, '', "class='form-control  picker-select workTypeSelect' data-id = '' id= 'workType0' ");?></td>
        <td><?php echo html::textarea('workContent[]', '', "class='form-control workContentSelect' ' rows='1' data-id = '' ") ;?></td>
        <td>
            <div class='table-row' style="width:120px">

                <div class='table-col' >
                    <div class='input-group'>
                        <span class="input-group-btn addStage " onclick="addTaskItem(this)" data-id='' id='codePlus0'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                        <span class=" delStage " onclick="delTaskItem(this)" data-id='' id='codeClose0'> <span class="btn addItem"><i class="icon-close" title=""></i></span></span>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
<?php
$w = date('w',strtotime(date(DT_DATE1)));
$dn = $w ? $w - 1 : 6;
js::set('start',date('Y-m-d',strtotime('-'.$dn."day",strtotime(date(DT_DATE1)))));
/*js::set('list',count($list));*/
js::set('end',date(DT_DATE1));
?>

<script>
    $(function() {
         $(".form-date").datetimepicker('setStartDate', start);
         $(".form-date").datetimepicker('setEndDate', '<?php echo date(DT_DATE1)?>');

    })

</script>
<?php include '../../common/view/footer.modal.html.php';?>

