<?php
class zentaomaxProject extends projectModel
{
    /**
     * Set menu of project module.
     *
     * @param  int    $objectID
     * @access public
     * @return void
     */
    public function setMenu($objectID, $params = array())
    {
        $project = $this->getByID($objectID);

        /* Replace waterfall lang. */
        if($project->model == 'waterfall')
        {
            global $lang;
            $this->loadModel('execution');
            $lang->executionCommon = $lang->project->stage;

            include $this->app->getModulePath('', 'execution') . 'lang/' . $this->app->getClientLang() . '.php';
        }

        return parent::setMenu($objectID, $params);
    }
}
