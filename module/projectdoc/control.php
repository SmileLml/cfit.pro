<?php
class projectdoc extends control
{
    public function __construct()
    {
        parent::__construct();

        $this->scm = $this->app->loadClass('scm');

        $disFuncs = str_replace(' ', '', ini_get('disable_functions'));
        if(stripos(",$disFuncs,", ',exec,') !== false or stripos(",$disFuncs,", ',shell_exec,') !== false)
        {
            echo js::alert($this->lang->projectdoc->error->useless);
            die(js::locate('back'));
        }

        $projectID = $this->session->project;
        $this->libs = $this->projectdoc->getLibPairs($projectID);

        /* Unlock session for wait to get data of doclib. */
        session_write_close();
    }

    /**
     * Project: chengfangjinke
     * Method: maintain
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called maintain.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function maintain($projectID, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('project')->setMenu($projectID);

        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $libList = $this->projectdoc->getLibList($projectID, $orderBy, $pager);

        $this->view->title      = $this->lang->projectdoc->common . $this->lang->colon . $this->lang->projectdoc->maintain;
        $this->view->position[] = $this->lang->projectdoc->maintain;

        $this->view->projectID = $projectID;
        $this->view->orderBy   = $orderBy;
        $this->view->pager     = $pager;
        $this->view->libList   = $libList;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     */
    public function create($projectID)
    {
        $this->loadModel('project')->setMenu($projectID);

        if($_POST)
        {
            /* Pager. */
            $this->app->loadClass('pager', $static = true);
            $pager = new pager(0, 10, 1);
            $libList = $this->projectdoc->getLibList($projectID, 'id_desc', $pager);
            if(!empty($libList))
            {
                dao::$errors[] = $this->lang->projectdoc->error->onlyOne;
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            }

            $libID = $this->projectdoc->create($projectID);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $link = $this->projectdoc->createLink('showSyncCommit', "projectID=$projectID&libID=$libID", '', false);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        $this->view->title      = $this->lang->projectdoc->common . $this->lang->colon . $this->lang->projectdoc->create;
        $this->view->position[] = $this->lang->projectdoc->create;
        $this->view->groups     = $this->loadModel('group')->getPairs();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noempty|nodeleted');

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: edit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called edit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $libID
     */
    public function edit($projectID, $libID)
    {
        $this->loadModel('project')->setMenu($projectID);
        $lib = $this->projectdoc->getLibByID($libID);
        if($_POST)
        {
            $noNeedSync = $this->projectdoc->update($libID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if(!$noNeedSync)
            {
                $link = $this->diclib->createLink('showSyncCommit', "projectID=$projectID&libID=$libID");
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('maintain', "projectID=$projectID")));
        }

        $this->app->loadLang('action');

        $this->view->lib      = $lib;
        $this->view->libID    = $libID;
        $this->view->groups   = $this->loadModel('group')->getPairs();
        $this->view->users    = $this->loadModel('user')->getPairs('noletter|noempty|nodeleted');

        $this->view->title      = $this->lang->projectdoc->common . $this->lang->colon . $this->lang->projectdoc->edit;
        $this->view->position[] = html::a(inlink('maintain'), $this->lang->projectdoc->common);
        $this->view->position[] = $this->lang->projectdoc->edit;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param int $libID
     * @param string $branchID
     * @param string $path
     * @param string $revision
     * @param int $refresh
     */
    public function browse($projectID, $libID = 0, $branchID = '', $path = '', $revision = 'HEAD', $refresh = 0)
    {
        $this->loadModel('project')->setMenu($projectID);
        $libID = $this->projectdoc->saveState($libID);
        $this->projectdoc->setMenu($this->libs, $libID);
        $this->projectdoc->setBackSession('list');

        /* Get path and refresh. */
        if($this->get->doclibPath) $path = $this->get->doclibPath;
        if(empty($refresh) and $this->cookie->doclibRefresh) $refresh = $this->cookie->doclibRefresh;

        session_start();
        $this->session->set('revisionList', $this->app->getURI(true));
        session_write_close();

        /* Get doclib and synchronous commit. */
        $doclib = $this->projectdoc->getLibByID($libID);
        if(!$doclib->synced) $this->locate($this->projectdoc->createLink('showSyncCommit', "projectID=$projectID&libID=$libID"));

        /* Decrypt path and get cacheFile. */
        $path      = $this->projectdoc->decodePath($path);
        $cacheFile = $this->projectdoc->getCacheFile($libID, $path, $revision);
        $this->scm->setEngine($doclib);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager(0, 8, 1);

        if($_POST)
        {
            $oldRevision = isset($this->post->revision[1]) ? $this->post->revision[1] : '';
            $newRevision = isset($this->post->revision[0]) ? $this->post->revision[0] : '';

            $this->locate($this->projectdoc->createLink('diff', "projectID=$projectID&libID=$libID&entry=" . $this->projectdoc->encodePath($path) . "&oldrevision=$oldRevision&newRevision=$newRevision"));
        }

        /* Cache infos. */
        if($refresh or !$cacheFile or !file_exists($cacheFile) or (time() - filemtime($cacheFile)) / 60 > $this->config->projectdoc->cacheTime)
        {
            /* Get cache infos. */
            $infos = $this->scm->ls($path, $revision);

            if($infos)
            {
                /* Update code commit history. */
                $this->projectdoc->updateCommit($doclib);

                $revisionList = array();
                foreach($infos as $info) $revisionList[$info->revision] = $info->revision;
                $comments = $this->projectdoc->getHistory($libID, $revisionList);
                foreach($infos as $info)
                {
                    if(isset($comments[$info->revision]))
                    {
                        $comment = $comments[$info->revision];
                        $info->comment = $comment->comment;
                    }
                }
            }

            if($cacheFile)
            {
                if(!file_exists($cacheFile . '.lock'))
                {
                    touch($cacheFile .  '.lock');
                    file_put_contents($cacheFile, serialize($infos));
                    unlink($cacheFile . '.lock');
                }
            }
        }
        else
        {
            $infos = unserialize(file_get_contents($cacheFile));
        }
        if($this->cookie->doclibRefresh) setcookie('doclibRefresh', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);

        /* Set logType and revisions. */
        $logType   = 'dir';
        $revisions = $this->projectdoc->getCommits($doclib, $path, $revision, $logType, $pager);

        /* Set committers. */
        $commiters = $this->loadModel('user')->getCommiters();
        foreach($infos as $info) $info->committer = zget($commiters, $info->account, $info->account);
        foreach($revisions as $log) $log->committer = zget($commiters, $log->committer, $log->committer);

        $this->view->title     = $this->lang->projectdoc->common;
        $this->view->projectID = $projectID;
        $this->view->doclib    = $doclib;
        $this->view->libs      = $this->libs;
        $this->view->revisions = $revisions;
        $this->view->revision  = $revision;
        $this->view->infos     = $infos;
        $this->view->libID     = $libID;
        $this->view->branchID  = $branchID ? $branchID : 0;
        $this->view->pager     = $pager;
        $this->view->path      = urldecode($path);
        $this->view->logType   = $logType;
        $this->view->cacheTime = date('m-d H:i', filemtime($cacheFile));

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $libID
     * @param string $entry
     * @param string $revision
     * @param string $showBug
     * @param string $encoding
     */
    public function view($projectID, $libID, $entry = '', $revision = 'HEAD', $showBug = 'false', $encoding = '')
    {
        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('file');
        if($this->get->doclibPath) $entry = $this->get->doclibPath;
        $this->projectdoc->setBackSession('view');

        if($libID == 0) $libID = $this->session->libID;

        if($_POST)
        {
            $oldRevision = isset($this->post->revision[1]) ? $this->post->revision[1] : '';
            $newRevision = isset($this->post->revision[0]) ? $this->post->revision[0] : '';

            $this->locate($this->projectdoc->createLink('diff', "projectID=$projectID&libID=$libID&entry=$entry&oldrevision=$oldRevision&newRevision=$newRevision"));
        }

        $file  = $entry;
        $lib   = $this->projectdoc->getLibByID($libID);
        $entry = $this->projectdoc->decodePath($entry);

        $this->scm->setEngine($lib);
        $info = $this->scm->info($entry, $revision);
        $path = $entry ? $info->path : '';
        if($info->kind == 'dir') $this->locate($this->projectdoc->createLink('browse', "projectID=$projectID&libID=$libID&branchID=&path=" . $this->projectdoc->encodePath($path) . "&revision=$revision"));
        $content  = $this->scm->cat($entry, $revision);
        $entry    = urldecode($entry);
        $pathInfo = pathinfo($entry);
        $encoding = empty($encoding) ? $lib->encoding : $encoding;
        $encoding = strtolower(str_replace('_', '-', $encoding));

        $fileName  = basename(urldecode($entry));
        $extension = ltrim(strrchr($fileName, '.'), '.');

        $officeTypes = 'doc|docx|xls|xlsx|ppt|pptx|pdf';
        if(stripos($officeTypes, $extension) !== false)
        {
            $sessionvar = isset($this->config->sessionvar) ? $this->config->sessionvar : '';
            if(isset($_get[$sessionvar]))
            {
                $sessionid = isset($_cookie[$this->config->sessionVar]) ? $_COOKIE[$this->config->sessionVar] : sha1(mt_rand());
                session_write_close();
                session_id($sessionID);
                session_start();
            }

            if(isset($this->config->file->convertType) and $this->config->file->convertType == 'collabora' and $this->config->requestType == 'PATH_INFO')
            {
                $discovery = $this->file->getCollaboraDiscovery();
                if(empty($discovery)) die(js::alert(sprintf($this->lang->file->collaboraFail, $this->config->file->collaboraPath)));
                if($discovery and isset($discovery[$extension]))
                {
                    $address = $this->config->file->internalAddress;
                    $doclibPath = $this->projectdoc->encodePath($entry);

                    $wopiSrc      = $address . $this->projectdoc->createLink('ajaxWopiFiles', "libID=$libID&path=$doclibPath&fromRevision=$revision");
                    $action       = $discovery[$extension]['action'];
                    $collaboraUrl = str_replace($this->config->file->collaboraPath, $this->config->file->publicPath, $discovery[$extension]['urlsrc']);

                    $this->view->collaboraUrl = $collaboraUrl . 'WOPISrc=' . $wopiSrc . '&access_token=' . session_id();
                    $this->view->title        = $fileName;
                }
            }
        }

        $suffix   = '';
        if(isset($pathInfo["extension"])) $suffix = strtolower($pathInfo["extension"]);
        if(!$suffix or (!array_key_exists($suffix, $this->config->projectdoc->suffix) and strpos($this->config->projectdoc->images, "|$suffix|") === false)) $suffix = $this->projectdoc->isBinary($content, $suffix) ? 'binary' : 'c';

        if(strpos($this->config->projectdoc->images, "|$suffix|") !== false)
        {
            $content = base64_encode($content);
        }
        elseif($encoding != 'utf-8')
        {
            $content = helper::convertEncoding($content, $encoding);
        }

        $this->app->loadClass('pager', $static = true);
        $pager = new pager(0, 8, 1);

        $commiters = $this->loadModel('user')->getCommiters();
        $logType   = 'file';
        $revisions = $this->projectdoc->getCommits($lib, '/' . $entry, 'HEAD', $logType, $pager);

        $i = 0;
        foreach($revisions as $log)
        {
            if($revision == 'HEAD' and $i == 0) $revision = $log->revision;
            if($revision == $log->revision) $revisionName = $log->revision;
            $log->committer = zget($commiters, $log->committer, $log->committer);
            $i++;
        }
        if(!isset($revisionName)) $revisionName = $revision;

        $this->view->projectID    = $projectID;
        $this->view->revisions    = $revisions;
        $this->view->title        = $this->lang->projectdoc->common;
        $this->view->type         = 'view';
        $this->view->showBug      = $showBug;
        $this->view->encoding     = str_replace('-', '_', $encoding);
        $this->view->libID        = $libID;
        $this->view->branchID     = $this->cookie->doclibBranch;
        $this->view->doclib       = $lib;
        $this->view->revision     = $revision;
        $this->view->revisionName = $revisionName;
        $this->view->preAndNext   = $this->projectdoc->getPreAndNext($lib, '/' . $entry, $revision);
        $this->view->file         = $file;
        $this->view->entry        = $entry;
        $this->view->path         = $entry;
        $this->view->suffix       = $suffix;
        $this->view->content      = $content;
        $this->view->pager        = $pager;
        $this->view->logType      = $logType;
        $this->view->info         = $info;

        $this->view->title      = $this->lang->projectdoc->common . $this->lang->colon . $this->lang->projectdoc->view;
        $this->view->position[] = $this->lang->projectdoc->common;
        $this->view->position[] = $this->lang->projectdoc->view;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: diff
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called diff.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $libID
     * @param string $entry
     * @param string $oldRevision
     * @param string $newRevision
     * @param string $showBug
     * @param string $encoding
     */
    public function diff($projectID, $libID, $entry = '', $oldRevision = '0', $newRevision = 'HEAD', $showBug = 'false', $encoding = '')
    {
        $this->loadModel('project')->setMenu($projectID);
        if($this->get->doclibPath) $entry = $this->get->doclibPath;
        $file   = $entry;
        $lib    = $this->projectdoc->getLibByID($libID);
        $entry  = $this->projectdoc->decodePath($entry);

        $pathInfo = pathinfo($entry);
        $suffix   = '';
        if(isset($pathInfo["extension"])) $suffix = strtolower($pathInfo["extension"]);

        $arrange = $this->cookie->arrange ? $this->cookie->arrange : 'inline';
        if($this->server->request_method == 'POST')
        {
            $oldRevision = isset($this->post->revision[1]) ? $this->post->revision[1] : '';
            $newRevision = isset($this->post->revision[0]) ? $this->post->revision[0] : '';

            if($this->post->arrange)
            {
                $arrange = $this->post->arrange;
                setcookie('arrange', $arrange);
            }
            if($this->post->encoding) $encoding = $this->post->encoding;

            $this->locate($this->projectdoc->createLink('diff', "projectID=$projectID&libID=$libID&entry=" . $this->projectdoc->encodePath($entry) . "&oldrevision=$oldRevision&newRevision=$newRevision&showBug=&encoding=$encoding"));
        }

        $this->scm->setEngine($lib);
        $encoding = empty($encoding) ? $lib->encoding : $encoding;
        $encoding = strtolower(str_replace('_', '-', $encoding));
        $info     = $this->scm->info($entry, $newRevision);
        $diffs    = $this->scm->diff($entry, $oldRevision, $newRevision);
        foreach($diffs as $diff)
        {
            if($encoding != 'utf-8')
            {
                $diff->fileName = helper::convertEncoding($diff->fileName, $encoding);
                if(empty($diff->contents)) continue;
                foreach($diff->contents as $content)
                {
                    if(empty($content->lines)) continue;
                    foreach($content->lines as $lines)
                    {
                        if(empty($lines->line)) continue;
                        $lines->line = helper::convertEncoding($lines->line, $encoding);
                    }
                }
            }
        }

        /* When arrange is appose then adjust data for show them easy.*/
        if($arrange == 'appose')
        {
            foreach($diffs as $diffFile)
            {
                if(empty($diffFile->contents)) continue;
                foreach($diffFile->contents as $content)
                {
                    $old = array();
                    $new = array();
                    foreach($content->lines as $line)
                    {
                        if($line->type != 'new') $old[$line->oldlc] = $line->line;
                        if($line->type != 'old') $new[$line->newlc] = $line->line;
                    }
                    $content->old = $old;
                    $content->new = $new;
                }
            }
        }

        $this->view->type        = 'diff';
        $this->view->showBug     = $showBug;
        $this->view->entry       = urldecode($entry);
        $this->view->suffix      = $suffix;
        $this->view->file        = $file;
        $this->view->libID       = $libID;
        $this->view->branchID    = $this->cookie->doclibBranch;
        $this->view->lib         = $lib;
        $this->view->encoding    = str_replace('-', '_', $encoding);
        $this->view->arrange     = $arrange;
        $this->view->diffs       = $diffs;
        $this->view->newRevision = $newRevision;
        $this->view->oldRevision = $oldRevision;
        $this->view->revision    = $newRevision;
        $this->view->historys    = '';
        $this->view->info        = $info;

        $this->view->title      = $this->lang->projectdoc->common . $this->lang->colon . $this->lang->projectdoc->diff;
        $this->view->position[] = $this->lang->projectdoc->common;
        $this->view->position[] = $this->lang->projectdoc->diff;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $libID
     * @param string $confirm
     */
    public function delete($projectID, $libID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            $lib = $this->projectdoc->getLibByID($libID);
            echo js::confirm(sprintf($this->lang->projectdoc->confirmDelete, $lib->name), $this->createLink('projectdoc', 'delete', "projectID=$projectID&libID=$libID&confirm=yes"));
            die();
        }
        else
        {
            $this->projectdoc->delete(TABLE_PROJECTDOCUMENT, $libID);
            $projectBrowse = $this->createLink('projectdoc', 'maintain', "projectID=$projectID");
            die(js::locate($projectBrowse, 'parent'));
        }
    }

    /**
     * Project: chengfangjinke
     * Method: revision
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called revision.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $libID
     * @param string $revision
     * @param string $root
     * @param string $type
     */
    public function revision($projectID, $libID, $revision = '', $root = '', $type = 'dir')
    {
        $this->loadModel('project')->setMenu($projectID);
        $this->projectdoc->setBackSession();
        if($libID == 0) $libID = $this->session->libID;
        $lib = $this->projectdoc->getLibByID($libID);

        /* Save session. */
        $this->session->set('revisionList', $this->app->getURI(true), 'project');

        $this->scm->setEngine($lib);
        $log = $this->scm->log('', $revision, $revision);

        $history = $this->dao->select('*')->from(TABLE_DOCHISTORY)->where('revision')->eq($log[0]->revision)->andWhere('lib')->eq($libID)->fetch();
        if($history)
        {
            $oldRevision = $this->dao->select('*')->from(TABLE_DOCHISTORY)->where('revision')->lt($history->revision)->andWhere('lib')->eq($libID)->orderBy('revision_desc')->limit(1)->fetch('revision');

            $log[0]->commit = $history->commit;
        }

        $changes  = array();
        $viewPriv = common::hasPriv('doclib', 'view');
        $diffPriv = common::hasPriv('doclib', 'diff');
        foreach($log[0]->change as $path => $change)
        {
            if($lib->prefix) $path = str_replace($lib->prefix, '', $path);
            $encodePath = $this->projectdoc->encodePath($path);
            if($change['kind'] == '' or $change['kind'] == 'file')
            {
                $change['view'] = $viewPriv ? html::a($this->projectdoc->createLink('view', "projectID=$projectID&libID=$libID&entry=$encodePath&revision=$revision"), $this->lang->projectdoc->viewA, '', "data-app='{$this->app->openApp}'") : '';
                if($change['action'] == 'M') $change['diff'] = $diffPriv ? html::a($this->projectdoc->createLink('diff', "projectID=$projectID&libID=$libID&entry=$encodePath&oldRevision=$oldRevision&newRevision=$revision"), $this->lang->projectdoc->diffAB, '', "data-app='{$this->app->openApp}'") : '';
            }
            else
            {
                $change['view'] = $viewPriv ? html::a($this->projectdoc->createLink('browse', "projectID=$projectID&libID=$libID&branchID=&path=$encodePath&revision=$revision"), $this->lang->projectdoc->browse, '', "data-app='{$this->app->openApp}'") : '';
                if($change['action'] == 'M') $change['diff'] = $diffPriv ? html::a($this->projectdoc->createLink('diff', "projectID=$projectID&libID=$libID&entry=$encodePath&oldRevision=$oldRevision&newRevision=$revision"), $this->lang->projectdoc->diffAB, '', "data-app='{$this->app->openApp}'") : '';
            }
            $changes[$path] = $change;
        }

        $root   = $this->projectdoc->decodePath($root);
        $parent = '';
        if($type == 'file')
        {
            $parent = $this->dao->select('parent')->from(TABLE_DOCFILES)
                ->where('revision')->eq($history->id)
                ->andWhere('path')->eq('/' . $root)
                ->fetch('parent');
        }

        $this->view->projectID   = $projectID;
        $this->view->title       = $this->lang->projectdoc->common;
        $this->view->log         = $log[0];
        $this->view->lib         = $lib;
        $this->view->path        = $root;
        $this->view->type        = $type;
        $this->view->changes     = $changes;
        $this->view->libID       = $libID;
        $this->view->branchID    = $this->cookie->doclibBranch;
        $this->view->revision    = $log[0]->revision;
        $this->view->parentDir   = $parent;
        $this->view->oldRevision = $oldRevision;
        $this->view->preAndNext  = $this->projectdoc->getPreAndNext($lib, $root, $revision, $type, 'revision');

        $this->view->title      = $this->lang->projectdoc->common . $this->lang->colon . $this->lang->projectdoc->viewRevision;
        $this->view->position[] = $this->lang->projectdoc->common;
        $this->view->position[] = $this->lang->projectdoc->viewRevision;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: download
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called download.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $path
     * @param string $fromRevision
     * @param string $toRevision
     * @param string $type
     */
    public function download($libID, $path, $fromRevision = 'HEAD', $toRevision = '', $type = 'file')
    {
        if($this->get->doclibPath) $path = $this->get->doclibPath;
        $entry = $this->projectdoc->decodePath($path);
        $lib   = $this->projectdoc->getLibByID($libID);

        $this->scm->setEngine($lib);
        $content = $type == 'file' ? $this->scm->cat($entry, $fromRevision) : $this->scm->diff($entry, $fromRevision, $toRevision, 'patch'); 

        $fileName = basename(urldecode($entry));
        if($type != 'file') $fileName .= "r$fromRevision--r$toRevision.patch";
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $this->fetch('file', 'sendDownHeader', array("fileName" => $fileName, "fileType" => $extension,  "content" => $content));
    }

    /**
     * Project: chengfangjinke
     * Method: filePreview
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called filePreview.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $fileLink
     * @param $fileTitle
     * @param int $libID
     * @param string $entry
     * @param string $revision
     * @param string $showBug
     * @param string $encoding
     * @return false|void
     */
    public function filePreview($projectID, $fileLink, $fileTitle, $libID = 0, $entry = '', $revision = 'HEAD', $showBug = 'false', $encoding = '')
    {
        /* Determine whether the preview file is in the version library. */
        $libPairs = $this->projectdoc->getLibPathPairs($projectID);
        foreach($libPairs as $libIndex => $path)
        {
            if(strpos($fileLink, $path) !== false)
            {
                $libFileName = str_replace($path, '', $fileLink);
                $libFileName = trim($libFileName, '/');
                $libID = $libIndex;
                $entry = $libFileName;
                $entry = $this->projectdoc->encodePath($entry);
                $_GET['doclibPath'] = $entry;
                break;
            }
        }

        if(empty($libID) or empty($entry))
        {
            $previewLink = '<a href="' . $fileLink . '">' . $fileTitle . '</a>';
            echo $previewLink;
            return false;
        }

        $this->loadModel('file');
        if($this->get->doclibPath) $entry = $this->get->doclibPath;
        $this->projectdoc->setBackSession('view');

        if($libID == 0) $libID = $this->session->libID;

        $file  = $entry;
        $lib   = $this->projectdoc->getLibByID($libID);
        $entry = $this->projectdoc->decodePath($entry);

        $this->scm->setEngine($lib);
        $info = $this->scm->info($entry, $revision);
        $path = $entry ? $info->path : '';

        if(empty($path) or $info->kind == 'dir')
        {
            $previewLink = '<a href="' . $fileLink . '">' . $fileTitle . '</a>';
            echo $previewLink;
            return false;
        }

        $content  = $this->scm->cat($entry, $revision);
        $entry    = urldecode($entry);
        $pathInfo = pathinfo($entry);
        $encoding = empty($encoding) ? $lib->encoding : $encoding;
        $encoding = strtolower(str_replace('_', '-', $encoding));

        $fileName  = basename(urldecode($entry));
        $extension = ltrim(strrchr($fileName, '.'), '.');

        $officeTypes = 'doc|docx|xls|xlsx|ppt|pptx|pdf';
        if(stripos($officeTypes, $extension) !== false)
        {
            $sessionvar = isset($this->config->sessionvar) ? $this->config->sessionvar : '';
            if(isset($_get[$sessionvar]))
            {
                $sessionid = isset($_cookie[$this->config->sessionVar]) ? $_COOKIE[$this->config->sessionVar] : sha1(mt_rand());
                session_write_close();
                session_id($sessionID);
                session_start();
            }

            if(isset($this->config->file->convertType) and $this->config->file->convertType == 'collabora' and $this->config->requestType == 'PATH_INFO')
            {
                $discovery = $this->file->getCollaboraDiscovery();
                if(empty($discovery)) die(js::alert(sprintf($this->lang->file->collaboraFail, $this->config->file->collaboraPath)));
                if($discovery and isset($discovery[$extension]))
                {
                    $address = $this->config->file->internalAddress;
                    $doclibPath = $this->projectdoc->encodePath($entry);

                    $wopiSrc      = $address . $this->projectdoc->createLink('ajaxWopiFiles', "libID=$libID&path=$doclibPath&fromRevision=$revision");
                    $action       = $discovery[$extension]['action'];
                    $collaboraUrl = str_replace($this->config->file->collaboraPath, $this->config->file->publicPath, $discovery[$extension]['urlsrc']);

                    $collaboraUrl = $collaboraUrl . 'WOPISrc=' . $wopiSrc . '&access_token=' . session_id();

                    $previewLink = '<a href="' . $collaboraUrl . '" target="_blank">' . $fileTitle . '</a>';
                    echo $previewLink;
                    return false;
                }
            }
        }
        else
        {
            $previewLink = '<a href="' . $fileLink . '">' . $fileTitle . '</a>';
            echo $previewLink;
            return false;
        }
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxWopiFiles
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called ajaxWopiFiles.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $path
     * @param string $fromRevision
     */
    public function ajaxWopiFiles($libID, $path, $fromRevision = 'HEAD')
    {
        $doclibPath = parse_url($this->get->doclibPath);
        $path = $doclibPath['path'];
        if(isset($doclibPath['query'])) parse_str($doclibPath['query'], $_GET);

        if(isset($_GET['access_token']))
        {
            session_write_close();
            session_id($_GET['access_token']);
            session_start();
            $this->app->company = $this->session->company;
            $this->app->user    = $this->session->user;
        }
        //if(!($this->loadModel('user')->isLogon() or ($this->app->company->guest and $this->app->user->account == 'guest'))) die();

        $entry = $this->projectdoc->decodePath($path);
        $lib   = $this->projectdoc->getLibByID($libID);

        $this->scm->setEngine($lib);
        $content = $this->scm->cat($entry, $fromRevision); 

        $fileName  = basename(urldecode($entry));
        $extension = ltrim(strrchr($fileName, '.'), '.');

        $method   = strtoupper($_SERVER['REQUEST_METHOD']);
        $contents = false;
        if(strpos($_SERVER['PATH_INFO'], '/contents') !== false) $contents = true;

        if($contents)
        {   
            header("Content-type: application/octet-stream");
            die($content);
        }   
        else
        {   
            $SHA256 = base64_encode(hash('sha256', $content, true));

            if(!preg_match("/\.{$extension}$/", $fileName)) $fileName .= '.' . $extension;

            $fileInfo['BaseFileName']    = $fileName;
            $fileInfo['OwnerId']         = $this->app->user->account;
            $fileInfo['UserId']          = $this->app->user->account;
            $fileInfo['UserFriendlyName']= $this->app->user->realname;
            $fileInfo['SHA256']          = $SHA256;
            $fileInfo['Size']            = strlen($content);
            $fileInfo['UserCanWrite']    = 0;
            $fileInfo['LastModifiedTime']= date('Y-m-d H:i:s', time());

            die(json_encode($fileInfo));
        } 
    }

    /**
     * Project: chengfangjinke
     * Method: showSyncCommit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:18
     * Desc: This is the code comment. This method is called showSyncCommit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param int $libID
     */
    public function showSyncCommit($projectID, $libID = 0)
    {
        $this->loadModel('project')->setMenu($projectID);
        if($libID == 0) $libID = $this->session->libID;

        $this->view->title      = $this->lang->projectdoc->common . $this->lang->colon . $this->lang->projectdoc->showSyncCommit;
        $this->view->position[] = $this->lang->projectdoc->showSyncCommit;

        $latestInDB = $this->projectdoc->getLatestCommit($libID);
        $this->view->version    = $latestInDB ? (int)$latestInDB->commit : 1;
        $this->view->libID      = $libID;
        $this->view->browseLink = $this->projectdoc->createLink('browse', "projectID=$projectID&libID=$libID", '', false);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxSyncCommit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:19
     * Desc: This is the code comment. This method is called ajaxSyncCommit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $libID
     * @param string $type
     */
    public function ajaxSyncCommit($libID = 0, $type = 'batch')
    {
        set_time_limit(0);
        $doclib = $this->projectdoc->getLibByID($libID);
        if(empty($doclib)) die();

        $this->scm->setEngine($doclib);

        $latestInDB = $this->dao->select('DISTINCT t1.*')->from(TABLE_DOCHISTORY)->alias('t1')
            ->leftJoin(TABLE_DOCBRANCH)->alias('t2')->on('t1.id=t2.revision')
            ->where('t1.lib')->eq($libID)
            ->orderBy('t1.time')
            ->limit(1)
            ->fetch();

        $version  = empty($latestInDB) ? 1 : $latestInDB->commit + 1;
        $logs     = array();
        $branchID = '';
        $revision = $version == 1 ? 'HEAD' : $latestInDB->revision;
        if($type == 'batch')
        {
            $logs = $this->scm->getCommits($revision, $this->config->projectdoc->batchNum, $branchID);
        }
        else
        {
            $logs = $this->scm->getCommits($revision, 0, $branchID);
        }

        $commitCount = $this->projectdoc->saveCommit($libID, $logs, $version, $branchID);
        if(empty($commitCount))
        {
            if(!$doclib->synced)
            {
                $this->projectdoc->markSynced($libID);
                die('finish');
            }
        }

        $this->dao->update(TABLE_DOCUMENT)->set('commits=commits + ' . $commitCount)->where('id')->eq($libID)->exec();
        echo $type == 'batch' ?  $commitCount : 'finish';
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxSideCommits
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:19
     * Desc: This is the code comment. This method is called ajaxSideCommits.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $libID
     * @param $path
     * @param string $type
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function ajaxSideCommits($libID, $path, $type = 'dir', $recTotal = 0, $recPerPage = 8, $pageID = 1) 
    {    
        if($this->get->repoPath) $path = $this->get->repoPath;
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $lib       = $this->projectdoc->getLibByID($libID);
        $path      = $this->projectdoc->decodePath($path);
        $commiters = $this->loadModel('user')->getCommiters();
        $revisions = $this->projectdoc->getCommits($lib, $path, 'HEAD', $type, $pager);
        foreach($revisions as $revision) $revision->committer = zget($commiters, $revision->committer, $revision->committer);

        $this->view->lib        = $this->projectdoc->getLibByID($libID);
        $this->view->revisions  = $revisions;
        $this->view->pager      = $pager;
        $this->view->libID      = $libID;
        $this->view->logType    = $type;
        $this->view->path       = urldecode($path);
        $this->display();
    }
}
