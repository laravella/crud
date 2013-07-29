<?php

class DatabaseSeeder extends Seeder {

    public function run()
    {
        $this->call('SeedUsergroups');
        Log::write("success", "Populated severities");
        
        $this->call('SeedUsers');
        Log::write("success", "Populated severities");

        $this->call('SeedSeverities');
        Log::write("success", "Populated severities");
        
        $this->call('SeedTables');
        Log::write("success", "Populated _db_tables and _db_fields");
        
        $this->call('SeedActions');
        Log::write("success", "Populated _db_actions");
        
        $this->call('SeedViews');
        Log::write("success", "Populated _db_views");
        
        $this->call('UpdateReferences');
        Log::write("success", "References seeded");
        
    }

}
