<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->defect->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
        <table class='table table-form'>

            <div style="font-size: 16px;"><span><strong>基础信息</strong></span></div>
            <tr>
                    <th class='w-140px'><?php echo $lang->defect->app;?></th>
                    <td colspan="4" class="required"><?php echo html::select('app', $apps, $defect->app, "class='form-control chosen'");?></td>
                </tr>
            <tr>
                <th><?php echo $lang->defect->product;?></th>
                <td colspan="2" class="required">
                    <?php echo html::select('product', $productList, $defect->product, "class='form-control chosen'"); ?>
                </td>
                <td colspan="2" class="required">
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->defect->project; ?></span>
                        <?php echo html::select('project', $projects, $defect->project, "class='form-control chosen'"); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th ><?php echo $lang->defect->defectTitle;?></th>
                <td colspan='4' class="required"><?php echo html::input('title', $defect->title, "class='form-control'");?></td>
            </tr>

            <tr>
                <th><?php echo $lang->defect->reportUser;?></th>
                <td colspan="2" class="closeError">
                    <?php echo html::select('reportUser', $users, $defect->reportUser, "class='form-control chosen'"); ?>
                </td>
                <td colspan="2" class="closeError">
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->defect->reportDate; ?></span>
                        <?php echo html::input('reportDate', $defect->reportDate, "class='form-control' readonly");?>
                    </div>
                </td>
            </tr>

            <tr>
                <th><?php echo $lang->defect->pri;?></th>
                <td colspan="2" class="required"><?php echo html::select('pri',$lang->bug->defectPriList, $defect->pri, "class='form-control chosen'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->defect->steps; ?></th>
                <td colspan="4" class="required"><?php echo html::textarea('issues', $defect->issues, "rows='3' class='form-control'"); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang->defect->type;?></th>
                <td colspan="2" class="required">
                    <?php echo html::select('type', $lang->bug->typeList, $defect->type, "class='form-control chosen'"); ?>
                </td>
                <td colspan="2" class="required">
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->bug->defectChildType; ?></span>
                        <?php echo html::select('childType', $childTypeList, $defect->childType,"class='form-control chosen'");?>
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->bug->frequency; ?></th>
                <td colspan='4' class="required"><?php echo html::select('frequency', $lang->bug->defectFrequencyList, $defect->frequency,"class='form-control chosen'"); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang->defect->developer;?></th>
                <td colspan="2" class="required">
                    <?php echo html::select('developer', $users,$defect->developer, "class='form-control chosen'");?>
                </td>
                <td colspan="2" class="required">
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->defect->severity; ?></span>
                        <?php echo html::select('severity', $lang->bug->defectSeverityList,$defect->severity, "class='form-control chosen'");?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px;padding: 15px 0"><span><strong>解决方案字段</strong></span></td>
            </tr>

            <tr>
                <th><?php echo $lang->defect->resolution;?></th>
                <td colspan="2" class="required">
                    <?php echo html::select('resolution', $lang->bug->resolutionList,$defect->resolution, "class='form-control chosen'");?>
                </td>
                <td colspan="2">
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->defect->resolvedBuild; ?></span>
                        <?php echo html::select('resolvedBuild', $resolvedBuilds,$defect->resolvedBuild, "class='form-control chosen'");?>
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->defect->resolvedDate;?></th>
                <td colspan="2">
                    <?php echo html::input('resolvedDate', $defect->resolvedDate, "class='form-control form-datetime'");?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->defect->dealUser; ?></th>
                <td colspan="2">
                    <?php echo html::select('dealUser', $users,$defect->dealUser, "class='form-control chosen'");?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->defect->cc; ?></th>
                <td colspan="2">
                    <?php echo html::select('cc', $users,$defect->cc, "class='form-control chosen'");?>
                </td>
            </tr>
            <tr>
                <th class="w-140px"></th>
                <td colspan='4' class='text-center form-actions' ><?php echo html::submitButton() . html::linkButton($lang->goback, $this->server->http_referer, 'self', '', 'btn btn-wide'); ?></td>
            </tr>
        </table>
    </form>
  </div>
</div>
<script>
    $('#type').change(function()
    {
        var type = $(this).val();
        $.get(createLink('bug', 'ajaxGetChildTypeList', 'type=' + type), function(data)
        {
            $('#childType_chosen').remove();
            $('#childType').replaceWith(data);
            $('#childType').chosen();
        });
    });
</script>
<?php include '../../common/view/footer.html.php';?>
