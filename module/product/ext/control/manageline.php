<?php
include '../../control.php';
class myProduct extends product 
{
    /**
     * Project: chengfangjinke
     * Method: manageLine
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:56
     * Desc: This is the code comment. This method is called manageLine.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function manageLine()
    {
        $this->app->loadLang('tree');
        if($_POST)
        {
            $this->product->manageLine();
            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadLines();"));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('manageLine')));
        }

        $this->view->title      = $this->lang->product->line;
        $this->view->position[] = $this->lang->product->line;

        $this->view->programs = array('') + $this->loadModel('program')->getTopPairs();
        $this->view->lines    = $this->dao->select('*')->from(TABLE_MODULE)->where('type')->eq('line')->andWhere('deleted')->eq(0)->orderBy('`order`')->fetchAll();
        $this->display();
    }
}
