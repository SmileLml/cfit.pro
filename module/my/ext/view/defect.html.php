<?php
/**
 * The risk view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     my
 * @version     $Id
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php js::set('mode', $mode);?>
<?php js::set('total', $pager->recTotal);?>
<?php js::set('rawMethod', $app->rawMethod);?>
<style>
.pri-low {color: #000000;}
.pri-middle {color: #FF9900;}
.pri-high {color: #E53333;}
</style>
<div id="mainMenu" class="clearfix">

    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach($lang->defect->labelList as $label => $labelName)
        {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a(inlink('work-defect', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

            $i++;
            if($i >= 10) break;
        }
        if($i>=10)
        {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach($lang->defect->labelList as $label => $labelName)
            {
                $i++;
                if($i <= 10) continue;

                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a(inlink('work-defect', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
    </div>
</div>
<div id="mainContent">
  <?php if(empty($defectInfo)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
  </div>
  <?php else:?>
  <form id='myTaskForm' class="main-table table-defect" data-ride="table" method="post">
    <table class="table has-sort-head table-fixed" id='defecttable'>
      <?php $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
        <thead>
        <thead>
        <tr>
            <th class="w-p15"><?php echo $lang->defect->idAB;?></th>
            <th style="width: 12%"><?php echo $lang->defect->defectTitle;?></th>
            <th style="width: 12%"><?php echo $lang->defect->product;?></th>
            <th style="width: 12%"><?php echo $lang->defect->project;?></th>
            <th style="width: 5%"><?php echo $lang->defect->pri;?></th>
            <th style="width: 8%"><?php echo $lang->defect->severity;?></th>
            <th style="width: 8%"><?php echo $lang->defect->source;?></th>
            <th class='w-p10'><?php echo $lang->defect->createdDate;?></th>
            <th style="width: 8%"><?php echo $lang->defect->status;?></th>
            <th style="width: 6%"><?php echo $lang->defect->nextUser;?></th>
            <th style="width: 6%"><?php echo $lang->defect->dealSuggest;?></th>
            <th style="width: 6%"><?php echo $lang->defect->syncStatus;?></th>
            <th class='text-center w-p10'><?php echo $lang->actions;?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($defectInfo as $defect):?>
            <tr>
                <td title="<?php echo $defect->code;?>" class='text-ellipsis'><?php echo common::hasPriv('defect', 'view') ? html::a(helper::createLink('defect', 'view', "defectID=$defect->id"), $defect->code, '', "data-app='project'") : $defect->code;?></td>
                <td title="<?php echo $defect->title;?>" class='text-ellipsis'><?php echo $defect->title;?></td>
                <td title="<?php echo $defect->product != 0 ? zget($products, $defect->product) :''?>" class='text-ellipsis'><?php echo  $defect->product != 0  ? zget($products, $defect->product) : '';?></td>
                <td title="<?php echo $defect->project != 0 ? zget($projects,$defect->project) : '';?>" class='text-ellipsis'><?php echo $defect->project != 0 ? zget($projects,$defect->project) : '';?></td>
                <td><?php echo zget($lang->bug->defectPriList, $defect->pri);?></td>
                <td><?php echo zget(array('0'=>'') + $lang->bug->defectSeverityList, $defect->severity);?></td>
                <td title="<?php echo zget($lang->defect->sourceList, $defect->source);?>" class='text-ellipsis'><?php echo zget($lang->defect->sourceList, $defect->source);?></td>
                <td title="<?php echo $defect->createdDate  != '0000-00-00 00:00:00' ? $defect->createdDate : '';?>" class='text-ellipsis'><?php echo $defect->createdDate  != '0000-00-00 00:00:00' ? $defect->createdDate : '';?></td>
                <td title="<?php echo zget($lang->defect->statusList, $defect->status);?>" class='text-ellipsis'>
                    <?php echo zget($lang->defect->statusList, $defect->status);?>
                </td>
                <td title="<?php echo zget($users, $defect->dealUser);?>" class='text-ellipsis'><?php echo zget($users, $defect->dealUser, '');?></td>
                <td title="<?php echo zget($lang->defect->dealSuggestList, $defect->dealSuggest);?>" class='text-ellipsis'><?php echo zget($lang->defect->dealSuggestList, $defect->dealSuggest);?></td>
                <td title="<?php echo zget($lang->defect->syncStatusList, $defect->syncStatus);?>" class='text-ellipsis'><?php echo zget($lang->defect->syncStatusList, $defect->syncStatus);?></td>
                <td class='c-actions text-center'>
                    <?php
                    common::printIcon('defect', 'edit', "defectID=$defect->id", $defect, 'list');
                    common::printIcon('defect', 'confirm', "defectID=$defect->id", $defect, 'list', 'ok', '', 'iframe', true);
                    common::printIcon('defect', 'deal', "defectID=$defect->id", $defect, 'list', 'time', '', 'iframe', true);
                    ?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
  </form>
  <?php endif;?>
</div>
<?php include '../../../common/view/footer.html.php';?>
