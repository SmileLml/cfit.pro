<?php

include '../../control.php';
class myProblem extends problem
{
    public function importByQA()
    {
        if($_FILES)
        {
            $this->loadModel('common');

            $res = $this->common->importProgress('problem');

            die(json_encode($res));
        }

        $this->display();
    }
}
