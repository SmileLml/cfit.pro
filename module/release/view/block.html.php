<table class='table table-data'> <tbody>
    <?php foreach ($releaseInfoList as $releaseInfo){
        $logNum = 0;
        ?>
        <tr>
            <th class='w-100px'><?php echo $lang->release->name; ?></th>
            <td colspan="3"><?php echo html::a($this->createLink('projectrelease', 'view', array('releaseID' => $releaseInfo->id)), $releaseInfo->name, '', 'data-app="project"'); ?></td>
        </tr>
        <?php if(isset($outwarddelivery) ){ ?>
            <tr>
                <th class='w-100px'><?php echo $lang->outwarddelivery->isMediaChanged; ?></th>
                <td colspan="3"><?php echo zget($lang->outwarddelivery->isMediaChangedList, $outwarddelivery->ifMediumChanges, ''); ?></td>
            </tr>
        <?php }?>
        <tr>
            <th class='w-100px'><?php echo $lang->release->path; ?></th>
            <td colspan="3"><?php if ($releaseInfo->path) echo $releaseInfo->path . $lang->api->sftpList['info']; ?></td>
        </tr>
        <?php if($releasePushLogs[$releaseInfo->id]){
            foreach ($releasePushLogs[$releaseInfo->id] as $pushlog) {  $logNum ++; ?>
                <tr>
                    <th class='w-100px'><?php echo $lang->release->pushTime; ?></th>
                    <td><?php echo  $pushlog->pushTime; ?></td>
                    <td class='w-100px'  style="color: #838a9d;"><?php echo $lang->release->pushStatus; ?></td>
                    <td><span style="text-align: left !important;"><?php echo $pushStatus = in_array($pushlog->pushStatus,[0,1]) ? '未推送' : ($pushlog->pushStatus == 3 ? "成功" : "失败"); ?></span></td>

                </tr>
                <?php if($pushStatus == '失败'){?>
                    <tr>
                        <th class='w-100px'><?php echo $lang->release->failReason; ?></th>
                        <td><?php echo $lang->release->pushStatusList[$pushlog->pushStatus]; ?></td>
                        <td class='w-100px' style="color: #838a9d;"><?php echo $lang->release->pushTimes; ?></th>
                        <td><span style="text-align: left !important;">第<?php echo $logNum; ?>次</span></td>
                    </tr>
                    <?php
                }
            }
        }

        if(in_array($releaseInfo->pushStatusQz,[0,1,2])) { //没有发送记录
            ?>
            <th class='w-100px'><?php echo $lang->release->pushTime; ?></th>
            <td><?php echo  $releaseInfo->pushTimeQz; ?></td>
            <td class='w-100px'  style="color: #838a9d;"><?php echo $lang->release->pushStatus; ?></td>
            <td><span style="text-align: left !important;"><?php echo $pushStatus = in_array($releaseInfo->pushStatusQz,[0,1]) ? '未推送' : "推送中"; ?></span></td>
            <?php
        }
        ?>
        <tr>
            <th class='w-100px'><?php echo $lang->file->common; ?></th>
            <td colspan="3">
                <div class='detail'>
                    <div class='detail-content article-content'>
                        <?php echo $this->fetch('file', 'printFiles', array('files' => $releaseInfo->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false)); ?>
                    </div>
                </div>
            </td>
        </tr>

    <?php }?> </tbody>
</table>
