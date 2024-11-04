
function changeDealResult() {
    var result = $('#dealResult').val();
    if(result == '1') {
        $('#suggest').show();
        $('#workHours').show();
        $('#workHour-td').addClass('required');
        $('#suggest-td').removeClass('required');

    } else {
        $('#workHours').hide();
        $('#suggest').show();
        $('#suggest-td').addClass('required');
    }
}
function validateNumber(e){
    const regx = /^\d+(\.\d+)?$/;
    if(!e.value){
        return;
    }
    if(!regx.test(e.value)){
        e.value = ''
    }
}

