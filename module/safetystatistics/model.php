<?php

class safetystatisticsModel extends model
{
    /**
     * 修改安全评分基础参数
     * @return string|true
     */
    public function editParams()
    {
        $data = fixer::input('post')->get();

        $insert = [];
        $sum    = 0;
        foreach ($data->target as $key => $value) {
            foreach ($value as $k => $v) {
                $v = (float)$v;
                if ($v <= 0) {
                    return dao::$errors[] = sprintf(
                        $this->lang->safetystatistics->error->weightNum,
                        $this->lang->safetystatistics->targetTwoList[$key],
                        $this->lang->safetystatistics->targetThreeList[$k]
                    );
                }
                $insert[] = [
                    'type'        => 1, //'权重类型  1 指标权重；2 标定值',
                    'targetOne'   => $this->lang->safetystatistics->target[$key],
                    'targetTwo'   => $key,
                    'targetThree' => $k,
                    'weightNum'   => $v,
                ];
                $sum += $v;
            }
        }
        if (abs($sum - 1.0) >= 0.0000000001) {
            return dao::$errors[] = $this->lang->safetystatistics->error->weightSum;
        }

        foreach ($data->calibration as $key => $value) {
            $value = (float)$value;
            if ($value <= 0) {
                return dao::$errors[] = sprintf(
                    $this->lang->safetystatistics->error->calibration,
                    $this->lang->safetystatistics->calibration[$key]
                );
            }
            $insert[] = [
                'type'        => 2, //'权重类型  1 指标权重；2 标定值',
                'targetOne'   => '',
                'targetTwo'   => $key,
                'targetThree' => '',
                'weightNum'   => $value,
            ];
        }

        foreach ($insert as $value) {
            $arr = $this->dao
                ->select('*')
                ->from(TABLE_SAFETY_PARAM)
                ->where('type')->eq($value['type'])
                ->andWhere('targetTwo')->eq($value['targetTwo'])
                ->beginIF(1 == $value['type'])
                ->andWhere('targetOne')->eq($value['targetOne'])
                ->andWhere('targetThree')->eq($value['targetThree'])
                ->FI()
                ->fetch();
            if (!empty($arr)) {
                $res = $this->dao->update(TABLE_SAFETY_PARAM)
                    ->set('weightNum')->eq($value['weightNum'])
                    ->where('id')->eq($arr->id)
                    ->exec();
            } else {
                $res = $this->dao
                    ->insert(TABLE_SAFETY_PARAM)
                    ->data($value)
                    ->exec();
            }
        }

        return true;
    }

    /**
     * 获取基础参数
     * @return array
     */
    public function getParams()
    {
        $data = $this->dao->select('*')->from(TABLE_SAFETY_PARAM)->orderBy('id_desc')->fetchAll();

        if (empty($data)) {
            return [];
        }

        $list = [];
        foreach ($data as $item) {
            //判断数据权重类型  1 指标权重；2 标定值
            if (1 == $item->type) {
                if (isset($list['targetOne'][$item->targetOne])) {
                    $list['targetOne'][$item->targetOne] += $item->weightNum;
                } else {
                    $list['targetOne'][$item->targetOne] = $item->weightNum;
                }

                if (isset($list['targetTwo'][$item->targetTwo]['weightNum'])) {
                    $list['targetTwo'][$item->targetTwo]['weightNum'] += $item->weightNum;
                } else {
                    $list['targetTwo'][$item->targetTwo]['weightNum'] = $item->weightNum;
                }

                $list['targetTwo'][$item->targetTwo]['child'][$item->targetThree] = $item->weightNum;
            } else {
                $list['calibration'][$item->targetTwo] = $item->weightNum;
            }
        }

        return $list;
    }

