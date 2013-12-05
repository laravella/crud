<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedOptions extends CrudSeeder {

    public function run()
    {
        $skins = Options::getJsonOptions('skins'); //Config::get('app.skins');

        DB::table('_db_option_types')->delete();
        DB::table('_db_options')->delete();

        $adminId = $this->addOptionType('admin');
        $this->addOption($adminId, 'skin', $skins['admin']); //the skin to use
        $this->addOption($adminId, 'default-view', "{$skins['admin']}.dbview");

        $optionTypeId = $this->addOptionType('frontend');
        $this->addOption($optionTypeId, 'skin', $skins['frontend']);
        $this->addOption($optionTypeId, 'default-view', "{$skins['frontend']}.frontview");
        
        $assetPosId = $this->addOptionType('asset-pos', $adminId);
        $this->addOption($assetPosId, 'asset-pos-top', 'top');
        $this->addOption($assetPosId, 'asset-pos-bottom', 'bottom');

        $optionTypeId = $this->addOptionType('installation');
        $this->addOption($optionTypeId, 'status', 0);
        $this->addOption($optionTypeId, 'site_root', base_path());
    }

}

?>