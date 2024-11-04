
function changeDealResult() {
    var result = $('#dealResult').val();
    if(result == '1') {
        $('#suggest').hide();
    } else {
        $('#suggest').show();
        $('#suggest-td').addClass('required');
    }
}


