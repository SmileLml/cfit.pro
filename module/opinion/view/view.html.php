<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .changeInfo{
        text-align: center;
    }
    .changeInfo th{
        text-align: center;
    }
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
        <?php $browseLink = $app->session->opinionList != false ? $app->session->opinionList : inlink('browse');?>

        <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $opinion->code?></span>
      <span class="text" title='<?php echo $opinion->name;?>'><?php echo $opinion->name;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->background;?></div>
          <?php if(strip_tags($opinion->background) == $opinion->background):?>
            <div class="detail-content article-content" style="white-space: pre-line">
          <?php else:?>
            <div class="detail-content article-content">
          <?php endif;?>
          <?php echo !empty($opinion->background) ? $opinion->background : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->overview;?></div>
          <?php if(strip_tags($opinion->overview) == $opinion->overview):?>
            <div class="detail-content article-content" style="white-space: pre-line">
          <?php else:?>
            <div class="detail-content article-content">
          <?php endif;?>
          <?php echo !empty($opinion->overview) ? $opinion->overview : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php if($opinion->desc):?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->desc;?></div>
          <?php if(strip_tags($opinion->desc) == $opinion->desc):?>
            <div class="detail-content article-content" style="white-space: pre-line">
          <?php else:?>
            <div class="detail-content article-content">
          <?php endif;?>
          <?php echo !empty($opinion->desc) ? $opinion->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php endif;?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->remark;?></div>
          <?php if(strip_tags($opinion->remark) == $opinion->remark):?>
            <div class="detail-content article-content" style="white-space: pre-line">
          <?php else:?>
            <div class="detail-content article-content">
          <?php endif;?>
          <?php echo !empty($opinion->remark) ? $opinion->remark : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $opinion->files, 'fieldset' => 'true', 'object' => $opinion, 'canOperate' => $opinion->createdBy == $this->app->user->account or $this->app->user->admin));?>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=opinion&objectID=$opinion->id");?>

        <?php
        $app->loadLang('requirement');
        if(!empty($changes)):?>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->changeRecord; ?></div>
                <div class="detail-content article-content">
                    <table class="table ops" style="text-align: center">
                        <tr>
                            <th class="w-100px" style="text-align: center"><?php echo $lang->requirement->changeNum; ?></th>
                            <th class="w-200px" style="text-align: center"><?php echo $lang->requirement->changeTime; ?></th>
                            <th class="w-200px" style="text-align: center"><?php echo $lang->requirement->changeCode; ?></th>
