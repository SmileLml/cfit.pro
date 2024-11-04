<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
    <?php $browseLink = $app->session->modifycnccList != false ? $app->session->modifycnccList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $modifycncc->code?></span>
    </div>
  </div>
  <?php if(!isonlybody()):?>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('modifycncc', 'exportWord')) echo html::a($this->createLink('modifycncc', 'exportWord', "modifycnccID=$modifycncc->id"), "<i class='icon-export'></i> {$lang->modifycncc->exportWord}", '', "class='btn btn-primary'");?>
  </div>
  <?php endif;?>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->desc;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->desc) ? $modifycncc->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
        <?php if (in_array($modifycncc->implementModality,[1,3,6])):?>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->outwarddelivery->aadsReason; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($modifycncc->aadsReason) ? $modifycncc->aadsReason : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        <?php endif;?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->target;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->target) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->target)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->reason;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->reason) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->reason)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
          <div class="detail-title"><?php echo $lang->modifycncc->changeContentAndMethod;?></div>
          <div class="detail-content article-content">
              <?php echo !empty($modifycncc->changeContentAndMethod) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->changeContentAndMethod)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
          <div class="detail-title"><?php echo $lang->modifycncc->step;?></div>
          <div class="detail-content article-content">
              <?php echo !empty($modifycncc->step) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->step)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
          <div class="detail-title"><?php echo $lang->modifycncc->techniqueCheck;?></div>
          <div class="detail-content article-content">
              <?php echo !empty($modifycncc->techniqueCheck) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->techniqueCheck)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
          <div class="detail-title"><?php echo $lang->modifycncc->test;?></div>
          <div class="detail-content article-content">
              <?php echo !empty($modifycncc->test) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->test)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
          <div class="detail-title"><?php echo $lang->modifycncc->checkList;?></div>
          <div class="detail-content article-content">
              <?php echo !empty($modifycncc->checkList) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->checkList)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->cooperateDepNameList;?></div>
        <div class="detail-content article-content">
          <?php echo zget($lang->modifycncc->cooperateDepNameListList, $modifycncc->cooperateDepNameList, '');?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->businessCooperateContent;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($modifycncc->businessCooperateContent) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->businessCooperateContent)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->judgeDep;?></div>
        <div class="detail-content article-content">
          <?php echo zget($lang->modifycncc->judgeDepList, $modifycncc->judgeDep, '');?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->judgePlan;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($modifycncc->judgePlan) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->judgePlan)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->controlTableFile;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($modifycncc->controlTableFile) ? $modifycncc->controlTableFile: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->controlTableSteps;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($modifycncc->controlTableSteps) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->controlTableSteps)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->feasibilityAnalysis;?></div>
        <div class="detail-content article-content">
            <?php if(!empty($modifycncc->feasibilityAnalysis)){
                $feasibilityAnalysisInfo=array();
                $feasibilityAnalysises=explode(',',$modifycncc->feasibilityAnalysis);
                foreach ($feasibilityAnalysises as $feasibilityAnalysis){
                    $feasibilityAnalysisInfo[]=zget($lang->modifycncc->feasibilityAnalysisList, $feasibilityAnalysis, '');
                }
                echo trim(implode(',',$feasibilityAnalysisInfo),',');
            }
            else{
                echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
            }
            ?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->risk;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($modifycncc->risk) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->risk)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->riskAnalysisEmergencyHandle;?></div>
          <div class="detail-content article-content">
            <table class="table ops">
              <tr>
                <th class="w-200px"><?php echo $lang->modifycncc->id;?></th>
                <td><?php echo $lang->modifycncc->riskAnalysis;?></td>
                <td><?php echo $lang->modifycncc->emergencyBackWay;?></td>
              </tr>
                <?php if ($modifycncc->riskAnalysisEmergencyHandle):?>
                  <?php $num=1; foreach ($modifycncc->riskAnalysisEmergencyHandle as $ER):?>
                    <tr>
                      <th><?php echo $num;?></th>
                      <td >
                        <?php echo $ER->riskAnalysis; ?>
                      </td>
                      <td>
                        <?php echo $ER->emergencyBackWay; $num=$num+1?>
                      </td>
                    </tr>
                  <?php endforeach;?>
                <?php else:?>
                  <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                <?php endif;?>
                </table>
            </div>
        </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->effect;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->effect) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->effect)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->businessFunctionAffect;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->businessFunctionAffect) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->businessFunctionAffect)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->backupDataCenterChangeSyncDesc;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->backupDataCenterChangeSyncDesc) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->backupDataCenterChangeSyncDesc)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->emergencyManageAffect;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->emergencyManageAffect) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->emergencyManageAffect)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->modifycncc->changeImpactAnalysis; ?></div>
            <div class="detail-content article-content">
                <?php echo !empty($modifycncc->changeImpactAnalysis) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->changeImpactAnalysis)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
            </div>
        </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->businessAffect ;?></div>
          <div class="detail-content article-content">
            <?php echo !empty($modifycncc->businessAffect ) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->businessAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->benchmarkVerificationType;?></div>
          <div class="detail-content article-content">
            <?php echo zget($lang->modifycncc->benchmarkVerificationTypeList,$modifycncc->benchmarkVerificationType,'');?>
          </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->verificationResults;?></div>
          <div class="detail-content article-content">
             <?php echo !empty($modifycncc->verificationResults) ? $modifycncc->verificationResults: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->resultTitle;?></div>
          <div class='detail-content'>
            <table class='table table-data'>
              <tbody>
                <tr>
                  <th class='w-150px'><?php echo $lang->modifycncc->feedBackId;?></th>
                  <td><?php echo $modifycncc->feedBackId;?></td>
                  <th class='w-150px'><?php echo $lang->modifycncc->operationName ;?></th>
                  <td><?php echo $modifycncc->operationName ;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->feedBackOperationType;?></th>
                  <td><?php echo $modifycncc->feedBackOperationType;?></td>
                  <th><?php echo $lang->modifycncc->depOddName ;?></th>
                  <td><?php echo $modifycncc->depOddName ;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->actualBegin;?></th>
                  <td><?php if(strtotime($modifycncc->actualBegin)>0) echo $modifycncc->actualBegin;?></td>
                  <th><?php echo $lang->modifycncc->actualEnd ;?></th>
                  <td><?php if(strtotime($modifycncc->actualEnd)>0) echo $modifycncc->actualEnd ;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->supply;?></th>
                  <td><?php echo $modifycncc->supply;?></td>
                  <th><?php echo $lang->modifycncc->changeNum ;?></th>
                  <td><?php echo $modifycncc->changeNum ;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->operationStaff;?></th>
                  <td><?php echo $modifycncc->operationStaff;?></td>
                  <th><?php echo $lang->modifycncc->executionResults;?></th>
                  <td><?php echo $modifycncc->executionResults;?></td>
                </tr>
