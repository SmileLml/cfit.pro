<?php

include '../../control.php';
class mySecondorder extends secondorder
{
    public function importByQA()
    {
        if($_FILES)
        {
            $this->loadModel('common');

            $res = $this->common->importProgress('secondorder');

            die(json_encode($res));
        }

        $this->display();
    }
}
