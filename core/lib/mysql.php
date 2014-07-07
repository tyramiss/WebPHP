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
	 * クエリ結果を取得する
	 *
	 * @param array $condition クエリ条件
	 * @return array クエリの結果
	 */
	public function find($condition = array()) {
	}

	private function _queryCode($condition = array()) {
		// デフォルト
		$column = "*";
		$table = $this->table;

		foreach ($condition as $key => $value) {
			switch($key) {
				// カラム
				case 'column' :
					{
						if (is_array($value)) {
							$column = join(',', $value);
						}
						else {
							$column = $value;
						}
					}
					break;
			}
		}
	}

	/**
	 * 設定内容の反映
	 *
	 * @param string $name 反映先の名前
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
}
