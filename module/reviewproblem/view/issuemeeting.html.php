<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
</style>

  <div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="btn-group angle-btn">
            <div class="btn-group">
                <?php if(empty($reviewInfo)):?>
                <button data-toggle="dropdown" type="button" class="btn btn-limit" id="currentItem" title="<?php echo $lang->reviewproblem->searchReview;?>" style="border-radius: 2px;"><?php echo $lang->reviewproblem->searchReview;?>
                    <?php else:?>
                <button data-toggle="dropdown" type="button" class="btn btn-limit" id="currentItem" title="<?php echo $reviewInfo->title;?>" style="border-radius: 2px;"><?php echo $reviewInfo->title;?>
                    <?php endif;?>
                    <span class="caret"></span>
                </button>
                <div id="dropMenu" class="dropdown-menu search-list load-indicator" data-ride="searchList" data-url="<?php echo $this->createLink('reviewproblem', 'ajaxGetReview',"project=$projectID&reviewID=$reviewID&status=$status");?>">
                    <div class="input-control search-box has-icon-left has-icon-right search-example">
                        <input type="search" class="form-control search-input empty">
                        <label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
                        <a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <?php foreach($lang->reviewproblem->newbrowseStatus as $key => $label):?>
            <?php $active = $status == $key ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($status == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
            <?php echo html::a($this->createLink('reviewproblem', 'issue', "project=$projectID&reviewID=$reviewID&status=$key&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"),  $label, '',"class='btn btn-link $active'");?>
        <?php endforeach;?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <div class="pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('reviewproblem', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('reviewproblem', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('reviewproblem', 'export') ? $this->createLink('reviewproblem', 'export', "project=$projectID&reviewID=$reviewID&orderBy=$orderBy&browseType=$status") : '#';
                echo "<li $class>" . html::a($link, $lang->reviewproblem->export, '', $misc) . "</li>";

                $class = common::hasPriv('reviewproblem', 'exportTemplate') ? '' : "class='disabled'";
                $link  = common::hasPriv('reviewproblem', 'exportTemplate') ? $this->createLink('reviewproblem', 'exportTemplate',"project=$projectID&reviewID=$reviewID") : '#';
                $misc  = common::hasPriv('reviewproblem', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->reviewproblem->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if(common::hasPriv('reviewproblem', 'import')) echo html::a($this->createLink('reviewproblem', 'import',"project=$projectID"), '<i class="icon-import muted"></i> <span class="text">' . $lang->reviewproblem->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
        </div>
        <!--        --><?php //echo $lang->pageActions;?>
        <div class="btn-toolbar pull-right">
            <?php
            $paramsBatch = "project=$projectID&reviewID=$reviewID&source=reviewproblem&status=$status&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
            $params = "project=$projectID&reviewID=$reviewID&status=$status&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
            ?>
            <?php common::printLink('reviewproblem', 'batchCreate', $paramsBatch, "<i class='icon icon-plus'></i>" . $lang->reviewproblem->batchCreate, '', "class='btn btn-secondary'");?>
            <?php common::printLink('reviewproblem', 'create', $params, "<i class='icon icon-plus'></i>" . $lang->reviewproblem->create, '', "class='btn btn-primary'");?>
        </div>
    </div>

  </div>
  <div id="mainContent" class="main-row">
    <div class="main-col">
        <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='reviewproblem'></div>
        <form class='main-table' method='post' id='reviewForm'>
          <?php
          include '../../common/view/datatable.feed.html.php';
          $setting = $this->datatable->getSetting('reviewproblem');
          $widths  = $this->datatable->setFixedFieldWidth($setting);
          ?>
        <table class='table has-sort-head datatable' id='issueTable' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' >
            <thead>
            <tr>
                <?php
                $vars = "project=$projectID&reviewID=$reviewID&status=$status&param=0&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
                foreach($setting as $value)
                {
                    if($value->show)
                    {
                        $this->datatable->printHeadFixed($value, $orderBy, $vars, false);
                    }
                }
                ?>
            </tr>
            </thead>
          <tbody class="sortable" id="issueTableList">
            <?php foreach($issueMeetingList as $issue):?>
            <tr data-id='<?php echo $issue->id?>'>
            <?php foreach($setting as $value) $this->reviewproblem->printCell($value, $issue,$reviewID, $users,$reviews,$projectID,$status,$orderBy,$pager);?>
            </tr>
                <?php endforeach;?>
          </tbody>
        </table>
        <div class="table-footer">
          <?php $pager->show('right', 'pagerjs');?>
        </div>
        </form>
    </div>
    </div>
  </div>
<script>
    $(function(){$('#reviewForm').table();})
    $(document).ready(function(){
      $("a").each(function(){
          if($(this).hasClass('disabled btn')){
              $(this).removeAttr('href');
          }
      })
    });

</script>
<?php include '../../common/view/footer.html.php'?>
