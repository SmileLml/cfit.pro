<?php include '../../common/view/header.html.php';?>
<?php if(isset($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->projectplan->import;?></h2>
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
    $('#import').click(function(){location.href = createLink('projectplan', 'showImport', "pageID=1&maxImport=" + $('#maxImport').val())})
});
</script>
<?php else:?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->projectplan->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-60px'><?php echo $lang->projectplan->id?></th>
          <th class='w-120px'><?php echo $lang->projectplan->year?></th>
            <th class='w-150px'><?php echo $lang->projectplan->type?></th>
          <th class='w-300px'><?php echo $lang->projectplan->name?></th>
          <th class='w-300px'><?php echo $lang->projectplan->outsideProject?></th>
          <th class='w-300px'><?php echo $lang->projectplan->outsideSubProject?></th>
          <th class='w-300px'><?php echo $lang->projectplan->outsideTask?></th>

          <th class='w-300px'><?php echo $lang->projectplan->basis?></th>
          <th class='w-300px'><?php echo $lang->projectplan->category?></th>
          <th class='w-250px'><?php echo $lang->projectplan->app;?></th>
            <th class='w-250px'><?php echo $lang->projectplan->line;?></th>
          <th class='w-300px'><?php echo $lang->projectplan->content?></th>

          <th class='w-120px'><?php echo $lang->projectplan->isImportant;?></th>
            <th class='w-100px'><?php echo $lang->projectplan->bearDept?></th>
            <th class='w-100px'><?php echo $lang->projectplan->secondLine?></th>
