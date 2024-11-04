<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $mailTitle;?></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->custommail->tips;?></legend>
            <div style='padding:5px;'>
                <?php echo $mailConf->mailContent;?>
            </div>
        </fieldset>
    </td>
</tr>

<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <?php
            $content = json_decode($authorityapply->content,true);
            $subSystem='';
            if($content){
//                全部子系统
                $subSystem = array_column($content,'subSystem');
                $subSystem = $subSystem?implode(',',$subSystem):'';
//                开通权限的全部人员
                $openPermissionPerson = array_column($content,'openPermissionPerson');
                $result=[];
                array_walk_recursive($openPermissionPerson,function ($value)use(&$result){
                    array_push($result,$value);
                });
                $openPermissionPerson = $result?implode(',',$result):'';
            }

            ?>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;min-width: 80px;'>
                    <?php echo $this->lang->authorityapply->code;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $authorityapply->code;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;min-width: 80px;'>
                    <?php echo $this->lang->authorityapply->summary;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $authorityapply->summary;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->authorityapply->openPermissionPerson;?></th>
                <td style='padding: 5px
                ; border: 1px solid #e5e5e5;'><?php echo zmget($users,$openPermissionPerson)?></td>
            </tr>
            <tr>

                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->authorityapply->involveSubSystem;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'> <?php echo zmget($this->lang->authorityapply->subSystemList, $subSystem); ?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->authorityapply->reason;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo  strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($authorityapply->reason))); ?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->authorityapply->dealUser;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zmget($users,$authorityapply->dealUser);?></td>
            </tr>

        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>

