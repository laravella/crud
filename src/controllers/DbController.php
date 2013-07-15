<?php 

/**
 * Description of DbController
 *
 * @author Victor
 */
class DbController extends Controller {

    protected $layout = 'crud::layouts.default';
    
    public function getIndex()
    {
        return View::make("crud::dbinstall", array('action' => 'index'));
    }

    public function getTables()
    {
        $results = DB::select('show tables');
        $prefix = "/db/select/";
        return View::make("crud::dbview", array('data' => $results, 'prefix' => $prefix));
    }

    public function getSelect($table = null)
    {
        $table = DB::table($table)->get();
        $prefix = "";
        return View::make("crud::dbview", array('data' => $table, 'prefix' => $prefix)); //->with('data', $table);
        
        //return View::make($this->layout)->nest('content','crud::dbview', array('data' => $table));
    }

    public function getInstall() {
        return View::make("crud::dbinstall", array('action' => 'install'));
    }
    
    public function missingMethod($parameters)
    {
        return "missing";
    }    


}

?>
