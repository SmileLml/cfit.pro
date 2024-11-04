<div class="cell">
    <div class='detail'>
        <div class='detail-title'><?php echo $lang->change->baseLineInfo;?></div>
        <div class="detail-content article-content ">
            <table class="table ops  table-fixed ">
                <thead>
                    <tr>
                        <th class='w-120px'><?php echo $lang->change->baseLineType;?></th>
                        <th><?php echo $lang->change->baseLinePath;?></th>
                        <th class='w-160px'><?php echo $lang->change->createdDate;?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($baseLineList)):?>
                        <tr>
                            <td colspan="3" style="text-align: center;"><?php echo $lang->noData;?></td>
                        </tr>
                    <?php else:?>
                        <?php foreach ($baseLineList as $val):?>
                            <tr>
                                <td><?php echo zget($baseLineTypelist, $val->baseLineType); ?></td>
                                <td><?php echo $val->baseLinePath;?></td>
                                <td><?php echo $val->baseLineTime;?></td>
                            </tr>
                        <?php endforeach;?>
                    <?php endif;?>

                </tbody>
            </table>
        </div>

    </div>
</div>