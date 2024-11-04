setMinStartDate();
setDefaultDateTime(); //默认报工时间
changeArea(area);
/**
 * 保存
 */
$(".saveBtn").click(function () {
    $("[name='issubmit']").val("save");
    submitData('saveBtn');
});

/**
 * 提交
 */
$(".submitBtn").click(function () {
    $("[name='issubmit']").val("submit");
    if(confirm(submitConfirmMsg) == true){
        submitData('submitBtn');
    }else{
        return false;
    }
});