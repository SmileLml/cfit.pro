/**
 * 撤回评审 需再次确认
 * @returns {boolean}
 */
function recall()
{
   // if(confirm("确认要撤回评审吗?",'确认窗口')){
    if(confirm("是否确定撤回，一旦撤回将从特定流程重新开始",'确认窗口')){
        return true;
    }else{
        return false;
    }
}

/**
 * 评审验证按钮提示
 * @returns {boolean}
 */
function reviewVerifyConfirm()
{
    if(confirm("该评审存在部分问题未验证，请确认是否直接给出验证结论？",'确认窗口')){
        return true;
    }else{
        return location.reload();
    }
}
/**
 * 关闭评审 需再次确认
 * @returns {boolean}
 */
function reviewClose(id,count)
{
    if(count > 0){
        if(confirm("存在N个问题未处理，确定要关闭评审吗？",'确认窗口')){
            $.zui.modalTrigger.show({iframe:createLink("reviewmanage","close","reviewID="+id)+"?onlybody=yes"});
            $('#reviewClose'+id).click();
        }
    }else{
        $.zui.modalTrigger.show({iframe:createLink("reviewmanage","close","reviewID="+id)+"?onlybody=yes"});
        $('#reviewClose'+id).click();
    }
}
