<?php

namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Schema;

class CrudBackupSeeder extends Seeder {

    public function run()
    {
        DB::transaction(function()
        {
        $pdo = DB::connection()->getPdo();
        $bakId = $this->makeBackup($pdo);
        $this->tableBackup($bakId, $pdo);
        $this->fieldBackup($bakId, $pdo);
        $this->menuBackup($bakId, $pdo);
        $this->permissionsBackup($bakId, $pdo);
        });
    }

    public function tableBackup($bakId, $pdo)
    {
        $selectMeta = "select $bakId as backup_id, t.name table_name, tav.page_size, 
            tav.title tav_title, a.`name` action_name, v.`name` view_name
from _db_tables t left outer join _db_table_action_views tav on t.id = tav.table_id
left outer join _db_views v on tav.view_id = v.id
left outer join _db_actions a on tav.action_id = a.id;";

        if (!Schema::hasTable('_db_bak_tables'))
        {
            $sql = "create table _db_bak_tables as " . $selectMeta;
            $pdo->query($sql);
        }
        else
        {
            $sql = "insert into _db_bak_tables " . $selectMeta;
            $pdo->query($sql);
        }

        echo "tables backed up\n";
    }

    private function makeBackup()
    {
        if (!Schema::hasTable('_db_backups'))
        {
            Schema::create('_db_backups', function($table)
                    {
                        $table->increments('id');
                        $table->timestamps();
                    });
        }

        $bakId = DB::table("_db_backups")->insertGetId(array());
        echo 'Backup Id : ' . $bakId . " \n";
        return $bakId;
    }

    private function fieldBackup($bakId, $pdo)
    {
        $selectMeta = "select distinct $bakId as backup_id, t.`id` as table_id, t.`name` as table_name,
    f.`id` field_id, f.`name` field_name, f.`fullname`, f.`label`, f.`display_type_id`,
    f.`searchable`, f.`display_order`, f.`type` field_type, f.`length`, f.`width`, 
    f.`null` nullable, f.`key`, f.`default`, f.extra, f.href, 
    f.pk_field_id, pf.`name` as pk_name,
    f.pk_display_field_id, df.`name` as pk_display_name,
    ft.id as pk_table_id, ft.`name` as pk_table_name,
    f.`widget_type_id`, wt.`name` widget_type_name, wt.`definition` widget_type_definition,
    dt.`name` display_type_name
    from _db_tables t 
    inner join _db_fields f on f.table_id = t.id
    left outer join _db_widget_types wt on wt.id = f.widget_type_id
    left outer join _db_display_types dt on f.display_type_id = dt.id
    left outer join _db_fields df on f.pk_display_field_id = df.id
    left outer join _db_fields pf on f.pk_field_id = pf.id
    left outer join _db_tables ft on pf.table_id = ft.id;";

        if (!Schema::hasTable('_db_bak_fields'))
        {
            $sql = "create table _db_bak_fields as " . $selectMeta;
            $pdo->query($sql);
        }
        else
        {
            $sql = "insert into _db_bak_fields " . $selectMeta;
            $pdo->query($sql);
        }
        echo "fields backed up\n";
    }

    public function menuBackup($bakId, $pdo)
    {
        if (!Schema::hasTable('_db_bak_menus'))
        {
            $sql = "create table _db_bak_menus as select $bakId backup_id, id, icon_class, label, href, parent_id from _db_menus";
            $pdo->query($sql);
        }
        else
        {
            $sql = "insert into _db_bak_menus select $bakId backup_id, id, icon_class, label, href, parent_id from _db_menus;";
            $pdo->query($sql);
        }
        echo "menus backed up\n";
    }

    private function permissionsBackup($bakId, $pdo)
    {
        $selectPermissions = "select $bakId backup_id, u.username, u.email, u.`password`,
                u.first_name, u.last_name, api_token, usergroup_id, ug.`group`,
                deleted_at, t.`name` `table_name`, a.`name` action_name 
                from _db_user_permissions dup 
                inner join users u on dup.user_id = u.id
                inner join _db_tables t on t.id = dup.table_id
                inner join _db_actions a on a.id = dup.action_id
                left outer join usergroups ug on u.usergroup_id = ug.id;";

        if (!Schema::hasTable('_db_bak_permissions'))
        {
            $sql = "create table _db_bak_permissions as " . $selectPermissions;
            $pdo->query($sql);
        }
        else
        {
            $sql = "insert into _db_bak_permissions " . $selectPermissions;
            $pdo->query($sql);
        }
        echo "permissions backed up\n";
    }

}
