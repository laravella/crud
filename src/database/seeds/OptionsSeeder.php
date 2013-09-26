<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedOptions extends CrudSeeder {

    public function run()
    {
        DB::table('_db_option_types')->delete();
        DB::table('_db_options')->delete();

        $optionTypeId = $this->addOptionType('database');
        
        $optionTypeId = $this->addOptionType('admin');
        $this->addOption($optionTypeId, 'skin', 'default');
        $this->addOption($optionTypeId, 'debug', '');
        $this->addOption($optionTypeId, 'configure', ''); //show shortcuts to _db_fields for each field, for easy configuration
        $this->addOption($optionTypeId, 'show-pk-tables', '');
        $this->addOption($optionTypeId, 'show-fk-tables', '');
        $this->addOption($optionTypeId, 'attach-params', '');
        
        $ulId = $this->addOptionType($optionTypeId, 'upload');
        $ivId = $this->addOptionType($ulId, 'image_versions');
        $this->addOptionType($ivId, 'medium');
        $this->addOptionType($ivId, 'thumbnail');
        
        $optionTypeId = $this->addOptionType('frontend');
        $this->addOption($optionTypeId, 'skin', 'default');
        
        $optionTypeId = $this->addOptionType('installation');
        $this->addOption($optionTypeId, 'status', 0);
        
        //'site_root'
        $this->addOption($optionTypeId, 'site_root', base_path());
    }

}

?>