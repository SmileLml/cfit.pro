<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $source == 'issue'?$lang->reviewproblem->resolvedIssue:$lang->reviewproblem->resolved;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->reviewproblem->status;?></th>
                    <td><?php echo html::select('status', $lang->reviewproblem->statusList, $issue->status, "class='form-control chosen' onchange='selectStatus(this.value)'");?></td>
                    <td>
                        <!--已采纳、部分采纳时，出现验证人员，且默认回显问题的提出人，验证人员回填待处理人-->
                        <?php if(in_array($issue->status,$lang->reviewproblem->activeStatusArr)): ?>
                        <div class='input-group required dev'>
                            <span class='input-group-addon'><?php echo $lang->reviewproblem->waitValidation;?></span>
                            <?php
                            echo html::select('validation', $users, $raiseBy, 'class="form-control chosen"');?>
                        </div>
                        <?php else:?>
                            <div class='input-group required hidden dev'>
                                <span class='input-group-addon'><?php echo $lang->reviewproblem->waitValidation;?></span>
                                <?php
                                echo html::select('validation', $users, $raiseBy, 'class="form-control chosen"');?>
                            </div>
                        <?php endif;?>
                        <!--验证未通过，动态出现解决人员-->
                        <?php if(in_array($issue->status,$lang->reviewproblem->failedStatusArr)): ?>
                            <div class='input-group required dev2'>
                                <span class='input-group-addon'><?php echo $lang->reviewproblem->waitResolutionBy;?></span>
                                <?php
                                echo html::select('resolutionBy', $users, $issue->reviewCreatedBy, 'class="form-control chosen"');?>
                            </div>
                        <?php else:?>
                            <div class='input-group required hidden dev2'>
                                <span class='input-group-addon'><?php echo $lang->reviewproblem->waitResolutionBy;?></span>
                                <?php
                                echo html::select('resolutionBy', $users, $issue->reviewCreatedBy, 'class="form-control chosen"');?>
                            </div>
                        <?php endif;?>

                    </td>
                </tr>
                <tr>
                    <th class="w-120px"><?php echo $lang->reviewproblem->desc;?></th>
                    <td colspan='2'><?php echo html::textarea('desc',  $issue->desc, 'class=" desc1 form-control " ');?></td>
                </tr>
                <tr>
                    <th class="w-120px"><?php echo $lang->reviewproblem->dealDesc;?></th>
                    <td colspan='2'><?php echo html::textarea('dealDesc',  $issue->dealDesc, 'class="form-control"');?></td>
                </tr>
                <tr>
                    <th></th>
                    <td style="color:rgb(192,192,192)"><?php echo $lang->reviewproblem->dealDescTemplate?> </td>
                </tr>
                <tr>
                    <th class="w-120px"><?php echo $lang->reviewproblem->changelog;?></th>
                    <td colspan='2'><?php echo html::textarea('changelog',  $issue->changelog, 'class="form-control"');?></td>
                </tr>
                <tr>
                    <th class="w-120px"></th>
                    <td colspan="2" class="form-actions text-center">
                        <?php echo html::submitButton($lang->save);?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'?>

<script>
    function selectStatus(status)
    {
        console.log(status);
        //已采纳、部分采纳 验证人员显示且必填
        if(status === 'active' || status === 'part')
        {
            $('.dev').removeClass('hidden');
        }else{
            $('.dev').addClass('hidden');
        }
        //验证未通过，解决人员显示且必填
        if(status === 'failed')
        {
            $('.dev2').removeClass('hidden');
        }else{
            $('.dev2').addClass('hidden');
        }

    }

</script>