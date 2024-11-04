$(document).ready(function () {
    for (k in content) {
        controlSub(content[k]['subSystem'], k)

        const selectObj1 = renderSelect('svnPermissionContent', k, svnAuthority, true)
        selectObj1.setValue(content[k]['svnPermissionContent'].split(','))

        const selectObj2 = renderSelect('gitLabPermissionContent', k, gitlabAuthority, true)
        selectObj2.setValue(content[k]['gitLabPermissionContent'].split(','))

        const selectObj3 = renderSelect('jenkinsPermissionContent', k, jenkinsAuthority, true)
        selectObj3.setValue(content[k]['jenkinsPermissionContent'].split(','))

        const selectObj4 = renderSelect('svnPermission', k, JSON.parse(svnPermission),true,true,false,"hidden",false,true,true)
        selectObj4.setValue(content[k]['svnPermission'].split(','))

        const selectObj5 = renderSelect('gitLabPermission', k, JSON.parse(gitLabPermission),true,true,false,"hidden",false,true,true)
        selectObj5.setValue(content[k]['gitLabPermission'].split(','))

        const selectObj6 = renderSelect('jenkinsPermission', k, JSON.parse(jenkinsPermission),true,true,false,"hidden",false,true,true)
        selectObj6.setValue(content[k]['jenkinsPermission'].split(','))

        controlRealSub(content[k]['subSystem'], k)
    }
    if (realPermission!=='') {
        for (k in realPermission) {
            // 实际分配权限时的数据渲染
            var selectObj = '';
            selectObj = renderSelect('realSvnPermissionPath', k, svnAuthority, true)
            selectObj.setValue(realPermission[k]['realSvnPermissionPath']?.split(',')?realPermission[k]['realSvnPermissionPath']?.split(','):[])

            selectObj = renderSelect('realGitLabPermissionPath', k, gitlabAuthority, true)
            selectObj.setValue(realPermission[k]['realGitLabPermissionPath']?.split(',')?realPermission[k]['realGitLabPermissionPath']?.split(','):[])

            selectObj = renderSelect('realJenkinsPermissionPath', k, jenkinsAuthority, true)
            selectObj.setValue(realPermission[k]['realJenkinsPermissionPath']?.split(',')?realPermission[k]['realJenkinsPermissionPath']?.split(','):[])

            var selectObjOperate = '';
            selectObjOperate = renderSelect('realZtPermissionOperate', k, ztPermission, true)
            selectObjOperate.setValue(realPermission[k]['realZtPermissionOperate']?.split(',')?realPermission[k]['realZtPermissionOperate']?.split(','):[])

            selectObjOperate = renderSelect('realSvnPermissionOperate', k, JSON.parse(svnPermission), true)
            selectObjOperate.setValue(realPermission[k]['realSvnPermissionOperate']?.split(',')?realPermission[k]['realSvnPermissionOperate']?.split(','):[])

            selectObjOperate = renderSelect('realGitLabPermissionOperate', k, JSON.parse(gitLabPermission), true)
            selectObjOperate.setValue(realPermission[k]['realGitLabPermissionOperate']?.split(',')?realPermission[k]['realGitLabPermissionOperate']?.split(','):[])

            selectObjOperate = renderSelect('realJenkinsPermissionOperate', k, JSON.parse(jenkinsPermission), true)
            selectObjOperate.setValue(realPermission[k]['realJenkinsPermissionOperate']?.split(',')?realPermission[k]['realJenkinsPermissionOperate']?.split(','):[])
            controlRealSub(realPermission[k]['involveSubSystem'], k)
        }
    }
    // 如果存在权限分配节点，渲染对应内容
   var permissionAssign= $('#permissionAssign').html();
    if(permissionAssign){
        // 如果已经保存了实际分配内容则渲染该内容，否则渲染申请时的内容
        if (realPermission!=='') {
            for (k in realPermission) {
                // 实际分配权限时的数据渲染
                // const selectObj4 = renderSelect('realZtPermissionPath', k, svnAuthority ,true)
                // selectObj4.setValue(content[k]['realZtPermissionPath'].split(','))
                var selectObj = '';

                selectObj = renderSelect('realSvnPermissionPath', k, svnAuthority, false)
                selectObj.setValue(realPermission[k]['realSvnPermissionPath']?.split(',')?realPermission[k]['realSvnPermissionPath']?.split(','):[])

                selectObj = renderSelect('realGitLabPermissionPath', k, gitlabAuthority, false)
                selectObj.setValue(realPermission[k]['realGitLabPermissionPath']?.split(',')?realPermission[k]['realGitLabPermissionPath']?.split(','):[])

                selectObj = renderSelect('realJenkinsPermissionPath', k, jenkinsAuthority, false)
                selectObj.setValue(realPermission[k]['realJenkinsPermissionPath']?.split(',')?realPermission[k]['realJenkinsPermissionPath']?.split(','):[])

                var selectObjOperate = '';
                selectObjOperate = renderSelect('realZtPermissionOperate', k, ztPermission, false)
                selectObjOperate.setValue(realPermission[k]['realZtPermissionOperate']?.split(',')?realPermission[k]['realZtPermissionOperate']?.split(','):[])

                selectObjOperate = renderSelect('realSvnPermissionOperate', k, JSON.parse(svnPermission), false,true,false,"hidden",false)
                selectObjOperate.setValue(realPermission[k]['realSvnPermissionOperate']?.split(',')?realPermission[k]['realSvnPermissionOperate']?.split(','):[])

                selectObjOperate = renderSelect('realGitLabPermissionOperate', k, JSON.parse(gitLabPermission), false,true,false,"hidden",false)
                selectObjOperate.setValue(realPermission[k]['realGitLabPermissionOperate']?.split(',')?realPermission[k]['realGitLabPermissionOperate']?.split(','):[])

                selectObjOperate = renderSelect('realJenkinsPermissionOperate', k, JSON.parse(jenkinsPermission), false,true,false,"hidden",false)
                selectObjOperate.setValue(realPermission[k]['realJenkinsPermissionOperate']?.split(',')?realPermission[k]['realJenkinsPermissionOperate']?.split(','):[])
                controlRealSub(realPermission[k]['involveSubSystem'], k)

            }
        } else {
            for (k in content) {
                // 实际分配权限时的数据渲染
                // const selectObj4 = renderSelect('realZtPermissionPath', k, svnAuthority ,true)
                // selectObj4.setValue(content[k]['realZtPermissionPath'].split(','))
                var selectObj = '';
                selectObj = renderSelect('realSvnPermissionPath', k, svnAuthority, false)
                selectObj.setValue(content[k]['svnPermissionContent'].split(','))
                selectObj = renderSelect('realGitLabPermissionPath', k, gitlabAuthority, false)
                selectObj.setValue(content[k]['gitLabPermissionContent'].split(','))
                selectObj = renderSelect('realJenkinsPermissionPath', k, jenkinsAuthority, false)
                selectObj.setValue(content[k]['jenkinsPermissionContent'].split(','))

                renderSelect('realZtPermissionOperate', k, ztPermission, false)
                renderSelect('realSvnPermissionOperate', k, JSON.parse(svnPermission), false,true,false,"hidden",false,true)
                renderSelect('realGitLabPermissionOperate', k, JSON.parse(gitLabPermission), false,true,false,"hidden",false,true)
                renderSelect('realJenkinsPermissionOperate', k, JSON.parse(jenkinsPermission), false,true,false,"hidden",false,true)
                controlRealSub(content[k]['subSystem'], k)
            }
        }
    }

})



