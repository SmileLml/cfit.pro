/**
 * 提交操作
 *
 * @param btnClass
 */
function submitData(btnClass) {
    $('.buttonInfo').attr('type', 'button');
    $('.' + btnClass).attr('type', 'submit');
}


$(".saveBtn").click(function () {
    $("[name='issubmit']").val("save");
    submitData('saveBtn');
});

//提交需要校验数据
$(".submitBtn").click(function () {
    $("[name='issubmit']").val("submit");
    var msg = "确认要提交吗，提交后将进入审批环节";
    if (confirm(msg) == true) {
        submitData('submitBtn');
    } else {
        return false;
    }
});
// function addRow() {
//     const templ = document.getElementById("p-contentTpl");
//     const tbody = document.getElementById("p-content");
//
//     const templSelect = templ.content.querySelectorAll('tr td select');
//     var a = $("#p-content tr:last td select:first ").data('id');
//     a = a + 1
//     // 子系统选择下拉框
//     templSelect[0].name = 'subSystem[' + a + ']';
//     templSelect[0].id = 'subSystem' + a;
//     templSelect[0] .setAttribute('data-id', a)
// // 子系统为研发过程时，申请内容为文本框
//     const templText = templ.content.querySelector('tr td textarea');
//     templText.name = 'permissionContent[' + a + ']';
//     templText.id = 'permissionContent' + a;
//     // 子系统为svn时，申请内容为SVN权限下拉框+输入框
//     templSelect[1].name = 'svnPermissionContent[' + a + '][]';
//     templSelect[1].id  =  'svnPermissionContent' + a;
//     // 子系统为gilLab时，申请内容为gitLab权限下拉框+输入框
//     templSelect[2].name = 'gitLabPermissionContent[' + a + '][]';
//     templSelect[2].id  =  'gitLabPermissionContent' + a;
//     // 子系统为jenkins时，申请内容为jenkins权限下拉框+输入框
//     templSelect[3].name = 'jenkinsPermissionContent[' + a + '][]';
//     templSelect[3].id  =  'jenkinsPermissionContent' + a;
//
//     const templInput= templ.content.querySelector('tr td input');
//     templInput.name = 'permissionContentSummary[' + a + ']';
//     templInput.id = 'permissionContentSummary' + a;
//     // 开通权限人员
//     templSelect[4].name = 'openPermissionPerson[' + a + '][]';
//     templSelect[4].id = 'openPermissionPerson' + a;
//
//     tbody.appendChild(templ.content.cloneNode(true))
//     $('#openPermissionPerson' + a).chosen();
//     $('#subSystem' + a).chosen();
//
//
// }
function addRow() {
    const templ = document.getElementById("p-contentTpl");
    const tbody = document.getElementById("p-content");

    const templSelect = templ.content.querySelectorAll('tr td select');
    var a = $("#p-content tr:last td select:first ").data('id');
    a = a + 1
    // 子系统选择下拉框
    templSelect[0].name = 'subSystem[' + a + ']';
    templSelect[0].id = 'subSystem' + a;
    templSelect[0].setAttribute('data-id', a)
// 子系统为研发过程时，申请内容为文本框
    const templText = templ.content.querySelector('tr td textarea');
    templText.name = 'permissionContent[' + a + ']';
    templText.id = 'permissionContent' + a;

    // const templInput = templ.content.querySelector('tr td input');
    // templInput.name = 'permissionContentSummary[' + a + ']';
    // templInput.id = 'permissionContentSummary' + a;
    // 开通权限人员
    templSelect[1].name = 'openPermissionPerson[' + a + '][]';
    templSelect[1].id = 'openPermissionPerson' + a;
    const templDiv = templ.content.querySelectorAll('#subContent>div');
    templDiv[0].id = 'svnPermissionContent' + a;
    templDiv[1].id = 'gitLabPermissionContent' + a;
    templDiv[2].id = 'jenkinsPermissionContent' + a;
    templDiv[3].id = 'svnPermission' + a;
    templDiv[4].id = 'gitLabPermission' + a;
    templDiv[5].id = 'jenkinsPermission' + a;

    tbody.appendChild(templ.content.cloneNode(true))
    $('#openPermissionPerson' + a).chosen();
    $('#subSystem' + a).chosen();
    emptyPermissionContent(a)
    controlSub($('#subSystem' + a).val(),a)

}
function emptyPermissionContent(a){
    var selectObj='';
    selectObj = renderSelect('svnPermissionContent', a, svnAuthority)
    selectObj.setValue([])
    selectObj = renderSelect('gitLabPermissionContent', a, gitlabAuthority)
    selectObj.setValue([])
    selectObj = renderSelect('jenkinsPermissionContent', a, jenkinsAuthority)
    selectObj.setValue([])

    selectObj = renderSelect('svnPermission', a,  JSON.parse(svnPermission),false,true,false,"hidden",false,true,true)
    selectObj.setValue([])
    selectObj.setValue([])
    selectObj = renderSelect('gitLabPermission', a, JSON.parse(gitLabPermission),false,true,false,"hidden",false,true,true)
    selectObj.setValue([])
    selectObj.setValue([])
    selectObj = renderSelect('jenkinsPermission', a, JSON.parse(jenkinsPermission),false,true,false,"hidden",false,true,true)
    selectObj.setValue([])
    selectObj.setValue([])
    $('#openPermissionPerson' + a).val("").trigger('chosen:updated')
    $('#permissionContent' + a).val("")
}

