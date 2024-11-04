<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a(inlink('browse'), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $productline->code?></span>
      <span class="text" title='<?php echo $productline->name;?>'><?php echo $productline->name;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->productline->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($productline->desc) ? $productline->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $productline->files, 'fieldset' => 'true', 'object' => $productline));?>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=productline&objectID=$productline->id");?>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack(inlink('browse'));?>
        <div class='divider'></div>
        <?php
          common::printIcon('productline', 'edit', "productlineID=$productline->id", $productline, 'button');
          common::printIcon('productline', 'delete', "productlineID=$productline->id", $productline, 'button', 'trash', 'hiddenwin');
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->productline->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->productline->code;?></th>
                <td><?php echo $productline->code;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->productline->createdBy;?></th>
                <td><?php echo zget($users, $productline->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->productline->createdDate;?></th>
                <td><?php echo $productline->createdDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->productline->dept;?></th>
                <td>
                <?php
                foreach(explode(',', $productline->depts) as $dept)
                {
                    echo '<p>' . zget($depts, $dept, '') . '</p>';
                }
                ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
