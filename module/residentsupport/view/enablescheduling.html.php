<?php include '../../common/view/header.lite.html.php';?>
<?php include '../../common/ext/view/calendar.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px;">
    <div class='center-block'>
        <div class='main-header'>
            <h2><?php echo $lang->residentsupport->enableScheduling;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>

            <table class='table table-form'>
                <tr>
                    <th class='w-60px'><?php echo $lang->residentsupport->type;?></th>
                    <td class='w-80px'>
                        <?php echo html::select('type', $lang->residentsupport->typeList,'1', "class='form-control' onchange='getTemplate()'");?>
                    </td>
                    <th class='w-60px'><?php echo $lang->residentsupport->subType;?></th>
                    <td class='w-80px'>
                        <?php echo html::select('subType', $lang->residentsupport->subTypeList, '1', "class='form-control' onchange='getTemplate()'");?>
                    </td>
                    <th class='w-60px'><?php echo $lang->residentsupport->choiceRostering;?></th>
                    <td class='w-100px'>
                        <?php echo html::select('templateId', [],'1', "class='form-control chosen' required onchange='setTemplate()'");?>
                    </td>
                </tr>

                <tr>
                    <th class='w-60px'><?php echo $lang->residentsupport->startDate;?></th>
                    <td class='w-80px'>
                        <?php echo html::input('startDate', '', "class='form-control form-date' onchange='checkDate()'");?>
                    </td>
                    <th class='w-60px'>
                        <?php echo $lang->residentsupport->endDate;?>
                    </th>
                    <td class='w-80px'>
                        <?php echo html::input('endDate', '', "class='form-control form-date' onchange='checkDate()'");?>
                    </td>
                    <td class='w-100px'>
                        <!--
                        <?php echo html::commonButton('搜索', 'onclick="search()"', 'btn btn-primary centerauto ','');?>
                        -->
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->residentsupport->currentComment;?></th>
                    <td colspan='5'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                </tr>

                <tr>
                    <td class='text-center' colspan='6'>
                        <input type="hidden" id="date" value="">
                        <?php echo html::submitButton('', '', 'btn btn-wide btn-primary enableScheduling');?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.lite.html.php';?>
<script>
    function getTemplate() {
        var type = $("#type option:selected").val();
        var subType = $("#subType option:selected").val();
        $.post(createLink('residentsupport', 'ajaxGetTemplate'),{type:type,subType:subType,source:1},function (res) {
            $('#templateId').siblings().remove();
            $('#templateId').replaceWith(res);
            $('#templateId').chosen();
        })
    }
    getTemplate();
    function setTemplate() {
        var templateStr = $("#templateId option:selected").text();
        if (templateStr){
            var arr = templateStr.split(" ");
            var date = arr[1].split("~");
            $("#startDate").val(date[0]);
            $("#endDate").val(date[1]);
            $("#date").val(arr[1]);

        }else{
            $("#startDate").val('');
            $("#endDate").val('');
            $("#date").val('');
        }
    }
    /**
     *获得当前日期
     *
     * @returns {string}
     */
    function getCurrentDate() {
        var dateObj = new Date();
        var year = dateObj.getFullYear();
        var month = dateObj.getMonth() + 1;
        if(month < 10){
            month = '0' + month.toString();
        }
        var day = dateObj.getDate();
        if(day < 10){
            day = '0'+day.toString();
        }
        var currentDate = year.toString() + '-' + month.toString() + '-' + day.toString();
        return currentDate;
    }

    /**
     * 检查时间
     */
    function checkDate() {
        var checkRes = true;
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var currentDate = getCurrentDate();
        var date = $("#date").val();
        date = date.split("~");

        if (startDate < date[0]){
            $("#startDate").val(date[0]);
            alert("开始时间不能小于模板开始时间");
            checkRes = false;
            return checkRes;
        }

        if (endDate > date[1]){
            $("#endDate").val(date[0]);
            alert("开始时间不能大于模板结束时间");
            checkRes = false;
            return checkRes;
        }
        if (startDate > endDate){
            $("#startDate").val(date[0]);
            $("#endDate").val(date[1]);
            alert("开始时间不能大于结束时间");
            checkRes = false;
            return checkRes;
        }
        if(startDate <= currentDate){
            alert("值班开始时间须大于当天时间");
            checkRes = false;
            return checkRes;
        }
        return checkRes;
    }

    /*
    function search() {
        //检查时间
        var checkRes = checkDate();
        if(!checkRes){
            return false;
        }
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var templateId = $("#templateId option:selected").val();
        if (templateId == '' || startDate == '' || endDate == ''){
            alert('请选择模板或是时间范围');
            return false
        }
    }
    */


    //验证，非委派时检查评审问题数量
    $('.enableScheduling').click(function (){
        var startDate  = $("#startDate").val();
        var endDate    = $("#endDate").val();
        var templateId = $("#templateId option:selected").val();
        if (templateId == '' || startDate == '' || endDate == ''){
            alert('请选择模板和时间范围');
            return false
        }
        //检查时间
        var checkRes = checkDate();
        if(!checkRes){
            return false;
        }

        //是否提交
        var isSubmit = false;
        var url = createLink('residentsupport', 'ajaxGetAllowEnableTemplateInfo');
        var data = {
            'templateId':templateId,
            'startDate':startDate,
            'endDate':endDate,
        };
        $.ajaxSettings.async = false;
        $.post(url, data, function(res){
            if(!res.result){
                alert(res.message);
            }else {
                if(res.code != '1'){
                    if(confirm('该时间段已有其他子类排班，如果启用该排班类型将关闭同时间段其他子类的排班，请确认是否启用该值班类型？')){
                        isSubmit = true;
                    }
                }else {
                    isSubmit = true;
                }
            }
        }, 'json');
        $.ajaxSettings.async = true;
        return isSubmit;
    });
</script>
