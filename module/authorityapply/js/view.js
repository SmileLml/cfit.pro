$(document).ready(function () {
    if(content!==''){
        for (k in content) {
            controlSub(content[k]['subSystem'], k)

            const selectObj1 = renderSelect('svnPermissionContent', k, svnAuthority ,true)
            selectObj1.setValue(content[k]['svnPermissionContent'].split(','))

            const selectObj2 = renderSelect('gitLabPermissionContent', k, gitlabAuthority,true)
            selectObj2.setValue(content[k]['gitLabPermissionContent'].split(','))

            const selectObj3 = renderSelect('jenkinsPermissionContent', k, jenkinsAuthority,true)
            selectObj3.setValue(content[k]['jenkinsPermissionContent'].split(','))

            const selectObj4 = renderSelect('svnPermission', k, JSON.parse(svnPermission),true,true)
            selectObj4.setValue(content[k]['svnPermission'].split(','))

            const selectObj5 = renderSelect('gitLabPermission', k, JSON.parse(gitLabPermission),true,true)
            selectObj5.setValue(content[k]['gitLabPermission'].split(','))

            const selectObj6 = renderSelect('jenkinsPermission', k, JSON.parse(jenkinsPermission),true,true)
            selectObj6.setValue(content[k]['jenkinsPermission'].split(','))
        }
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
})
$('select').prop('disabled',true).trigger('chosen:updated')

function applyInfoBtnClick(){
    $('#applyInfoBtn').addClass('active1')
    $('#applyInfo').show()
    $('#historyRecord').hide()
    $('#flowImg').hide()
    $('#historyRecordBtn').removeClass('active1')
    $('#flowImgBtn').removeClass('active1')
}
function historyRecordBtnClick(){
    $('#historyRecordBtn').addClass('active1')
    $('#applyInfo').hide()
    $('#historyRecord').show()
    $('#flowImg').hide()
    $('#applyInfoBtn').removeClass('active1')
    $('#flowImgBtn').removeClass('active1')
}
function flowImgBtnClick(){
    $('#flowImgBtn').addClass('active1')
    $('#applyInfo').hide()
    $('#historyRecord').hide()
    $('#flowImg').show()
    $('#historyRecordBtn').removeClass('active1')
    $('#applyInfoBtn').removeClass('active1')
}