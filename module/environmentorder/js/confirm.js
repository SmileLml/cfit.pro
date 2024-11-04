
function changeDealResult() {
    var result = $('#dealResult').val();
    // 受理
    if(result == '1') {
        $('#suggest').show();
        $('#assign').show();
        $('#assign-td').addClass('required');
        $('#suggest-td').removeClass('required');

    } else {
        $('#suggest').show();
        $('#suggest-td').addClass('required');
        $('#assign').hide();
    }
}
function changeAssignResult(){
    var result = $('#assignResult').val();
    if(result == '1') {
        $('#executors').show();
        $('#suggest').hide();
        $('#executors-td').addClass('required');
    } else {
        $('#executors').hide();
    }
}


