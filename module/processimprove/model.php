<?php
class processimproveModel extends model
{
    /**
     * Get process improve list.
     * @param  string  $browseType
     * @param  string  $orderBy
     * @param  object  $pager
     * @access public
     * @return void
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $processimproveQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('processimproveQuery', $query->sql);
                $this->session->set('processimproveForm',  $query->form);
            }

            if($this->session->processimproveQuery == false) $this->session->set('processimproveQuery', ' 1 = 1');

            $processimproveQuery = $this->session->processimproveQuery;
        }

        $processimproves = $this->dao->select('*')->from(TABLE_PROCESSIMPROVE)
            ->where(1)
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($processimproveQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'processimprove', $browseType != 'bysearch');
        
        return $processimproves;
    }

    /**
     * Get process improve.
     *
     * @param  int    $processID
     * @access public
     * @return void
     */
    public function getByID($processID)
    {
        $processImprove = $this->dao->findByID($processID)->from(TABLE_PROCESSIMPROVE)->fetch();
        $processImprove = $this->loadModel('file')->replaceImgURL($processImprove, 'desc,judge');
        $processImprove->files = $this->loadModel('file')->getByObject('processimprove', $processID);

        return $processImprove;
    }

    /**
     * Create a process improve.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        $processImprove = fixer::input('post')
            ->setIF(!$this->post->isAccept, 'isAccept', 0)
            ->setIF(!$this->post->isDeploy, 'isDeploy', 0)
            ->setIF($this->post->judge,'judgedBy', $this->app->user->account)
            ->setIF($this->post->judge,'judgedDate', helper::today())
            ->add('createdBy', $this->app->user->account)
            ->add('createdDept', $this->app->user->dept)
            ->add('createdDate', helper::today())
            ->remove('uid,files,labels,comment,contactListMenu')
            ->join('mailto', ',')
            ->stripTags($this->config->processimprove->editor->create['id'], $this->config->allowedTags)
            ->get();

        $processImprove = $this->loadModel('file')->processImgURL($processImprove, $this->config->processimprove->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_PROCESSIMPROVE)->data($processImprove)->autoCheck()->batchCheck($this->config->processimprove->create->requiredFields, 'notempty')->exec();
        $processID = $this->dao->lastInsertID();

        $this->loadModel('file')->updateObjectID($this->post->uid, $processID, 'processimprove');
        $this->file->saveUpload('processimprove', $processID);

        return $processID;
    }

    /**
     * Update process improve.
     *
     * @access int $processID
     * @access public
     * @return void
     */
    public function update($processID)
    {
        $oldProcess = $this->getByID($processID);
        $processImprove = fixer::input('post')
            ->setIF(!$this->post->isAccept, 'isAccept', 0)
            ->setIF(!$this->post->isDeploy, 'isDeploy', 0)
            ->setIF($this->post->judge,'judgedBy', $this->app->user->account)
            ->setIF($this->post->judge,'judgedDate', helper::today())
            ->remove('uid,files,labels,comment,contactListMenu')
            ->join('mailto', ',')
            ->stripTags($this->config->processimprove->editor->edit['id'], $this->config->allowedTags)
            ->get();

        $processImprove = $this->loadModel('file')->processImgURL($processImprove, $this->config->processimprove->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_PROCESSIMPROVE)->data($processImprove)->where('id')->eq($processID)->autoCheck()->batchCheck($this->config->processimprove->edit->requiredFields, 'notempty')->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $processID, 'processimprove');
        $this->file->saveUpload('processimprove', $processID);

        return common::createChanges($oldProcess, $processImprove);
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:55
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $processID
     * @return array
     */
    public function feedback($processID)
    {
        $oldProcess = $this->getByID($processID);
        if($this->post->isAccept != 1)
        {
            $processImprove = new stdclass();
            $processImprove->isAccept = $this->post->isAccept;
            $processImprove->status   = 'feedbacked';

            $this->dao->update(TABLE_PROCESSIMPROVE)->data($processImprove)->where('id')->eq($processID)->autoCheck()->exec();
        }
        else
        {
            $processImprove = fixer::input('post')
                ->setIF(!$this->post->isAccept, 'isAccept', 0)
                ->setIF(!$this->post->isDeploy, 'isDeploy', 0)
                ->setIF($this->post->judge,'judgedBy', $this->app->user->account)
                ->setIF($this->post->judge,'judgedDate', helper::today())
                ->join('reviewedBy', ',')
                ->remove('uid,files,labels,comment')
                ->stripTags($this->config->processimprove->editor->feedback['id'], $this->config->allowedTags)
                ->get();
            $processImprove->status = 'feedbacked';

            $processImprove = $this->loadModel('file')->processImgURL($processImprove, $this->config->processimprove->editor->feedback['id'], $this->post->uid);
            $this->dao->update(TABLE_PROCESSIMPROVE)->data($processImprove)->where('id')->eq($processID)->autoCheck()->batchCheck($this->config->processimprove->feedback->requiredFields, 'notempty')->exec();

            $this->loadModel('file')->updateObjectID($this->post->uid, $processID, 'processimprove');
            $this->file->saveUpload('processimprove', $processID);
        }

        return common::createChanges($oldProcess, $processImprove);
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->processimprove->search['actionURL'] = $actionURL;
        $this->config->processimprove->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->processimprove->search);
    }

    /**
     * close.
     *
     * @param  int    $processID
     * @access public
     * @return void
     */
    public function close($processID = 0)
    {
        $data = new stdClass();
        $data->status = 'closed';

        $this->dao->update(TABLE_PROCESSIMPROVE)->data($data)->where('id')->eq($processID)->exec();
    }

    /**
     * isClickable.
     *
     * @param  int    $processimprove
     * @param  int    $action
     * @static
     * @access public
     * @return bool
     */
    public static function isClickable($processimprove, $action)
    {
        $action = strtolower($action);

        if($action == 'edit') return $processimprove->status == 'wait';
        if($action == 'feedback') return $processimprove->status != 'closed';
        if($action == 'close') return $processimprove->status != 'closed';

        return true;
    }

    /**
     * sendmail
     *
     * @param  int    $processID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($processID, $actionID)
    {
        $this->loadModel('mail');
        $processimprove   = $this->getById($processID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'processimprove');
        $oldcwd     = getcwd();
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');
        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $sendUsers = $this->getToAndCcList($processimprove);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $this->getSubject($processimprove, $action->action);

        /* Send mail. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * Get mail subject.
     *
     * @param  object $processimprove
     * @param  string $actionType created|edited
     * @access public
     * @return string
     */
    public function getSubject($processimprove, $actionType)
    {
        /* Set email title. */
        if($actionType == 'created')
        {
            return sprintf($this->lang->processimprove->mail->create->title, $this->app->user->realname, $processimprove->id);
        }
        else
        {
            return sprintf($this->lang->processimprove->mail->edit->title, $this->app->user->realname, $processimprove->id);
        }
    }

    /**
     * Get toList and ccList.
     *
     * @param  object     $processimprove
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($processimprove)
    {
        /* Set toList and ccList. */
        $toList   = '';
        $ccList   = str_replace(' ', '', trim($processimprove->mailto, ','));

        if(empty($toList))
        {
            if(empty($ccList)) return false;
            if(strpos($ccList, ',') === false)
            {
                $toList = $ccList;
                $ccList = '';
            }
            else
            {
                $commaPos = strpos($ccList, ',');
                $toList   = substr($ccList, 0, $commaPos);
                $ccList   = substr($ccList, $commaPos + 1);
            }
        }
        return array($toList, $ccList);
    }
}
