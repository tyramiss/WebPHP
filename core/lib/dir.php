<?php
/**
 * ディレクトリ関連のクラス
 */
class Dir {
	/**
	 * ディレクトリであり読み込み可能かを調べる。
	 *
	 * @param string $directory ディレクトリのパス。
	 * @return boolean ディレクトリでいて読み込み可能であればTRUEを返す。
	 */
	public static function isRead($directory) {
		return(is_readable($directory) && is_executable($directory) && is_dir($directory));
	}

	/**
	 * 通常のファイルであり書き込み可能かを調べる。
	 *
	 * @param string $directory ディレクトリのパス。
	 * @return boolean ディレクトリでいて書き込み可能であればTRUEを返す。
	 */
	public static function isWrite($directory) {
		return(is_writable($directory) && is_executable($directory) && is_dir($directory));
	}

	/**
	 * 通常のファイルであり読み書き可能かを調べる。
	 *
	 * @param string $directory ディレクトリのパス。
	 * @return boolean ディレクトリでいて読み書き可能であればTRUEを返す。
	 */
	public static function isReadWrite($directory) {
		return(is_readable($directory) && is_writable($directory) && is_executable($directory) && is_dir($directory));
	}
}