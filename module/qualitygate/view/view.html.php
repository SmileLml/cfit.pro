<?php
/**
 * The view file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: view.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php $browseLink = $app->session->qualitygateList ? $app->session->qualitygateList : inlink('browse', "productID=$qualitygate->projectId");?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if(!isonlybody()):?>
            <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $qualitygate->code ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->qualitygate->basicinfo;?></div>
                <div class="detail-content article-content">
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->version; ?></th>
                            <td><?php echo $qualitygate->productVersionTitle; ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->productVersionBeginDate; ?></th>
                            <td><?php echo $qualitygate->productVersionBeginDate; ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->productVersionEndDate; ?></th>
                            <td><?php echo $qualitygate->productVersionEndDate; ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->productPlanDesc; ?></th>
                            <td><?php echo $qualitygate->productPlanDesc; ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->buildName; ?></th>
                            <td>
                                <?php if($qualitygate->buildId):?>
                                    <?php echo html::a($this->createLink('build', 'view', 'id=' . $qualitygate->buildId, '', true), $qualitygate->buildName, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                                <?php endif;?>
                            </td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->buildStatus; ?></th>
                            <td><?php echo zget($buildstatusList, $qualitygate->buildStatus) == '-' ? '' : zget($buildstatusList, $qualitygate->buildStatus); ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->qualitygate; ?></th>
                            <td><?php echo $this->qualitygate->diffSeverityGateResult($qualitygate->severityGate); ?>
                                <span style="padding-left: 80px"><?php echo html::a($this->createLink('report', 'qualityGateCheckResult', "projectId=$qualitygate->projectId&productId=$qualitygate->productId&productVersion=$qualitygate->productVersion&buildId=$qualitygate->buildId", '', true).'#app=project', $lang->qualitygate->clickCheckDetail, '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'");?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->qualitygate->severityTest; ?></th>
                            <td><?php echo $this->qualitygate->diffColorStatus($qualitygate->status); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class='cell'><?php include '../../common/view/action.html.php';?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($browseLink);?>
                <?php if(!$qualitygate->deleted):?>
                    <div class='divider'></div>
                    <?php
                    //common::printIcon('qualitygate', 'edit', "qualitygateID=$qualitygate->id", $qualitygate, 'button','edit', '', 'iframe', true, '', $this->lang->qualitygate->edit);
                    common::printIcon('qualitygate', 'assignedTo',  "qualitygateId=$qualitygate->id", $qualitygate, 'button', 'hand-right', '', 'iframe', true, '', $this->lang->qualitygate->assign);
                    common::printIcon('qualitygate', 'deal', "qualitygateId=$qualitygate->id", $qualitygate, 'button', 'time', '', 'iframe', true, '', $this->lang->qualitygate->todeal);
                    common::printIcon('qualitygate', 'delete', "qualitygateId=$qualitygate->id", $qualitygate, 'list', 'trash', '', 'iframe', true, '', $this->lang->qualitygate->todelete);
                    ?>
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->qualitygate->baseinfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th style="width: 110px !important;"><?php echo $lang->qualitygate->belongProject  ?></th>
                            <td><?php echo $qualitygate->projectName ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->qualitygate->productName; ?></th>
                            <td><?php echo $qualitygate->productName ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->qualitygate->productCode; ?></th>
                            <td><?php echo $qualitygate->productCode; ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->qualitygate->severityTestUser; ?></th>
                            <td><?php echo zget($users, $qualitygate->severityTestUser);?></td>
                        </tr>


                        <tr>
                            <th><?php echo $lang->qualitygate->dealUser; ?></th>
                            <td><?php echo zget($users, $qualitygate->dealUser); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->qualitygate->createdBy; ?></th>
                            <td><?php echo zget($users, $qualitygate->createdBy); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->qualitygate->createdDept; ?></th>
                            <td><?php echo zget($deptInfo, 'name'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->qualitygate->createdTime; ?></th>
                            <td><?php echo $qualitygate->createdTime; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->qualitygate->editedtime; ?></th>
                            <td><?php echo $qualitygate->editedtime !=  '0000-00-00 00:00:00' ? $qualitygate->editedtime: ''?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>
<?php include '../../common/view/syntaxhighlighter.html.php';?>
<?php include '../../common/view/footer.html.php';?>
