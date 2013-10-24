<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


class SeedAssets extends CrudSeeder {
    
    public function run()
    {

        DB::table('_db_assets')->delete();
        
        $id = $this->addAsset('adminstyles.css', 'styles');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('main.css', 'styles');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('jquery-1.8.3.min.js', 'scripts', 'jquery', '1.8.3');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('modernizr-2.6.2.min.js', 'scripts', 'modernizr', '2.6.2');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('jsonconvert.js', 'scripts');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('admintools.js', 'scripts');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('ckeditor/ckeditor.js', 'scripts', 'ckeditor');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('plugins.js', 'scripts');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('main.js', 'scripts');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('bootstrap.min.js', 'scripts', 'bootstrap');
        $this->linkAssetPage($id, '*');
        $id = $this->addAsset('google-analytics.js', 'scripts');
        $this->linkAssetPage($id, '*');
        
    }

}
?>