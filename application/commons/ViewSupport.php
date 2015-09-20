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
