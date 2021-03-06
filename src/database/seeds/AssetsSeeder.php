<?php

namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedAssets extends CrudSeeder {

    public function run()
    {
        set_time_limit(0);

        DB::table('_db_assets')->delete();
        DB::table('_db_page_assets')->delete();
        
        $assetGroupId = $this->addAssetType('default');

        $id = $this->addAsset('admintools.js', 'scripts', 'default', 'bottom');
        $this->info('adding asset admintools.js');

        $id = $this->addAsset('ckeditor/ckeditor.js', 'scripts', 'default', 'bottom', 'ckeditor');
        $this->info('adding asset ckeditor/ckeditor.js');

        $id = $this->addAsset('plugins.js', 'scripts', 'default', 'bottom');
        $this->info('adding asset plugins.js');

        $id = $this->addAsset('main.js', 'scripts', 'default', 'bottom');
        $this->info('adding asset main.js');

        $id = $this->addAsset('bootstrap.min.js', 'scripts', 'default', 'bottom', 'bootstrap');
        $this->info('adding asset bootstrap.min.js');

        $id = $this->addAsset('google-analytics.js', 'scripts', 'default', 'bottom');
        $this->info('adding asset google-analytics.js');

        $id = $this->addAsset('adminstyles.css', 'styles', 'default');
        $this->info('adding asset adminstyles.css');

        $id = $this->addAsset('css/bootstrap.css', 'styles', 'default');
        $this->info('adding asset main.css');

        $id = $this->addAsset('jquery-1.8.3.min.js', 'scripts', 'default', 'top', 'jquery', '1.8.3');
        $this->info('adding asset jquery-1.8.3.min.js');

        $id = $this->addAsset('modernizr-2.6.2.min.js', 'scripts', 'default', 'top', 'modernizr', '2.6.2');
        $this->info('adding asset modernizr-2.6.2.min.js');

        $id = $this->addAsset('jsonconvert.js', 'scripts', 'default', 'bottom');
        $this->info('adding asset jsonconvert.js');
        
        $this->linkAssetPage($assetGroupId, '*');
        
        $skins = Options::getSkin();
        $adminSkin = $skins['admin'];
        
        //$assetGroupId = $this->addAssetType('uploads');
//        $this->linkAssetPageGroups('default', "{$skins['admin']}.uploadview");
        
    }

}

?>