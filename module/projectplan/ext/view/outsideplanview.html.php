<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php $browseLink = $app->session->outsideplanList != false ? $app->session->outsideplanList : inlink('browse');?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php printf('%03d', $plan->id);?></span>
            <span class="text" title='<?php echo $plan->name;?>'><?php echo $plan->name;?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->outsideplan->content;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($plan->content) ? $plan->content : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->outsideplan->projectCreation;?></div>
                <div class="detail-content article-content">
                    <?php if(empty($plan->creation)):?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                    <?php else:?>
                        <table class='table'>
                            <tr>
                                <th class='w-150px'><?php echo $lang->outsideplan->code;?></th>
                                <td><?php echo $plan->creation->code;?></td>
                                <th class='w-80px'><?php echo $lang->outsideplan->mark;?></th>
                                <td><?php echo $plan->creation->mark;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->name;?></th>
                                <td colspan='3'><?php echo $plan->creation->name;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->PM;?></th>
                                <td><?php echo zget($users, $plan->creation->PM, '');?></td>
                                <th><?php echo $lang->outsideplan->dept;?></th>
                                <td><?php echo zget($depts, $plan->creation->dept, '');?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->source;?></th>
                                <td colspan='3'><?php echo zget($lang->outsideplan->sourceList, $plan->creation->source, '');?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->opinion->union;?></th>
                                <td colspan='3'><?php echo zget($lang->opinion->unionList, $plan->creation->union, '');?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->type;?></th>
                                <td colspan='3'><?php echo zget($lang->outsideplan->typeList, $plan->creation->type, '');?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->linkPlan;?></th>
                                <td colspan='3'>
                                    <?php
                                    //$linkPlans = explode(',', trim($plan->creation->linkPlan));
                                    //foreach($linkPlans as $linkPlan) echo zget($plans, $linkPlan) . '&nbsp;';
                                    foreach(explode(',', $plan->linkedPlan) as $linkPlan) echo zget($plans, $linkPlan) . '&nbsp;';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->beginAndEnd;?></th>
                                <td colspan='3'><?php echo $plan->creation->begin . ' - ' . $plan->creation->end;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->workload . "({$lang->outsideplan->monthly})";?></th>
                                <td colspan='3'><?php echo $plan->creation->workload;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->background;?></th>
                                <td colspan='3'><?php echo $plan->creation->background;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->range;?></th>
                                <td colspan='3'><?php echo $plan->creation->range;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->goal;?></th>
                                <td colspan='3'><?php echo $plan->creation->goal;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->stakeholder;?></th>
                                <td colspan='3'><?php echo $plan->creation->stakeholder;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outsideplan->verify;?></th>
                                <td colspan='3'><?php echo $plan->creation->verify;?></td>
                            </tr>
                        </table>
                    <?php endif;?>
                </div>
            </div>
            <?php if($plan->creation) echo $this->fetch('file', 'printFiles', array('files' => $plan->creation->files, 'fieldset' => 'true'));?>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=outsideplan&objectID=$plan->id");?>
        </div>
        <div class="cell"><?php include '../../common/view/action.html.php';?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack(inlink('browse'));?>
                <div class='divider'></div>
                <?php
                common::printIcon('outsideplan', 'edit', "outsideplanID=$plan->id", $plan, 'list');
                common::printIcon('outsideplan', 'delete', "outsideplanID=$plan->id", $plan, 'button', 'trash', 'hiddenwin');
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->outsideplan->basicInfo;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->opinion->category;?></th>
                            <td><?php echo zget($lang->opinion->categoryList, $plan->category, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->status;?></th>
                            <td><?php echo zget($lang->outsideplan->statusList, $plan->status, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->year;?></th>
                            <td><?php echo $plan->year;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->code;?></th>
                            <td><?php echo $plan->code;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->mark;?></th>
                            <td><?php echo $plan->mark;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->type;?></th>
                            <td><?php echo zget($lang->outsideplan->typeList, $plan->type, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->basis;?></th>
                            <td><?php echo zget($lang->outsideplan->basisList, $plan->basis, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->app;?></th>
                            <td><?php echo $plan->app ? zget($apps, $plan->app, ''): '';?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->line;?></th>
                            <td>
                                <?php
                                $lineList = explode(',', str_replace(' ', '', $plan->line));
                                foreach($lineList as $lineID)
                                {
                                    if($lineID) echo ' ' . zget($lines, $lineID, '');
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->storyStatus;?></th>
                            <td><?php echo zget($lang->outsideplan->storyStatusList, $plan->storyStatus, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->linkedPlan;?></th>
                            <td>
                                <?php
                                if(isset($plan->linkedPlan))
                                {
                                    $ps = explode(',', str_replace(' ', '', $plan->linkedPlan));
                                    foreach($ps as $planID)
                                    {
                                        if($planID) echo '<p>' .zget($plans, $planID, '') . '</p>';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->opinion->category;?></th>
                            <td><?php echo zget($lang->opinion->categoryList, $plan->category, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->structure;?></th>
                            <td><?php echo zget($lang->outsideplan->structureList, $plan->structure, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->localize;?></th>
                            <td><?php echo zget($lang->outsideplan->localizeList, $plan->localize, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->begin;?></th>
                            <td><?php echo $plan->begin == '0000-00-00' ? '' : $plan->begin;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->end;?></th>
                            <td><?php echo $plan->end == '0000-00-00' ? '' : $plan->end;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->workload;?></th>
                            <td><?php if($plan->workload) echo $plan->workload . ' ' . $lang->outsideplan->monthly;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->duration;?></th>
                            <td><?php if($plan->duration) echo $plan->duration . ' ' . $lang->outsideplan->day;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->bearDept;?></th>
                            <td><?php echo zget($depts, $plan->bearDept);?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->depts;?></th>
                            <td>
                                <?php
                                if(isset($plan->depts))
                                {
                                    $planDepts = explode(',', str_replace(' ', '', $plan->depts));
                                    foreach($planDepts as $deptID)
                                    {
                                        if($deptID) echo ' ' . zget($depts, $deptID, '');
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->reviewDate;?></th>
                            <td><?php echo $plan->reviewDate === '0000-00-00' ? '' : $plan->reviewDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->owner;?></th>
                            <td><?php echo zget($users, $plan->owner, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->phone;?></th>
                            <td><?php echo $plan->phone;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->createdBy;?></th>
                            <td><?php echo zget($users, $plan->createdBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outsideplan->createdDate;?></th>
                            <td><?php echo $plan->createdDate;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
