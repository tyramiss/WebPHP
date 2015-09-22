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

	/** 認証のユーザーID */
	public $id = "user_id";

	/** 認証のパスワード */
	public $password = "password";

	/** 認証時の条件 */
	public $treatment = "? AND status = 1";
}