<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $application->code?></span>
            <span class="text" title='<?php echo $application->name;?>'><?php echo $application->name;?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->application->desc;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($application->desc) ? $application->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <?php echo $this->fetch('file', 'printFiles', array('files' => $application->files, 'fieldset' => 'true', 'object' => $application));?>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=application&objectID=$application->id");?>
        </div>
        <div class="cell"><?php include '../../../common/view/action.html.php';?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack(inlink('browse', "programID=$application->program"));?>
                <div class='divider'></div>
                <?php
                common::printIcon('application', 'edit', "applicationID=$application->id", $application, 'button');
                common::printIcon('application', 'delete', "applicationID=$application->id", $application, 'button', 'trash', 'hiddenwin');
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->application->basicInfo;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-150px'><?php echo $lang->application->code;?></th>
                            <td><?php echo $application->code;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->isPayment;?></th>
                            <td><?php echo $lang->application->isPaymentList[$application->isPayment];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->team;?></th>
                            <td><?php echo zget($lang->application->teamList, $application->team, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->attribute;?></th>
                            <td><?php echo zget($lang->application->attributeList, $application->attribute);?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->network;?></th>
                            <td><?php echo zget($lang->application->networkList, $application->network);?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->fromUnit;?></th>
                            <td><?php echo zget($lang->application->fromUnitList, $application->fromUnit);?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->version;?></th>
                            <td><?php echo $application->version;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->feature;?></th>
                            <td><?php echo $application->feature;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->range;?></th>
                            <td><?php echo $application->range;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->useDept;?></th>
                            <td><?php echo $application->useDept;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->projectMonth;?></th>
                            <td><?php echo $application->projectMonth;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->productDate;?></th>
                            <td><?php echo $application->productDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->opsDate;?></th>
                            <td><?php echo $application->opsDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->promote;?></th>
                            <td><?php echo $application->promote;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->protectLevel;?></th>
                            <td><?php echo $application->protectLevel;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->continueLevel;?></th>
                            <td><?php echo $application->continueLevel;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->securityLevel;?></th>
                            <td><?php echo $application->securityLevel;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->cityBak;?></th>
                            <td><?php echo $application->cityBak;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->offsiteBak;?></th>
                            <td><?php echo $application->offsiteBak;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->vendor;?></th>
                            <td><?php echo $application->vendor;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->vendorContact;?></th>
                            <td><?php echo $application->vendorContact;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->retrofit;?></th>
                            <td><?php echo $application->retrofit;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->runStatus;?></th>
                            <td><?php echo $application->runStatus;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->runUnit;?></th>
                            <td><?php echo $application->runUnit;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->runLeader;?></th>
                            <td><?php echo $application->runLeader;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->runDept;?></th>
                            <td><?php echo $application->runDept;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->owner;?></th>
                            <td><?php echo $application->owner;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->opsLeader;?></th>
                            <td><?php echo $application->opsLeader;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->opsDept;?></th>
                            <td><?php echo $application->opsDept;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->opsManager;?></th>
                            <td><?php echo $application->opsManager;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->info;?></th>
                            <td><?php echo $application->info;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->createdBy;?></th>
                            <td><?php echo zget($users, $application->createdBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->createdDate;?></th>
                            <td><?php echo $application->createdDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->editedBy;?></th>
                            <td><?php echo zget($users, $application->editedBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->application->editedDate;?></th>
                            <td><?php echo $application->editedDate;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
