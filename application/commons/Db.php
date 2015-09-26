<?php
// include
require_once 'Zend/Db.php';

/**
 * データベースの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
class Db
{
	/**
	 * データベースの接続
	 *
	 * @return Zend_Db 接続済みのデータベースオブジェクト
	 */
	public static function Connect() {
		// データベースの設定を取得
		$config = new Zend_Config_Ini(APP_CONFIG . "application.ini", "database");
		// データベースへ接続
		return Zend_Db::factory($config->adapter, $config->params);
	}
}
