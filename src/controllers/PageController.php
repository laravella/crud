<?php use Laravella\Crud\Options;

use Laravella\Crud\Params;

/**
 * This is used for ajax calls. 
 * It just formats the data differently but the work is still done by DbController class.
 */
class PageController extends DbController {
    public $displayType = self::HTML; //or self::JSON or self::HTML

    /**
     * 
     * @param type $page
     */
    public function getPage($page='contents') {
        
        return $this->getIndex($page);
        
    }
    
}

?>
