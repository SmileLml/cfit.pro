<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-content'>
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $bug->id; ?></span>
                <?php echo isonlybody() ? ('<span title="' . $bug->title . '">' . $bug->title . '</span>') : html::a($this->createLink('bug', 'view', 'bug=' . $bug->id), $bug->title); ?>

                <?php if (!isonlybody()): ?>
                    <small><?php echo $lang->arrow . $lang->bug->defect; ?></small>
                <?php endif; ?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class='table table-form'>
                <tr>
                    <th></th>
                    <td colspan="4" style="color:rgb(136,136,136);font-size: 13px;"><?php echo $lang->bug->prompt?> </td>
                </tr>
                <tr>
                    <th><?php echo $lang->bug->system;?></th>
                    <td colspan="2" class="required">
                        <?php echo html::select('app', $apps, $bug->applicationID ?? '', "class='form-control chosen'");?>
                    </td>
                    <td colspan="2" class="required">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->bug->product;?></span>
                            <?php echo html::select('product', $productList, $bug->product, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-140px'><?php echo $lang->bug->project;?></th>
                    <td colspan="4" class="required"><?php echo html::select('project', $projects, $bug->project, "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <th ><?php echo $lang->bug->defectTitle;?></th>
                    <td colspan='4' class="required"><?php echo html::input('title', $bug->title, "class='form-control'");?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->bug->reportUser;?></th>
                    <td colspan="2" class="closeError">
                        <?php echo html::select('reportUser', $users, $app->user->account, "class='form-control chosen'"); ?>
                    </td>
                    <td colspan="2" class="closeError">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->bug->reportDate; ?></span>
                            <?php echo html::input('reportDate', date('Y-m-d'), "class='form-control' readonly");?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->bug->pri;?></th>
                    <td colspan="2" class="required">
                        <?php echo html::select('pri',$lang->bug->defectPriList, $bug->pri, "class='form-control chosen'");?>
                    </td>
                    <td colspan="2" class="required">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->bug->frequency; ?></span>
                            <?php echo html::select('frequency', $lang->bug->defectFrequencyList, '',"class='form-control chosen'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->bug->steps; ?></th>
                    <td colspan='4' class="required"><?php echo html::textarea('issues', htmlspecialchars($bug->steps), "rows='6' class='w-p94'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->bug->defectType;?></th>
                    <td colspan="2" class="required"  id='typeBox'>
                        <?php echo html::select('defectType', $lang->bug->typeList, $bug->type, "class='form-control chosen'"); ?>
                    </td>
                    <td colspan="2" class="closeError" id='childTypeBox'>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->bug->typeChild; ?></span>
<!--                            --><?php //echo html::select('childType', $childTypeList, $bug->childType,"class='form-control chosen'"); ?>
                            <?php echo html::select('childType', empty($parentChildTypeList[$bug->type]) ? array('' => '') : $parentChildTypeList[$bug->type], $bug->childType, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->bug->developer;?></th>
                    <td colspan="2" class="required">
                        <?php echo html::select('developer', $users,"", "class='form-control chosen'");?>
                    </td>
                    <td colspan="2">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->bug->dept; ?></span>
                            <?php echo html::input('dept', '', "class='form-control' readonly");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->bug->resolution;?></th>
                    <td colspan="2" class="required">
                        <?php echo html::select('resolution', $lang->bug->resolutionList,$bug->resolution, "class='form-control chosen'");?>
                    </td>
                    <td colspan="2" class="required">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->bug->severityType; ?></span>
                            <?php echo html::select('severity', $lang->bug->defectSeverityList,$bug->severity, "class='form-control chosen'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->bug->resolvedBuild;?></th>
                    <td id='newBuildExecutionBox' class='hidden'>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->build->execution;?></span>
                            <?php echo html::select('buildExecution', $executions, '', "class='form-control chosen'");?>
                        </div>
                    </td>
                    <td colspan="2">
                        <div id='resolvedBuildBox'><?php echo html::select('resolvedBuild', $resolvedBuilds, $bug->resolvedBuild, "class='form-control chosen'");?></div>
                        <div id='newBuildBox' class='hidden'><?php echo html::input('buildName', '', "class='form-control' placeholder='{$lang->bug->placeholder->newBuildName}'");?></div>
                    </td>
                    <td>
                        <?php if(common::hasPriv('build', 'create')):?>
                            <div class='checkbox-primary'>
                                <input type='checkbox' id='createBuild' name='createBuild' value='1' />
                                <label for='createBuild'><?php echo $lang->bug->createBuild;?></label>
                            </div>
                        <?php endif;?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->bug->resolvedDate;?></th>
                    <td colspan="2">
                        <?php echo html::input('resolvedDate', $bug->resolvedDate, "class='form-control form-datetime'");?>
                    </td>
                    <td colspan="2" class="required">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->bug->assignedTo; ?></span>
                            <?php echo html::select('dealUser', $users,$app->user->account, "class='form-control chosen'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-140px'><?php echo $lang->bug->cc;?></th>
                    <td colspan="4"><?php echo html::select('cc[]',$users,  '', "class='form-control chosen' multiple");?></td>
                </tr>
                <tr>
                    <td colspan='5' class='text-center form-actions' ><?php echo html::submitButton() . html::linkButton($lang->goback, $this->server->http_referer, 'self', '', 'btn btn-wide'); ?></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script>
    // $(document).ready(function () {
    //     $('#dealSuggest').change();
    // });
    $('#dealSuggest').change(function () {
        var dealSuggest = $(this).val();
        if(dealSuggest == 3){  //修复建议为待纳入后续计划时，计划变更日期和计划提交变更日期为必填
            $("#changeDateClass").addClass('required');
            $("#submitChangeDateClass").addClass('required');
        }else{
            $("#changeDateClass").removeClass('required');
            $("#submitChangeDateClass").removeClass('required');
        }

    });
    $('#defectType').change(function()
    {
        var type = $(this).val();
        $.get(createLink('bug', 'ajaxGetChildTypeList', 'type=' + type), function(data)
        {
            $('#childType_chosen').remove();
            $('#childType').replaceWith(data);
            $('#childType').chosen();
        });
    });

    $('#developer').change(function()
    {
        var account = $(this).val();
        $.get(createLink('bug', 'ajaxGetDeptById', 'type=' + account), function(data)
        {
            $('#dept_chosen').remove();
            $('#dept').replaceWith(data);
            $('#dept').chosen();
        });

        $.get(createLink('bug', 'ajaxGetDeptUserById', 'type=' + account), function(data)
        {
            $('#cc').val(data.split(",")).trigger("chosen:updated");
        });
    });

    $('#submit').click(function () {
        var cfm = confirm('是否将当前实验室缺陷转为遗留缺陷，确定后该数据将无法进行删除，请谨慎操作');
        if(cfm == true) {
            $('#dataform').submit()
        }else{
            return false;
        }
    })

    $(function()
    {
        /* Fix bug #3227. */
        var requiredFields = config.requiredFields;
        if(requiredFields.indexOf('resolvedBuild') == -1)
        {
            resolvedBuildTd  = $('#resolvedBuild').closest('td');
            $('#resolution').change(function()
            {
                if($(this).val() == 'fixed')
                {
                    resolvedBuildTd.addClass('required');
                }
                else
                {
                    resolvedBuildTd.removeClass('required');
                }
            });
        }

        $('#createBuild').change(function()
        {
            if($(this).prop('checked'))
            {
                $('#resolvedBuildBox').addClass('hidden');
                $('#newBuildBox').removeClass('hidden');
                $('#newBuildExecutionBox').removeClass('hidden');
            }
            else
            {
                $('#resolvedBuildBox').removeClass('hidden');
                $('#newBuildBox').addClass('hidden');
                $('#newBuildExecutionBox').addClass('hidden');
            }
        })


    })
</script>