function renderSelect(id, a, data,disabled=false,isRadio = false,isFilter=true,isShowIcon='hidden',isclickExpand=true,isExpandKeys=false,isClickclose=false) {
    var obj = xmSelect.render({
        el: '#' + id + a,
        height: "200px",
        disabled:disabled,
        name: id + '[' + a + ']',
        filterable: isFilter,
        filterMethod:function (val,item) {
            if(val==item.value){
                return true;
            }
            if(item.name.toUpperCase().indexOf(val.toUpperCase())!=-1){
                return true;
            }
            return  false;
        },
        pageEmptyShow: false,
        searchTips: "请搜索",
        direction: 'down',
        radio:isRadio,
        clickClose:isClickclose,
        theme: {
            color: "#0081ff"
        },
        model:{

          icon:isShowIcon
        },
        autoRow: true,
        tree: {
            show: false,
            showFolderIcon: true,
            showLine: true,
            expandedKeys: isExpandKeys?[data[0].value]:[],
            lazy: true,
            clickExpand:isclickExpand
        },
        data() {
            return data
        }
    })
    return obj;
}

function delRow(obj) {
    if($('#p-content').find('tr').length<=2){
        alert("当前只剩一行数据不可删除")
        return;
    }
    var currentRow = $(obj).parent().parent().parent();
    var select = $(obj).parent().parent().prev().find('select')
    select.val("")
    openPermissionPersonChange()
    currentRow.remove();
}

// 开通权限人员改变时，请求接口判断这些人中是否是有实习，厂商，外协人员，有的话拿到申请部门的分管领导
function openPermissionPersonChange() {
    // 开通权限人员id
    var openPermissionPersons = []
    $('select[name^="openPermissionPerson"]').each(function () {
        var selestedValues = $(this).find('option:selected').map(function () {
            return $(this).val();
        }).get();
        openPermissionPersons = openPermissionPersons.concat(selestedValues)
    })
    if (openPermissionPersons.length == 0) {
        openPermissionPersons = ''
    }
    // 申请部门的id
    var applyDepartment = $('#applyDepartment').val();
    $.post(createLink('authorityapply', 'ajaxGetChargeLeaderByUser'), {
        openPermissionPerson: openPermissionPersons,
        applyDepartment: applyDepartment
    }, function (res) {
        // res 为返回的分管领导值
        if (res != '') {
            var deptLeader = JSON.parse(res);
            $('#departChargeCEO td').text(deptLeader.realname);
            $('#departChargeCEO').show();
            $('#thisDeptChargeLeader').val(deptLeader.account)
        } else {
            $('#thisDeptChargeLeader').val('')
            $('#departChargeCEO td').text('');
            $('#departChargeCEO').hide();
        }
    })

}

// 申请部门改变时，获取改变后的申请部门负责人 , cm 也要改变
function applyDepartmentChange(e) {
    var applyDepartment = $('#' + e.id).val()
    // 审批部门id
    var approvalDepartment = $('#approvalDepartment').val();
    if (approvalDepartment) {
        approvalDepartment.push(applyDepartment)
    } else {
        approvalDepartment = applyDepartment
    }
    $.post(createLink('authorityapply', 'ajaxGetManagerByUser'), {
        approvalDepartment: approvalDepartment,
        applyDepartment: applyDepartment
    }, function (res) {
        // res 为返回的分管领导值

        if (res != '') {
            var deptLeader = JSON.parse(res);
            $('#manager1 td').text(deptLeader.manager1.realname);
            $('#departChargeCM td').text(deptLeader.cm.realname);

            $('#thisDeptLeader').val(deptLeader.manager1.thisDeptLeader);
            $('#thatDeptLeader').val(deptLeader.manager1.thatDeptLeader);
            $('#cm').val(deptLeader.cm.account);
            // 判断原先是否有分管领导，有则替换
            var thisDeptChargeLeader = $('#thisDeptChargeLeader').val();
            if (thisDeptChargeLeader) {
                $('#thisDeptChargeLeader').val(deptLeader.leader1.account);
                $('#departChargeCEO td').text(deptLeader.leader1.realname);
            }
            $('#manager1').show()
        } else {
            $('#manager1 td').text("");
            $('#manager1').hide()
        }
    })
}

