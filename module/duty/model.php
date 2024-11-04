<?php
class dutyModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:27
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $dutyQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('dutyQuery', $query->sql);
                $this->session->set('dutyForm', $query->form);
            }

            if($this->session->dutyQuery == false) $this->session->set('dutyQuery', ' 1 = 1');

            $dutyQuery = $this->session->dutyQuery;
        }

        $dutys = $this->dao->select('*')->from(TABLE_DUTY)
            ->beginIF($browseType == 'bysearch')->where($dutyQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'duty', $browseType != 'bysearch');
        return $dutys;
    }

    /**
     * Get count.
     *
     * @access public
     * @return void
     */
    public function getCount()
    {
        return $this->dao->select('count(*) as count')->from(TABLE_DUTY)->fetch('count');
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
        $this->config->duty->search['actionURL'] = $actionURL;
        $this->config->duty->search['queryID']   = $queryID;

        $this->config->duty->search['params']['application']['values'] = array('') + $this->loadModel('application')->getapplicationNameCodePairs();

        $this->loadModel('search')->setSearchParams($this->config->duty->search);
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:27
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function create()
    {
        $data = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->join('mailto', ',')
            ->join('user', ',')
            ->join('actualUser', ',')
            ->remove('uid,files,labels,comment,contactListMenu')
            ->stripTags($this->config->duty->editor->create['id'], $this->config->allowedTags)
            ->get();

        $data = $this->loadModel('file')->processImgURL($data, $this->config->duty->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_DUTY)->data($data)->autoCheck()->batchCheck($this->config->duty->create->requiredFields, 'notempty')->exec();

        $dutyID = $this->dao->lastInsertID();
        $this->loadModel('file')->updateObjectID($this->post->uid, $dutyID, 'duty');
        $this->file->saveUpload('duty', $dutyID);

        return $dutyID;
    }

    /**
     * Project: chengfangjinke
     * Method: batchCreate
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:27
     * Desc: This is the code comment. This method is called batchCreate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function batchCreate()
    {
        $data = fixer::input('post')->get();

        foreach($data->user as $i => $user)
        {
            if(empty($user)) continue;

            $duty = new stdclass();
            $duty->user        = $user;
            $duty->type        = $data->type[$i];
            $duty->planDate   = $data->planDate[$i];
            $duty->desc        = nl2br($data->desc[$i]);
            $duty->createdBy   = $this->app->user->account;
            $duty->createdDate = helper::today();

            $this->dao->insert(TABLE_DUTY)->data($duty)->autoCheck()->batchCheck($this->config->duty->create->requiredFields, 'notempty')->exec();

            $dutyID = $this->dao->lastInsertID();
            $this->loadModel('action')->create('duty', $dutyID, 'created');
        }
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:27
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $dutyID
     * @return mixed
     */
    public function getByID($dutyID)
    {
        $duty = $this->dao->select("*")->from(TABLE_DUTY)->where('id')->eq($dutyID)->fetch();
        $duty = $this->loadModel('file')->replaceImgURL($duty, 'desc');
        return $duty;
    }

    /**
     * Update duty improve.
     *
     * @access int $dutyID
     * @access public
     * @return void
     */
    public function update($dutyID)
    {
        $oldDuty = $this->getByID($dutyID);
        $duty = fixer::input('post')
            ->stripTags($this->config->duty->editor->edit['id'], $this->config->allowedTags)
            ->join('user', ',')
            ->join('actualUser', ',')
            ->join('mailto', ',')
            ->join('realityUser', ',')
            ->remove('uid,files,labels,comment,contactListMenu')
            ->get();

        $duty = $this->loadModel('file')->processImgURL($duty, $this->config->duty->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_DUTY)->data($duty)->where('id')->eq($dutyID)->autoCheck()->batchCheck($this->config->duty->edit->requiredFields, 'notempty')->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $dutyID, 'duty');
        $this->file->saveUpload('duty', $dutyID);

        return common::createChanges($oldDuty, $duty);
    }

    /**
     * Get duties for calendar.
     *
     * @param  string $year
     * @access public
     * @return void
     */
    public function getDuties4Calendar($year = '')
    {
        $duties = $this->dao->select('*')->from(TABLE_DUTY)
            ->beginIF($year)->where("(LEFT(`planDate`, 4) = '$year')")->fi()
            ->orderBy('planDate, id')
            ->fetchAll('id');

        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        $events = array();
        foreach($duties as $id => $duty)
        {
            $event = array();
            $event['id']    = $id;
            $event['title'] = zget($this->lang->duty->typeList, $duty->type, '');
            $event['start'] = $duty->planDate;
            $event['end']   = $duty->planDate;
            $event['url']   = helper::createLink('duty', 'view', "id=$id", '', true);

            $event['improtantTime'] = $duty->importantTime;

            $events[] = $event;
        }
        return json_encode($events);
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
        $duty   = $this->getById($processID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'duty');
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

        $sendUsers = $this->getToAndCcList($duty);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $this->getSubject($duty, $action->action);

        /* Send mail. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * Get mail subject.
     *
     * @param  object $duty
     * @param  string $actionType created|edited
     * @access public
     * @return string
     */
    public function getSubject($duty, $actionType)
    {
        /* Set email title. */
        if($actionType == 'created')
        {
            return sprintf($this->lang->duty->mail->create->title, $this->app->user->realname, $duty->id);
        }
        else
        {
            return sprintf($this->lang->duty->mail->edit->title, $this->app->user->realname, $duty->id);
        }
    }

    /**
     * Get toList and ccList.
     *
     * @param  object     $duty
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($duty)
    {
        /* Set toList and ccList. */
        $toList   = '';
        $ccList   = str_replace(' ', '', trim($duty->mailto, ','));

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
