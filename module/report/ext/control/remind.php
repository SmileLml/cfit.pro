<?php
include '../../control.php';
class myReport extends report
{
    public function remind()
    {
        $bugs = $tasks = $todos = $testTasks = array();
        if($this->config->report->dailyreminder->bug)      $bugs  = $this->report->getUserBugs();
        if($this->config->report->dailyreminder->task)     $tasks = $this->report->getUserTasks();
        if($this->config->report->dailyreminder->todo)     $todos = $this->report->getUserTodos();
        if($this->config->report->dailyreminder->testTask) $testTasks = $this->report->getUserTestTasks();

        /* 额外审批提醒相关对象数据。*/
        $modifys = $problems = $demands = $fixs = $gains = $plans = $reviews = $changes = $requirements = array();
        if($this->config->report->dailyreminder->problem) $problems = $this->report->getUserProblemList();
        if($this->config->report->dailyreminder->demand)  $demands  = $this->report->getUserDemandList();
        if($this->config->report->dailyreminder->modify)  $modifys  = $this->report->getUserModifyList();
        if($this->config->report->dailyreminder->fix)     $fixs     = $this->report->getUserFixList();
        if($this->config->report->dailyreminder->gain)    $gains    = $this->report->getUserGainList();
        if($this->config->report->dailyreminder->gainqz)    $gainsqz   = $this->report->getUserGainqzList();
        if($this->config->report->dailyreminder->projectplan) $plans   = $this->report->getUserProjectplanList();
        if($this->config->report->dailyreminder->review)      $reviews = $this->report->getUserReviewList();
        if($this->config->report->dailyreminder->change)      $changes = $this->report->getUserChangeList();
        if($this->config->report->dailyreminder->requirement) $requirements = $this->report->getUserRequirementList();

        $reminder = array();

        $users = array_unique(array_merge(array_keys($bugs), array_keys($tasks), array_keys($todos), array_keys($testTasks), array_keys($problems), array_keys($demands), array_keys($modifys), array_keys($fixs), array_keys($gains), array_keys($plans), array_keys($reviews), array_keys($changes), array_keys($requirements)));

        if(!empty($users)) foreach($users as $user) $reminder[$user] = new stdclass();
        if(!empty($bugs))  foreach($bugs as $user => $bug)   $reminder[$user]->bugs  = $bug;
        if(!empty($tasks)) foreach($tasks as $user => $task) $reminder[$user]->tasks = $task;
        if(!empty($todos)) foreach($todos as $user => $todo) $reminder[$user]->todos = $todo;
        if(!empty($testTasks)) foreach($testTasks as $user => $testTask) $reminder[$user]->testTasks = $testTask;

        /* 额外审批提醒相关对象数据。*/
        if(!empty($problems)) foreach($problems as $user => $problem) $reminder[$user]->problems = $problem;
        if(!empty($demands))  foreach($demands as $user => $demand)   $reminder[$user]->demands  = $demand;
        if(!empty($modifys))  foreach($modifys as $user => $modify)   $reminder[$user]->modifys  = $modify;
        if(!empty($fixs))     foreach($fixs as $user => $fix)         $reminder[$user]->fixs     = $fix;
        if(!empty($gains))    foreach($gains as $user => $gain)       $reminder[$user]->gains    = $gain;
        if(!empty($gainsqz))   foreach($gainsqz as $user => $gainqz)  $reminder[$user]->gainqzs   = $gainqz;
        if(!empty($plans))    foreach($plans as $user => $plan)       $reminder[$user]->plans    = $plan;
        if(!empty($reviews))  foreach($reviews as $user => $review)   $reminder[$user]->reviews  = $review;
        if(!empty($changes))  foreach($changes as $user => $change)   $reminder[$user]->changes  = $change;
        if(!empty($requirements)) foreach($requirements as $user => $requirement) $reminder[$user]->requirements = $requirement;

        $this->loadModel('mail');
        $this->app->loadLang('custommail');

        /* Check mail turnon.*/
        if(!$this->config->mail->turnon)
        {
            echo "You should turn on the Email feature first.\n";
            return false;
        }

        // 查找已发信用户，删除该用户不进行发信了。
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');
        $mailUserList = $this->dao->select('id,account')->from(TABLE_MAILRECORD)->where('sendDate')->eq($today)->andWhere('sendResult')->eq('ok')->fetchPairs();
        foreach($mailUserList as $account)
        {
            unset($reminder[$account]);
        }

        foreach($reminder as $user => $mail)
        {
            /* Reset $this->output. */
            $this->clear();

            $mailTitle  = $this->lang->report->mailTitle->begin;
            $mailTitle .= isset($mail->bugs)  ? sprintf($this->lang->report->mailTitle->bug,  count($mail->bugs))  : '';
            $mailTitle .= isset($mail->tasks) ? sprintf($this->lang->report->mailTitle->task, count($mail->tasks)) : '';
            $mailTitle .= isset($mail->todos) ? sprintf($this->lang->report->mailTitle->todo, count($mail->todos)) : '';
            $mailTitle .= isset($mail->testTasks) ? sprintf($this->lang->report->mailTitle->testTask, count($mail->testTasks)) : '';

           /* 额外审批提醒相关对象数据。*/
            $mailTitle .= isset($mail->problems) ? sprintf($this->lang->report->mailTitle->problem, count($mail->problems)) : '';
            $mailTitle .= isset($mail->demands)  ? sprintf($this->lang->report->mailTitle->demand, count($mail->demands)) : '';
            $mailTitle .= isset($mail->modifys)  ? sprintf($this->lang->report->mailTitle->modify, count($mail->modifys)) : '';
            $mailTitle .= isset($mail->fixs)     ? sprintf($this->lang->report->mailTitle->fix, count($mail->fixs)) : '';
            $mailTitle .= isset($mail->gains)    ? sprintf($this->lang->report->mailTitle->gain, count($mail->gains)) : '';
            $mailTitle .= isset($mail->gainsqz)  ? sprintf($this->lang->report->mailTitle->gainsqz, count($mail->gainsqz)) : '';
            $mailTitle .= isset($mail->plans)    ? sprintf($this->lang->report->mailTitle->projectplan, count($mail->plans)) : '';
            $mailTitle .= isset($mail->reviews)  ? sprintf($this->lang->report->mailTitle->review, count($mail->reviews)) : '';
            $mailTitle .= isset($mail->changes)  ? sprintf($this->lang->report->mailTitle->change, count($mail->changes)) : '';
            $mailTitle .= isset($mail->requirements) ? sprintf($this->lang->report->mailTitle->requirement, count($mail->requirements)) : '';

            $mailTitle  = rtrim($mailTitle, ',');

            /* Get email content and title.*/
            $this->view->mail      = $mail;
            $this->view->mailTitle = $mailTitle;

            $oldViewType = $this->viewType;
            if($oldViewType == 'json') $this->viewType = 'html';
            $mailContent = $this->parse('report', 'dailyreminder');
            $this->viewType == $oldViewType;

            /* Send email.*/
            echo date('Y-m-d H:i:s') . " sending to $user, ";
            $this->mail->send($user, $mailTitle, $mailContent, '', true);
            if($this->mail->isError())
            {
                echo "fail: \n" ;
                a($this->mail->getError());

                $mailrecord = new stdClass();
                $mailrecord->account     = $user;
                $mailrecord->sendDate    = $today;
                $mailrecord->sendResult  = 'fail';
                $mailrecord->createdDate = $now;
                $this->dao->insert(TABLE_MAILRECORD)->data($mailrecord)->exec();

                continue;
            }
            echo "ok\n";

            $mailrecord = new stdClass();
            $mailrecord->account     = $user;
            $mailrecord->sendDate    = $today;
            $mailrecord->sendResult  = 'ok';
            $mailrecord->createdDate = $now;
            $this->dao->insert(TABLE_MAILRECORD)->data($mailrecord)->exec();
        }
    }
}
