<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class CrudDatabaseSeeder extends Seeder {

    public function run()
    {
        /*
        $this->call('SeedGroups');
        Log::write("success", "Populated severities");
        **/
        try {
            $this->call('Laravella\Crud\SeedUsergroups');
            Log::write("success", "Populated usergroups");

            $this->call('Laravella\Crud\SeedUsers');
            Log::write("success", "Populated users");

            $this->call('Laravella\Crud\SeedMenus');
            Log::write("success", "Populated _db_menus");

            $this->call('Laravella\Crud\SeedOptions');
            Log::write("success", "Populated _db_options");

            $this->call('Laravella\Crud\SeedSeverities');
            Log::write("success", "Populated severities");

            $this->call('Laravella\Crud\SeedPageTypes');
            Log::write("success", "Populated page types in to _db_option_types");

            $this->call('Laravella\Crud\SeedTables');
            Log::write("success", "Populated _db_tables and _db_fields");

            $this->call('Laravella\Crud\SeedActions');
            Log::write("success", "Populated _db_actions");

            $this->call('Laravella\Crud\SeedObjects');
            Log::write("success", "Populated _db_objects");

            $this->call('Laravella\Crud\SeedViews');
            Log::write("success", "Populated _db_views");

            $this->call('Laravella\Crud\SeedPages');
            Log::write("success", "Populated _db_pages");

            $this->call('Laravella\Crud\SeedKeyTypes');
            Log::write("success", "Populated _db_key_types");

            $this->call('Laravella\Crud\UpdateReferences');
            Log::write("success", "References seeded");

            $this->call('Laravella\Crud\UpdateCMSFields');
            Log::write("success", "CMS Fields updates");

            $this->call('Laravella\Crud\SeedAssets');
            Log::write("success", "Assets seeded");

            $this->call('Laravella\Crud\PostCrudSeeder');
            Log::write("success", "Crud PostCrudSeeder ran");
        } catch (Exception $e) {
            echo $e->getMessage();
            var_dump(debug_backtrace());
        }
        
    }

}
?>