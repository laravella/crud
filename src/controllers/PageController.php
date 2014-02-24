<?php use Laravella\Crud\Options;

use Laravella\Crud\Params;

/**
 * This is used for ajax calls. 
 * It just formats the data differently but the work is still done by DbController class.
 */
class PageController extends DbController {
    public $displayType = self::HTML; //or self::JSON or self::HTML

    public $layoutName = '.frontlayout';
    public $viewName = '.frontview';
    
    /**
     * 
     * @param type $page
     */
    public function getPage($contentsSlug='contents', $action=null, $frontend=false) {
        
        return $this->getIndex($contentsSlug);
        
    }
    
}

?>
