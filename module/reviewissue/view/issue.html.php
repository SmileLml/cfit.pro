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
          <button data-toggle="dropdown" type="button" class="btn btn-limit" id="currentItem" title="<?php echo $lang->reviewissue->searchReview;?>" style="border-radius: 2px;"><?php echo $lang->reviewissue->searchReview;?>
          <?php else:?>
          <button data-toggle="dropdown" type="button" class="btn btn-limit" id="currentItem" title="<?php echo $reviewInfo->title;?>" style="border-radius: 2px;"><?php echo $reviewInfo->title;?>
          <?php endif;?>
          <span class="caret"></span>
          </button>
          <div id="dropMenu" class="dropdown-menu search-list load-indicator" data-ride="searchList" data-url="<?php echo $this->createLink('reviewissue', 'ajaxGetReview',"project=$projectID&reviewID=$reviewID&status=$status");?>">
            <div class="input-control search-box has-icon-left has-icon-right search-example">
              <input type="search" class="form-control search-input empty">
              <label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
              <a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a>
            </div>
          </div>
        </div>
      </div>
      <?php
      $params = "project=$projectID&reviewID=$reviewID&status=$status&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
      foreach($lang->reviewissue->browseStatus as $type => $label)
      {
          $active = $type == $status ? 'btn-active-text' : '';
          echo html::a($this->createLink('reviewissue', 'issue', "project=$projectID&reviewID=$reviewID&status=$type&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"),  "<span class='text'>" . $label . '</span>', '',"class='btn btn-link $active'");
      }
      ?>
      <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <div class="pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('reviewissue', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('reviewissue', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('reviewissue', 'export') ? $this->createLink('reviewissue', 'export', "project=$projectID&reviewID=$reviewID&orderBy=$orderBy&browseType=$status") : '#';
                echo "<li $class>" . html::a($link, $lang->reviewissue->export, '', $misc) . "</li>";

                $class = common::hasPriv('reviewissue', 'exportTemplate') ? '' : "class='disabled'";
                $link  = common::hasPriv('reviewissue', 'exportTemplate') ? $this->createLink('reviewissue', 'exportTemplate',"project=$projectID&reviewID=$reviewID") : '#';
                $misc  = common::hasPriv('reviewissue', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->reviewissue->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if(common::hasPriv('reviewissue', 'import')) echo html::a($this->createLink('reviewissue', 'import',"project=$projectID"), '<i class="icon-import muted"></i> <span class="text">' . $lang->reviewissue->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
        </div>
<!--        --><?php //echo $lang->pageActions;?>
        <div class="btn-toolbar pull-right">
            <?php
                $paramsBatch = "project=$projectID&reviewID=$reviewID&source=reviewissue&status=$status&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
            ?>
            <?php common::printLink('reviewissue', 'batchCreate', $paramsBatch, "<i class='icon icon-plus'></i>" . $lang->reviewissue->batchCreate, '', "class='btn btn-secondary'");?>
            <?php common::printLink('reviewissue', 'create', $params, "<i class='icon icon-plus'></i>" . $lang->reviewissue->create, '', "class='btn btn-primary'");?>
        </div>
    </div>

  </div>
  <div id="mainContent" class="main-row">
    <div class="main-col">
        <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='reviewissue'></div>
        <form class='main-table' method='post' id='reviewForm'>
          <?php
          include '../../common/view/datatable.feed.html.php';
          $setting = $this->datatable->getSetting('reviewissue');
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
            <?php foreach($issueList as $issue):?>
            <tr data-id='<?php echo $issue->id?>'>
            <?php foreach($setting as $value) $this->reviewissue->printCell($value, $issue,$reviewID, $users,$reviews,$projectID,$status,$orderBy,$pager);?>
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
