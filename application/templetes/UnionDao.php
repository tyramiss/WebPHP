<?php
// include
require_once APP_COMMON . 'Table.php';

/**
 * UnionTableDAOのテンプレート
 *
 * DAOの説明
 * どういうテーブルに繋がっているかの説明とか
 * こんなことができるとか、書きたいこといろいろと
 * 好きなことを書いてください
 *
 * @author Navi
 * @version 1.0.0
 */
class TempleteDao extends BASE_Db_Union
{
	/** テーブル名 */
	public $name = array("templete1", "templete2");	/* テーブルまたはビューの名前を書きます(必須) */
	/* UNION結合するテーブルまたはビュー名を配列で記述してください */

	/** フィールド名変換候補 */
	public $as = array(		/* 後で追加や削除、変更ができます。変数宣言なし、空の場合は"*"となります */
		'user_id' => array(
			'templete1' => "main_id",	/* SQL → templete1.main_id AS user_id */
			'templete2' => "test_id"	/* SQL → templete2.test_id AS user_id */
		),
		'password' => array(
			'templete1' => "main_ps",
			'templete2' => "test_ps"
		)
	);

	/*
	 * テーブル名を記述するだけで最低限の機能は実装されてます
	 *
	 * $this->find() : データを取得
	 */

	/*
	 * 引数の種類
	 *   $data : データ
	 *     array(
	 *       [カラム名] => [値]
	 *       ...
	 *     )
	 *
	 *   $option : オプション
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
	 * データを取得
	 *   $this->find([$option])
	 *   @param $option : オプション (省略可)
	 *   @return Zend_Db::FETCH_ASSOC データの配列
	 */

	/**
	 * カスタムUNION関数
	 *
	 * @return Zend_Db_Table_Rowset データの配列
	 */
	public function customUnion() {
		// 各テーブルのSELECTを設定
		$child_1 = $this->select("templete1");	/* $this->db->select()と同じ */
		// フィールド名変換候補を使用してフィールド名を制限
		$child_1->from("templete1", $this->getColumns("templete1"));
		// 結合
		$child_1->join("templete3", "templete3.id = templete1.id");

		/*
		 * その他は Zend_Db_Table でできる事と同じです
		 */

		// 各テーブルのSELECTを設定
		$child_2 = $this->select("templete2");
		// 結合
		$child_2->leftJoin("templete3", "templete3.id = templete2.id");

		// Union
		$all = $this->union($child_1, $child_2);
		// 条件
		$all->where("id > ?", 0);
		$all->orWhere("user_id = ?", "vagrant");
		/*
		 * self::_createWhere関数で配列を自動解釈することもできます
		 * 詳細は BASE_Db_Table::_createWhere() を参照してください
		 */
		// 並び順
		$all->order("user_id");

		// 結果を返す
		return $this->db->fetchAll($all);
	}

	/**
	 * 通常find関数にwhere区のみ独自
	 *
	 * @param array $option オプション
	 * @return Zend_Db::FETCH_ASSOC データのリスト
	 */
	public function customFind($option) {
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
		if (!empty($option['column'])) {
			foreach ($option['column'] as $key => $value) {
				if (empty($value)) {
					next;
				}
				switch($key) {
					case "id"       : $select->where("id > ?", $value); break;
					case "user_id"  : $select->orWhere("user_id LIKE ?", $value); break;
					case "password" : $select->where("password = ?", $value); break;
					case "text" :
						// AND (name LIKE '%***%' OR kana LIKE '%***%')
						$like = "%{$value}%";
						$select->where("name LIKE ? OR kana LIKE ?", array($like, $like));
						break;
				}
			}
		}

		// 並び順
		if (!empty($option['order'])) {
			$select->order($option['order']);
		}

		// 結果を返す
		return $this->db->fetchAll($select);
	}
}