<?php
error_reporting(1);
define('RUN_MODE', 'cli');
ob_start();
include '../framework/router.class.php';
include '../framework/cli.class.php';
include '../framework/model.class.php';
include '../framework/helper.class.php';
$app = router::createApp('pms', dirname(dirname(__FILE__)), 'router');
$common = $app->loadCommon();

/**
 * 记录日志
 * @param $line
 */
function saveLog($line, $model = 'cli', $func = 'run'){
    if(is_array($line) || is_object($line))
    {
        $line = json_encode($line, JSON_UNESCAPED_UNICODE);
    }
    $line = '['.date('H:i:s').']-'. $model .'-'. $func . ':: '.$line .PHP_EOL;
    $logPath = dirname(dirname(__FILE__)).'/www/data/log/'.date('Ym').'/';
    if(!is_dir($logPath)) mkdir($logPath, 0777, true);
    $logFile = $logPath.'cron-'.date('Y-m-d').'-'.$model.'-'.$func.'.log';
    file_put_contents($logFile, $line, FILE_APPEND);
}


/**
 * 文件锁（其实锁定时间1分钟，与参数2没有关系）
 */
function getLock($className = '', $min = 1)
{
    $exp = date('YmdHi', strtotime("+". $min ."min"));
    $lock = '/tmp/phplock-'.$className.'-'. $exp .'.lock';
    if(file_exists($lock)){
        saveLog('locked');
        die('locked');
    }
    file_put_contents($lock, date('Y-m-d H:i：s'));
    return $lock;
}

/**
 *设置锁定一定的时间
 *
 * @param string $className
 * @param int $min
 * @return string
 */
function getTimeLock($className = '', $min = 1){
    $currentTime = time();
    $expTime = date('Y-m-d H:i:s', strtotime("+". $min ."min"));
    $lock = '/tmp/phplock-'.$className.'-'. 'Time' .'.lock';
    if(file_exists($lock)){
        $lastExpTime = file_get_contents($lock);
        $lastExpTime = strtotime($lastExpTime);
        if($lastExpTime > $currentTime){ //上一次记录的时间大于当前时间，则不记录锁定
            saveLog('locked');
            die('locked');
        }
    }
    file_put_contents($lock, $expTime);
    return $lock;
}


/**
 *文件锁(永久锁，确保传的参数名称唯一)
 *
 * @param string $className
 * @return string
 */
function getPermanentLock($className = '')
{
    $lock = '/tmp/phplock-'.$className .'.lock';
    if(file_exists($lock)){
        saveLog('locked');
        die('locked');
    }
    file_put_contents($lock, date('Y-m-d H:i:s'));
    return $lock;
}
/**
 * 执行完解锁
 */
function unlock($lock)
{
    unlink($lock);
}