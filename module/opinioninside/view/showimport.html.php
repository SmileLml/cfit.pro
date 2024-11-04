<?php include '../../common/view/header.html.php';?>
<?php if(isset($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->opinioninside->import;?></h2>
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
    $('#import').click(function(){location.href = createLink('opinion', 'showImport', "pageID=1&maxImport=" + $('#maxImport').val())})
});
</script>
<?php else:?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->opinioninside->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-60px'><?php echo $lang->opinioninside->id?></th>
          <th class='w-300px required'><?php echo $lang->opinioninside->name?></th>
          <th class='w-150px required'><?php echo $lang->opinioninside->background?></th>
          <th class='w-300px required'><?php echo $lang->opinioninside->overview?></th>
          <th class='w-120px required'><?php echo $lang->opinioninside->desc;?></th>
          <th class='w-100px required'><?php echo $lang->opinioninside->sourceMode?></th>
          <th class='w-100px required'><?php echo $lang->opinioninside->sourceName;?></th>
          <th class='w-100px required'><?php echo $lang->opinioninside->category?></th>
          <th class='w-160px required'><?php echo $lang->opinioninside->union;?></th>
          <th class='w-120px required'><?php echo $lang->opinioninside->receiveDate?></th>
          <th class='w-120px required'><?php echo $lang->opinioninside->deadline?></th>
          <th class='w-100px required'><?php echo $lang->opinioninside->createdBy?></th>
          <th class='w-120px required'><?php echo $lang->opinioninside->contact?></th>
          <th class='w-120px required'><?php echo $lang->opinioninside->contactInfo?></th>
          <th class='w-120px required'><?php echo $lang->opinioninside->assignedTo?></th>
          <th class='w-200px required'><?php echo $lang->opinioninside->status?></th>
          <th class='w-200px required'><?php echo $lang->opinioninside->dealUser;?></th>
          <th class='w-200px'><?php echo $lang->opinioninside->remark;?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        ?>
        <?php foreach($opinionData as $key => $opinion):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($opinion->id))
            {
                echo $opinion->id . html::hidden("id[$key]", $opinion->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->opinioninside->new}</sub>";
            }
            ?>
          </td>
          <td><?php echo html::input("name[$key]", htmlspecialchars($opinion->name, ENT_QUOTES), "class='form-control'")?></td>
          <td><?php echo html::textarea("background[$key]", $opinion->background, "class='form-control' cols='35' rows='1'")?></td>
          <td><?php echo html::textarea("overview[$key]", $opinion->overview, "class='form-control' cols='35' rows='1'")?></td>
          <td><?php echo html::textarea("desc[$key]", $opinion->desc, "class='form-control' cols='35' rows='1'")?></td>
          <td><?php echo html::select("sourceMode[$key]", $lang->opinion->sourceModeList, !empty($opinion->sourceMode) ? $opinion->sourceMode : ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->sourceMode : ''), "class='form-control'")?></td>
          <td><?php echo html::input("sourceName[$key]", !empty($opinion->sourceName) ? $opinion->sourceName : ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->sourceName : ''), "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::select("category[$key]", $lang->opinion->categoryList, !empty($opinion->category) ? $opinion->category: ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->category: ''), "class='form-control chosen'")?></td>
          <td><?php echo html::select("union[$key][]", $lang->opinion->unionList, !empty($opinion->union) ? $opinion->union : ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->union : ''), "class='form-control chosen' multiple")?></td>
          <td><?php echo html::input("receiveDate[$key]", !empty($opinion->receiveDate) ? $opinion->receiveDate : ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->receiveDate : ''), "class='form-control form-date' autocomplete='off'")?></td>
          <td><?php echo html::input("deadline[$key]", !empty($opinion->deadline) ? $opinion->deadline : ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->deadline : ''), "class='form-control form-date' autocomplete='off'")?></td>
          <td><?php echo html::select("createdBy[$key]", $users, !empty($opinion->createdBy) ? $opinion->createdBy: ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->createdBy : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::input("contact[$key]", !empty($opinion->contact) ? $opinion->contact : ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->contact : ''), "class='form-control'")?></td>
          <td><?php echo html::input("contactInfo[$key]", !empty($opinion->contactInfo) ? $opinion->contactInfo : ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->contactInfo : ''), "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::select("assignedTo[$key]", $users, !empty($opinion->assignedTo) ? $opinion->assignedTo: ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->assignedTo : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::select("status[$key]", $statusList, !empty($opinion->status) ? $opinion->status: ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->status : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::select("dealUser[$key]", $users, !empty($opinion->dealUser) ? $opinion->dealUser: ((!empty($opinion->id) and isset($opinions[$opinion->id])) ? $opinions[$opinion->id]->dealUser : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::textarea("remark[$key]", $opinion->remark, "class='form-control' cols='35' rows='1'")?></td>
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
