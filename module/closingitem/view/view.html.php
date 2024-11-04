<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .detail-content {
        word-wrap: break-word;
        word-break: keep-all;
    }
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $closingitemHistory = $app->session->closingitemHistory ? $app->session->closingitemHistory : inlink('browse', "projectID=$project") ?>
            <?php echo html::a($closingitemHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="label label-id"><?php echo $closingitem->id ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                <tr>
                    <th class="w-180px"><?php echo $lang->closingitem->isAssembly;?></th>
                    <td><?php echo $this->lang->closingitem->typeIsList[$closingitem->isAssembly] ;?> </td>
                    <th class="w-150px"><?php echo $lang->closingitem->assemblyNum;?></th>
                    <td colspan="2"><?php echo $closingitem->assemblyNum;?></td>
                </tr>
            </table>
        </div>
        <div class="page-title" style="text-align: center"><h4><?php echo $lang->closingitem->assemblycontent; ?></h4></div>
        <div class="cell">
            <?php if(!empty($closingitem->assembly)):
                foreach ($closingitem->assembly as $assembly) :?>
                    <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->code;?></th>
                            <td><?php echo $assembly['codes1'];?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->assemblyAlready;?></th>
                            <td colspan="2"><?php echo $components[$assembly['assemblyIndex']];?></td>
                            <th class="w-150px"><?php echo $lang->closingitem->assemblyLevel;?></th>
                            <td><?php echo $levelList[$assembly['assemblyLevel']];?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->assemblyDesc;?></th>
                            <td colspan="6"><?php echo $assembly['assemblyDesc'];?></td>
                        </tr>
                    </table>
                    <hr />
                <?php endforeach;
            else : ?>
                <div class='text-center text-muted'>无</div>
            <?php endif; ?>
        </div>
        <div class="page-title" style="text-align: center"><h4><?php echo $lang->closingitem->assemblyAdvise; ?></h4></div>
        <div class="cell">
            <?php if(!empty($closingitem->assemblyAdvise) and $closingitem->isAssembly == 1):?>
                <?php foreach ($closingitem->advises[1] as $key => $advise) :?>
                    <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->code;?></th>
                            <td><?php echo $key+1;?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->status;?></th>
                            <td><?php echo $adviseStatus[$advise->status];?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->advise;?></th>
                            <td colspan="6"><?php echo $advise->advise;?></td>
                        </tr>
                    </table>
                    <hr />
                <?php endforeach;
            else : ?>
                <div class='text-center text-muted'>无</div>
            <?php endif; ?>
        </div>

        <div class="page-title" style="text-align: center"><h4><?php echo $lang->closingitem->toolsUsage; ?></h4></div>
        <div class="cell">
            <?php if(!empty($closingitem->toolsUsage) and $closingitem->toolsUsage == 1):?>
                <?php foreach ($closingitem->tools as $tool):?>
                    <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->code;?></th>
                            <td><?php echo $tool['codes3']?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->toolsName;?></th>
                            <td colspan="2"><?php echo $tool['toolsName'];?></td>
                            <th class="w-150px"><?php echo $lang->closingitem->toolsVersion;?></th>
                            <td><?php echo $tool['toolsVersion'];?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->isTesting;?></th>
                            <td><?php echo $lang->closingitem->typeIsList[$tool['isTesting']]?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->toolsTypeName;?></th>
                            <td colspan="2"><?php echo $lang->closingitem->toolsType[$tool['toolsType']]?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->toolsDesc;?></th>
                            <td colspan="6"><?php echo $tool['toolsDesc']?> </td>
                        </tr>
                    </table>
                    <hr />
                <?php endforeach;
            else : ?>
                <div class='text-center text-muted'>无</div>
            <?php endif; ?>
        </div>
        <div class="page-title" style="text-align: center"><h4><?php echo $lang->closingitem->toolsAdvise; ?></h4></div>
        <div class="cell">
            <?php if(!empty($closingitem->toolsAdvise) and $closingitem->toolsAdvise == 1):?>
                <?php foreach ($closingitem->advises[2] as $key =>  $advise):?>
                    <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->code;?></th>
                            <td><?php echo $key+1?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->status;?></td>
                            <td><?php echo $adviseStatus[$advise->status];?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->advise;?></th>
                            <td colspan="6"><?php echo $advise->advise;?></td>
                        </tr>
                    </table>
                    <hr />
                <?php endforeach;
            else : ?>
                <div class='text-center text-muted'>无</div>
            <?php endif; ?>
        </div>
        <div class="page-title" style="text-align: center"><h4><?php echo $lang->closingitem->osspAdvise; ?></h4></div>
        <div class="cell">
            <?php if(!empty($closingitem->osspAdvise) and $closingitem->osspAdvise == 1):?>
                <?php foreach ($closingitem->advises[3] as $key =>  $advise):?>
                    <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->code;?></th>
                            <td><?php echo $key+1?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->status;?></td>
                            <td><?php echo $adviseStatus[$advise->status];?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->advise;?></th>
                            <td colspan="6"><?php echo $advise->advise;?></td>
                        </tr>
                    </table>
                    <hr />
                <?php endforeach;
            else : ?>
                <div class='text-center text-muted'>无</div>
            <?php endif; ?>
        </div>

        <div class="page-title" style="text-align: center"><h4><?php echo $lang->closingitem->platformAdvise; ?></h4></div>
        <div class="cell">
            <?php if(!empty($closingitem->platformAdvise) and $closingitem->platformAdvise == 1):?>
                <?php foreach ($closingitem->advises[4] as $key => $advise):?>
                    <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->code;?></th>
                            <td><?php echo $key+1?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->status;?></td>
                            <td><?php echo $adviseStatus[$advise->status];?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->advise;?></th>
                            <td colspan="6"><?php echo $advise->advise;?></td>
                        </tr>
                    </table>
                    <hr />
                <?php endforeach;
            else : ?>
                <div class='text-center text-muted'>无</div>
            <?php endif; ?>
        </div>

        <div class="page-title" style="text-align: center"><h4><?php echo $lang->closingitem->adviseChecklist; ?></h4></div>
        <div class="cell">
            <?php if(!empty($closingitem->adviseChecklist) and $closingitem->adviseChecklist == 1):?>
                <?php foreach ($closingitem->knowledge as $knowledge):?>
                    <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->code;?></th>
                            <td><?php echo $knowledge['codes7']?> </td>
                            <th class="w-150px"><?php echo $lang->closingitem->submitFileName;?></td>
                            <td colspan="4"><?php echo $knowledge['submitFileName'];?></td>
                            <th class="w-150px"><?php echo $lang->closingitem->versionCodeOSSPName;?></th>
                            <td><?php echo $lang->closingitem->versionCodeOSSP[$knowledge['versionCodeOSSP']]?> </td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->submitReason;?></th>
                            <td colspan="6"><?php echo $knowledge['submitReason'];?></td>
                        </tr>
                        <tr>
                            <th class="w-150px"><?php echo $lang->closingitem->comment;?></td>
                            <td colspan="6"><?php echo $knowledge['advise'];?></td>
                        </tr>
                    </table>
                    <hr />
                <?php endforeach;
            else : ?>
                <div class='text-center text-muted'>无</div>
            <?php endif; ?>
        </div>

        <div class="cell">
            <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                <tr>
                    <th class="w-150px"><?php echo $lang->closingitem->realPoints;?></th>
                    <td><?php echo $closingitem->realPoints;?> </td>
                    <th class="w-150px"><?php echo $lang->closingitem->demandAdviseName;?></td>
                    <td colspan="2"><?php
                        $demandArr = explode(',',$closingitem->demandAdvise);
                        $demandStr = '';
                        foreach($demandArr as $demand){
                            $demandStr .= $lang->closingitem->demandAdviseList[$demand].'，';
                        }
                        echo trim($demandStr, '，');?></td>
                    <th class="w-200px"><?php echo $lang->closingitem->constructionAdviseName;?></th>
                    <td><?php
                        $constructionArr = explode(',',$closingitem->constructionAdvise);
                        $constructionStr = '';
                        foreach($constructionArr as $construction){
                            $constructionStr .= $lang->closingitem->constructionAdviseList[$construction].'，';
                        }
                        echo trim($constructionStr, '，');?> </td>
                </tr>
            </table>
            <?php if ($closingitem->files): ?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->closingitem->fileTitle; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                    <div class="detail-content">
                        <?php
                        foreach ($closingitem->files as $key => $file) {
                            echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $closingitem, 'canOperate' => $file->addedBy == $this->app->user->account));
                        }; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($closingitem->projectType == 6): ?>
        <div class="cell">
            <table class='table table-form' style="word-break:break-all; word-wrap:break-all;">
                <tr>
                    <th class="w-150px"><?php echo $lang->closingitem->achievementNumName;?></th>
                    <td><?php echo $closingitem->achievementNum;?> </td>
                    <th class="w-150px"><?php echo $lang->closingitem->planNum;?></td>
                    <td colspan="2"><?php echo $closingitem->achievementNum;?></td>
                    <th class="w-180px"><?php echo $lang->closingitem->outPlanNum;?></th>
                    <td><?php echo $closingitem->outPlanNum;?> </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($closingitemHistory); ?>
                <div class='divider'></div>
                <?php
                common::hasPriv('closingitem','edit') ? common::printIcon('closingitem', 'edit', "closingitemID=$closingitem->id&projectID=$project", $closingitem, 'list') : '';
                common::hasPriv('closingitem','submit') ? common::printIcon('closingitem', 'submit', "closingitemID=$closingitem->id&projectID=$project", $closingitem, 'list','play', 'hiddenwin') : '';
                common::hasPriv('closingitem','review') ? common::printIcon('closingitem', 'review', "closingitemID=$closingitem->id", $closingitem, 'list', 'glasses', '', 'iframe', true) : '';
                common::hasPriv('closingitem','delete') ? common::printIcon('closingitem', 'delete', "closingitemID=$closingitem->id", $closingitem, 'list', 'trash', 'hiddenwin') : '';
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->closingitem->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th><?php echo $lang->closingitem->projectType; ?></th>
                            <td><?php echo zget($typeList, $closingitem->projectType, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->closingitem->status; ?></th>
                            <td><?php echo zget($this->lang->closingitem->browseStatus, $closingitem->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->closingitem->dealUser; ?></th>
                            <?php
                            $dealUserTitle = '';
                            $dealUsersTitles = '';
                            if (!empty($closingitem->dealuser)) {
                                foreach (explode(',', $closingitem->dealuser) as $dealUser) {
                                    if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                                }
                            }
                            $dealUsersTitles = trim($dealUserTitle, ',');
                            ?>
                            <td title='<?php echo $dealUsersTitles; ?>'
                                class='text-ellipsis'><?php echo $dealUsersTitles; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->closingitem->createdBy; ?></th>
                            <td><?php echo zget($users, $closingitem->createdBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->closingitem->createdDate; ?></th>
                            <td><?php echo $closingitem->createdDate; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->closingitem->statusTransition; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->closingitem->nodeUser; ?></th>
                            <td class='text-center'><?php echo $lang->closingitem->before; ?></td>
                            <td class='text-center'><?php echo $lang->closingitem->after; ?></td>
                        </tr>
                        <?php foreach ($closingitem->consumed as $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                <?php
                                echo "<td class='text-center'>" . zget($lang->closingitem->browseStatus, $c->before, '-') . "</td>";
                                echo "<td class='text-center'>" . zget($lang->closingitem->browseStatus, $c->after, '-') . "</td>";?>
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
