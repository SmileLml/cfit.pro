<?php
class excelTestcase extends testcaseModel
{
    public function setListValue($applicationID, $productID, $branch = 0, $projectID = 0)
    {
        $typeList   = $this->lang->testcase->typeList;
        $priList    = $this->lang->testcase->priList;
        $stageList  = $this->lang->testcase->stageList;
        $statusList = $this->lang->testcase->statusList;
        $listFields = $this->config->testcase->export->listFields;

        if(empty($productID)) $productID = 'na';
        $products = $this->loadModel('rebirth')->getProductPairs($applicationID, true);
        $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
        $modules  = array();
        $stories  = array();

        $application = $this->rebirth->getApplicationByID($applicationID);
        $this->post->set('applicationID', $application->name . "(#$application->id)");

        if($projectID)
        {
            $project = $this->loadModel('project')->getByID($projectID);
            unset($this->config->testcase->export->listFields[1]);
            $this->post->set('project', $project->name . "(#$projectID)");

            $executionList = $this->project->getExecutionByAvailable($projectID);
            $executions    = array();
            foreach($executionList as $id => $execution) $executions[$id] = "$execution(#$id)";

            $this->config->testcase->export->listFields[] = 'execution';
            $this->post->set('executionList', $executions);

            if($productID == 'na' or is_numeric($productID))
            {
                $this->post->set('product', zget($products, $productID) . "(#$productID)");

                $modules = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, $branch);
                $stories = $this->loadModel('story')->getProductStories($productID, $branch);
                foreach($modules as $id => $module) $modules[$id] .= "(#$id)";
                foreach($stories as $id => $story)  $stories[$id]  = "$story->title(#$story->id)";
            }
            else
            {
                 $this->config->testcase->export->listFields[] = 'product';
                 foreach($products  as $id => $product) $products[$id] = "$product(#$id)";
                 $this->post->set('productList', $products);
            }
        }
        else
        {
            if($productID == 'na' or is_numeric($productID))
            {
                $this->post->set('product', zget($products, $productID) . "(#$productID)");

                $modules = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, $branch);
                $stories = $this->loadModel('story')->getProductStories($productID, $branch);
                foreach($modules as $id => $module) $modules[$id] .= "(#$id)";
                foreach($stories as $id => $story)  $stories[$id]  = "$story->title(#$story->id)";
            }
            else
            {
                 $this->config->testcase->export->listFields[] = 'product';
                 foreach($products  as $id => $product) $products[$id] = "$product(#$id)";
                 $this->post->set('productList', $products);
            }

            if(empty($projects)) $projects[0] = '';
            foreach($projects  as $id => $project) $projects[$id] = "$project(#$id)";
            $this->post->set('projectList', $projects);
        }

        unset($typeList['']);
        unset($stageList['']);
        unset($statusList['']);

        $this->post->set('moduleList', $modules);
        $this->post->set('storyList',  $stories);
        $this->post->set('typesList',  join(',', $typeList));
        $this->post->set('priList',    join(',', $priList));
        $this->post->set('stageList',  join(',', $stageList));
        $this->post->set('statusList', $statusList);
        $this->post->set('listStyle',  $this->config->testcase->export->listFields);
        $this->post->set('extraNum',   0);
        if(!empty($storyList)) $this->post->set('cascade', array('story' => 'module'));
    }
}
