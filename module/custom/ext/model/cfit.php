<?php
public function setChildTypeList($module, $field)
{
    $data         = array();
    $defaultIndex = 0;
    $identical    = array();

    foreach($_POST['typeList'] as $index => $value)
    {
//        if(!trim($value)) continue;

        $childTypeListKey   = trim($_POST['childTypeListKey'][$index]);
        $childTypeListValue = trim($_POST['childTypeListValue'][$index]);

        if(trim($value) === '' && !$childTypeListKey && !$childTypeListValue){
            continue;
        }

        $defaultIndex ++;
        if(empty($childTypeListKey) or empty($childTypeListValue))
        {
            $msg = sprintf($this->lang->custom->$module->childTypeEmpty, $defaultIndex);
            dao::$errors[] = $msg;
            return false;
        }

        if(trim($value) === ''){
            $msg = sprintf($this->lang->custom->$module->typeListEmpty, $defaultIndex);
            dao::$errors[] = $msg;
            return false;
        }

        if(!empty($identical[$childTypeListKey]))
        {
            dao::$errors[] = $this->lang->custom->$module->childTypeIdentical;
            return false;
        }

        $data[$value][$childTypeListKey] = $childTypeListValue;
        $identical[$childTypeListKey]    = $childTypeListKey;
    }

    $data = json_encode($data);
    $this->setItem("all.{$module}.childTypeList.all.0", $data);

    //$this->loadModel('setting');
    //$enableChildType = $_POST['enableChildType'];
    //$this->setting->setItem('system.common.global.enableChildType', $enableChildType);;
}

public function setGuide($module, $field)
{
    if(!empty($_FILES['guideFileName']))
    {
        $this->loadModel('file');
        $oldFile = $this->loadModel('file')->getByObject('guide', '9999');
        if(!empty($oldFile))
        {
            foreach($oldFile as $fileID => $file)
            {
                $this->dao->delete()->from(TABLE_FILE)->where('id')->eq($file->id)->exec();

                $fileRecord = $this->dao->select('id')->from(TABLE_FILE)->where('pathname')->eq($file->pathname)->fetch();
                if(empty($fileRecord)) @unlink($file->realPath);
            }
        }

        $file = $this->file->saveUpload('guide', '9999', '', 'guideFileName');
    }
}

public function getGuideFile()
{
    $files = $this->loadModel('file')->getByObject('guide', '9999');
    $file  = '';
    if(!empty($files))
    {
        $file = array_shift($files);
    }
    return $file;
}
