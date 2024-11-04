<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
      <?php $browseLink = $app->session->infoList != false ? $app->session->infoList : inlink('browse');?>
      <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
      <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $info->code?></span>
    </div>
  </div>
  <?php if(!isonlybody()):?>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('info', 'exportWord')) echo html::a($this->createLink('info', 'exportWord', "infoID=$info->id"), "<i class='icon-export'></i> {$lang->info->exportWord}", '', "class='btn btn-primary'");?>
  </div>
  <?php endif;?>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $action == 'gain' ? $lang->info->gainDesc : $lang->info->fixDesc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($info->desc) ? html_entity_decode(str_replace("\n","<br/>",$info->desc)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $action == 'gain' ? $lang->info->gainReason : $lang->info->fixReason;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($info->reason) ? html_entity_decode(str_replace("\n","<br/>",$info->reason)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php if($action == 'fix'):?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->info->operation;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($info->operation) ? html_entity_decode(str_replace("\n","<br/>",$info->operation)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php endif;?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->info->test;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($info->test) ? html_entity_decode(str_replace("\n","<br/>",$info->test)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $action == 'gain' ? $lang->info->gainStep : $lang->info->fixStep;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($info->step) ? html_entity_decode(str_replace("\n","<br/>",$info->step)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->info->checkList;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($info->checkList) ? $info->checkList : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $action == 'gain' ? $lang->info->gainResult : $lang->info->fixResult;?></div>
        <div class="detail-content article-content">
          <?php if($action == 'gain'): ?>
            <?php echo zget($lang->info->fetchResultList, $info->fetchResult, '')  ?>
          <?php else: ?>
            <?php echo !empty($info->result) ? $info->result : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          <?php endif?>
        </div>
      </div>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=info&objectID=$info->id");?>
    </div>
    <?php if(!empty($nodes)):?>
    <div class="cell">
      <div class="detail">
          <div class="clearfix">
              <div class="detail-title pull-left"><?php echo $lang->info->reviewComment;?></div>
              <div class="detail-title pull-right">
                  <?php
                  if(common::hasPriv('info', 'showHistoryNodes')) echo html::a($this->createLink('info', 'showHistoryNodes', 'id='.$info->id, '', true), $lang->info->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                  ?>
              </div>
          </div>
        <div class="detail-content article-content">
          <?php if(!empty($nodes)):?>
          <table class="table ops">
            <tr>
              <th class="w-200px"><?php echo $lang->info->reviewNode;?></th>
              <td class="w-200px"><?php echo $lang->info->reviewer;?></td>
              <td class="w-200px"><?php echo $lang->info->reviewResult;?></td>
              <td style="width:370px"><?php echo $lang->info->reviewComment;?></td>
            </tr>
              <?php
              if ($info->createdDate > "2024-04-02 23:59:59"){
                  unset($this->lang->info->reviewerList[3]);
              }
                foreach ($lang->info->reviewerList as $key => $reviewNode):
                    $reviewerUserTitle = '';
                    $reviewerUsersShow = '';
                    $realReviewer = new stdClass();
                    $realReviewer->status = '';
                    $realReviewer->comment = '';
                    if(isset($nodes[$key])){
                        $currentNode = $nodes[$key];
                        $reviewers = $currentNode->reviewers;
                        if(!(is_array($reviewers) && !empty($reviewers))) {
                            continue;
                        }
                        //所有审核人
                        $reviewersArray = array_column($reviewers, 'reviewer');
                        $userCount = count($reviewersArray);
                        if($userCount > 0) {
                            $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                            $reviewerUserTitle = implode(',', $reviewerUsers);
                            $subCount = 3;
                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                            //获得实际审核人
                            $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                        }
                      }
              ?>
              <tr>
                  <th class="w-30px"><?php echo $reviewNode;?></th>
                  <td title="<?php echo $reviewerUserTitle; ?>" class="w-30px">
                      <?php echo $reviewerUsersShow; ?>
                  </td>
                  <td class="w-30px">
                      <?php echo zget($lang->info->confirmResultList, $realReviewer->status, '');?>
                      <?php
                        if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                      ?>
                      &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                      <?php endif; ?>
                  </td>
                  <td class="w-80px"><?php echo in_array($key, [0,1,7]) && '不用审批' == $realReviewer->comment  ? '不用处理' : $realReviewer->comment; ?></td>
              </tr>
              <?php endforeach;?>

            <?php else:?>
            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            <?php endif;?>
          </table>
        </div>
      </div>
    </div>
    <?php endif;?>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
        common::printIcon('info', 'edit', "infoID=$info->id", $info, 'button');
        common::printIcon('info', 'reject', "infoID=$info->id", $info, 'button', 'arrow-left', '', 'iframe', true, '', $this->lang->info->reject);
        common::printIcon('info', 'link', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'button', 'link', '', 'iframe', true);
        common::printIcon('info', 'review', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'button', 'glasses', '', 'iframe', true);
        common::printIcon('info', 'run', "infoID=$info->id", $info, 'button', 'play', '', 'iframe', true);
        common::printIcon('info', 'delete', "infoID=$info->id", $info, 'button', 'trash', '', 'iframe', true);
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->info->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-120px'><?php echo $lang->info->type;?></th>
                <td><?php echo zget($lang->info->typeList, $info->type, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->fixType;?></th>
                <td><?php echo zget($lang->info->fixTypeList, $info->fixType, '');?></td>
              </tr>
              <tr>
                <th class='w-120px'><?php echo $lang->info->classify;?></th>
                <td>
                <?php
                $classifyList = explode(',', $info->classify);
                $as = [];
                foreach($classifyList as $classify)
                {
                  if(!$classify) continue;
                  $as[] = zget($lang->info->techList, $classify, '');
                }
                echo implode('，',$as);
                ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->info->status;?></th>
                <td><?php echo zget($lang->info->statusList, $info->status, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->app;?></th>
                <td>
                <?php
                $as = [];
                foreach(explode(',', $info->app) as $app)
                {
                    if(!$app) continue;
                    $as[] = zget($apps, $app , "",$apps[$app]->name);
                }
                $app = implode("<br/>", $as);
                echo $app;
                ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->info->isPayment;?></th>
                <td>
                <?php
                $as = [];
                foreach(explode(',', $info->app) as $app)
                {
                    if(!$app) continue;
                    $as[] = zget($apps, $app, "",zget($lang->application->isPaymentList, $apps[$app]->isPayment, ''));
                }
                $applicationtype = implode('，', $as);
                echo $applicationtype;
                ?>
                </td>
              </tr>
              <?php if($info->action != 'gain'):?>
              <tr>
                <th class='w-100px'><?php echo $info->action == 'gain' ? $lang->info->gainNode : $lang->info->fixNode;?></th>
                <td>
                <?php
                if($info->node)
                {
                    $as = array();
                    foreach(explode(',', $info->node) as $nodeID)
                    {
                        if(!$nodeID) continue;
                        $as[] = zget($lang->info->nodeList, $nodeID, $nodeID);
                    }
                    echo implode(',', $as);
                }
                ?>
                </td>
              </tr>
              <?php endif;?>
              <tr>
                <th><?php echo $lang->info->team;?></th>
                <td><?php echo $info->appTeam;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->planBegin;?></th>
                <td><?php echo $info->planBegin;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->planEnd;?></th>
                <td><?php echo $info->planEnd;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->info->actualBegin;?></th>
                <td><?php echo $info->actualBegin;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->info->actualEnd;?></th>
                <td><?php echo $info->actualEnd;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->problem;?></th>
                <td>
                  <?php foreach($objects['problem'] as $objectID => $object):?>
                      <?php echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                  <?php endforeach;?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->info->demand;?></th>
                <td>
                  <?php foreach($objects['demand'] as $objectID => $object):?>
                      <?php
                      if($object->sourceDemand == 1){
                              echo html::a($this->createLink('demand', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                          }else{
                              echo html::a($this->createLink('demandinside', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                          } ?>
                  <?php endforeach;?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->info->secondorderId;?></th>
                <td>
                  <?php foreach (explode(',', $info->secondorderId) as $secondorderId): ?>
                      <?php if ($secondorderId and $secondorder->$secondorderId['code']) {
                          echo html::a($this->createLink('secondorder', 'view', 'id=' . $secondorderId, '', true), $secondorder->$secondorderId['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                      } ?>
                  <?php endforeach; ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->info->project;?></th>
                <td><?php foreach(explode(',', $info->project) as $project) echo  zget($projects, $project, '').PHP_EOL ;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->createdBy;?></th>
                <td><?php echo zget($users, $info->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->createdDate;?></th>
                <td><?php echo $info->createdDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->editedBy;?></th>
                <td><?php echo zget($users, $info->editedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->editedDate;?></th>
                <td><?php echo $info->editedDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->supply;?></th>
                <?php if(!empty($info->supply)):?>
                <td><?php foreach(explode(',', $info->supply) as $supply) echo zget($users, $supply); ?></td>
                <?php else:?>
                <td></td>
                <?php endif;?>
              </tr>
              <tr>
                <th><?php echo $lang->info->revertReason; ?></th>
                <td>
                  <?php
                  if($info->revertReason){
                    foreach(json_decode($info->revertReason) as $item){
                      echo $item->RevertDate.' '.zget($lang->info->revertReasonList, $item->RevertReason, '');
                       echo '<br/>';
                    }
                  }
                   ?>
                </td>
              </tr>
              <tr>
                  <th><?php echo $lang->info->revertReasonChild; ?></th>
                  <td>
                      <?php
                      if($info->revertReason){
                          $childTypeList = isset($this->lang->info->childTypeList) ? $this->lang->info->childTypeList['all'] : '[]';
                          $childTypeList = json_decode($childTypeList, true);
                          foreach(json_decode($info->revertReason) as $item){
                              if (isset($item->RevertReasonChild) && $item->RevertReasonChild != ''){
                                  echo $item->RevertDate.' '.$childTypeList[$item->RevertReason][$item->RevertReasonChild];
                              }
                              echo '<br/>';
                          }
                      }
                      ?>
                  </td>
              </tr>
              <?php if(isset($this->lang->info->cancelLinkageUserList[$this->app->user->account]) || $this->app->user->account == 'admin'):?>
                  <tr>
                      <th><?php echo $lang->info->problemCancelLinkage;?></th>
                      <td>
                          <?php echo zget($this->lang->info->cancelLinkageList,$info->problemCancelLinkage,'');?>
                          <?php echo html::a($this->createLink('info', 'cancelLinkage', "infoId=$info->id&type=problemCancelLinkage", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                      </td>
                  </tr>
              <?php endif;?>
            </tbody>
          </table>
        </div>
      </div>
    </div> 
    <?php foreach($info->releases as $release):?>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->info->release;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->release->name;?></th>
                <td><?php echo $release->name;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->info->path;?></th>
                <td><?php if($release->path) echo $release->path. $lang->api->sftpList['info'];?></td>
              </tr>
              <tr>
                <th><?php echo $lang->file->common;?></th>
                <td>
                  <div class='detail'>
                    <div class='detail-content article-content'>
                      <?php echo $this->fetch('file', 'printFiles', array('files' => $release->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false));?>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div> 
    <?php endforeach;?>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->info->dataMasking?></div>
        <div class="detail-content">
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-120px'><?php echo $lang->info->isJinke; ?></th>
                <td><?php echo zget($lang->info->isJinkeList, $info->isJinke); ?></td>
              </tr>
              <?php if($info->isJinke =='1'):?>
              <tr>
                <th><?php echo $lang->info->desensitizationType; ?></th>
                <td><?php echo zget($lang->info->desensitizationTypeList, $info->desensitizationType); ?></td>
              </tr>
              <tr>
                <th><?php echo $lang->info->deadline; ?></th>
                <td>
                  <?php
                    if($info->isDeadline == '1'){
                      echo '长期';
                    }else{
                      echo substr($info->deadline,0,10);
                    }
                    ?>    
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->info->dataManagementCode; ?></th>           
                <td><?php echo $info->dataManagementCode ? html::a($this->createLink('dataManagement', 'view', 'id=' . $info->dataManagementID, '', true), $info->dataManagementCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"):'';?></td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->consumedTitle;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->info->nodeUser;?></th>
               <!-- <td class='text-right'><?php /*echo $lang->info->consumed;*/?></td>-->
                <td class='text-center'><?php echo $lang->info->before;?></td>
                <td class='text-center'><?php echo $lang->info->after;?></td>
              </tr>
              <?php foreach($info->consumed as $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
               <!-- <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                <td class='text-center'><?php echo zget($lang->info->statusList, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->info->statusList, $c->after, '-');?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<?php js::set('action', $info->action);?>
<script>
    $(function() {
        setMenuHighlight(action);
    });
</script>
