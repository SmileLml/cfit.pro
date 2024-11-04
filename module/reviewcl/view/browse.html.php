<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/sortable.html.php';?>
<div id='mainMenu' class='clearfix'>
  <div id="sidebarHeader">
    <div class="title">
      <?php
      if($object)
      {   
          echo zget($lang->reviewcl->objectList, $object);
          echo html::a(inlink('browse', "object="), "<i class='icon icon-sm icon-close'></i>", '', "class='text-muted'");
      }
      else
      {
          echo $lang->reviewcl->browseByType; 
      }
      ?>  
    </div>
  </div>
  <div class='btn-toolbar pull-left'>
    <?php include './menu.html.php';?>
  </div>
  <div class='btn-toolbar pull-right'>
    <?php common::printLink('reviewcl', 'batchCreate', '', "<i class='icon icon-plus'></i>" . $lang->reviewcl->batchCreate, '', "class='btn btn-primary'");?>
    <?php common::printLink('reviewcl', 'create',      '', "<i class='icon icon-plus'></i>" . $lang->reviewcl->create,      '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade in">
  <div class="side-col" id="sidebar">
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class="cell">
      <ul class='tree'>
      <?php foreach($lang->reviewcl->objectList as $key => $value):?>
      <?php $active = $object == $key ? 'active' : '';?>
      <li><?php echo html::a($this->createLink('reviewcl', 'browse', "object=$key"), $value, '', "class= $active");?></li>
      <?php endforeach;?>
      </ul>
    </div>
  </div>
  <div class="main-col">
    <div id="mainContent" class="main-table">
      <?php if(empty($reviewcls)):?>
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->reviewcl->noReviewcl;?></span></p>
      </div>
      <?php else:?>
      <table class='table has-sort-head table-fixrd' id="reviewclTable">
        <?php $vars = "object=$object&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";?>
          <thead>
            <tr>
              <th class='w-100px'><?php common::printOrderLink('category',    $orderBy, $vars, $lang->reviewcl->category);?></th>
              <th class='w-70px'> <?php common::printOrderLink('id',          $orderBy, $vars, $lang->reviewcl->id);?></th>
              <th>                <?php common::printOrderLink('title',       $orderBy, $vars, $lang->reviewcl->title);?></th>
              <th class='w-150px'><?php common::printOrderLink('object',      $orderBy, $vars, $lang->reviewcl->object);?></th>
              <th class='w-120px'><?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->reviewcl->createdBy);?></th>
              <th class='w-120px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->reviewcl->createdDate);?></th>
              <th class='c-action w-80px'><?php echo $lang->reviewcl->actions;?></th>
            </tr>
          </thead>
          <tbody class='sortable' id='orderTableList'>
          <?php foreach($lang->reviewcl->categoryList as $key => $value):?>
          <?php if(!isset($reviewcls[$key])) continue;?>
          <?php $i = 0;?>
          <?php foreach($reviewcls[$key] as $reviewcl):?>
          <tr>
            <?php if($i == 0):?> 
            <td rowspan=<?php echo count($reviewcls[$key]);?>><?php echo $value; ?></td>
            <?php endif;?>
            <td><?php echo $reviewcl->id;?></td>
            <td><?php echo html::a($this->createLink('reviewcl', 'view', "id=$reviewcl->id"), $reviewcl->title);?></td>
            <td><?php echo zget($lang->reviewcl->objectList, $reviewcl->object);?></td>
            <td><?php echo zget($users, $reviewcl->createdBy);?></td>
            <td><?php echo substr($reviewcl->createdDate, 0, 11);?></td>
            <td class='c-actions'>
            <?php
            $vars = "id={$reviewcl->id}";
            common::printIcon('reviewcl', 'edit',   $vars, $reviewcl, 'list');
            common::printIcon('reviewcl', 'delete', $vars, $reviewcl, 'list', 'trash', 'hiddenwin');
            ?>
            </td>
          </tr>
          <?php $i ++;?>
          <?php endforeach;?>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class='table-footer table-statistic'>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
      <?php endif;?>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
