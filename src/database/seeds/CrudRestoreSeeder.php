<?php

namespace Laravella\Crud;

use DB;
use Laravella\Crud\Exceptions\DBException;
use Seeder;
use \Model;

class CrudRestoreSeeder extends CrudSeeder {

    private $backupId = null;
    private $pdo = null;

    public function run($bakId = null)
    {
        $this->pdo = DB::connection()->getPdo();
        $this->backupId = empty($bakId) ? $this->__getMaxId() : $bakId;

        echo "Restoring backup : " . $this->backupId . "\n";

        DB::transaction(function()
                {
                    $this->bakId = empty($this->backupId) ? $this->__getMaxId() : $this->backupId;
                    $this->tableRestore();
                    $this->fieldRestore();
                    $this->menuRestore();
//                    $this->permissionsRestore();
                });
    }

    private function __getMaxId()
    {
        $sql = "SELECT max(id) as backup_id FROM _db_backups";
        $bu = DB::select($sql);
        $bid = (is_array($bu)) ? $bu[0]->backup_id : null;
        return $bid;
    }

    /**
      Tables:
      backup_id,
      table_name,
      page_size,
      tav_title,
      action_name,
      view_name
     * 
     */
    private function tableRestore()
    {
        $tavs = Model::getInstance('_db_bak_tables')->where('backup_id', $this->backupId)->get()->toArray();

        foreach ($tavs as $tav)
        {
            try
            {
                $tableId = $this->getId('_db_tables', 'name', $tav['table_name']);
                $actionId = $this->getId('_db_actions', 'name', $tav['action_name']);
                $viewId = $this->getId('_db_views', 'name', $tav['view_name']);

                $this->updateOrInsert('_db_pages', array('table_id' => $tableId, 'action_id' => $actionId), array('table_id' => $tableId, 'action_id' => $actionId,
                    'view_id' => $viewId, 'page_size' => $tav['page_size'], 'title' => $tav['tav_title']));
            }
            catch (DBException $e)
            {
                echo $e->getMessage() . "\n";
            }
        }
    }

    /**

      Fields:
      backup_id,
      table_id,
      table_name,
      field_id,
      field_name,
      f.`fullname`,
      f.`label`,
      f.`display_type_id`,
      f.`searchable`,
      f.`display_order`,
      field_type,
      f.`length`,
      f.`width`,
      f.`null` nullable,
      f.`key`,
      f.`default`,
      f.extra,
      f.href,
      f.pk_field_id,
      pf.`name` as pk_name,
      f.pk_display_field_id,
      df.`name` as pk_display_name,
      ft.id as pk_table_id,
      ft.`name` as pk_table_name,
      f.`widget_type_id`,
      wt.`name` widget_type_name,
      wt.`definition` widget_type_definition,
      dt.`name` display_type_name
     * 
     */
    private function fieldRestore()
    {
        $fields = Model::getInstance('_db_bak_fields')->where('backup_id', $this->backupId)->get()->toArray();

        foreach ($fields as $field)
        {
            try
            {

                $displayId = $this->getId('_db_display_types', 'name', $field['display_type_name']);
                $widgetId = $this->getId('_db_widget_types', 'name', $field['widget_type_name']);

                $pkName = empty($field['pk_table_name']) ? '' : $field['pk_table_name'] . '.' . $field['pk_name'];
                $pkdName = empty($field['pk_table_name']) ? '' : $field['pk_table_name'] . '.' . $field['pk_display_name'];
                $pkFieldId = $this->getId('_db_fields', 'fullname', $pkName);
                $pkdFieldId = $this->getId('_db_fields', 'fullname', $pkdName);

                if (!empty($pkName))
                {
                    echo $pkFieldId . ' : ' . $pkName . " | " . $pkdFieldId . ' : ' . $pkdName . "\n";
                }

                $this->updateOrInsert('_db_fields', array('fullname' => $field['fullname']), array('display_type_id' => $displayId,
                    'widget_type_id' => $widgetId,
                    'label' => $field['label'],
                    'display_order' => $field['display_order'],
                    'href' => $field['href'],
                    'pk_field_id' => $pkFieldId,
                    'pk_display_field_id' => $pkdFieldId,
                    'searchable' => $field['searchable'])
                );
            }
            catch (DBException $e)
            {
                echo $e->getMessage() . "\n";
            }
        }
    }

