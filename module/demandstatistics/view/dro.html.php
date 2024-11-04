<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php include '../../opinion/lang/zh-cn.php'; ?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    .tableWrap{
        overflow: auto;
    }
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text">综合信息表</span>
        </div>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='side-col' id='sidebar'>
        <?php
        include 'blockreportlist.html.php';
        $this->app->loadLang('opinion');
        $this->app->loadConfig('opinion');
        $this->loadModel('requirement');
        $this->app->loadConfig('requirement');
        $this->app->loadLang('requirement');
        $this->loadModel('demand');
        $this->app->loadConfig('demand');
        $this->app->loadLang('demand');
        ?>

    </div>
    <div class='main-col'>
        <div class="cell">
            <div class="with-padding">
                <div class="table-row">
                    <form method="post">
                        <div class="col-md-2" style="width:310px;">
                            <div class="input-group">
                                <span class='input-group-addon'><?php echo $lang->demandstatistics->startDate; ?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('startDate', $startDate, "class='form-control form-date'"); ?></div>
                            </div>
                        </div>
                        <div class="col-md-2" style="width:310px;">
                            <div class="input-group">
                                <span class='input-group-addon'><?php echo $lang->demandstatistics->endDate; ?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('endDate', $endDate, "class='form-control form-date'"); ?></div>
                            </div>
                        </div>
                        <div class='col-md-2'><?php echo html::commonButton($lang->searchAB, '', 'btn btn-primary btnSubmit'); ?></div>
                    </form>
                    <?php if (common::hasPriv('demandstatistics', 'export')): ?>
                    <div class='col-md-2'><?php
                        $startDate = strtotime($startDate);
                        $endDate = strtotime($endDate);
                        echo html::a($this->createLink('demandstatistics', 'export', "startDate=$startDate&endDate=$endDate"), "<i class='icon-push'></i> {$lang->export}", '', "data-toggle='modal' data-type='iframe' class='btn btn-primary pull-right'", '');
                        ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class='cell'>
            <div class='panel'>
                <div data-ride='table' class="tableWrap">
                    <table class='table table-condensed table-striped table-bordered table-fixed no-margin'
                           id='productList'>
                        <thead>
                        <tr class="text-center">

                            <th class='w-100px'><?php echo $lang->demandstatistics->opinionCode; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->opinionDemandCode; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->opinionName; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->opinionOverview; ?></th>
                            <th class='w-120px'><?php echo $lang->demandstatistics->opinionStatus; ?></th>
                            <th class='w-120px'><?php echo $lang->demandstatistics->opinionCreatedDate; ?></th>
                            <th class='w-110px'><?php echo $lang->demandstatistics->opinionCreatedBy; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->opinionCategory; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->opinionUrgency; ?></th>
                            <th class='w-110px'><?php echo $lang->demandstatistics->opinionAssignedTo; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->opinionSourceMode; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->opinionSourceName; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->opinionUnion; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->opinionDate; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->opinionReceiveDate; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->opinionDeadline; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->opinionEnd; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->opinionSolvedTime; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->opinionOnlineTimeByDemand; ?></th>
                            <th class='w-120px'><?php echo $lang->demandstatistics->opinionType; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->requirementCode; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->requirementEntriesCode; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->requirementName; ?></th>
                            <th class='w-220px'><?php echo $lang->demandstatistics->requirementDesc; ?></th>
                            <th class='w-120px'><?php echo $lang->demandstatistics->requirementStatus; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->requirementCreatedDate; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->requirementStartTime; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->finalPublishedTime; ?></th>
                            <th class='w-110px'><?php echo $lang->demandstatistics->requirementCreatedBy; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->requirementApp; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->requirementProductManager; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->requirementSourceMode; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->requirementAcceptTime; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->requirementDeadLine; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->requirementEnd; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->requirementFeedbackStatus; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->requirementFeedbackEnd; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->requirementSolvedTime; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->requirementOnlineTimeByDemand; ?></th>
                            <th class='w-120px'><?php echo $lang->demandstatistics->requirementType; ?></th>
                            <th class='w-120px'><?php echo $lang->demandstatistics->requireStartTime; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->demandCode; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->demandTitle; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->demandDesc; ?></th>
                            <th class='w-120px'><?php echo $lang->demandstatistics->demandStatus; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->demandCreatedDate; ?></th>
                            <th class='w-110px'><?php echo $lang->demandstatistics->demandCreatedBy; ?></th>
                            <th class='w-100px'><?php echo $lang->demandstatistics->demandFixType; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->demandProject; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->demandProduct; ?></th>
                            <th class='w-200px'><?php echo $lang->demandstatistics->demandProductPlan; ?></th>
