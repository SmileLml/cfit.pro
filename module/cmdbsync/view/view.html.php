<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
</style>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php if (!isonlybody()): ?>
                <?php $browseLink = $app->session->cmdbsyncList != false ? $app->session->cmdbsyncList : inlink('browse'); ?>
                <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
                <div class="divider"></div>
            <?php endif; ?>
            <div class="page-title">
                <span class="label label-id"><?php echo $cmdbsyncInfo->id ?></span>
            </div>
        </div>
    </div>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">
            <?php if(!empty($cmdbsyncInfo->addInfo)):?>
                <?php foreach ($cmdbsyncInfo->addInfo as $addInfo):?>
                    <div class="cell">
                        <div class="detail">
                            <div class="detail">
                                <div class="detail-title"><?php echo $lang->cmdbsync->addApp.$addInfo->name; ?></div>
                                <div class='detail-content'>
                                    <table class='table table-data'>
                                        <tbody>
                                            <?php $i = 1;foreach ($this->lang->cmdbsync->apiItem as $v):?>
                                                <?php if($i%2==1):?>
                                                    <tr>
                                                        <th class='w-160px'><?php echo $v['name']; ?></th>
                                                        <td><?php $key = $v['target'];
                                                            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                $langKey = $v['lang'];
                                                                if($v['single'] == '1'){
                                                                    echo zget($this->lang->application->$langKey, $addInfo->$key);
                                                                }else{
                                                                    echo zmget($this->lang->application->$langKey, $addInfo->$key);
                                                                }
                                                            }else{
                                                                echo $addInfo->$key;
                                                            }
                                                         ?></td>
                                                    <?php if($i == count($this->lang->cmdbsync->apiItem)-1):?>
                                                    </tr>
                                                    <?php endif;?>
                                                <?php else:?>
                                                        <th class='w-160px'><?php echo $v['name']; ?></th>
                                                        <td><?php $key = $v['target'];
                                                            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                $langKey = $v['lang'];
                                                                if($v['single'] == '1'){
                                                                    echo zget($this->lang->application->$langKey, $addInfo->$key);
                                                                }else{
                                                                    echo zmget($this->lang->application->$langKey, $addInfo->$key);
                                                                }
                                                            }else{
                                                                echo $addInfo->$key;
                                                            }
                                                        ?></td>
                                                    </tr>
                                                <?php endif; $i++;?>
                                            <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
            <?php if(!empty($cmdbsyncInfo->deleteInfo)):?>
                <?php foreach ($cmdbsyncInfo->deleteInfo as $deleteInfo):?>
                    <div class="cell">
                        <div class="detail">
                            <div class="detail">
                                <div class="detail-title"><?php echo $lang->cmdbsync->deleteApp?><?php  echo html::a($this->createLink('application', 'view', 'appID=' . $deleteInfo->id, '', true), $deleteInfo->name, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></div>
                                <div class='detail-content'>
                                    <table class='table table-data'>
                                        <tbody>
                                            <tr>
                                                <th class='w-160px'><?php echo $lang->cmdbsync->appId; ?></th>
                                                <td><?php echo $deleteInfo->id; ?></td>
                                            </tr>
                                            <tr>
                                                <th class='w-160px'><?php echo $lang->cmdbsync->appCode; ?></th>
                                                <td><?php echo $deleteInfo->code; ?></td>
                                                <th class='w-160px'><?php echo $lang->cmdbsync->appName; ?></th>
                                                <td><?php echo $deleteInfo->name; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
            <?php if(!empty($cmdbsyncInfo->updateInfo)):?>
                <?php foreach ($cmdbsyncInfo->updateInfo as $updateInfo):?>
                    <div class="cell">
                        <div class="detail">
                            <div class="detail">
                                <div class="detail-title"><?php echo $lang->cmdbsync->updateApp?><?php echo html::a($this->createLink('application', 'view', 'appID=' .($cmdbsyncInfo->type == 'cmdb'?$cmdbsyncInfo->app:$updateInfo->id->new), '', true), $updateInfo->name->old, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></div>
                                <div class='detail-content'>
                                    <table class='table table-data'>
                                        <tbody>
                                        <?php $i = 1; $itemList = $this->lang->cmdbsync->putcmdbsysncApiItem;
                                        if($cmdbsyncInfo->type == 'putproduction'){
                                            $itemList = $this->lang->cmdbsync->apiItem;
                                        }
                                        foreach ($itemList as $v):?>
                                            <?php if($i%2==1):?>
                                                <tr>
                                                <th class='w-160px'><?php echo $v['name']; ?></th>
                                                <?php $key = $v['target'];if($updateInfo->$key->isColumnDiffer):?>
                                                    <td><?php
                                                        if(empty($updateInfo->$key->old)){
                                                            echo '空';
                                                        }else{
                                                            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                $langKey = $v['lang'];
                                                                if($v['single'] == '1'){
                                                                    echo zget($this->lang->application->$langKey, $updateInfo->$key->old);
                                                                }else{
                                                                    echo zmget($this->lang->application->$langKey, $updateInfo->$key->old);
                                                                }
                                                            }else{
                                                                echo $updateInfo->$key->old;
                                                            }
                                                        }
                                                        ?><i class="icon icon-arrow-right" style="color:red"></i>
                                                        <?php
                                                            if(empty($updateInfo->$key->new)){
                                                                echo '空';
                                                            }else{
                                                                if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                    $langKey = $v['lang'];
                                                                    if($v['single'] == '1'){
                                                                        echo zget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                    }else{
                                                                        echo zmget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                    }
                                                                }else{
                                                                    echo $updateInfo->$key->new;
                                                                }
                                                            }
                                                        ?></td>
                                                <?php else:?>
                                                    <td><?php
                                                        if(empty($updateInfo->$key->new)){
                                                            echo '空';
                                                        }else{
                                                            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                $langKey = $v['lang'];
                                                                if($v['single'] == '1'){
                                                                    echo zget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                }else{
                                                                    echo zmget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                }
                                                            }else{
                                                                echo $updateInfo->$key->new;
                                                            }
                                                        }
                                                        ?></td>
                                                <?php endif;?>
                                                <?php if($i == count($this->lang->cmdbsync->apiItem)-1):?>
                                                </tr>
                                                <?php endif;?>
                                            <?php else:?>
                                                <th class='w-160px'><?php echo $v['name']; ?></th>
                                                <?php $key = $v['target'];if($updateInfo->$key->isColumnDiffer):?>
                                                    <td><?php
                                                        if(empty($updateInfo->$key->old)){
                                                            echo '空';
                                                        }else{
                                                            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                $langKey = $v['lang'];
                                                                if($v['single'] == '1'){
                                                                    echo zget($this->lang->application->$langKey, $updateInfo->$key->old);
                                                                }else{
                                                                    echo zmget($this->lang->application->$langKey, $updateInfo->$key->old);
                                                                }
                                                            }else{
                                                                echo $updateInfo->$key->old;
                                                            }
                                                        }
                                                        ?><i class="icon icon-arrow-right" style="color:red"></i>
                                                        <?php
                                                        if(empty($updateInfo->$key->new)){
                                                            echo '空';
                                                        }else{
                                                            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                $langKey = $v['lang'];
                                                                if($v['single'] == '1'){
                                                                    echo zget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                }else{
                                                                    echo zmget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                }
                                                            }else{
                                                                echo $updateInfo->$key->new;
                                                            }
                                                        }
                                                        ?></td>
                                                <?php else:?>
                                                    <td><?php
                                                        if(empty($updateInfo->$key->new)){
                                                            echo '空';
                                                        }else{
                                                            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                                                                $langKey = $v['lang'];
                                                                if($v['single'] == '1'){
                                                                    echo zget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                }else{
                                                                    echo zmget($this->lang->application->$langKey, $updateInfo->$key->new);
                                                                }
                                                            }else{
                                                                echo $updateInfo->$key->new;
                                                            }
                                                        }
                                                        ?></td>
                                                <?php endif;?>
                                                </tr>
                                            <?php endif; $i++;?>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php common::printBack($browseLink); ?>
                    <div class='divider'></div>
                    <?php
                    common::printIcon('cmdbsync', 'deal', "id=$cmdbsyncInfo->id", $cmdbsyncInfo, 'button', 'time', '', 'iframe', true);
                    common::printIcon('cmdbsync', 'repush', "id=$cmdbsyncInfo->id", $cmdbsyncInfo, 'button', 'share', '', 'iframe', true);
                    ?>
                </div>
            </div>
        </div>
        <!-- 右侧基础信息 -->
        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->cmdbsync->baseinfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class="w-120px"><?php echo $lang->cmdbsync->status; ?></th>
                                <td><?php echo zget($lang->cmdbsync->statusList,$cmdbsyncInfo->status) ; ?></td>
                            </tr>
                            <tr>
                                <th class="w-120px"><?php echo $lang->cmdbsync->dealUser; ?></th>
                                <td><?php echo zmget($users,$cmdbsyncInfo->dealUser) ; ?></td>
                            </tr>
                            <tr>
                                <th class="w-120px"><?php echo $lang->cmdbsync->type; ?></th>
                                <td><?php echo zget($lang->cmdbsync->typeList,$cmdbsyncInfo->type) ; ?></td>
                            </tr>
                            <?php if($cmdbsyncInfo->type == 'putproduction'):?>
                            <tr>
                                <th class="w-120px"><?php echo $lang->cmdbsync->putproductionNumber; ?></th>
                                <td><?php echo html::a($this->createLink('putproduction', 'view', 'id=' . $putproductionInfo->id, '', true), $putproductionInfo->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></td>
                            </tr>
                            <?php endif;?>
                            <tr>
                                <th class="w-120px"><?php echo $lang->cmdbsync->createdDate; ?></th>
                                <td><?php echo $cmdbsyncInfo->createdDate ; ?></td>
                            </tr>
                            <tr>
                                <th class="w-120px"><?php echo $lang->cmdbsync->sendStatus; ?></th>
                                <td><?php echo zget($lang->cmdbsync->sendStatusList, $cmdbsyncInfo->sendStatus) ; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../../common/view/footer.html.php'; ?>