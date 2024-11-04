setMinStartDate();

$(".saveBtn").click(function () {
    $("[name='issubmit']").val("save");
    submitData('saveBtn');
});

//提交需要校验数据
$(".submitBtn").click(function () {
    $("[name='issubmit']").val("submit");
    if(confirm(submitConfirmMsg) == true){
        submitData('submitBtn');
    }else{
        return false;
    }
});