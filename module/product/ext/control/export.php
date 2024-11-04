<?php
include '../../control.php';
class myProduct extends product
{
    /**
     * Export product.
     *
     * @param  string    $status
     * @param  string    $orderBy
     * @access public
     * @return void
     */
    public function export($status, $orderBy)
    {
        if($_POST)
        {
            $productLang   = $this->lang->product;
            $productConfig = $this->config->product;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $productConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = zget($productLang, $fieldName);
                unset($fields[$key]);
            }

            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getTopPairs();
            $lines = $this->product->getLinePairs();
            $productStats = $this->product->getStats($orderBy, null, $status);
            //a($productStats);die;
            foreach($productStats as $i => $product)
            {

                $product->line             = zget($lines, $product->line, '');
                $product->app              = zget($apps, $product->app, '');
                $product->PO               = zget($users, $product->PO, '');
                $product->belongDeptIds    = zmget($depts, $product->belongDeptIds);
                $product->type             = zget($this->lang->product->typeList, $product->type, '');
                $product->status           = zget($this->lang->product->statusList, $product->status, '');
                $product->activeStories    = (int)$product->stories['active'];
                $product->changedStories   = (int)$product->stories['changed'];
                $product->draftStories     = (int)$product->stories['draft'];
                $product->closedStories    = (int)$product->stories['closed'];
                $product->unResolvedBugs   = (int)$product->unResolved;
                $product->assignToNullBugs = (int)$product->assignToNull;

                $product->piplinePath = $product->piplinePath;
                $product->skipBuild = $product->skipBuild  ? '是' : '否';
                if(!empty($product->id)&&!empty($product->code)){
                    $product->historyCode = $this->product->getHistoryCodes($product->id,$product->code);
                }
                if($this->post->exportType == 'selected')
                {
                    $checkedItem = $this->cookie->checkedItem;
                    if(strpos(",$checkedItem,", ",{$product->id},") === false) unset($productStats[$i]);
                }

            }
            if(isset($this->config->bizVersion)) list($fields, $productStats) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $productStats);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $productStats);
            $this->post->set('kind', 'product');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }
}
