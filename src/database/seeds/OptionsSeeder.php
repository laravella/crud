<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedOptions extends CrudSeeder {

    public function run()
    {
        $skin = Config::get('app.skin');//"skins::flatly";
        
        DB::table('_db_option_types')->delete();
        DB::table('_db_options')->delete();

        $optionTypeId = $this->addOptionType('database');
        $adminId = $this->addOptionType('admin');
        
        $this->addOption($adminId, 'skin', $skin); //the skin to use
        $this->addOption($adminId, 'debug', '0'); //make debug information available in frontend, performance hit
        $this->addOption($adminId, 'configure', '1'); //show shortcuts to _db_fields for each field, for easy configuration
        $this->addOption($adminId, 'show-pk-tables', '0');
        $this->addOption($adminId, 'show-fk-tables', '0');
        $this->addOption($adminId, 'attach-params', '0');
        $this->addOption($adminId, 'default-view', "$skin.dbview");
        
        $assetPosId = $this->addOptionType('asset-pos', $adminId);
        $this->addOption($assetPosId, 'asset-pos-top', 'top');
        $this->addOption($assetPosId, 'asset-pos-bottom', 'bottom');
        
        //for image thumbnails
        $ulId = $this->addOptionType('upload', $adminId);
        $ivId = $this->addOptionType('image_versions', $ulId);
        $mediumId = $this->addOptionType('medium', $ivId);
        $this->addOption($mediumId, 'max_width', '200');
        $this->addOption($mediumId, 'max_height', '200');
        $thumbId = $this->addOptionType('thumbnail', $ivId);
        $this->addOption($thumbId, 'max_width', '100');
        $this->addOption($thumbId, 'max_height', '100');
        
        $optionTypeId = $this->addOptionType('frontend');
        $this->addOption($optionTypeId, 'skin', $skin);
        $this->addOption($optionTypeId, 'default-view', "$skin.frontview");
        
        $optionTypeId = $this->addOptionType('installation');
        $this->addOption($optionTypeId, 'status', 0);
        
        //'site_root'
        $this->addOption($optionTypeId, 'site_root', base_path());
    }

}

?>