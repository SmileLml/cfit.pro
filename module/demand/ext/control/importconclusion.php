<?php

include '../../control.php';
class myDemand extends demand
{
    public function importConclusion()
    {
        if($_FILES) {
            $this->loadModel('common');

            $res = $this->common->importProgress('demand');

            die(json_encode($res));
        }

        $this->display();
    }
}
