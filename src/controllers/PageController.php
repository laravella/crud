<?php use Laravella\Crud\Options;

use Laravella\Crud\Params;

/**
 * This is used for ajax calls. 
 * It just formats the data differently but the work is still done by DbController class.
 */
class PageController extends DbController {
    public $displayType = self::HTML; //or self::JSON or self::HTML
    
    public function getIndex() {
        
        $viewName = Options::get('skin').'.account.login';
        $params = new Params(self::SUCCESS, '', null, $viewName);
        
        return View::make(Options::get('skin').'.frontend.default')
                ->nest('content', Options::get('skin').'.frontview', $params->asArray());
    }
}

?>
