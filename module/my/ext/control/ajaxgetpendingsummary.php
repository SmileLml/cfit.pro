<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mymy extends my
{
    /**
     * 异步获得新的待办数量
     */
    public function ajaxGetPendingSummary()
    {
        $summaryList = $this->my->pendingSummary();
        $this->send($summaryList);
    }
}
?>
