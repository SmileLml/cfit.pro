<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .table td.content div {height: 25px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; float: left; max-width: calc(100% - 20px);}
    .table td.content .more {height: 25px; float: left;}
    td.c-branch {overflow: hidden; text-align: left !important; text-overflow: ellipsis; white-space: nowrap;}

    .table-children {border-left: 2px solid #cbd0db; border-right: 2px solid #cbd0db;}
    .table tbody > tr.table-children.table-child-top {border-top: 2px solid #cbd0db;}
    .table tbody > tr.table-children.table-child-bottom {border-bottom: 2px solid #cbd0db;}
    .table td.has-child > a {max-width: 90%; max-width: calc(100% - 30px); display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
    .table td.has-child > .task-toggle {color: #838a9d; position: relative; top: 1px; line-height: 16px;}
    .table td.has-child > .task-toggle:hover {color: #006af1; cursor: pointer;}
    .table td.has-child > .task-toggle > .icon {font-size: 16px; display: inline-block; transition: transform .2s; -ms-transform: rotate(-90deg); -moz-transform: rotate(-90deg); -o-transform: rotate(-90deg); -webkit-transform: rotate(-90deg); transform: rotate(-90deg);}
    .table td.has-child > .task-toggle > .icon:before {text-align: left;}
    .table td.has-child > .task-toggle.collapsed {top: 2px;}
    .table td.has-child > .task-toggle.collapsed > .icon {-ms-transform: rotate(90deg); -moz-transform: rotate(90deg); -o-transform: rotate(90deg); -webkit-transform: rotate(90deg); transform: rotate(90deg);}
    .main-table tbody > tr.table-children > td:first-child::before {width: 3px;}
    @-moz-document url-prefix() {.main-table tbody > tr.table-children > td:first-child::before {width: 4px;};}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $componentpublicHistory = $app->session->componentpublicHistory ? $app->session->componentpublicHistory : inlink('browse') ?>
            <?php echo html::a($componentpublicHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="text"
                  title='<?php echo $componentpublic->name; ?>'><?php echo $componentpublic->name; ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->componentpublic->basicinfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->name; ?></th>
                            <td><?php echo $componentpublic->name ?></td>
                            <?php if ($app->user->account == $componentpublic->maintainer or in_array($app->user->account, $componentpublic->pmrm)): ?>
                                <th><?php common::printIcon('componentpublic', 'editinfo', "componentpublicId=$componentpublic->id", $componentpublic, 'view-button', 'edit', '', 'btn btn-secondary iframe', true); ?></th>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->latestVersion; ?></th>
                            <td><?php echo $componentpublic->latestVersion ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->level; ?></th>
                            <td><?php echo zget($lang->componentpublic->levelList, $componentpublic->level); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->category; ?></th>
                            <td><?php echo zget($lang->component->categoryList, $componentpublic->category); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->functionDesc; ?></th>
                            <td><?php echo $componentpublic->functionDesc; ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->location; ?></th>
                            <td><?php echo $componentpublic->location; ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->maintainerDept; ?></th>
                            <td><?php echo zget($depts, $componentpublic->maintainerDept); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->maintainer; ?></th>
                            <td><?php echo zget($users, $componentpublic->maintainer); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->component->relationgit; ?></th>
                            <?php
                            $gitlabname = '';
                            $gitlablist = json_decode($componentpublic->gitlab);
                            if($gitlablist){
                                foreach($gitlablist as $key=>$gitval){
                                    $gitlabname .= $gitval.'<br />';
                                }
                            }
                            ?>
                            <td ><?php echo $gitlabname; ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->developLanguage; ?></th>
                            <td><?php echo zget($lang->component->developLanguageList, $componentpublic->developLanguage); ?></td>
                        </tr>
                        <tr>
                            <th class='w-150px'><?php echo $lang->componentpublic->status; ?></th>
                            <td><?php echo zget($lang->component->publishStatusList, $componentpublic->status); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->componentpublic->detailInfo; ?></div>
                <div class='detail-content'>
                    <div class="btn-toolbar pull-right">
                        <?php common::printIcon('componentpublic', 'createversion', "componentpublicId=$componentpublic->id", $componentpublic, 'view-button', 'plus', '', 'btn btn-secondary iframe', true); ?>
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
                            <table class='table table-fixed has-sort-head' id="versionList">
                                <thead>
                                <tr>
                                    <th class='w-60px'><?php echo $lang->componentpublic->code; ?></th>
                                    <th class='w-100px'><?php echo $lang->componentpublic->version; ?></th>
                                    <th class='w-80px'><?php echo $lang->componentpublic->updatedDate; ?></th>
                                    <th class='w-250px'><?php echo $lang->componentpublic->desc; ?></th>
                                    <th class='w-120px'><?php echo $lang->componentpublic->useFile; ?></th>
                                    <th class='w-50px'><?php echo $lang->componentpublic->usedNum; ?></th>
                                    <th class='text-center w-80px'><?php echo $lang->actions; ?></th>
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
                                        <td class='text-left content'>
                                            <?php $desc = trim(strip_tags(str_replace(array('</p>', '<br />', '<br>', '<br/>'), "\n", str_replace(array("\n", "\r"), '', $data->desc)), '<img>'));?>
                                            <div title='<?php echo $desc;?>' style="line-height:16px"><?php echo nl2br($desc);?></div>
                                        </td>
                                        <td style="white-space: normal"><?php
                                            foreach ($data->files as $key => $file) {
                                                echo $this->fetch('file', 'printfilesbycomponent', array('files' => array($key => $file)));
                                            }; ?></td>
                                        <td class='text-ellipsis'
                                            title="<?php echo $data->usedNum; ?>"><?php echo common::hasPriv('componentpublicaccount', 'browse') ? html::a($this->createLink('componentpublicaccount', 'browse', "browseType=componentVersionId&param=$data->id"), $data->usedNum) : $data->usedNum;?></td>
                                        <td class='c-actions text-center' style="overflow:visible">
                                            <?php
                                            common::printIcon('componentpublic', 'viewversion', "versionID=$data->id", $componentpublic, 'list', 'eye', '', 'iframe', true);
                                            common::printIcon('componentpublic', 'editversion', "versionID=$data->id", $componentpublic, 'list', 'edit', '', 'iframe', true);
                                            common::printIcon('componentpublic', 'deleteversion', "versionID=$data->id", $componentpublic, 'list', 'trash', '', 'iframe', true);
                                            common::hasPriv('componentpublicaccount', 'create') ? common::printIcon('componentpublicaccount', 'create', "componentId=$componentpublic->id&versionID=$data->id", $componentpublic, 'list', 'hand-right', '', '', false) : '';
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
                <div class="detail-title"><?php echo $lang->componentpublic->reviewInfo; ?></div>
                <div class='detail-content'>
                    <?php if (empty($component)): ?>
                        <div class="table-empty-tip">
                            <p>
                                <span class="text-muted"><?php echo $lang->componentpublic->noComponentRequest; ?></span>
                            </p>
                        </div>
                    <?php else: ?>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentpublic->componentRequest; ?></th>
                                <td><?php echo html::a($this->createLink('component', 'view', 'id=' . $component->id, '', true), $component->name, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentpublic->componentRequestCreater; ?></th>
                                <td><?php echo zget($users, $component->createdBy); ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentpublic->componentRequestCreateDate; ?></th>
                                <td><?php echo $component->createdDate ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->componentpublic->componentRequestPassDate; ?></th>
                                <td><?php echo $component->reviewTime ?></td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->componentpublic->relationComponentReview; ?></div>
                <div class='detail-content'>
                    <?php if (empty($relationComponentList)): ?>
                        <div class="table-empty-tip">
                            <p>
                                <span class="text-muted"><?php echo $lang->componentpublic->noRelationComponentReview; ?></span>
                            </p>
                        </div>
                    <?php else: ?>
                        <table class='table table-data'>
                            <thead>
                            <tr>
                                <th class="w-60px"><?php echo $lang->componentpublic->code;?></th>
                                <th><?php echo $lang->componentpublic->componentRequest;?></th>
                                <th class="w-60px"><?php echo $lang->componentpublic->componentRequestCreater;?></th>
                                <th><?php echo $lang->componentpublic->componentRequestCreateDate;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($relationComponentList as $relationComponent){
                                ?>
                                <tr>
                                    <td ><?php echo $relationComponent->id; ?></td>
                                    <td><?php echo html::a($this->createLink('component', 'view', 'id=' . $relationComponent->id, '', true), $relationComponent->name, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ?></td>
                                    <td ><?php echo zget($users,$relationComponent->createdBy); ?></td>
                                    <td ><?php echo $relationComponent->createdDate; ?></td>
                                </tr>
                                <?php

                            }
                            ?>

                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=componentpublic&objectID=$componentpublic->id"); ?>
        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
    </div>
</div>
<script>
    $(function()
    {
        $('#versionList tbody tr').each(function()
        {
            var $content = $(this).find('td.content');
            var content  = $content.find('div').html();
            if(content.indexOf('<br') >= 0)
            {
                var index = getNum(content, '<br');
                if(index >= 5){
                    index = 4;
                    $content.append("<a href='###' class='more' style='padding-top: 32.5px'><i class='icon icon-chevron-double-down'></i></a>");
                }
                $content.find('div').css('height', (index+1)*16+'px');
            }
        });
    });
    $(document).on('click', 'td.content .more', function(e)
    {
        var $toggle = $(this);
        if($toggle.hasClass('open'))
        {
            $toggle.removeClass('open');
            //$toggle.closest('.content').find('div').css('height', '25px');
            var content  = $toggle.closest('.content').find('div').html();
            if(content.indexOf('<br') >= 0)
            {
                var index = getNum(content, '<br');
                if(index >= 5){
                    index = 4;
                }
                $toggle.closest('.content').find('div').css('height', (index+1)*16+'px');
                $toggle.css('padding-top', ($toggle.closest('.content').find('div').height() - $toggle.height()) / 2);
            }
            //$toggle.css('padding-top', 0);
            $toggle.find('i').removeClass('icon-chevron-double-up').addClass('icon-chevron-double-down');
        }
        else
        {
            $toggle.addClass('open');
            $toggle.closest('.content').find('div').css('height', 'auto');
            $toggle.css('padding-top', ($toggle.closest('.content').find('div').height() - $toggle.height()) / 2);
            $toggle.find('i').removeClass('icon-chevron-double-down').addClass('icon-chevron-double-up');
        }
    });

    function getNum(str, match){
        var array = str.split(match);
        return array.length-1;
    }
</script>
<?php include '../../common/view/footer.html.php'; ?>
