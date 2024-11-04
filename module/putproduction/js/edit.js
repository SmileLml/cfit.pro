changeLevel();
setStageInfo();
changeProperty();
$(".saveBtn").click(function () {
    $("[name='issubmit']").val("save");
    submitData('saveBtn');
});
//提交需要校验数据
$(".submitBtn").click(function () {
    $("[name='issubmit']").val("submit");
    var msg = "确认要提交吗，提交后将进入审批环节";
    if(confirm(msg) == true){
        submitData('submitBtn');
    }else{
        return false;
    }
});