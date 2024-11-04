<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->review->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr id="addItem" class="hidden">
            <th class='w-120px object-th'></th>
            <td class='w-200px'>
              <?php echo html::select('object[]', $lang->review->objectList, '', "class='form-control object-select'");?>
            </td>
            <td colspan='2'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->review->fileUrl;?></span>
                <?php echo html::input('url[]', '', "class='form-control'");?>
              </div>
            </td>
            <td class='c-actions text-left'>
              <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
              <a href='javascript:;' onclick='deleteItem(this)' class='btn btn-link'><i class='icon icon-close'></i></a>
            </td>
          </tr>
          <tr>
            <th class='w-120px'><?php echo $lang->review->object;?></th>
            <td class='w-200px'>
              <?php echo html::select('object[]', $lang->review->objectList, '', "class='form-control chosen'");?>
            </td>
            <td colspan='2'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->review->fileUrl;?></span>
                <?php echo html::input('url[]', '', "class='form-control'");?>
              </div>
            </td>
            <td class='c-actions text-left'>
              <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
              <a href='javascript:;' onclick='deleteItem(this)' class='btn btn-link'><i class='icon icon-close'></i></a>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->review->title;?></th>
            <td colspan="2"><?php echo html::input('title', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->type;?></th>
            <td colspan="2"><?php echo html::select('type', $lang->review->typeList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->grade;?></th>
            <td colspan="2"><?php echo html::select('grade', $lang->review->gradeList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->owner;?></th>
            <td colspan="2"><?php echo html::select('owner[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->expert;?></th>
            <td colspan="2"><?php echo html::select('expert[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->outside;?></th>
            <td colspan="2"><?php echo html::select('outside[]', $outside, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->reviewedBy;?></th>
            <td colspan="2"><?php echo html::select('reviewedBy[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->reviewer;?></th>
            <td colspan="2"><?php echo html::select('reviewer', $users, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->deadline;?></th>
            <td colspan="2"><?php echo html::input('deadline', '', "class='form-date form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td colspan='3' class='form-actions text-center'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('projectID', $projectID)?>
<?php js::set('reviewText', $lang->review->common)?>
<?php js::set('bearDept', $bearDept);?>
<?php include '../../common/view/footer.html.php';?>