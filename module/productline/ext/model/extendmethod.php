<?php
/**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/12/6
     * Time: 14:57
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getPairsLineAndName()
    {
        $lines = $this->dao->select('id,concat(concat(code,"_"),name)')->from(TABLE_PRODUCTLINE)
            ->where('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        return $lines;
    }


?>