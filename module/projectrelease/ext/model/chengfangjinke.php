<?php
/**
 * Project: chengfangjinke
 * Method: publish
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:23
 * Desc: This is the code comment. This method is called publish.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $releaseID
 */
public function publish($releaseID)
{
    $this->dao->update(TABLE_RELEASE)->set('isSent')->eq(1)->where('id')->eq($releaseID)->exec();
}

/**
 * Get list of releases.
 *
 * @param  int    $projectID
 * @param  string $type
 * @param  int    $productID
 * @param  int    $branch
 * @access public
 * @return array
 */
public function getList($projectID, $type = 'all', $productID = 0, $branch = 0 )
{
    return $this->dao->select('t1.*, t2.name as productName,t2.code as productCode,t3.id as buildID, t3.name as buildName, t3.execution, t4.name as executionName')
        ->from(TABLE_RELEASE)->alias('t1')
        ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
        ->leftJoin(TABLE_BUILD)->alias('t3')->on('t1.build = t3.id')
        ->leftJoin(TABLE_EXECUTION)->alias('t4')->on('t3.execution = t4.id')
        ->where('t1.project')->eq((int)$projectID)
        ->beginIF($type != 'all')->andWhere('t1.status')->eq($type)->fi()
        ->andWhere('t1.deleted')->eq(0)
        ->orderBy('t1.date DESC')
        ->fetchAll();
}

/**
 * Get release by id.
 *
 * @param  int    $releaseID
 * @param  bool   $setImgSize
 * @access public
 * @return object
 */
public function getByID($releaseID, $setImgSize = false)
{
    $release = $this->dao->select('t1.*, t2.id as buildID, t2.filePath, t2.scmPath, t2.name as buildName, t2.execution, t3.name as productName, t3.type as productType, t3.code as productCode')
        ->from(TABLE_RELEASE)->alias('t1')
        ->leftJoin(TABLE_BUILD)->alias('t2')->on('t1.build = t2.id')
        ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
        ->where('t1.id')->eq((int)$releaseID)
        ->orderBy('t1.id DESC')
        ->fetch();
    if(!$release) return false;

    $this->loadModel('file');
    $release = $this->file->replaceImgURL($release, 'desc');
    $release->files = $this->file->getByObject('release', $releaseID);
    if(empty($release->files))$release->files = $this->file->getByObjectExtra('build', $release->buildID,'verifyFiles');
    if($setImgSize) $release->desc = $this->file->setImgSize($release->desc);
    return $release;
}
