<?php
/**
 * ZenTaoPHP的control类。
 * The control class file of ZenTaoPHP framework.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/**
 * control基类继承与baseControl，所有模块的control类都派生于它。
 * The base class of control extends baseControl.
 *
 * @package framework
 */
include dirname(__FILE__) . '/base/cli.class.php';
class control extends baseControl
{
    /**
     * Check requiredFields and set exportFields for workflow.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @param  string $appName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '', $appName = '')
    {
        parent::__construct($moduleName, $methodName, $appName);
        $className = get_class($this);
        $fileName = $className.'.php';
        $isStop = $this->loadModel('cronconfig')->getCronIsStop($fileName);
        if($isStop){
            die($className.'脚本当前停止');
        }
    }


    /**
     * 企业版部分功能是从然之合并过来的。然之代码中调用loadModel方法时传递了一个非空的appName，在禅道中会导致错误。
     * 调用父类的loadModel方法来避免这个错误。
     * Some codes merged from ranzhi called the function loadModel with a non-empty appName which causes an error in zentao.
     * Call the parent function with empty appName to avoid this error.
     *
     * @param   string  $moduleName 模块名，如果为空，使用当前模块。The module name, if empty, use current module's name.
     * @param   string  $appName    The app name, if empty, use current app's name.
     * @access  public
     * @return  object|bool 如果没有model文件，返回false，否则返回model对象。If no model file, return false, else return the model object.
     */
    public function loadModel($moduleName = '', $appName = '')
    {
        return parent::loadModel($moduleName);
    }

}
