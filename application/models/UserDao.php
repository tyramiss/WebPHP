<?php
// include
require_once APP_COMMON . 'Table.php';

/**
 * ユーザーテーブルDAO
 *
 * @author Navi
 * @version 1.0.0
 */
class UserDao extends BASE_Db_Table
{
	/** テーブル名 */
	public $name = "user";
}