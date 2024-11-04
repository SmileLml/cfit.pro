<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mymy extends my
{
    /**
     * Project: chengfangjinke
     * Method: work
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called work.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $mode
     * @param string $type
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @param tring $extra
     */
    public function work($mode = 'task', $type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $extra = '')
    {
        if(!($extra && $extra == 'asyncGetWaitCount')){
            $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
            $this->session->set('workWaitList', $this->app->getURI(true),'backlog');
        }
        $summaryList = $this->my->pendingSummary();
        $summaryList = json_encode($summaryList);
        echo $this->fetch('my', $mode, "type=$type&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID");
        echo <<<EOF
        <script>
        var summaryList = $summaryList;
        for(var newMode in summaryList)
        {
            var newTotal = summaryList[newMode];
            $('#subNavbar li[data-id=' + mode + ']').addClass('active');
            var pendingClass = '';
            if(newTotal > 0)
            {
                pendingClass = 'red-pending';
            }

            if(typeof rawMethod === 'string' && rawMethod == 'work') $('#subNavbar li[data-id=' + newMode + '] a').append('<span class="label label-light label-badge ' + pendingClass + '">' + newTotal + '</span>');
        }
        if(mode == 'audit')
        {
            var objectList = $summaryList;
            for(var newMode in objectList['reviewObject'])
            {
                var newTotal = objectList['reviewObject'][newMode];
                var pendingClass = '';
                if(newTotal > 0)
                {
                    pendingClass = 'red-pending';
                }

                if(typeof rawMethod === 'string' && rawMethod == 'work') $('#audit' + newMode).append(' <span class="label label-light label-badge ' + pendingClass +'">' + newTotal + '</span>');
            }
        }
       
</script>
EOF;
    }
}
?>