// 审批部门改变时，获取全部部门负责人 ,cm
function approvalDepartmentChange(e) {
    var approvalDepartment = $('#' + e.id).val()
// 申请部门的id
    var applyDepartment = $('#applyDepartment').val();
    if (approvalDepartment) {
        approvalDepartment.push(applyDepartment)
    } else {
        approvalDepartment = applyDepartment
    }

    $.post(createLink('authorityapply', 'ajaxGetManagerByUser'), {
        approvalDepartment: approvalDepartment,
        applyDepartment: applyDepartment
    }, function (res) {

        // res 为返回的分管领导值
        if (res != '') {
            var deptLeader = JSON.parse(res);
            $('#manager1 td').text(deptLeader.manager1.realname);
            $('#departChargeCM td').text(deptLeader.cm.realname);
            $('#thisDeptLeader').val(deptLeader.manager1.thisDeptLeader);
            $('#thatDeptLeader').val(deptLeader.manager1.thatDeptLeader);
            $('#cm').val(deptLeader.cm.account);
            $('#manager1').show()
        } else {
            $('#manager1 td').text("");
            $('#manager1').hide()
        }
    })

}

// 应用系统改变，联动项目下来列表改变
function applicationChange(e) {
    var application = $('#' + e.id).val()
    var html = '';
    $.post(createLink('authorityapply', 'ajaxGetProductByAppId'), {application: application}, function (res) {
        $('#product').empty();
        $('#product').trigger("chosen:updated")

        var app = JSON.parse(res);
        for (var key in app) {
            if (app[key]) {
                var option = document.createElement('option');
                option.value = key
                option.textContent = app[key]
                $('#product').append(option)
                $('#product').trigger("chosen:updated")
            }
        }
    })
}

// 申请内容中子系统改变时，对应不同系统的申请内容不同
function subSystemChange(e) {
    var subSystem = $(e).val()
    var a = $(e).data('id')
    // 移除当前行的校验提示
    $(e).parent().parent().find('.text-danger').remove()
    $(e).parent().parent().find('.has-error').removeClass('has-error')
    controlSub(subSystem, a)
    emptyPermissionContent(a)

}

function controlSub(subSystem, a) {
    if (subSystem == 'svn') {
        $('#svnPermissionContent' + a).show()
        $('#svnPermission' + a).show()
        $('#permissionContent' + a).hide()
        $('#gitLabPermissionContent' + a).hide()
        $('#gitLabPermission' + a).hide()
        $('#jenkinsPermissionContent' + a).hide()
        $('#jenkinsPermission' + a).hide()

    } else if (subSystem == 'gitlab') {
        $('#gitLabPermissionContent' + a).show()
        $('#gitLabPermission' + a).show()
        $('#permissionContent' + a).hide()
        $('#svnPermissionContent' + a).hide()
        $('#svnPermission' + a).hide()
        $('#jenkinsPermissionContent' + a).hide()
        $('#jenkinsPermission' + a).hide()

    } else if (subSystem == 'jenkins') {
        $('#jenkinsPermissionContent' + a).show()
        $('#jenkinsPermission' + a).show()
        $('#permissionContent' + a).hide()
        $('#svnPermissionContent' + a).hide()
        $('#svnPermission' + a).hide()
        $('#gitLabPermissionContent' + a).hide()
        $('#gitLabPermission' + a).hide()
    }else {
            $('#permissionContent' + a).show()
            $('#svnPermissionContent' + a).hide()
            $('#svnPermission' + a).hide()
            $('#gitLabPermissionContent' + a).hide()
            $('#gitLabPermission' + a).hide()
            $('#jenkinsPermissionContent' + a).hide()
            $('#jenkinsPermission' + a).hide()

    }
}
function controlRealSub(subSystem, a) {
    if (subSystem == 'dpmp') {
        $('#realZt' + a).show()
        $('#realZt' + a+'  .xm').css('display','block')
        $('#realSvn' + a).hide()
        $('#realGitLab' + a).hide()
        $('#realJenkins' + a).hide()
        $('#realOther' + a).hide()
    } else if (subSystem == 'svn') {
        $('#realZt' + a).hide()
        $('#realSvn' + a).show()
        $('#realSvn' + a+'  .xm').css('display','block')

        $('#realGitLab' + a).hide()
        $('#realJenkins' + a).hide()
        $('#realOther' + a).hide()
    } else if (subSystem == 'gitlab') {
        $('#realZt' + a).hide()
        $('#realSvn' + a).hide()
        $('#realGitLab' + a).show()
        $('#realGitLab' + a+'  .xm').css('display','block')
        $('#realJenkins' + a).hide()
        $('#realOther' + a).hide()

    } else if (subSystem == 'jenkins') {
        $('#realZt' + a).hide()
        $('#realSvn' + a).hide()
        $('#realGitLab' + a).hide()
        $('#realJenkins' + a).show()
        $('#realJenkins' + a+'  .xm').css('display','block')
        $('#realOther' + a).hide()
    } else {
        $('#realZt' + a).hide()
        $('#realSvn' + a).hide()
        $('#realGitLab' + a).hide()
        $('#realJenkins' + a).hide()
        $('#realOther' + a).show()
    }
}
function changeDealResult() {
    var result = $("input[name='dealResult']:checked").val();
    if (result == '2') {
        $('#suggest-td').addClass('required');
    } else {
        $('#suggest-td').addClass('required');

    }
}



