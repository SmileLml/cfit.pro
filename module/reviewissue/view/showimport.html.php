<?php include '../../common/view/header.html.php'; ?>
<?php if (isset($suhosinInfo)): ?>
    <div class='alert alert-info'><?php echo $suhosinInfo ?></div>
<?php elseif (empty($maxImport) and $allCount > $this->config->file->maxImport): ?>
    <div id="mainContent" class="main-content fade">
        <div class="main-header">
            <h2><?php echo $lang->reviewissue->import; ?></h2>
        </div>
        <p><?php echo sprintf($lang->file->importSummary, $allCount, html::input('maxImport', $config->file->maxImport, "style='width:50px'"), ceil($allCount / $config->file->maxImport)); ?></p>
        <p><?php echo html::commonButton($lang->import, "id='import'", 'btn btn-primary'); ?></p>
    </div>
    <script>
        $(function () {
            $('#maxImport').keyup(function () {
                if (parseInt($('#maxImport').val())) $('#times').html(Math.ceil(parseInt($('#allCount').html()) / parseInt($('#maxImport').val())));
            });
            $('#import').click(function () {
                location.href = createLink('reviewissue', 'showImport', "pageID=1&maxImport=" + $('#maxImport').val())
            })
        });
    </script>
<?php else: ?>
    <div id="mainContent" class="main-content">
        <div class="main-header clearfix">
            <h2><?php echo $lang->reviewissue->import; ?></h2>
        </div>
        <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
            <table class='table table-form' id='showData'>
                <thead>
                <tr>
                    <th class='w-60px'><?php echo $lang->reviewissue->id ?></th>
                    <th class='w-120px required'><?php echo $lang->reviewissue->code ?></th>
                    <th class='w-200px required'><?php echo $lang->reviewissue->review ?></th>
                    <th class='w-300px required'><?php echo $lang->reviewissue->title ?></th>
                    <th class='w-300px required'><?php echo $lang->reviewissue->desc ?></th>
                    <th class='w-150px required'><?php echo $lang->reviewissue->type ?></th>
                    <th class='w-150px required'><?php echo $lang->reviewissue->raiseBy ?></th>
                    <th class='w-150px required'><?php echo $lang->reviewissue->raiseDate ?></th>
                    <th class='w-150px required'><?php echo $lang->reviewissue->status ?></th>
                    <th class='w-150px'><?php echo $lang->reviewissue->resolutionBy ?></th>
                    <th class='w-150px'><?php echo $lang->reviewissue->resolutionDate ?></th>
                    <th class='w-300px'><?php echo $lang->reviewissue->dealDesc ?></th>
                    <th class='w-150px'><?php echo $lang->reviewissue->validation ?></th>
                    <th class='w-150px'><?php echo $lang->reviewissue->verifyDate ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $insert = true;
                $i = 1;
                //        $lang->reviewissue->categoryList = $this->lang->opinion->categoryList;
                ?>
                <?php foreach ($reviewIssueData as $key => $reviewIssue): ?>
                    <tr class='text-top'>
                        <td>
                            <?php
                            $key = $key - 1;
                            echo $i++ . " <sub style='vertical-align:sub;color:gray'>{$lang->reviewissue->new}</sub>";
                            ?>
                        <td><?php echo html::input("code[$key]", $reviewIssue->code, "class='form-control' onchange='codeCheck(value,$key)'") ?></td>
                        <td><?php echo html::select("review[$key]", $reviewList[$key], $reviewIssue->review, "class='form-control chosen' onchange='reviewCheck(value,$key)' ") ?></td>
                        <td><?php echo html::input("title[$key]", $reviewIssue->title, "class='form-control' placeholder='{$lang->reviewissue->titleTemplate}'") ?></td>
                        <td><?php echo html::textarea("desc[$key]", $reviewIssue->desc, "class='form-control'") ?></td>
                        <td><?php echo html::select("type[$key]", $this->lang->reviewissue->typeList, $reviewIssue->type, "class='form-control chosen'") ?></td>
                        <td><?php echo html::select("raiseBy[$key]", $users, $reviewIssue->raiseBy, "class='form-control chosen'") ?></td>
                        <td><?php echo html::input("raiseDate[$key]", $reviewIssue->raiseDate, "class='form-control form-date'") ?></td>
                        <td><?php echo html::select("status[$key]", $lang->reviewissue->statusList, $reviewIssue->status, "class='form-control chosen'") ?></td>
                        <td><?php echo html::select("resolutionBy[$key]", $users, $reviewIssue->resolutionBy, "class='form-control chosen'") ?></td>
                        <td><?php echo html::input("resolutionDate[$key]", $reviewIssue->resolutionDate, "class='form-control form-date'") ?></td>
                        <td><?php echo html::textarea("dealDesc[$key]", $reviewIssue->dealDesc, "class='form-control'") ?></td>
                        <td><?php echo html::select("validation[$key]", $users, $reviewIssue->validation, "class='form-control chosen'") ?></td>
                        <td><?php echo html::input("verifyDate[$key]", $reviewIssue->verifyDate, "class='form-control form-date'") ?></td>
                    </tr>
                <?php endforeach; ?>
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
            <?php if (!$insert and $dataInsert === '') include '../../common/view/noticeimport.html.php'; ?>
        </form>
    </div>
<?php endif; ?>
<script>

    $(function () {
        $.fixedTableHead('#showData');
    });

    function codeCheck(code, $i) {
        var link = createLink('reviewissue', 'getAjaxReviewListByCode', 'code=' + code + "&classId=" + $i);
        $.post(link, function (data) {
            $('#review' + $i).replaceWith(data);
            $('#review' + $i + '_chosen').remove();
            $('#review' + $i).chosen();
            $('#review' + $i).change();
        })
    }

    function reviewCheck(review, $i) {
        var link = createLink('reviewissue', 'ajaxGetType', 'reviewID=' + review);
        $.post(link, function (data) {
            var result = $.parseJSON(data);
            $('#resolutionBy' + $i).val(result.issue);
            $('#resolutionBy' + $i).trigger('chosen:updated');

            $('#type' + $i).val(result.grade);
            $('#type' + $i).trigger('chosen:updated');
        })

    }
</script>
<?php include '../../common/view/footer.html.php'; ?>
