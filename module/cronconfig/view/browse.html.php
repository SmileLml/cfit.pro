<?php
/**
 * The index view file of cron module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     cron
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $hiddenLabelList = [];
        $i = 0;
        foreach($lang->cronconfig->labelList as $label => $labelName)
        {
            $active = $browseType == $label ? 'btn-active-text' : '';
            $i++;
            if($i < 13){
                echo html::a($this->createLink('cronconfig', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
            }else{
                $hiddenLabelList[$label] = $labelName;
            }
        }

        if(!empty($hiddenLabelList)){
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            foreach($hiddenLabelList   as $label => $labelName) {
                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink('cronconfig', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'"). '</li>';
            }
            echo '</ul></div>';

        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <div class="btn-toolbar pull-right">

        <?php if(common::hasPriv('cronconfig', 'create')) echo html::a($this->createLink('cronconfig', 'create'), "<i class='icon-plus'></i> {$lang->cronconfig->create}", '', "class='btn btn-primary'");?>
    </div>
</div>
<div id='mainContent' class='main-content'>
    <div class='main-col'>
        <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='cronconfig'></div>
        <table class='table table-condensed table-bordered table-fixed main-table'>
            <thead>
              <tr>
                <th class='w-60px'><?php echo $lang->cronconfig->ID;?></th>
                <th><?php echo $lang->cronconfig->command?></th>
                <th class='w-200px'><?php echo $lang->cronconfig->remark?></th>
                <th class='w-60px'><?php echo $lang->cronconfig->status?></th>
                <th class='w-100px'><?php echo $lang->actions;?></th>
              </tr>
            </thead>
            <tbody class='text-center'>
            <?php foreach($crons as $cron):?>
              <tr>
               <td><?php echo $cron->id;?></td>
                <td class='text-left' title='<?php echo $cron->command?>'>
                    <?php echo common::hasPriv('cronconfig', 'view') ?  html::a(inlink('view', "changeID=$cron->id"), $cron->command) : $cron->command;?>
                </td>
                <td class='text-left' title='<?php echo $cron->remark?>'><?php echo $cron->remark;?></td>
                <td><?php echo zget($lang->cronconfig->statusList, $cron->status, '');?></td>
                <td class='text-center'>
                <?php
                    common::printIcon('cronconfig', 'edit', "cronID=$cron->id", $cron, 'list', '', '', 'iframe', true);
                    common::printIcon('cronconfig', 'delete', "cronID=$cron->id", $cron, 'list', 'trash', '', 'iframe', true);
                ?>
                </td>
              </tr>
            <?php endforeach;?>
            </tbody>
          </table>
        <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
    </div>


</div>
<?php include '../../common/view/footer.html.php';?>
