<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $componentHistory = $app->session->componentHistory ? $app->session->componentHistory : inlink('browse') ?>
            <?php echo html::a($componentHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="label label-id"><?php echo $component->id ?></span>
            <span class="text" title='<?php echo $component->name; ?>'><?php echo $component->name; ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->component->reviewObject; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-150px'><?php echo $lang->component->name; ?></th>
                            <td><?php echo $component->name ?></td>
                            <th class='w-150px'><?php echo $lang->component->version; ?></th>
                            <td><?php echo $component->version ?></td>
                            <th class='w-150px'><?php common::printIcon('component', 'editstatus', "ID=$component->id", $component, 'view-button', 'edit', '', 'btn btn-secondary iframe', true); ?></th>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->component->componentType; ?></th>
                            <td><?php echo zget($lang->component->type, $component->type, ''); ?></td>
                            <th class='w-150px'><?php echo $lang->component->application; ?></th>
                            <td><?php echo zget($lang->component->applicationMethod, $component->applicationMethod, ''); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->component->project; ?></th>
                            <td><?php echo zget($projectPlanList, $component->projectId) ?></td>
                            <th class='w-150px'><?php echo $lang->component->developLanguage; ?></th>
                            <td><?php echo zget($lang->component->developLanguageList, $component->developLanguage, ''); ?></td>
                        </tr>
                        <?php if ($component->type == 'public'): ?>
                            <tr>
                                <th class='w-150px'><?php echo $lang->component->level; ?></th>
                                <td><?php echo zget($lang->component->levelList, $component->level, ''); ?></td>
                                <th class='w-150px'><?php echo $lang->component->hasProfessionalReview; ?></th>
                                <td><?php echo zget($lang->component->professionalReviewResult, $component->hasProfessionalReview, ''); ?></td>
                            </tr>
                            <tr>
                                <th class='w-150px'><?php echo $lang->component->maintainer; ?></th>
                                <td><?php echo zget($users, $component->maintainer); ?></td>
                                <th class='w-150px'><?php echo $lang->component->createdDept; ?></th>
                                <td><?php echo zget($depts, $component->createdDept) ?></td>
                            </tr>
                            <tr>
                                <th class='w-150px'><?php echo $lang->component->relationgit; ?></th>
                                <?php
                                $gitlabname = '';
                                $gitlablist = json_decode($component->gitlab);
                                if($gitlablist){
                                    foreach($gitlablist as $key=>$gitval){
                                        $gitlabname .= $gitval.'<br />';
                                    }
                                }
                                ?>
                                <td colspan="3"><?php echo $gitlabname; ?></td>
                            </tr>
                            <tr>
                                <th class='w-150px'><?php echo $lang->component->location; ?></th>
                                <td colspan="3"><?php echo $component->location ?></td>
                            </tr>
                        <?php elseif ($component->type == 'thirdParty'): ?>
                            <!--<tr>
                                <th class='w-150px'><?php /*echo $lang->component->artifactId; */ ?></th>
                                <td><?php /*echo $component->artifactId*/ ?></td>
                                <th class='w-150px'><?php /*echo $lang->component->groupId; */ ?></th>
                                <td><?php /*echo $component->groupId*/ ?></td>
                            </tr>
                            <tr>
                                <th class='w-150px'><?php /*echo $lang->component->licenseType; */ ?></th>
                                <td><?php /*echo $component->licenseType*/ ?></td>
                            </tr>-->
                            <tr>
                                <th class='w-150px'><?php echo $lang->component->licenseType; ?></th>
                                <td><?php echo $component->licenseType ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($component->type == 'public'): ?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->component->functionDesc; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($component->functionDesc) ? $component->functionDesc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
            <?php elseif ($component->type == 'thirdParty'): ?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->component->applicationReason; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($component->applicationReason) ? $component->applicationReason : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->component->evidence; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($component->evidence) ? $component->evidence : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=component&objectID=$component->id"); ?>
            <?php if ($component->files): ?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->component->fileTitle; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                    <div class="detail-content">
                        <?php
                        foreach ($component->files as $key => $file) {
                            echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $component, 'canOperate' => $file->addedBy == $this->app->user->account));
                        }; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- 审核审批意见/处理意见 -->
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->component->reviewOpinion; ?></div>
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <td class="w-180px"><?php echo $lang->component->status; ?></td>
                                <td class="w-180px"><?php echo $lang->component->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->component->reviewResult; ?></td>
                                <td><?php echo $lang->component->reviewOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->component->reviewOpinionTime; ?></td>
                            </tr>
                            <?php foreach ($lang->component->reviewNodeStatusList as $key => $reviewNode):
                                //$currentNode = $nodes[$key - 1];

                                $reviewerUserTitle = '';
                                $reviewerUsersShow = '';
                                $realReviewer = new stdClass();
                                $realReviewer->status = '';
                                $realReviewer->comment = '';
                                if (isset($nodes[$key - 1])) {
                                    $currentNode = $nodes[$key - 1];
                                    $reviewers = $currentNode->reviewers;
                                    if (!(is_array($reviewers) && !empty($reviewers))) {
                                        continue;
                                    }
                                    //所有审核人
                                    $reviewersArray = array_column($reviewers, 'reviewer');
                                    $userCount = count($reviewersArray);
                                    if ($userCount > 0) {
                                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                        $reviewerUserTitle = implode(',', $reviewerUsers);
                                        $subCount = 10;
                                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                        //获得实际审核人
                                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                    }
                                }else{
                                    continue;
                                }

                                if (($currentNode->status != 'ignore') && ($reviewNode != 'toteamreview')):?>
                                    <tr>
                                        <th><?php echo zget($lang->component->statusList, $reviewNode); ?></th>
                                        <td title="<?php echo $reviewerUserTitle; ?>">
                                            <?php echo $reviewerUsersShow; ?>
                                        </td>
                                        <td><?php echo zget($lang->component->confirmResultList, $realReviewer->status, ''); ?>
                                            <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject' || $realReviewer->status == 'incorporate' || $realReviewer->status == 'appoint'): ?>
                                                &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($realReviewer->status == 'incorporate' && isset($componentParent)){
                                                echo $lang->component->name."：".$componentParent->name.'<br />';
                                            }
                                            ?>
                                            <?php echo $lang->component->reviewOpinion;?>:<?php echo $realReviewer->comment ?>
                                            <?php
                                            if ($realReviewer->status != 'ignore' and $realReviewer->status != 'wait' and $realReviewer->status != 'pending' and $realReviewer->status != 'confirming'): ?>
                                                <span class="action-span" style="float: right; margin-right: 10px;">
                                              <?php
                                              $params = "reviewersID=$realReviewer->id&componentID=$component->id";
                                              common::printIcon('component', 'editcomment', $params, $component, 'list', 'edit', '', 'iframe', true, 'data-position="50px"');
                                              ?>
                                          </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $realReviewer->reviewTime ?></td>
                                    </tr>
                                <?php elseif (($currentNode->status != 'ignore') && ($reviewNode == 'toteamreview')): ?>
                                    <?php foreach ($currentNode->reviewers as $reviewersKey => $reviewer): ?>
                                        <tr>
                                            <?php if ($reviewersKey == 0): ?>
                                                <th rowspan="<?php echo count($currentNode->reviewers); ?>">
                                                    <?php echo zget($lang->component->statusList, $reviewNode); ?>
                                                    <span class="action-span"
                                                          style="float: right; margin-right: 10px;">
                                                      <?php
                                                      $params = "componentID=$component->id";
                                                      common::printIcon('component', 'changeteamreviewer', $params, $component, 'list', 'edit', '', 'iframe', true, 'data-position="50px"');
                                                      ?>
                                                  </span>
                                                </th>
                                            <?php endif; ?>
                                            <td><?php echo zget($users, $reviewer->reviewer, ''); ?></td>
                                            <td><?php echo zget($lang->component->confirmResultList, $reviewer->status, ''); ?></td>
                                            <td><?php echo $reviewer->comment ?>
                                                <?php if ($realReviewer->status != 'ignore' and $realReviewer->status != 'wait' and $realReviewer->status != 'pending' and $realReviewer->status != 'confirming'): ?>
                                                    <span class="action-span"
                                                          style="float: right; margin-right: 10px;">
                                              <?php
                                              $params = "reviewersID=$reviewer->id&componentID=$component->id";
                                              common::printIcon('component', 'editcomment', $params, $component, 'list', 'edit', '', 'iframe', true, 'data-position="50px"');
                                              ?>
                                             </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $reviewer->reviewTime ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>


        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($componentHistory); ?>
                <div class='divider'></div>
                <?php
                common::printIcon('component', 'edit', "componentID=$component->id", $component, 'button');
                common::printIcon('component', 'submit', "componentID=$component->id", $component, 'button', 'play', '', 'iframe', true);
                common::printIcon('component', 'review', "componentID=$component->id&changeVersion=$component->changeVersion&reviewStage=$component->reviewStage", $component, 'button', 'glasses', '', 'iframe', true);
                common::printIcon('component', 'publish', "componentID=$component->id", $component, 'button', 'folder-open', '', 'iframe', true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->component->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th><?php echo $lang->component->status; ?></th>
                            <td><?php echo zget($lang->component->statusList, $component->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->component->dealUser; ?></th>
                            <?php
                            $dealUserTitle = '';
                            $dealUsersTitles = '';
                            if (!empty($component->dealUser)) {
                                foreach (explode(',', $component->dealUser) as $dealUser) {
                                    if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                                }
                            }
                            $dealUsersTitles = trim($dealUserTitle, ',');
                            ?>
                            <td title='<?php echo $dealUsersTitles; ?>'
                                class='text-ellipsis'><?php echo $dealUsersTitles; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->component->createdBy; ?></th>
                            <td><?php echo zget($users, $component->createdBy); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->component->createdDate; ?></th>
                            <td><?php echo $component->createdDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->component->reviewTime; ?></th>
                            <td><?php echo $component->reviewTime; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->component->statusTransition; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->component->nodeUser; ?></th>
                            <!--                            <td class='text-right'>-->
                            <?php //echo $lang->component->consumed;
                            ?><!--</td>-->
                            <td class='text-center'><?php echo $lang->component->before; ?></td>
                            <td class='text-center'><?php echo $lang->component->after; ?></td>
                            <td class='text-center'><?php echo $lang->component->time; ?></td>
                        </tr>
                        <?php foreach ($consumed as $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                <!--                                <td class='text-right'>-->
                                <?php //echo $c->consumed . ' ' . $lang->hour;?><!--</td>-->
                                <?php
                                echo "<td class='text-center'>" . zget($lang->component->statusList, $c->before, '-') . "</td>";
                                echo "<td class='text-center'>" . zget($lang->component->statusList, $c->after, '-') . "</td>";
                                echo "<td class='text-center'>" . $c->createdDate . "</td>";
                                ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
