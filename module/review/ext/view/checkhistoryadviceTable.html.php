<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $review->id ?></span>
            <span class="text"><?php echo $review->title; ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class='detail'>
                <div class='detail-title'><?php echo $lang->review->reviewAdvice; ?>
                </div>
                <div class="detail-content article-content ">
                    <?php for ($j = $maxVersion; $j >= 0; $j--): ?>
                        <div class="page-title">
                              <span class="text">
                                   <?php
                                   //获取最新日期
                                   $endDate = $this->review->getNodeEndDate('review', $review->id, $j)->createdDate;
                                   $startDate = $this->review->getNodeStartDate('review', $review->id, $j)->createdDate;
                                   $endDate = date('Y-m-d', strtotime($endDate));
                                   $startDate = date('Y-m-d', strtotime($startDate));
                                   $count =0;
                                   $version = $j+$count;
                                   ?>
                                  <?php if (!empty($this->review->getNodeEndDate('review', $review->id, $j)->createdDate)): ?>
                                      <?php
                                      //获取最新日期
                                      if(empty($startDate)){
                                          echo $version . "版本，起止日期" . $review->createdDate . "~" . $endDate;
                                      }else{
                                          echo $version . "版本，起止日期" . $startDate . "~" . $endDate;
                                      }
                                      ?>
                                      <a href='#collapseExample<?php echo $j ?> ' data-toggle="collapse"
                                         class="btn btn-link">展开\隐藏</a>
                                  <?php endif; ?>
                              </span></div>
                            <div class="collapse" id='collapseExample<?php echo $j ?>'>
                                <table class="table ops  table-fixed ">
                                    <thead>
                                    <tr>
                                        <th class='w-90px'><?php echo $lang->review->reviewStage; ?></th>
                                        <th class='w-160px'><?php echo $lang->review->reviewNode; ?></th>
                                        <th class='w-100px'><?php echo $lang->review->reviewPerson; ?></th>
                                        <th class='w-120px'><?php echo $lang->review->reviewResult; ?></th>
                                        <th class='w-120px'><?php echo $lang->review->reviewOpinion; ?></th>
                                        <th class='w-80px'><?php echo $lang->review->reviewMode; ?></th>
                                        <th class='w-100px'><?php echo $lang->review->reviewDate ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $reviewNodeReviewerList = $this->review->getAllReviewNodeFormatReviewerList($review->id, $j);
                                    if (empty($reviewNodeReviewerList)):?>
                                        <tr>
                                            <th colspan="7"
                                                style="text-align: center;"><?php echo $lang->noData; ?></th>
                                        </tr>
                                    <?php else: ?>

                                        <?php foreach ($reviewNodeReviewerList as $nodeCode => $nodeData):
                                            $count = $nodeData['total'];
                                            $currentNode = $nodeData['data'];
                                            $name = zget($lang->review->nodeStageNameList, $nodeCode);
                                            echo "   <tr>";
                                            echo " <th rowspan = $count>" . " $name" . '</th>';
                                            foreach ($currentNode as $key => $current):
                                                unset($current->reviewers['total']);
                                                $currentNodeReviewers = $current->reviewers; //当前节点的用户数
                                                $currentSubNode = 0;
                                                if (isset($current->id)) {
                                                    $currentSubNode = $current->id;
                                                }
                                                //用户数量
                                                $reviewedCount = count($currentNodeReviewers);
                                                //第一个用户信息
                                                $firstReviewerInfo = $currentNodeReviewers[0];
                                                $extraInfo = $firstReviewerInfo->extraInfo;
                                                //当前审核节点的nodeCode
                                                $currentNodeCode = $current->nodeCode;
                                                ?>
                                                <th rowspan="<?php echo $reviewedCount; ?>">
                                                    <?php
                                                    echo zget($lang->review->nodeCodeNameList, $currentNodeCode, '');
                                                    ?>
                                                </th>
                                                <td>
                                                    <?php

                                                    $companyId = $this->loadModel('user')->getUserDeptName($firstReviewerInfo->reviewer)->company;
                                                    if ($companyId == 0) {
                                                        $deptName = $this->loadModel('user')->getUserDeptName($firstReviewerInfo->reviewer)->deptName;
                                                    } else {
                                                        $deptName = zget($companies, $companyId);
                                                    }
                                                    if (!empty($deptName)) {
                                                        echo $deptName . "/" . zget($users, $firstReviewerInfo->reviewer);
                                                    } else {
                                                        echo zget($users, $firstReviewerInfo->reviewer);
                                                    }

                                                    ?>
                                                    <?php if (isset($firstReviewerInfo->parentId) && $firstReviewerInfo->parentId > 0): ?>
                                                        （委托）
                                                    <?php endif; ?>
                                                </td>
                                                <?php if ($currentNodeCode == 'firstAssignReviewer'): ?>
                                                <?php echo "<th rowspan=' $reviewedCount' style='background:white;font-weight:normal;vertical-align: middle'>";
                                                $deptzs = $current->deptzs;
                                                if (count($deptzs) > 0) {
                                                    $str = '';
                                                    foreach ($deptzs as $key => $item) {
                                                        $str = $item . "&nbsp&nbsp";
                                                        if ($key % 2) {
                                                            $str .= '<br>';
                                                        }
                                                        echo $str;
                                                    }
                                                }
                                                echo "</th>";
                                                ?>
                                            <?php elseif ($currentNodeCode == 'baseline'): ?>
                                                <?php
                                                echo "<th  style='background:white;font-weight:normal;vertical-align: middle'>";
                                                //已打基线和无需打基线状态
                                                if ($current->status == 'pending') {
                                                    echo $this->lang->review->needBaseline;
                                                } else if ($current->status == 'pass') {
                                                    echo zget($lang->review->condition, $nodeData['baselineCondition']);
                                                }

                                                echo "</th>";
                                                ?>
                                            <?php else: ?>

                                                <td>
                                                    <?php if ($currentNodeCode == 'firstAssignDept'): ?>
                                                        <?php $dept = $current->dept;
                                                        if (isset($extraInfo['skipfirstreview']) && $dept) {
                                                            //是否跳过
                                                            echo $extraInfo['skipfirstreview'] . "&nbsp&nbsp";
                                                        } else {
                                                            if (isset($extraInfo['skipfirstreview']) && $review->type != 'pmo') {
                                                                echo $extraInfo['skipfirstreview'] . "&nbsp&nbsp";
                                                            } else if (isset($extraInfo['skipfirstreview']) && $review->type == 'pmo') {
                                                                echo 'PMO咨询无需初审';
                                                            } else {
                                                                //部门
                                                                if (count($dept) > 0) {
                                                                    $str = '';
                                                                    foreach ($dept as $key => $item) {
                                                                        $str = $item . "&nbsp&nbsp";
                                                                        if ($key % 2) {
                                                                            $str .= '<br>';
                                                                        }
                                                                        echo $str;
                                                                    }
                                                                } else {
                                                                    echo zget($lang->review->confirmResultList, $firstReviewerInfo->status);
                                                                }
                                                            }
                                                        } ?>
                                                    <?php elseif (in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList)): ?>
                                                        <?php
                                                        if (!isset($extraInfo['expert']) && !isset($extraInfo['outside'])) {
                                                            echo zget($lang->review->confirmResultList, $firstReviewerInfo->status);
                                                        } else {
                                                            if (isset($extraInfo['appoint']) && $extraInfo['appoint'] == 1) {
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
                                                    <?php elseif (($currentNodeCode == 'formalOwnerReview') && isset($extraInfo['grade'])): ?>
                                                        <?php echo zget($lang->review->gradeList, $extraInfo['grade'], ''); ?>
                                                    <?php elseif ($currentNodeCode == 'meetingReview' && $firstReviewerInfo->status == 'pass'): ?>
                                                        <?php echo ''; ?>
                                                    <?php elseif (($currentNodeCode == 'close') && $closeType == 'nopass'): ?>
                                                        <?php echo zget($this->lang->review->closeList, $current->status, '等待处理') ?>
                                                    <?php elseif (($currentNodeCode == 'close') && $closeType == 'pass'): ?>
                                                        <?php echo '评审通过'; ?>
                                                    <?php else: ?>
                                                        <?php if (isset($extraInfo['appointUser'])): ?>
                                                            委托 &nbsp;<?php echo zget($users, $extraInfo['appointUser']) ?>
                                                        <?php else: ?>
                                                            <?php echo zget($lang->review->confirmResultList, $firstReviewerInfo->status) ?>
                                                            <?php if (isset($extraInfo['isEditInfo'])): ?>
                                                                <?php echo zget($lang->review->isEditInfoList, $extraInfo['isEditInfo']); ?>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>

                                                <td>
                                                    <!--                              --><?php //if($firstReviewerInfo->reviewer && isset($firstReviewerInfo->status))echo $firstReviewerInfo->comment;
                                                    ?>
                                                    <?php echo $firstReviewerInfo->comment; ?>
                                                </td>

                                                <td>
                                                    <?php if (in_array($currentNodeCode, $lang->review->adviceGradeNodeCodes) && isset($extraInfo['grade'])): ?>
                                                        <?php echo '建议' . zget($gradeList, $extraInfo['grade']) ?>
                                                    <?php elseif (in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList) && isset($extraInfo['grade'])): ?>
                                                        <?php echo '确定' . zget($gradeList, $extraInfo['grade']) ?>
                                                    <?php endif; ?>
                                                </td>

                                                <!--评审日期-->
                                                <td>
                                                    <?php
                                                    if (isset($firstReviewerInfo->status) && !in_array($firstReviewerInfo->status, array('pending', 'wait'))):?>
                                                        <?php if (isset($extraInfo['reviewedDate']) && $extraInfo['reviewedDate']): ?>
                                                            <?php echo $extraInfo['reviewedDate']; ?>
                                                        <?php elseif ($firstReviewerInfo->reviewTime): ?>
                                                            <?php echo substr($firstReviewerInfo->reviewTime, 0, 10); ?>
                                                        <?php else: ?>
                                                            <?php echo $firstReviewerInfo->createdDate; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                </tr>
                                                <?php
                                                if ($reviewedCount > 1):
                                                    for ($i = 1; $i < $reviewedCount; $i++):
                                                        $reviewerInfo = $currentNodeReviewers[$i];
                                                        $extraInfo = $reviewerInfo->extraInfo;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                $companyId = $this->loadModel('user')->getUserDeptName($reviewerInfo->reviewer)->company;
                                                                if ($companyId == 0) {
                                                                    $deptName = $this->loadModel('user')->getUserDeptName($reviewerInfo->reviewer)->deptName;
                                                                } else {
                                                                    $deptName = zget($companies, $companyId);
                                                                }
                                                                if (!empty($deptName)) {
                                                                    echo $deptName . "/" . zget($users, $reviewerInfo->reviewer);
                                                                } else {
                                                                    echo zget($users, $reviewerInfo->reviewer);
                                                                }
                                                                ?>
                                                                <?php if (isset($firstReviewerInfo->parentId) && $reviewerInfo->parentId > 0): ?>
                                                                    （委托）
                                                                <?php endif; ?>
                                                            </td>
                                                            <?php if ($currentNodeCode != 'firstAssignReviewer'): ?>
                                                                <td>
                                                                    <?php if ($currentNodeCode == 'firstAssignDept'): ?>
                                                                        <?php $dept = $current->dept;
                                                                        if (isset($extraInfo['skipfirstreview']) && $dept) {
                                                                            //跳过
                                                                            echo $extraInfo['skipfirstreview'] . "&nbsp&nbsp";
                                                                        } else {
                                                                            //部门
                                                                            if (count($dept) > 0) {
                                                                                $str = '';
                                                                                foreach ($deptzs as $key => $item) {
                                                                                    $str = $item . "&nbsp&nbsp";
                                                                                    if ($key % 2) {
                                                                                        $str .= '<br>';
                                                                                    }
                                                                                    echo $str;
                                                                                }
                                                                            }
                                                                        } ?>
                                                                    <?php elseif (in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList)): ?>
                                                                        <?php
                                                                        if (!isset($extraInfo['expert']) && !isset($extraInfo['outside'])) {
                                                                            echo zget($lang->review->confirmResultList, $reviewerInfo->status);
                                                                        } else {
                                                                            if (isset($extraInfo['appoint']) && $extraInfo['appoint'] == 1) {
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
                                                                    <?php elseif (($currentNodeCode == 'formalOwnerReview') && isset($extraInfo['grade'])): ?>
                                                                        <?php echo zget($lang->review->gradeList, $extraInfo['grade'], ''); ?>
                                                                    <?php elseif ($currentNodeCode == 'meetingReview' && $reviewerInfo->status == 'pass'): ?>
                                                                        <?php echo ''; ?>
                                                                    <?php elseif ($currentNodeCode == 'close'): ?>
                                                                        <?php echo zget($this->lang->review->closeList, $current->status, '') ?>
                                                                    <?php else: ?>
                                                                        <?php if (isset($extraInfo['appointUser'])): ?>
                                                                            委托 &nbsp;<?php echo zget($users, $extraInfo['appointUser']) ?>
                                                                        <?php else: ?>
                                                                            <?php echo zget($lang->review->confirmResultList, $reviewerInfo->status) ?>
                                                                            <?php if (isset($extraInfo['isEditInfo'])): ?>
                                                                                <?php echo zget($lang->review->isEditInfoList, $extraInfo['isEditInfo']); ?>
                                                                            <?php endif; ?>
                                                                        <?php endif; ?>

                                                                    <?php endif; ?>

                                                                </td>
                                                            <?php endif; ?>
                                                            <td>
                                                                <?php echo $reviewerInfo->comment; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (in_array($currentNodeCode, $lang->review->adviceGradeNodeCodes) && isset($extraInfo['grade'])): ?>
                                                                    <?php echo '建议' . zget($gradeList, $extraInfo['grade']) ?>
                                                                <?php elseif (in_array($currentNodeCode, $lang->review->assignExpertNodeCodeList) && isset($extraInfo['grade'])): ?>
                                                                    <?php echo '确定' . zget($gradeList, $extraInfo['grade']) ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (isset($reviewerInfo->status) && !in_array($reviewerInfo->status, array('pending', 'wait'))): ?>
                                                                    <?php if (isset($extraInfo['reviewedDate']) && $extraInfo['reviewedDate']): ?>
                                                                        <?php echo $extraInfo['reviewedDate']; ?>
                                                                    <?php elseif ($reviewerInfo->reviewTime): ?>
                                                                        <?php echo substr($reviewerInfo->reviewTime, 0, 10); ?>
                                                                    <?php else: ?>
                                                                        <?php echo $reviewerInfo->createdDate; ?>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    endfor;
                                                endif;
                                                ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var scroll_height = document.body.scrollHeight;
    var window_height = window.innerHeight;
    if (scroll_height > window_height) {
        var _top = 120;
        if (window_height <= 700) {
            _top = 60
        }
        $(".dialog").append(".modal-dialog{top:" + _top + 'px' + "!important}");
    }
</script>

