<?php include '../../common/view/header.html.php';?>
<style>
.main-table tbody>tr>td:first-child, .main-table thead>tr>th:first-child {
    padding-right: 4px;
    padding-left: 8px;
}
</style>
<div id="mainMenu" class="clearfix">
</div>
<div id="mainContent" class="main-row fade in">
  <div class="main-col">
    <div class="main-table">
      <table class="table table-bordered has-sort-head table-fixed">
        <thead>
         <tr class="text-center">
           <td colspan="12"><h5><?php echo $lang->cm->baselineReport;?></h5></td>
         </tr>
         <tr>
           <td><strong><?php echo $lang->cm->projectName;?></strong></td>
           <td colspan="3"><?php echo $project->name;?></td>
           <td><strong><?php echo $lang->cm->projectCode;?></strong></td>
           <td colspan="2"><?php echo $project->code;?></td>
           <td><strong><?php echo $lang->cm->PM;?></strong></td>
           <td><strong><?php echo zget($users, $project->PM);?></strong></td>
           <td><strong><?php echo $lang->cm->PO;?></strong></td>
           <td colspan='2'><strong><?php echo zget($users, $project->PO);?></strong></td>
         </tr>
         <tr>
           <td><strong><?php echo $lang->cm->QA;?></strong></td>
           <td colspan="3"><strong><?php echo zget($users, $project->QA);?></strong></td>
           <td><strong><?php echo $lang->cm->projectStage;?></strong></td>
           <td colspan="7"><?php echo $currentStage;?></td>
         </tr>
         <tr>
           <td><?php echo $lang->cm->title;?></td>
           <td><?php echo $lang->cm->type;?></td>
           <td><?php echo $lang->cm->cmAndDate;?></td>
           <td><?php echo $lang->cm->reviewerAndDate;?></td>
           <td><?php echo $lang->cm->itemname;?></td>
           <td><?php echo $lang->cm->itemcode;?></td>
           <td><?php echo $lang->cm->version;?></td>
           <td><?php echo $lang->cm->changed;?></td>
           <td><?php echo $lang->cm->changedID;?></td>
           <td><?php echo $lang->cm->changedDate;?></td>
           <td><?php echo $lang->cm->path;?></td>
           <td><?php echo $lang->cm->comment;?></td>
         </tr>
        </thead>
        <tbody>
          <?php foreach($baselines as $id => $baseline):?>
          <?php $i = 0;?>
          <?php foreach($baseline->items as $item):?>
          <tr>
            <?php if($i == 0):?>
            <td title=<?php echo $baseline->title;?> rowspan=<?php echo count($baseline->items);?>><?php echo $baseline->title;?></td>
            <td rowspan=<?php echo count($baseline->items);?>><?php echo zget($lang->cm->typeList, $baseline->type);?></td>
            <td rowspan=<?php echo count($baseline->items);?>><?php echo zget($users, $baseline->cm) . ' ' . $baseline->cmDate;?></td>
            <td rowspan=<?php echo count($baseline->items);?>><?php echo zget($users, $baseline->reviewer) . ' ' . $baseline->reviewedDate;?></td>
            <?php endif;?>
            <td title=<?php echo $item->title;?>><?php echo $item->title;?></td>
            <td><?php echo $item->code;?></td>
            <td><?php echo $item->version;?></td>
            <td><?php echo $item->changed ? $lang->cm->changeList['yes'] : $lang->cm->changeList['no'];?></td>
            <td title=<?php echo zget($changes, $item->changedID);?>>
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
                echo "\\";
            }
            ?>
            </td>
            <td><?php echo $item->changedDate == '0000-00-00' ? '\\' : $item->changedDate;?></td>
            <td><?php echo $item->path;?></td>
            <td><?php echo $item->comment;?></td>
          </tr>
          <?php $i ++;?>
          <?php endforeach;?>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
