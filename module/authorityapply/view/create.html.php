<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php js::import($jsRoot.'xm-select.js')?>
<?php js::set('svnAuthority',$svnAuthority)?>
<?php js::set('gitlabAuthority',$gitlabAuthority)?>
<?php js::set('jenkinsAuthority',$jenkinsAuthority)?>

<?php js::set('subSystemList',$lang->authorityapply->subSystemList)?>

<?php js::set('svnPermission',$svnPermission)?>
<?php js::set('gitLabPermission',$gitLabPermission)?>
<?php js::set('jenkinsPermission',$jenkinsPermission)?>

<div id="mainContent" class="main-content fade">

    <div class="center-block">
        <div class="flex-container" style="margin-top:8px;">
            <div class="notice">
                <?php echo $lang->authorityapply->notice; ?>
            </div>
            <div>
                <?php foreach ($lang->authorityapply->noticeList as $k => $v): ?>
                    <div class="notice-content">
                        <?php echo $v; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form" id="">
                <tbody>
                <tr>
                    <th><?php echo $lang->authorityapply->summary; ?></th>
                    <td colspan='5'><?php echo html::input('summary', '', "class='form-control' required maxlength='50'") ; ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->createdBy; ?></th>
                    <td colspan='2'><?php echo html::input('createdBy',   zget($userList, $this->app->user->account, ''), "class='form-control' readonly required"); ?></td>
                    <th><?php echo $lang->authorityapply->applyDepartment; ?></th>
                    <td colspan='2'> <?php echo html::select('applyDepartment', $deptList, $this->app->user->dept, "class='form-control chosen' required  onchange='applyDepartmentChange(this);'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->project; ?></th>
                    <td colspan='5'>
                        <?php $a = $lang->authorityapply->projectAlert[1];?>
                        <?php echo html::select('project[]', $projectList, '', "class='form-control chosen' multiple required data-placeholder='$a'"); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->application; ?></th>
                    <td colspan='5'> <?php echo html::select('application[]', $appList, '', 'class="form-control chosen" multiple required onchange="applicationChange(this);" '); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->product; ?></th>
                    <td colspan='5'> <?php echo html::select('product[]', $productList, '', 'class="form-control chosen" multiple '); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->content; ?></th>
                    <td colspan='5'>
                        <table class="table table-bordered required" style="border:none">
                            <tbody id="p-content">
                            <tr>
                                <th style="text-align: center;"><span
                                            style="color: #ff5d5d">* </span><?php echo $lang->authorityapply->subSystem; ?>
                                </th>
                                <th style="text-align: center;"><span
                                            style="color: #ff5d5d">* </span><?php echo $lang->authorityapply->content; ?>
                                </th>
                                <th style="text-align: center;"><span
                                            style="color: #ff5d5d">* </span><?php echo $lang->authorityapply->openPermissionPerson; ?>
                                </th>
                                <th style="text-align: center;"><?php echo $lang->authorityapply->operate;?></th>
                            </tr>
                            <template id="p-contentTpl">
                                <tr>
                                    <td class="w-130px">
                                        <?php echo html::select('subSystem[]', $lang->authorityapply->subSystemList, '', 'class="form-control chosen" data-id="0" onChange=subSystemChange(this)'); ?>
                                    </td>
                                    <td class="subContent">
                                        <textarea  name="permissionContent[0]" style="height: 32px"   class="textarea-inherit" placeholder="<?php echo $lang->authorityapply->permissionPlaceholder;?>" ></textarea>
                                        <div style="display: flex;align-items: center;justify-content: space-around" id="subContent">
                                            <div class="xm"></div>
                                            <div class="xm"></div>
                                            <div class="xm"></div>
                                            <div class="xm xm1"></div>
                                            <div class="xm xm1"></div>
                                            <div class="xm xm1"></div>
                                        </div>
                                    </td>
                                    <td class="w-300px"><?php echo html::select('', $userList, '', "class='form-control chosen ' multiple onchange='openPermissionPersonChange(this);' style='margin-left:10px;'"); ?></td>
                                    <td  class="w-80px">
                                        <div class="input-group">
                                            <a style="color:blue" href="javascript:void(0)" onclick="addRow()" class="btn btn-link"><i
                                                        class="icon-plus"></i></a>
                                            <a href="javascript:void(0)" onclick="delRow(this)" class="btn btn-link"><i
                                                        class="icon-close"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr>
                                <td class="w-130px">
                                    <?php echo html::select('subSystem[1]', $lang->authorityapply->subSystemList, '', 'class="form-control chosen" id="subSystem1" data-id="1" onChange=subSystemChange(this)'); ?>
                                </td>
                                <td class="subContent">
                                    <textarea name="permissionContent[1]" id="permissionContent1" style="height: 32px"  class="textarea-inherit" placeholder="<?php echo $lang->authorityapply->permissionPlaceholder;?>" ></textarea>
                                    <div style="display: flex;align-items: center;justify-content: space-around" id="subContent">
                                        <div class="xm" id="svnPermissionContent1"></div>
                                        <div class="xm" id="gitLabPermissionContent1"></div>
                                        <div class="xm" id="jenkinsPermissionContent1"></div>
                                        <div class="xm xm1" id="svnPermission1"></div>
                                        <div class="xm xm1" id="gitLabPermission1"></div>
                                        <div class="xm xm1" id="jenkinsPermission1"></div>

                                    </div>
                                </td>
                                <td class="w-300px"><?php echo html::select('openPermissionPerson[1][]', $userList, '', "class='form-control chosen ' multiple onchange='openPermissionPersonChange(this);' style='margin-left:10px;'"); ?></td>
                                <td  class="w-80px">
                                    <div class="input-group">
                                        <a style="color:blue" href="javascript:void(0)" onclick="addRow()" class="btn btn-link"><i
                                                    class="icon-plus"></i></a>
                                        <a href="javascript:void(0)" onclick="delRow(this)" class="btn btn-link"><i
                                                    class="icon-close"></i></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->reason; ?></th>
                    <td colspan='5'>
                        <?php echo html::textarea('reason', '', " class='form-control'   required"); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->approvalDepartment; ?></th>
                    <td colspan='5'>
                        <?php echo html::select('approvalDepartment[]', $deptList, $this->app->user->dept, 'class="form-control chosen" multiple required onchange="approvalDepartmentChange(this);"'); ?>
                        <div class="notice-content">
                            <?php echo $lang->authorityapply->depReviewTips;?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->authorityapply->reviewer; ?></th>
                    <td colspan='5' class="article-content">
                        <table class="table table-bordered ">
                            <tbody>
                            <tr id="manager1">
                                <th class="w-150px" ><?php echo $lang->authorityapply->departCEO; ?></th>
                                <td><?php  echo  zmget($userList, $manager1, '');?></td>
                            </tr>
                            <tr style="display: none" id="departChargeCEO">
                                <th><?php echo $lang->authorityapply->departChargeCEO; ?></th>
                                <td></td>
                            </tr>
                            <tr id="departChargeCM">
                                <th><?php echo $lang->authorityapply->CM; ?></th>
                                <td><?php  echo  zmget($userList, $cm, '');?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>

                </tr>
                <tr>
                    <th class="w-120px"></th>
                    <td class='form-actions text-center' colspan='5'>
                        <input type="hidden" name="issubmit" value="save">
                        <?php
                        echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn buttonInfo') .
                            html::commonButton($lang->submit, '', 'btn btn-wide btn-primary submitBtn buttonInfo') .
                            html::linkButton("返回",$returnUrl,'self','','btn btn-wide');
                        ?>
                    </td>
                </tr>

                </tbody>
            </table>
        <input type="hidden" id="thisDeptLeader" name="thisDeptLeader" value="<?php echo $manager1;?>"/>
        <input type="hidden" id="thatDeptLeader" name="thatDeptLeader"/>
        <input type="hidden" id="thisDeptChargeLeader" name="thisDeptChargeLeader"/>
        <input type="hidden" id="cm" name="cm" value="<?php echo $cm;?>"/>
        </form>

    </div>
</div>


<?php include '../../common/view/footer.html.php'; ?>

