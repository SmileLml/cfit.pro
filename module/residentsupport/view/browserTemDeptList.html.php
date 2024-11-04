<table class='table table-fixed has-sort-head' id='residentsupports'>
    <thead>
    <tr>
        <th class='w-120px'><?php common::printOrderLink('dutyDateTime', $orderBy, $vars, $lang->residentsupport->dutyDateTime); ?></th>
        <th class='w-60px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->residentsupport->type); ?></th>
        <th class='w-80px'><?php common::printOrderLink('subType', $orderBy, $vars, $lang->residentsupport->subType); ?></th>
        <th class='w-80px'><?php common::printOrderLink('deptId', $orderBy, $vars, $lang->residentsupport->deptId); ?></th>
        <th class='w-160px'><?php common::printOrderLink('dutyGroupLeader', $orderBy, $vars, $lang->residentsupport->dutyGroupLeader); ?></th>
        <th class='w-120px'><?php common::printOrderLink('dutyUser', $orderBy, $vars, $lang->residentsupport->dutyUser); ?></th>
        <th class='w-60px'><?php common::printOrderLink('enable', $orderBy, $vars, $lang->residentsupport->enable); ?></th>
        <th class='w-60px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->residentsupport->status); ?></th>
        <th class='w-120px'><?php common::printOrderLink('dealUsers', $orderBy, $vars, $lang->residentsupport->dealUsers); ?></th>
        <!--
        <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->residentsupport->createdBy); ?></th>
        <th class='w-120px'><?php common::printOrderLink('createdTime', $orderBy, $vars, $lang->residentsupport->createdTime); ?></th>
         -->
        <th class='text-center c-actions-1 w-150px'><?php echo $lang->actions; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($reviewList as $residentsupport):
        ?>
        <tr>
            <td title="<?php echo $residentsupport->startDate . '~'.$residentsupport->endDate;?>"
                class='text-ellipsis'>
                <?php echo common::hasPriv('residentsupport', 'view') ? html::a($this->createLink('residentsupport', 'view', "templateId=$residentsupport->templateId"), $residentsupport->startDate . '~'.$residentsupport->endDate) : $residentsupport->startDate . '~'.$residentsupport->endDate; ?>
            </td>
            <td  class='text-ellipsis'><?php echo zget($lang->residentsupport->typeList, $residentsupport->type); ?></td>
            <td  class='text-ellipsis'><?php echo zget($lang->residentsupport->subTypeList, $residentsupport->subType); ?></td>
            <td class='text-ellipsis'><?php echo zget($depts, $residentsupport->deptId); ?></td>
            <?php
            $dutyGroupLeadersInfo = '';
            $subDutyGroupLeadersInfo = ''; //部分组长
            $dutyGroupLeaders = $residentsupport->dutyGroupLeaderList;
            $subCount = 2;
            if(!empty($dutyGroupLeaders)){
                $count = count($dutyGroupLeaders);
                foreach ($dutyGroupLeaders as $key => $dutyGroupLeader){
                    $deptName = $dutyGroupLeader->deptName;
                    $userName = $dutyGroupLeader->realname;
                    if($userName){
                        $dutyGroupLeadersInfo .= $deptName.'/'.$userName. " ";
                        if($key < $subCount){
                            $subDutyGroupLeadersInfo .= $deptName.'/'.$userName. " ";
                        }
                    }
                }
                if($count >= $subCount){
                    $subDutyGroupLeadersInfo .= '…';
                }
            }
            ?>
            <td title='<?php echo $dutyGroupLeadersInfo;?>' class='text-ellipsis'>
                <?php echo $subDutyGroupLeadersInfo;?>
            </td>
            <?php
            $usersNameStr = '';
            $usersNameSubStr = '';
            $tempUsers = $residentsupport->dutyUsers;
            if($tempUsers){
                $usersArray = explode(',', $tempUsers);
                //所有审核人
                $usersNameList = getArrayValuesByKeys($users, $usersArray);
                $usersNameStr .= implode(',', $usersNameList);
                $subCount = 3;
                $usersNameSubStr .= getArraySubValuesStr($usersNameList, $subCount);
            }
            ?>
            <td title='<?php echo $usersNameStr;?>' class='text-ellipsis'>
                <?php echo $usersNameSubStr;?>
            </td>
            <td  class='text-ellipsis'><?php echo zget($lang->residentsupport->enableList, $residentsupport->enable); ?></td>
            <td class='text-ellipsis'><?php echo zget($lang->residentsupport->temDeptStatusDescList, $residentsupport->status); ?></td>

            <?php
            $usersNameStr = '';
            $usersNameSubStr = '';
            $tempUsers = $residentsupport->dealUsers;
            if($tempUsers){
                $usersArray = explode(',', $tempUsers);
                //所有审核人
                $usersNameList    = getArrayValuesByKeys($users, $usersArray);
                $usersNameStr .= implode(',', $usersNameList);
                $subCount = 3;
                $usersNameSubStr .= getArraySubValuesStr($usersNameList, $subCount);
            }
            ?>
            <td title='<?php echo $usersNameStr;?>' class='text-ellipsis'>
                <?php echo $usersNameSubStr;?>
            </td>
            <!--
            <td class='text-ellipsis'><?php echo zget($users, $residentsupport->createdBy); ?></td>
            <td class='text-ellipsis'><?php echo $residentsupport->createdTime; ?></td>
            -->
            <td class='c-actions text-center'>
                <?php
                $flag = $this->loadModel('residentsupport')->isClickable($residentsupport, 'deleteDutyUser');
                $clickInfo = $flag ? 'onclick="return deleteDutyUser()"' : '';
                //编辑排班
                common::printIcon('residentsupport', 'editScheduling', "templateId=$residentsupport->templateId&schedulingDeptType=selfDept&".$params, $residentsupport, 'list', 'edit',  '', '', '','', $lang->residentsupport->editSchedulingTip);
                //申请审批
                common::printIcon('residentsupport', 'submit', "templateDeptId=$residentsupport->id", $residentsupport, 'list', 'start', '', 'iframe', true,'', $lang->residentsupport->submitTip);
                //审批
                common::printIcon('residentsupport', 'review',"templateDeptId=$residentsupport->id", $residentsupport, 'list', 'glasses', '', 'iframe', true, '', $lang->residentsupport->reviewTip);

                //删除排班
                common::printIcon('residentsupport', 'deleteDutyUser', "templateDeptId=$residentsupport->id", $residentsupport, 'list', 'trash', 'hiddenwin', '', '', "$clickInfo", $lang->residentsupport->deleteDutyUserTip);
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script type="application/javascript">
    //清空排班人员
    function deleteDutyUser() {
        if(confirm('确认要清空该该模板下该部门值班人员吗？')){
            return true;
        }
        return false;
    }
</script>