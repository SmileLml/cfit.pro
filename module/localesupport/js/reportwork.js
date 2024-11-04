setDefaultDateTime();
sortLine();
/**
 * 增加报工
 *
 * @param obj
 */
function addLine(obj){
    var supportUser = $(obj).parent().parent().find('input[name="supportUser[]"]').val();
    var supportUserName = supportUsersList[supportUser];
    if((!isAllReportWork) && (supportUser != currentUser)){
        alert('没有权限操作该用户的报工');
        return false;
    }
    $('#lineDemo').find('input[name="supportUser[]"]').val(supportUser);
    $('#lineDemo').find('#supportUserInfo').text(supportUserName);
    $(obj).parent().parent().after($('#lineDemo').children(':first-child').clone());
    setDefaultDateTime();
    sortLine();
}

/**
 * 删除报工
 *
 * @param obj
 */
function deleteLine(obj)
{
    if($(obj).parent().parent().parent().children().length>1){
        var supportUser = $(obj).parent().parent().find('input[name="supportUser[]"]').val();
        if((!isAllReportWork) && (supportUser != currentUser)){
            alert('没有权限操作该用户的报工');
            return false;
        }
        $(obj).parent().parent().remove();
        sortLine();
    }
}

/**
 * 排序
 */
function sortLine() {
    $('#supportUserTBody').children('tr').each(function (index) {
        if($(this).attr('id') != 'tipInfo') {
            var keyIndex = index + 1;
            $(this).attr('id', 'supportUserInfo_' + keyIndex);
            $(this).children(':first-child').text(keyIndex);
        }
    });
}