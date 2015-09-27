<?php
// include
require_once APP_COMMON . 'Table.php';

/**
 * DAOのテンプレート
 *
 * DAOの説明
 * どういうテーブルに繋がっているかの説明とか
 * こんなことができるとか、書きたいこといろいろと
 * 好きなことを書いてください
 *
 * @author Navi
 * @version 1.0.0
 */
class TempleteDao extends BASE_Db_Table
{
	/** テーブル名 */
	protected $_name = "templete";	/* テーブルまたはビューの名前を書きます(必須) */

	/*
	 * このクラスには下記の関数が用意されています
	 *
	 * $this->select()       : SELECTを作成
	 * $this->get()          : SELECTを取得
	 * $this->union()        : 結果セットを結合する
	 * $this->query()        : ステートメントの作成
	 * $this->fetch()        : 結果セットからの単一の行の取得
	 * $this->fetchAll()     : 結果セット全体の取得
	 * $this->fetchAssoc()   : 連想配列形式での結果セットの取得
	 * $this->fetchCol()     : 結果セットの単一のカラムの取得
	 * $this->fetchPairs()   : 結果セットからの キー/値 のペアの取得
	 * $this->fetchRow()     : 結果セットからの単一の行の取得
	 * $this->fetchOne()     : 結果セットからの単一のスカラー値の取得
	 * $this->setFetchMode() : フェッチモードの設定
	 * $this->insert()       : データを登録
	 * $this->update()       : データを変更
	 * $this->delete()       : データを削除
	 *
	 * $this->fetch〜 はZend_Db_Selectのドキュメントの各関数と同じ動作します
	 */

	/*
	 * このクラスは foreach に対応しています
	 * 下記の動作は同じになります
	 *
	 * foreach
	 *   foreach($this as $key => $row) {
	 *       var_dump($row);
	 *   }
	 *
	 * while & fetch
	 *   $key = 0;
	 *   while ($row = $this->fetch()) {
	 *       var_dump($row);
	 *       $key++;
	 *   }
	 *
	 * fetchAll
	 *   $list = $this->fetchAll();
	 *   foreach($list as $key => $row) {
	 *       var_dump($row);
	 *   }
	 */

	/**
	 * 条件による結果を取得
	 *
	 * @param array $where 条件
	 * @return Base_Db_Table 自身を返す
	 */
	public function customSelect($where = array()) {
		// SELECT作成
		$select = $this->select();

		// 条件
		foreach ($where as $key => $value) {
			// 値が空なら何もしない
			if (empty($value)) {
				continue;
			}
			// 渡されたキー名ごとにwhereの条件を変える
			switch ($key) {
				case 'id'   : $select->where("id = ?", $value); break;
				case 'name' : $select->where("name LIKE ? OR kana LIKE ?", array($value, $value)); break;
				case 'date' : $select->where("date > ?", $value); break;
			}
		}

		// 並び順
		$select->order("id");
		$select->order("date ASC");

		/*
		 * コントローラー側で BASE_Db_Table::fetch() 等を使用する為に自身を返しています
		 * 関数呼び出し時に結果セットが必要であれば $this->fetch() 等にしてください
		 */
		return $this;
	}

	/**
	 * Join結果を取得
	 *
	 * @return Base_Db_Table 自身を返す
	 */
	public function customJoin() {
		// SELECT作成
		$select = $this->select();

		// フィールド制限
		$select->from($this->table, array('user_id', 'password', 'name', 'status'));

		// Join結合
		$select->join("templete2", "templete2.id = templete.id");
		$select->leftJoin("templete3", "templete3.id = templete.id");

		return $this;
	}

	/**
	 * Union結果を取得
	 *
	 * @param mixed $tables Unionするテーブル
	 * @return Base_Db_Table 自身を返す
	 */
	public function customUnion($union) {
		$this->union($union);

		return $this;
	}
}