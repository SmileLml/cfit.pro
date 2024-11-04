<?php include '../../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">

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
        <?php common::printLink('residentwork', 'createlog', "", "<i class='icon icon-plus'></i>" . $lang->residentwork->create , '', "class='btn btn-primary'");?>
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
                    <th class='w-80px'><?php echo $lang->residentwork->dutyDept; ?></th>
                    <th class='w-90px'><?php echo $lang->residentwork->dutyGroupLeader; ?></th>
                    <th class='w-90px'><?php echo $lang->residentwork->dutyUser; ?></th>
                    <th class='w-140px'><?php echo $lang->residentwork->desc; ?></th>
                    <th class='w-80px'><?php echo $lang->residentwork->pushTitle; ?></th>
                    <th class='w-80px'><?php echo $lang->residentwork->logSource; ?></th>
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
                        <td class='text-ellipsis' title="<?php echo zget($lang->residentsupport->typeList, $residentsupport->type); ?>"><?php echo zget($lang->residentsupport->typeList, $residentsupport->type); ?></td>
                        <td class='text-ellipsis' title="<?php echo zget($lang->residentsupport->subTypeList, $residentsupport->subType); ?>" ><?php echo zget($lang->residentsupport->subTypeList, $residentsupport->subType); ?></td>


                        <?php
                        $dutyUserDept = '';
                        $dutyUserDeptArr = explode(',',$residentsupport->realDutyuserDept);
                        $dutyUserDeptList = getArrayValuesByKeys($depts, $dutyUserDeptArr);
                        $dutyUserDept .= implode(',', array_unique($dutyUserDeptList));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $dutyUserDept; ?>"><?php echo $dutyUserDept; ?></td>

                        <?php
                        $dutyGroupLeader = '';
                        $dutyGroupLeaderArr = explode(',',$residentsupport->groupLeader);
                        $dutyGroupLeaderList = getArrayValuesByKeys($users, $dutyGroupLeaderArr);
                        $dutyGroupLeader .= implode(',', array_unique($dutyGroupLeaderList));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $dutyGroupLeader; ?>"><?php echo $dutyGroupLeader; ?></td>

                        <?php
                            $dutyUser = '';
                            $userArr = explode(',',$residentsupport->dutyUser);
                            $dutyUserList = getArrayValuesByKeys($users, $userArr);
                            $dutyUser .= implode(',', array_unique($dutyUserList));
                        ?>
                        <td class='text-ellipsis' title="<?php echo $dutyUser; ?>"><?php echo $dutyUser; ?></td>

                        <td class='text-ellipsis' title="<?php echo strip_tags($residentsupport->logs) ?? ''; ?>"><?php echo strip_tags($residentsupport->logs) ?? ''; ?></td>
                        <td class='text-ellipsis' title=""><?php echo $this->lang->residentwork->logPushStatusArray[$residentsupport->pushStatus] ?? '暂未推送'?></td>
                        <td class='text-ellipsis' title="">用户创建</td>
                        <td class='c-actions text-center'>
                            <?php
                                $templateDayInfo = new stdClass();
                                $templateDayInfo->id = $residentsupport->id;
                                $templateDayInfo->createdBy = $residentsupport->createdBy;
                                //变更排班
                                $dutyDate = str_replace("-",',',$residentsupport->dutyDate);
//                                common::printIcon('residentwork', 'modifyScheduling', "dayId=$residentsupport->id&schedulingDeptType=selfDept&".$params, $templateDayInfo, 'list', 'calendar', '');
                                common::printIcon('residentwork', 'editlog', "id=$residentsupport->id", $residentsupport, 'list', 'edit', '');
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
<?php include '../../../common/view/footer.html.php'; ?>
