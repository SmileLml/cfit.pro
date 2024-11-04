<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<style>.body-modal #mainMenu > .btn-toolbar .page-title {
        width: auto;
    }</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $goback = $app->session->demandinsideList ? $app->session->demandinsideList : inlink('browse'); ?>
            <?php echo html::a($goback, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="label label-id"><?php echo $demand->code ?></span>
            <span class="text" title='<?php echo $demand->title; ?>'><?php echo $demand->title; ?></span>
        </div>
    </div>
    <?php if (!isonlybody()): ?>
        <div class="btn-toolbar pull-right">
            <?php if (common::hasPriv('demandinside', 'exportWord')) echo html::a($this->createLink('demandinside', 'exportWord', "demandID=$demand->id"), "<i class='icon-export'></i> {$lang->demandinside->exportWord}", '', "class='btn btn-primary'"); ?>
        </div>
    <?php endif; ?>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->desc; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->desc) ? $demand->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->reason; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->reason) ? $demand->reason : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->solution; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->solution) ? $demand->solution : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail hidden">
                <div class="detail-title"><?php echo $lang->demandinside->plateMakAp; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->plateMakAp) ? $demand->plateMakAp : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
<!--            迭代22取消制版申请和制版信息，只做隐藏，不做其他处理 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->plateMakInfo; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->plateMakInfo) ? $demand->plateMakInfo : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->progress; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->progress) ? $demand->progress : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->comment; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->comment) ? $demand->comment : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <?php if ($demand->files) echo $this->fetch('file', 'printFiles', array('files' => $demand->files, 'fieldset' => 'true')); ?>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=demand&objectID=$demand->id"); ?>
        </div>
        <?php if(common::hasPriv('demand', 'fieldsAboutonConlusion')):?>
            <div class="cell">
                <div class="detail-title"><?php echo $lang->demandinside->conclusionInfo; ?></div>
                <div class="detail" >
                    <div class="detail-title"><?php echo $lang->demandinside->secondLineDevelopmentPlan; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($demand->secondLineDevelopmentPlan) ? nl2br($demand->secondLineDevelopmentPlan) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <?php $this->app->loadLang('demand'); ?>
                <div class="detail" >
                    <div class="detail-title"><?php echo $lang->demandinside->editSpecial; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($demand->conclusion) ? nl2br($demand->conclusion) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail" >
                    <div class="detail-title"><?php echo $lang->demandinside->secondLineDevelopmentStatus; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($demand->secondLineDevelopmentStatus) ? zget($lang->demand->secondLineDepStatusList,$demand->secondLineDevelopmentStatus) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail" >
                    <div class="detail-title"><?php echo $lang->demandinside->secondLineDevelopmentApproved; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($demand->secondLineDevelopmentApproved) ? zget($lang->demand->ifApprovedList,$demand->secondLineDevelopmentApproved) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail" >
                    <div class="detail-title"><?php echo $lang->demandinside->secondLineDevelopmentRecord; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($demand->secondLineDevelopmentRecord) ? zget($lang->demand->secondLineDepRecordList,$demand->secondLineDevelopmentRecord) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>

            </div>
        <?php endif; ?>
        <div class="cell"><?php include '../../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($this->session->demandinsideList); ?>
                <div class='divider'></div>

                <?php
                    common::printIcon('demandinside', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                    common::printIcon('demandinside', 'deal', "demandID=$demand->id", $demand, 'list', 'time', '', 'iframe', true);
                    common::printIcon('demandinside', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                    common::printIcon('demandinside', 'assignment', "demandID=$demand->id", $demand, 'list', 'hand-right', '', 'iframe', true);
                    common::printIcon('demandinside', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'list', 'trash', '', 'iframe', true);
                    common::printIcon('demandinside', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                    //忽略/恢复
                    if ($demand->ignoreStatus == 0){
                        common::printIcon('demandinside', 'ignore', "demandID=$demand->id", $demand, 'list', 'ban', '', 'iframe', true);
                    }else{
                        common::printIcon('demandinside', 'recoveryed', "demandID=$demand->id", $demand, 'list', 'bell', '', 'iframe', true);
                    }
                    //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                    if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or $this->app->user->account == $demand->dealUser or in_array($this->app->user->account, $executives)) {
                        if ($demand->status == 'suspend') {
                            common::printIcon('demandinside', 'start', "demandID=$demand->id", $demand, 'list', 'magic', '', 'iframe', true);
                        } else {
                            common::printIcon('demandinside', 'suspend', "demandID=$demand->id", $demand, 'list', 'pause', '', 'iframe', true);
                        }
                    }
                    else if($demand->status == 'suspend'){
                        echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->start . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                    }else{
                        echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                    }
                    common::printIcon('demandinside', 'editSpecial', "demandID=$demand->id", $demand, 'button', '', '', 'iframe', true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->opinionID; ?></th>
                            <?php if ($demand->opinionID): ?>
                                <td><?php echo html::a($this->createLink('opinioninside', 'view', array('id' => $opinioninside->id), '', true), $opinioninside->name, '', 'class="iframe"'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->requirementID; ?></th>
                            <?php if ($demand->requirementID): ?>
                                <td><?php echo html::a($this->createLink('requirementinside', 'view', array('id' => $demand->requirementID), '', true), $requirementName, '', 'class="iframe"'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->app; ?></th>
                            <td>
                                <?php
                                $as = [];
                                foreach (explode(',', $demand->app) as $app) {
                                    if (!$app) continue;
                                    $as[] = zget($apps, $app,'');
                                }
                                $app = implode(', ', $as);
                                echo $app;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->endDate; ?></th>
                            <td><?php echo $demand->endDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->product; ?></th>
                            <td><?php if ($demand->product) echo $demand->product == '99999' ? '无': $product->name; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->productPlan; ?></th>
                            <td><?php if ($demand->productPlan) echo $demand->productPlan  == '1' ? '无' : $productPlan->title; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->fixType; ?></th>
                            <td><?php echo zget($lang->demandinside->fixTypeList, $demand->fixType, ''); ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->acceptDept; ?></th>
                            <td><?php echo zget($depts, $demand->acceptDept, ''); ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->acceptUser; ?></th>
                            <td><?php echo zget($users, $demand->acceptUser, $demand->acceptUser); ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->end; ?></th>
                            <td><?php echo $demand->end; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->solvedTime; ?></th>
                            <td><?php echo strpos($demand->solvedTime,'0000-00-00') === false ? $demand->solvedTime : ''; ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->demandOnlineDate; ?></th>
                            <td><?php echo $demand->actualOnlineDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->relationModify; ?></th>
                            <td>
                                <?php if(isset($objects['modify'])):?>
                                <?php foreach ($objects['modify'] as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('modify', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>
                                <?php endif;?>
                                <?php if(isset($objects['modifycncc'])):?>
                                <?php foreach ($objects['modifycncc'] as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('modifycncc', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>
                                <?php endif;?>
                            </td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->collectionId; ?></th>
                            <td><?php
                                $collectionIds = explode(',', trim($demand->collectionId, ','));
                                $collectionList = $this->dao
                                    ->select('id,title')
                                    ->from(TABLE_DEMANDCOLLECTION)
                                    ->where('id')->in($collectionIds)
                                    ->andWhere('deleted')->eq('0')
                                    ->fetchAll('id');

                                foreach ($collectionList as $collectionId => $value){
                                    echo html::a($this->createLink('demandcollection', 'view', 'id=' . $collectionId, '', true), $value->title, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br />';
                                }
                                ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demandinside->status;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th><?php echo $lang->demandinside->status; ?></th>
                            <td><?php echo zget($lang->demandinside->statusList, $demand->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->dealUser;?></th>
                            <td>
                                <?php echo  zget($users, $demand->dealUser, ''); ?>
                            </td>
                        </tr>
                        <?php if($demand->mailto != ''):?>
                            <tr>
                                <th><?php echo $lang->demandinside->mailto;?></th>
                                <td><?php foreach(explode(',', $demand->mailto) as $user) echo zget($users, $user, '') . ' '; ?></td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->demandinside->createdBy; ?></th>
                            <td><?php echo zget($users, $demand->createdBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->createdDate; ?></th>
                            <td><?php echo $demand->createdDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->closedBy; ?></th>
                            <td><?php echo zget($users, $demand->closedBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->closedDate; ?></th>
                            <td><?php echo $demand->closedDate; ?></td>
                        </tr>
                        <?php if(common::hasPriv('demandinside', 'updateStatusLinkage') || $this->app->user->account == 'admin') :?>
                            <tr>
                                <th><?php echo $lang->demand->secureStatusLinkage;?></th>
                                <td>
                                    <?php echo zget($this->lang->demandinside->secureStatusLinkageList,$demand->secureStatusLinkage,'');?>
                                    <?php echo  common::printIcon('demandinside', 'updateStatusLinkage', "demandID=$demand->id", $demand, 'list','edit','','iframe',true) ;?>
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
                <div class="detail-title"><?php echo "关联属性";?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <!--由于若干个版本存库字段都是projectPlan,固使用projectPlan字段-->
                        <tr>
                            <th><?php echo $lang->opinioninside->project; ?></th>
                            <td>
<!--                                --><?php //if($demand->project) {
//                                    $array = explode(',', $demand->project);
//                                    foreach ($array as $pid){
//                                        if(empty($pid)) continue;
//                                        /*                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $pid), $plan[$pid], '', " style='color: #0c60e1;'");*/
//                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplanid), $plan->name, '', "data-app='platform' style='color: #0c60e1;'");
//                                        echo "<br>";
//                                    }
//                                }
//                                ?>
                                <?php if($plans) {
                                    foreach ($plans as $plan){
//                                        if(empty($pid)) continue;
                                        /*                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $pid), $plan[$pid], '', " style='color: #0c60e1;'");*/
                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $plan->id), $plan->name, '', "data-app='platform' style='color: #0c60e1;'");
                                        echo "<br>";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->task;?></th>
                            <td><?php if($task)  echo html::a('javascript:void(0)', $task->taskName, '', 'data-app="project" onclick="seturl('.$demand->project.','.$task->id.')"')?></td>
                            <td class="hidden"><?php echo html::a('','','','data-app="project"  id="demandtaskurl"')?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->stage;?></th>
                            <td><?php echo zget($executions,$demand->execution,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->buildName;?></th>
                            <td><?php if(isset($buildAndRelease->buildname) && $buildAndRelease->buildname)  echo html::a('javascript:void(0)', $buildAndRelease->buildname, '', 'data-app="project" onclick="newurl('.$demand->project.','.$buildAndRelease->bid.')"')?></td>
                            <td class="hidden"><?php echo html::a('','','','data-app="project"  id="demandbuildurl"')?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demandinside->releaseName;?></th>
                            <td><?php if(isset($buildAndRelease->buildname) && $buildAndRelease->rid)  echo html::a('javascript:void(0)', $buildAndRelease->releasename, '', 'data-app="project" onclick="addurl('.$demand->project.','.$buildAndRelease->rid.')"')?></td>
                            <td class="hidden"><?php echo html::a('','','','data-app="project"  id="demandreleaseurl"')?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->consumedTitle; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demandinside->nodeUser; ?></th>
                            <td class='text-center'><?php echo $lang->demandinside->before; ?></td>
                            <td class='text-center'><?php echo $lang->demandinside->after; ?></td>
                        </tr>
                        <?php foreach ($demand->consumed as $index => $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                <td class='text-center'><?php echo zget($lang->demandinside->statusConsumedList, $c->before, '-'); ?></td>
                                <td class='text-center'><?php echo zget($lang->demandinside->statusConsumedList, $c->after, '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../../common/view/footer.html.php'; ?>
