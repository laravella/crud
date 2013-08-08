<?php

use Laravella\Crud\Log;

class BackupSeeder extends Seeder {

    public function run()
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

        $selectMeta = "select distinct $bakId backup_id, t.`id` table_id, t.`name` `table_name`,
    f.`id` field_id, f.`name` field_name, f.`fullname`, f.`label`, f.`display_type_id`,
    f.`searchable`, f.`display_order`, f.`type` field_type, f.`length`, f.`width`, 
    f.`null` nullable, f.`key`, f.`default`, f.extra, f.href, f.`_db_table_id` table_id,
    f.pk_field_id, f.pk_display_field_id, 
    f.`widget_type_id`, wt.`name` widget_type_name, wt.`definition` widget_type_definition,
    tav.page_size, tav.title tav_title,
    a.`name` action_name, 
    dt.`name` display_type_name
    from _db_tables t 
    inner join _db_fields f on f._db_table_id = t.id
    left outer join _db_widget_types wt on wt.id = f.widget_type_id
    left outer join _db_table_action_views tav on t.id = tav.table_id
    left outer join _db_views v on tav.view_id = v.id
    left outer join _db_actions a on tav.action_id = a.id
    left outer join _db_display_types dt on f.display_type_id = dt.id;";

        $pdo = DB::connection()->getPdo();

        if (!Schema::hasTable('_db_bak_meta'))
        {
            $sql = "create table _db_bak_meta as " . $selectMeta;
            $pdo->query($sql);
        }
        else
        {
            $sql = "insert into _db_bak_meta " . $selectMeta;
            $pdo->query($sql);
        }

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
    }

}
