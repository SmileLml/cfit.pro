<?php

class ProgressException extends Exception
{
}
class progressCommon extends commonModel
{
    /**
     * 导入工作进展跟踪信息
     * @param  mixed $model
     * @return array
     */
    public function importProgress($model)
    {
        $temp = $model;//临时变量
        $model = $model == 'demandinside' ? 'demand' : $model;
        $this->app->loadLang($model);
        $modelArr = array('deptorder','problem','secondorder');
        $secondLineDepStatusList         = array_flip($this->lang->$model->secondLineDepStatusList);
        $secondLineDepApprovedList       = array_flip(in_array($model,$modelArr) ? $this->lang->$model->secondLineDepApprovedList : $this->lang->$model->ifApprovedList );
        $secondLineDevelopmentRecordList = array_flip(in_array($model,$modelArr) ? $this->lang->$model->secondLineDevelopmentRecordList :$this->lang->$model->secondLineDepRecordList);
        $model = $temp ; //恢复传过来的model类型
        
        try {
            $rows = $this->loadModel('file')->import2Excel($this->lang->progress->importTitle);
            foreach ($rows as $key => $row) {
                $code                          = $row['A'];
                $secondLineDevelopmentPlan     = $row['B'];
                $progressQA                    = $row['C'];
                $secondLineDevelopmentStatus   = $row['D'];
                $secondLineDevelopmentApproved = $row['E'];
                $secondLineDevelopmentRecord   = $row['F'];

                if (empty($code)) {
                    throw new ProgressException(sprintf($this->lang->progress->importError, $key + 2, '单号'));
                }
                if (isset($data[$code])) {
                    throw new ProgressException(sprintf($this->lang->progress->importError, $key + 2, '单号重复'));
                }
                if (!empty($secondLineDevelopmentPlan)) {
                    $data[$code]['secondLineDevelopmentPlan'] = $secondLineDevelopmentPlan;
                }
                if (!empty($progressQA)) {
                    if ('demand' == $model || 'demandinside' == $model) {
                        $data[$code]['conclusion'] = $progressQA;
                    } else {
                        $data[$code]['progressQA'] = $progressQA;
                    }
                }
                if (!empty($secondLineDevelopmentStatus)) {
                    if (!isset($secondLineDepStatusList[$secondLineDevelopmentStatus])) {
                        throw new ProgressException(sprintf($this->lang->progress->importError, $key + 2, $this->lang->progress->secondLineDevelopmentStatus));
                    }
                    $data[$code]['secondLineDevelopmentStatus'] = $secondLineDepStatusList[$secondLineDevelopmentStatus];
                }
                if (!empty($secondLineDevelopmentApproved)) {
                    if (!isset($secondLineDepApprovedList[$secondLineDevelopmentApproved])) {
                        throw new ProgressException(sprintf($this->lang->progress->importError, $key + 2, $this->lang->progress->secondLineDevelopmentApproved));
                    }
                    $data[$code]['secondLineDevelopmentApproved'] = $secondLineDepApprovedList[$secondLineDevelopmentApproved];
                }
                if (!empty($secondLineDevelopmentRecord)) {
                    if (!isset($secondLineDevelopmentRecordList[$secondLineDevelopmentRecord])) {
                        throw new ProgressException(sprintf($this->lang->progress->importError, $key + 2, $this->lang->progress->secondLineDevelopmentRecord));
                    }
                    $data[$code]['secondLineDevelopmentRecord'] = $secondLineDevelopmentRecordList[$secondLineDevelopmentRecord];
                }
            }

            if (empty($data)) {
                throw new ProgressException($this->lang->file->fileContentEmpty);
            }

            $table      = $this->getTable($model);
            $codeList   = array_keys($data);
            $codeListDB = $this->dao
                ->select('id, code')
                ->from($table)
                ->where('`code`')->in($codeList)
                ->beginIF('problem' == $model)->andWhere('status')->ne('deleted')->fi()
                ->beginIF('secondorder' == $model || 'deptorder' == $model)->andWhere('deleted')->eq('0')->fi()
                ->beginIF('demand' == $model)->andWhere('sourceDemand')->eq(1)->fi()
                ->beginIF('demandinside' == $model)->andWhere('sourceDemand')->eq(2)->fi()
                ->fetchPairs();

            $diff = array_diff($codeList, $codeListDB);
            if (!empty($diff)) {
                throw new ProgressException(sprintf($this->lang->progress->codeError, implode('，', $diff)));
            }

            foreach ($data as $code => $info) {
                $this->dao->update($table)->data($info)->where('code')->eq($code)->exec();
            }

            return ['code' => 0, 'message' => 'success'];
        } catch (ImportException|ProgressException $e) {
            return ['code' => 1, 'message' => $e->getMessage()];
        }
    }

    /**
     * 根据模块名获取对应表名
     * @param $model
     * @return false|string
     */
    private function getTable($model)
    {
        switch ($model) {
            case 'problem':
                return TABLE_PROBLEM;
            case 'secondorder':
                return TABLE_SECONDORDER;
            case 'deptorder':
                return TABLE_DEPTORDER;
            case 'demandinside':
            case 'demand':
                return TABLE_DEMAND;
            default:
                return false;
        }
    }
}
