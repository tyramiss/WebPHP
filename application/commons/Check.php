<?php
// include
require_once 'Zend/Date.php';	// ZendFrameworkの日付

/**
 * 入力チェッククラス
 *
 * @author Navi
 * @version 1.0.0
 */
class Check
{
	/**
	 * 変数のチェックをする
	 *
	 * @param array $input 入力内容
	 * @param array $options チェック内容
	 * @return array エラー結果
	 */
	public static function validate($input, $options) {
		/*
		 * $options : チェック内容
		 *   array(
		 *     [チェック変数名] => array(
		 *       // 連想配列形式
		 *       array(
		 *         'type'  => [チェック形式],
		 *         'error' => [失敗の判定値],
		 *         'comp'  => [比較対象]
		 *       ),
		 *       // 配列形式
		 *       array(
		 *         [チェック形式],
		 *         [失敗の判定値],
		 *         [比較対象]
		 *       ),
		 *       ...
		 *     ),
		 *   )
		 *   動作
		 *     チェックは上から順にされていき、
		 *     失敗したら以降のチェックを行わない
		 *
		 * [type] チェック形式(大文字小文字の区別なし)
		 *   empty  : 値が空でなければ成功
		 *   min    : 長さが [比較対象] より大きければ成功
		 *   max    : 長さが [比較対象] より小さければ成功
		 *   equal  : 値が [比較対象] と同じであれば成功
		 *   eqtype : 値が [比較対象] と型まで同じであれば成功
		 *   preg   : 値が [比較対象] の正規表現に一致すれば成功
		 *   date   : 日付が妥当であれば成功
		 *            [比較対象] に日付フォーマットを記述が必要
		 *            フォーマットはZend::Dateのフォーマット形式
		 *
		 * エラー結果
		 *   配列の内容は
		 *     array(
		 *       [チェック変数名] => [失敗の判定値],
		 *       ...
		 *     )
		 *   失敗判定された変数のみ出力される
		 */

		// 連想配列と配列番号の順番
		$name_sort = array(
			'type',
			'error',
			'comp'
		);

		// エラー用変数
		$error = array();

		// チェックする変数とチェック内容を展開
		foreach ($options as $key => $checkes) {
			// チェック内容を展開
			foreach ($checkes as $info) {
				// すでにエラーが検知された場合は以降の処理をスキップ
				if (array_key_exists($key, $error)) {
					break;
				}
				// 配列形式を連想配列形式に変換
				foreach ($name_sort as $no => $name) {
					if (isset($info[$no])) {
						$info[$name] = $info[$no];
					}
				}
				// 対象
				$target = $input[$key];
				// チェック形式ごとに処理を分割
				switch (strtolower($info['type'])) {
					// 空のチェック
					case 'empty' :
						if (self::isEmpty($target)) {
							$error[$key] = $info['error'];
						}
						break;

					// 最小のチェック
					case 'min' :
						if (self::isMin($target, $info['comp'])) {
							$error[$key] = $info['error'];
						}
						break;

					// 最大のチェック
					case 'max' :
						if (self::isMax($target, $info['comp'])) {
							$error[$key] = $info['error'];
						}
						break;

					// 同一チェック
					case 'equal' :
						if (self::isNotEqual($target, $info['comp'])) {
							$error[$key] = $info['error'];
						}
						break;

					// 完全同一チェック
					case 'absequal' :
						if (self::isNotAbsEqual($target, $info['comp'])) {
							$error[$key] = $info['error'];
						}
						break;

					// 正規表現の一致チェック
					case 'match' :
						if (self::isMatch($target, $info['comp'])) {
							$error[$key] = $info['error'];
						}
						break;

					// 日付の妥当性チェック
					case 'date' :
						if (self::isDate($target, $info['comp'])) {
							$error[$key] = $info['error'];
						}
						break;
				}
			}
		}

		// エラー結果を返す
		return $error;
	}

	/**
	 * 空のチェックをする
	 *
	 * @param mixed $target チェック対象
	 * @return boolean 空の場合は true を返す
	 */
	public static function isEmpty($target) {
		return (empty($target));
	}

	/**
	 * 最小のチェックをする
	 *
	 * @param mixed $target チェック対象
	 * @param mixed $offset 基準値
	 * @return boolean 基準値より小さい場合は true を返す
	 */
	public static function isMin($target, $offset) {
		return (strlen($target) < $offset);
	}

	/**
	 * 最大のチェックをする
	 *
	 * @param mixed $target チェック対象
	 * @param mixed $offset 基準値
	 * @return boolean 場合は true を返す
	 */
	public static function isMax($target, $offset) {
		return (strlen($target) > $offset);
	}

	/**
	 * 同一チェックをする
	 *
	 * @param mixed $target チェック対象
	 * @param mixed $offset 基準値
	 * @return boolean 基準値と違う場合は true を返す
	 */
	public static function isNotEqual($target, $offset) {
		return ($target != $offset);
	}

	/**
	 * 完全同一チェックをする
	 *
	 * @param mixed $target チェック対象
	 * @param mixed $offset 基準値
	 * @return boolean 基準値と型も含めて違う場合は true を返す
	 */
	public static function isNotAbsEqual($target, $offset) {
		return ($target !== $offset);
	}

	/**
	 * 正規表現の一致チェックをする
	 *
	 * @param mixed $target チェック対象
	 * @param mixed $format 正規表現
	 * @return boolean 正規表現に一致しない場合は true を返す
	 */
	public static function isMatch($target, $format) {
		return (preg_match($format, $target) == 0);
	}

	/**
	 * 日付の妥当性チェックをする
	 *
	 * @param mixed $target チェック対象
	 * @param mixed $format 日付フォーマット
	 * @return boolean 日付が妥当ではない場合は true を返す
	 */
	public static function isDate($target, $format) {
		return (!Zend_Date::isDate($target, $format));
	}
}