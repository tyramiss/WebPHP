<?php
/**
 * ハッシュ関連のクラス
 */
class Hash {
	/**
	 * 配列の参照位置の値を返す。
	 *
	 * @param array  参照する配列。
	 * @param string 参照したいパス。
	 * @return mixed 参照位置の値を返す。値がなければNULLを返す。
	 */
	public static function get($src, $path) {
		// 配列ではない
		if (!is_array($src)) {
			throw new Exception('$src params not type array');
		}
		// 文字列ではない
		if (!is_string($path)) {
			throw new Exception('$path params not type string');
		}

		// 階層を配列定義として変換
		$arrayKeyString = "['" . str_replace('.',"']['", $path) . "']";

		// 変数として存在していない
		if (!eval("return(isset(¥$src{$arrayKeyString}));")) {
			return(null);
		}

		// 変数の内容を返す
		return(eval("return(¥$src{$arrayKeyString})"));
	}

	/**
	 * 配列のキーマップを返す。
	 *
	 * @param array $src 参照する配列。
	 * @return array 参照した配列のキーマップを返す。
	 */
	public static function map($src, $base_name = "") {
		// 配列ではない
		if (!is_array($src)) {
			throw new Exception('$src params not type array');
		}
		// 文字列ではない
		if (!is_string($base_name)) {
			throw new Exception('$base_name params not type string');
		}

		// ベースのキー名が存在する場合は、その後ろに名前を付ける
		if (isset($base_name{0})) {
			$base_name .= ".";
		}

		// 出力するキーマップ用
		$keyMap = array();

		// 配列のキー名を配列に記憶する
		// 配列の値が配列の場合は再起的に取得する
		foreach ($src as $key => $value) {
			$keyMap[] = $base_name . $key;
			if (is_array($value)) {
				$subKeyMap = Hash::map($value, $base_name . $key);
				$keyMap = array_merge($keyMap, $subKeyMap);
			}
		}

		// キーマップを返す
		return($keyMap);
	}

	/**
	 * 配列の正規表現の参照パターンと一致した値を返す。
	 *
	 * @param array $src 参照する配列。
	 * @param string $match 参照パターン(正規表現)。
	 * @return array 参照パターンに一致した値をキーマップ文字列をキーとして配列で返す。
	 */
	public static function getPregMatch($src, $match) {
		// 配列ではない
		if (!is_array($src)) {
			throw new Exception('$src params not type array');
		}
		// 文字列ではない
		if (!is_string($match)) {
			throw new Exception('$match params not type string');
		}

		// 配列キーマップを取得
		$keyMaps = Hash::map($src);

		// 配列キーが正規表現と一致するものだけ記憶する
		$result = array();
		foreach ($keyMaps as $keyMap) {
			if (preg_match($match, $keyMap) === 1) {
				$result[$keyMap] = Hash::get($src, $keyMap);
			}
		}

		// 結果を返す
		return($result);
	}

	/**
	 * 配列の簡略参照パターンと一致した値を返す。
	 *
	 * @param array $src 参照する配列。
	 * @param string $format 参照パターン。
	 * @return array 参照パターンに一致した値をキーマップ文字列をキーとして配列で返す。
	 */
	public static function extract($src, $format) {
		// 配列ではない
		if (!is_array($src)) {
			throw new Exception('$src params not type array');
		}
		// 文字列ではない
		if (!is_string($format)) {
			throw new Exception('$match params not type string');
		}

		// 簡略パターンを正規表現文字列に変換
		$format = preg_quote($format, '/');
		$format = str_replace('¥{n¥}', '%d+', $format);   // 数値
		$format = str_replace('¥{s¥}', '[^.]+', $format); // 文字列
		$format = '/^' . $format . '$/';

		// 純粋な配列に変換している
		$result = array();
		$findData = Hash::get($src, $format);
		foreach ($findData as $value) {
			$result[] = $value;
		}

		// 結果を返す
		return($result);
	}

	/**
	 * 対象が連想配列か調べる。
	 *
	 * @param array $src 調査対象。
	 * @return boolean 連想配列ならTRUEを返す。
	 */
	public static function isHash($src) {
		// 配列ではない
		if (!is_array($src)) {
			return(false);
		}
		$i = 0;
		foreach ($src as $key => $value) {
			if ($i++ !== $key) {
				return(true);
			}
		}
		return(false);
	}

	/**
	 * 配列中の指定したキー名で配列を並び替える。
	 *
	 * @param array $src 対象の配列。
	 * @param string $chikd_key 並び替える時に基準となるキー。
	 * @return array 並び替えた配列。
	 */
	public static function keySort($src, $chikd_key) {
		// 配列ではない
		if (!is_array($src)) {
			throw new Exception('$src params not type array');
		}
		// 配列を並び替える
		$result = array();
		foreach ($src as $value) {
			$key = $value[$chikd_key];
			$result[$key] = $value;
		}
		// 返す
		return($result);
	}
}
