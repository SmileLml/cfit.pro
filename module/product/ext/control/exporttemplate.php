<?php
include '../../control.php';
class myProduct extends product
{
    /**
     * Export templet
     *
     * @access public
     * @return void
     */
    public function exportTemplate()
    {
        if($_POST)
        {
            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
            $users = $this->loadModel('user')->getPairs('nodeleted|noclosed');
            $lines = $this->product->getLinePairs();
            $this->post->set('POList', join(',', $users));
            $this->post->set('appList', join(',', $apps));
            $this->post->set('lineList', join(',', $lines));
            $this->post->set('typeList', join(',', $this->lang->product->typeList));
            $this->post->set('aclList', join(',', $this->lang->product->aclList));
            $this->post->set('listStyle',  array('app', 'line', 'PO', 'type', 'acl'));
            $this->post->set('extraNum',   0);

            $fields = array();
            //$fields['app']  = $this->lang->product->app;
            $fields['name'] = $this->lang->product->name;
            //$fields['line'] = $this->lang->product->line;
            $fields['code'] = $this->lang->product->code;
            $fields['enableTime'] = $this->lang->product->enableTime;
            $fields['comment'] = $this->lang->product->comment;
            //$fields['PO']   = $this->lang->product->PO;
            $fields['desc'] = $this->lang->product->desc;
            //$fields['type'] = $this->lang->product->type;
            $fields['acl']  = $this->lang->product->acl;

            $rows = array();
            $num  = (int)$this->post->num;

            $this->post->set('fields', $fields);
            $this->post->set('kind', 'product');
            $this->post->set('rows', array());
            $this->post->set('width', 40);
            $this->post->set('extraNum', $num);
            $this->post->set('fileName', 'productTemplet');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }
}
