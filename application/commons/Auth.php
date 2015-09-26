<?php
// include
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

/**
 * データベースの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
class Auth
{
	/**
	 * 認証状態を取得
	 *
	 * @return boolean 認証状態
	 */
	public static function status() {
		return Zend_Auth::getInstance()->hasIdentity();
	}

	/**
	 * ログイン
	 *
	 * @param Zend_Db $db       データベースオブジェクト
	 * @param string  $name     ユーザー名
	 * @param string  $password パスワード
	 * @return boolean 認証結果
	 */
	public static function login($db, $name, $password) {
		// 認証はユーザー情報を使用する
		$table = new Zend_Auth_Adapter_DbTable(
			$db,
			"user",
			"user_id",
			"password",
			"? AND status = 1"
		);

		// 入力されたユーザーID
		$table->setIdentity($name);
		// 入力されたパスワード
		$table->setCredential($password);

		// 認証問い合わせ結果を返す
		$auth = Zend_Auth::getInstance();
		return ($auth->authenticate($table)->isValid());
	}

	/**
	 * ログアウト
	 */
	public static function logout() {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$auth->clearIdentity();
		}
	}
}
