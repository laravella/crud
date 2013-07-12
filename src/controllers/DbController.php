<?php 

/**
 * Description of DbController
 *
 * @author Victor
 */
class DbController extends Controller {

    protected $layout = 'layouts.default';
    
    public function getIndex()
    {
        return "hello from DbController->getIndex()";
    }

    public function getSelect($table = null)
    {
        $table = DB::table($table)->get();
        
        return View::make("crud::dbview", array('data' => $table)); //->with('data', $table);
        
        //return View::make($this->layout)->nest('content','crud::dbview', array('data' => $table));
    }

    public function missingMethod($parameters)
    {
        return "missing";
    }    


}

?>
