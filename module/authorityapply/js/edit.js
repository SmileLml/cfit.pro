$(document).ready(function () {
    var selectObj={}
    for (k in content) {
        controlSub(content[k]['subSystem'], k)

        selectObj = renderSelect('svnPermissionContent', k, svnAuthority)
        selectObj.setValue(content[k]['svnPermissionContent'].split(','))

        selectObj = renderSelect('gitLabPermissionContent', k, gitlabAuthority)
        selectObj.setValue(content[k]['gitLabPermissionContent'].split(','))

        selectObj = renderSelect('jenkinsPermissionContent', k, jenkinsAuthority)
        selectObj.setValue(content[k]['jenkinsPermissionContent'].split(','))

        selectObj = renderSelect('svnPermission', k, JSON.parse(svnPermission),false,true,false,"hidden",false,true)
        selectObj.setValue(content[k]['svnPermission'].split(','))

        selectObj = renderSelect('gitLabPermission', k, JSON.parse(gitLabPermission),false,true,false,"hidden",false,true)
        selectObj.setValue(content[k]['gitLabPermission'].split(','))

        selectObj = renderSelect('jenkinsPermission', k, JSON.parse(jenkinsPermission),false,true,false,"hidden",false,true)
        selectObj.setValue(content[k]['jenkinsPermission'].split(','))

    }
})
