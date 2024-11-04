<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mymy extends my
{
    /**
     * Project: chengfangjinke
     * Method: contribute
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called contribute.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $mode
     * @param string $type
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function contribute($mode = 'task', $type = 'openedBy', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if(($mode == 'issue' or $mode == 'risk') and $type == 'openedBy') $type = 'createdBy';

        echo $this->fetch('my', $mode, "type=$type&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID");
        echo <<<EOF
        <script>
            $('#subNavbar li[data-id=' + mode + ']').addClass('active');
</script>;
EOF;
    }
}
