<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
   <div class="btn-toolbar pull-left">
     <?php echo html::a(inlink('browse', "project=$baseline->project"), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
     <div class="divider"></div>
     <div class="page-title">
       <span class="label label-id"><?php echo $baseline->id?></span>
       <span class="text" title="<?php echo $baseline->title;?>"><?php echo $baseline->title;?></span>
     </div>
   </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class='detail-title'><?php echo $lang->cm->item;?></div>
      <div class='detail-content article-content main-table'>
        <table class='table'>
          <thead>
            <tr>
              <th><?php echo $lang->cm->itemname;?></th>
              <th><?php echo $lang->cm->itemcode;?></th>
              <th><?php echo $lang->cm->version;?></th>
              <th><?php echo $lang->cm->changed;?></th>
              <th><?php echo $lang->cm->changedID;?></th>
              <th><?php echo $lang->cm->changedDate;?></th>
              <th><?php echo $lang->cm->path;?></th>
              <th><?php echo $lang->cm->comment;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($baseline->items as $item):?>
            <tr>
              <td><?php echo $item->title;?></td>
              <td><?php echo $item->code;?></td>
              <td><?php echo $item->version;?></td>
              <td><?php echo $item->changed ? $lang->cm->changeList['yes'] : $lang->cm->changeList['no'];?></td>
              <td>
                <?php 
                if(common::hasPriv('change', 'view') and $item->changedID) 
                {
                    $changid = $item->changedID;
                    $id = explode('-',$changid);
                    $id = end($id);
                    echo html::a($this->createLink('change', 'view', "changeID=$id"), zget($changes, $item->changedID));
                }
                else
                {
                    echo '\\';
                }
                ?>
              </td>
              <td><?php echo $item->changedDate == '0000-00-00' ? '\\' : $item->changedDate;?></td>
              <td><?php echo $item->path;?></td>
              <td><?php echo $item->comment;?></td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class='side-col col-4'>
    <div class="cell">
      <div class='detail-title'><?php echo $lang->cm->basicInfo;?></div>
      <div class='detail-content'>
        <table class='table-data'>
          <tr>
            <th class='w-120px'><?php echo $lang->cm->title;?></th>
            <td><?php echo $baseline->title;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->cm->type;?></th>
            <td><?php echo zget($lang->cm->typeList, $baseline->type);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->cm->createdBy;?></th>
            <td><?php echo zget($users, $baseline->createdBy);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->cm->cmDate;?></th>
            <td><?php echo $baseline->cmDate;?></td>
          </tr>
         <tr>
            <th><?php echo $lang->cm->createdDate;?></th>
            <td><?php echo $baseline->createdDate;?></td>
        </tr>
        </table>
      </div>
    </div>
    <div class='cell'>
    <?php include '../../common/view/action.html.php';?>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
