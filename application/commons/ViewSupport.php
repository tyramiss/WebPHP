<?php
/**
 * 表示サポート関数
 *
 * 表示する際に使用するあると便利な関数達をこのファイル内に記述しておく
 *
 * @author Navi
 * @version 1.0.0
 */

/** HTML エンティティ変換 */
define('HTML_ENTITY' , ENT_QUOTES | ENT_HTML401);

/** エラー時のクラス名 */
define('CLASS_ERROR', "errors");


/**
 * 特殊文字を HTML エンティティに変換する
 *
 * @param string $str エスケープ処理したい文字列
 * @return string エスケープ処理された文字列
 */
function h($str) {
	return htmlspecialchars($str, HTML_ENTITY);
}

/**
 * 特殊文字と改行を変換する
 *
 * @param string $str エスケープ処理したい文字列
 * @return string エスケープ処理された文字列
 */
function h_br($str) {
	return nl2br(h($str));
}

/**
 * 特殊文字と改行とタブを変換する
 *
 * @param string $str エスケープ処理したい文字列
 * @return string エスケープ処理された文字列
 */
function h_sys($str) {
	return nl2br(str_replace(" ","&nbsp;", str_replace("\t", "    ", h($str))));
}

/**
 * エラーがある場合にクラス名を返す
 *
 * @param array $error エラー内容
 * @param string 確認するエラー変数名
 * @return string エラー時はエラークラスを返す
 */
function cs_err() {
	// エラー内容
	$error = func_get_arg(0);
	// 複数のチェックに対応
	for ($num = 1; $num < func_num_args(); $num++) {
		$name = func_get_arg($num);
		// エラー内容に指定した変数名が存在した
		if (array_key_exists($name, $error)) {
			return CLASS_ERROR;
		}
	}
	// エラーはなかった
	return "";
}
