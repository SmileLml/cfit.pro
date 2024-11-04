<?php

include '../../control.php';
class myDemandinside extends demandinside
{
    public function importConclusion()
    {
        if($_FILES)
        {
            $this->loadModel('common');

            $res = $this->common->importProgress('demandinside');

            die(json_encode($res));
        }

        $this->display();
    }
}
