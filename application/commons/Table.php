<?php
// include
require_once 'Zend/Db/Table.php';

/**
 * データベースの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
class DB
{
	/** DB接続 */
	public $db;

	/**
	 * データベースの接続
	 *
	 * @return Zend_Db 接続済みのデータベースオブジェクト
	 */
	public static function Connect() {
		// データベースの設定を取得
		$config = new Zend_Config_Ini(APP_CONFIG . "application.ini", "database");
		// データベースへ接続
		return Zend_Db::factory($config->adapter, $config->params);
	}

	/**
	 * コンストラクタ
	 */
	function __construct() {
		// データベースへ接続
		$this->db = Self::Connect();
	}

	/**
	 * トランザクション開始
	 */
	public function begin() {
		$this->db->beginTransaction();
	}

	/**
	 * コミット
	 */
	public function commit() {
		$this->db->commit();
	}

	/**
	 * ロールバック
	 */
	public function rollback() {
		$this->db->rollback();
	}
}

/**
 * テーブルの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
abstract class BASE_Db_Table
{
	/** テーブルの名前 */
	public $name;

	/** データベース */
	public $db;

	/** テーブル */
	public $table;

	/** 自動トランザクション処理設定(デフォルトではON) */
	public $begin = true;

	/**
	 * コンストラクタ
	 *
	 * @param Zend_Db $db 接続済みのデータベースオブジェクト
	 * @param boolean $begin 自動トランザクション設定
	 */
	function __construct($db = null, $begin = null) {
		// データベースへ接続
		if (is_null($db)) {
			$db = DB::Connect();
		}

		// 自動トランザクション設定
		if (is_bool($begin)) {
			$this->begin = $begin;
		}

		// データベースをクラスメンバに反映
		$this->db = $db;

		// テーブルへ接続
		$this->table = new Zend_Db_Table(array('db' => $db, 'name' => $this->name));
	}

	/**
	 * 件数の取得
	 *
	 * @param array $where 条件
	 * @return int 件数
	 */
	public function count($where = array()) {
		// SELECT作成
		$select = $this->table->select();

		// 表示を件数のみ
		$select->from($this->table, "COUNT(*)");

		// 条件あり
		if (!empty($where)) {
			$select = self::_createWhere($select, $where);
		}

		// 結果を返す
		return $this->db->fetchOne($select);
	}

	/**
	 * データの取得
	 *
	 * @param array $option オプション
	 * @return Zend_Db::FETCH_ASSOC データのリスト
	 */
	public function find($option = array()) {
		// SELECT作成
		$select = $this->table->select();

		// フィールド条件
		if (!empty($option['column'])) {
			$select->form($this->table, $option['column']);
		}

		// 条件
		if (!empty($option['where'])) {
			// 条件の作成
			$select = self::_createWhere($select, $option['where']);
		}

		// 並び順
		if (!empty($option['order'])) {
			$select->order($option['order']);
		}

		// 結果を返す
		return $this->db->fetchAll($select);
	}

	/**
	 * 1件のデータを取得
	 *
	 * @param array $where 条件
	 * @return Zend_Db::FETCH_ROW 1件のデータ
	 */
	public function findRow($where = array()) {
		// 条件なし
		if (empty($where)) {
			return $this->table->fetchAll();
		}

		// SELECT作成
		$select = $this->table->select();

		// 条件の作成
		$select = self::_createWhere($select, $where);

		// 結果を返す
		return $this->db->fetchRow($select);
	}

	/**
	 * データの登録
	 *
	 * @param array $data 登録するデータ
	 * @return int 登録したデータのプライマルキー
	 */
	public function insert($data) {
		// トランザクションの開始
		$this->_begin();

		try {
			// 登録
			$ret = $this->table->insert($data);
			// コミット
			$this->_commit();
			// 結果を返す
			return $ret;
		}
		catch (Zend_Exception $e) {
			$this->_rollback();
			throw $e;
		}
	}

	/**
	 * データの変更
	 *
	 * @param array $data 登録するデータ
	 * @param array $where 条件
	 * @return int 変更した件数
	 */
	public function update($data, $where = array()) {
		// トランザクションの開始
		$this->_begin();

		try {
			// 登録
			$ret = $this->table->update($data, self::_correctionWhere($where));
			// コミット
			$this->_commit();
			// 結果を返す
			return $ret;
		}
		catch(Zend_Exception $e) {
			$this->_rollback();
			throw $e;
		}
	}

	/**
	 * データの削除
	 *
	 * @param array $where 条件
	 * @return int 削除した件数
	 */
	public function delete($where = array()) {
		// トランザクションの開始
		$this->_begin();

		try {
			// 登録
			$ret = $this->table->delete(self::_correctionWhere($where));
			// コミット
			$this->_commit();
			// 結果を返す
			return $ret;
		}
		catch(Zend_Exception $e) {
			$this->_rollback();
			throw $e;
		}
	}

	/**
	 * 条件の補正
	 *
	 * @param array $where 条件
	 * @return array 補正された条件
	 */
	protected static function _correctionWhere($where) {
		// 条件の作成
		$correction = array();
		foreach ($where as $key => $value) {
			// 条件式ではない
			if (strpos($key, "?") === false) {
				// 値が配列
				if (is_array($value)) {
					$correction[$key . " IN (?)"] = $value;
				}
				else {
					$correction[$key . " = ?"] = $value;
				}
			}
			else {
				$correction[$key] = $value;
			}
		}
		// 補正された条件を返す
		return $correction;
	}

	/**
	 * 条件の作成
	 *
	 * @param Zend_Db_Select $select Selectオブジェクト
	 * @param array $where 条件
	 * @return Zend_Db_Select 条件の追加されたSelectオブジェクト
	 */
	protected static function _createWhere($select, $where) {
		// 条件を補正
		$where = self::_correctionWhere($where);
		// 条件の作成
		foreach ($where as $key => $value) {
			// 値が空の場合は除外
			if (!empty($value)) {
				$select->where($key, $value);
			}
		}
		// 条件の追加されたSelectオブジェクトを返す
		return $select;
	}

	/**
	 * トランザクション開始
	 */
	protected function _begin() {
		if ($this->begin) {
			$this->db->beginTransaction();
		}
	}

	/**
	 * コミット
	 */
	protected function _commit() {
		if ($this->begin) {
			$this->db->commit();
		}
	}

	/**
	 * ロールバック
	 */
	protected function _rollback() {
		if ($this->begin) {
			$this->db->rollback();
		}
	}
}