<!--                            <th class="w-100px" style="text-align: center">--><?php //echo $lang->requirement->changeRemark; ?><!--</th>-->
                        </tr>
                        <?php $num = 1;
                        foreach ($changes as $val): ?>
                            <tr>
                                <td><?php echo $num++; ?></td>
                                <td>
                                    <?php echo $val->createdDate; ?>
                                </td>
                                <td>
                                    <a class="iframe" data-width="900" href='<?php echo $this->createLink('requirementchange', 'changeview', "changeID=$val->id",'',true)?>'><?php echo $val->changeNumber; ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endif;?>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->opinion->baseChangeTip; ?></div>
            <div class="detail-content article-content">
                <?php if (!empty($changeInfo)): ?>
                    <table class="table changeInfo">
                        <tr>
                            <th class="w-50px"><?php echo $this->lang->opinion->changeTimes; ?></th>
                            <th class="w-150px"><?php echo $this->lang->opinion->changeDate; ?></th>
                            <th class="w-150px"><?php echo $this->lang->opinion->changeCode; ?></th>
                            <th class="w-100px"><?php echo $this->lang->opinion->changeStatus; ?></th>
                            <th class="w-50px"><?php echo '操作'; ?></th>
                        </tr>
                        <?php foreach ($changeInfo as $key => $item):?>
                            <tr>
                                <td><?php echo $key+1; ?></td>
                                <td><?php echo $item->createdDate;?></td>
                                <td>
                                    <?php echo html::a($this->createLink('opinion', 'changeview', array('id' => $item->id,'opinionID'=>$item->opinionID), '', true), $item->changeCode, '', 'class="iframe"'); ?>
                                </td>
                                <td><?php echo zget($this->lang->opinion->changeStatusList,$item->status); ?></td>
                                <td>
                                    <?php
                                    if($item->status == 'back' && $this->app->user->account == $item->createdBy)
                                    {
                                        if(common::hasPriv('opinion','revoke')) common::printLink('opinion', 'revoke', array('id' => $item->id,'opinionID'=>$item->opinionID),  '撤销', '', "class='iframe text-blue'",'',true);
                                        echo "<span>&nbsp;&nbsp;</span>";
                                        if(common::hasPriv('opinion','editchange')) common::printLink('opinion', 'editchange', array('id' => $item->id,'opinionID'=>$item->opinionID), '编辑', '', "class='iframe text-blue'",'',true);
                                    }

                                    ?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                <?php else: ?>
                    <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                <?php endif; ?>

            </div>
        </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->opinion->liftCycle; ?></div>
            <div class="detail-content article-content">
                <table class="table changeInfo">
                    <tr>
                        <th class="w-150px"><?php echo $this->lang->opinion->common; ?></th>
                        <th class="w-150px"><?php echo $this->lang->opinion->commonRequirement; ?></th>
                        <th class="w-100px"><?php echo $this->lang->opinion->commonDemand; ?></th>
                    </tr>
                    <tr>
                        <td rowspan="<?php echo $lifeOpinionInfo['allCount'];?>"><?php echo $lifeOpinionInfo['name'];?></td>
                        <?php $firstRequirement = $lifeOpinionInfo['requirements'][0];?>
                        <?php $firstDemand = $lifeOpinionInfo['requirements'][0]['demands'][0];?>
                        <td rowspan="<?php echo $firstRequirement['demandCount'];?>">
                            <?php
                                if(!empty($firstRequirement['id'])){
                                    echo html::a($this->createLink('requirement', 'view', array('id' => $firstRequirement['id']), '', true), $firstRequirement['name'], '', "class='iframe' data-width='90%'");
                                }else{
                                    echo $firstRequirement['name'];
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if(!empty($firstDemand['id']))
                                {
                                    echo html::a($this->createLink('demand', 'view', array('id' => $firstDemand['id']), '', true), $firstDemand['name'], '', "class='iframe' data-width='90%'");
                                }else{
                                    echo $firstDemand['name'];
                                }
                            ?>
                        </td>
                    </tr>
                    <?php foreach ($lifeOpinionInfo['requirements'] as $item => $lifeRequirement):?>
                        <?php if($item == 0):?>
                            <?php for($i = 1; $i < $lifeRequirement['demandCount'];$i++):?>
                                <tr>
                                    <td>
                                        <?php
                                            if(!empty($lifeRequirement['demands'][$i]['id']))
                                            {
                                                echo html::a($this->createLink('demand', 'view', array('id' => $lifeRequirement['demands'][$i]['id']), '', true), $lifeRequirement['demands'][$i]['name'], '', "class='iframe' data-width='90%'");
                                            }else{
                                                echo $lifeRequirement['demands'][$i]['name'];
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endfor;?>
                        <?php endif;?>

                        <?php if($item > 0):?>
                            <?php foreach ($lifeRequirement['demands'] as $demandNum => $lifeDemand):?>
                                <tr>
                                    <?php if($demandNum == 0):?>
                                        <td rowspan="<?php echo $lifeRequirement['demandCount'];?>">
                                            <?php
                                                if(!empty($lifeRequirement['id']))
                                                {
                                                    echo html::a($this->createLink('requirement', 'view', array('id' => $lifeRequirement['id']), '', true), $lifeRequirement['name'], '', "class='iframe' data-width='90%'");
                                                }else{
                                                    echo $lifeRequirement['name'];
                                                }
                                            ?>
                                        </td>
                                    <?php endif;?>
                                        <td>
                                            <?php
                                            if(!empty($lifeDemand['id']))
                                            {
                                                echo html::a($this->createLink('demand', 'view', array('id' => $lifeDemand['id']), '', true), $lifeDemand['name'], '', "class='iframe' data-width='90%'");
                                            }else{
                                                echo $lifeDemand['name'];
                                            }
                                            ?>
                                        </td>
                                </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                    <?php endforeach;?>
                </table>
            </div>
        </div>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
          <?php
            common::printIcon('opinion', 'subdivide', "opinionID=$opinion->id", $opinion, 'button', 'split', '');
            common::printIcon('opinion', 'edit', "opinionID=$opinion->id", $opinion, 'button','edit', '');
//            common::printIcon('opinion', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe',true);
          //研发责任人取所有需求条目合集  //迭代三十二 所有人可发起变更
          //if((in_array($this->app->user->account,explode(',',$opinion->acceptUser)) and !in_array($opinion->opinionChangeStatus,[2,3])) or $this->app->user->account == 'admin')
          if(!in_array($opinion->opinionChangeStatus,[2,3]))
          {
              common::printIcon('opinion', 'change', "opinionID=$opinion->id", $opinion, 'button','alter', '', 'iframe',true);
            }else{
              echo '<button type="button" class="disabled btn" title="' . $lang->opinion->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
            }
            common::printIcon('opinion', 'assignment', "opinionID=$opinion->id", $opinion, 'button', 'hand-right', '', 'iframe', true);
//            common::printIcon('opinion', 'review', "opinionID=$opinion->id", $opinion, 'list','', '', 'iframe', true);
            ?>

          <?php if($opinion->status != 'deleted') :?>
          <?php if($this->app->user->account != 'admin' and  $opinion->demandCode):?>
              <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->opinion->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
          <?php elseif($this->app->user->account != 'admin' and (($this->app->user->account != $opinion->dealUser and in_array($opinion->status,array('created')) or (!in_array($opinion->status,array('created')))) and (strstr($opinion->changeNextDealuser, $app->user->account) == false))):?>
              <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->opinion->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
          <?php else:?>
              <div class="btn-group dropup">
                  <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                  <ul class="dropdown-menu">
                      <?php if($this->app->user->account == 'admin' or (in_array($opinion->status,array('created')) and $this->app->user->account == $opinion->reviewOpinionDealUser and !$opinion->demandCode)): ?>
                          <li><?php echo html::a($this->createLink('opinion', 'review', 'opinionID=' . $opinion->id , '', true), $lang->opinion->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                      <?php else:?>
                          <li style="margin-top:-10px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->opinion->review; ?></span></li>
                      <?php endif;?>
                      <?php if(!empty($this->app->user->account == 'admin' or (strstr($opinion->changeNextDealuser, $app->user->account) !== false))):?>
                          <li><?php echo html::a($this->createLink('opinion', 'reviewchange', 'opinionID=' . $opinion->id , '', true), $lang->opinion->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                      <?php else:?>
                          <li style="margin-top:-10px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->opinion->reviewchange; ?></span></li>
                      <?php endif;?>
                  </ul>
              </div>
          <?php endif;?>
          <?php endif;?>


            <?php
            if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executivesOpinion) or $this->app->user->account == $opinion->closedBy or $this->app->user->account == $opinion->createdBy) {
                if ($opinion->status == 'closed') {
                    common::printIcon('opinion', 'reset',"opinionID=$opinion->id", $opinion, 'button', 'magic', '', 'iframe', true);
                } else {
                    common::printIcon('opinion', 'close', "opinionID=$opinion->id", $opinion, 'button', 'pause', '', 'iframe', true);
                }
            }else if($opinion->status == 'closed'){
                echo '<button type="button" class="disabled btn" title="' . $lang->opinion->reset . '"><i class="icon-common-start disabled icon-magic"></i></button>';
            }else{
                echo '<button type="button" class="disabled btn" title="' . $lang->opinion->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
            }

            common::printIcon('opinion', 'delete', "opinionID=$opinion->id", $opinion, 'button', 'trash', '', 'iframe',true);
            if ($opinion->ignore) {
                common::printIcon('opinion', 'recoveryed', "opinionID=$opinion->id", $opinion, 'button', 'bell', '', 'iframe', true);
            } else {
                common::printIcon('opinion', 'ignore', "opinionID=$opinion->id", $opinion, 'button', 'ban', '', 'iframe', true);
            }
          ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinion->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-120px'><?php echo $lang->opinion->category;?></th>
                <td><?php echo zget($lang->opinion->categoryList, $opinion->category, '');?></td>
              </tr>
             <tr>
               <th><?php echo $lang->opinion->charge;?></th>
                <td class='c-actions text-left'>
                  <div style="display:flex; flex-wrap:wrap;align-items:center;">
                <?php
                  foreach(explode(',',trim($opinion->assignedTo,',')) as $index => $assignedTo):?>
                  <span>
                    <?php echo zget($users, $assignedTo,'').($index+1==sizeof(explode(',',trim($opinion->assignedTo,',')))?'':'，'); ?>
                  </span>
                  <?php endforeach?><span>
                  <?php common::printIcon('opinion', 'editassignedto', "opinionId=$opinion->id", $opinion, 'list', 'edit', '', 'iframe', true);?>
                  </span>
                  </div>
                </td>
             </tr>
              <tr>
                <th><?php echo $lang->opinion->sourceMode;?></th>
                <td><?php echo zget($lang->opinion->sourceModeList, $opinion->sourceMode, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->opinion->sourceName;?></th>
                <td><?php echo $opinion->sourceName;?></td>
              </tr>
            <tr>
                <th><?php echo $lang->opinion->union;?></th>
                <td>
                    <?php
                    $unionList = explode(',',str_replace(' ', '', $opinion->union));
                    foreach ($unionList as $union)
                    {
                        if($union) echo ' ' . zget($lang->opinion->unionList, $union, '');
                    }
                    ?>
                </td>
            </tr>
              <tr>
                <th><?php echo $lang->opinion->contact;?></th>
                <td><?php echo $opinion->contact;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->opinion->contactInfo;?></th>
                <td><?php echo $opinion->contactInfo;?></td>
              </tr>
              <?php if($opinion->createdBy == 'guestcn'):?>
              <tr>
                <th><?php echo $lang->opinion->date;?></th>
                <td><?php echo $opinion->createdDate;?></td>
              </tr>
              <?php else:?>
              <tr>
                  <th><?php echo $lang->opinion->date;?></th>
                  <td><?php echo $opinion->date;?></td>
              </tr>
            <?php endif;?>
            <tr>
                <th><?php echo $lang->opinion->receiveDate;?></th>
                <td><?php echo $opinion->receiveDate;?></td>
            </tr>
              <tr>
                  <th><?php echo $lang->opinion->lastChangeTime;?></th>
                  <td><?php echo $opinion->lastChangeTime;?></td>
              </tr>
              <?php if(!empty($opinion->opinionChangeTimes)):?>
              <tr>
                  <th><?php echo $lang->opinion->changeTimes;?></th>
                  <td><?php echo $opinion->opinionChangeTimes;?></td>
              </tr>
              <?php endif;?>
              <tr>
                <th><?php echo $lang->opinion->deadline;?></th>
                <td><?php echo $opinion->deadline;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->opinion->project;?></th>
                <td>
                    <?php
                    foreach ($projectList as $projectID => $item) {
                        if ($projectID) {
                            echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectID), $item->name, '', "data-app='platform' style='color: #0c60e1;'");
                            echo "<br>";
                        }
                    }
                    ?>
                </td>
              </tr>
              <tr>
                  <th><?php echo $lang->opinion->solvedTime; ?></th>
                  <?php if(in_array($opinion->status,['delivery','online'])):?>
                    <td><?php echo $opinion->solvedTime; ?></td>
                  <?php endif;?>
              </tr>
              <tr>
                <th><?php echo $lang->opinion->onlineTimeByDemand;?></th>
                  <td><?php echo $opinion->status == 'online' ?  $opinion->onlineTimeByDemand :'';?></td>
              </tr>
              <?php if($opinion->synUnion):?>
              <tr>
                <th class='w-100px'><?php echo $lang->opinion->synUnion;?></th>
                <td>
                    <?php
                    $synUnionList = explode(',', str_replace(' ', '', $opinion->synUnion));
                    foreach($synUnionList as $synUnion)
                    {
                        if($synUnion) echo ' ' . zget($lang->opinion->synUnionList, $synUnion, '');
                    }
                    ?>
                </td>
              </tr>
              <?php endif;?>
              <?php if($opinion->isOutsideProject):?>
              <tr>
                  <th><?php echo $lang->opinion->isOutsideProject;?></th>
                  <td><?php echo zget($lang->opinion->isOutsideProjectList, $opinion->isOutsideProject, '');?></td>
              </tr>
              <?php endif;?>
              <?php if($opinion->demandCode):?>
                  <tr>
                      <th class='w-120px'><?php echo $lang->opinion->demandCode;?></th>
                      <td><?php echo $opinion->demandCode;?></td>
                  </tr>
                  <tr>
                      <th class='w-120px'><?php echo $lang->opinion->urgency;?></th>
                      <td><?php echo $opinion->urgency;?></td>
                  </tr>
                  <tr>
                      <th class='w-120px'><?php echo $lang->opinion->type;?></th>
                      <td><?php echo $opinion->type;?></td>
                  </tr>
              <?php endif;?>
              <?php if($opinion->createdBy != 'guestcn'):?>
                  <tr>
                      <th><?php echo $lang->opinion->lockStatus;?></th>
                      <td>
                          <?php echo zget($this->lang->opinion->lockStatusList,$opinion->changeLock,'');?>
                          <?php if(((common::hasPriv('opinion', 'unlockSeparate') && in_array($this->app->user->account,$unLock)) || $this->app->user->account == 'admin') && $opinion->changeLock == 2 ) :?>
                              <?php echo  common::printIcon('opinion', 'unlockSeparate', "opinionID=$opinion->id", $opinion, 'list','edit','','iframe',true) ;?>
                          <?php endif;?>
                      </td>
                  </tr>
              <?php endif;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
          <div class="detail-title"><?php echo $lang->opinion->processInfo;?></div>
          <div class='detail-content'>
              <table class='table table-data'>
                  <tbody>
                      <tr>
                          <th><?php echo $lang->opinion->status;?></th>
                          <td><?php echo zget($lang->opinion->statusList, $opinion->status, '');?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->opinion->dealUser;?></th>
                          <td><?php echo zget($users, $opinion->dealUser, '');?></td>
                      </tr>
                      <?php if($opinion->mailto != ''):?>
                      <tr>
                          <th><?php echo $lang->opinion->mailto;?></th>
                          <td><?php foreach(explode(',', $opinion->mailto) as $user) echo zget($users, $user, '') . ' '; ?></td>
                      </tr>
                      <?php endif;?>
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
                <th class='w-100px'><?php echo $lang->opinion->nodeUser;?></th>
<!--                <td class='text-right'>--><?php //echo $lang->opinion->consumed;?><!--</td>-->
                <td class='text-center'><?php echo $lang->opinion->before;?></td>
                <td class='text-center'><?php echo $lang->opinion->after;?></td>
              </tr>
              <?php foreach($opinion->consumed as $index => $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
<!--                <td class='text-right'>--><?php //echo $c->consumed . ' ' . $lang->hour;?><!--</td>-->
                <td class='text-center'><?php echo zget($lang->opinion->statusList, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->opinion->statusList, $c->after, '-');?></td>
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
