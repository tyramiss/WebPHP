<?php
/**
 * ファイル関連のクラス
 */
class File {
	/**
	 * 通常のファイルであり読み込み可能かを調べる。
	 *
	 * @param string $filename ファイルへのパス。
	 * @return boolean 通常のファイルでいて読み込み可能であればTRUEを返す。
	 */
	public static function isRead($filename) {
		return(is_readable($filename) && is_file($filename));
	}

	/**
	 * 通常のファイルであり書き込み可能かを調べる。
	 *
	 * @param string $filename ファイルへのパス。
	 * @return boolean 通常のファイルでいて書き込み可能であればTRUEを返す。
	 */
	public static function isWrite($filename) {
		return(is_writable($filename) && is_file($filename));
	}

	/**
	 * 通常のファイルであり読み書き可能かを調べる。
	 *
	 * @param string $filename ファイルへのパス。
	 * @return boolean 通常のファイルでいて読み書き可能であればTRUEを返す。
	 */
	public static function isReadWrite($filename) {
		return(is_readable($filename) && is_writable($filename) && is_file($filename));
	}
}