function changeDealResult() {
    var result = $('#dealResult').val();
    if(result == '1')
    {
        $('#suggestTd').removeClass('required');
        $('.sftpPathTrClass').removeClass('hidden');
    }
    else
    {
        $('#suggestTd').addClass('required');
        $('.sftpPathTrClass').addClass('hidden');
    }
}