<?php include '../../common/view/header.html.php';?>
<?php if(isset($suhosinInfo)):?>
    <div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
    <div id="mainContent" class="main-content fade">
        <div class="main-header">
            <h2><?php echo $lang->demand->import;?></h2>
        </div>
        <p><?php echo sprintf($lang->file->importSummary, $allCount, html::input('maxImport', $config->file->maxImport, "style='width:50px'"), ceil($allCount / $config->file->maxImport));?></p>
        <p><?php echo html::commonButton($lang->import, "id='import'", 'btn btn-primary');?></p>
    </div>
    <script>
        $(function()
        {
            $('#maxImport').keyup(function()
            {
                if(parseInt($('#maxImport').val())) $('#times').html(Math.ceil(parseInt($('#allCount').html()) / parseInt($('#maxImport').val())));
            });
            $('#import').click(function(){location.href = createLink('demand', 'showImport', "pageID=1&maxImport=" + $('#maxImport').val())})
        });
    </script>
<?php else:?>
    <div id="mainContent" class="main-content">
        <div class="main-header clearfix">
            <h2><?php echo $lang->demand->import;?></h2>
        </div>
        <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
            <table class='table table-form' id='showData'>
                <thead>
                <tr>
                    <th class='w-60px'><?php echo $lang->demand->id?></th>
                    <th class='w-200px required'><?php echo $lang->demand->title?></th>
                    <th class='w-200px required'><?php echo $lang->demand->requirementID?></th>
                    <th class='w-200px required'><?php echo $lang->demand->opinionID?></th>
                    <th class='w-200px required'><?php echo $lang->demand->desc?></th>
                    <th class='w-200px required'><?php echo $lang->demand->endDate?></th>
                    <!-- <th class='w-200px required'><?php echo $lang->demand->acceptDept?></th> -->
                    <th class='w-200px required'><?php echo $lang->demand->acceptUser?></th>
                    <th class='w-200px required'><?php echo $lang->demand->createdBy?></th>
                    <th class='w-200px required'><?php echo $lang->demand->dealUser?></th>
                    <th class='w-200px required'><?php echo $lang->demand->app?></th>
                    <th class='w-200px required'><?php echo $lang->demand->fixType?></th>
                    <th class='w-200px required'><?php echo $lang->demand->product?></th>
                    <th class='w-200px'><?php echo $lang->demand->productPlan?></th>
                    <th class='w-200px required'><?php echo $lang->demand->status?></th>
                    <th class='w-200px'><?php echo $lang->demand->actualOnlineDate?></th>
                    <th class='w-200px'><?php echo $lang->demand->comment?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $insert = true;
                $i  = 1;
                ?>
                <?php foreach($demandData as $key => $demand):?>
                    <tr class='text-top'>
                        <td>
                            <?php
                            $key = $key-1;
                            echo $i++ . " <sub style='vertical-align:sub;color:gray'>{$lang->demand->new}</sub>";
                            ?>
                        <td><?php echo html::input("title[$key]", $demand->title, "class='form-control'")?></td>
                        <td><?php echo html::select("requirementID[$key]",$requirementList, $demand->requirementID, "class='form-control chosen'")?></td>
                        <td><?php echo html::select("opinionID[$key]",$opinionList, $demand->opinionID, "class='form-control chosen'")?></td>
                        <td><?php echo html::textarea("desc[$key]", $demand->desc, "class='form-control'")?></td>
                        <td><?php echo html::input("endDate[$key]", $demand->endDate, "class='form-control form-date'")?></td>
                        <!-- <td><?php echo html::select("acceptDept[$key]", $dept,$demand->acceptDept, "class='form-control chosen'")?></td> -->
                        <td><?php echo html::select("acceptUser[$key]", $users, $demand->acceptUser, "class='form-control chosen'")?></td>
                        <td><?php echo html::select("createdBy[$key]", $users, $demand->createdBy, "class='form-control chosen'")?></td>
                        <td><?php echo html::select("dealUser[$key]", $users, $demand->dealUser, "class='form-control chosen'")?></td>
                        <td><?php echo html::select("app[$key]",$apps,$demand->app, "class='form-control chosen'")?></td>
                        <td><?php echo html::select("fixType[$key]", $this->lang->demand->fixTypeList,$demand->fixType, "class='form-control chosen'")?></td>
                        <td><?php echo html::select("product[$key]",$productList, $demand->product, "class='form-control chosen'")?></td>
                        <td><?php echo html::input("productPlan[$key]", $demand->productPlan, "class='form-control '")?></td>
                        <td><?php echo html::select("status[$key]", $this->lang->demand->statusList,$demand->status, "class='form-control chosen'")?></td>
                        <td><?php echo html::input("actualOnlineDate[$key]", $demand->actualOnlineDate, "class='form-control form-date'")?></td>
                        <td><?php echo html::textarea("comment[$key]", $demand->comment, "class='form-control'")?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan='16' class='text-center form-actions'>
                        <?php
                        echo html::submitButton($this->lang->save);
                        echo html::hidden('isEndPage', $isEndPage ? 1 : 0);
                        echo html::hidden('pagerID', $pagerID);
                        echo ' &nbsp; ' . html::backButton();
                        echo ' &nbsp; ' . sprintf($lang->file->importPager, $allCount, $pagerID, $allPager);
                        ?>
                    </td>
                </tr>
                </tfoot>
            </table>
            <?php if(!$insert and $dataInsert === '') include '../../common/view/noticeimport.html.php';?>
        </form>
    </div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
