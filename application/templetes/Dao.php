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
	public $name = "templete";	/* テーブルまたはビューの名前を書きます(必須) */

	/** 自動トランザクション処理設定 */
	public $begin = true;
	/* トランザクションの自動化、記述しなければ設定が有効になります */

	/*
	 * テーブル名を記述するだけで最低限の機能は実装されてます
	 *
	 * $this->count()   : 件数を取得
	 * $this->find()    : データを取得
	 * $this->findRow() : 1件のデータを取得
	 * $this->insert()  : データを登録
	 * $this->update()  : データを変更
	 * $this->delete()  : データを削除
	 */

	/*
	 * 引数の種類
	 *   $data : データ
	 *     array(
	 *       [カラム名] => [値]
	 *       ...
	 *     )
	 *
	 *   $options : オプション
	 *     array(
	 *       'column' => [カラム名]
	 *       'where'  => [条件]
	 *       'order'  => [並び順]
	 *       ...
	 *     )
	 *
	 *     [カラム名]
	 *       表示するカラム名の配列
	 *       array(
	 *         [カラム名],
	 *         [カラム名] => [表示項目名]
	 *         ...
	 *       )
	 *
	 *     [条件]
	 *       array(
	 *         [条件式] => [値]
	 *         ...
	 *       )
	 *
	 *     [条件式]
	 *       'id = ?'        => 23             -> id = 23
	 *       'name LIKE ?'   => "%北%"         -> name LIKE '%北%'
	 *       'statuc IN (?)' => array(0,1,2)   -> status IN (0,1,2) 
	 *       'user_id'       => "test"         -> user_id = 'test'
	 *       'password'      => array("a","b") -> password IN ('a','b')
	 *
	 *     ※ 複数の条件はANDで結合します
	 *     ※ 値が空だと条件は作られません
	 *
	 *     [並び順]
	 *       並びの優先順位
	 *       array(
	 *         [カラム名],
	 *         [カラム名] ASC
	 *         ...
	 *       )
	 */

	/*
	 * 件数を取得
	 *   $this->count([$where])
	 *   @param $where : 条件 (省略可)
	 *   @return int 件数
	 */

	/*
	 * データを取得
	 *   $this->find([$option])
	 *   @param $option : オプション (省略可)
	 *   @return Zend_Db::FETCH_ASSOC データの配列
	 */

	/*
	 * 1件のデータを取得
	 *   $this->findRow([$where])
	 *   @param $where : 条件 (省略可)
	 *   @return Zend_Db::FETCH_ROW 1件のデータ
	 */

	/*
	 * データを登録
	 *   $this->insert($data)
	 *   @param $data : データ
	 *   @return int 登録したデータのプライマルキー
	 */

	/*
	 * データを変更
	 *   $this->update($data, [$where])
	 *   @param $data : データ
	 *   @param $where : 条件 (省略可)
	 *   @return int 変更した件数
	 */

	/*
	 * データを削除
	 *   $this->update([$where])
	 *   @param $where : 条件 (省略可)
	 *   @return int 削除した件数
	 */

	/**
	 * カスタムFIND関数
	 *
	 * @return Zend_Db_Table_Rowset データの配列
	 */
	public function customFind() {
		// SELECT作成
		$select = $this->table->select();

		// フィールド制限
		$select->from($this->table, array('user_id', 'password', 'name', 'status'));

		// 結合
		$select->join("templete2", "templete2.id = templete.id");
		$select->leftJoin("templete3", "templete3.id = templete.id");

		// 条件
		$select->where("id > ?", 0);
		$select->orWhere("user_id = ?", "vagrant");
		/*
		 * self::_createWhere関数で配列を自動解釈することもできます
		 * 詳細は BASE_Db_Table::_createWhere() を参照してください
		 */

		// 並び順
		$select->order("user_id");
		$select->order("id DESC");

		// 結果を返す
		return $this->db->fetchAll($select);
	}

	/**
	 * カスタムUNION関数
	 *
	 * @return Zend_Db_Table_Rowset データの配列
	 */
	public function customUnion() {
		// Union候補1
		$child_1 = $this->db->select();
		{
			// フィールド制限
			$child_1->from("table1", array('user_id', 'password', 'name', 'status'));

			// 結合
			$child_1->join("templete2", "templete2.id = templete.id");
			$child_1->leftJoin("templete3", "templete3.id = templete.id");

			// 条件
			$child_1->where("id > ?", 0);
			$child_1->orWhere("user_id = ?", "vagrant");
		}

		// Union候補2
		$child_2 = $this->db->select();
		{
			// フィールド制限
			$child_2->from("table2", array(
				'user_id'  => "test_id",	// test_id AS user_id
				'password' => "test_ps",	// test_ps AS pawword
				'name'     => "test_nm",	// test_nm AS name
				'status'
			));

			// 結合
			$child_2->join("templete2", "templete2.id = templete.id");
			$child_2->leftJoin("templete3", "templete3.id = templete.id");

			// 条件
			$child_2->where("id > ?", 0);
			$child_2->orWhere("user_id = ?", "vagrant");
		}

		// Union集合体
		$all = $this->db->select();
		$all->union($child_1, $child_2);
		// 並び順
		$all->order("user_id");
		$all->order("id DESC");

		// 結果を返す
		return $this->db->fetchAll($select);
	}

	/**
	 * カスタムUPDATE関数
	 *
	 * @param array $data データ
	 * @param array $where 条件
	 * @return Zend_Db_Table_Rowset データの配列
	 */
	public function customUpdate($data, $where) {
		// トランザクションの開始
		$this->_begin();

		/* insert/update/delete処理はロールバック用にTryCatchを使用する */
		try {
			// 登録
			$ret = $this->table->update($data, $where);
			/*
			 * self::_correctionWhere関数を使用するとデータと同じ形でも条件に使えます
			 * 詳細は BASE_Db_Table::_correctionWhere() を参照してください
			 */
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
}