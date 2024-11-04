<?php
class implementionplanModel extends model
{

    /**
     * Project: chengfangjinke
     * Method: getLibList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:20
     * Desc: This is the code comment. This method is called getLibList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function getList($orderBy, $pager,$projectID)
    {
        $implement = $this->dao->select('*')->from(TABLE_IMPLEMENTIONPLAN)
            ->where('deleted')->eq('0')
            ->andWhere('projectID')->eq($projectID)
            ->orderBy($orderBy)
            ->page($pager)->fetchAll('id');

        foreach ($implement as $key => $item) {
            $implement[$key]->files = $this->loadModel('file')->getByObject('implementionplan', $item->id);
        }

        return $implement;
    }

    /**
     * 上传实施计划
     * @return mixed
     */
    public function create($projectID)
    {
        $file = $this->loadModel('file')->getUpload('files');
        if(!$file){
            return dao::$errors['file'] = $this->lang->implementionplan->fileEmpty;
        }
        $data = fixer::input('post')
            ->remove('file,uid')
            ->stripTags($this->config->implementionplan->editor->uploadplan['id'], $this->config->allowedTags)
            ->get();
        $data->uploadTime   = helper::now(); //上传时间
        $data->name         = $file[0]['title'];//项目工程实施计划名称
        $data->uploadPerson = $this->app->user->account;//上传人
        $data->projectID = $projectID;
        $this->dao->insert(TABLE_IMPLEMENTIONPLAN)->data($data)
            ->autoCheck()->batchCheck($this->config->implementionplan->uploadplan->requiredFields, 'notempty')
            ->exec();
        $implementionID = $this->dao->lastInsertId();

        if (!dao::isError())
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $implementionID, 'implementionplan');
            $this->file->saveUpload('implementionplan', $implementionID);
        }
        return $implementionID;
    }

    /**
     *根据id获取数据
     * @param $implementID
     * @return mixed
     */
    public function getByID($implementID)
    {
        $implement = $this->dao->select("*")->from(TABLE_IMPLEMENTIONPLAN)->where('id')->eq($implementID)->fetch();
        $implement->files = $this->loadModel('file')->getByObject('implementionplan', $implementID);
        return $implement;
    }
}
