var selectObj='';
selectObj = renderSelect('svnPermissionContent', 1, svnAuthority)
selectObj.setValue([])
selectObj = renderSelect('gitLabPermissionContent', 1, gitlabAuthority)
selectObj.setValue([])
selectObj = renderSelect('jenkinsPermissionContent', 1, jenkinsAuthority)
selectObj.setValue([])

selectObj = renderSelect('svnPermission', 1,  JSON.parse(svnPermission),false,true,false,"hidden",false,true,true)
selectObj.setValue([])
selectObj = renderSelect('gitLabPermission', 1, JSON.parse(gitLabPermission),false,true,false,"hidden",false,true,true)
selectObj.setValue([])
selectObj = renderSelect('jenkinsPermission', 1, JSON.parse(jenkinsPermission),false,true,false,"hidden",false,true,true)
selectObj.setValue([])


for(var i = Object.keys(subSystemList).length-1;i>=0;i--){
    controlSub(Object.keys(subSystemList)[i], 1)
}
