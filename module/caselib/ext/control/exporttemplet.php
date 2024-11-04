<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mycaselib extends caselib
{
    public function exportTemplet($libID)
    {
        $this->loadModel('testcase');
        if($_POST)
        {
            $this->caselib->setListValue($libID);

            $fields['module']       = $this->lang->testcase->module;
            $fields['title']        = $this->lang->testcase->title;
            $fields['precondition'] = $this->lang->testcase->precondition;
            $fields['stepDesc']     = $this->lang->testcase->stepDesc;
            $fields['stepExpect']   = $this->lang->testcase->stepExpect;
            $fields['keywords']     = $this->lang->testcase->keywords;
            $fields['pri']          = $this->lang->testcase->pri;
            $fields['types']        = $this->lang->testcase->type;
            $fields['stage']        = $this->lang->testcase->stage;
            $fields['categories']   = $this->lang->testcase->categories;

            $width['module']       = 20;
            $width['precondition'] = 30;
            $this->config->excel->cellHeight = 40;

            $this->post->set('fields', $fields);
            $this->post->set('kind', 'caselib');
            $this->post->set('rows', array());
            $this->post->set('width', $width);
            $this->post->set('extraNum', $this->post->num);
            $this->post->set('fileName', 'libTemplate');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }
}
