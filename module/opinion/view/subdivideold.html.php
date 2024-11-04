<?php include '../../common/view/header.html.php';?>
<style>
.main-header {padding: 5px 10px;}
</style>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class='cell'>
      <div class="main-header">
        <h2><?php echo $lang->opinion->subdivide;?></h2>
      </div>
      <form method='post' class='load-indicator main-form form-ajax' enctype='multipart/form-data' id="batchCreateForm">
        <div class="table-responsive">
          <table class="table table-form">
            <thead>
              <tr>
                <th class='w-90px'><?php echo $lang->requirement->code;?></th>
                <th class='text-center'><?php echo $lang->requirement->name;?></th>
                <th class='w-120px'><?php echo $lang->requirement->method;?></th>
              </tr>
            </thead>
            <tbody>
              <?php if($requirements):?>
              <?php foreach($requirements as $id => $requirement):?>
              <tr>
                <td><?php echo $requirement->code;?></td>
                <td><?php echo html::input("name[$requirement->code]", $requirement->name, "class='form-control' disabled='disabled'");?></td>
                <td><?php echo html::select("method[$requirement->code]", $lang->requirement->methodList, $requirement->method, "class='form-control chosen' disabled='disabled'");?></td>
              </tr>
              <?php endforeach;?>
              <?php endif;?> 
              <?php for($i = $maxChildID; $i <= $maxChildID + 8; $i++):?>
              <tr>
                <td><?php echo $opinion->code . '-' . sprintf('%02d', $i) . html::hidden("code[$i]", $opinion->code . '-' . sprintf('%02d', $i));?></td>
                <td><?php echo html::input("name[$i]", '', "class='form-control'");?></td>
                <td><?php echo html::select("method[$i]", $lang->requirement->methodList, '', "class='form-control chosen'");?></td>
              </tr>
              <?php endfor;?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan='3' class="text-center form-actions">
                  <?php echo html::submitButton($this->lang->opinion->submit);?>
                  <?php echo html::backButton();?>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </form>
    </div>
    <div class='cell'>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($opinion->desc) ? $opinion->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
  </div>
  <div class='side-col col-4'>
    <div class='cell'>
      <div class="main-header">
        <h2><?php echo $lang->opinion->common . $lang->opinion->info;?></h2>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->background;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($opinion->background) ? $opinion->background : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->overview;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($opinion->overview) ? $opinion->overview : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
