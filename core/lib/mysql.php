<?php
class baseDatabase {
	/* -- グローバル変数 -- */

	// 設定
	public $type;
	public $host;
	public $user;
	public $password;
	public $port;
	public $socket;
	public $charset;

	// テーブル
	public $table;

	// MySQL
	public $mysql;
	public $result;
	public $query;
	public $error;
	public $errno;

	/**
	 * コンストラクタ
	 */
	public function __construct($params) {
		// データベースの設定内容
		$config = config("db");

		// デフォルトの設定
		$this->type = "mysql";
		$this->host = "";
		$this->user = "";
		$this->password = "";
		$this->charset = "utf8";
		$this->port = ini_get("mysqli.default_port");
		$this->socket = ini_get("mysqli.default_socket");

		// データベース設定
		$this->_setConfig('host', $params);
		$this->_setConfig('user', $params);
		$this->_setConfig('password', $params);
		$this->_setConfig('port', $params);
		$this->_setConfig('socket', $params);
		$this->_setConfig('charset', $charset);

		// テーブル
		if (isset($params['table'])) {
			$this->table = $params['table'];
		}

		// データベースへ接続
		$this->mysql = new mysqli($this->host, $this->user, $this->password, $this->port, $this->socket);
		$this->mysql->set_charset($this->charset);
		$this->mysql->autocommit(false);
		$this->result = null;
		$this->query = "";
		$this->error = "";
		$this->errno = 0;
	}

	/**
	 * デストラクタ
	 */
	public function __destruct() {
		// データベース終了
		$this->mysql->close();
	}

	/**
	 * コミット
	 */
	public function commit() {
		$this->mysql->commit();
	}

	/**
	 * ロールバック
	 */
	public function rollback() {
		$this->mysql->rollback();
	}

	/**
	 * クエリ結果を取得する。
	 *
	 * @param array $sql クエリ条件。
	 * @return array クエリの結果。
	 */
	public function find($sql = array()) {
		// クエリ発行
		$this->query = _queryCode($sql);
		$this->result = $this->mysql->query($this->query);
		$this->errno = $this->mysql->errno;
		$this->error = $this->mysql->error;

		// クエリ結果を取得
		$result = array();
		while ($data = $this->result->fetch_assoc()) {
			$result[] = $data;
		}
		return($result);
	}

	/**
	 * クエリ結果の行数を取得する。
	 *
	 * @param array $sql クエリ条件。
	 * @return array クエリの結果。
	 */
	public function count($sql = array()) {
		// カラム部分を意図的に変更する
		$sql['column'] = "count(*)";
		// クエリ発行
		$this->query = _queryCode($sql);
		$this->result = $this->mysql->query($this->query);
		$this->errno = $this->mysql->errno;
		$this->error = $this->mysql->error;

		// クエリ結果を取得
		$result = array();
		while ($data = $this->result->fetch_row()) {
			$result[] = $data;
		}
		return($result);
	}

	/**
	 * クエリコードの生成。
	 *
	 * @param array $sql クエリコード配列。
	 * @return string クエリコード。
	 */
	private function _queryCode($sql = array()) {
		// デフォルト
		$uniqe = "";
		$print = "*";
		$join = "";
		$where = "";
		$group = "";
		$order = "";
		$limit = "";

		// ベースとなるテーブル
		$table = $this->table;
		if (isset($sql['table'])) {
			$table = $sql['table'];
		}

		foreach ($sql as $key => $value) {
			switch($key) {
				// 重複
				case 'distinct' :
					if ($value) {
						$uniqe = " DISTINCT";
					}
					break;
				// カラム
				case 'column' :
					{
						if (is_array($value)) {
							$print = join(',', $value);
						}
						else {
							$print = $value;
						}
					}
					break;

				// 結合
				case 'inner' :
				case 'left' :
					{
						// 'inner' => ['table.id' => 'table.id'];
						foreach ($value as $base => $chain) {
							$joinTable = current(preg_split(".", $base));
							$join .= " {$key} JOIN {$joinTable} ON {$base} = {$chain}";
						}
					}
					break;

				// 条件
				case 'where' :
					{
						$where = " WHERE " . baseDatabase::_whereAnd($value);
					}
					break;

				// グループ
				case 'group' :
					{
						$group = " GROUP BY ";
						if (is_array($value)) {
							$group .= join(",", $value);
						}
						else {
							$group .= $value;
						}
					}
					break;

				// 並び順
				case 'order' :
					{
						$orders = array();
						foreach ($value as $column => $sort) {
							$orders[] = "{$column} {$type}";
						}
						$order = " ORDER BY " . join(",", $orders);
					}
					break;

				// 制限
				case 'limit' :
					{
						$limit = " LIMIT {$value}";
					}
					break;
			}
		}

		// クエリ文完成
		return("SELECT{$uniqe} {$print} FROM {$table}{$join}{$where}{$group}{$order}{$limit}");
	}

	/**
	 * 設定内容の反映。
	 *
	 * @param string $name 反映先の名前。
	 */
	private function _setConfig($name, $params) {
		if (isset($params[$name])) {
			$this->{$name} = $params[$name];
			return;
		}
		$config = config("db");
		if (isset($config[$name])) {
			$this->{$name} = $config[$name];
		}
	}

	/**
	 * WHERE解析(AND)。
	 *
	 * @param array WHERE条件配列。
	 * @return string SQLクエリ文。
	 */
	private static function _whereAnd($where) {
		$query = array();
		foreach ($where as $key => $value) {
			if (strtoupper($key) === "OR") {
				$query[] = baseDatabase::_whereOr($value);
			}
			else {
				$query[] = baseDatabase::_whereDecode($value);
			}
		}
		return("(" . join(" AND ", $query) . ")");
	}

	/**
	 * WHERE解析(OR)。
	 *
	 * @param array WHERE条件配列。
	 * @return string SQLクエリ文。
	 */
	private static function _whereOr($where) {
		$query = array();
		foreach ($where as $key => $value) {
			if (strtoupper($key) === "AND") {
				$query[] = baseDatabase::_whereAnd($value);
			}
			else {
				$query[] = baseDatabase::_whereDecode($value);
			}
		}
		return("(" . join(" OR ", $query) . ")");
	}

	/**
	 * WHERE解析クエリ文を返す。
	 *
	 * @param array WHERE条件配列。
	 * @return string SQLクエリ文。
	 */
	private static function _whereDecode($where) {
		// 直クエリ
		if (!is_array($where)) {
			return($where);
		}

		list($left, $right) = each($where);
		// 参照値が複数ある
		if (is_array($right)) {
			$split = join("','", $right);
			return("{$left}('{$split}')");
		}
		// NULLの場合
		if ($right === null) {
			return("{$left} IS NULL");
		}
		// 単値
		return("{$left}'{$right}'");
	}
}
