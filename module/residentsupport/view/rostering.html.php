<?php
/**
 * The view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     calendar
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/ext/view/calendar.html.php';?>

<div id="mainMenu" class="clearfix">
        <table class='table table-form table_tr'>
            <tr>
                <th class='w-40px'><?php echo $lang->residentsupport->type;?></th>
                <td class='w-60px'><?php echo html::select('type', $type,'1', "class='form-control' onchange='getTemplate()'");?></td>
                <th class='w-40px'><?php echo $lang->residentsupport->subType;?></th>
                <td class='w-60px'><?php echo html::select('subType', $subType, '1', "class='form-control' onchange='getTemplate()'");?>
                <th class='w-40px'><?php echo $lang->residentsupport->choiceRostering;?></th>
                <td class='w-90px'><?php echo html::select('templateId', [],'1', "class='form-control chosen' required onchange='setTemplate()'");?></td>

                <td class='w-130px'>
                    <div class="table-row" style="width: 100%">
                        <div class="table-col">
                            <div class="input-group">
                                <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;">值班开始</span>
                                <?php echo html::input('startDate', '', "class='form-control form-date' onchange='checkDate()'");?>
                                <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;">结束</span>
                                <?php echo html::input('endDate', '', "class='form-control form-date' onchange='checkDate()'");;?>
                            </div>
                        </div>
                    </div>
                </td>

                <td class='w-40px'><?php echo html::commonButton('搜索', 'onclick="search()"', 'btn btn-primary centerauto ','');?></td>
            </tr>
            <input type="hidden" id="date" value="">
        </table>
</div>

<div class="main-row">
  <div class="main-col">
    <div class="cell">

    </div>
  </div>
</div>
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
    function checkDate() {
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var date = $("#date").val();
        date = date.split("~")
        if (startDate < date[0]){
            $("#startDate").val(date[0]);
            alert("开始时间不能小于模板开始时间");
        }
        if (endDate > date[1]){
            $("#endDate").val(date[0]);
            alert("开始时间不能大于模板结束时间");
        }
        if (startDate > endDate){
            $("#startDate").val(date[0]);
            $("#endDate").val(date[1]);
            alert("开始时间不能大于结束时间");
        }
    }
    function search() {
        checkDate();
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var templateId = $("#templateId option:selected").val()
        if (templateId == '' || startDate == '' || endDate == ''){
            alert('请选择模板或是时间范围');
            return false
        }
        var type = $("#type option:selected").val();
        var subType = $("#subType option:selected").val();
        var link = '<?php echo $this->createLink('residentsupport', 'onLineScheduling');?>';
        link = link+"?source=1&templateId="+templateId+"&startDate="+startDate+"&endDate="+endDate+"&type="+type+"&subType="+subType
        location.href = link;
    }
</script>
<?php include '../../common/view/footer.html.php';?>