<!--            // tongyanqi 2022-04-19-->
<!--            研发8项-->
            <th class='w-120px'><?php echo $lang->projectplan->storyStatus;?></th>
            <th class='w-120px'><?php echo $lang->projectplan->dataEnterLake;?></th>
            <th class='w-120px'><?php echo $lang->projectplan->basicUpgrade;?></th>
          <th class='w-120px'><?php echo $lang->projectplan->systemAssemble;?></th>
          <th class='w-120px'><?php echo $lang->projectplan->cloudComputing;?></th>
          <th class='w-120px'><?php echo $lang->projectplan->passwordChange;?></th>
          <th class='w-100px'><?php echo $lang->projectplan->structure?></th>
          <th class='w-100px'><?php echo $lang->projectplan->localize;?></th>
            <th class='w-120px'><?php echo $lang->projectplan->planRemark;?></th>
          <th class='w-100px'><?php echo $lang->projectplan->reviewDate?></th>
          <th class='w-100px'><?php echo $lang->projectplan->begin?></th>
          <th class='w-100px'><?php echo $lang->projectplan->end?></th>
          <th class='w-100px'><?php echo $lang->projectplan->workload?></th>

            <th class='w-100px'><?php echo $lang->projectplan->workloadBase?></th>
            <th class='w-100px'><?php echo $lang->projectplan->workloadChengdu?></th>
            <th class='w-100px'><?php echo $lang->projectplan->nextYearWorkloadBase?></th>
            <th class='w-100px'><?php echo $lang->projectplan->nextYearWorkloadChengdu?></th>
            <th class='w-100px'><?php echo $lang->projectplan->duration?></th>
            <th class='w-140px'><?php echo $lang->projectplan->owner?></th>
          <th class='w-100px'><?php echo $lang->projectplan->phone?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        $lang->projectplan->categoryList = $this->lang->opinion->categoryList;
        ?>
        <?php foreach($projectplanData as $key => $projectplan):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($projectplan->id))
            {
                echo $projectplan->id . html::hidden("id[$key]", $projectplan->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->projectplan->new}</sub>";
            }
            ?>
          </td>
          <td><?php echo html::input("year[$key]", $projectplan->year, "class='form-control'")?></td>
            <td><?php echo html::select("type[$key]", $lang->projectplan->typeList, !empty($projectplan->type) ? $projectplan->type : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->type : ''), "class='form-control chosen'")?></td>
            <td><?php echo html::input("name[$key]", htmlspecialchars($projectplan->name, ENT_QUOTES), "class='form-control'")?></td>
          <td><?php echo html::select("outsideProject[$key][]", $outsideProjects, !empty($projectplan->outsideProject) ? $projectplan->outsideProject : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->app : ''), "class='form-control chosen' multiple")?></td>
          <td><?php echo html::select("outsideSubProject[$key][]", $outsideSubProject, "", "class='form-control chosen' multiple")?></td>
          <td><?php echo html::select("outsideTask[$key][]", $outsideTask, "", "class='form-control chosen' multiple")?></td>
            <td><?php echo html::select("basis[$key][]", $lang->projectplan->basisList, !empty($projectplan->basis) ? $projectplan->basis : '', "class='form-control chosen' multiple")?></td>
           <td><?php echo html::select("category[$key]", $lang->projectplan->categoryList, !empty($projectplan->category) ? $projectplan->category : '', "class='form-control chosen'")?></td>
            <td><?php echo html::select("app[$key][]", $apps, !empty($projectplan->app) ? $projectplan->app : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->app : ''), "class='form-control chosen' multiple")?></td>
            <td><?php echo html::select("line[$key][]", $lines, !empty($projectplan->line) ? $projectplan->line : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->line : ''), "class='form-control chosen' multiple")?></td>
            <td><?php echo html::textarea("content[$key]", !empty($projectplan->content) ? $projectplan->content : '', "class='form-control'")?></td>
            <td><?php echo html::select("isImportant[$key]", $lang->projectplan->isImportantList, $projectplan->isImportant ?? '', "class='form-control chosen'")?></td>
            <td><?php echo html::select("bearDept[$key][]", $depts, !empty($projectplan->bearDept) ? $projectplan->bearDept : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->bearDept : ''), "class='form-control chosen' multiple")?></td>
            <td><?php echo html::select("secondLine[$key][]", $lang->projectplan->secondLineList, !empty($projectplan->secondLine) ? $projectplan->secondLine : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->secondLine : ''), "class='form-control chosen'")?></td>
            <td><?php echo html::select("storyStatus[$key]", $lang->projectplan->storyStatusList, !empty($projectplan->storyStatus) ? $projectplan->storyStatus : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->storyStatus : ''), "class='form-control chosen'")?></td>
            <td><?php echo html::select("dataEnterLake[$key]", $lang->projectplan->dataEnterLakeList, $projectplan->dataEnterLake ?? "", "class='form-control chosen'")?></td>
            <td><?php echo html::select("basicUpgrade[$key]", $lang->projectplan->basicUpgradeList,  $projectplan->basicUpgrade ?? "",  "class='form-control chosen'")?></td>

            <td><?php echo html::select("systemAssemble[$key]", $lang->projectplan->systemAssembleList, $projectplan->systemAssemble ?? '', "class='form-control chosen'")?></td>
          <td><?php echo html::select("cloudComputing[$key]", $lang->projectplan->cloudComputingList, $projectplan->cloudComputing ?? '', "class='form-control chosen'")?></td>
          <td><?php echo html::select("passwordChange[$key]", $lang->projectplan->passwordChangeList, $projectplan->passwordChange ?? '', "class='form-control chosen'")?></td>
          <td><?php echo html::input("structure[$key]", !empty($projectplan->structure) ? $projectplan->structure : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->structure : ''), "class='form-control'")?></td>
          <td><?php echo html::select("localize[$key]", $lang->projectplan->localizeList, !empty($projectplan->localize) ? $projectplan->localize : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->localize : ''), "class='form-control chosen'")?></td>
            <td><?php echo html::input("planRemark[$key]", !empty($projectplan->planRemark) ? $projectplan->planRemark : '', "class='form-control'")?></td>
            <td><?php echo html::input("reviewDate[$key]", !empty($projectplan->reviewDate) ? $projectplan->reviewDate : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->reviewDate : ''), "class='form-control form-date' autocomplete='off'")?></td>
          <td><?php echo html::input("begin[$key]", !empty($projectplan->begin) ? $projectplan->begin : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->begin : ''), "class='form-control form-date' autocomplete='off'")?></td>
          <td><?php echo html::input("end[$key]", !empty($projectplan->end) ? $projectplan->end : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->end : ''), "class='form-control form-date' autocomplete='off'")?></td>
          <td><?php echo html::input("workload[$key]", !empty($projectplan->workload)&& is_numeric($projectplan->workload) ?  round(($projectplan->workload),2)  : 0, "class='form-control'")?></td>
          <td><?php echo html::input("workloadBase[$key]", !empty($projectplan->workloadBase)&& is_numeric($projectplan->workloadBase) ? round($projectplan->workloadBase,2)  : 0, "onkeyup='value=value.replace(/[^\d]/g,\"\")'  class='form-control'")?></td>
          <td><?php echo html::input("workloadChengdu[$key]", !empty($projectplan->workloadChengdu)&& is_numeric($projectplan->workloadChengdu) ?  round($projectplan->workloadChengdu,2)  : 0, "onkeyup='value=value.replace(/[^\d]/g,\"\")'  class='form-control'")?></td>
          <td><?php echo html::input("nextYearWorkloadBase[$key]", !empty($projectplan->nextYearWorkloadBase)&& is_numeric($projectplan->nextYearWorkloadBase) ?  round($projectplan->nextYearWorkloadBase,2) : 0, "onkeyup='value=value.replace(/[^\d]/g,\"\")'  class='form-control'")?></td>
          <td><?php echo html::input("nextYearWorkloadChengdu[$key]", !empty($projectplan->nextYearWorkloadChengdu)&& is_numeric($projectplan->nextYearWorkloadChengdu) ?  round($projectplan->nextYearWorkloadChengdu,2) : 0, "onkeyup='value=value.replace(/[^\d]/g,\"\")'  class='form-control'")?></td>
          <td><?php echo html::input("duration[$key]", !empty($projectplan->duration) ? $projectplan->duration : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->duration : ''), "class='form-control' autocomplete='off'")?></td>
        <td><?php echo html::select("owner[$key][]", $users, !empty($projectplan->owner) ? $projectplan->owner : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->owner : ''), "class='form-control chosen' multiple")?></td>
          <td><?php echo html::input("phone[$key]", !empty($projectplan->phone) ? $projectplan->phone : ((!empty($projectplan->id) and isset($projectplans[$projectplan->id])) ? $projectplans[$projectplan->id]->phone : ''), "class='form-control' autocomplete='off'")?></td>
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

