<?php
$lang->doclib->common         = '知识库';
$lang->doclib->create         = '创建知识库';
$lang->doclib->maintain       = '知识库列表';
$lang->doclib->edit           = '编辑知识库';
$lang->doclib->browse         = '文档列表';
$lang->doclib->view           = '文档详情';
$lang->doclib->revision       = '文档版本';
$lang->doclib->showDoc        = '在线预览';
$lang->doclib->diff           = '比较差异';
$lang->doclib->delete         = '删除知识库';
$lang->doclib->showSyncCommit = '同步记录';

$lang->doclib->id           = '编号';
$lang->doclib->name         = '名称';
$lang->doclib->path         = '路径';
$lang->doclib->acl          = '权限';
$lang->doclib->group        = '分组';
$lang->doclib->user         = '用户';
$lang->doclib->desc         = '描述';
$lang->doclib->path         = '地址';
$lang->doclib->client       = '客户端';
$lang->doclib->revisionA    = '版本';
$lang->doclib->revisions    = '版本';
$lang->doclib->time         = '提交时间';
$lang->doclib->committer    = '作者';
$lang->doclib->comment      = '注释';
$lang->doclib->allLog       = '所有版本';
$lang->doclib->allChanges   = '其他改动';
$lang->doclib->blame        = '追溯';
$lang->doclib->download     = '下载';
$lang->doclib->fullscreen   = '全屏';
$lang->doclib->downloadDiff = '下载Diff';
$lang->doclib->changes      = '修改记录';
$lang->doclib->viewA        = '查看';
$lang->doclib->diffAB       = '比较';
$lang->doclib->diffAll      = '全部比较';
$lang->doclib->info         = '版本信息';
$lang->doclib->viewRevision = '查看修订';

$lang->doclib->encoding = '编码';
$lang->doclib->account  = '用户名';
$lang->doclib->password = '密码';

$lang->doclib->encodingList['utf_8'] = 'UTF-8';
$lang->doclib->encodingList['gbk']   = 'GBK';

$lang->doclib->encryptList['plain']  = '不加密';
$lang->doclib->encryptList['base64'] = 'BASE64';

$lang->doclib->viewDiffList['inline'] = '直列';
$lang->doclib->viewDiffList['appose'] = '并排';

$lang->doclib->example              = new stdclass();
$lang->doclib->example->client      = new stdclass();
$lang->doclib->example->path        = new stdclass();
$lang->doclib->example->client->svn = "例如：/usr/bin/svn";
$lang->doclib->example->path->svn   = "例如：http://example.googlecode.com/svn/trunk/myproject";
$lang->doclib->example->config      = "https需要填写配置目录的位置，通过config-dir选项生成配置目录";
$lang->doclib->example->encoding    = "填写版本库中文件的编码";

$lang->doclib->error = new stdclass();
$lang->doclib->error->useless       = '你的服务器禁用了exec,shell_exec方法，无法使用该功能';
$lang->doclib->error->connect       = '连接版本库失败，请填写正确的用户名、密码和版本库地址！';
$lang->doclib->error->version       = "https和svn协议需要1.8及以上版本的客户端，请升级到最新版本！详情访问:http://subversion.apache.org/";
$lang->doclib->error->path          = '版本库地址直接填写文件路径，如：/home/test。';
$lang->doclib->error->cmd           = '客户端错误！';
$lang->doclib->error->diff          = '必须选择两个版本';
$lang->doclib->error->safe          = '因为安全原因，需要检测客户端版本，请将版本号写入文件 %s <br /> 可以执行命令：%s';
$lang->doclib->error->commentText   = '请填写评审内容';
$lang->doclib->error->comment       = '请填写内容';
$lang->doclib->error->title         = '请填写标题';
$lang->doclib->error->accessDenied  = '你没有权限访问该版本库';
$lang->doclib->error->noFound       = '你访问的版本库不存在';
$lang->doclib->error->noFile        = '目录 %s 不存在';
$lang->doclib->error->noPriv        = '程序没有权限切换到目录 %s';
$lang->doclib->error->output        = "执行命令：%s\n错误结果(%s)： %s\n";
$lang->doclib->error->clientVersion = "客户端版本过低，请升级或更换SVN客户端";
$lang->doclib->error->encoding      = "编码可能错误，请更换编码重试。";
$lang->doclib->error->deleted       = "删除版本库失败，当前版本库有提交记录与设计关联";
$lang->doclib->error->clientPath    = "客户端安装目录不能有空格！";

$lang->doclib->notice                 = new stdclass();
$lang->doclib->notice->syncing        = '正在同步中, 请稍等...';
$lang->doclib->notice->syncComplete   = '同步完成，正在跳转...';
$lang->doclib->notice->syncedCount    = '已经同步记录条数';
$lang->doclib->notice->delete         = '是否要删除该版本库？';
$lang->doclib->notice->successDelete  = '已经成功删除版本库。';
$lang->doclib->notice->commentContent = '输入回复内容';
$lang->doclib->notice->deleteBug      = '确认删除该Bug？';
$lang->doclib->notice->deleteComment  = '确认删除该回复？';
$lang->doclib->notice->lastSyncTime   = '最后更新于：';

$lang->doclib->encodingsTips = "提交日志的编码，可以用逗号连接起来的多个，比如utf-8。";
$lang->doclib->confirmDelete = "确认删除文档库:%s？";
