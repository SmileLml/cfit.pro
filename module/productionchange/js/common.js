

//验证权限
function isClickable(id, action){
    $.get(createLink('productionchange', 'ajaxIsClickable', "id=" + id + "&action="+action), function(data)
    {
        var obj = JSON.parse(data);
        if(obj.code == 0){
            $('#isClickable_' + action + id).click();
        }else {
            let errorMsg = new $.zui.Messager({
                type:'warning',
                time: 2000,
            })
            errorMsg.show(obj.data);
        }
    });
}
