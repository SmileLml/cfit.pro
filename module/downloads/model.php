<?php
class downloadsModel extends model
{
    static $_STATUS_START = 'start';
    static $_STATUS_COPIED = 'copied';
    static $_STATUS_DOWNLOADED = 'downloaded';
    static $_HTTP_DOWNLOAD_REMOTE_IP = 'http://172.22.67.213';
    static $_HTTP_DOWNLOAD_LOCAL_IP  = 'http://10.128.28.213';
    /*
     * 下载记录
     */
    public function saveDownload($remoteFile, $code)
    {
        $downloadData['filename']       = $remoteFile;
        $downloadData['code']           = $code;
        $downloadData['createTime']     = date('Y-m-d H:i:s');
        $downloadData['expireTime']     = time() + (86400 * 3); //3天过期 已经改成定时清空文件了 这些都没有用了
        $downloadData['status']         = self::$_STATUS_START;
        $downloadData['fileDeleted']    = 0;
        $downloadData['downloadDeleted'] = 0;
        $downloadData['downloadTime']   = date('Y-m-d H:i:s');
        $this->dao->insert(TABLE_DOWNLOADS)->data($downloadData)->autoCheck()->exec();
        return $this->dao->lastInsertID();
    }

    /*
     * 获取下载记录
     */
    public function getDownload($fileName)
    {
        $download = $this->dao->select('*')->from(TABLE_DOWNLOADS)
            ->where('filename')->eq($fileName)
            ->andwhere('downloadDeleted')->eq(0)->orderBy('id_desc')
            ->fetch();
        return (array)$download;
    }

    /*
     * 更新下载记录
     */
    public function updateDownload($id, $downloadData)
    {
        $this->dao->update(TABLE_DOWNLOADS)->data($downloadData)->where('id')->eq($id)->autoCheck()->exec();

    }

    /*
     * 删除下载记录
     */
    public function deleteDownload($id, $exception = '')
    {
        $downloadData['downloadDeleted'] = 1;
        $downloadData['exception'] = $exception;
        $this->dao->update(TABLE_DOWNLOADS)->data($downloadData)->where('id')->eq($id)->autoCheck()->exec();
    }

    /*
     * 标记文件已传输到本地服务器
     */
    public function setCopied($id, $md5 ='')
    {
        $downloadData['status'] = self::$_STATUS_COPIED;
        $downloadData['md5'] = $md5;
        $this->dao->update(TABLE_DOWNLOADS)->data($downloadData)->where('id')->eq($id)->autoCheck()->exec();
    }

    /*
     * 标记文件已经被客户完成下载
     */
    public function setDownloaded($id)
    {
        $downloadData['status'] = self::$_STATUS_DOWNLOADED;
        $downloadData['fileDeleted'] = 1;
        $this->dao->update(TABLE_DOWNLOADS)->data($downloadData)->where('id')->eq($id)->autoCheck()->exec();
        $this->resetLock();
    }

    /*
     * kv锁
     */
    public function getLock()
    {
        $lock['value'] = 1;
        $lock['expireTime'] = time() + 120; //2分钟锁
        return $this->dao->update(TABLE_KV)->data($lock)->where('`key`')->eq('download')->andWhere('expireTime')->lt(time())->autoCheck()->exec();
    }

    /*
     * 重置kv锁
     */
    public function resetLock()
    {
        $lock['value'] = 0;
        $lock['expireTime'] = 0;
        $this->dao->update(TABLE_KV)->data($lock)->where('`key`')->eq('download')->autoCheck()->exec();
    }

