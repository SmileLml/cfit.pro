<?php
include '../../control.php';
class myProduct extends product
{
    /**
     * Project: chengfangjinke
     * Method: showImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:56
     * Desc: This is the code comment. This method is called showImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function showImport()
    {
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        if($_POST)
        {
            $this->product->createFromImport();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            unlink($tmpFile);
            die(js::locate(inlink('all'), 'parent'));
        }

        $productLang = $this->lang->product;
        $fields      = array();
        $fields[]    = 'app';
        $fields[]    = 'name';
        $fields[]    = 'line';
        $fields[]    = 'code';
        $fields[]    = 'enableTime';
        $fields[]    = 'comment';
        $fields[]    = 'PO';
        $fields[]    = 'desc';
        $fields[]    = 'type';
        $fields[]    = 'acl';

        $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
        $users = $this->loadModel('user')->getPairs('nodeleted|noclosed');
        $lines = $this->product->getLinePairs();
        $productLang->POList   = $users;
        $productLang->appList  = $apps;
        $productLang->lineList = $lines;

        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($productLang->$fieldName) ? $productLang->$fieldName : $fieldName;
            unset($fields[$key]);
        }

        $rows = $this->file->getRowsFromExcel($file);
        $productData = array();
        foreach($rows as $currentRow => $row)
        {
            $product = new stdclass();
            foreach($row as $currentColumn => $cellValue)
            {
                if($currentRow == 1)
                {
                    $field = array_search($cellValue, $fields);
                    $columnKey[$currentColumn] = $field ? $field : '';
                    continue;
                }

                $cellValue = preg_replace('/_x([0-9a-fA-F]{4})_/', '', $cellValue);
                if(empty($columnKey[$currentColumn])) continue;
                $field = $columnKey[$currentColumn];

                // check empty data.
                if(empty($cellValue)) continue;

                elseif(in_array($field, array('app', 'line', 'PO', 'type', 'acl')))
                {
                    if(strrpos($cellValue, '(#') === false)
                    {
                        $product->$field = $cellValue;
                        if(!isset($productLang->{$field . 'List'}) or !is_array($productLang->{$field . 'List'})) continue;

                        /* when the cell value is key of list then eq the key. */
                        $listKey = array_keys($productLang->{$field . 'List'});
                        unset($listKey[0]);
                        unset($listKey['']);

                        $fieldKey = array_search($cellValue, $productLang->{$field . 'List'});
                        if($fieldKey) $product->$field = $fieldKey;
                    }
                    else
                    {
                        $id = trim(substr($cellValue, strrpos($cellValue,'(#') + 2), ')');
                        $product->$field = $id;
                    }
                }
                else
                {
                    $product->$field = $cellValue;
                }
            }

            if(empty($product->name)) continue;
            $productData[$currentRow] = $product;
            unset($product);
        }

        $this->view->title      = $this->lang->product->common . $this->lang->colon . $this->lang->product->showImport;
        $this->view->position[] = $this->lang->product->showImport;
        $this->view->rows       = $productData;
        $this->view->apps       = $apps;
        $this->view->lines      = $lines;
        $this->view->users      = $users;

        $this->display();
    }
}
