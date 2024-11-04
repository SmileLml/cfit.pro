<?php
class doclibModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: checkPriv
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:19
     * Desc: This is the code comment. This method is called checkPriv.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $lib
     * @return bool
     */
    public function checkPriv($lib)
    {
        $account = $this->app->user->account;
        if(strpos(",{$this->app->company->admins},", ",$account,") !== false) return true;
        if(empty($lib->acl->groups) and empty($lib->acl->users)) return true;
        if(!empty($lib->acl->groups))
        {
            foreach($this->app->user->groups as $group)
            {
                if(in_array($group, $lib->acl->groups)) return true;
            }
        }
        if(!empty($lib->acl->users) and in_array($account, $lib->acl->users)) return true;
        return false;
    }

    /**
     * Project: chengfangjinke
     * Method: setMenu
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:20
     * Desc: This is the code comment. This method is called setMenu.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libs
     * @param string $libID
     */
    public function setMenu($libs, $libID = '')
    {    
        if(empty($libs)) $this->lang->switcherMenu = '';
        if(empty($libID)) $libID = $this->session->libID ? $this->session->libID : key($libs);
        if(!isset($libs[$libID])) $libID = key($libs);

        /* Check the privilege. */
        if($libID)
        {    
            $doclib = $this->getLibByID($libID);
            if(empty($doclib))
            {    
                echo(js::alert($this->lang->doclib->error->noFound));
                die(js::locate('back'));
            }    

            if(!$this->checkPriv($doclib))
            {    
                echo(js::alert($this->lang->doclib->error->accessDenied));
                die(js::locate('back'));
            }    
        }    

        common::setMenuVars('doclib', $libID);
        session_start();
        $this->session->set('libID', $libID);
        session_write_close();
    }

    /**
     * Project: chengfangjinke
     * Method: getLibList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:20
     * Desc: This is the code comment. This method is called getLibList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function getLibList($orderBy, $pager)
    {
        $libs = $this->dao->select('*')->from(TABLE_DOCUMENT)
            ->where('deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)->fetchAll('id');

        foreach($libs as $i => $lib)
        {   
            $lib->acl = json_decode($lib->acl);
            if(!$this->checkPriv($lib))
            {   
                unset($libs[$i]);
            }
        }

        return $libs;
    }

    /**
     * Project: chengfangjinke
     * Method: getLibByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:20
     * Desc: This is the code comment. This method is called getLibByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @return false
     */
    public function getLibByID($libID)
    {
        $doclib = $this->dao->select('*')->from(TABLE_DOCUMENT)->where('id')->eq($libID)->fetch();
        if(!$doclib) return false;

        if($doclib->encrypt == 'base64') $doclib->password = base64_decode($doclib->password);
        $doclib->acl = json_decode($doclib->acl);
        return $doclib;
    }

    /**
     * Project: chengfangjinke
     * Method: getHistory
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:20
     * Desc: This is the code comment. This method is called getHistory.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $revisions
     * @return mixed
     */
    public function getHistory($libID, $revisions)
    {
        return $this->dao->select('DISTINCT t1.*')->from(TABLE_DOCHISTORY)->alias('t1')
            ->leftJoin(TABLE_DOCBRANCH)->alias('t2')->on('t1.id=t2.revision')
            ->where('t1.lib')->eq($libID)
            ->andWhere('t1.revision')->in($revisions)
            ->fetchAll('revision');
    }

    /**
     * Project: chengfangjinke
     * Method: getCommits
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:20
     * Desc: This is the code comment. This method is called getCommits.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $doclib
     * @param $entry
     * @param string $revision
     * @param string $type
     * @param null $pager
     * @param int $begin
     * @param int $end
     * @return mixed
     */
    public function getCommits($doclib, $entry, $revision = 'HEAD', $type = 'dir', $pager = null, $begin = 0, $end = 0)
    {
        $entry = ltrim($entry, '/');
        $entry = $doclib->prefix . (empty($entry) ? '' : '/' . $entry);

        $libID = $doclib->id;
        $revisionTime = $this->dao->select('time')->from(TABLE_DOCHISTORY)->alias('t1')
            ->leftJoin(TABLE_DOCBRANCH)->alias('t2')->on('t1.id=t2.revision')
            ->where('t1.lib')->eq($libID)
            ->beginIF($revision != 'HEAD')->andWhere('t1.revision')->eq($revision)->fi()
            ->orderBy('time desc')
            ->limit(1)
            ->fetch('time');

        $historyIdList = array();
        if($entry != '/' and !empty($entry))
        {
            $historyIdList = $this->dao->select('DISTINCT t2.id')->from(TABLE_DOCFILES)->alias('t1')
                ->leftJoin(TABLE_DOCHISTORY)->alias('t2')->on('t1.revision=t2.id')
                ->leftJoin(TABLE_DOCBRANCH)->alias('t3')->on('t2.id=t3.revision')
                ->where('1=1')
                ->andWhere('t1.lib')->eq($libID)
                ->andWhere('t2.`time`')->le($revisionTime)
                ->andWhere('left(t2.comment, 12)')->ne('Merge branch')
                ->beginIF($type == 'dir')
                ->andWhere('t1.parent', true)->like(rtrim($entry, '/') . "/%")
                ->orWhere('t1.parent')->eq(rtrim($entry, '/'))
                ->markRight(1)
                ->fi()
                ->beginIF($type == 'file')->andWhere('t1.path')->eq("$entry")->fi()
                ->orderBy('t2.`time` desc')
                ->page($pager, 't2.id')
                ->fetchPairs('id', 'id');
        }

        $comments = $this->dao->select('DISTINCT t1.*')->from(TABLE_DOCHISTORY)->alias('t1')
            ->leftJoin(TABLE_DOCBRANCH)->alias('t2')->on('t1.id=t2.revision')
            ->where('t1.lib')->eq($libID)
            ->andWhere('t1.`time`')->le($revisionTime)
            ->andWhere('left(t1.comment, 12)')->ne('Merge branch')
            ->beginIF($entry != '/' and !empty($entry))->andWhere('t1.id')->in($historyIdList)->fi()
            ->beginIF($begin)->andWhere('t1.time')->ge($begin)->fi()
            ->beginIF($end)->andWhere('t1.time')->le($end)->fi()
            ->orderBy('time desc');
        if($entry == '/' or empty($entry))$comments->page($pager, 't1.id');
        $comments = $comments->fetchAll('revision');

        return $comments;
    }

    /**
     * Project: chengfangjinke
     * Method: getLibPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:20
     * Desc: This is the code comment. This method is called getLibPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return array
     */
    public function getLibPairs()
    {
        $libs = $this->dao->select('*')->from(TABLE_DOCUMENT)
            ->where('deleted')->eq(0)
            ->fetchAll();

        $pairs = array();
        foreach($libs as $lib)
        {
            $lib->acl = json_decode($lib->acl);
            if($this->checkPriv($lib))
            {
                $pairs[$lib->id] = "[svn] " . $lib->name;
            }
        }

        return $pairs;
    }

    /**
     * Project: chengfangjinke
     * Method: getLibPathPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:21
     * Desc: This is the code comment. This method is called getLibPathPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getLibPathPairs()
    {
        $pairs = $this->dao->select('id,path')->from(TABLE_DOCUMENT)
            ->where('deleted')->eq(0)
            ->fetchPairs();

        return $pairs;
    }

    /**
     * Project: chengfangjinke
     * Method: saveState
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:21
     * Desc: This is the code comment. This method is called saveState.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $libID
     * @return mixed
     */
    public function saveState($libID = 0)
    {
        if($libID > 0) $this->session->set('libID', (int)$libID);

        $libs = $this->getLibPairs();
        if($libID == 0 and $this->session->libID == '')
        {
            $this->session->set('libID', key($libs));
        }

        if(!isset($libs[$this->session->libID]))
        {
            $this->session->set('libID', key($libs));
        }

        return $this->session->libID;
    }

    /**
     * Project: chengfangjinke
     * Method: encodePath
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:21
     * Desc: This is the code comment. This method is called encodePath.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $path
     * @return mixed|string
     */
    public function encodePath($path = '')
    {
        if(empty($path)) return $path;
        return helper::safe64Encode(urlencode($path));
    }

    /**
     * Project: chengfangjinke
     * Method: decodePath
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:21
     * Desc: This is the code comment. This method is called decodePath.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $path
     * @return mixed|string
     */
    public function decodePath($path = '')
    {
        if(empty($path)) return $path;
        return trim(urldecode(helper::safe64Decode($path)), '/');
    }

    /**
     * Project: chengfangjinke
     * Method: getCacheFile
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:21
     * Desc: This is the code comment. This method is called getCacheFile.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $path
     * @param $revision
     * @return false|string
     */
    public function getCacheFile($libID, $path, $revision)
    {
        $cachePath = $this->app->getCacheRoot() . '/' . 'doclib';
        if(!is_dir($cachePath)) mkdir($cachePath, 0777, true);
        if(!is_writable($cachePath)) return false;
        return $cachePath . '/' . $libID . '-' . md5("{$this->cookie->doclibBranch}-$path-$revision");
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return false
     */
    public function create()
    {
        if(!$this->checkClient()) return false;
        if(!$this->checkConnection()) return false;

        $data = fixer::input('post')
            ->add('SCM', 'Subversion')
            ->skipSpecial('path,client,account,password')
            ->get();

        $data->acl = empty($data->acl) ? '' : json_encode($data->acl);

        $scm = $this->app->loadClass('scm');
        $scm->setEngine($data);
        $info = $scm->info('');
        $data->prefix = empty($info->root) ? '' : trim(str_ireplace($info->root, '', str_replace('\\', '/', $data->path)), '/');
        if($data->prefix) $data->prefix = '/' . $data->prefix;

        if($data->encrypt == 'base64') $data->password = base64_encode($data->password);
        $this->dao->insert(TABLE_DOCUMENT)->data($data)
            ->batchCheck($this->config->doclib->create->requiredFields, 'notempty')
            ->check($this->config->doclib->svn->requiredFields, 'notempty')
            ->autoCheck()
            ->exec();

        if(!dao::isError()) $this->rmClientVersionFile();

        return $this->dao->lastInsertID();
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $id
     * @return bool
     */
    public function update($id)
    {
        $lib = $this->getLibByID($id);

        $data = fixer::input('post')
            ->setDefault('SCM', 'Subversion')
            ->setDefault('client', 'svn')
            ->setDefault('prefix', $lib->prefix)
            ->setIF($this->post->path != $lib->path, 'synced', 0)
            ->skipSpecial('path,client,account,password')
            ->get();

        $data->acl = empty($data->acl) ? '' : json_encode($data->acl);

        $scm = $this->app->loadClass('scm');
        $scm->setEngine($data);
        $info = $scm->info('');
        $data->prefix = empty($info->root) ? '' : trim(str_ireplace($info->root, '', str_replace('\\', '/', $data->path)), '/');
        if($data->prefix) $data->prefix = '/' . $data->prefix;

        if($data->client != $lib->client and !$this->checkClient()) return false;
        if(!$this->checkConnection()) return false;

        if($data->encrypt == 'base64') $data->password = base64_encode($data->password);
        $this->dao->update(TABLE_DOCUMENT)->data($data)
            ->batchCheck($this->config->doclib->edit->requiredFields, 'notempty')
            ->check($this->config->doclib->svn->requiredFields, 'notempty')
            ->autoCheck()
            ->where('id')->eq($id)->exec();

        $this->rmClientVersionFile();

        if($lib->path != $data->path)
        {
            $this->dao->delete()->from(TABLE_DOCHISTORY)->where('lib')->eq($id)->exec();
            $this->dao->delete()->from(TABLE_DOCFILES)->where('lib')->eq($id)->exec();
            return false;
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: checkConnection
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called checkConnection.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return bool
     */
    public function checkConnection()
    {
        if(empty($_POST)) return false;

        $client   = $this->post->client;
        $account  = $this->post->account;
        $password = $this->post->password;
        $encoding = strtoupper($this->post->encoding);
        $path     = $this->post->path;
        if($encoding != 'UTF8' and $encoding != 'UTF-8') $path = helper::convertEncoding($path, 'utf-8', $encoding);

        /* Get svn version. */
        $versionCommand = "$client --version --quiet 2>&1";
        exec($versionCommand, $versionOutput, $versionResult);
        if($versionResult)
        {
            $message = sprintf($this->lang->doclib->error->output, $versionCommand, $versionResult, join("<br />", $versionOutput));
            dao::$errors['client'] = $this->lang->doclib->error->cmd . "<br />" . nl2br($message);
            return false;
        }
        $svnVersion = end($versionOutput);

        $path = '"' . str_replace(array('%3A', '%2F', '+'), array(':', '/', ' '), urlencode($path)) . '"';
        if(stripos($path, 'https://') === 1 or stripos($path, 'svn://') === 1)
        {
            if(version_compare($svnVersion, '1.6', '<'))
            {
                dao::$errors['client'] = $this->lang->doclib->error->version;
                return false;
            }

            $command = "$client info --username $account --password $password --non-interactive --trust-server-cert-failures=cn-mismatch --trust-server-cert --no-auth-cache $path 2>&1";
            if(version_compare($svnVersion, '1.9', '<')) $command = "$client info --username $account --password $password --non-interactive --trust-server-cert --no-auth-cache $path 2>&1";
        }
        else if(stripos($path, 'file://') === 1)
        {
            $command = "$client info --non-interactive --no-auth-cache $path 2>&1";
        }
        else
        {
            $command = "$client info --username $account --password $password --non-interactive --no-auth-cache $path 2>&1";
        }

        exec($command, $output, $result);
        if($result)
        {
            $message = sprintf($this->lang->doclib->error->output, $command, $result, join("<br />", $output));
            if(stripos($message, 'Expected FS format between') !== false and strpos($message, 'found format') !== false)
            {
                dao::$errors['client'] = $this->lang->doclib->error->clientVersion;
                return false;
            }
            if(preg_match('/[^\:\/\\A-Za-z0-9_\-\'\"\.]/', $path))
            {
                dao::$errors['encoding'] = $this->lang->doclib->error->encoding . "<br />" . nl2br($message);
                return false;
            }

            dao::$errors['submit'] = $this->lang->doclib->error->connect . "<br>" . nl2br($message);
            return false;
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: rmClientVersionFile
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called rmClientVersionFile.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function rmClientVersionFile()
    {
        $clientVersionFile = $this->session->clientVersionFile;
        if($clientVersionFile)
        {
            session_start();
            $this->session->set('clientVersionFile', $clientVersionFile);
            session_write_close();

            if(file_exists($clientVersionFile)) unlink($clientVersionFile);
        }
    }

    /**
     * Project: chengfangjinke
     * Method: checkClient
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called checkClient.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return bool
     */
    public function checkClient()
    {
        if(!$this->post->client) return true;

        if(strpos($this->post->client, ' '))
        {
            dao::$errors['client'] = $this->lang->doclib->error->clientPath;
            return false;
        }

        $clientVersionFile = $this->session->clientVersionFile;
        if(empty($clientVersionFile))
        {
            $clientVersionFile = $this->app->getLogRoot() . uniqid('version_') . '.log';

            session_start();
            $this->session->set('clientVersionFile', $clientVersionFile);
            session_write_close();
        }

        if(file_exists($clientVersionFile)) return true;

        $cmd = $this->post->client . " --version > $clientVersionFile";
        dao::$errors['client'] = sprintf($this->lang->doclib->error->safe, $clientVersionFile, $cmd);

        return false;
    }

    /**
     * Project: chengfangjinke
     * Method: getLatestCommit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called getLatestCommit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @return null
     */
    public function getLatestCommit($libID)
    {    
        $lastComment = $this->dao->select('t1.*')->from(TABLE_DOCHISTORY)->alias('t1')
            ->leftJoin(TABLE_DOCBRANCH)->alias('t2')->on('t1.id=t2.revision')
            ->where('t1.lib')->eq($libID)
            ->orderBy('t1.time desc')
            ->limit(1)
            ->fetch();
        if(empty($lastComment)) return null;

        return $lastComment;
    }

    /**
     * Project: chengfangjinke
     * Method: saveCommit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called saveCommit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $logs
     * @param $version
     * @param string $branch
     * @return int
     */
    public function saveCommit($libID, $logs, $version, $branch = '')
    {
        $count = 0;
        if(empty($logs)) return $count;

        foreach($logs['commits'] as $i => $commit)
        {
            $existsRevision  = $this->dao->select('id,revision')->from(TABLE_DOCHISTORY)->where('lib')->eq($libID)->andWhere('revision')->eq($commit->revision)->fetch();
            if($existsRevision)
            {
                if($branch) $this->dao->replace(TABLE_DOCBRANCH)->set('lib')->eq($libID)->set('revision')->eq($existsRevision->id)->set('branch')->eq($branch)->exec();
                continue;
            }

            $commit->lib     = $libID;
            $commit->commit  = $version;
            $commit->comment = htmlspecialchars($commit->comment);
            $this->dao->insert(TABLE_DOCHISTORY)->data($commit)->exec();
            if(!dao::isError())
            {
                $commitID = $this->dao->lastInsertID();
                if($branch) $this->dao->replace(TABLE_DOCBRANCH)->set('lib')->eq($libID)->set('revision')->eq($commitID)->set('branch')->eq($branch)->exec();
                foreach($logs['files'][$i] as $file)
                {
                    $parentPath = dirname($file->path);

                    $file->parent   = $parentPath == '\\' ? '/' : $parentPath;
                    $file->revision = $commitID;
                    $file->lib      = $libID;
                    $this->dao->insert(TABLE_DOCFILES)->data($file)->exec();
                }
                $revisionPairs[$commit->revision] = $commit->revision;
                $version++;
                $count++;
            }
            else
            {
                dao::getError();
            }
        }
        return $count;
    }

    /**
     * Project: chengfangjinke
     * Method: createLink
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called createLink.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $method
     * @param string $params
     * @param string $viewType
     * @param false $onlybody
     * @return string
     */
    public function createLink($method, $params = '', $viewType = '', $onlybody = false)
    {    
        if($this->config->requestType == 'GET') return helper::createLink('doclib', $method, $params, $viewType, $onlybody);

        $parsedParams = array();
        parse_str($params, $parsedParams);

        $pathParams = '';
        $pathKey    = 'path';
        if(isset($parsedParams['entry'])) $pathKey = 'entry';
        if(isset($parsedParams['file']))  $pathKey = 'file';
        if(isset($parsedParams['root']))  $pathKey = 'root';
        if(isset($parsedParams[$pathKey]))
        {
            $pathParams = 'doclibPath=' . $parsedParams[$pathKey];
            $parsedParams[$pathKey] = $parsedParams[$pathKey];
        }

        $params = http_build_query($parsedParams);
        $link   = helper::createLink('doclib', $method, $params, $viewType, $onlybody);
        if(empty($pathParams)) return $link;

        $link .= strpos($link, '?') === false ? '?' : '&'; 
        $link .= $pathParams;
        return $link;
    }

    /**
     * Project: chengfangjinke
     * Method: markSynced
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called markSynced.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     */
    public function markSynced($libID)
    {    
        $this->fixCommit($libID);
        $this->dao->update(TABLE_DOCUMENT)->set('synced')->eq(1)->where('id')->eq($libID)->exec();
    }

    /**
     * Project: chengfangjinke
     * Method: fixCommit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:22
     * Desc: This is the code comment. This method is called fixCommit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     */
    public function fixCommit($libID)
    {    
        $stmt = $this->dao->select('DISTINCT t1.id')->from(TABLE_DOCHISTORY)->alias('t1')
            ->leftJoin(TABLE_DOCBRANCH)->alias('t2')->on('t1.id=t2.revision')
            ->where('t1.lib')->eq($libID)
            ->orderBy('time')
            ->query();

        $i = 1;
        while($docHistory = $stmt->fetch())
        {
            $this->dao->update(TABLE_DOCHISTORY)->set('`commit`')->eq($i)->where('id')->eq($docHistory->id)->exec();
            $i++;
        }
    }

    /**
     * Project: chengfangjinke
     * Method: updateCommit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:25
     * Desc: This is the code comment. This method is called updateCommit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $doclib
     * @return false|void
     */
    public function updateCommit($doclib)
    {
        if(!$this->loadModel('svn')->setRepo($doclib)) return false;

        /* Print log and get lastInDB. */
        $lastInDB = $this->getLatestCommit($doclib->id);

        /* Ignore unsynced doclib. */
        if(empty($lastInDB)) return false;

        $version = $lastInDB->commit;
        $logs    = $this->getUnsyncCommits($doclib);

        /* Update code commit history. */
        $objects = array();
        if(!empty($logs))
        {
            foreach($logs as $log)
            {
                $version = $this->saveOneCommit($doclib->id, $log, $version);
            }
            $this->updateCommitCount($doclib->id, $lastInDB->commit + count($logs));
            $this->dao->update(TABLE_DOCUMENT)->set('lastSync')->eq(helper::now())->where('id')->eq($doclib->id)->exec();
        }
    }

    /**
     * Project: chengfangjinke
     * Method: getUnsyncCommits
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:25
     * Desc: This is the code comment. This method is called getUnsyncCommits.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $doclib
     * @return array|false
     */
    public function getUnsyncCommits($doclib)
    {
        $libID = $doclib->id;
        $lastInDB = $this->getLatestCommit($libID);

        $scm = $this->app->loadClass('scm');
        $scm->setEngine($doclib);

        $logs = $scm->log('', $lastInDB ? $lastInDB->revision : 0);
        if(empty($logs)) return false;

        /* Process logs. */
        $logs = array_reverse($logs, true);
        foreach($logs as $i => $log)
        {
            if($lastInDB->revision == $log->revision)
            {
                unset($logs[$i]);
                continue;
            }

            $log->author = $log->committer;
            $log->msg    = $log->comment;
            $log->date   = $log->time;

            /* Process files. */
            $log->files = array();
            foreach($log->change as $file => $info) $log->files[$info['action']][] = $file;
        }
        return $logs;
    }

    /**
     * Project: chengfangjinke
     * Method: saveOneCommit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:25
     * Desc: This is the code comment. This method is called saveOneCommit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $commit
     * @param $version
     * @param string $branch
     * @return bool|int|mixed
     */
    public function saveOneCommit($libID, $commit, $version, $branch = '')
    {
        $existsRevision  = $this->dao->select('id,revision')->from(TABLE_DOCHISTORY)->where('lib')->eq($libID)->andWhere('revision')->eq($commit->revision)->fetch();
        if($existsRevision) return true;

        $history = new stdclass();
        $history->lib       = $libID;
        $history->revision  = $commit->revision;
        $history->committer = $commit->committer;
        $history->time      = $commit->time;
        $history->commit    = $version;
        $history->comment   = htmlspecialchars($commit->comment);
        $this->dao->insert(TABLE_DOCHISTORY)->data($history)->exec();
        if(!dao::isError())
        {
            $commitID = $this->dao->lastInsertID();
            foreach($commit->change as $file => $info)
            {
                $parentPath = dirname($file);

                $doclibFile = new stdclass();
                $doclibFile->lib      = $libID;
                $doclibFile->revision = $commitID;
                $doclibFile->path     = $file;
                $doclibFile->parent   = $parentPath == '\\' ? '/' : $parentPath;
                $doclibFile->type     = $info['kind'];
                $doclibFile->action   = $info['action'];
                $this->dao->insert(TABLE_DOCFILES)->data($doclibFile)->exec();
            }
            $version++;
        }
        else
        {
            dao::getError();
        }

        return $version;
    }

    /**
     * Project: chengfangjinke
     * Method: updateCommitCount
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called updateCommitCount.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $count
     * @return mixed
     */
    public function updateCommitCount($libID, $count)
    {    
        return $this->dao->update(TABLE_DOCUMENT)->set('commits')->eq($count)->where('id')->eq($libID)->exec();
    }

    /**
     *
     * Project: chengfangjinke
     * Method: getPreAndNext
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called getPreAndNext.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $lib
     * @param $entry
     * @param string $revision
     * @param string $fileType
     * @param string $method
     * @return stdclass
     */
    public function getPreAndNext($lib, $entry, $revision = 'HEAD', $fileType = 'dir', $method = 'view')
    {
        $entry = ltrim($entry, '/');
        $entry = $lib->prefix . '/' . $entry;
        $libID = $lib->id;

        if($method == 'view')
        {
            $revisions = $this->dao->select('DISTINCT t1.revision,t1.commit')->from(TABLE_DOCHISTORY)->alias('t1')
                ->leftJoin(TABLE_DOCFILES)->alias('t2')->on('t1.id=t2.revision')
                ->leftJoin(TABLE_DOCBRANCH)->alias('t3')->on('t1.id=t3.revision')
                ->where('t1.lib')->eq($libID)
                ->andWhere('t2.path')->eq("$entry")
                ->orderBy('commit desc')
                ->fetchPairs();
        }
        else
        {
            $revisions = $this->dao->select('DISTINCT t1.revision,t1.commit')->from(TABLE_DOCHISTORY)->alias('t1')
                ->leftJoin(TABLE_DOCFILES)->alias('t2')->on('t1.id=t2.revision')
                ->leftJoin(TABLE_DOCBRANCH)->alias('t3')->on('t1.id=t3.revision')
                ->where('t1.lib')->eq($libID)
                ->beginIF($entry == '/')->andWhere('t2.revision = t1.id')->fi()
                ->beginIF($fileType == 'dir' && $entry != '/')
                ->andWhere('t2.parent', true)->like(rtrim($entry, '/') . "/%")
                ->orWhere('t2.parent')->eq(rtrim($entry, '/'))
                ->markRight(1)
                ->fi()
                ->beginIF($fileType == 'file' && $entry != '/')->andWhere('t2.path')->eq($entry)->fi()
                ->orderBy('commit desc')
                ->fetchPairs();
        }

        $preRevision  = false;
        $preAndNext   = new stdclass();
        $preAndNext->pre  = '';
        $preAndNext->next = '';
        foreach($revisions as $version => $commit)
        {
            /* Get next object. */
            if($preRevision === true)
            {
                $preAndNext->next = $version;
                break;
            }

            /* Get pre object. */
            if($revision == $version)
            {
                if($preRevision) $preAndNext->pre = $preRevision;
                $preRevision = true;
            }
            if($preRevision !== true) $preRevision = $version;
        }
        return $preAndNext;
    }

    /**
     * Project: chengfangjinke
     * Method: setBackSession
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called setBackSession.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $type
     */
    public function setBackSession($type = 'list')
    {    
        $backKey = 'doclib' . ucfirst(strtolower($type));
        session_start();
        $uri = $this->app->getURI(true);
        if(!empty($_GET) and $this->config->requestType == 'PATH_INFO') $uri .= "?" . http_build_query($_GET);
        $_SESSION[$backKey] = $uri;
        if($type == 'list') unset($_SESSION['doclibView']);
        session_write_close();
    }

    /**
     * Project: chengfangjinke
     * Method: isBinary
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called isBinary.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $content
     * @param string $suffix
     * @return bool
     */
    public function isBinary($content, $suffix = '')
    {    
        if(strpos($this->config->doclib->binary, "|$suffix|") !== false) return true;

        $blk = substr($content, 0, 512);
        return (
            false ||
            substr_count($blk, "^\r\n")/512 > 0.3 ||
            substr_count($blk, "^ -~")/512 > 0.3 ||
            substr_count($blk, "\x00") > 0
        );   
    }
}