    /**

      _db_bak_menus :
      backup_id,
      m.id,
      m.icon_class,
      m.label,
      m.href,
      m.parent_id,
      ug.group as group_name
     * 
     */
    private function menuRestore()
    {

        $sql = "delete from _db_menus";
        echo DB::unprepared($sql);
//
//        $sql = "insert into _db_menus 
//select distinct id, icon_class, label, weight, href, parent_id, null, null 
//from _db_bak_menus where backup_id = {$this->backupId}";
//
//        echo DB::unprepared($sql);

        $mbs = Model::getInstance('_db_bak_menus')->distinct()
                        ->select(array('id', 'icon_class', 'label', 'weight', 'href', 'parent_id'))
                        ->where('backup_id', $this->backupId)
                        ->get()->toArray();

        foreach ($mbs as $mb)
        {
            $this->updateOrInsert('_db_menus', array('id' => $mb['id']), $mb);
            echo 'menu : '.$mb['id'].' : '.$mb['href']."\n";
        }

        $mbs = Model::getInstance('_db_bak_menus')
                //->join('usergroups', 'usergroups.id', '=', '_db_bak_menu_permissions.usergroup_id')
                ->distinct()
                ->select(array('id', 'group_name'))
                ->where('backup_id', $this->backupId)
                ->get()
                ->toArray();

        foreach ($mbs as $mb)
        {
            $usergroupId = $this->getId('usergroups', 'group', $mb['group_name']);
            //menuid remains the same
            
            if (!empty($mb['id']) && !empty($usergroupId)) {
                $this->updateOrInsert('_db_menu_permissions', 
                        array('menu_id' => $mb['id'], 'usergroup_id' => $usergroupId), 
                        array('menu_id' => $mb['id'], 'usergroup_id' => $usergroupId));
                
                echo 'inserting '.$mb['id'].' : '.$usergroupId."\n";
            } else {
                echo "empty menu permissions \n";
            }
        }
    }

    /**
      permissions :
      backup_id,
      u.username,
      u.email,
      u.`password`,
      u.first_name,
      u.last_name,
      api_token,
      usergroup_id,
      ug.`group`,
      deleted_at,
      t.`name` `table_name`,
      a.`name` action_name
     */
    private function permissionsRestore()
    {
        /**
         * 
          "select $bakId backup_id, u.username, u.email, u.`password`,
          u.first_name, u.last_name, api_token, usergroup_id, ug.`group`,
          deleted_at, t.`name` `table_name`, a.`name` action_name
          from _db_user_permissions dup
          inner join users u on dup.user_id = u.id
          inner join _db_tables t on t.id = dup.table_id
          inner join _db_actions a on a.id = dup.action_id
          left outer join usergroups ug on u.usergroup_id = ug.id;"; *
         */
        $sql = "delete from users";
        echo DB::unprepared($sql) . "\n";

        $sql = "delete from _db_user_permissions";
        echo DB::unprepared($sql) . "\n";

        $perms = Model::getInstance('_db_bak_permissions')
                        ->where('backup_id', $this->backupId)
                        ->select(array('email', 'username', 'usergroup_id', 'api_token', 'group', 'password', 'first_name', 'last_name'))
                        ->distinct()
                        ->get()->toArray();

        foreach ($perms as $perm)
        {
            $usergroupId = $this->getId('usergroups', 'group', $perm['group']);

            unset($perm['group']);

            $perm['usergroup_id'] = $usergroupId;

            $this->updateOrInsert('users', array('username' => $perm['username']), $perm);
        }

        $perms = Model::getInstance('_db_bak_permissions')
                        ->where('backup_id', $this->backupId)
                        ->select(array('username', 'table_name', 'action_name'))
                        ->distinct()
                        ->get()->toArray();

        foreach ($perms as $perm)
        {
            $userId = $this->getId('users', 'username', $perm['username']);
            $tableId = $this->getId('_db_tables', 'name', $perm['table_name']);
            $actionId = $this->getId('_db_actions', 'name', $perm['action_name']);

            $a = array('user_id' => $userId,
                'table_id' => $tableId,
                'action_id' => $actionId);
            $this->updateOrInsert('_db_user_permissions', $a, $a);
        }

        $sql = "select username, `table_name`, action_name from _db_bak_permissions;";
        echo DB::unprepared($sql) . "\n";
        echo "permissions restored. \n";
    }

}
