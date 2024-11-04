<?php
$lang->ldap->common = 'LDAP配置';
$lang->ldap->index  = '配置首页';
$lang->ldap->base   = '基本配置';
$lang->ldap->attr   = '属性配置';
$lang->ldap->other  = '其他配置';
$lang->ldap->import = '从LDAP导入用户';

$lang->ldap->basicConf      = 'LDAP-连接配置';
$lang->ldap->deptConf       = 'LDAP-部门配置';
$lang->ldap->noticeConf     = 'LDAP-邮箱通知';
$lang->ldap->syncHistory    = 'LDAP-同步记录';
$lang->ldap->turnon         = '功能状态';
$lang->ldap->type           = '服务器类型';
$lang->ldap->host           = 'LDAP服务器';
$lang->ldap->port           = '端口号';
$lang->ldap->version        = 'LDAP版本';
$lang->ldap->admin          = '管理账号';
$lang->ldap->password       = '账号密码';
$lang->ldap->baseDN         = 'Base DN(用户)';
$lang->ldap->account        = '登录名';
$lang->ldap->realname       = '真实姓名';
$lang->ldap->email          = 'Email字段';
$lang->ldap->phone          = '工作电话';
$lang->ldap->mobile         = '手机';
$lang->ldap->anonymous      = '匿名';
$lang->ldap->charset        = 'LDAP编码';
$lang->ldap->custom         = '自定义';
$lang->ldap->repeatPolicy   = '真实姓名重复';
$lang->ldap->defaultGroup   = '默认用户组';
$lang->ldap->autoCreate     = '自动创建用户';
$lang->ldap->dept           = '部门';
$lang->ldap->number         = '编号';
$lang->ldap->deptBaseDN     = 'Base DN(部门)';
$lang->ldap->zentaoDeptName = '禅道部门';
$lang->ldap->ldapDeptName   = 'LDAP部门';
$lang->ldap->sendUser       = '发信用户';
$lang->ldap->preview        = '预览';
$lang->ldap->mailContent    = '正文内容';
$lang->ldap->mailTitle      = '邮件标题';
$lang->ldap->id             = '序号';
$lang->ldap->syncAccount    = 'LDAP账号';
$lang->ldap->syncResult     = 'LDAP同步结果';
$lang->ldap->syncTime       = 'LDAP同步时间';
$lang->ldap->syncInterval   = '同步间隔时间';
$lang->ldap->minute         = '分钟';
$lang->ldap->employeeNumber = '员工编号';

$lang->ldap->example   = '例如：';
$lang->ldap->accountPS = 'LDAP服务器中对应个人用户名的字段';
$lang->ldap->groupPS   = 'LDAP用户登陆后的所处的分组';
$lang->ldap->deptBaseDnDesc = '该处的值用于获取LDAP部门数据，用于计算用户所属部门。';

$lang->ldap->successSave    = '成功保存';

$lang->ldap->error          = new stdclass();
$lang->ldap->error->connect = '不能连接LDAP服务器，可能LDAP的地址或端口错误！';
$lang->ldap->error->verify  = '管理账号的用户名或密码错误，或者LDAP版本选择错误';
$lang->ldap->error->noempty = '[%s]不能为空';

$lang->ldap->turnonList[0] = '关闭';
$lang->ldap->turnonList[1] = '开启';

$lang->ldap->versionList[3] = '3';
$lang->ldap->versionList[2] = '2';

$lang->ldap->typeList['ldap'] = "LDAP服务器";
$lang->ldap->typeList['ad']   = "活动目录";

$lang->ldap->repeatPolicyList['number'] = '加编号，例如 admin,admin2';
$lang->ldap->repeatPolicyList['dept']   = '加部门，例如 admin(研发)，admin(测试)';

$lang->ldap->autoCreateList[1] = '是';
$lang->ldap->autoCreateList[0] = '否';

$lang->ldap->noldap          = new stdclass();
$lang->ldap->noldap->header  = 'ERROR：没加载PHP的LDAP扩展';
$lang->ldap->noldap->content = '本配置依赖于PHP的LDAP扩展，需要加载LDAP扩展，你可以修改php.ini文件，或者可以安装LDAP扩展。具体安装可以参考该文档 <a href="https://www.zentao.net/help-read-79704.html" target="_blank">安装PHP的LDAP扩展</a> 。';

$lang->ldap->syncResultList               = array();
$lang->ldap->syncResultList['success']    = '定时同步新增用户成功。';
$lang->ldap->syncResultList['deptEmpty']  = '用户所属部门不在同步范围之内。';
$lang->ldap->syncResultList['illaccount'] = '用户名重复，不能添加！';
$lang->ldap->syncResultList['repeat']     = '用户名不合法，添加失败！';
