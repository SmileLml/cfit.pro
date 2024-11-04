<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='changereview'>
    <table class="table table-form">
        <tbody>
        <?php if($SvnList):?>
        <?php foreach ($SvnList as $key =>$item) :
                $indexKey = $key+1;
        ?>
                <tr class="archiveList" id='archiveTable<?php echo $indexKey?>'>
                    <th><?php echo $lang->change->archiveSvnUrl;?></th>
                    <td colspan='4' class="required">
                        <input type="text" name="svnUrl[]" id="svnUrl<?php echo $indexKey?>" value="<?php echo $item->svnUrl?>" class="form-control svnUrl" placeholder="<?php echo htmlspecialchars($lang->change->archiveSvnUrlTip)?>">
                    </td>

                    <td colspan='2' class="required">
                        <input type="text" name="svnVersion[]" id="svnVersion<?php echo $indexKey?>" value="<?php echo $item->svnVersion?>" class="form-control svnVersion required" placeholder="<?php echo htmlspecialchars($lang->change->archiveSvnVersion)?>" autocomplete="off">
                    </td>
                    <td class="c-actions">
                        <a href="javascript:void(0)" onclick="addArchiveItem(this)" data-id='<?php echo $indexKey?>' id= "codePlus<?php echo $indexKey?>" class="btn btn-link addArchiveBtn"><i class="icon-plus"></i></a>
                        <?php if($indexKey > 1):?>
                            <a href="javascript:void(0)" onclick="delArchiveItem(this)" data-id='<?php echo $indexKey?>' id= "codeClose<?php echo $indexKey?>" class="btn btn-link delArchiveBtn"><i class="icon-close"></i></a>
                        <?php endif;?>
                    </td>
                </tr>
        <?php endforeach;?>
        <?php else :?>
        <tr class="archiveTrList" id='archiveTable1'>
            <th><?php echo $lang->change->archiveSvnUrl;?></th>
            <td colspan='4' class="required">
                <input type="text" name="svnUrl[]" id="svnUrl" value="" class="form-control svnUrl" placeholder="<?php echo htmlspecialchars($lang->change->archiveSvnUrlTip)?>">
            </td>

            <td colspan='2' class="required">
                <input type="text" name="svnVersion[]" id="svnVersion" value="" class="form-control svnVersion required" placeholder="<?php echo htmlspecialchars($lang->change->archiveSvnVersion)?>" autocomplete="off">
            </td>
            <td class="c-actions">
                <a href="javascript:void(0)" onclick="addArchiveItem(this)" data-id='1' class="btn btn-link addArchiveBtn"><i class="icon-plus"></i></a>
            </td>
        </tr>
        <?php endif;?>
        <!--<tr>
            <th><?php /*echo $lang->change->consumed;*/?></th>
            <td colspan='6'><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
        </tr>-->

        <tr>
            <th><?php echo $lang->change->comment;?></th>
            <td colspan='6'><?php echo html::textarea('comment', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->change->archiveCommentTip)."'");?></td>
        </tr>

        <tr>
            <td class='form-actions text-center' colspan='8'>
                <!--保存初始审核节点-->
                <input type="hidden" name = "version" value="<?php echo $change->version; ?>">
                <input type="hidden" name = "status" value="<?php echo $change->status; ?>">
                <?php echo html::submitButton() . html::backButton();?>
            </td>
        </tr>
        </tbody>
    </table>
</form>