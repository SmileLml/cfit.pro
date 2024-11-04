<table class='hidden'>
    <tbody id="archiveTable">
    <tr id='archiveTable0'>
        <th><?php echo $lang->review->archiveSvnUrl;?></th>
        <td colspan='4'>
            <input type="text" name="svnUrl[]" id="svnUrl0" value="" class="form-control svnUrl" placeholder="<?php echo htmlspecialchars($lang->review->archiveSvnUrlTip)?>">
        </td>

        <td colspan='2'>
            <input type="text" name="svnVersion[]" id="svnVersion0" value="" class="form-control svnVersion" placeholder="<?php echo htmlspecialchars($lang->review->archiveSvnVersion);?>" autocomplete="off">
        </td>

        <td class="c-actions">
            <a href="javascript:void(0)" onclick="addArchiveItem(this)" data-id='0' id='codePlus0' class="btn btn-link addArchiveBtn"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delArchiveItem(this)" data-id='0' id='codeClose0' class="btn btn-link delArchiveBtn"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>

<script>

    var relevantIndex = 1;
    //添加
    function addArchiveItem(obj) {
        var relevantObj  = $('#archiveTable');
        var relevantHtml = relevantObj.clone();
        relevantIndex++;

        relevantHtml.find('#archiveTable0').attr({'class':'archiveTrList'});
        relevantHtml.find('#codePlus0').attr({'id':'codePlus' + relevantIndex, 'data-id': relevantIndex});
        relevantHtml.find('#codeClose0').attr({'id':'codeClose' + relevantIndex, 'data-id': relevantIndex});

        relevantHtml.find('#svnUrl0').attr({'id':'svnUrl' + relevantIndex});
        relevantHtml.find('#svnVersion0').attr({'id':'svnVersion' + relevantIndex});
        relevantHtml.find('#archiveTable0').attr({'id':'archiveTable' + relevantIndex});

        var objIndex = $(obj).attr('data-id');
        $('#archiveTable' + objIndex).after(relevantHtml.html());
        sortArchiveItem();

    }
    //刪除
    function delArchiveItem(obj) {
        var objIndex = $(obj).attr('data-id');
        $('#archiveTable' + objIndex).remove();
        sortArchiveItem();
    }

    //重新排序
    function sortArchiveItem() {
        var sortKey = 0;
        $('.archiveTrList').each(function () {
            sortKey++;
            $(this).find('.addArchiveBtn').attr({'id':'codePlus' + sortKey, 'data-id': sortKey});
            $(this).find('.delArchiveBtn').attr({'id':'codeClose' + sortKey, 'data-id': sortKey});

            $(this).find('.svnUrl').attr({'id':'svnUrl' + sortKey});
            $(this).find('.svnVersion').attr({'id':'svnVersion' + sortKey});
            $(this).attr({'id':'archiveTable' + sortKey});
        });
    }

</script>