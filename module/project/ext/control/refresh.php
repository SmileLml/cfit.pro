<?php
include '../../control.php';
class myProject extends project
{
    /**
     * Project: chengfangjinke
     * Method: refresh
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called refresh.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     */
    public function refresh($projectID,$flag = null)
    {
        $this->dao->update(TABLE_TASK)->set('progress')->eq(0)->where('project')->eq($projectID)->andWhere('status')->eq('wait')->beginIF($flag)->andWhere('dataVersion')->eq('2')->fi()->exec();
        $this->dao->update(TABLE_TASK)->set('progress')->eq(100)->where('project')->eq($projectID)->andWhere('status')->in('done,closed')->beginIF($flag)->andWhere('dataVersion')->eq('2')->fi()->exec();

        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('project')->eq($projectID)->andWhere('deleted')->eq(0)->beginIF($flag)->andWhere('dataVersion')->eq('2')->fi()->fetchAll('id');
        foreach($tasks as $task)
        {
            if($task->parent) $tasks[$task->parent]->hasChildren = true;
        }
        $this->loadModel('task');
        foreach($tasks as $task)
        {
            if(!isset($task->hasChildren)) 
            {
                $plan = helper::diffDate($task->deadline, $task->estStarted) + 1;
                $end  = substr($task->finishedDate, 0, 10);
                $real = $end != '0000-00-00' ? helper::diffDate($end, $task->realStarted) + 1 : 0;
                $this->dao->update(TABLE_TASK)->set('planDuration')->eq($plan)->set('realDuration')->eq($real)->where('id')->eq($task->id)->exec();
                $this->task->computeConsumed($task->id);
            }
        }
        if($flag){
            $url = $this->createLink('newexecution', 'execution', "type=all&projectID=$projectID");
        }else{
            $url = $this->createLink('project', 'execution', "type=all&projectID=$projectID");
        }
        die(js::locate($url));
    }
}
