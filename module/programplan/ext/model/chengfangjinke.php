<?php
/**
 * Project: chengfangjinke
 * Method: createFromImport
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:13
 * Desc: This is the code comment. This method is called createFromImport.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $projectID
 * @return mixed
 */
public function createFromImport($projectID)
{
    return $this->loadExtension('chengfangjinke')->createFromImport($projectID);
}

/**
 * Project: chengfangjinke
 * Method: getDataForGantt
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:13
 * Desc: This is the code comment. This method is called getDataForGantt.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $executionID
 * @param $productID
 * @param int $baselineID
 * @param string $selectCustom
 * @param bool $returnJson
 * @return mixed
 */
public function getDataForGantt($executionID, $productID, $baselineID = 0, $selectCustom = '', $returnJson = true)
{
    return $this->loadExtension('chengfangjinke')->getDataForGantt($executionID, $productID, $baselineID, $selectCustom, $returnJson);
}

/**
 * Project: chengfangjinke
 * Method: getHoliday
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:13
 * Desc: This is the code comment. This method is called getHoliday.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $begin
 * @param $end
 * @return mixed
 */
public function getHoliday($begin, $end)
{
    return $this->loadExtension('chengfangjinke')->getHoliday($begin, $end);
}

/**
 * Project: chengfangjinke
 * Method: workDays
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:14
 * Desc: This is the code comment. This method is called workDays.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $begin
 * @param $end
 * @param $holiday
 * @return mixed
 */
public function workDays($begin, $end, $holiday)
{
    return $this->loadExtension('chengfangjinke')->workDays($begin, $end, $holiday);
}

/**
 * Project: chengfangjinke
 * Method: days
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:14
 * Desc: This is the code comment. This method is called days.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $begin
 * @param $end
 * @param $holiday
 * @return mixed
 */
public function days($begin, $end, $holiday)
{
    return $this->loadExtension('chengfangjinke')->days($begin, $end, $holiday);
}

/**
 * Project: chengfangjinke
 * Method: createSubStage
 * User: Tony Stark
 * Year: 2022
 * Date: 2022/03/24
 * Time: 17:14
 * Desc: This is the code comment. This method is called days.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $projectID
 * @param $executionID
 * @return mixed
 */

public function createSubStage($projectID, $executionID ,$flag = null)
{
    return $this->loadExtension('chengfangjinke')->createSubStage($projectID, $executionID,$flag);
}
