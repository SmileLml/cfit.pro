
/*按时间查询*/
function checkDate(){
    var end = $('#enddate').val().replaceAll('-','');
    var begin = $('#date').val().replaceAll('-','');
    if(begin.length == 0 || end.length == 0){
        alert('开始时间或结束时间不能为空');
        $('#enddate').val('');
        return false;
    }
    if(end < begin){
        alert('结束时间不能早于开始时间！');
        $('#enddate').val('');
        return false;
    }
    link = createLink('workreport', 'history', 'param=' +param+'&recTotal='+recTotal+'&recPerPage='+recPerPage+'&pageID='+pageID+'&begin='+begin+'&end='+end);
    location.href = link;
}
function checkBegin(){
    var end = $('#enddate').val().replaceAll('-','');
    var begin = $('#date').val().replaceAll('-','');

    if(end < begin && end.length != 0 && begin.length != 0){
        alert('结束时间不能早于开始时间！');
        $('#enddate').val('');
        return false;
    }
    if(end.length != 0 && begin.length != 0){
        link = createLink('workreport', 'history', 'param=' +param+'&recTotal='+recTotal+'&recPerPage='+recPerPage+'&pageID='+pageID+'&begin='+begin+'&end='+end);
        location.href = link;
    }

}
