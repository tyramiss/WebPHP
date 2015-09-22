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
		$this->table = new Zend_Db_Table(array('db' => $this->db, 'name' => $this->name));
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
	 * @param array $where 条件
	 * @return Zend_Db::FETCH_ASSOC データのリスト
	 */
	public function find($where = array()) {
		// 条件なし
		if (empty($where)) {
			return $this->table->fetchAll();
		}

		// SELECT作成
		$select = $this->table->select();

		// 条件の作成
		$select = self::_createWhere($select, $where);

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
			$select->where($key, $value);
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
