<?php
$lang->projectdoc->common         = '项目文档库';
$lang->projectdoc->create         = '创建文档库';
$lang->projectdoc->edit           = '编辑文档库';
$lang->projectdoc->maintain       = '文档库列表';
$lang->projectdoc->browse         = '文档列表';
$lang->projectdoc->view           = '文档详情';
$lang->projectdoc->revision       = '文档版本';
$lang->projectdoc->showDoc        = '在线预览';
$lang->projectdoc->diff           = '比较差异';
$lang->projectdoc->delete         = '删除文档库';
$lang->projectdoc->showSyncCommit = '同步记录';

$lang->projectdoc->id           = '编号';
$lang->projectdoc->name         = '名称';
$lang->projectdoc->path         = '路径';
$lang->projectdoc->acl          = '权限';
$lang->projectdoc->group        = '分组';
$lang->projectdoc->user         = '用户';
$lang->projectdoc->desc         = '描述';
$lang->projectdoc->path         = '地址';
$lang->projectdoc->client       = '客户端';
$lang->projectdoc->revisionA    = '版本';
$lang->projectdoc->revisions    = '版本';
$lang->projectdoc->time         = '提交时间';
$lang->projectdoc->committer    = '作者';
$lang->projectdoc->comment      = '注释';
$lang->projectdoc->allLog       = '所有版本';
$lang->projectdoc->allChanges   = '其他改动';
$lang->projectdoc->blame        = '追溯';
$lang->projectdoc->download     = '下载';
$lang->projectdoc->fullscreen   = '全屏';
$lang->projectdoc->downloadDiff = '下载Diff';
$lang->projectdoc->changes      = '修改记录';
$lang->projectdoc->viewA        = '查看';
$lang->projectdoc->diffAB       = '比较';
$lang->projectdoc->diffAll      = '全部比较';
$lang->projectdoc->info         = '版本信息';
$lang->projectdoc->viewRevision = '查看修订';

$lang->projectdoc->encoding = '编码';
$lang->projectdoc->account  = '用户名';
$lang->projectdoc->password = '密码';

$lang->projectdoc->encodingList['utf_8'] = 'UTF-8';
$lang->projectdoc->encodingList['gbk']   = 'GBK';

$lang->projectdoc->encryptList['plain']  = '不加密';
$lang->projectdoc->encryptList['base64'] = 'BASE64';

$lang->projectdoc->viewDiffList['inline'] = '直列';
$lang->projectdoc->viewDiffList['appose'] = '并排';

$lang->projectdoc->example              = new stdclass();
$lang->projectdoc->example->client      = new stdclass();
$lang->projectdoc->example->path        = new stdclass();
$lang->projectdoc->example->client->svn = "例如：/usr/bin/svn";
$lang->projectdoc->example->path->svn   = "例如：http://example.googlecode.com/svn/trunk/myproject";
$lang->projectdoc->example->config      = "https需要填写配置目录的位置，通过config-dir选项生成配置目录";
$lang->projectdoc->example->encoding    = "填写版本库中文件的编码";

$lang->projectdoc->error = new stdclass();
$lang->projectdoc->error->useless       = '你的服务器禁用了exec,shell_exec方法，无法使用该功能';
$lang->projectdoc->error->connect       = '连接版本库失败，请填写正确的用户名、密码和版本库地址！';
$lang->projectdoc->error->version       = "https和svn协议需要1.8及以上版本的客户端，请升级到最新版本！详情访问:http://subversion.apache.org/";
$lang->projectdoc->error->path          = '版本库地址直接填写文件路径，如：/home/test。';
$lang->projectdoc->error->cmd           = '客户端错误！';
$lang->projectdoc->error->diff          = '必须选择两个版本';
$lang->projectdoc->error->safe          = '因为安全原因，需要检测客户端版本，请将版本号写入文件 %s <br /> 可以执行命令：%s';
$lang->projectdoc->error->commentText   = '请填写评审内容';
$lang->projectdoc->error->comment       = '请填写内容';
$lang->projectdoc->error->title         = '请填写标题';
$lang->projectdoc->error->accessDenied  = '你没有权限访问该版本库';
$lang->projectdoc->error->noFound       = '你访问的版本库不存在';
$lang->projectdoc->error->noFile        = '目录 %s 不存在';
$lang->projectdoc->error->noPriv        = '程序没有权限切换到目录 %s';
$lang->projectdoc->error->output        = "执行命令：%s\n错误结果(%s)： %s\n";
$lang->projectdoc->error->clientVersion = "客户端版本过低，请升级或更换SVN客户端";
$lang->projectdoc->error->encoding      = "编码可能错误，请更换编码重试。";
$lang->projectdoc->error->deleted       = "删除版本库失败，当前版本库有提交记录与设计关联";
$lang->projectdoc->error->clientPath    = "客户端安装目录不能有空格！";
$lang->projectdoc->error->onlyOne       = '一个项目只能创建一个文档库！';

$lang->projectdoc->notice                 = new stdclass();
$lang->projectdoc->notice->syncing        = '正在同步中, 请稍等...';
$lang->projectdoc->notice->syncComplete   = '同步完成，正在跳转...';
$lang->projectdoc->notice->syncedCount    = '已经同步记录条数';
$lang->projectdoc->notice->delete         = '是否要删除该版本库？';
$lang->projectdoc->notice->successDelete  = '已经成功删除版本库。';
$lang->projectdoc->notice->commentContent = '输入回复内容';
$lang->projectdoc->notice->deleteBug      = '确认删除该Bug？';
$lang->projectdoc->notice->deleteComment  = '确认删除该回复？';
$lang->projectdoc->notice->lastSyncTime   = '最后更新于：';

$lang->projectdoc->encodingsTips = "提交日志的编码，可以用逗号连接起来的多个，比如utf-8。";
$lang->projectdoc->confirmDelete = "确认删除文档库:%s？";
