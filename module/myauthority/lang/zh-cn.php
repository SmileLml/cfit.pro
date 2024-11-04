<?php
$lang->myauthority->common      = '我的权限';
$lang->myauthority->browse      = '我的权限列表';

$lang->myauthority->role        =  '所属分组';
$lang->myauthority->repository  =  '所属仓库';
$lang->myauthority->project     =  '所属项目';
$lang->myauthority->permsission =  '功能权限';
$lang->myauthority->roleDesc    =  '所属分组描述';
$lang->myauthority->create      = '权限申请';
$lang->myauthority->permsissionName    = '权限名称';
$lang->myauthority->applyPermsission   = '申请权限';
$lang->myauthority->expires            = '过期时间';
$lang->myauthority->JenkinsUrl         = '流水线URL';

$lang->myauthority->query       = '查询';
$lang->myauthority->reset       = '重置';
//子系统
$lang->myauthority->subSystem   = array();
$lang->myauthority->subSystem['dpmp']      = '研发过程';
$lang->myauthority->subSystem['gitlab']    = 'Gitlab';
$lang->myauthority->subSystem['jenkins']   = 'Jenkins';
$lang->myauthority->subSystem['svn']       = 'SVN';

//jenkins权限
$lang->myauthority->jenkinsAuthority   = array();
$lang->myauthority->jenkinsAuthority['com.cloudbees.plugins.credentials.CredentialsProvider.Create']      = '凭据-Create';
$lang->myauthority->jenkinsAuthority['com.cloudbees.plugins.credentials.CredentialsProvider.Delete']      = '凭据-Delete';
$lang->myauthority->jenkinsAuthority['com.cloudbees.plugins.credentials.CredentialsProvider.ManageDomains']      = '凭据-ManageDomains';
$lang->myauthority->jenkinsAuthority['com.cloudbees.plugins.credentials.CredentialsProvider.Update']      = '凭据-Update';
$lang->myauthority->jenkinsAuthority['com.cloudbees.plugins.credentials.CredentialsProvider.View']      = '凭据-View';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Build']      = 'Job-Build';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Cancel']      = 'Job-Cancel';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Configure']      = 'Job-Configure';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Create']      = 'Job-Create';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Delete']      = 'Job-Delete';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Discover']      = 'Job-Discover';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Move']      = 'Job-Move';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Read']      = 'Job-Read';
$lang->myauthority->jenkinsAuthority['hudson.model.Item.Workspace']      = 'Job-Workspace';
$lang->myauthority->jenkinsAuthority['hudson.model.Run.Delete']      = 'Run-Delete';
$lang->myauthority->jenkinsAuthority['hudson.model.Run.Replay']      = 'Run-Replay';
$lang->myauthority->jenkinsAuthority['hudson.model.Run.Update']      = 'Run-Update';
$lang->myauthority->jenkinsAuthority['hudson.model.View.Read']      = 'View-Read';
$lang->myauthority->jenkinsAuthority['hudson.model.View.Configure']      = 'View-Configure';
$lang->myauthority->jenkinsAuthority['hudson.model.View.Create']      = 'View-Create';

//gitlab权限
$lang->myauthority->gitlabAuthority   = array();
$lang->myauthority->gitlabAuthority['0']      = '无权限';
$lang->myauthority->gitlabAuthority['10']      = '访客';
$lang->myauthority->gitlabAuthority['20']      = '报告者';
$lang->myauthority->gitlabAuthority['30']      = '开发者';
$lang->myauthority->gitlabAuthority['40']      = '维护者';
$lang->myauthority->gitlabAuthority['50']      = '所有者';

$lang->myauthority->svnAuthority   = array();
$lang->myauthority->svnAuthority ['r']   = '只读';
$lang->myauthority->svnAuthority ['rw']  = '读写';