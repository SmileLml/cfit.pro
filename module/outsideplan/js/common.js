function computeWorkDays()
{
    var begin = $('#begin').val();
    var end   = $('#end').val();
    if(!end || !begin) return false;
    
    var days = computeDaysDelta(begin, end);
    if(days <= 0) {
        alert('计划完成日期不应小于开始日期');
        $('#end').val('');
        $('#workload').val('');
        return false;
    }
    $('#workload').val(days);
}

function convertStringToDate(dateString)
{
    dateString = dateString.split('-');
    return new Date(dateString[0], dateString[1] - 1, dateString[2]);
}

function computeDaysDelta(date1, date2)
{
    date1 = convertStringToDate(date1);
    date2 = convertStringToDate(date2);
    delta = (date2 - date1) / (1000 * 60 * 60 * 24) + 1;

    weekEnds = 0;
    for(i = 0; i < delta; i++)
    {   
        // if((weekend == 2 && date1.getDay() == 6) || date1.getDay() == 0) weekEnds ++;
        date1 = date1.valueOf();
        date1 += 1000 * 60 * 60 * 24; 
        date1 = new Date(date1);
    }   
    return delta - weekEnds;
}
// 新建编辑页
function addSub()
{
    let id = parseInt(Math.random() * 100001 + 100000);
    let $currentRow = $('.subBlocks:eq(0)').clone();
    $currentRow.attr({'id':id});

    $('#sub_1').append($currentRow);
    $('#'+id +' input').val('');
}
// 拆分任务页
function addSub2()
{
    let id = parseInt(Math.random() * 100001 + 100000);
    let $currentRow = $('.subBlocks:eq(0)').clone();
    $currentRow.attr({'id':id});

    $('#sub_1').append($currentRow);
    $('#'+id +' input').val('');
    $('#'+id +' textarea').val('');
    $('#'+id +' select').val('');
    $('#'+id +' .form-date').datetimepicker(
    {
        language:  config.clientLang,
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0,
        format: 'yyyy-mm-dd'
    });
}
function delSub(obj)
{
    if($('.subBlocks').length == 1){ return; }
    $(obj).parent().parent().remove();
}

function delSub2(obj)
{
    if($('.subBlocks').length == 1){ return; }
    $(obj).parent().parent().remove();
}

function showsub(id)
{
    $(".subpro"+id).toggle();
    var $toggle = $("#toggleid"+id);
    var isCollapsed = $toggle.hasClass('table-nest-child-hide');
    if(isCollapsed)
    {
        $toggle.removeClass('table-nest-child-hide');
    }
    else
    {
        $toggle.addClass('table-nest-child-hide');
    }
}

function checkDate()
{
    // alert(begin);
    let flag = true;
    let tmpdate = '2000-01-01';
    let tmpdate2 = '2999-01-01';
    let oldendobj = new Date(end); //计划结束时间
    let oldendobj2 = new Date(begin); //计划开始时间
    let tmpdateobj = new Date(tmpdate);
    let tmpdateobj2 = new Date(tmpdate2);
    $(".subTaskEnd").each(function (i){

       let endobj = new Date($(this).val());

       if(endobj.getTime() > tmpdateobj.getTime()){

           tmpdate = $(this).val();
           tmpdateobj = new Date(tmpdate);
       }
    })
    $(".subTaskBegin").each(function (i){

        let endobj = new Date($(this).val());

        if(endobj.getTime() < tmpdateobj2.getTime()){

            tmpdate2 = $(this).val();
            tmpdateobj2 = new Date(tmpdate2);
        }
    })
    if(tmpdateobj.getTime() > oldendobj.getTime()){

        if(confirm("当前的(外部)子项/子任务计划完成时间("+tmpdate+")大于(外部)项目/任务计划完成时间("+ end +")，请确认。若点击确认将自动更新(外部)项目/任务计划完成时间为("+tmpdate+")")){
            flag = true;
        } else {
            return false
        }
    }
    if(tmpdateobj2.getTime() < oldendobj2.getTime()){
        if(confirm("当前的(外部)子项/子任务计划开始时间("+tmpdate2+")小于(外部)项目/任务计划开始时间("+ begin +")，请确认。若点击确认将自动更新(外部)项目/任务计划开始时间为("+tmpdate2+")")){
            flag = true;
        } else {
            return false
        }
    }
    return flag;
}