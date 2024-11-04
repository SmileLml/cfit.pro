/**
 * 根据系统获得产品
 */
getProductName(productIds);

/**
 * 获得项目
 */
changeFixType(implementationForm, projectPlanId);
/**
 * 获得异常单关联的信息
 */
selectabnormalCode(problemIds, demandIds, secondorderIds);

changeLevel(level);

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
