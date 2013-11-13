<?php use Laravella\Crud\Options;

use Laravella\Crud\Params;

/**
 * This is used for ajax calls. 
 * It just formats the data differently but the work is still done by DbController class.
 */
class PageController extends DbController {
    public $displayType = self::HTML; //or self::JSON or self::HTML
    
    public function getIndex() {
        $viewName = Options::get('skin', 'frontend').'.frontview';
        $params = new Params(true, self::SUCCESS, '', null, $viewName);
        
        return View::make(Options::get('skin', 'frontend').'.frontlayout')
                ->nest('content', Options::get('skin', 'frontend').'.frontview', $params->asArray());
    }

}

?>
