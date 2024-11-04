<?php include '../../common/view/header.html.php';?>
<?php if(isset($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->outsideplan->import;?></h2>
  </div>
  <p><?php echo sprintf($lang->file->importSummary, $allCount, html::input('maxImport', $config->file->maxImport, "style='width:50px'"), ceil($allCount / $config->file->maxImport));?></p>
  <p><?php echo html::commonButton($lang->import, "id='import'", 'btn btn-primary');?></p>
</div>
<script>
$(function()
{
    $('#maxImport').keyup(function()
    {
        if(parseInt($('#maxImport').val())) $('#times').html(Math.ceil(parseInt($('#allCount').html()) / parseInt($('#maxImport').val())));
    });
    $('#import').click(function(){location.href = createLink('outsideplan', 'showImport', "pageID=1&maxImport=" + $('#maxImport').val())})
});
</script>
<?php else:?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->outsideplan->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
            <th class='w-60px'><?php echo $lang->outsideplan->id?></th>
            <th class='w-120px'><?php echo $lang->outsideplan->year?></th>

            <th class='w-120px'><?php echo $lang->outsideplan->code;?></th>
            <th class='w-120px'><?php echo $lang->outsideplan->historyCode;?></th>
            <th class='w-300px'><?php echo $lang->outsideplan->name?></th>
            <th class='w-100px'><?php echo $lang->outsideplan->begin?></th>
            <th class='w-100px'><?php echo $lang->outsideplan->end?></th>
            <th class='w-100px'><?php echo $lang->outsideplan->workload?></th>
            <th class='w-100px'><?php echo $lang->outsideplan->duration?></th>
            <th class='w-120px'><?php echo $lang->outsideplan->status;?></th>
          <th class='w-120px'><?php echo $lang->outsideplan->maintainers;?></th>
          <th class='w-120px'><?php echo $lang->outsideplan->phone;?></th>
          <th class='w-120px'><?php echo $lang->outsideplan->content;?></th>
          <th class='w-250px'><?php echo $lang->outsideplan->milestone;?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->changes?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->apptype?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->projectinitplan?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->uatplanfinishtime?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->materialplanonlinetime?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->planonlinetime?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->projectisdelay?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->projectisdelaydesc?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->projectischange?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->projectischangedesc?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subProjectName;?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subTaskName?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subTaskBegin?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->subTaskEnd?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subProjectUnit?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subProjectBearDept?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subProjectDemandParty?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subTaskDemandContact?></th>
          <th class='w-100px'><?php echo $lang->outsideplan->subTaskDemandDeadline?></th>
          <th class='w-140px'><?php echo $lang->outsideplan->subTaskDesc?></th>
        </tr>

      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        ?>
        <?php foreach($outsideplanData as $key => $outsideplan):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($outsideplan->id))
            {
                echo $outsideplan->id . html::hidden("id[$key]", $outsideplan->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->outsideplan->new}</sub>";
            }
            ?>
          </td>
<!--            type,year,code,name,begin,end,workload,duration,status,maintainers,phone,content,milestone,changes,subProjectName,subTaskName,subTaskBegin,subTaskEnd,subProjectUnit,subProjectBearDept,subProjectDemandParty,subTaskDemandContact,subTaskDemandDeadline,subTaskDesc");-->

            <td><?php echo html::input("year[$key]", $outsideplan->year, "class='form-control'")?></td>
            <td><?php echo html::input("code[$key]", !empty($outsideplan->code) ? $outsideplan->code : ((!empty($outsideplan->id) and isset($outsideplans[$outsideplan->id])) ? $outsideplans[$outsideplan->id]->code : ''), "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("historyCode[$key]", !empty($outsideplan->historyCode) ? $outsideplan->historyCode : ((!empty($outsideplan->id) and isset($outsideplans[$outsideplan->id])) ? $outsideplans[$outsideplan->id]->historyCode : ''), "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("name[$key]", htmlspecialchars($outsideplan->name, ENT_QUOTES), "class='form-control'")?></td>
            <td><?php echo html::input("begin[$key]",  $outsideplan->begin, "class='form-control form-date' autocomplete='off'")?></td>
            <td><?php echo html::input("end[$key]", $outsideplan->end, "class='form-control form-date' autocomplete='off'")?></td>
            <td><?php echo html::input("workload[$key]", !empty($outsideplan->workload) ? $outsideplan->workload : ((!empty($outsideplan->id) and isset($outsideplans[$outsideplan->id])) ? $outsideplans[$outsideplan->id]->workload : ''), "class='form-control'")?></td>
            <td><?php echo html::input("duration[$key]", !empty($outsideplan->duration) ? $outsideplan->duration : ((!empty($outsideplan->id) and isset($outsideplans[$outsideplan->id])) ? $outsideplans[$outsideplan->id]->duration : ''), "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::select("status[$key]", $lang->outsideplan->statusList,  $outsideplan->status, "class='form-control chosen'")?></td>
            <td><?php echo html::select("maintainers[$key][]", $users, $outsideplan->maintainers, "class='form-control chosen' multiple")?></td>
            <td><?php echo html::input("phone[$key]", !empty($outsideplan->phone) ? $outsideplan->phone : ((!empty($outsideplan->id) and isset($outsideplans[$outsideplan->id])) ? $outsideplans[$outsideplan->id]->phone : ''), "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("content[$key]", !empty($outsideplan->content) ? $outsideplan->content : ((!empty($outsideplan->id) and isset($outsideplans[$outsideplan->id])) ? $outsideplans[$outsideplan->id]->content : ''), "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("milestone[$key]", $outsideplan->milestone, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("changes[$key]", $outsideplan->changes, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::select("apptype[$key]", $lang->outsideplan->apptypeList,  $outsideplan->apptype, "class='form-control chosen'")?></td>
            <td><?php echo html::textarea("projectinitplan[$key]", $outsideplan->projectinitplan, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::textarea("uatplanfinishtime[$key]", $outsideplan->uatplanfinishtime, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::textarea("materialplanonlinetime[$key]", $outsideplan->materialplanonlinetime, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::textarea("planonlinetime[$key]", $outsideplan->planonlinetime, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::select("projectisdelay[$key]", $lang->outsideplan->projectisdelayList,  $outsideplan->projectisdelay, "class='form-control chosen'")?></td>
            <td><?php echo html::textarea("projectisdelaydesc[$key]", $outsideplan->projectisdelaydesc, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::select("projectischange[$key]", $lang->outsideplan->projectischangeList,  $outsideplan->projectischange, "class='form-control chosen'")?></td>
            <td><?php echo html::textarea("projectischangedesc[$key]", $outsideplan->projectischangedesc, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("subProjectName[$key]", $outsideplan->subProjectName, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("subTaskName[$key]", $outsideplan->subTaskName, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("subTaskBegin[$key]", $outsideplan->subTaskBegin, "class='form-control form-date' autocomplete='off'")?></td>
            <td><?php echo html::input("subTaskEnd[$key]", $outsideplan->subTaskEnd, "class='form-control form-date' autocomplete='off'")?></td>
            <td><?php echo html::select("subProjectUnit[$key][]", $subProjectUnitList,  $outsideplan->subProjectUnit, "class='form-control chosen' multiple")?></td>
            <td><?php echo html::select("subProjectBearDept[$key][]", $subProjectBearDeptList,  $outsideplan->subProjectBearDept, "class='form-control chosen' multiple")?></td>
            <td><?php echo html::select("subProjectDemandParty[$key][]", $subProjectDemandPartyList,  $outsideplan->subProjectDemandParty, "class='form-control chosen' multiple")?></td>
            <td><?php echo html::input("subTaskDemandContact[$key]", $outsideplan->subTaskDemandContact, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo html::input("subTaskDemandDeadline[$key]", $outsideplan->subTaskDemandDeadline, "class='form-control form-date' autocomplete='off'")?></td>
            <td><?php echo html::input("subTaskDesc[$key]", $outsideplan->subTaskDesc, "class='form-control' autocomplete='off'")?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='16' class='text-center form-actions'>
            <?php
            $submitText = $isEndPage ? $this->lang->save : $this->lang->file->saveAndNext;
            if(!$insert and $dataInsert === '')
            {
                echo "<button type='button' data-toggle='modal' data-target='#importNoticeModal' class='btn btn-primary btn-wide'>{$submitText}</button>";
            }
            else
            {
                echo html::submitButton($submitText);
                if($dataInsert !== '') echo html::hidden('insert', $dataInsert);
            }
            echo html::hidden('isEndPage', $isEndPage ? 1 : 0);
            echo html::hidden('pagerID', $pagerID);
            echo ' &nbsp; ' . html::backButton();
            echo ' &nbsp; ' . sprintf($lang->file->importPager, $allCount, $pagerID, $allPager);
            ?>
          </td>
        </tr>
      </tfoot>
    </table>
    <?php if(!$insert and $dataInsert === '') include '../../common/view/noticeimport.html.php';?>
  </form>
</div>
<?php endif;?>
<script>
$(function()
{
    $.fixedTableHead('#showData');
});
</script>
<?php include '../../common/view/footer.html.php';?>
