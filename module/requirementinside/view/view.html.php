<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .detail-title-bold{font-size:14px;lisne-height:20px;font-weight:bold;}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if(!isonlybody()):?>
            <?php $requirementHistory = $app->session->requirementHistory? $app->session->requirementHistory: inlink('browse')?>
            <?php echo html::a($requirementHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $requirement->code ?></span>
            <span class="text" title='<?php echo htmlspecialchars_decode($requirement->name); ?>'><?php echo htmlspecialchars_decode($requirement->name); ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirementinside->desc; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($requirement->desc) ? $requirement->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirementinside->comment; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($requirement->comment) ? $requirement->comment : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <?php if (!empty($requirement->feedbackStatus) and $requirement->feedbackStatus != ''): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title-bold"><?php echo $lang->requirementinside->analysis; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($requirement->analysis) ? $requirement->analysis : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title-bold"><?php echo $lang->requirementinside->handling; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($requirement->handling) ? $requirement->handling : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title-bold"><?php echo $lang->requirementinside->implement; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($requirement->implement) ? $requirement->implement : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
            </div>
            <?php if(!empty($requirementChangeInfo)):?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->requirementinside->changeRecord; ?></div>
                    <div class="detail-content article-content">
                        <table class="table ops" style="text-align: center">
                            <tr>
                                <th class="w-100px" style="text-align: center"><?php echo $lang->requirementinside->changeNum; ?></th>
                                <th class="w-200px" style="text-align: center"><?php echo $lang->requirementinside->changeTime; ?></th>
                                <th class="w-200px" style="text-align: center"><?php echo $lang->requirementinside->changeCode; ?></th>
<!--                                <th class="w-100px" style="text-align: center">--><?php //echo $lang->requirementinside->changeRemark; ?><!--</th>-->
                            </tr>
                            <?php $num = 1;
                            foreach ($requirementChangeInfo as $val): ?>
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
            </div>
            <?php endif;?>


        <?php endif; ?>

        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=requirement&objectID=$requirement->id"); ?>
        <?php if ($requirement->files): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->requirementinside->fileTitle; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                    <div class="detail-content">
                        <?php
                        foreach ($requirement->files as $key => $file) {
                            echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $requirement, 'canOperate' => $file->addedBy == $this->app->user->account));
                        }; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>


            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php common::printBack($requirementHistory);?>
                    <div class='divider'></div>
                    <?php
                    common::printIcon('requirementinside', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                    common::printIcon('requirementinside', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                    common::printIcon('requirementinside', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');
                    if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy) {
                        if ($requirement->status == 'closed') {
                            common::printIcon('requirementinside', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                        } else {
                            common::printIcon('requirementinside', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                        }
                    }else if($requirement->status == 'closed'){
                        echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                    }else{
                        echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                    }

                    if ($requirement->ignoreStatus) {
                        common::printIcon('requirementinside', 'recover', "requirementID=$requirement->id", $requirement, 'list', 'bell', '', 'iframe', true);
                    } else {
                        common::printIcon('requirementinside', 'ignore', "requirementID=$requirement->id", $requirement, 'list', 'ban', '', 'iframe', true);
                    }
                    common::printIcon('requirementinside', 'delete', "requirementID=$requirement->id", $requirement, 'list', 'trash', '', 'iframe', true);

                    ?>
                </div>
            </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirementinside->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirementinside->opinionID; ?></th>
                            <td><?php echo html::a($this->createLink('opinioninside', 'view', "id=$requirement->opinion"), $opinion->name); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->app; ?></th>
                            <?php
                            $appNames = '';
                            $appList = explode(',', $requirement->app);
                            foreach ($appList as $app) {
                                if ($app) $appNames .= ' ' . zget($apps, $app, '');
                                $appNames .= '<br>';
                            }; ?>
                            <td title='<?php echo $appNames; ?>'><?php echo $appNames; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->acceptTime; ?></th>
                            <td><?php echo $requirement->acceptTime; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->planEnd; ?></th>
                            <td>
                                <?php echo $requirement->planEnd; ?>
                                <!--
                                <?php echo  common::printIcon('requirement', 'editEnd', "requirementID=$requirement->id", $requirement, 'list','edit','','iframe',true) ;?>
                                -->
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->deadLine; ?></th>
                            <td><?php echo $requirement->deadLine; ?></td>
                        </tr>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirementinside->project; ?></th>

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
                            <th><?php echo $lang->requirementinside->taskLaunchTime; ?></th>
                            <td><?php echo $requirement->onlineTimeByDemand; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->productManager; ?></th>
                            <?php
                            $productManagerArray = explode(",", $requirement->productManager);
                            $productManagerChnArray = array();
                            foreach ($productManagerArray as $productManager) {
                                array_push($productManagerChnArray, zget($users, $productManager, ''));
                            }
                            $productManagerChn = trim(implode(",", $productManagerChnArray),','); ?>
                            <td title='<?php echo $productManagerChn; ?>'><?php echo $productManagerChn; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->dept; ?></th>
                            <?php
                            $deptArray = explode(",", $requirement->dept);
                            $deptChnArray = array();
                            foreach ($deptArray as $dept) {
                                array_push($deptChnArray, zget($depts, $dept, ''));
                            }
                            $deptChn = trim(implode(",", $deptChnArray),','); ?>
                            <td title='<?php echo $deptChn; ?>'><?php echo $deptChn; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->owner; ?></th>
                            <?php
                            $ownerArray = explode(",", $requirement->owner);
                            $ownerChnArray = array();
                            foreach ($ownerArray as $owner) {
                                array_push($ownerChnArray, zget($users, $owner, ''));
                            }
                            $ownerChn = trim(implode(",", $ownerChnArray),','); ?>
                            <td><?php echo $ownerChn; ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirementinside->status;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirementinside->status; ?></th>
                            <td><?php echo zget($lang->requirementinside->statusList, $requirement->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->pending; ?></th>
                            <?php
                            $dealUserArray = explode(",", $requirement->dealUser);
                            $dealUserChnArray = array();
                            foreach ($dealUserArray as $dealUser) {
                                array_push($dealUserChnArray, zget($users, $dealUser, ''));
                            }
                            $dealUserChn = trim(implode(",", $dealUserChnArray),','); ?>
                            <td><?php echo $dealUserChn; ?></td>
                        </tr>
                        <?php if($requirement->mailto != ''):?>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirementinside->mailto;?></th>
                                <td><?php foreach(explode(',', $requirement->mailto) as $user) echo zget($users, $user, '') . ' '; ?></td>
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
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirementinside->sourceMode; ?></th>
                            <td><?php echo zget($lang->opinioninside->sourceModeList, $opinion->sourceMode, ''); ?></td>
                        </tr>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirementinside->sourceName; ?></th>
                            <td><?php echo $opinion->sourceName; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->line; ?></th>
                            <?php
                            $lineTitle = '';
                            $lineList = explode(',', str_replace(' ', '', $requirement->line));
                            foreach ($lineList as $lineID) {
                                if ($lineID) {
                                    $lineTitle .= ' ' . zget($lines, $lineID, '');
                                    $lineTitle .= '<br>';
                                }
                            }
                            ?>
                            <td title='<?php echo $lineTitle; ?>'><?php echo $lineTitle; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirementinside->ownproduct; ?></th>
                            <?php
                            $productTitle = '';
                            $productList = explode(',', str_replace(' ', '', $requirement->product));
                            foreach ($productList as $productID) {
                                if ($productID) {
                                    $productTitle .= ' ' . zget($products, $productID, '');
                                    $productTitle .= '<br>';
                                }
                            }
                            ?>
                            <td title='<?php echo $productTitle; ?>'><?php echo $productTitle; ?></td>
                        </tr>
                        <?php if ($opinion->sourceMode == 8):?>
                        <tr>
                            <th><?php echo $lang->requirementinside->isImprovementTitle; ?></th>
                            <td title=''><?php echo $lang->requirementinside->isImprovementServices[$requirement->isImprovementServices]; ?></td>
                        </tr>
                            <tr style="display: none;">
                                <th><?php echo $lang->requirementinside->estimateWorkloadTitle; ?></th>
                                <td title=''><?php echo $requirement->estimateWorkload; ?></td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->requirementinside->projectManager; ?></th>
                            <?php
                            $projectManagerArray = explode(",", $requirement->projectManager);
                            $projectManagerChnArray = array();
                            foreach ($projectManagerArray as $projectManager) {
                                array_push($projectManagerChnArray, zget($users, $projectManager, ''));
                            }
                            $projectManagerChn = trim(implode(",", $projectManagerChnArray),','); ?>
                            <td title='<?php echo $projectManagerChn; ?>'><?php echo $projectManagerChn; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if (!empty($requirement->feedbackStatus) and $requirement->feedbackStatus != ''): ?>
        <?php endif; ?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirementinside->statusTransition;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirementinside->nodeUser;?></th>
<!--                            <td class='text-right'>--><?php //echo $lang->requirementinside->consumed;?><!--</td>-->
                            <td class='text-center'><?php echo $lang->requirementinside->before;?></td>
                            <td class='text-center'><?php echo $lang->requirementinside->after;?></td>
                        </tr>
                        <?php foreach($requirement->consumed as $c):?>
                            <tr>
                                <th class='w-120px'><?php echo zget($users, $c->account, '');?></th>
<!--                                <td class='text-right'>--><?php //echo $c->consumed . ' ' . $lang->hour;?><!--</td>-->
                                <?php
                                echo "<td class='text-center'>".zget($lang->requirementinside->statusList, $c->before, '-')."</td>";
                                echo "<td class='text-center'>".zget($lang->requirementinside->statusList, $c->after, '-')."</td>";
                                ?>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
