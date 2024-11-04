<?php
/**
 * The project view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     my
 * @version     $Id
 * @link        http://www.zentao.net
 */
?>

<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
    #queryBox .table td{overflow: unset}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->reviewmanage->reviewMenu as $key => $label):?>
    <?php $active = $status == $key ? 'btn-active-text' : '';?>
    <?php $label = "<span class='text'>$label</span>";?>
    <?php if($status == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
    <?php echo html::a(inlink('browse', "type=$key"), $label, '', "class='btn btn-link $active'");?>
    <?php endforeach;?>
      <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
</div>
<div id="mainContent" class="main-row fade">
    <div class='main-col'>
        <div class="cell<?php
        if($status == 'bysearch') echo ' show';?>" id="queryBox" data-module='reviewmanage'></div>
        <?php if($status =='deptjoin' and !common::hasPriv('reviewmanage', 'judgepermission') ):?>
        <div class="table-empty-tip">
            <p>
                <span class="text-muted"><?php echo $lang->reviewmanage->noPermissions;?></span>
            </p>
        </div>
        <?php elseif(empty($reviewList)):?>
        <div class="table-empty-tip">
            <p>
                <span class="text-muted"><?php echo $lang->noData;?></span>
            </p>
        </div>
        <?php else:?>
            <form class='main-table' method='post' id='reviewManageForm'>
                <div class="table-header fixed-right">
                    <nav class="btn-toolbar pull-right"></nav>
                </div>
                <?php
                $vars = "status=$status&queryID=$queryID&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                include '../../common/view/datatable.html.php';

                $setting = $this->datatable->getSetting('reviewmanage');
                $widths  = $this->datatable->setFixedFieldWidth($setting);
                $actions = array('edit','submit','recall','assign','review','reviewreport','close','delete');//操作列
                $allow = [];
                if(isset($this->app->user->rights['rights']['reviewmanage'])){
                    $allow = array_values(array_keys($this->app->user->rights['rights']['reviewmanage']));//有权限
                }
                $diff  = array_intersect($allow,$actions);
                if(count($diff) >= 7 ||$this->app->user->account == 'admin') {
                    $widths['rightWidth'] = '220';
                }else{
                    $widths['rightWidth'] = 70 + (20*count($diff));
                }

                ?>
                <table class='table has-sort-head datatable' id='reviewManageList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
                    <thead>
                    <tr>
                        <?php
                        foreach($setting as $value)
                        {
                            //不参与排序初审部门，初审部门接口人，初审主审人员，初审参与人员
                            if(in_array($value->id,['trialDept','trialDeptLiasisonOfficer','trialAdjudicatingOfficer','trialJoinOfficer'])){
                                $value->sort = 'no';
                            }
                            if($value->id == 'actions'){
                                if(count($diff) >= 7) {
                                    $value->width = '220';
                                }else{
                                    $widths['rightWidth'] = 70 + (20*count($diff));
                                }
                            }
                            if($value->show)
                            {
                                $this->datatable->printHead($value, $orderBy, $vars, false);
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $review):?>
                        <tr data-id='<?php echo $review->id?>'>
                            <?php foreach($setting as $value) $this->reviewmanage->printCell($value, $review, $users, $products,0);?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
            </form>
        <?php endif;?>
    </div>
</div>
<script>
    $(function(){$('#reviewManageForm').table();})
</script>
<?php include '../../common/view/footer.html.php';?>