    /**
     * 下载sftp文件
     * 自己调试fromLocal=1 否则访问不到
     */
    public function downloadSftpFile($remoteFile, $downloadId, $fromLocal = 0)
    {
        try{
            set_time_limit(0); //php 不超时
            $sftpConfig     = $this->lang->api->sftpList;
            $conn           = ssh2_connect($sftpConfig['host'], $sftpConfig['port']); //登陆远程服务器
            if(!ssh2_auth_password($conn, $sftpConfig['username'], $sftpConfig['password'])) { $this->response('sftp 连接失败', $downloadId, 'connection'); } //用户名密码验证
            $sftp           = ssh2_sftp($conn); //打开sftp
            $preDir         = 'zip/' . date('YmdH') . '/' . mt_rand(0, 999999); //已经有在下载的 新建随机保存目录
            $dir            = $_SERVER['DOCUMENT_ROOT'] . '/data/upload/'. $preDir; //测试路径  /var/www/zentaopms/www/data/upload/zip/20220914/123456/files...'
            $localRealFile  = $dir . $remoteFile;  //本地临时文件地址
            $remoteFileMd5   = $this->getMd5FileName($remoteFile); ///aaa.zip 转成 aaa.md5
            $localRealFileMd5   = $dir . $remoteFileMd5;    //本地临时文件地址MD5
            $targetPath         = dirname($localRealFile); //创建保存目录
            if(!is_dir($targetPath)) mkdir($targetPath, 0777, true);
            //下载sftp服务器上MD5文件
            if(is_file($localRealFileMd5)) { unlink($localRealFileMd5); } //保证每次最新
            $resource = "ssh2.sftp://{$sftp}" . $remoteFileMd5;   //远程文件地址md5
            if (!file_exists($resource)) {
                $remoteFileMd5   = $this->getMd5OrgFileName($remoteFile); ///aaa.zip 转成 md5.org
                $localRealFileMd5   = $dir . $remoteFileMd5;    //本地临时文件地址MD5
                $targetPath         = dirname($localRealFile); //创建保存目录
                if(!is_dir($targetPath)) mkdir($targetPath, 0777, true);
                //下载sftp服务器上MD5文件
                if(is_file($localRealFileMd5)) { unlink($localRealFileMd5); } //保证每次最新
                $resource = "ssh2.sftp://{$sftp}" . $remoteFileMd5;   //远程文件地址md5
                if(!file_exists($resource)){
                    $this->response('md5文件不存在', $downloadId, 'no md5');
                }
            } //检查下载文件是否存在
            copy($resource, $localRealFileMd5);   //将远程md5文件复制到本地
            $md5 = $this->getFileMd5($localRealFileMd5); //读取sftp服务器上MD5文件内容
            $resource = "ssh2.sftp://{$sftp}" . $remoteFile;   //远程文件地址
            if (!file_exists($resource)) {
                $this->response('文件不存在', $downloadId, 'no zip');
            } //检查下载文件是否存在
            $this->setCopied($downloadId, $md5); //记录下载文件的MD5
            global $config;
            $sftpServerIP = $config->global->sftpServerIP ?? self::$_HTTP_DOWNLOAD_REMOTE_IP; //如有配置取配置 没有取默认
            if($fromLocal) $sftpServerIP =  self::$_HTTP_DOWNLOAD_LOCAL_IP;
            header('Location:'.$sftpServerIP.$remoteFile); //介质服务器http下载地址 2022-10-10

            exit();
        }
        catch (Exception $exception)
        {
            $this->updateDownload($downloadId, ['exception'=>$exception->getMessage()]);
        }
    }

    //读MD5文件中的MD5值
    public function getFileMd5($filename)
    {
        $info = file_get_contents($filename);
        $md5 = substr($info, 0,32);
        unlink($filename);
        return $md5;
    }

    //把后缀变成MD5
    public function getMd5FileName($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'md5';
    }

    //把文件名变成md5.org
    public function getMd5OrgFileName($filename)
    {
        $arr = explode('/', $filename);
        $arr[sizeof($arr)-1]='md5.org';
        return rtrim(implode('/',$arr),'/');
    }

    //测试用
    public function testTime()
    {
        $lock['value'] = date('Y-m-d H:i:s');
        $lock['expireTime'] = 0;
        $this->dao->update(TABLE_KV)->data($lock)->where('`key`')->eq('testTime')->autoCheck()->exec();
        return $lock['value'];
    }

    //文件签名
    public function getSign($filename)
    {
        return crc32(mb_substr($filename, -5));
    }

    /*
     * 清空无效任务
     */
    public function cleanTasks()
    {
        $list = $this->dao->select()->from(TABLE_DOWNLOADS)->where('`status`')->ne('downloaded')->andWhere('expireTime')->lt(time())->andWhere('downloadDeleted')->eq(0)->fetchAll();
        foreach ($list as $item)
        {
            @unlink($_SERVER['DOCUMENT_ROOT'] . '/data/upload/' . $item->filename);
            $downloadData['downloadDeleted'] = 1;
            $downloadData['fileDeleted'] = 1;
            $this->dao->update(TABLE_DOWNLOADS)->data($downloadData)->where('id')->eq($item->id)->autoCheck()->exec();
        }
    }

    /**
     * Response.
     *
     * @param  string $code
     * @access public
     * @return void
     */
    public function response($code, $id, $err = '')
    {
        $this->deleteDownload($id, $code); //取消下载任务
        $this->resetLock();  //重置下载锁
        $response = new stdclass();
        $response->errcode = $code;
        header('HTTP/1.1 704 sftp error '. $err) ;
        die(helper::jsonEncode($response));
    }
}