    /**
     * 获取bug数量
     * @return array
     */
    public function getBugNum()
    {
        $childType = "(CASE childType
        WHEN 'a1' THEN 'source'
        WHEN 'a2' THEN 'module'
        WHEN 'a3' THEN 'master'
		WHEN 'a4' THEN 'permeate'
		WHEN 'a5' THEN 'cip'
		WHEN 'a6' THEN 'other' ELSE 'null'
		END ) AS childType";
        $severity = "(CASE severity
        WHEN '1' THEN 'severity'
		WHEN '2' THEN 'severity'
		WHEN '3' THEN 'ordinary'
		WHEN '4' THEN 'slight'
		WHEN '5' THEN 'suggest' ELSE 'null'
		END ) AS severity";

        $data = $this->dao
            ->select("applicationID, childType, {$childType}, {$severity}, count(*) AS num")
            ->from(TABLE_BUG)
            ->where('type')->eq('security')
            ->andWhere()
            ->markleft(1)
            ->where('status')->eq('active')
            ->orWhere()
            ->markleft(1)
            ->where('status')->eq('resolved')
            ->andWhere('resolution')->in(['bydesign', 'external', 'notrepro', 'postponed', 'willnotfix', 'tostory', 'tostorytwo', ''])
            ->markright(2)
            ->groupBy('applicationID, childType, severity')
            ->fetchAll();

        if (empty($data)) {
            return [];
        }

        $list = [];
        foreach ($data as $item) {
            if (!isset($list[$item->applicationID][$item->childType][$item->severity])) {
                $list[$item->applicationID][$item->childType][$item->severity] = $item->num;
            } else {
                $list[$item->applicationID][$item->childType][$item->severity] += $item->num;
            }
        }

        return $list;
    }

    /**
     * 计算系统安全评分
     * @return true
     */
    public function createScore()
    {
        $appData    = $this->getAppPairs();
        $bugData    = $this->getBugNum();
        $paramsData = $this->getParams();

        $data = [];
        foreach ($appData as $id => $name) {
            $details   = [];
            $risk      = 0.00; //综合风险值
            $offsetAll = 0;
            foreach ($this->lang->safetystatistics->targetTwoList as $key => $value) {
                $child     = [];
                $riskValue = 0.00; //单项风险值
                foreach ($this->lang->safetystatistics->targetThreeList as $k => $v) {
                    $child[$k] = $bugData[$id][$key][$k] ?? 0;
                    $riskValue += $child[$k] * $paramsData['targetTwo'][$key]['child'][$k];
                }

                //如果有严重问题初始分数减去20
                $offset = $child['severity'] > 0 ? 20 : 0;
//                if (1 == bccomp($riskValue, $paramsData['calibration'][$key], 5)) {
                if (round($riskValue, 5) > round($paramsData['calibration'][$key], 5)) {
                    $score = max(80 - $offset - 10 * log10(1 + $riskValue - $paramsData['calibration'][$key]), 0);
                } else {
                    $score = 100 - $offset - (20 / $paramsData['calibration'][$key] * $riskValue);
                }

                $details[$key] = [
                    'riskValue' => number_format($riskValue, 2),
                    'score'     => number_format($score, 2),
                    'child'     => $child,
                ];

                if ($child['severity'] > 0) {
                    $offsetAll = 20;
                }
                $risk += (100 - $score) * $paramsData['targetTwo'][$key]['weightNum'];
            }

//            if (1 == bccomp($risk, $paramsData['calibration']['composite'], 5)) {
            if (round($risk, 5) > round($paramsData['calibration']['composite'], 5)) {
                $score = max(80 - $offsetAll - 10 * log10(1 + $risk - $paramsData['calibration']['composite']), 0);
            } else {
                $score = 100 - $offsetAll - (20 / $paramsData['calibration']['composite'] * $risk);
            }

            //如果用其他类型只展示bug数量不计算评分
            if (isset($bugData[$id]['other'])) {
                foreach ($this->lang->safetystatistics->targetThreeList as $k => $v) {
                    $child[$k] = $bugData[$id]['other'][$k] ?? 0;
                }
                $details['other'] = [
                    'riskValue' => '',
                    'score'     => '',
                    'child'     => $child,
                ];
            }

            $data[] = [
                'appId'     => $id,
                'riskValue' => number_format($risk, 2),
                'score'     => number_format($score, 2),
                'details'   => $details,
            ];
        }

        return $this->addScore($data);
    }

    /**
     * 获取系统名称
     * @return mixed
     */
    public function getAppPairs()
    {
        return $this->dao
            ->select('id, name')
            ->from(TABLE_APPLICATION)
            ->where('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetchPairs();
    }

    /**
     * 添加或修改系统安全评分
     * @param $data
     * @return true
     */
    public function addOrEditScore($data)
    {
        $data['details'] = json_encode($data['details']);

        $arr = $this->dao
            ->select('appId,riskValue,score,details')
            ->from(TABLE_SAFETY_SCORE)
            ->where('appId')->eq($data['appId'])
            ->fetch();
        if (!empty($arr)) {
            if ($data != $arr) {
                return $this->dao
                    ->update(TABLE_SAFETY_SCORE)
                    ->set('riskValue')->eq($data['riskValue'])
                    ->set('score')->eq($data['score'])
                    ->set('details')->eq($data['details'])
                    ->where('appId')->eq($data['appId'])
                    ->exec();
            }
        } else {
            $this->dao->insert(TABLE_SAFETY_SCORE)->data($data)->exec();

            return $this->dao->lastInsertID();
        }

        return true;
    }

    /**
     * 重新生成安全评分
     * @param $data
     * @return bool
     */
    public function addScore($data)
    {
        $this->dao->begin();  //开启事务
        //重新生成报表要先清空数据
        $res = $this->dao->query('TRUNCATE TABLE ' . TABLE_SAFETY_SCORE);
        if($res == false){
            $this->dao->rollBack();
            return false;
        }

        foreach ($data as $item){
            $item['details'] = json_encode($item['details']);
            $res = $this->dao->insert(TABLE_SAFETY_SCORE)->data($item)->exec();
            if(!$res){
                $this->dao->rollBack();
                return false;
            }
        }

        $this->dao->commit(); //提交事务

        return true;
    }
}
