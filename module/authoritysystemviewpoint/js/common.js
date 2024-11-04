$(document).ready(function()
{
    setTimeout(function(){
        $('div').removeClass('load-indicator loading');
    },500);

})

/**
 * 增加权限用户
 */
function createUser() {
    var originIndex = 0;
    supportUserIndex++;
    var currentRow = $('#userDemo').children(':first-child').clone();
    currentRow.find('#addWorkItem' + originIndex).attr({'data-id': supportUserIndex, 'id':'addWorkItem' + supportUserIndex});
    currentRow.find('#supportUserTd' + originIndex).attr({'id':'supportUserTd' + supportUserIndex});
    currentRow.find('#ids' + originIndex).attr({'id':'ids' + supportUserIndex,'data-index':supportUserIndex,'name':'ids' + "["+supportUserIndex+"]"});
    currentRow.find('#realname' + originIndex + '_chosen').remove();
    currentRow.find('#realname' + originIndex).attr({'id':'realname' + supportUserIndex,'data-index':supportUserIndex ,'name':'realname' + "["+supportUserIndex+"]"});

    currentRow.find('#account' + originIndex).attr({'id':'account' + supportUserIndex,'data-index':supportUserIndex ,'name':'account' + "["+supportUserIndex+"]"});
    currentRow.find('#deptName' + originIndex).attr({'id':'deptName' + supportUserIndex,'data-index':supportUserIndex,'name':'deptName' + "["+supportUserIndex+"]"});
    currentRow.find('#job' + originIndex).attr({'id':'job' + supportUserIndex,'data-index':supportUserIndex ,'name':'job' + "["+supportUserIndex+"]"});
    currentRow.find('#employeeNumber' + originIndex).attr({'id':'employeeNumber' + supportUserIndex,'data-index':supportUserIndex ,'name':'employeeNumber' + "["+supportUserIndex+"]"});
    currentRow.find('#staffType' + originIndex).attr({'id':'staffType' + supportUserIndex,'data-index':supportUserIndex ,'name':'staffType' + "["+supportUserIndex+"]"});

    currentRow.find("input[name*='account[]']").val('');
    currentRow.find("input[name*='deptName[]']").val('');
    currentRow.find("input[name*='job[]']").val('');
    currentRow.find("input[name*='employeeNumber[]']").val('');
    currentRow.find("input[name*='staffType[]']").val('');
    currentRow.find("input[name*='ids[]']").val('');

    $('#createWorkTr').attr('class','hidden'); //隐藏添加按钮
    $('#dataTBody').append(currentRow);
    $('#realname' + supportUserIndex).attr('class','form-control chosen supportUserSelect');
    $('#realname' + supportUserIndex).chosen();
    $('#realname' + supportUserIndex).val('').trigger("chosen:updated");
}
/**
 * 增加权限
 *
 * @param obj
 */
function addWork(obj){
    //var originIndex = $(obj).attr('data-id');
    var originIndex = 0;
    supportUserIndex++;

    var currentRow = $('#userDemo').children(':first-child').clone();
    currentRow.find('#addWorkItem' + originIndex).attr({'data-id': supportUserIndex, 'id':'addWorkItem' + supportUserIndex});
    currentRow.find('#supportUserTd' + originIndex).attr({'id':'supportUserTd' + supportUserIndex});
    currentRow.find('#ids' + originIndex).attr({'id':'ids' + supportUserIndex,'data-index':supportUserIndex,'name':'ids' + "["+supportUserIndex+"]"});
    currentRow.find('#realname' + originIndex + '_chosen').remove();
    currentRow.find('#realname' + originIndex).attr({'id':'realname' + supportUserIndex,'data-index':supportUserIndex,'name':'realname' + "["+supportUserIndex+"]"});

    currentRow.find('#account' + originIndex).attr({'id':'account' + supportUserIndex,'data-index':supportUserIndex,'name':'account' + "["+supportUserIndex+"]"});
    currentRow.find('#deptName' + originIndex).attr({'id':'deptName' + supportUserIndex,'data-index':supportUserIndex,'name':'deptName' + "["+supportUserIndex+"]"});

    currentRow.find('#job' + originIndex).attr({'id':'job' + supportUserIndex,'data-index':supportUserIndex ,'name':'job' + "["+supportUserIndex+"]"});
    currentRow.find('#employeeNumber' + originIndex).attr({'id':'employeeNumber' + supportUserIndex,'data-index':supportUserIndex ,'name':'employeeNumber' + "["+supportUserIndex+"]"});
    currentRow.find('#staffType' + originIndex).attr({'id':'staffType' + supportUserIndex,'data-index':supportUserIndex ,'name':'staffType' + "["+supportUserIndex+"]"});

    currentRow.find("input[name*='account[]']").val('');
    currentRow.find("input[name*='deptName[]']").val('');
    currentRow.find("input[name*='job[]']").val('');
    currentRow.find("input[name*='employeeNumber[]']").val('');
    currentRow.find("input[name*='staffType[]']").val('');
    currentRow.find("input[name*='ids[]']").val('');
    $(obj).parent().parent().parent().after(currentRow);
    $('#realname' + supportUserIndex).attr('class','form-control chosen supportUserSelect');
    $('#realname' + supportUserIndex).chosen();
    $('#realname' + supportUserIndex).val('').trigger("chosen:updated");

    sortOrderLine()

}

/**
 * 删除记录
 *
 * @param obj
 */
function delWork(obj) {
    var currentRow = $(obj).parent().parent().parent();
    var count = $("#dataTBody select[name*='realname']").length;
    currentRow.remove();
    if (count == 1) { //删除后没有
        $('#createWorkTr').removeClass('hidden');//隐藏添加按钮
    }else if(count > 1){
        sortOrderLine();
    }
}
/**
 * 排序
 */
function sortOrderLine() {
    var keyIndex = 0;
    $('#dataTBody').children('tr').each(function (index) {
        if($(this).attr('id') != 'createWorkTr'){
            keyIndex++;
            $(this).attr('id', 'supportUserInfo_' + keyIndex);
        }
    });
}