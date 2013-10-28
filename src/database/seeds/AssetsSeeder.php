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
        
        $assetGroupId = $this->addAssetType('default');

        $id = $this->addAsset('admintools.js', 'scripts', 'default');
        $this->info('adding asset admintools.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('ckeditor/ckeditor.js', 'scripts', 'default', 'ckeditor');
        $this->info('adding asset ckeditor/ckeditor.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('plugins.js', 'scripts', 'default');
        $this->info('adding asset plugins.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('main.js', 'scripts', 'default');
        $this->info('adding asset main.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('bootstrap.min.js', 'scripts', 'default', 'bootstrap');
        $this->info('adding asset bootstrap.min.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('google-analytics.js', 'scripts', 'default');
        $this->info('adding asset google-analytics.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('adminstyles.css', 'styles', 'default');
        $this->info('adding asset adminstyles.css');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('main.css', 'styles', 'default');
        $this->info('adding asset main.css');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('jquery-1.8.3.min.js', 'scripts', 'default', 'jquery', '1.8.3');
        $this->info('adding asset jquery-1.8.3.min.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('modernizr-2.6.2.min.js', 'scripts', 'default', 'modernizr', '2.6.2');
        $this->info('adding asset modernizr-2.6.2.min.js');
        $this->linkAssetPage($assetGroupId, '*');

        $id = $this->addAsset('jsonconvert.js', 'scripts', 'default');
        $this->info('adding asset jsonconvert.js');
        $this->linkAssetPage($assetGroupId, '*');
    }

}

?>