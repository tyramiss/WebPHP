<?php
/**
 * テーブルの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
abstract class Base_Db_Select implements Iterator
{
	/**
	 * データベースオブジェクト
	 */
	private $_db;

	/**
	 * セレクトオブジェクト
	 */
	private $_select;

	/**
	 * ステートメント
	 */
	private $_statement;

	/**
	 * フェッチモード
	 */
	private $_mode;

	/**
	 * 位置
	 */
	private $_pos;

	/**
	 * 現在の行データ
	 */
	private $_row;

	/**
	 * コンストラクタ
	 *
	 * @param Zend_Db $db 接続済みのデータベースオブジェクト
	 * @param boolean $begin 自動トランザクション設定
	 */
	function __construct($db) {
		// データベースオブジェクト
		$this->db = $db;
		// 初期SELECT作成
		$this->_select = $this->db->select();

		// デフォルトのフェッチモード
		$this->_mode = Zend_Db::FETCH_ASSOC;
	}

	/**
	 * データベースオブジェクトの取得
	 *
	 * @return Zend_Db データベースオブジェクト
	 */
	public function db() {
		return $this->_db;
	}

	/**
	 * フェッチモードの設定
	 *
	 * @params Zend_Db::CONST $mode フェッチ結果の種類
	 */
	public function setFetchMode($mode) {
		$this->_mode = $mode;
	}

	/**
	 * SELECTを作成
	 *
	 * @return Zend_Db_Select SELECTオブジェクト
	 */
	public function &select() {
		$this->_select = $this->db->select();
		return $this->_select;
	}

	/**
	 * SELECTを取得
	 *
	 * @return Zend_Db_Select SELECTオブジェクト
	 */
	public function &get() {
		return $this->_select;
	}

	/**
	 * 結果セットを結合する
	 *
	 * @param mixed $tables 結合するテーブル一覧
	 * @return Zend_Db_Select 結合したSELECTオブジェクト
	 */
	public function union($tables, $mode = Zend_Db_Select::SQL_UNION_ALL) {
		// 配列ではない
		if (!is_array($tables)) {
			$tables = array($tables);
		}

		// 変換処理
		foreach ($tables as &$table) {
			// オブジェクト
			if (is_object($table)) {
				// 自身のクラス
				$class = array(get_class($table) => get_class($table));
				// 親クラス
				$class += class_parents($table);
				// Base_Db_Table
				if (in_array("Base_Db_Table", $class, true)) {
					$table = $table->get();
				}
				// Base_Db_Select
				else if (in_array("Base_Db_Select", $class, true)) {
					$table = $table->get();
				}
				// Zend_Db_Table_Abstract
				else if (in_array("Zend_Db_Table_Abstract", $class, true)) {
					$table = $table->select();
				}
				// Zend_Db_Table
				else if (in_array("Zend_Db_Table", $class, true)) {
					$table = $table->select();
				}
			}
		}

		// Union
		$this->_select->union($tables, $mode);

		// 結果を返す
		return $this->_select;
	}

	/**
	 * ステートメントの作成
	 *
	 * @return Zend_Db_Statement ステートメント
	 */
	public function query() {
		return $this->_db->query($this->_select);
	}

	/**
	 * 結果セットからの単一の行の取得
	 *
	 * @return Zend_Db_Table_Row 結果セットの1行
	 */
	public function fetch() {
		return $this->_statement->fetch();
	}

	/**
	 * 結果セット全体の取得
	 *
	 * @return Zend_Db_Table_Rowset 結果セットの全行
	 */
	public function fetchAll() {
		return $this->_db->fetchAll($this->_select);
	}

	/**
	 * 連想配列形式での結果セットの取得
	 *
	 * @return Zend_Db_Table_Rowset 結果セットの全行
	 */
	public function fetchAssoc() {
		return $this->_db->fetchAssoc($this->_select);
	}

	/**
	 * 結果セットの単一のカラムの取得
	 *
	 * @return Zend_Db_Table_Row 結果セットの最初の行
	 */
	public function fetchCol() {
		return $this->_db->fetchCol($this->_select);
	}

	/**
	 * 結果セットからの キー/値 のペアの取得
	 *
	 * @return Array 連想配列(1つ目のカラム => 2つ目のカラム)
	 */
	public function fetchPairs() {
		return $this->_db->fetchPairs($this->_select);
	}

	/**
	 * 結果セットからの単一の行の取得
	 *
	 * @return Zend_Db_Table_Row 結果セットの最初の行
	 */
	public function fetchRow() {
		return $this->_db->fetchRow($this->_select);
	}

	/**
	 * 結果セットからの単一のスカラー値の取得
	 *
	 * @return int|string 最初の行の最初のスカラー値
	 */
	public function fetchOne() {
		return $this->_db->fetchOne($this->_select);
	}

	/**
	 * クラスの文字列出力
	 *
	 * @return string SQL文を出力
	 */
	public function __toString() {
		return $this->_select->__toString();
	}

	/**
	 * Array:reset()
	 */
	public function rewind() {
		$this->_pos = 0;
		$this->_statement = $this->_db->query($this->_select);
		$this->_row = $this->_statement->fetch();
	}

	/**
	 * Array:key()
	 */
	public function key() {
		return $this->_pos;
	}

	/**
	 * Array:current()
	 */
	public function current() {
		return $this->_row;
	}

	/**
	 * Array:next()
	 */
	public function next() {
		$this->_pos++;
		$this->_row = $this->_statement->fetch();
		return $this->_row;
	}

	/**
	 * Array:valid()
	 */
	public function valid() {
		return ($this->_row !== false);
	}
}
