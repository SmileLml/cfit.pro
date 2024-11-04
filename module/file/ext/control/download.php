<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class myfile extends file
{
    /**
     * Project: chengfangjinke
     * Method: download
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:41
     * Desc: This is the code comment. This method is called download.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $fileID
     * @param string $mouse
     * @param false $edit
     */
    public function download($fileID, $mouse = '', $edit = false)
    {
        if(session_id() != $this->app->sessionID) helper::restartSession($this->app->sessionID);
        if($this->config->file->libreOfficeTurnon)
        {
            $file = $this->file->getById($fileID);
            $officeTypes = 'doc|docx|xls|xlsx|ppt|pptx|pdf';
            if(stripos($officeTypes, $file->extension) !== false and $mouse == 'left')
            {
                if(isset($this->config->file->convertType) and $this->config->file->convertType == 'collabora' and $this->config->requestType == 'PATH_INFO')
                {
                    $discovery = $this->file->getCollaboraDiscovery();
                    if(empty($discovery)) die(js::alert(sprintf($this->lang->file->collaboraFail, $this->config->file->collaboraPath)));
                    if($discovery and isset($discovery[$file->extension]))
                    {
                        $address = $this->config->file->internalAddress;

                        $wopiSrc     = $address . $this->createLink('file', 'ajaxWopiFiles', "fileID=$fileID");
                        $wopiEditSrc = $address . $this->createLink('file', 'ajaxWopiFiles', "fileID=$fileID&canEdit=1");

                        $action       = $discovery[$file->extension]['action'];
                        $collaboraUrl = str_replace($this->config->file->collaboraPath, $this->config->file->publicPath, $discovery[$file->extension]['urlsrc']);
                        $this->view->collaboraUrl  = $collaboraUrl . 'WOPISrc=' . $wopiSrc . '&access_token=' . session_id();
                        if($action == 'edit') $this->view->collaboraEdit = $collaboraUrl . 'WOPISrc=' . $wopiEditSrc . '&access_token=' . session_id();
                        if($edit) $edit = common::hasPriv('file', 'edit');
                        if($edit) $this->view->collaboraUrl = $this->view->collaboraEdit;
                        $this->view->edit  = $edit;
                        $this->view->title = $file->title;
                        die($this->display('file', 'collabora'));
                    }
                }
                else
                {
                    $sofficePath = isset($this->config->file->sofficePath) ? $this->config->file->sofficePath : '';
                    if(file_exists($file->realPath) and !empty($sofficePath) and file_exists($sofficePath))
                    {
                        $convertedFile = $file->extension == 'pdf' ? $file->realPath : $this->file->convertOffice($file);
                        if($convertedFile)
                        {
                            $mime = strpos($convertedFile, '.html') ? "text/html" : 'application/pdf';
                            header("Content-type: $mime");

                            $handle = fopen($convertedFile, "r");
                            if($handle)
                            {
                                while(!feof($handle)) echo fgets($handle);
                                fclose($handle);
                                die();
                            }
                        }
                        elseif(file_exists($this->app->getCacheRoot() . 'convertoffice/lock'))
                        {
                            die("<html><head><meta charset='utf-8'></head><body>{$this->lang->file->officeBusy}</body></html>");
                        }
                    }
                }
            }
        }

        return parent::download($fileID, $mouse);
    }
}
