/**
 * 修改审批结果
 */
function changeDealResult() {
    var result = $('#dealResult').val();
    if(result == '1')
    {
        $('#suggestTd').removeClass('required');
        $('.sftpPathTrClass').removeClass('hidden');
    } else {
        $('#suggestTd').addClass('required');
        $('.sftpPathTrClass').addClass('hidden');
    }
}

/**
 * 修改变更结果
 */
function changeModifyStatus() {
    var status = $('#status').val();

    if(status == 'cancel')
    {
        $('#onlineTimeTd').removeClass('required');
        $('#onlineTimeInfo').addClass('hidden');
    } else {
        $('#onlineTimeTd').addClass('required');
        $('#onlineTimeInfo').removeClass('hidden');
    }
}