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
        
/*      
        $this->addOption($adminId, 'debug', '0'); //make debug information available in frontend, performance hit
        $this->addOption($adminId, 'configure', '1'); //show shortcuts to _db_fields for each field, for easy configuration
        $this->addOption($adminId, 'show-pk-tables', '0');
        $this->addOption($adminId, 'show-fk-tables', '0');
        $this->addOption($adminId, 'attach-params', '0');
*/        
        
        $assetPosId = $this->addOptionType('asset-pos', $adminId);
        $this->addOption($assetPosId, 'asset-pos-top', 'top');
        $this->addOption($assetPosId, 'asset-pos-bottom', 'bottom');

/*        
        //for image thumbnails
        $ulId = $this->addOptionType('upload', $adminId);
        $ivId = $this->addOptionType('image_versions', $ulId);
        $mediumId = $this->addOptionType('medium', $ivId);
        $this->addOption($mediumId, 'max_width', '200');
        $this->addOption($mediumId, 'max_height', '200');
        $thumbId = $this->addOptionType('thumbnail', $ivId);
        $this->addOption($thumbId, 'max_width', '100');
        $this->addOption($thumbId, 'max_height', '100');
*/        
        $optionTypeId = $this->addOptionType('installation');
        $this->addOption($optionTypeId, 'status', 0);
        $this->addOption($optionTypeId, 'site_root', base_path());
/*
        $memberTypeId = $this->addOptionType('member-type'); //this is a type of object member e.g. property / method / event
        $methodTypeId = $this->addOptionType('method', $memberTypeId);
        $propertyTypeId = $this->addOptionType('property', $memberTypeId);
        $eventTypeId = $this->addOptionType('event', $memberTypeId);
        $typeId = $this->addOptionType('onGet', $eventTypeId);
        $typeId = $this->addOptionType('onPost', $eventTypeId);
        $typeId = $this->addOptionType('onBeforeSearch', $eventTypeId);
        $typeId = $this->addOptionType('onAfterSearch', $eventTypeId);
        $typeId = $this->addOptionType('onApiGet', $eventTypeId);
        $typeId = $this->addOptionType('onApiPost', $eventTypeId);
        $typeId = $this->addOptionType('onBeforeInsert', $eventTypeId);
        $typeId = $this->addOptionType('onAfterInsert', $eventTypeId);
        $typeId = $this->addOptionType('onBeforeEdit', $eventTypeId);
        $typeId = $this->addOptionType('onAfterEdit', $eventTypeId);
*/        
    }

}

?>