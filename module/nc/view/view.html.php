<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php $browseLink = $this->session->ncList ? $this->session->ncList : $this->createLink('nc', 'browse', "program=$nc->project");?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $nc->id?></span>
      <span class="text"><?php echo $nc->title;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class='cell'>
      <div class='detail-title'><?php echo $lang->nc->desc;?></div>
      <div class='detail-content'>
        <?php echo $nc->desc;?>
      </div>
    </div>
    <div class='cell'><?php include '../../common/view/action.html.php';?></div>
  </div>
  <div class="side-col col-4">
    <div class='cell'>
      <div class='detail-title'><?php echo $lang->nc->basicInfo;?></div>
      <div class='detail-content'>
        <table class='table-data'>
          <tr>
            <th class='w-100px'><?php echo $lang->nc->severity;?></th>
            <td><?php echo zget($lang->nc->severityList, $nc->severity);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->type;?></th>
            <td><?php echo zget($lang->nc->typeList, $nc->type);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->object;?></th>
            <td><?php echo $nc->objectType == 'activity' ? zget($activities, $nc->objectID) : zget($outputs, $nc->objectID);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->status;?></th>
            <td><?php echo zget($lang->nc->statusList, $nc->status);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->deadline;?></th>
            <td><?php echo $nc->deadline;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->createdBy;?></th>
            <td><?php echo zget($users, $nc->createdBy);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->createdDate;?></th>
            <td><?php echo zget($users, $nc->createdDate);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->resolution;?></th>
            <td><?php echo zget($lang->nc->resolutionList, $nc->resolution);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->resolvedBy;?></th>
            <td><?php echo zget($users, $nc->resolvedBy);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->resolvedDate;?></th>
            <td><?php echo $nc->resolvedDate;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->closedBy;?></th>
            <td><?php echo zget($users, $nc->closedBy);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->closedDate;?></th>
            <td><?php echo $nc->closedDate;?></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
