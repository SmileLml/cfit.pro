<?php
/**
 * Project: chengfangjinke
 * Method: export2Word
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:42
 * Desc: This is the code comment. This method is called export2Word.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $name
 * @param $phpword
 * @return mixed
 */
public function export2Word($name, $phpword)
{
    return $this->loadExtension('cfjk')->export2Word($name, $phpword);
}

/**
 * excel导入
 * @param $title
 * @return mixed
 */
public function import2Excel($title)
{
    return $this->loadExtension('cfjk')->import2Excel($title);
}
