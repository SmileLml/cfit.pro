<?php
include '../../control.php';
class myProgramPlan extends programplan
{
    /**
     * Browse program plans.
     *
     * @param  int     $projectID
     * @param  int     $productID
     * @param  string  $type
     * @param  string  $orderBy
     * @param  int     $baselineID
     * @access public
     * @return void
     */
    public function showImport($projectID, $subtask = 0)
    {
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        if($_POST)
        {
            $this->programplan->createFromImport($projectID);
            unlink($tmpFile);
            die(js::locate($this->createLink('project', 'execution', "status=all&projectID=$projectID&orderBy=id_asc"), 'parent'));
        }

        $this->loadModel('project')->setMenu($projectID);

        if(file_exists($tmpFile))
        {
            $taskData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            $rows   = $this->file->getRowsFromExcel($file);
            $header = $rows[1];

            $kman       = -1;
            $klevel     = -1;
            $kwbs       = -1;
            $kname      = -1;
            $kbegin     = -1;
            $kend       = -1;
            $kduration  = -1;
            $kmilestone = -1;
            foreach($header as $key => $col)
            {
                if(strpos($col, 'WBS') !== FALSE)      { $kwbs       = $key; continue; };
                if(strpos($col, '大纲级别') !== FALSE) { $klevel     = $key; continue; };
                if(strpos($col, '开始时间') !== FALSE) { $kbegin     = $key; continue; };
                if(strpos($col, '完成时间') !== FALSE) { $kend       = $key; continue; };
                if(strpos($col, '资源') !== FALSE)     { $kman       = $key; continue; };
                if(strpos($col, '名称') !== FALSE)     { $kname      = $key; continue; };
                if(strpos($col, '工期') !== FALSE)     { $kduration  = $key; continue; };
                if(strpos($col, '里程碑') !== FALSE)   { $kmilestone = $key; continue; };
            }

            $taskData = [];

            $begin = '2222-01-01';
            $end   = '1970-01-01';
            foreach($rows as $currentRow => $row)
            {
                if($currentRow == 1) continue;
                if(!$row[2]) continue; //名称为空的直接跳过

                $data            = new stdclass();
                $data->level     = $row[$klevel];
                $data->wbs       = $row[$kwbs];
                $data->name      = $row[$kname];
                $data->begin     = date('Y-m-d', strtotime($row[$kbegin]));
                $data->end       = date('Y-m-d', strtotime($row[$kend]));
                $data->resource  = !isset($row[$kman]) ? '' : $row[$kman];
                $data->milestone = !empty($row[$kmilestone]) && $row[$kmilestone] == '是' ? 1 : 0;

                // 获取大纲级别前2级为阶段。
                $data->type = $row[$klevel] <= 2 ? 'stage' : 'task';

                // 如果是task里程碑是否。
                if($data->type == 'task') $data->milestone = 0;

                $taskData[] = $data;

                // 二级阶段判断。
                if($data->level == 2)
                {
                    // 判断阶段的下一个数据是否为任务，如果不是，则判断是否创建同名的子任务。
                    $nextDataIndex = $currentRow + 1;
                    $nextData      =  empty($rows[$nextDataIndex]) ? null : $rows[$nextDataIndex];

                    // 大于2说明阶段下面有任务，反之亦然。
                    if(empty($nextData) or $nextData[$klevel] <= 2)
                    {
                        if($subtask != 1) $subtask = 3; // 需要提示为阶段创建同名子任务。

                        // 为阶段创建同名子任务。
                        if($subtask == 1)
                        {
                            $subTaskData = clone $data;
                            $subTaskData->level = 3;
                            $subTaskData->wbs   = $subTaskData->wbs . '.1';
                            $subTaskData->milestone = 0;
                            $subTaskData->type      = 'task';
                            $taskData[] = $subTaskData;
                        }
                    }
                }

                if($data->begin < $begin) $begin = $data->begin;
                if($data->end > $end)     $end   = $data->end;
            }

            foreach($taskData as $key => $data)
            {
                $taskData[$key]->duration = $this->programplan->days($data->begin, $data->end, array());
            }
        }

        $this->view->title = $this->lang->programplan->import;
        $this->view->rows  = $taskData;
        $this->view->projectID = $projectID;
        $this->view->subtask   = $subtask;
        $this->display();
    }
}
