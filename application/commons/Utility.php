<?php
/**
 * ユーティリティークラス
 *
 * @author Navi
 * @version 1.0.0
 */
class Util
{
	/**
	 * 認証
	 */
	public static function auth() {
		// 認証状態を取得
		$auth = Zend_Auth::getInstance();
		// 認証できていなければリダイレクトする
		if ($auth->hasIdentity()) {
			header("Location: " . AUTH_NOT_REDIRECT);
			exit();
		}
		// 認証を返す
		return $ath;
	}

	/**
	 * POSTに入力された値を取得する
	 *
	 * @param Zend_Controller_Request_Http $request リクエストオブジェクト
	 * @param array $defaults POSTから取得する変数名と初期値
	 * @return array 受け取ったPOSTの値
	 */
	public static function getPost($request, $defaults) {
		// POST受け取り変数
		$post = array();

		// ユーザーによってリクエストされた内容の取得
		foreach ($defaults as $key => $value) {
			$post[$key] = $request->getPost($key , $value);
		}

		// 受け取ったPOSTを返す
		return $post;
	}

	/**
	 * 入力の状態を取得する
	 *
	 * @param Zend_Controller_Request_Http $request リクエストオブジェクト
	 * @return boolean 入力のリクエストがあればTRUEを返す
	 */
	public static function getInputStatus($request) {
		// <input type="hidden" name="{FORM_KEY}" value="{FORM_INPUT}"> を使用すること
		return ($request->getPost(FORM_KEY , FORM_NONE) == FORM_INPUT);
	}
}
