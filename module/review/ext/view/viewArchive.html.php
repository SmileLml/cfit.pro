<div class="cell">
    <div class='detail'>
        <div class='detail-title'><?php echo $lang->review->archiveInfo;?></div>
        <div class="detail-content article-content ">
            <table class="table ops  table-fixed ">
                <thead>
                    <tr>
                        <th><?php echo $lang->review->archiveSvnUrl;?></th>
                        <th class='w-120px'><?php echo $lang->review->archiveSvnVersion;?></th>
                        <th class='w-160px'><?php echo $lang->review->createdDate;?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($archiveList)):?>
                        <tr>
                            <td colspan="3" style="text-align: center;"><?php echo $lang->noData;?></td>
                        </tr>
                    <?php else:?>
                        <?php foreach ($archiveList as $val):?>
                            <tr>
                                <td><?php echo $val->svnUrl;?></td>
                                <td><?php echo $val->svnVersion;?></td>
                                <td><?php echo $val->createdTime;?></td>
                            </tr>
                        <?php endforeach;?>
                    <?php endif;?>

                </tbody>
            </table>
        </div>

    </div>
</div>