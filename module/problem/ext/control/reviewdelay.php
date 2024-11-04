<?php

include '../../control.php';
class myProblem extends problem
{
    public function reviewdelay($id)
    {
        $this->app->loadLang('review');
        $problem = $this->problem->getByID($id);
        $flag    = common::hasPriv('problem', 'reviewdelay')
            && in_array($problem->delayStatus, array_keys($this->lang->problem->reviewNodeStatusLableList))
            && in_array($this->app->user->account, explode(',', $problem->delayDealUser));
        if(!$flag){
//            $response['result']  = 'fail';
//            $response['message'] = $this->lang->problem->authStatusError;
//            $response['locate']  = inlink('view', "problemID=$id");
//            $this->send($response);
        }
//        公司领导
        $toManager = $this->dao->select('account,realname')->from(TABLE_USER)
            ->alias('u')
            ->leftJoin(TABLE_DEPT)->alias('d')->on('d.id=u.dept')
            ->where('d.name')->eq('公司领导')->fetchAll();
        $this->view->toManager = array(''=>'') + array_column($toManager,'realname','account');

        if($_POST)
        {
            $logChanges = $this->problem->delayReview($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('problem', $id, 'problemreviewchange', $this->post->suggest);
            $this->action->logHistory($actionID, $logChanges);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title          = $this->lang->review->submit;
        $this->view->position[]     = $this->lang->review->submit;
        $this->view->problem       = $problem;
        $this->display();
    }
}
