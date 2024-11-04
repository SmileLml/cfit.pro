<?php
/**
 * The create view file of deploy module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     deploy
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
    <div  class='main-content'>
        <div>
            <table class='table table-form'>
                <tr>
                    <th>周报时间</th>
                    <td><?php echo $report->reportStartDate;?>~<?php echo $report->reportEndDate;?></td>
                    <th>新建人员</th>
                    <td><?php echo zget($users, $report->createdBy, '');?></td>
                    <th>新建时间</th>
                    <td><?php echo $report->createTime; ?></td>
                    <th>编辑人员</th>
                    <td><?php echo zget($users, $report->editedBy, ''); ?></td>
                    <th>编辑时间</th>
                    <td><?php echo $report->updateTime; ?></td>

                </tr>

            </table>
        </div>

    </div>

    <div id="mainContent" class="main-row">

        <div class="main-col col-8">
            <div class='main-header' style="text-align: center">
                <span style="font-weight:bold">项目周报详情</span>
            </div>
            <div class="cell">
            <table class='table table-form'>
                <tr>
                    <th>整体进度</th>
                    <td style="width:20px">90%</td>
                    <th>处于阶段</th>
                    <td style="width: 150px"><?php $selects = $lang->weeklyreport->progressStatus;
                        $progressStatusStr = '';
                        foreach(explode(',', $report->progressStatus) as $progressStatus){
                            $progressStatusStr .= $selects[$progressStatus] . '; ';
                        }
                        echo $progressStatusStr = rtrim($progressStatusStr,'; ');
                        ?></td>
                    <th>项目状态</th>
                    <td><?php $selects = $lang->weeklyreport->insideStatus; echo $selects[$report->insideStatus];?></td>
                    <th style="width: 150px">项目状态（对外）</th>
                    <td><?php $selects = $lang->weeklyreport->outsideStatus; echo $selects[$report->outsideStatus];?></td>

                </tr>

            </table>
            </div>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title">项目进展描述</div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo $reportDesc;?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title">产品发布情况</div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php
                        $i = 1;
                        foreach ($relations as $relation) {
                            echo '['. $i .'] '.$relation->realRelease .'发布了'.$relation->productPlanCode;
                        }
                        ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title">项目移交情况</div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo $report->transDesc;?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title">备注</div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo $report->remark;?>
                    </div>
                </div>
            </div>
            <div class='main-header' style="text-align: center">
                <span style="font-weight:bold">项目风险情况</span>
            </div>
            <div class="cell">
                <table class="table ops">
                    <tbody><tr>
                        <th class="w-200px">风险描述</th>
                        <td class="w-100px">风险措施</td>
                        <td class="w-100px">风险状态</td>
                    </tr>
                    <tr>
                        <th>x</th>
                        <td>x</td>
                        <td>x</td>
                    </tr>
                    </tbody></table>
            </div>
            <div class='main-header' style="text-align: center">
                <span style="font-weight:bold">项目问题情况</span>
            </div>
            <div class="cell">
                <table class="table ops">
                    <tbody><tr>
                        <th class="w-200px">问题描述</th>
                        <td class="w-100px">建议解决方案</td>
                        <td class="w-100px">问题状态</td>
                    </tr>
                    <tr>
                        <th>x</th>
                        <td>x</td>
                        <td>x</td>
                    </tr>
                    </tbody></table>
            </div>
        </div>
        <div class="side-col col-4">
            <div class='main-header' style="text-align: center">
                <span style="font-weight:bold">项目基本信息</span>
            </div>
            <div class="cell">
                <div class="detail">
<!--                    <div class="detail-title">a</div>-->
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-140px'>开发部门</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>项目经理</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（内部）项目类型</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（内部）项目编号</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（内部）项目名称</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（内部）项目代号</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（内部）计划开始时间</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（内部）计划结束时间</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）项目类型</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）项目编号</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）项目名称</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）子项目名称</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>业务司局</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）需求方</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）承建单位</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）计划开始时间</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）计划介绍时间</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）计划开工作量<br>(人月)</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>项目里程碑</th>
                                <td>xxx</td>
                            </tr>
                            <tr>
                                <th>（外部）变化情况</th>
                                <td>xxx</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
<div style="text-align: center; padding-top: 10px">
<?php echo html::linkButton('返回','weeklyreport-index-'. $report->projectId.'.html#app=project','self','','btn btn-wide');?>
</div>