/**
 * UNIONテーブルの基本クラス
 *
 * @author Navi
 * @version 1.0.0
 */
abstract class BASE_Db_Union
{
	/** テーブルの名前 */
	public $name;

	/** データベース */
	public $db;

	/** テーブル */
	public $table;

	/** フィールド名変換候補 */
	public $as = array();

	/**
	 * コンストラクタ
	 *
	 * @param Zend_Db $db 接続済みのデータベースオブジェクト
	 * @param boolean $begin 自動トランザクション設定
	 */
	function __construct($db = null) {
		// データベースへ接続
		if (is_null($db)) {
			$db = DB::Connect();
		}

		// データベースをクラスメンバに反映
		$this->db = $db;

		// 複数のテーブル処理
		$this->_select = array();
		foreach ($this->name as $name) {
			// テーブルへ接続
			$this->table[$name] = new Zend_Db_Table(array('db' => $db, 'name' => $name));
		}
	}

	/**
	 * SELECTクラスを取得する
	 *
	 * return Zend_Db_Select SELECTクラス
	 */
	public function select($name = null) {
		// 名前指定がない場合はDBから作成
		if (empty($name)) {
			return $this->db->select();
		}
		// 名前指定がある場合はテーブルから作成
		return $this->table[$name]->select();
	}

	/**
	 * UNION結合済みのSELECTクラスを取得する
	 *
	 * return Zend_Db_Select SELECTクラス
	 */
	public function union() {
		$all = $this->db->select();
		foreach ($this->_select as $select) {
			$all->union($select);
		}
		return $all;
	}

	/**
	 * データの取得
	 *
	 * @param array $option オプション
	 * @return Zend_Db::FETCH_ASSOC データのリスト
	 */
	public function find($option = array()) {
		// 全体SELECTクラス
		$select = $this->db->select();

		// 各テーブル処理
		foreach ($this->table as $name => $table) {
			$child = $table->select();
			$child->form($table, $this->getColumns($name));
			$select->union($child);
		}

		// フィールド条件
		if (!empty($option['column'])) {
			$select->form($this->table, $option['column']);
		}

		// 条件
		if (!empty($option['where'])) {
			// 条件の作成
			$select = BASE_Db_Table::_createWhere($select, $option['where']);
		}

		// 並び順
		if (!empty($option['order'])) {
			$select->order($option['order']);
		}

		// 結果を返す
		return $this->db->fetchAll($select);
	}

	/**
	 * カラム名を取得する
	 *
	 * @param string $name 取得したいテーブル名
	 * @param string $filed 取得したいテーブル名
	 * @return string カラム名を返す。存在しなければ NULL を返す
	 */
	public function getColumn($name, $filed) {
		// 存在する変数
		if (isset($this->as[$filed][$name])) {
			return $this->as[$filed][$name];
		}
		// 存在しなかった
		return null;
	}

	/**
	 * フィールド名を取得する
	 *
	 * @param string $name 取得したいテーブル名
	 * @return string|array フィールド名
	 */
	public function getColumns($name) {
		// フィールド名用
		$columns = array();
		// 変換候補から作成
		foreach ($this->as as $key => $info) {
			if (isset($info[$name])) {
				$columns[$key] = $info[$name];
			}
		}
		// 変換候補なしの場合は全カラム
		if (empty($columns)) {
			$columns = "*";
		}
		return $columns;
	}

	/**
	 * フィールド名を登録する
	 *
	 * @param string $name 登録したいテーブル名
	 * @param string $column カラム名
	 * @param string $filed フィールド名
	 */
	public function addColumns($name, $column, $filed) {
		// 初回作成
		if (empty($this->as[$filed])) {
			$this->as[$filed] = array();
		}
		// 登録
		$this->as[$filed][$name] = $column;
	}

	/**
	 * フィールド名から削除する
	 *
	 * @param string $name 削除したいテーブル名
	 * @param string$filed フィールド名
	 */
	public function removeColumns($name, $filed = null) {
		foreach ($this->as as $key => $info) {
			// 候補ではない
			if (empty($info[$name])) {
				next;
			}
			// カラム名なし
			if (empty($fileds) || $key == $filed) {
				unlink($info[$name]);
			}
		}
	}
}
