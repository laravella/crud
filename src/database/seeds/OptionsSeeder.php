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
        
        $type = array();
    
        $type['database'] = $this->addOptionType('database');
        $type['frontend'] = $this->addOptionType('frontend');
        $type['installation'] = $this->addOptionType('installation');
        
        $type['admin'] = $this->addOptionType('admin');
        $type['asset-pos'] = $this->addOptionType('asset-pos', $type['admin']);
        $type['upload'] = $this->addOptionType('upload', $type['admin']);

        $type['image_versions'] = $this->addOptionType('image_versions', $type['upload']);
        $type['medium'] = $this->addOptionType('medium', $type['image_versions']);
        $type['thumbnail'] = $this->addOptionType('thumbnail', $type['image_versions']);
        
        $type['member-type'] = $this->addOptionType('thumbnail', $type['image_versions']);
        $type['method'] = $this->addOptionType('method', $type['member-type']);
        $type['property'] = $this->addOptionType('property', $type['member-type']);
        $type['event'] = $this->addOptionType('event', $type['member-type']);
        $type['onGet'] = $this->addOptionType('onGet', $type['event']);
        $type['onPost'] = $this->addOptionType('onPost', $type['event']);
        $type['onBeforeSearch'] = $this->addOptionType('onBeforeSearch', $type['event']);
        $type['onAfterSearch'] = $this->addOptionType('onAfterSearch', $type['event']);
        $type['onApiGet'] = $this->addOptionType('onApiGet', $type['event']);
        $type['onApiPost'] = $this->addOptionType('onApiPost', $type['event']);
        $type['onBeforeInsert'] = $this->addOptionType('onBeforeInsert', $type['event']);
        $type['onAfterInsert'] = $this->addOptionType('onAfterInsert', $type['event']);
        $type['onBeforeEdit'] = $this->addOptionType('onBeforeEdit', $type['event']);
        $type['onAfterEdit'] = $this->addOptionType('onAfterEdit', $type['event']);
        
        $this->addOption($type['admin'], 'debug', 0);
        $this->addOption($type['admin'], 'configure', 1);
        $this->addOption($type['admin'], 'show-pk-tables', 0);
        $this->addOption($type['admin'], 'show-fk-tables', 0);
        $this->addOption($type['admin'], 'attach-params', 0);
        
        $this->addOption($type['admin'], 'skin', $skins['admin']); //the skin to use
        $this->addOption($type['admin'], 'default-view', "{$skins['admin']}.dbview");

        $this->addOption($type['frontend'], 'skin', $skins['frontend']);
        $this->addOption($type['frontend'], 'default-view', "{$skins['frontend']}.frontview");
        
        $this->addOption($type['asset-pos'], 'asset-pos-top', 'top');
        $this->addOption($type['asset-pos'], 'asset-pos-bottom', 'bottom');
        
        $this->addOption($type['medium'], 'max_width', '200');
        $this->addOption($type['medium'], 'max_height', '200');
        
        $this->addOption($type['thumbnail'], 'max_width', '100');
        $this->addOption($type['thumbnail'], 'max_height', '100');
        
        $this->addOption($type['installation'], 'status', '0');
        $this->addOption($type['installation'], 'site_root', base_path());
        
    }

}

?>