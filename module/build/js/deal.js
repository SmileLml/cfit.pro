function changeStaus(){
    var status = $('#status').val();
    $('#comment').parent().find('.kindeditor-ph').text('可以在编辑器直接贴图');
    if(status == 'verifysuccess'){
        $('#comment').parent().removeClass('required');
    }else if(status == 'verifyrejectbacksystem' || status == 'verifyrejectsubmit'){
        $('#comment').parent().addClass('required');
    }else if(status == 'waitdeptmanager'){ //待部门负责人审批
        $('#comment').parent().find('.kindeditor-ph').text('请填写特批制版原因');
    }
}
