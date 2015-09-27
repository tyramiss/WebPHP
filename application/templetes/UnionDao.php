<?php
// include
require_once APP_COMMON . 'Select.php';
require_once APP_MODEL . 'TempleteDao1.php';		/* 連携するテーブルのDAOを書きます */
require_once APP_MODEL . 'TempleteDao2.php';

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
class UnionDao extends Base_Db_Select	/* Base_Db_Table と違う事に注意 */
{
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
	 * Unionによる結果を取得
	 *
	 * @return Base_Db_Table 自身を返す
	 */
	public function customUnion() {
		// 結合するDAO1
		$dao1 = new TempleteDao1($this->db());
		$select1 = $dao1->select();
		$select1->where(array('id = ?' => 11));

		// 結合するDAO1
		$dao2 = new TempleteDao2($this->db());
		$dao2->customFind();

		// Union結合
		$select = $this->union(array($dao1, $dao2));

		// 並び順
		$select->order("id");
		$select->order("date ASC");

		/*
		 * コントローラー側で BASE_Db_Table::fetch() 等を使用する為に自身を返しています
		 * 関数呼び出し時に結果セットが必要であれば $this->fetch() 等にしてください
		 */
		return $this;
	}
}