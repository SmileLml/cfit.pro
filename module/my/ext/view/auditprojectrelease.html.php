<?php
/**
 * The build view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 �ൺ�����촴����Ƽ����޹�˾(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: build.html.php 4262 2013-01-24 08:48:56Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->my->myReviewList as $key => $type):?>
            <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
            <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
        <?php endforeach;?>
    </div>
</div>
<div id="mainContent">
  <?php if(empty($reviewList)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
  </div>
  <?php else:?>
  <div class='main-table' data-ride="table" data-checkable="false">
    <table class="table text" id='reviewList'>
      <thead>
        <tr>
            <th class='w-id'><?php echo $lang->release->id;?></th>
            <th><?php echo $lang->release->name;?></th>
            <th class='w-220px'><?php echo $lang->projectrelease->buildname;?></th>
            <th class='w-product'><?php echo $lang->release->product;?></th>
            <th class='w-180px'><?php echo $lang->projectrelease->productCode;?></th>
            <th class='c-date text-center w-100px'><?php echo $lang->release->date;?></th>
            <th class='text-center w-90px'><?php echo $lang->release->status;?></th>
            <th class='w-120px'><?php echo $lang->projectrelease->dealUser;?></th>
            <?php
            $extendFields = $this->loadModel('projectrelease')->getFlowExtendFields();
            foreach($extendFields as $extendField) echo "<th>{$extendField->name}</th>";
            ?>
            <th class='c-actions-2 text-center w-60px'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>

        <?php foreach($reviewList as $index => $release):?>
            <?php $canBeChanged = common::canBeChanged('projectrelease', $release);?>
            <tr>
                <td><?php echo html::a($this->createLink('projectrelease','view', "releaseID=$release->id"), sprintf('%03d', $release->id));?></td>
                <td title='<?php echo $release->name?>'>
                    <?php
                    $flagIcon = $release->marker ? "<icon class='icon icon-flag red' title='{$lang->release->marker}'></icon> " : '';
                    echo html::a($this->createLink('projectrelease', 'view', "release=$release->id"), $release->name, '', "data-app='$from'") . $flagIcon;
                    ?>
                </td>
                <td title='<?php echo $release->buildName?>'><?php echo empty($release->execution) ? $release->buildName : html::a($this->createLink('build', 'view', "buildID=$release->buildID"), $release->buildName);?></td>
                <td title='<?php echo $release->productName?>'><?php echo $release->productName?></td>
                <td title='<?php echo $release->productCode?>'><?php echo $release->productCodeInfo?></td>
                <td class='text-center'><?php echo $release->date;?></td>
                <?php $statusDesc = zget($lang->projectrelease->statusLabelList, $release->status);?>
                <td class='c-status text-center' title='<?php echo $statusDesc;?>'>
                    <span class="status-release status-<?php echo $release->status?>"><?php echo $statusDesc;?></span>
                </td>
                <td title='<?php echo $release->dealUserStr; ?>' class="text-ellipsis"><?php  echo $release->dealUserStr;?></td>
                <?php foreach($extendFields as $extendField) echo "<td>" . $this->loadModel('flow')->getFieldValue($extendField, $release) . "</td>";?>
                <td class='c-actions'>
                    <?php

                    if($canBeChanged) {
                        common::hasPriv('projectrelease', 'deal') ? common::printIcon('projectrelease', 'deal', "release=$release->id&version=$release->version&status=$release->status", $release, 'list', 'time', '', 'iframe', true,"data-width='1200px'", $lang->projectrelease->deal): '';
                    }
                    ?>
            </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
      <div class="table-footer"></div>
  <?php endif;?>
</div>
<?php include '../../../common/view/footer.html.php';?>
