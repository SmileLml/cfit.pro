<?php
include '../../control.php';
class myApi extends api
{
    /**
     * 接口下载sftp文件(10m大内 无问题)
     */
    public function download()
    {
        parse_str($this->server->query_String, $queryString); //获取get参数
        $queryString['filename'] = urldecode($queryString['filename']);

        $remoteFile =  $queryString['filename'] ; //'/files/项目测试版本发布区/TEST/测试.txt';
        if(substr($remoteFile, 0, 7) !=='/files/'){
            $this->response('filename error 下载文件名错误', 701);
        }

//        $this->checkApiToken(); //校验token
        $this->loadModel('downloads');
        //校验签名
        if(isset($queryString['sign'])) { $queryString['sgin'] = $queryString['sign']; }
        if($queryString['sgin'] != 8888 && $queryString['sgin'] != $this->downloads->getSign($queryString['filename'])) {
            $this->response('sign error 签名错误', 702);
        }

        $downloadId = $this->downloads->saveDownload($remoteFile, $queryString['code']); //记录下载信息

        $local = $queryString['local'] ?? 0; //自己调试local=1 否则访问不到
        $this->downloads->downloadSftpFile($remoteFile, $downloadId, $local); //下载文件 2022-10-10
    }
}