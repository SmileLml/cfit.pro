<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class myeffort extends effort
{
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
     * @param string $date
     * @param string $userID
     */
    public function batchCreate($date = 'today', $userID = '')
    {
        if(!empty($_POST))
        {
            $efforts = fixer::input('post')->get();
            $taskTotalConsumed = 0;
            foreach($efforts->id as $id => $num)
            {
                if(strpos($efforts->objectType[$id], '_') !== false)
                {
                    $pos = strpos($efforts->objectType[$id], '_');
                    $efforts->objectType[$id] = substr($efforts->objectType[$id], 0, $pos);
                    if($efforts->objectType[$id] == 'task')
                    {
                        $taskTotalConsumed += $efforts->consumed[$id];
                    }
                }
            }

            $consumedDate = empty($_POST['date']) ? date('Y-m-d H:i:s') : $_POST['date'];
            $consumedToday = $this->loadModel('effort')->getWorkloadToday($this->app->user->account, $taskTotalConsumed, 'insert', $consumedDate);

            $this->effort->batchCreate();
            if(dao::isError()) die(js::error(dao::getError()));

            if(isonlybody()) die(js::closeModal('parent.parent', '', "function(){if(typeof(parent.parent.refreshCalendar) == 'function'){parent.parent.refreshCalendar()}else{parent.parent.location.reload(true)}}"));
            die(js::locate($this->createLink('my', 'effort'), 'parent'));
        }
        parent::batchCreate($date, $userID);
    }
}
