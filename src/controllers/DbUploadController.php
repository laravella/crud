<?php

/**
 * This is used for ajax calls. 
 * It just formats the data differently but the work is still done by DbController class.
 */
class DbUploadController extends DbController {

    protected $layout = 'crud::layouts.default';
    public $displayType = self::HTML; //or self::JSON or self::XML or self::HTML

    public function postUpload()
    {
        $action = 'postUpload';
    }

    public function getUpload()
    {

        $action = 'getUpload';

        $params = $this->__makeParams(self::INFO, "Enter data to insert.", null, 'medias', $action);

        return View::make($this->layout)->nest('content', $params->view->name, $params->asArray());
    }

}

?>
