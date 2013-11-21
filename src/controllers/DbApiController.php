<?php

/**
 * This is used for ajax calls. 
 * It just formats the data differently but the work is still done by DbController class.
 */
class DbApiController extends DbController {
    
    private $layoutName = '.content';
    private $viewName = '.dbview';
    
    
    public $displayType = self::XML; //or self::JSON or self::HTML
    
}

?>
