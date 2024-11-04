<div class='detail'>
    <div class=" pull-right">

        <?php
        //是否具有权限
            $isAllowViewHistory = false;
            $currentModule = $this->app->rawModule;
            if(common::hasPriv($currentModule, 'checkhistoryadvice') && in_array($currentModule, ['review', 'reviewmanage'])){
                $isAllowViewHistory = true;
                $reviewModule = $currentModule;
            }else{
                if(common::hasPriv('review', 'checkhistoryadvice') || common::hasPriv('reviewmanage', 'checkhistoryadvice')) {
                    $isAllowViewHistory = true;
                    if (common::hasPriv('reviewmanage', 'checkhistoryadvice')) {
                        $reviewModule = 'reviewmanage';
                    } else {
                        $reviewModule = 'review';
                    }
                }
            }

            if($isAllowViewHistory):
            ?>
        <?php
            echo html::a($this->createLink($reviewModule, 'checkhistoryadvice', "reviewID=$review->id"), $lang->review->historyAdvie,'',"style='color: #0c60e1;'");?>
        <?php else:?>
            <!--
            <?php echo baseHTML::a('javascript:;', $lang->review->historyAdvie, "style='color: #0c60e1;' onclick='reviewCheckHistoary()'"); ?>
            -->
        <?php endif;?>
    </div>
    <div class='detail-title'><?php echo $lang->review->reviewAdvice;?></div>
    <div class="detail-content article-content ">
        <table class="table ops  table-fixed ">
            <thead>
            <tr>
                <th class='w-90px' ><?php echo $lang->review->reviewStage ;?></th>
                <th class='w-160px'><?php echo $lang->review->reviewNode ;?></th>
                <th class='w-100px'><?php  echo $lang->review->reviewPerson  ;?></th>
                <th class='w-120px'><?php echo $lang->review->reviewResult ;?></th>
                <th class='w-120px'><?php echo $lang->review->reviewOpinion  ;?></th>
                <th class='w-80px'><?php echo  $lang->review->reviewMode  ;?></th>
                <th class='w-120px'><?php echo  $lang->review->reviewDate ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(empty($reviewNodeReviewerList)):?>
                <tr>
                    <th colspan="7" style="text-align: center;"><?php echo $lang->noData;?></th>
                </tr>
            <?php else:?>

                <?php
                $currentUser = $app->user->account;
                $owner =  $review->owner; //评审主席
                $reviewer = $review->reviewer; //评审专员
                $tempUsers = [$owner, $reviewer];
                foreach($reviewNodeReviewerList as $nodeCode => $nodeData):
                    $count = $nodeData['total'];
                    $currentNode = $nodeData['data'];
                    $name = zget($lang->review->nodeStageNameList, $nodeCode);
                    echo "   <tr>";
                    echo " <th rowspan = $count>"." $name".'</th>';
                    foreach ($currentNode as $key => $current ):
                        unset($current->reviewers['total']);
                        $currentNodeReviewers = $current->reviewers; //当前节点的用户数
                        $currentSubNode = 0;
                        if(isset($current->id)){
                            $currentSubNode = $current->id;
                        }
                        //用户数量
                        $reviewedCount = count($currentNodeReviewers);
                        //第一个用户信息
                        $firstReviewerInfo =  $currentNodeReviewers[0];
                        $extraInfo = $firstReviewerInfo->extraInfo;
                        //当前审核节点的nodeCode
                        $currentNodeCode = $current->nodeCode;
                        ?>
                        <th rowspan="<?php echo $reviewedCount;?>">
                            <?php
                            echo zget($lang->review->nodeCodeNameList, $currentNodeCode,'');
                            ?>
                            <span class="action-span" style="float: right; margin-right: 10px;">
                                  <?php
                                  $params = "reviewID=$review->id&node=$currentSubNode";
                                  $review->currentSubNode = $currentSubNode;
                                  if(in_array($current->status, ['pending', 'wait'])){ //未处理
                                  if($review->type == 'dept'){
                                      $class = in_array($review->currentSubNode, $review->allowEditNodes) && (!in_array($review->status, $lang->review->notAllowEditNodeUsersStatusList)) && (common::hasPriv($this->app->rawModule, 'editNodeUsers') || in_array($currentUser, $tempUsers)) ? 'btn iframe' : 'btn iframe disabled';
                                      echo html::a(helper::createLink($this->app->rawModule, 'ajaxEditNodeUsers', $params, '', true), '<i class="icon-edit"></i>', '', "title='{$this->lang->review->editNodeUsers}' class='{$class}'");
                                  }else{
                                      common::hasPriv($this->app->rawModule, 'editNodeUsers') ? common::printIcon($this->app->rawModule, 'editNodeUsers', $params, $review, 'list','edit', '', 'iframe', true,'data-position="50px"'):'';
                                  }

                                  }else{ //处理了
                                      if($review->type == 'dept'){
                                          $class = (common::hasPriv('reviewmanage', 'editNodeInfos') || in_array($currentUser, $tempUsers)) ? 'btn iframe' : 'btn iframe disabled';
                                          echo html::a(helper::createLink('reviewmanage', 'ajaxEditNodeInfos', $params, '', true), '<i class="icon-edit"></i>', '', "title='{$this->lang->review->editNodeInfos}' class='{$class}'");
                                      }else{
                                          if(common::hasPriv('reviewmanage', 'editNodeInfos')){
                                              common::printIcon('reviewmanage', 'editNodeInfos', $params, $review, 'list','edit', '', 'iframe', true,'data-position="50px"');
                                          }
                                      }

                                  }
                                  ?>
                              </span>
                        </th>
                        <td>
                            <?php

                            $companyId = $this->loadModel('user')->getUserDeptName($firstReviewerInfo->reviewer)->company;
                            if ($companyId == 0) {
                                $deptName = $this->loadModel('user')->getUserDeptName($firstReviewerInfo->reviewer)->deptName;
                            } else {
                                $deptName = zget($companies, $companyId);
                            }
                            if(!empty($deptName)){
                                echo $deptName."/".zget($users, $firstReviewerInfo->reviewer);
                            }else{
                                echo zget($users, $firstReviewerInfo->reviewer);
                            }

                            ?>
                            <?php if(isset($firstReviewerInfo->parentId) && $firstReviewerInfo->parentId > 0 && !in_array($nodeCode, ['firstReview','firstMainReview'])):?>
                                （委托）
                            <?php endif;?>
                            <?php if(isset($firstReviewerInfo->comment) && $firstReviewerInfo->comment == '系统自动关闭'):?>
                                （系统自动处理）
                            <?php endif;?>
                        </td>


                    <?php if($currentNodeCode == 'firstAssignReviewer'):?>
                        <?php echo "<th rowspan=' $reviewedCount' style='background:white;font-weight:normal;vertical-align: middle'>";
                        $deptzs = $current->deptzs;
                        if(count($deptzs) > 0){
                            $str = '';
                            foreach ($deptzs as $key => $item) {
                                $str = $item ."&nbsp&nbsp";
                                if($key%2){
                                    $str .= '<br>';
                                }
                                echo $str;
                            }
                        }
                        echo "</th>";
                        ?>
                    <?php elseif($currentNodeCode == 'baseline'):?>
                        <?php
                        echo "<th  style='background:white;font-weight:normal;vertical-align: middle'>";
                        //已打基线和无需打基线状态
                        if($current->status == 'pending'){
                            echo $this->lang->review->needBaseline;
                        }else if($current->status == 'pass'){
                            echo  zget($lang->review->condition, $nodeData['baselineCondition']);
                        }

                        echo "</th>";
                        ?>
                    <?php else:?>

                        <td>
                            <?php if($currentNodeCode == 'firstAssignDept'):?>
                                <?php $dept = $current->dept;
                                if(isset($extraInfo['skipfirstreview']) && $dept){
                                    //是否跳过
                                    echo $extraInfo['skipfirstreview'] ."&nbsp&nbsp";
                                }else{
                                    if(isset($extraInfo['skipfirstreview']) && $review->type != 'pmo'){
                                        echo $extraInfo['skipfirstreview'] ."&nbsp&nbsp";
                                    }else if(isset($extraInfo['skipfirstreview']) && $review->type == 'pmo'){
                                        echo 'PMO咨询无需初审';
                                    } else{
                                        //部门
                                        if(count($dept) > 0){
                                            $str = '';
                                            foreach ($dept as $key=>$item) {
                                                $str = $item ."&nbsp&nbsp";
                                                if($key%2){
                                                    $str .= '<br>';
                                                }
                                                echo $str;
                                            }
                                        }else{
                                            echo zget($lang->review->confirmResultList, $firstReviewerInfo->status);
                                        }
                                    }
                                }?>
                            <?php elseif(in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList)):?>
                                <?php
                                if(!isset($extraInfo['expert']) && !isset($extraInfo['outside'])){
                                    echo zget($lang->review->confirmResultList, $firstReviewerInfo->status) ;
                                }else {
                                    if(isset($extraInfo['appoint']) && $extraInfo['appoint'] == 1){
                                        echo '已委托';
                                    }else{
                                        if (isset($extraInfo['expert'])) {
                                            $expert = explode(',', $extraInfo['expert']);
                                            foreach ($expert as $expe) {
                                                echo zget($users, $expe, '') . '&nbsp&nbsp';
                                            }
                                        }
                                        if (isset($extraInfo['reviewedBy'])) {
                                            $reviewedBy = explode(',', $extraInfo['reviewedBy']);
                                            foreach ($reviewedBy as $userAccount) {
                                                echo zget($outsideList1, $userAccount, '') . '&nbsp&nbsp';
                                            }
                                        }
                                        if (isset($extraInfo['outside'])) {
                                            $outside = explode(',', $extraInfo['outside']);
                                            foreach ($outside as $userAccount) {
                                                echo zget($outsideList2, $userAccount, '') . '&nbsp&nbsp';
                                            }
                                        }
                                    }
                                }
                                ?>
                            <?php elseif(($currentNodeCode == 'formalOwnerReview') && isset($extraInfo['grade'])):?>
                                <?php echo zget($lang->review->gradeList, $extraInfo['grade'], '');?>
                            <?php elseif($currentNodeCode == 'meetingReview' && $firstReviewerInfo->status == 'pass'):?>
                                <?php echo '';?>

                            <?php elseif($currentNodeCode == 'close'):?>
                                <?php if($closeType == 'nopass'):?>
                                <?php echo zget($this->lang->review->closeList,$current->status,'等待处理')?>
                                <?php elseif($closeType == 'pass'):?>
                                <?php echo '评审通过';?>
                                <?php endif;?>

                            <?php elseif($currentNodeCode == 'archive'):?>
                                <?php if($firstReviewerInfo->status == 'pass'):?>
                                    已归档
                                <?php else:?>
                                    <?php echo zget($lang->review->confirmResultList, $firstReviewerInfo->status) ?>
                                <?php endif;?>
                            <?php elseif(in_array($currentNodeCode, $lang->review->passButEditnodeCodeList) || $currentNodeCode == 'rejectVerifyButEdit'):?>
                                <?php if($firstReviewerInfo->status == 'pass'):?>
                                    已修改
                                <?php else:?>
                                    <?php echo zget($lang->review->confirmResultList, $firstReviewerInfo->status) ?>
                                <?php endif;?>
                            <?php else:?>
                                <?php if(isset($extraInfo['appointUser'])):?>
                                    委托 &nbsp;<?php echo zget($users, $extraInfo['appointUser']) ?>
                                <?php else:?>
                                    <?php echo zget($lang->review->confirmResultList, $firstReviewerInfo->status) ?>
                                    <?php if(isset($extraInfo['isEditInfo'])):?>
                                        <?php echo zget($lang->review->isEditInfoList, $extraInfo['isEditInfo']); ?>
                                    <?php endif;?>
                                <?php endif;?>
                            <?php endif;?>
                        </td>
                    <?php endif;?>

                        <td>
                            <!--                              --><?php //if($firstReviewerInfo->reviewer && isset($firstReviewerInfo->status))echo $firstReviewerInfo->comment; ?>
                            <?php echo $firstReviewerInfo->comment != '系统自动关闭' ? $firstReviewerInfo->comment : '';?>
                        </td>

                        <td>
                            <?php if(in_array($currentNodeCode,  $lang->review->adviceGradeNodeCodes) && isset($extraInfo['grade'])):?>
                                <?php
                                if(!empty(zget($gradeList, $extraInfo['grade'])) and $firstReviewerInfo->comment != '逾期未提交意见' and $firstReviewerInfo->comment != '逾期自动处理' ){
                                    echo '建议'.zget($gradeList, $extraInfo['grade']);
                                }
                                 ;?>
                            <?php elseif(in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList) && isset($extraInfo['grade'])):?>
                                <?php echo '确定'.zget($gradeList, $extraInfo['grade']) ?>
                            <?php endif;?>
                        </td>

                        <!--评审日期-->
                        <td>
                            <?php
                            if(isset($firstReviewerInfo->status) && !in_array($firstReviewerInfo->status, array('pending', 'wait'))):?>
                                <?php if(isset($extraInfo['reviewedDate']) && $extraInfo['reviewedDate']):?>
                                    <?php echo strlen($extraInfo['reviewedDate']) < 11 ? $extraInfo['reviewedDate'] . ' 00:00:00':$extraInfo['reviewedDate']; ?>
                                <?php elseif($firstReviewerInfo->reviewTime):?>
                                    <?php echo $firstReviewerInfo->reviewTime; ?>
                                <?php else:?>
                                    <?php echo $firstReviewerInfo->createdDate; ?>
                                <?php endif;?>
                            <?php endif;?>
                        </td>
                        </tr>
                        <?php
                        if($reviewedCount > 1):
                            for($i = 1; $i < $reviewedCount; $i++):
                                $reviewerInfo =  $currentNodeReviewers[$i];
                                $extraInfo = $reviewerInfo->extraInfo;
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        $companyId =  $this->loadModel('user')->getUserDeptName($reviewerInfo->reviewer)->company;
                                       if($companyId ==0){
                                           $deptName = $this->loadModel('user')->getUserDeptName($reviewerInfo->reviewer)->deptName;
                                       }else{
                                           $deptName = zget($companies,$companyId);
                                        }
                                        if(!empty($deptName)){
                                            echo $deptName."/".zget($users, $reviewerInfo->reviewer);
                                        }else{
                                            echo zget($users, $reviewerInfo->reviewer);
                                        }
                                        ?>
                                        <?php if(isset($firstReviewerInfo->parentId) && $reviewerInfo->parentId > 0 && !in_array($nodeCode, ['firstReview','firstMainReview'])):?>
                                            （委托）
                                        <?php endif;?>
                                    </td>
                                    <?php if($currentNodeCode != 'firstAssignReviewer'):?>
                                        <td>
                                            <?php if($currentNodeCode == 'firstAssignDept'):?>
                                                <?php $dept = $current->dept;
                                                if(isset($extraInfo['skipfirstreview'])&& $dept){
                                                    //跳过
                                                    echo $extraInfo['skipfirstreview']."&nbsp&nbsp";
                                                }else{
                                                    //部门
                                                    if(count($dept) > 0){
                                                        $str = '';
                                                        foreach ($deptzs as $key=>$item) {
                                                            $str = $item ."&nbsp&nbsp";
                                                            if($key%2){
                                                                $str .= '<br>';
                                                            }
                                                            echo $str;
                                                        }
                                                    }
                                                }?>
                                            <?php elseif(in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList)):?>
                                                <?php
                                                if(!isset($extraInfo['expert']) && !isset($extraInfo['outside'])){
                                                    echo zget($lang->review->confirmResultList, $reviewerInfo->status) ;
                                                }else {
                                                    if(isset($extraInfo['appoint']) && $extraInfo['appoint'] == 1){
                                                        echo '已委托';
                                                    } else {
                                                        if (isset($extraInfo['expert'])) {
                                                            $expert = explode(',', $extraInfo['expert']);
                                                            foreach ($expert as $expe) {
                                                                echo zget($users, $expe, '') . '&nbsp&nbsp';
                                                            }
                                                        }
                                                        if (isset($extraInfo['reviewedBy'])) {
                                                            $reviewedBy = explode(',', $extraInfo['reviewedBy']);
                                                            foreach ($reviewedBy as $userAccount) {
                                                                echo zget($outsideList1, $userAccount, '') . '&nbsp&nbsp';
                                                            }
                                                        }
                                                        if (isset($extraInfo['outside'])) {
                                                            $outside = explode(',', $extraInfo['outside']);
                                                            foreach ($outside as $userAccount) {
                                                                echo zget($outsideList2, $userAccount, '') . '&nbsp&nbsp';
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                            <?php elseif(($currentNodeCode == 'formalOwnerReview') && isset($extraInfo['grade'])):?>
                                                <?php echo zget($lang->review->gradeList, $extraInfo['grade'], '');?>
                                            <?php elseif($currentNodeCode == 'meetingReview' && $reviewerInfo->status == 'pass'):?>
                                                <?php echo '';?>
                                            <?php elseif($currentNodeCode == 'close'):?>
                                                <?php echo zget($this->lang->review->closeList, $current->status,'')?>
                                            <?php elseif($currentNodeCode == 'archive'):?>
                                                <?php if($reviewerInfo->status == 'pass'):?>
                                                    已归档
                                                <?php else:?>
                                                    <?php echo zget($lang->review->confirmResultList, $reviewerInfo->status) ?>
                                                <?php endif;?>
                                            <?php elseif(in_array($currentNodeCode, $lang->review->passButEditnodeCodeList) || $currentNodeCode == 'rejectVerifyButEdit'):?>
                                                <?php if($reviewerInfo->status == 'pass'):?>
                                                    已修改
                                                <?php else:?>
                                                    <?php echo zget($lang->review->confirmResultList, $reviewerInfo->status) ?>
                                                <?php endif;?>
                                            <?php else:?>
                                                <?php if(isset($extraInfo['appointUser'])):?>
                                                    委托 &nbsp;<?php echo zget($users, $extraInfo['appointUser']) ?>
                                                <?php else:?>
                                                    <?php echo zget($lang->review->confirmResultList, $reviewerInfo->status) ?>
                                                    <?php if(isset($extraInfo['isEditInfo'])):?>
                                                        <?php echo zget($lang->review->isEditInfoList, $extraInfo['isEditInfo']); ?>
                                                    <?php endif;?>
                                                <?php endif;?>

                                            <?php endif;?>

                                        </td>
                                    <?php endif;?>


                                    <td>
                                        <?php echo $reviewerInfo->comment; ?>
                                    </td>
                                    <td>
                                        <?php if(in_array($currentNodeCode,  $lang->review->adviceGradeNodeCodes) && isset($extraInfo['grade'])):?>
                                            <?php
                                            if(!empty(zget($gradeList, $extraInfo['grade'])) and $reviewerInfo->comment != '逾期未提交意见' and $reviewerInfo->comment != '逾期自动处理' ){
                                                echo '建议'.zget($gradeList, $extraInfo['grade']);
                                            }
                                            ; ?>
                                        <?php elseif(in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList) && isset($extraInfo['grade'])):?>
                                            <?php echo '确定'.zget($gradeList, $extraInfo['grade']) ?>
                                        <?php endif;?>
                                    </td>
                                    <td>
                                        <?php if(isset($reviewerInfo->status) && !in_array($reviewerInfo->status, array('pending', 'wait'))):?>
                                            <?php if(isset($extraInfo['reviewedDate']) && $extraInfo['reviewedDate']):?>
                                                <?php echo strlen($extraInfo['reviewedDate']) < 11 ? $extraInfo['reviewedDate'] . ' 00:00:00':$extraInfo['reviewedDate']; ?>
                                            <?php elseif($reviewerInfo->reviewTime):?>
                                            <?php echo $reviewerInfo->reviewTime; ?>
                                            <?php else:?>
                                                <?php echo $reviewerInfo->createdDate; ?>
                                            <?php endif;?>
                                        <?php endif;?>
                                    </td>
                                </tr>
                            <?php
                            endfor;
                        endif;
                        ?>
                    <?php endforeach;?>
                <?php endforeach;?>
            <?php endif;?>
            </tbody>
        </table>
    </div>
</div>