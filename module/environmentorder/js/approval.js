
function changeDealResult() {
    var result = $('#dealResult').val();
    if(result == '1') {
        $('#executors').show();
        $('#suggest').hide();
        $('#executor-td').addClass('required');
    } else {
        $('#executors').hide();
        $('#suggest').show();
        $('#suggest-td').addClass('required');
    }
}


