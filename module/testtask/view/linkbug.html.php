<?php
/**
 * The linkcase view file of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: linkcase.html.php 4411 2013-02-22 00:56:04Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<div id='mainMenu' class='clearfix'>
    <div class='btn-toolbar pull-left'>
        <?php common::printBack($this->session->testtaskList, 'btn btn-link'); ?>
        <div class='divider'></div>


        <?php if($type == 'all')
        {
            echo "<a class='btn btn-link querybox-toggle' id='bysearchTab'><i class='icon icon-search muted'></i>{$lang->testtask->bySearch}</a>";
        }?>
    </div>
</div>
<div class="cell show" id="queryBox" data-module='testtask_link_bug'></div>
<div id='mainContent'>
    <form class='main-table table-case' data-ride='table' method='post' id='linkCaseForm'>
        <table class='table tablesorter'>
            <div class="table-header">
                <i class="icon-unlink"></i> &nbsp;<strong><?php echo $lang->testtask->unlinkedCases; ?></strong> (<?php echo $pager->recTotal; ?>)
            </div>
            <thead>
                <tr>
                    <th class='c-id'>
                        <?php if($bugs):?>
                        <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
                            <label></label>
                        </div>
                        <?php endif; ?>
                        <?php echo $lang->idAB; ?>
                    </th>
                    <th class='w-80px text-center'>
                        <nobr><?php echo $lang->bug->severity; ?></nobr>
                    </th>
                    <th class='c-pri'><?php echo $lang->priAB; ?></th>
                    <th><?php echo $lang->bug->confirmed; ?></th>
                    <th><?php echo $lang->bug->title; ?></th>
                    <th class='c-status'><?php echo $lang->statusAB; ?></th>
                    <th class='c-user'><?php echo $lang->openedByAB; ?></th>
                    <th class='c-user'><?php echo $lang->bug->openedDate; ?></th>
                    <th class='c-user'><?php echo $lang->bug->assignedTo; ?></th>
                    <th class=''><?php echo $lang->bug->resolution; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $canView = common::hasPriv('bug', 'view');?>
                <?php foreach($bugs as $bug):?>
                <?php $bugLink = helper::createLink('bug', 'view', "bugID=$bug->id"); ?>
                <tr>
                    <td class='cell-id'>
                        <?php echo html::checkbox('bugs', [$bug->id => sprintf('%03d', $bug->id)]); ?>
                    </td>
                    <td class='text-center'>
                        <?php
                    $severityValue     = zget($this->lang->bug->severityList, $bug->severity);
                    $hasCustomSeverity = !is_numeric($severityValue);
                    if($hasCustomSeverity)
                    {
                        echo "<span class='label-severity-custom' data-severity='{$bug->severity}' title='" . $severityValue . "'>" . $severityValue . "</span>";
                    }
                    else
                    {
                        echo "<span class='label-severity' data-severity='{$bug->severity}' title='" . $severityValue . "'></span>";
                    }
                    ?>
                    </td>
                    <td>
                        <?php
                        echo "<span class='label-pri label-pri-" . $bug->pri . "' title='" . zget($this->lang->bug->priList, $bug->pri, $bug->pri) . "'>";
                        echo zget($this->lang->bug->priList, $bug->pri, $bug->pri);
                        echo "</span>";
                    ?>
                    </td>
                    <td>
                        <?php
                        $class = 'confirm' . $bug->confirmed;
                    echo "<span class='$class'>" . zget($this->lang->bug->confirmedList, $bug->confirmed, $bug->confirmed) . "</span> ";

                    ?>
                    </td>
                    <td>
                        <?php
                        if($bug->module and isset($modulePairs[$bug->module]))
                        {
                            echo "<span class='label label-gray label-badge'>{$modulePairs[$bug->module]}</span> ";
                        }
                        echo $canView ? html::a($bugLink, $bug->title, null, "style='color: $bug->color'") : "<span style='color: $bug->color'>{$bug->title}</span>";

                    ?>
                    </td>
                    <td>
                        <?php
                    echo "<span class='status-bug status-{$bug->status}'>";
                    echo $this->processStatus('bug', $bug);
                    echo  '</span>';
                    ?>
                    </td>
                    <td>
                        <?php echo zget($users, $bug->openedBy); ?>
                    </td>
                    <td>
                        <?php echo substr($bug->openedDate, 5, 11); ?>
                    </td>
                    <td>
                        <?php $this->loadModel('bug')->printAssignedHtml($bug, $users); ?>
                    </td>
                    <td>
                        <?php echo zget($this->lang->bug->resolutionList, $bug->resolution); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if($bugs):?>
        <div class='table-footer'>
            <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
            <div class="table-actions btn-toolbar show-always"><?php echo html::submitButton('', '', 'btn'); ?></div>
            <div class="table-statistic"></div>
            <?php $pager->show('right', 'pagerjs'); ?>
        </div>
        <?php endif; ?>
    </form>
</div>
<?php include '../../common/view/footer.html.php'; ?>