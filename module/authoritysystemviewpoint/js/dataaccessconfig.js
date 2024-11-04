
function refresh(){
    window.parent.location.reload();
}

function updateItem(obj){
    var index =  $(obj).attr("data-index");
    var realname = $('#realname'+index).val();
    var name = [];
    $("select[name*='realname'] option:selected").each(function () {
        name.push($(this).val());
    })

    var total = count(name,realname);
    if(total > 1){
        alert('用户'+realname +'已存在！');
        $('#realname'+index).val('').trigger("chosen:updated");
        return false;
    }
    if(realname){
        $.get(createLink('authoritysystemviewpoint', 'ajaxGetNameAndDept','account='+realname), function(data)
        {
            var obj = $.parseJSON(data);
            $('#account'+index).val(obj.account).trigger("chosen:updated");
            $('#deptName'+index).val(obj.dept).trigger("chosen:updated");
            $('#job'+index).val(obj.role).trigger("chosen:updated");
            $('#employeeNumber'+index).val(obj.employeeNumber).trigger("chosen:updated");
            $('#staffType'+index).val(obj.staffType).trigger("chosen:updated");
            /* $(".form-date").eq(index*2-2).datetimepicker('setStartDate', data);
             $(".form-date").eq(index*2-1).datetimepicker('setStartDate', data);*/
        });
    }else{
        $('#account'+index).val('').trigger("chosen:updated");
        $('#deptName'+index).val('').trigger("chosen:updated");
        $('#job'+index).val('').trigger("chosen:updated");
        $('#employeeNumber'+index).val('').trigger("chosen:updated");
        $('#staffType'+index).val('').trigger("chosen:updated");
    }
}
function search(obj){
    var value = $(obj).val();
    link = createLink('authoritysystemviewpoint', 'dataAccessConfig', 'type=' +type+'&searchFlag='+value);
    location.href = link;
}
function count(arr,value){
    return $.grep(arr,function(el){
        return el === value && value !== '' && value !== null;
    }).length;
}