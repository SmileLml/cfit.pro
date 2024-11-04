<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $componentthirdHistory = $app->session->componentthirdHistory ? $app->session->componentthirdHistory : inlink('browse') ?>
            <?php echo html::a($componentthirdHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="text"
                  title='<?php echo $componentthird->name; ?>'><?php echo $componentthird->name; ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->componentthird->basicinfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->name; ?></th>
                            <td><?php echo $componentthird->name ?></td>
                            <?php if (in_array($app->user->account, $componentthird->pmrm)): ?>
                                <th><?php common::printIcon('componentthird', 'editinfo', "componentthirdId=$componentthird->id", $componentthird, 'view-button', 'edit', '', 'btn btn-secondary iframe', true); ?></th>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->baseline; ?></th>
                            <td><?php echo $componentthird->baseline; ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->recommendVersion; ?></th>
                            <td><?php echo $componentthird->recommendVersion; ?></td>
                        </tr>
                        <tr class="hidden">
                            <th class='w-150px'><?php echo $lang->componentthird->versionDate; ?></th>
                            <td><?php echo $componentthird->versionDate; ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->category; ?></th>
                            <td><?php echo zget($lang->component->thirdcategoryList, $componentthird->category); ?></td>
                        </tr>
                        <!--<tr>
                            <th class='w-150px'><?php /*echo $lang->componentthird->chineseClassify; */?></th>
                            <td><?php /*echo zget($lang->component->chineseClassifyList, $componentthird->chineseClassify); */?></td>
                        </tr>-->
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->englishClassify; ?></th>
                            <td><?php echo zget($lang->component->englishClassifyList, $componentthird->englishClassify); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->licenseType; ?></th>
                            <td><?php echo $componentthird->licenseType; ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->developLanguage; ?></th>
                            <td><?php echo zget($lang->component->developLanguageList, $componentthird->developLanguage); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentthird->status; ?></th>
                            <td><?php echo zget($lang->component->thirdStatusList, $componentthird->status); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->componentthird->detailInfo; ?></div>
                <div class='detail-content'>
                    <div class="btn-toolbar pull-right">
                        <?php common::printIcon('componentthird', 'createversion', "componentthirdId=$componentthird->id", $componentthird, 'view-button', 'plus', '', 'btn btn-secondary iframe', true); ?>
                    </div>
                    <?php if (empty($detailDatas)): ?>
                        <div class="table-empty-tip">
                            <p>
                                <span class="text-muted"><?php echo $lang->noData; ?></span>
                            </p>
                        </div>
                    <?php else: ?>
                        <form class='main-table' data-ride='table' data-nested='true'
                              data-checkable='false'>
                            <table class='table table-fixed'>
                                <thead>
                                <tr>
                                    <th class='w-60px'><?php echo $lang->componentthird->code; ?></th>
                                    <th class='w-100px'><?php echo $lang->componentthird->version; ?></th>
                                    <th class='w-80px'><?php echo $lang->componentthird->updatedDate; ?></th>
                                    <th class='w-180px'><?php echo $lang->componentthird->vulnerabilityLevel; ?></th>
                                    <th class='w-60px'><?php echo $lang->componentthird->usedNum; ?></th>
                                    <th class='text-center w-120px'><?php echo $lang->actions; ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($detailDatas as $data): ?>
                                    <tr>
                                        <td class='text-ellipsis'
                                            title="<?php echo $data->code; ?>"><?php echo $data->code; ?></td>
                                        <td class='text-ellipsis'
                                            title="<?php echo $data->version; ?>"><?php echo $data->version; ?></td>
                                        <td class='text-ellipsis'
                                            title="<?php echo $data->updatedDate; ?>"><?php echo $data->updatedDate; ?></td>
                                        <td class='text-ellipsis'
                                            title="<?php echo zget($lang->componentthird->vulnerabilityLevelList, $data->vulnerabilityLevel); ?>"><?php echo zget($lang->componentthird->vulnerabilityLevelList, $data->vulnerabilityLevel); ?></td>
                                        <td class='text-ellipsis'
                                            title="<?php echo $data->usedNum; ?>"><?php echo common::hasPriv('componentthirdaccount', 'browse') ? html::a($this->createLink('componentthirdaccount', 'browse', "browseType=componentVersionId&param=$data->id"), $data->usedNum) : $data->usedNum;?></td>
                                        <td class='c-actions text-center' style="overflow:visible">
                                            <?php
                                            common::printIcon('componentthird', 'editversion', "versionID=$data->id", $componentthird, 'list', 'edit', '', 'iframe', true);
                                            common::printIcon('componentthird', 'deleteversion', "versionID=$data->id", $componentthird, 'list', 'trash', '', 'iframe', true);
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class='main-actions'>
            <div class="btn-toolbar">

            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->componentthird->reviewInfo; ?></div>
                <div class='detail-content'>
                    <?php if (empty($component)): ?>
                        <div class="table-empty-tip">
                            <p>
                                <span class="text-muted"><?php echo $lang->componentthird->noComponentRequest; ?></span>
                            </p>
                        </div>
                    <?php else: ?>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentthird->componentRequest; ?></th>
                                <td><?php echo html::a($this->createLink('component', 'view', 'id=' . $component->id, '', true), $component->name, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentthird->componentRequestCreater; ?></th>
                                <td><?php echo zget($users, $component->createdBy); ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentthird->componentRequestCreateDate; ?></th>
                                <td><?php echo $component->createdDate ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentthird->componentRequestPassDate; ?></th>
                                <td><?php echo $component->reviewTime ?></td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=componentthird&objectID=$componentthird->id"); ?>
        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
