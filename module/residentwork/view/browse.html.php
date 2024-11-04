<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach ($lang->residentwork->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a($this->createLink('residentwork', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'>
            <i class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <?php if(common::hasPriv('residentwork', 'workexportAll')){?>
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('residentwork', 'workExport') ? '' : "class=disabled";
                $misc  = common::hasPriv('residentwork', 'workExport') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('residentwork', 'workExport') ? $this->createLink('residentwork', 'workExport', "browseType=$browseType&param=$param&orderBy=$orderBy") : '#';
                echo "<li $class>" . html::a($link, '导出日排班明细', '', $misc) . "</li>";
                ?>
            </ul>
        </div>
        <?php }?>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox" data-module='residentwork'></div>
        <?php if (empty($residentsupports)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php
        else:
        //搜索条件
        $params = "browseType=$browseType&param=$param&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
        ?>
        <form class='main-table' id='residentworkForm' method='post' data-ride='table' data-nested='true'
              data-checkable='false'>
            <?php
            $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";
            ?>
            <table class='table table-fixed has-sort-head' id='residentworks'>
                <tbody>
                <thead>
                <tr>
                    <th class='w-60px'><?php echo $lang->residentwork->id; ?></th>
                    <th class='w-80px'><?php echo $lang->residentwork->dutyDate; ?></th>
                    <th class='w-70px'><?php echo $lang->residentwork->type; ?></th>
                    <th class='w-70px'><?php echo $lang->residentwork->subType; ?></th>
                    <th class='w-70px'><?php echo $lang->residentwork->postTypeInfo; ?></th>
                    <th class='w-80px'><?php echo $lang->residentwork->dutyDept; ?></th>
                    <th class='w-90px'><?php echo $lang->residentwork->dutyGroupLeader; ?></th>
                    <th class='w-90px'><?php echo $lang->residentwork->dutyUser; ?></th>
                    <th class='w-80px'><?php echo $lang->residentwork->timeType; ?></th>
                    <th class='w-100px'><?php echo $lang->residentwork->dutyTime; ?></th>
                    <th class='w-140px'><?php echo $lang->residentwork->requireInfo; ?></th>
                    <th class='w-140px'><?php echo $lang->residentwork->desc; ?></th>
                    <th class='w-80px'><?php echo $lang->residentwork->pushTitle; ?></th>
                    <th class='text-center c-actions-1 w-120px'><?php echo $lang->actions; ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($residentsupports as $residentsupport): ?>
                    <tr>
                        <td class='text-ellipsis'>
                            <?php echo $residentsupport->id; ?>
                        </td>
                        <td class='text-ellipsis' title="<?php echo $residentsupport->dutyDate; ?>">
                        <?php echo common::hasPriv('residentwork', 'view') ? html::a(inlink('view', "dayId=$residentsupport->id"), $residentsupport->dutyDate) : $residentsupport->dutyDate; ?>
                        </td>
                        <?php $type = $residentsupport->templateInfo->type ?? ''; ?>
                        <?php $subType = $residentsupport->templateInfo->subType ?? ''; ?>
                        <td class='text-ellipsis' title="<?php echo zget($lang->residentsupport->typeList, $type); ?>"><?php echo zget($lang->residentsupport->typeList, $type); ?></td>
                        <td class='text-ellipsis' title="<?php echo zget($lang->residentsupport->typeList, $subType); ?>" ><?php echo zget($lang->residentsupport->subTypeList, $subType); ?></td>
                        <?php
                        $postType = '';
                        $postTypeArr = explode(',',$residentsupport->detailInfo->postType);
                        $postTypeList = getArrayValuesByKeys($lang->residentsupport->postType, $postTypeArr);
                        $postType .= implode(',', array_unique($postTypeList));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $postType; ?>"><?php echo $postType; ?></td>

                        <?php
                        $dutyUserDept = '';
                        $dutyUserDeptArr = explode(',',$residentsupport->detailInfo->dutyUserDept);
                        $dutyUserDeptList = getArrayValuesByKeys($depts, $dutyUserDeptArr);
                        $dutyUserDept .= implode(',', array_unique($dutyUserDeptList));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $dutyUserDept; ?>"><?php echo $dutyUserDept; ?></td>

                        <?php
                        $dutyGroupLeader = '';
                        $dutyGroupLeaderArr = explode(',',$residentsupport->dutyGroupLeader);
                        $dutyGroupLeaderList = getArrayValuesByKeys($users, $dutyGroupLeaderArr);
                        $dutyGroupLeader .= implode(',', array_unique($dutyGroupLeaderList));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $dutyGroupLeader; ?>"><?php echo $dutyGroupLeader; ?></td>

                        <?php
                            $dutyUser = '';
                            $userArr = explode(',',$residentsupport->detailInfo->dutyUser);
                            $dutyUserList = getArrayValuesByKeys($users, $userArr);
                            $dutyUser .= implode(',', array_unique($dutyUserList));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $dutyUser; ?>"><?php echo $dutyUser; ?></td>

                        <?php
                        $timeType = '';
                        $timeTypeArr = explode(',',$residentsupport->detailInfo->timeType);
                        $timeTypeList = getArrayValuesByKeys($lang->residentsupport->durationTypeList, $timeTypeArr);
                        $timeType .= implode(',', array_unique($timeTypeList));
                        ?>

                        <td class='text-ellipsis' title="<?php echo $timeType; ?>"><?php echo $timeType; ?></td>
                        <?php
                        $timeSlot = '';
                        $timeSlotArr = explode(',',$residentsupport->detailInfo->timeSlot);
                        $timeSlot .= implode(',', array_unique($timeSlotArr));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $timeSlot; ?>"><?php echo $timeSlot; ?></td>

                        <?php
                        $requireInfo = '';
                        $requireInfoArr = explode(',',$residentsupport->detailInfo->requireInfo);
                        $requireInfo .= implode(',', array_unique($requireInfoArr));
                        ?>
                        <td class='text-ellipsis' title="<?php echo strip_tags($requireInfo); ?>"><?php echo $requireInfo; ?></td>

                        <td class='text-ellipsis' title="<?php echo strip_tags($residentsupport->workInfo->logs) ?? ''; ?>"><?php echo $residentsupport->workInfo->logs ?? ''; ?></td>
                        <td class='text-ellipsis' title=""><?php echo $this->lang->residentwork->logPushStatusArray[$residentsupport->workInfo->pushStatus] ?? '暂未推送'?></td>
                        <td class='c-actions text-center'>
                            <?php
                                $templateDayInfo = new stdClass();
                                $templateDayInfo->id = $residentsupport->id;
                                $templateDayInfo->templateId = $residentsupport->templateId;
                                $templateDayInfo->dutyDate = $residentsupport->dutyDate;
                                $templateDayInfo->isModify = $residentsupport->templateInfo->templateDetail->isModify;
                                $templateDayInfo->status = $residentsupport->templateInfo->templateDetail->status;
                                //变更排班
                                $dutyDate = str_replace("-",',',$residentsupport->dutyDate);
                                common::printIcon('residentwork', 'modifyScheduling', "dayId=$residentsupport->id&schedulingDeptType=selfDept&".$params, $templateDayInfo, 'list', 'calendar', '');
                                common::printIcon('residentwork', 'recordDutyLog', "dutyDate=$dutyDate&dayId=$residentsupport->id", $residentsupport, 'list', 'edit', '');
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </tbody>
            </table>
            <div class="table-footer">
                <?php $pager->show('right', 'pagerjs'); ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