<!--                            <th class='w-100px'>--><?php //echo $lang->demandstatistics->demandEndDate; ?><!--</th>-->
                            <th class='w-100px'><?php echo $lang->demandstatistics->demandEnd; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->demandSolvedTime; ?></th>
                            <th class='w-150px'><?php echo $lang->demandstatistics->demandActualOnlineDate; ?></th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $id => $item): ?>
                            <tr class="text-center">
                                <td title="<?php echo $item->opinionCode; ?>"><?php echo $item->opinionCode; ?></td>
                                <td title="<?php echo $item->opinionDemandCode; ?>"><?php echo $item->opinionDemandCode; ?></td>
                                <td title="<?php echo $item->opinionName; ?>"><?php echo $item->opinionName; ?></td>
                                <td title='<?php echo strip_tags($item->opinionOverview); ?>'><?php echo strip_tags($item->opinionOverview); ?></td>
                                <td><?php echo zget($this->lang->opinion->statusList, $item->opinionStatus, $item->opinionStatus); ?></td>
                                <td title="<?php echo $item->opinionCreatedDate; ?>"><?php echo $item->opinionCreatedDate; ?></td>
                                <td title="<?php echo zget($users, $item->opinionCreatedBy, $item->opinionCreatedBy); ?>"><?php echo zget($users, $item->opinionCreatedBy, $item->opinionCreatedBy); ?></td>
                                <td title="<?php echo zget($lang->opinion->categoryList, $item->opinionCategory, ''); ?>"><?php echo zget($lang->opinion->categoryList, $item->opinionCategory, ''); ?></td>
                                <td title="<?php echo $item->opinionUrgency; ?>"><?php echo $item->opinionUrgency; ?></td>
                                <?php
                                $item->opinionAssignedTo = array_filter(explode(',', $item->opinionAssignedTo));
                                $opinionAssignedTo = '';
                                foreach ($item->opinionAssignedTo as $assignedTo){
                                    $opinionAssignedTo .= zget($users, $assignedTo, $assignedTo) . '，';
                                }
                                $item->opinionAssignedTo = mb_substr($opinionAssignedTo, 0, -1);
                                ?>
                                <td title="<?php echo $item->opinionAssignedTo; ?>"><?php echo $item->opinionAssignedTo; ?></td>
                                <td title="<?php echo zget($lang->opinion->sourceModeListOld, $item->opinionSourceMode, ''); ?>"><?php echo zget($lang->opinion->sourceModeListOld, $item->opinionSourceMode, ''); ?></td>
                                <td title="<?php echo $item->opinionSourceName; ?>"><?php echo $item->opinionSourceName; ?></td>
                                <?php
                                $unionList = explode(',', trim(str_replace(' ', '', $item->opinionUnion), ','));
                                $opinionUnion = '';
                                foreach ($unionList as $union) {
                                    if($union) $opinionUnion .= ' ' . zget($lang->opinion->unionList, $union, '');
                                }
                                ?>
                                <td title="<?php echo $opinionUnion; ?>"><?php echo $opinionUnion; ?></td>
                                <td title="<?php echo $item->opinionDate; ?>"><?php echo $item->opinionDate; ?></td>
                                <td title="<?php echo $item->opinionReceiveDate; ?>"><?php echo $item->opinionReceiveDate; ?></td>
                                <td title="<?php echo $item->opinionDeadline; ?>"><?php echo $item->opinionDeadline; ?></td>
                                <td title="<?php echo $item->opinionEnd; ?>"><?php echo $item->opinionEnd; ?></td>
                                <td title="<?php echo $item->opinionSolvedTime; ?>"><?php echo $item->opinionSolvedTime; ?></td>
                                <?php
                                $item->opinionOnlineTimeByDemand = 'online' == $item->opinionStatus ? $item->opinionOnlineTimeByDemand : '';
                                ?>
                                <td title="<?php echo $item->opinionOnlineTimeByDemand; ?>"><?php echo $item->opinionOnlineTimeByDemand; ?></td>
                                <td title="<?php echo $item->opinionType; ?>"><?php echo $item->opinionType; ?></td>
                                <td title="<?php echo $item->requirementCode; ?>"><?php echo $item->requirementCode; ?></td>
                                <td title="<?php echo $item->requirementEntriesCode; ?>"><?php echo $item->requirementEntriesCode; ?></td>
                                <td title="<?php echo $item->requirementName; ?>"><?php echo $item->requirementName; ?></td>
                                <td title="<?php echo strip_tags($item->requirementDesc); ?>"><?php echo strip_tags($item->requirementDesc); ?></td>
                                <td><?php echo  zget($lang->requirement->statusList, $item->requirementStatus, $item->requirementStatus); ?></td>
                                <td title="<?php echo $item->requirementCreatedDate; ?>"><?php echo $item->requirementCreatedDate; ?></td>
                                <td title="<?php echo $item->requirementStartTime; ?>"><?php echo $item->requirementStartTime; ?></td>
                                <td title="<?php echo $item->finalPublishedTime; ?>"><?php echo $item->finalPublishedTime; ?></td>
                                <td><?php echo zget($users, $item->requirementCreatedBy, $item->requirementCreatedBy); ?></td>
                                <?php
                                if(!empty($item->requirementApp)){
                                    $appName = '';
                                    $item->requirementApp = explode(',', $item->requirementApp);
                                    foreach ($item->requirementApp as $app) {
                                        if ($app) $appName .= zget($apps, $app, '') . '，';
                                    };
                                    $item->requirementApp = mb_substr($appName, 0, -1);
                                }
                                ?>
                                <td title="<?php echo $item->requirementApp; ?>"><?php echo $item->requirementApp; ?></td>
                                <td><?php echo zget($users, $item->requirementProductManager, $item->requirementProductManager); ?></td>
