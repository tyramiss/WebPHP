<?php
// include
require_once 'Zend/Db/Table.php';

/**
 * テーブルの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
abstract class Base_Db_Table extends Zend_Db_Table_Abstract implements Iterator
{
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
		// 親のコンストラクタ
		parent::__construct(array('db' => $db));

		// 初期SELECT作成
		$this->_select = parent::select();
		$this->_select->setIntegrityCheck(false);

		// デフォルトのフェッチモード
		$this->_mode = Zend_Db::FETCH_ASSOC;
	}

	/**
	 * フェッチモードの設定
	 */
	public function setFetchMode($mode) {
		$this->_mode = $mode;
	}

	/**
	 * セレクトオブジェクトを作成
	 */
	public function &select() {
		$this->_select = parent::select();
		$this->_select->setIntegrityCheck(false);
		return $this->_select;
	}

	/**
	 * セレクトオブジェクトを取得
	 */
	public function &get() {
		return $this->_select;
	}

	/**
	 * Union クエリ
	 */
	public function &union($tables, $mode = Zend_Db_Select::SQL_UNION) {
		// 配列ではない
		if (!is_array($tables)) {
			$tables = array($tables);
		}
		// 自身を含めた配列を作成
		$tables = array_merge(array($this->_select), $tables);

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
		$this->_select = $this->_db->select();
		$this->_select->union($tables, $mode);

		// 結果を返す
		return $this->_select;
	}

	/**
	 * ステートメントの作成
	 */
	public function query() {
		return $this->_db->query($this->_select);
	}

	/**
	 * 結果セットからの単一の行の取得
	 */
	public function fetch() {
		return $this->_statement->fetch();
	}

	/**
	 * 結果セット全体の取得
	 */
	public function fetchAll() {
		return $this->_db->fetchAll($this->_select);
	}

	/**
	 * 連想配列形式での結果セットの取得
	 */
	public function fetchAssoc() {
		return $this->_db->fetchAssoc($this->_select);
	}

	/**
	 * 結果セットの単一のカラムの取得
	 */
	public function fetchCol() {
		return $this->_db->fetchCol($this->_select);
	}

	/**
	 * 結果セットからの キー/値 のペアの取得
	 */
	public function fetchPairs() {
		return $this->_db->fetchPairs($this->_select);
	}

	/**
	 * 結果セットからの単一の行の取得
	 */
	public function fetchRow() {
		return $this->_db->fetchRow($this->_select);
	}

	/**
	 * 結果セットからの単一のスカラー値の取得
	 */
	public function fetchOne() {
		return $this->_db->fetchOne($this->_select);
	}

	/**
	 * クラスの文字列出力
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
