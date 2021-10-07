<?php

if(!defined('IN_CMS')) exit;

if (!$permission = Permission::findByName('performances')) {
	if (Record::insert('Permission', array('name'=>'performances'))) {
		$permission = Permission::findByName('performances');
	}
}

$base_permission = Permission::findById(1);

if ($role = Role::findByName('performances')) {
	$role_id = $role->id;
} else {
	if (Record::insert('Role', array('name'=>'performances'))) {
		$role_id = Record::lastInsertId();
	}
}
if ($role_id && $permission) {
	RolePermission::savePermissionsFor($role_id, array($base_permission, $permission));
}

/**
 * Вывод всплывающего сообщения на экран
 * О том, что наш плагин успешно включен
 */
Flash::set('success', 'Плагин PERFORMANCES, инициализирован');