$('select').prop('disabled', true).trigger('chosen:updated')
$('#p-realContent select').prop('disabled', false).trigger('chosen:updated')

// function changeDealResult(e) {
//     var result = e.value;
//     if (result == '2') {
//
//         $(e).parent().parent().parent().parent().parent().find('textarea').parent().addClass('required');
//     } else {
//         $(e).parent().parent().parent().parent().parent().find('textarea').parent().removeClass('required');
//
//     }
// }

function applyInfoBtnClick() {
    $('#applyInfoBtn').addClass('active1')
    $('#applyInfo').show()
    $('#historyRecord').hide()
    $('#flowImg').hide()
    $('#historyRecordBtn').removeClass('active1')
    $('#flowImgBtn').removeClass('active1')
}

function historyRecordBtnClick() {
    $('#historyRecordBtn').addClass('active1')
    $('#applyInfo').hide()
    $('#historyRecord').show()
    $('#flowImg').hide()
    $('#applyInfoBtn').removeClass('active1')
    $('#flowImgBtn').removeClass('active1')
}

function flowImgBtnClick() {
    $('#flowImgBtn').addClass('active1')
    $('#applyInfo').hide()
    $('#historyRecord').hide()
    $('#flowImg').show()
    $('#historyRecordBtn').removeClass('active1')
    $('#applyInfoBtn').removeClass('active1')
}

function changeDealResultPermissionAssign() {

    var result = $("input[name='dealResult']:checked").val();
    if (result == '2') {
        $('#suggest-td').addClass('required');
        $('#permissionAssign').hide();
        $('.saveBtn1').addClass('hidden');

    } else {
        $('#suggest-td').addClass('required');
        $('#permissionAssign').show();
        $('.saveBtn1').removeClass('hidden');

    }
}

