<?php
// include
require_once 'Zend/Date.php';	// ZendFrameworkの日付

/**
 * ユーティリティークラス
 *
 * @author Navi
 * @version 1.0.0
 */
class Util
{
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
	 * 渡された配列と同じ構造の配列を作成する
	 *
	 * @param array $base 配列または連想配列
	 * @param mixed $default 配列内の初期値
	 * @return $baseと同じ構造の配列
	 */
	public static function cloneArrayKey($base, $default = null) {
		// キー名のみ複製用の変数
		$key_clone = array();
		// キー名のみ複製する
		foreach (array_keys($base) as $key) {
			$key_clone[$key] = $default;
		}
		// 複製した変数を返す
		return $key_clone;
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

	/**
	 * 変数のチェックをする
	 *
	 * @param string $target チェックする対象
	 * @param array $options チェック内容
	 * @param mixed $success チェック成功時の返り値(省略時はNULL)
	 * @return mixed チェック判定値、NULLはチェックに問題なし
	 */
	public static function check($target, $options, $success = null) {
		/*
		 * $option の形式
		 *   $option = array(
		 *     // 連想配列形式
		 *     array(
		 *       'type'  => [チェック形式],
		 *       'error' => [失敗の判定値],
		 *       'comp'  => [比較対象]
		 *     ),
		 *     // 配列形式
		 *     array(
		 *       [チェック形式],
		 *       [失敗の判定値],
		 *       [比較対象]
		 *     ),
		 *     ...
		 *   )
		 *   動作
		 *     チェックは上から順にされていき、
		 *     失敗したら以降のチェックを行わない
		 *
		 * [type] チェック形式
		 *   empty  : 値が空でなければ成功
		 *   min    : 長さが [比較対象] より大きければ成功
		 *   max    : 長さが [比較対象] より小さければ成功
		 *   equal  : 値が [比較対象] と同じであれば成功
		 *   eqtype : 値が [比較対象] と型まで同じであれば成功
		 *   preg   : 値が [比較対象] の正規表現に一致すれば成功
		 *   date   : 日付が妥当であれば成功
		 *            [比較対象] に日付フォーマットを記述が必要
		 *            フォーマットはZend::Dateのフォーマット形式
		 */

		// 連想配列と配列番号の順番
		$name_sort = array(
			'type',
			'error',
			'comp'
		);

		// オプションの解析
		foreach ($options as $info) {
			// 配列形式を連想配列形式に変換
			foreach ($name_sort as $no => $key) {
				if (isset($info[$no])) { $info[$key] = $info[$no]; }
			}
			// チェック形式ごとに処理を分割
			switch ($info['type']) {
				// 空のチェック
				case 'empty' :
					if (empty($target)) {
						return $info['error'];
					}
					break;

				// 最小のチェック
				case 'min' :
					if (strlen($target) < $info['comp']) {
						return $info['error'];
					}
					break;

				// 最大のチェック
				case 'max' :
					if (strlen($target) > $info['comp']) {
						return $info['error'];
					}
					break;

				// 同一チェック
				case 'equal' :
					if ($target == $info['comp']) {
						return $info['error'];
					}
					break;

				// 完全同一チェック
				case 'eqtype' :
					if ($target === $info['comp']) {
						return $info['error'];
					}
					break;

				// 正規表現の一致チェック
				case 'preg' :
					if (preg_match($info['comp'], $target) == 0) {
						return $info['error'];
					}
					break;

				// 日付の妥当性チェック
				case 'date' :
					if (!Zend_Date::isDate($target, $info['comp'])) {
						return $info['error'];
					}
					break;
			}
		}

		// チェックの結果に問題なし
		return $success;
	}
}
