<style>
    .btn-dealIssue{display: inline !important;}
</style>

<div class="detail">
    <div class="pull-right">
        <?php if($this->app->rawModule == 'review'):?>
            <?php if(common::hasPriv('reviewissue','issue'))common::printLink('reviewissue', 'issue', "projectID=$review->project&reviewID=$review->id", "<i class='icon icon-checked'></i>" . $lang->review->dealIssue, '', "class='btn btn-primary btn-dealIssue'");?>
        <?php else:?>
            <?php if(isset($flag) && $flag):?>
                <?php if(common::hasPriv('reviewproblem','issue'))common::printLink('reviewproblem', 'issue', "projectID=$review->project&reviewID=$review->id&browseType=all", "<i class='icon icon-checked'></i>" . $lang->review->dealIssue, '', "class='btn btn-primary btn-dealIssue'");?>
            <?php endif;?>
        <?php endif;?>
    </div>

    <div class="detail-title"><?php echo sprintf($lang->review->reviewIssueTotal, count($issueList)) ;?></div>

    <div class='detail-content article-content panel-body scrollbar-hover' style="max-height: 300px;">
        <table class='table table-detail table-bordered table-condensed table-striped table-fixed'>
            <thead>
            <tr>
                <th class='w-500px'><?php echo $lang->reviewissue->title;?></th>
                <th class='w-900px'><?php  echo $lang->reviewissue->desc ;?></th>
                <th class='w-120px'><?php echo $lang->reviewissue->type;?></th>
                <th class='w-80px'><?php echo  $lang->reviewissue->raiseBy ;?></th>
                <th class='w-100px'><?php echo  $lang->reviewissue->raiseDate?></th>
                <th class='w-80px'><?php echo  $lang->reviewissue->status;?></th>
                <th class='w-80px'><?php echo  $lang->reviewissue->resolutionBy;?></th>
                <th class='w-100px'><?php echo  $lang->reviewissue->resolutionDate ;?></th>
                <th class='w-80px'><?php echo  $lang->reviewissue->validation ;?></th>
                <th class='w-100px'><?php echo  $lang->reviewissue->verifyDate ;?></th>
                <th class='w-300px'><?php echo  $lang->reviewissue->dealDesc  ;?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($issueList)) :?>
                <?php foreach($issueList as $issue):?>
                    <tr>
                        <td class="text-left"><?php echo html_entity_decode($issue->title);?></td>
                        <td class="text-left text-ellipsis"><?php echo $issue->desc ;?></td>
                        <td><?php echo zget($typeList,$issue->type,'');?></td>
                        <td><?php echo zget($users,$issue->raiseBy,'');?></td>
                        <td><?php echo $issue->raiseDate;?></td>
                        <td><?php echo zget($statusList,$issue->status,'');?></td>
                        <td><?php echo zget($users,$issue->resolutionBy,'');?></td>
                        <td><?php echo $issue->resolutionDate;?></td>
                        <td><?php echo zget($users,$issue->validation,'');?></td>
                        <td><?php echo $issue->verifyDate;?></td>
                        <td class="text-left text-ellipsis"><?php echo html_entity_decode($issue->dealDesc);?></td>
                    </tr>
                <?php endforeach;?>
            <?php else: ?>
                <tr>
                    <td colspan="11"> <?php echo   "<div class='text-center text-muted'>" . $lang->noData . '</div>';?></td>
                </tr>
            <?php endif;?>
            </tbody>
        </table>
    </div>
</div>