<!--                <tr>-->
<!--                    <th>--><?php //echo $lang->modifycncc->result;?><!--</th>-->
<!--                    <td>--><?php //echo zget($lang->modifycncc->resultList,$modifycncc->result);?><!--</td>-->
<!--                    <th>--><?php //echo $lang->modifycncc->internalSupply;?><!--</th>-->
<!--                    --><?php //if(!empty($modifycncc->internalSupply)){
//                        $supplyInfo=array();
//                        $internalSupplys=explode(',',$modifycncc->internalSupply);
//                        foreach ($internalSupplys as $internalSupply){
//                            $supplyInfo[]=zget($users,$internalSupply);
//                        }
//                    }?>
<!--                    <td>--><?php //echo implode(',',$supplyInfo);?><!--</td>-->
<!--                </tr>-->
                <tr>
                    <th><?php echo $lang->modifycncc->problemDescription;?></th>
                    <td><?php echo html_entity_decode(str_replace("\n","<br/>",$modifycncc->problemDescription));?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->modifycncc->resolveMethod;?></th>
                    <td><?php echo html_entity_decode(str_replace("\n","<br/>",$modifycncc->resolveMethod));?></td>
                </tr>
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
          <div class="clearfix">
              <div class="detail-title pull-left"><?php echo $lang->outwarddelivery->reviewOpinion; ?></div>
              <div class="detail-title pull-right">
                  <?php
                  if(common::hasPriv('modifycncc', 'showHistoryNodes')) echo html::a($this->createLink('modifycncc', 'showHistoryNodes', 'id='.$parentId, '', true), $lang->outwarddelivery->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                  ?>
              </div>
          </div>
        <div class="detail-content article-content">
          <?php if(!empty($nodes)):?>
          <table class="table ops">
            <tr>
              <th class="w-180px"><?php echo $lang->modifycncc->reviewNode;?></th>
              <td class="w-180px"><?php echo $lang->modifycncc->reviewer;?></td>
              <td class="w-180px"><?php echo $lang->modifycncc->reviewResult;?></td>
              <td style="width:370px"><?php echo $lang->modifycncc->reviewComment;?></td>
              <td class="w-180px"><?php echo $lang->modifycncc->reviewTime;?></td>
            </tr>
            <?php
                if($modifycncc->level == 2):
                    unset($lang->modifycncc->reviewNodeList[6]);
                elseif($modifycncc->level == 3):
                    unset($lang->modifycncc->reviewNodeList[5]);
                    unset($lang->modifycncc->reviewNodeList[6]);
                endif;
                //循环数据
                if ($modifycncc->createdDate > "2024-04-02 23:59:59"){
                    unset($this->lang->modifycncc->reviewNodeList[3]);
                }
                foreach ($lang->modifycncc->reviewNodeList as $key => $reviewNode):
                    $reviewerUserTitle = '';
                    $reviewerUsersShow = '';
                    $realReviewer = new stdClass();
                    $realReviewer->status = '';
                    $realReviewer->comment = '';
                    if(isset($nodes[$key])) {
                        $currentNode = $nodes[$key];
                        $reviewers = $currentNode->reviewers;
                        if(!(is_array($reviewers) && !empty($reviewers))) {
                            continue;
                        }
                        //所有审核人
                        $reviewersArray = array_column($reviewers, 'reviewer');
                        $reviewersArrayNew = $this->loadModel('common')->getAuthorizer('outwarddelivery', implode(',', $reviewersArray), $lang->outwarddelivery->reviewBeforeStatusList[$key], $lang->outwarddelivery->authorizeStatusList);
                        $reviewersArray = explode(',', $reviewersArrayNew);
                        $userCount = count($reviewersArray);
                        if ($userCount > 0) {
                            $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                            $reviewerUserTitle = implode(',', $reviewerUsers);
                            $subCount = 10;
                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                            //获得实际审核人
                            $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                            $extra = json_decode($realReviewer->extra);
                            if(!empty($extra->reviewerList)){
                                $reviewersArray = $extra->reviewerList;
                                $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                $reviewerUserTitle = implode(',', $reviewerUsers);
                                $subCount = 10;
                                $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                            }
                        }
                    }
                    if ($key==4 and (! in_array($realReviewer->status,['pass','reject']))) { continue; }
                    ?>

                  <tr>
                      <th><?php echo $reviewNode;?></th>
                      <td title="<?php echo $reviewerUserTitle; ?>">
                          <?php echo $reviewerUsersShow; ?>
                      </td>
                      <td>
                          <?php if($modifycncc->status!='waitsubmitted'):?>
                              <?php
                              if($realReviewer->status == 'ignore'){
                                  if($key != 3){
                                      echo '本次跳过';
                                      $realReviewer->comment = '已通过';
                                  }else{
                                      echo '无需处理';
                                      //$realReviewer->comment = '';
                                      $realReviewer->comment = implode('',array_unique(array_column((array)$reviewers, 'comment')));
                                  }
                              }else{
                                  echo zget($lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                              }
                              ?>
                              <?php
                                  if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                      ?>
                                      &nbsp;（
                                      <?php $extra = json_decode($realReviewer->extra);
                                      if(!empty($extra->proxy)){
                                          echo zget($users, $extra->proxy, '')."处理";
                                          echo "【".zget($users, $realReviewer->reviewer)."授权】";
                                      }else{
                                          echo zget($users, $realReviewer->reviewer, '');
                                      }?>）
                              <?php endif; ?>
                          <?php endif; ?>
                      </td>
                      <td><?php echo $realReviewer->comment; ?></td>
                      <td><?php echo $realReviewer->reviewTime; ?></td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <th><?php echo $lang->outwarddelivery->outerReviewNodeList['4'];?></th>
                  <td>
                      <?php echo zget($users,'guestjk',','); ?>
                  </td>
                  <td>
                      <?php
                      if(in_array($modifycncc->status,array('waitqingzong','qingzongsynfailed'))){
                          echo zget($lang->modifycncc->statusList, $modifycncc->status, '');
                      }
                      elseif(in_array($modifycncc->status,array('withexternalapproval','modifyfail','modifysuccesspart','modifysuccess','modifyreject','psdlreview','centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus))){
                          echo $lang->outwarddelivery->synSuccess;
                      }
                      else{
                          echo '';
                      }?>
                  </td>
                  <td>
                      <?php if($modifycncc->pushStatus and $MClog->response->message and (in_array($modifycncc->status,array('withexternalapproval','modifyfail','modifysuccesspart','modifysuccess','modifyreject','psdlreview','centrepmreview','giteepass','giteeback','qingzongsynfailed')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus)))){
                          echo $MClog->response->message;
                      }
                      elseif($modifycncc->status=='qingzongsynfailed'){
                          echo $lang->outwarddelivery->synFail;
                      }
                      else{
                          $MClog->requestDate='';
                      }?>
                  </td>
                  <td><?php if(isset($MClog->requestDate)) echo $MClog->requestDate; ?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->outwarddelivery->outerReviewNodeList['5'];?></th>
                  <td>
                      <?php echo zget($users,'guestcn',','); ?>
                  </td>
                  <td>
                      <?php
                      if(in_array($modifycncc->status,array('withexternalapproval','modifyfail','modifysuccesspart','modifysuccess','modifyreject','psdlreview','centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus))){
                          echo zget($lang->modifycncc->statusList,$modifycncc->status);
                          if($modifycncc->status == 'modifyreject'){
                              echo "（金信退回总中心，仅供参考）";
                          }
                      }
                      else{
                          echo '';
                      }?>
                  </td>
                  <td><?php if($modifycncc->reasonCNCC and (in_array($modifycncc->status,array('withexternalapproval','modifyfail','modifysuccesspart','modifysuccess','modifyreject','psdlreview','centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus)))) {
                      if($modifycncc->status == 'giteeback'){
                          echo "打回人：".$modifycncc->approverName."<br>审批意见：".$modifycncc->reasonCNCC;
                      }else{
                          echo $modifycncc->reasonCNCC;
                      }
                  }?>
                  </td>
                  <td><?php if(strtotime($modifycncc->feedbackDate)>0 and (in_array($modifycncc->status,array('withexternalapproval','modifyfail','modifysuccesspart','modifysuccess','modifyreject','psdlreview','centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus)))) echo $modifycncc->feedbackDate; ?></td>
              </tr>
            <?php else:?>
                <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            <?php endif;?>
          </table>
        </div>
      </div>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
          <div class="detail-title"><?php echo $lang->modifycncc->basicInfo;?></div>
          <div class='detail-content'>
              <table class='table table-data'>
                  <tbody>
                  <tr>
                      <th class="w-120px"><?php echo $lang->modifycncc->outsideNum ;?></th>
                      <td><?php echo $modifycncc->giteeId ;?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->status;?></th>
                      <td><?php echo $modifycncc->closed == '1' ? $lang->modifycncc->labelList['closed'] :zget($lang->modifycncc->statusList, $modifycncc->status, '');?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->createdBy;?></th>
                      <td><?php echo zget($users, $modifycncc->createdBy, '');?></td>
                  </tr>
                  <tr>
                      <th class="w-120px"><?php echo $lang->modifycncc->createdDept;?></th>
                      <td><?php echo zget($depts, $modifycncc->createdDept, '');?></td>
                  </tr>
                  <?php
                  if ($modifycncc->belongedApp){
                      foreach($modifycncc->belongedAppsInfo as $appInfo)
                      {
                          if ($appInfo){
                              $belongedApps[] = zget($apps, $appInfo->id);
                              $isPayments[] = zget($lang->application->isPaymentList,$appInfo->isPayment, '');
                              $teams[] = zget($lang->application->teamList,$appInfo->team, '');
                          }
                      }
                  }
                  ?>
                  <tr>
                      <th><?php echo $lang->modifycncc->belongedApp;?></th>
                      <td>
                          <?php echo trim(implode('<br/>', array_unique($belongedApps)),'<br/>');?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->isPayment;?></th>
                      <td>
                          <?php echo trim(implode('<br/>', array_unique($isPayments)),'<br/>');?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->team;?></th>
                      <td>
                          <?php echo trim(implode('<br/>', array_unique($teams)),'<br/>');?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->belongedOutwardDelivery?></th>
                      <td>
                          <?php
                          if( $parentId and $outwarddeliveryPairs[$parentId]->code){
                              $outwardDeliveryMsg = html::a($this->createLink('outwarddelivery', 'view', 'id=' . $parentId, '', true), $outwarddeliveryPairs[$parentId]->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'（';
                              if ($parent->isNewTestingRequest and $testingrequestPairs[$parent->testingRequestId]){
                                  $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('testingrequest', 'view', 'id=' . $parent->testingRequestId, '', true), $testingrequestPairs[$parent->testingRequestId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                              }
                              if ($parent->isNewProductEnroll and $productenrollPairs[$parent->productEnrollId]){
                                  $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('productenroll', 'view', 'id=' . $parent->productEnrollId, '', true), $productenrollPairs[$parent->productEnrollId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                              }
                              if ($parent->isNewModifycncc and $modifycnccPairs[$parent->modifycnccId]){
                                  $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('modifycncc', 'view', 'id=' . $parent->modifycnccId, '', true), $modifycnccPairs[$parent->modifycnccId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                              }
                              $outwardDeliveryMsg = trim($outwardDeliveryMsg,',').'）<br/>';
                              echo $outwardDeliveryMsg;
                          }?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->relatedOutwardDelivery?></th>
                      <td>
                          <?php foreach($allRelations['parents'] as $object){

                              $outwardDeliveryMsg = html::a($this->createLink('outwarddelivery', 'view', 'id=' . $object['id'], '', true), $object['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'（';
                              $trID = $outwarddeliveryPairs[$object['id']]->testingRequestId;
                              $peID = $outwarddeliveryPairs[$object['id']]->productEnrollId;
                              $mcID = $outwarddeliveryPairs[$object['id']]->modifycnccId;
                              if ($trID and $testingrequestPairs[$trID]){
                                  $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('testingrequest', 'view', 'id=' . $trID, '', true), $testingrequestPairs[$trID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                              }
                              if ($peID and $productenrollPairs[$peID]){
                                  $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('productenroll', 'view', 'id=' . $peID, '', true), $productenrollPairs[$peID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                              }
                              if ($mcID and $modifycnccPairs[$mcID]){
                                  $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('modifycncc', 'view', 'id=' . $trID, '', true), $modifycnccPairs[$mcID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                              }
                              $outwardDeliveryMsg = trim($outwardDeliveryMsg,',').'）<br/>';
                              echo $outwardDeliveryMsg;

                          }?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->relatedTestingRequest?></th>
                      <td>
                          <?php if($parent->testingRequestId and $testingrequestPairs[$parent->testingRequestId]):?>
                              <?php echo html::a($this->createLink('testingrequest', 'view', 'id=' . $parent->testingRequestId, '', true), $testingrequestPairs[$parent->testingRequestId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?><br/>
                          <?php endif;?>
                          <?php foreach($allRelations['testList'] as $objectId){
                              $outwardDeliveryMsg = '';
                              if ($testingrequestPairs[$objectId]){
                                  $outwardDeliveryMsg = html::a($this->createLink('productenroll', 'view', 'id=' . $objectId, '', true), $testingrequestPairs[$objectId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br>';
                              }
                              echo $outwardDeliveryMsg;
                          }?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->relatedProductEnroll?></th>
                      <td>
                          <?php if($parent->productEnrollId and $productenrollPairs[$parent->productEnrollId]):?>
                              <?php echo html::a($this->createLink('productenroll', 'view', 'id=' . $parent->productEnrollId, '', true), $productenrollPairs[$parent->productEnrollId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?><br/>
                          <?php endif;?>
                          <?php foreach($allRelations['productList'] as $objectId){
                              $outwardDeliveryMsg = '';
                              if ($productenrollPairs[$objectId]){
                                  $outwardDeliveryMsg = html::a($this->createLink('productenroll', 'view', 'id=' . $objectId, '', true), $productenrollPairs[$objectId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br>';
                              }
                              echo $outwardDeliveryMsg;
                          }?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->returnTimes;?></th>
                      <td><?php echo $modifycncc->returnTimes;?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->createdDate;?></th>
                      <td><?php if(strtotime($modifycncc->createdDate)>0) echo $modifycncc->createdDate;?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->editedBy;?></th>
                      <td><?php echo zget($users, $modifycncc->editedBy, '');?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->editedDate;?></th>
                      <td><?php if(strtotime($modifycncc->editedDate)>0) echo $modifycncc->editedDate;?></td>
                  </tr>
                  <tr>
                    <th><?php echo $lang->modifycncc->feedbackDate;?></th>
                    <td><?php if(strtotime($modifycncc->feedbackDate)>0) echo $modifycncc->feedbackDate;?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->closeBy;?></th>
                      <td><?php echo zget($users, $modifycncc->closedBy, '');?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->closeDate;?></th>
                      <td><?php if(strtotime($modifycncc->closedDate)>0) echo $modifycncc->closedDate;?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->modifycncc->closeReason;?></th>
                      <td><?php echo zget( $lang->outwarddelivery->closedReasonList,$modifycncc->closedReason);?></td>
                  </tr>
                  <tr style="display:none">
                      <th><?php echo $lang->modify->isMakeAmends;?></th>
                      <td><?php echo zget($lang->modify->isMakeAmendsList,$modifycncc->isMakeAmends,'')?></td>
                  </tr>
                  <?php if($modifycncc->isMakeAmends == 'yes'):?>
                      <tr>
                          <th><?php echo $lang->modify->actualDeliveryTime;?></th>
                          <td><?php echo $modifycncc->actualDeliveryTime;?></td>
                      </tr>
                  <?php endif;?>
                  </tbody>
              </table>
          </div>
      </div>
  </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->modifyParam;?></div>
          <div class='detail-content'>
            <table class='table table-data'>
              <tbody>
                <tr>
                  <th class='w-120px'><?php echo $lang->modifycncc->applyUsercontact;?></th>
                  <td><?php echo $modifycncc->applyUsercontact;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->level;?></th>
                  <td><?php echo zget($lang->modifycncc->levelList, $modifycncc->level, '');?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->productRegistrationCode ;?></th>
                  <td><?php echo $modifycncc->productRegistrationCode ;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->node;?></th>
                  <td>
                      <?php
                      $changeNodes = [];
                      foreach (explode(',',$modifycncc->node) as $node)
                      {
                          if(empty($node)) continue;
                          $changeNodes [] = zget($lang->modifycncc->nodeList, $node, '') ;
                      }
                      echo implode(',', $changeNodes);
                      ?>
                  </td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->operationType ;?></th>
                  <td><?php echo zget($lang->modifycncc->operationTypeList, $modifycncc->operationType,'') ;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->app;?></th>
                  <td>
                    <?php
                      if ($modifycncc->app){
                        foreach($modifycncc->appsInfo as $appID=>$appInfo)
                        {
                          $partitionMsg=$appInfo->name;
                            if (!empty($appInfo->partition[0])){
                              $partitionMsg.=' (';
                              foreach($appInfo->partition as $partition){
                                $partitionMsg.=$partition.' 分区,';
                              }
                              $partitionMsg=trim($partitionMsg,', ').' )';
                            }
                          echo $partitionMsg.'<br/>';
                        }
                      }
                    ?>
                  </td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->mode;?></th>
                  <td><?php echo zget($lang->modifycncc->modeList, $modifycncc->mode, '');?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->classify;?></th>
                  <td><?php echo zget($lang->modifycncc->classifyList, $modifycncc->classify, '');?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->changeSource;?></th>
                  <td><?php echo zget($lang->modifycncc->changeSourceList, $modifycncc->changeSource, '');?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->changeStage;?></th>
                  <td><?php echo zget($lang->modifycncc->changeStageList, $modifycncc->changeStage, '');?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->implementModality;?></th>
                  <td><?php echo zget($lang->modifycncc->implementModalityList, $modifycncc->implementModality, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->modifycncc->changeForm;?></th>
                    <td><?php echo zget($lang->modifycncc->changeFormList, $modifycncc->changeForm, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->modifycncc->automationTools;?></th>
                    <td><?php echo zget($lang->modifycncc->automationToolsList, $modifycncc->automationTools, '');?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->type;?></th>
                  <td><?php echo zget($lang->modifycncc->typeList, $modifycncc->type, '');?></td>
                </tr>
                <?php if($modifycncc->type == 1):?>
                    <tr>
                        <th><?php echo $lang->outwarddelivery->urgentSource; ?></th>
                        <td><?php echo zget($lang->modifycncc->urgentSourceList, $modifycncc->urgentSource, ''); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->outwarddelivery->urgentReason; ?></th>
                        <td><?php echo $modifycncc->urgentReason; ?></td>
                    </tr>
                <?php endif;?>
                <tr>
                  <th><?php echo $lang->modifycncc->isBusinessCooperate;?></th>
                  <td><?php echo zget($lang->modifycncc->isBusinessCooperateList, $modifycncc->isBusinessCooperate, '');?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->isBusinessJudge;?></th>
                  <td><?php echo zget($lang->modifycncc->isBusinessJudgeList, $modifycncc->isBusinessJudge, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->modifycncc->isBusinessAffect;?></th>
                    <td><?php echo zget($lang->modifycncc->isBusinessAffectList, $modifycncc->isBusinessAffect, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->modifycncc->property;?></th>
                    <td><?php echo zget($lang->modifycncc->propertyList, $modifycncc->property, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->modifycncc->isReview; ?></th>
                    <td><?php echo zget($lang->modifycncc->isReviewList, $modifycncc->isReview, ''); ?></td>
                </tr>
                <?php if($modifycncc->isReview == 1): ?>
                    <tr>
                        <th><?php echo $lang->modifycncc->reviewReport; ?></th>
                        <td><?php foreach (explode(',',$modifycncc->reviewReport) as $value):?>
                                <?php echo html::a($this->createLink('review', 'view', 'id=' . $modifycncc->reviewReport, '', true), zget($reviewReportList, $value, ''), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ; ?>
                                <br>
                            <?php endforeach;?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                  <th><?php echo $lang->modifycncc->backspaceExpectedStartTime;?></th>
                  <td><?php if(strtotime($modifycncc->backspaceExpectedStartTime)>0) echo $modifycncc->backspaceExpectedStartTime;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->backspaceExpectedEndTime;?></th>
                  <td><?php if(strtotime($modifycncc->backspaceExpectedEndTime)>0) echo $modifycncc->backspaceExpectedEndTime;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->planBegin;?></th>
                  <td><?php if(strtotime($modifycncc->planBegin)>0) echo $modifycncc->planBegin;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->planEnd;?></th>
                  <td><?php if(strtotime($modifycncc->planEnd)>0) echo $modifycncc->planEnd;?></td>
                </tr>
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->relateInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
            <tr>
              <th class="w-100px"><?php echo $lang->modifycncc->fixType;?></th>
              <td><?php echo zget($lang->modifycncc->fixTypeList, $modifycncc->fixType, '');?></td>
            </tr>
            <tr>
              <th><?php echo $lang->modifycncc->project;?></th>
              <td><?php foreach(explode(',', $modifycncc->project) as $project)
                  {
                      if ($project) echo zget($projects, $project, '') . '<br/>';
                  } ?></td>
            </tr>
            <tr>
                <th><?php echo $lang->modifycncc->secondorderId;?></th>
                <td>
                    <?php foreach($objects['secondorder'] as $objectID => $object):?>
                        <?php if( $objectID and $object) echo html::a($this->createLink('secondorder', 'view', 'id=' . $objectID, '', true), $secondorders[$objectID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?><br/>
                    <?php endforeach;?>
                </td>
            </tr>
            <tr>
              <th><?php echo $lang->modifycncc->problem;?></th>
              <td>
                <?php foreach($objects['problem'] as $objectID => $object):?>
                  <?php if( $objectID and $object) echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $problems[$objectID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?><br/>
                <?php endforeach;?>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->modifycncc->demand;?></th>
              <td>
                  <?php foreach($objects['demand'] as $objectID => $object):?>
                      <?php if($object->sourceDemand == 1){
                          echo html::a($this->createLink('demand', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                      }else{
                          echo html::a($this->createLink('demandinside', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                      } ?>
                  <?php endforeach;?>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->modifycncc->productCode;?></th>
              <td><?php if(!empty($modifycncc->productCode)) echo $modifycncc->productCode;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->modifycncc->relatedDemandNum?></th>
              <td>
                <?php foreach($objects['requirement'] as $objectID => $object):?>
                    <?php if($object->sourceRequirement == 1){
                        echo html::a($this->createLink('requirement', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                    }else{
                        echo html::a($this->createLink('requirementinside', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                    } ?>
                <?php endforeach;?>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->modifycncc->CNCCprojectIdUnique;?></th>
              <td>
                  <?php if (!empty($modifycncc->CBPProjectCode)):?>
                  <?php foreach($modifycncc->CBPProjectCode as $cbpprojectcode):?>
                      <?php echo $cbpprojectcode->code.'（'.$cbpprojectcode->name.'）'. '<br/>' ;?><br/>
                  <?php endforeach;?>
                  <?php endif;?>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->modifycncc->changeRelation;?></th>
              <td>
                  <?php foreach($objects['modifycncc'] as $relation => $modifycnccObjects){
                      if(!empty($modifycnccObjects)){
                          if ($relation=='beInclude'){
                              $relationModifycnccMsg=$lang->modifycncc->relateTypeIncluded.'（';
                          }
                          else{
                              $relationModifycnccMsg=zget($lang->modifycncc->relateTypeList,$relation,'').'（';
                          }
                          foreach ($modifycnccObjects as $num=>$relatedModifycncc){
                              $relationModifycnccMsg=$relationModifycnccMsg.html::a($this->createLink('modifycncc', 'view', 'id=' . $relatedModifycncc[0], '', true), $relatedModifycncc[1], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").', ';
                          }
                          echo rtrim($relationModifycnccMsg,', ').'）'. '<br/>';
                      }
                  } ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->modifycncc->outReviewInfo;?></div>
          <div class='detail-content'>
            <table class='table table-data'>
              <tbody>
                <tr>
                  <th class="w-100px"><?php echo $lang->modifycncc->changeStatus;?></th>
                  <td><?php echo zget($lang->modifycncc->changeStatusList, $modifycncc->changeStatus, '');?></td>
                </tr>
                <?php if('modifycancel' != $modifycncc->status): ?>
                <tr>
                  <th><?php echo $lang->modifycncc->changeRemark;?></th>
                  <td><?php echo $modifycncc->changeRemark;?></td>
                </tr>
                <?php endif; ?>
                <tr>
                  <th><?php echo $lang->modifycncc->actualBegin;?></th>
                  <td><?php if(strtotime($modifycncc->actualBegin)>0) echo $modifycncc->actualBegin;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->actualEnd;?></th>
                  <td><?php if(strtotime($modifycncc->actualEnd)>0) echo $modifycncc->actualEnd;?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->modifycncc->reasonCNCC;?></th>
                  <td><?php echo $modifycncc->reasonCNCC;?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->modifycncc->verificationReturnNum; ?></th>
                    <td><?php echo $modifycncc->verificationReturnNum; ?></td>
                </tr>
                <?php if(!empty($modifycncc->returnLogArray)):?>
                    <tr class = 'detail-content article-content'>
                        <table class="table ops">
                            <tr>
                                <th class="w-120px"><?php echo $lang->modifycncc->returnTime; ?></th>
                                <th class="w-120px"><?php echo $lang->modifycncc->returnNode; ?></th>
                                <th class="w-120px"><?php echo $lang->modifycncc->returnPerson; ?></th>
                                <th class="w-120px"><?php echo $lang->modifycncc->reasonCNCC; ?></th>
                            </tr>
                            <?php foreach ($modifycncc->returnLogArray as $key=>$value):?>
                                <tr>
                                    <td><?php echo $value->date;?></td>
                                    <td><?php echo $value->node;?></td>
                                    <td><?php echo $value->dealUser;?></td>
                                    <td><?php echo $value->reason;?></td>
                                </tr>
                            <?php endforeach;?>
                        </table>
                    </tr>
                <?php endif;?>
              </tbody>
            </table>
        </div>
    </div>
  </div>
    <?php if ($modifycncc->release):?>
          <div class="cell">
              <div class="detail">
                  <div class="detail-title"><?php echo $lang->outwarddelivery->release;?></div>
                  <div class='detail-content'>
                      <?php include '../../release/view/block.html.php'; ?>
                  </div>
              </div>
          </div>
      <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>