function addPermissionRow() {
    const templ = document.getElementById("p-realContentTpl");
    const tbody = document.getElementById("p-realContent");

    const templSelect = templ.content.querySelectorAll('tr td select');
    var a = $("#p-realContent tr:last td select:first ").data('id');
    a = a + 1
    templSelect[0].name = 'involveSubSystem[' + a + ']';
    templSelect[0].id = 'involveSubSystem' + a;
    templSelect[0].setAttribute('data-id', a)
    templSelect[1].name = 'realOpenPermissionPerson[' + a + '][]';
    templSelect[1].id = 'realOpenPermissionPerson' + a;
    // tr > td:nth-child(3) > div > div:nth-child(1) > div.xm.ml-10
    const templDiv = templ.content.querySelectorAll('tr > td:nth-child(3) > div > div');
    templDiv[0].id = 'realZt' + a;
    templDiv[1].id = 'realSvn' + a;
    templDiv[2].id = 'realGitLab' + a;
    templDiv[3].id = 'realJenkins' + a;
    templDiv[4].id = 'realOther' + a;
    const templDiv0 = templDiv[0].querySelectorAll('div');
    templDiv0[0].id = 'realZtPermissionOperate' + a;

    const templDiv1 = templDiv[1].querySelectorAll('div');
    templDiv1[0].id = 'realSvnPermissionPath' + a;
    templDiv1[1].id = 'realSvnPermissionOperate' + a;

    const templDiv2 = templDiv[2].querySelectorAll('div');
    templDiv2[0].id = 'realGitLabPermissionPath' + a;
    templDiv2[1].id = 'realGitLabPermissionOperate' + a;

    const templDiv3 = templDiv[3].querySelectorAll('div');
    templDiv3[0].id = 'realJenkinsPermissionPath' + a;
    templDiv3[1].id = 'realJenkinsPermissionOperate' + a;

    const templDiv4 = templDiv[4].querySelectorAll('input');
    templDiv4[0].id = 'realOtherPermissionOperate' + a;
    templDiv4[0].name = 'realOtherPermissionOperate[' + a + ']';
    tbody.appendChild(templ.content.cloneNode(true))

    $('#realOpenPermissionPerson' + a).chosen();
    $('#involveSubSystem' + a).chosen();
    emptyRealPermission(a)
    controlRealSub($('#involveSubSystem' + a).val(), a)
}
function emptyRealPermission(a){
    var selectObj = '';
    selectObj = renderSelect('realSvnPermissionPath', a, svnAuthority, false)
    selectObj.setValue([])
    selectObj = renderSelect('realGitLabPermissionPath', a, gitlabAuthority, false)
    selectObj.setValue([])
    selectObj = renderSelect('realJenkinsPermissionPath', a, jenkinsAuthority, false)
    selectObj.setValue([])

    selectObj = renderSelect('realZtPermissionOperate', a, ztPermission, false)
    selectObj.setValue([])
    selectObj = renderSelect('realSvnPermissionOperate', a, JSON.parse(svnPermission), false,true,false,"hidden",false,true)
    selectObj.setValue([])
    selectObj = renderSelect('realGitLabPermissionOperate', a, JSON.parse(gitLabPermission), false,true,false,"hidden",false,true)
    selectObj.setValue([])
    selectObj = renderSelect('realJenkinsPermissionOperate', a, JSON.parse(jenkinsPermission), false,true,false,"hidden",false,true)
    selectObj.setValue([])
    $('#realOpenPermissionPerson' + a).val("").trigger('chosen:updated')
    $('#realOtherPermissionOperate' + a).val("")
}

function subInvolveSystemChange(e) {
    var involveSubSystem = $('#' + e.id).val()
    var a = $('#' + e.id).data('id')
    // 移除当前行的校验提示
    $(e).parent().parent().find('.text-danger').remove()
    $(e).parent().parent().find('.has-error').removeClass('has-error')
    // 切换系统选择清空值
    emptyRealPermission(a)
    // 控制子系统对应的显示分配内容
    controlRealSub(involveSubSystem, a)

}

function delPermissionRow(obj) {
    if($('#p-realContent').find('tr').length<=2){
        alert("当前只剩一行数据不可删除")
        return;
    }
    var currentRow = $(obj).parent().parent().parent();
    var select = $(obj).parent().parent().prev().find('select')
    select.val("")
    currentRow.remove();
}

$(".saveBtn").click(function () {
    $("[name='issubmit']").val("submit");
    submitData('saveBtn');
});
$(".saveBtn1").click(function () {
    $("[name='issubmit']").val("save");
    submitData('saveBtn1');
});