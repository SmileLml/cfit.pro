<?php
/**
 * The browse view file of testreport module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     testreport
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datatable.fix.html.php'; ?>

<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach($lang->productionchange->labelList as $label => $labelName)
        {
            $active = $browseType == strtolower($label) ? 'btn-active-text' : '';
            echo html::a(inlink('browse', "browseType=$label&param=0&param=$param&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

            $i++;
            if($i >= 10) break;
        }
        if($i>=10)
        {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach($lang->productionchange->labelList as $label => $labelName)
            {
                $i++;
                if($i <= 10) continue;

                $active = $browseType == strtolower($label) ? 'btn-active-text' : '';
                echo '<li>' . html::a(inlink('browse', "browseType=$label&param=0&param=$param&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('productionchange', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('productionchange', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('productionchange', 'export') ? $this->createLink('productionchange', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->productionchange->export, '', $misc) . "</li>";
                ?>
            </ul>

            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('productionchange', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('productionchange', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('productionchange', 'export') ? $this->createLink('productionchange', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->productionchange->export, '', $misc) . "</li>";
                ?>
            </ul>
        </div>
        <?php if (common::hasPriv('productionchange', 'create')) echo html::a($this->createLink('productionchange', 'create'), "<i class='icon-plus'></i> {$lang->productionchange->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox" data-module='productionchange'></div>
        <?php if (empty($info)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='productionchangeForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='productionchanges'>
                    <thead>
                    <tr>
                        <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->productionchange->code); ?></th>
                        <th class='w-200px'><?php common::printOrderLink('abstract', $orderBy, $vars, $lang->productionchange->abstract); ?></th>
                        <th class='w-180px'><?php common::printOrderLink('application', $orderBy, $vars, $lang->productionchange->application); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('applicant', $orderBy, $vars, $lang->productionchange->applicant); ?></th>
                        <th class='w-140px'><?php common::printOrderLink('applicantDept', $orderBy, $vars, $lang->productionchange->applicantDept); ?></th>
<!--                        <th class='w-100px'>--><?php //common::printOrderLink('onlineType', $orderBy, $vars, $lang->productionchange->onlineType); ?><!--</th>-->
<!--                        <th class='w-90px'>--><?php //common::printOrderLink('createdBy', $orderBy, $vars, $lang->productionchange->createdBy); ?><!--</th>-->
                        <th class='w-140px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->productionchange->createdDate); ?></th>
                        <th class='w-140px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->productionchange->status); ?></th>
                        <th class='w-110px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->productionchange->dealUser); ?></th>
                        <th class='text-center w-250px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($info as $productionchange): ?>
                        <tr>
                            <td title='<?php echo $productionchange->code; ?>'><?php echo common::hasPriv('productionchange', 'view') ? html::a(inlink('view', "preproductionID=$productionchange->id"), $productionchange->code) : $productionchange->code; ?></td>
                            <td title='<?php echo $productionchange->abstract; ?>'><?php echo $productionchange->abstract; ?></td>
                            <td title='<?php echo zmget($apps,$productionchange->application); ?>'><?php echo zmget($apps,$productionchange->application); ?></td>
                            <td><?php echo zget($users,$productionchange->applicant); ?></td>
                            <td><?php echo zget($depts,$productionchange->applicantDept); ?></td>
<!--                            <td>--><?php //echo zget($lang->productionchange->onlineTypeList,$productionchange->onlineType); ?><!--</td>-->
<!--                            <td title='--><?php //echo zget($users,$productionchange->createdBy); ?><!--'>--><?php //echo zget($users,$productionchange->createdBy); ?><!--</td>-->
                            <td title='<?php echo $productionchange->createdDate; ?>'><?php echo $productionchange->createdDate; ?></td>
                            <td><?php echo zget($lang->productionchange->statusList,$productionchange->status); ?></td>
                            <td title='<?php echo zmget($users,$productionchange->dealUser); ?>'><?php echo zmget($users,$productionchange->dealUser); ?></td>
                            <td class='c-actions text-center' style="overflow:visible">
                                <?php
                                $account = $this->app->user->account;
                                common::printIcon('productionchange', 'edit', "preproductionID=$productionchange->id", $productionchange, 'list');

                                if(in_array($productionchange->status,array('wait','feedback'))  and strstr($productionchange->dealUser, $account) !== false){
                                    echo '<button type="button" class="btn" title="' . $lang->productionchange->deal . '" onclick="isClickable('.$productionchange->id.', \'deal\')"><i class="icon-common-suspend icon-time"></i></button>';
                                    common::printIcon('productionchange', 'deal', "preproductionID=$productionchange->id", $productionchange, 'list', 'time', '', 'iframe hidden', true, 'id=isClickable_deal' . $productionchange->id);
                                }else{
                                    common::printIcon('productionchange', 'deal', "preproductionID=$productionchange->id", $productionchange, 'list', 'time', '', 'iframe', true);
                                }

                                common::printIcon('productionchange', 'review', "preproductionID=$productionchange->id", $productionchange, 'list', 'glasses', '', 'iframe', true);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../../common/view/footer.html.php';?>