<!--                                <td>--><?php //echo zget($users, $item->requirementOwner, $item->requirementOwner); ?><!--</td>-->
                                <td><?php echo zget($this->lang->opinion->sourceModeList, $item->requirementSourceMode, $item->requirementSourceMode); ?></td>
                                <td title="<?php echo $item->requirementAcceptTime; ?>"><?php echo $item->requirementAcceptTime; ?></td>
                                <?php
                                if ('0000-00-00' == $item->requirementDeadLine || empty($item->requirementDeadLine)) {
                                    $item->requirementDeadLine = 'guestcn' == $item->requirementCreatedBy ? $item->opinionDeadline : '';
                                }
                                ?>
                                <td title="<?php echo $item->requirementDeadLine; ?>"><?php echo $item->requirementDeadLine; ?></td>
                                <td title="<?php echo $item->requirementEnd; ?>"><?php echo $item->requirementEnd; ?></td>
                                <td title="<?php echo $item->requirementFeedbackStatus; ?>"><?php echo $item->requirementFeedbackStatus; ?></td>
                                <td title="<?php echo $item->requirementFeedbackEnd; ?>"><?php echo $item->requirementFeedbackEnd; ?></td>
                                <td><?php echo zget($solvedTime, $item->requirementID, ''); ?></td>
                                <td title="<?php echo $item->requirementOnlineTimeByDemand; ?>"><?php echo $item->requirementOnlineTimeByDemand; ?></td>
                                <td title="<?php echo $item->requirementType; ?>"><?php echo $item->requirementType; ?></td>
                                <td title="<?php echo $item->requireStartTime != '0000-00-00' ? $item->requireStartTime:''; ?>"><?php echo $item->requireStartTime != '0000-00-00' ? $item->requireStartTime:''; ?></td>
                                <td title="<?php echo $item->demandCode; ?>"><?php echo $item->demandCode; ?></td>
                                <td title="<?php echo $item->demandTitle; ?>"><?php echo $item->demandTitle; ?></td>
                                <td title="<?php echo strip_tags($item->demandDesc); ?>"><?php echo strip_tags($item->demandDesc); ?></td>
                                <td><?php echo zget($this->lang->demand->statusList, $item->demandStatus, $item->demandStatus); ?></td>
                                <td title="<?php echo $item->demandCreatedDate; ?>"><?php echo $item->demandCreatedDate; ?></td>
                                <td><?php echo zget($users, $item->demandCreatedBy, $item->demandCreatedBy); ?></td>
                                <td><?php echo zget($this->lang->demand->fixTypeList, $item->demandFixType, $item->demandFixType); ?></td>
                                <?php
                                if(!empty($item->demandProject)){
                                    $projectName = '';
                                    $item->demandProject = explode(',', trim($item->demandProject, ','));
                                    foreach ($item->demandProject as $projectId){
                                        if($projectList[$projectId]) $projectName .= $projectList[$projectId] . '，';

                                    }
                                    $item->demandProject = mb_substr($projectName, 0, -1);
                                }
                                $item->demandProduct     = $productList[$item->demandProduct]         ?? '';
                                $item->demandProductPlan = $productPlanList[$item->demandProductPlan] ?? '';
                                ?>
                                <td title="<?php echo $item->demandProject; ?>"><?php echo $item->demandProject; ?></td>
                                <td title="<?php echo $item->demandProduct; ?>"><?php echo $item->demandProduct; ?></td>
                                <td title="<?php echo $item->demandProductPlan; ?>"><?php echo $item->demandProductPlan; ?></td>
<!--                                <td title="--><?php //echo $item->demandEndDate; ?><!--">--><?php //echo $item->demandEndDate; ?><!--</td>-->
                                <td title="<?php echo $item->demandEnd; ?>"><?php echo $item->demandEnd; ?></td>
                                <td title="<?php echo $item->demandSolvedTime; ?>"><?php echo $item->demandSolvedTime; ?></td>
                                <td title="<?php echo $item->demandActualOnlineDate; ?>"><?php echo $item->demandActualOnlineDate; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs');?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script>
    $("form").submit(function(event){
        var start = $('#startDate').val();
        var end = $('#endDate').val();

        if(end != '' && start > end){
            js:alert('开始日期不能大于结束日期！');
            return false;
        }
    })

    $('.btnSubmit').click(function (){
        var start = $('#startDate').val();
        var end = $('#endDate').val();

        if(end != '' && start > end){
            js:alert('开始日期不能大于结束日期！');
            return false;
        }
        start = start != '' ? (new Date(start).getTime())/1000 : '';
        end   = end != '' ? (new Date(end).getTime())/1000 : '';
        window.location.href = createLink('demandstatistics', 'dro', 'startDate='+start+'&endDate='+end, '', '');
    })
</script>
