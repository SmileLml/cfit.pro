<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php $closingAdviseHistory = $app->session->closingAdviseHistory ? $app->session->closingAdviseHistory : inlink('browse', "projectID=$projectId") ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php echo html::backButton('<i class="icon icon-back icon-sm"></i>' . $lang->goback , '','btn btn-secondary');?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $closingadvise->id;?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class='cell'>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->closingadvise->advise;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($closingadvise->advise) ? $closingadvise->advise : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        </div>
        <div class='cell'>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->closingadvise->comment;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($closingadvise->comment) ? $closingadvise->comment : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        </div>
        <div class='cell'><?php include '../../common/view/action.html.php';?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($closingAdviseHistory); ?>
                <div class='divider'></div>
                <?php
                common::hasPriv('closingadvise','review') ? common::printIcon('closingadvise', 'review', "closingadviseID=$closingadvise->id", $closingadvise, 'list', 'checked', '', 'iframe', true) : '';
                common::hasPriv('closingadvise','assignUser') ? common::printIcon('closingadvise', 'assignUser', "closingadviseID=$closingadvise->id", $closingadvise, 'list', 'hand-right', '', 'iframe', true) : '';
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->closingadvise->issueInfo;?></div>
                <div class="detail-content">
                    <table class="table table-data">
                        <tbody>
                        <tr>
                            <th class="w-100px"><?php echo $lang->closingadvise->type;?></th>
                            <td><?php echo zget($this->lang->closingadvise->sourceList, $closingadvise->source, '');?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->closingadvise->status;?></th>
                            <td><?php echo zget($lang->closingadvise->browseStatus + $feedbackResults, $closingadvise->status);?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->closingadvise->dealUser; ?></th>
                            <?php
                            $dealUserTitle = '';
                            $dealUsersTitles = '';
                            if (!empty($closingadvise->dealuser)) {
                                foreach (explode(',', $closingadvise->dealuser) as $dealUser) {
                                    if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                                }
                            }
                            $dealUsersTitles = trim($dealUserTitle, ',');
                            ?>
                            <td title='<?php echo $dealUsersTitles; ?>'
                                class='text-ellipsis'><?php echo $dealUsersTitles; ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->closingadvise->createdBy;?></th>
                            <td><?php echo zget($users, $closingadvise->createdBy);?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->closingadvise->createdDate;?></th>
                            <td><?php echo $closingadvise->createdDate;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'?>
