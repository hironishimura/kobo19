<?php
/**
 * テーマを有効化したときに、制作物13点と固定ページを一度だけ登録する。
 *
 * 登録済みかどうかはオプション kobo19_demo_installed で判定するので、
 * 記事を消してもう一度入れ直したいときは、このオプションを削除してから
 * テーマを切り替え直してください。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 素の文章をブロックエディタの形式に変換する。
 *
 * 空行で段落に分け、「## 」で始まる行は見出し、「- 」で始まる行はリストにします。
 * **強調** は太字になります。この変換を通しておくと、管理画面で開いたときに
 * ひとかたまりのクラシックブロックではなく、編集できるブロックとして並びます。
 *
 * @param string $text 素の文章。
 * @return string
 */
function kobo19_to_blocks( $text ) {
	$chunks = preg_split( '/\n{2,}/', trim( $text ) );
	$blocks = array();

	foreach ( $chunks as $chunk ) {
		$chunk = trim( $chunk );

		if ( '' === $chunk ) {
			continue;
		}

		// 見出し
		if ( 0 === strpos( $chunk, '## ' ) ) {
			$heading  = kobo19_inline_markup( substr( $chunk, 3 ) );
			$blocks[] = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$heading}</h2>\n<!-- /wp:heading -->";
			continue;
		}

		// 箇条書き
		if ( 0 === strpos( $chunk, '- ' ) ) {
			$items = array();

			foreach ( explode( "\n", $chunk ) as $line ) {
				$line = trim( $line );
				if ( 0 === strpos( $line, '- ' ) ) {
					$items[] = "<!-- wp:list-item -->\n<li>" . kobo19_inline_markup( substr( $line, 2 ) ) . "</li>\n<!-- /wp:list-item -->";
				}
			}

			$blocks[] = "<!-- wp:list -->\n<ul class=\"wp-block-list\">" . implode( '', $items ) . "</ul>\n<!-- /wp:list -->";
			continue;
		}

		$paragraph = kobo19_inline_markup( str_replace( "\n", '<br>', $chunk ) );
		$blocks[]  = "<!-- wp:paragraph -->\n<p>{$paragraph}</p>\n<!-- /wp:paragraph -->";
	}

	return implode( "\n\n", $blocks );
}

/**
 * **強調** を太字タグに置き換える。
 *
 * @param string $text 文章。
 * @return string
 */
function kobo19_inline_markup( $text ) {
	return preg_replace( '/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $text );
}

/**
 * 制作物の初期データを返す。
 *
 * @return array<int, array<string, mixed>>
 */
function kobo19_demo_works() {
	return array(
		array(
			'title'    => 'design casa 宇都宮 ／ 株式会社エスホーム',
			'category' => 'homepage',
			'order'    => 1,
			'no'       => 'W-01',
			'year'     => '2026',
			'stack'    => 'WordPress / PHP / 自作テーマ',
			'status'   => '公開中',
			'client'   => '株式会社エスホーム（栃木県宇都宮市）',
			'excerpt'  => '建築家とつくる注文住宅ブランドの加盟工務店サイト。169ページ。',
			'content'  => "建築家とつくる注文住宅ブランド design casa の加盟工務店として、施工事例・イベント・スタッフ紹介・資料請求までを一つにまとめたサイトです。\n\nテーマはゼロから書き起こしました。ページビルダー系のプラグインを使わない代わりに、更新のしかたは管理画面の標準機能にそろえてあり、担当の方が事例を1件足すのに特別な手順は要りません。\n\n公開前の内容確認用に、169ページ分の静的プレビューを別途生成して共有しました。文言のチェックを、WordPressに触らずブラウザだけで回せるようにするためです。",
		),
		array(
			'title'    => '8月セミナー 告知ページ',
			'category' => 'homepage',
			'order'    => 2,
			'no'       => 'W-02',
			'year'     => '2026',
			'stack'    => 'HTML / CSS / WordPress 固定ページ',
			'status'   => '公開中',
			'client'   => '',
			'excerpt'  => '一日限りの催しに、一枚のページを。',
			'content'  => "開催日が決まっている催しのための、一枚完結の告知ページです。\n\n申込フォームを真ん中にはさむ構成にしてあり、前半・後半それぞれのHTMLをカスタムHTMLブロックとして貼り、間にフォームのブロックを置けば組み上がります。既存サイトのテーマを触らずに設置できるので、開催のたびに日付と内容を差し替えて使い回せます。",
		),
		array(
			'title'    => '検索順位チェッカー',
			'category' => 'program',
			'order'    => 1,
			'no'       => 'P-01',
			'year'     => '2026',
			'stack'    => 'Python / macOS メニューバー常駐 / Excel 出力',
			'status'   => '運用中',
			'client'   => '',
			'excerpt'  => '登録したキーワードの検索順位を、毎日ひとりでに記録する。',
			'content'  => "Google と Yahoo! JAPAN で自社サイトが何位に出ているかを、キーワードごとに毎日記録します。順位は広告枠を除いた自然検索のもので、掲載されているページのURLもあわせて残します。\n\n推移はグラフで確認でき、Excel に書き出せます。1日1回、キーワードごとに時間を分散して計測するので、まとめて叩きにいくことはありません。\n\nメニューバーに常駐させておけば、起動を意識せずに済みます。社内の複数台で共有する使い方にも対応しています。",
		),
		array(
			'title'    => '株価チェック',
			'category' => 'program',
			'order'    => 2,
			'no'       => 'P-02',
			'year'     => '2026',
			'stack'    => 'Python 標準ライブラリのみ / Yahoo Finance',
			'status'   => '運用中',
			'client'   => '',
			'excerpt'  => '保有している株・ETF・投資信託の値動きと収益を、ブラウザで見る。',
			'content'  => "アプリをダブルクリックするとブラウザが開き、売買の記録・訂正・株価の更新まで、すべてその画面で完結します。ターミナルは出ません。\n\n株価は Yahoo Finance から、投資信託の基準価額は Yahoo!ファイナンス（日本）から取得します。外部ライブラリは使っておらず、Mac に最初から入っている Python だけで動きます。\n\nサーバーは launchd が裏で動かすので、閉じ忘れても害はありません。終わるときは画面右上の終了ボタンを押します。",
		),
		array(
			'title'    => '日報システム',
			'category' => 'program',
			'order'    => 3,
			'no'       => 'P-03',
			'year'     => '2026',
			'stack'    => 'Node.js / LINE WORKS Board API / 生成AI',
			'status'   => '運用中',
			'client'   => '',
			'excerpt'  => '掲示板の日報を担当者ごとにまとめ、アドバイスを添えて本人に送る。',
			'content'  => "LINE WORKS の掲示板に投稿された日報を読み込み、担当者ごとに整理します。AI が内容を要約してアドバイスを添え、前回の TODO が片付いているかどうかも判定します。\n\nできあがったメールは送信前に画面で確認・修正できます。文面を直してから送る運用を前提にしているので、AI の書いたものがそのまま本人に届くことはありません。\n\nトークの過去メッセージは API から後追いで読めないため、日報は掲示板に投稿してもらう運用にしています。この制約の説明も含めて手順書を用意しました。",
		),
		array(
			'title'    => 'Letterly ／ 開封確認メーラー',
			'category' => 'program',
			'order'    => 4,
			'no'       => 'P-04',
			'year'     => '2026',
			'stack'    => 'Node.js / SMTP',
			'status'   => '完了',
			'client'   => '',
			'excerpt'  => '送ったメールが読まれたかどうかを記録する、小さなメーラー。',
			'content'  => "SMTP でメールを送り、本文に仕込んだトラッキングピクセルへのアクセスを開封として記録します。宛先ごとの開封状況を一覧で確認できます。\n\n手元で動かす前提の小さな構成です。この形で運用してみて分かったことを、次の名簿同期型の設計に持ち込みました。",
		),
		array(
			'title'    => '開封確認メーラー（名簿同期型）',
			'category' => 'program',
			'order'    => 5,
			'no'       => 'P-05',
			'year'     => '2026',
			'stack'    => 'Next.js / TypeScript / SendGrid Event Webhook',
			'status'   => '運用中',
			'client'   => '',
			'excerpt'  => '名簿の正本は自作アプリ、配信はSendGrid。',
			'content'  => "購読者名簿の正本を自作アプリのデータベースに置き、SendGrid 側はその写しとして同期します。配信そのものは SendGrid の管理画面から行うので、テンプレートや差し込みの機能を作り直す必要がありません。\n\n開封・クリック・配信停止・バウンスは Event Webhook で自作アプリに戻し、個人単位の履歴として積み上げます。配信停止は SendGrid 側で発生したものを正本に反映する、と向きを決めてあります。\n\n独自サブドメインからの送信に対応済みです。将来、配信画面まで自作したくなったときは、SendGrid の担当部分だけ差し替えれば済む構成にしています。",
		),
		array(
			'title'    => 'M³ 反射スペクトル解析ツール',
			'category' => 'program',
			'order'    => 6,
			'no'       => 'P-06',
			'year'     => '2026',
			'stack'    => 'JavaScript / Python / NASA PDS',
			'status'   => '公開中',
			'client'   => '',
			'excerpt'  => '月の地図をクリックして、その地点の鉱物を読む。',
			'content'  => "Chandrayaan-1 に搭載された月鉱物マッパー M³ のハイパースペクトルデータを開き、画像上をクリックした地点の反射スペクトルを表示して、吸収帯を自動で計算します。\n\n月の地図をクリックすると NASA PDS からその地点のシーンを取得し、画素を選ぶとスペクトルが出る、というところまでブラウザだけで完結します。検索もダウンロードも解析もサーバーを介しません。\n\n同じアルゴリズムを移植した Python パッケージも用意し、両者の解析結果が一致することを検証しています。",
		),
		array(
			'title'    => '不動産選択アプリ',
			'category' => 'program',
			'order'    => 7,
			'no'       => 'P-07',
			'year'     => '2026',
			'stack'    => 'TypeScript / 土地評価アルゴリズム / 生成AI',
			'status'   => '設計中',
			'client'   => '',
			'excerpt'  => '土地を家族で見比べて、決める。',
			'content'  => "候補に挙がった土地を項目ごとに採点し、家族で共有しながら比較するためのアプリです。日当たり・地盤・通学路といった条件をスコア化し、AI が所見を添えます。\n\n土地選びは一人で決められるものではないので、同じ画面を家族が見て、それぞれの重みづけを入れられることを前提に設計しています。\n\n現在は要件定義・画面設計・データベース設計・評価アルゴリズムの検討まで。実装はこれからです。",
		),
		array(
			'title'    => 'Suji ／ 打ちながら計算する電卓',
			'category' => 'product',
			'order'    => 1,
			'no'       => 'D-01',
			'year'     => '2026',
			'stack'    => 'Swift / SwiftUI / macOS',
			'status'   => '制作中',
			'client'   => '',
			'excerpt'  => '上から式を書いていくと、右端に答えが出る電卓。',
			'content'  => "式を上から書いていくと、行ごとの答えが右端に出ます。どの数字を書き換えても、それを使っている行がすべて計算し直されます。\n\n一度出した値には名前をつけられて、あとの行からいつでも呼び出せます。見積もりのように「前の答えを次に使う」計算を、消さずに全部残したまま進められます。\n\nボタンを押す電卓ではなく、書きながら考えるための道具として作っています。",
		),
		array(
			'title'    => 'ClaudeBar',
			'category' => 'product',
			'order'    => 2,
			'no'       => 'D-02',
			'year'     => '2026',
			'stack'    => 'Swift / macOS メニューバー常駐',
			'status'   => '運用中',
			'client'   => '',
			'excerpt'  => 'Claude Code の使用量と処理中のタスクを、メニューバーから見る。',
			'content'  => "使用率のゲージ、レート制限に到達した記録、今日と今月のトークン数と金額を、メニューバーから確認できる macOS の常駐アプリです。処理中のタスクもここに出ます。\n\n読みにいくのはローカルのファイルだけで、外に出る通信は「公式の使用率」を有効にしたときの一本に限っています。",
		),
		array(
			'title'    => 'Yチェア',
			'category' => 'product',
			'order'    => 3,
			'no'       => 'D-03',
			'year'     => '2026',
			'stack'    => 'Rhinoceros / Blender',
			'status'   => '完了',
			'client'   => '',
			'excerpt'  => '名作椅子を、図面から立体に起こす。',
			'content'  => "曲面でできた笠木と、編み込みのペーパーコード座面を3Dで再現しました。\n\n面は Rhinoceros でつくり、質感と光は Blender であてています。継ぎ手の納まりが見えるよう、部分のディテールも別に用意しました。\n\n図面を読んで立体に起こす作業は、そのまま「寸法で考える」練習になります。",
		),
		array(
			'title'    => '犬小屋',
			'category' => 'product',
			'order'    => 4,
			'no'       => 'D-04',
			'year'     => '2026',
			'stack'    => 'Rhinoceros / Blender',
			'status'   => '完了',
			'client'   => '',
			'excerpt'  => '実際に建てるための、原寸の検討。',
			'content'  => "板の厚みと継ぎ手を持たせたまま、組み立てられる状態でモデリングしました。\n\n材料の拾い出しと切り出し寸法の確認まで3D上で済ませられるので、木取り図をそのまま起こせます。",
		),
	);
}

/**
 * 制作物と固定ページの初期データを登録する。
 */
function kobo19_install_demo_content() {
	if ( get_option( 'kobo19_demo_installed' ) ) {
		return;
	}

	kobo19_seed_work_categories();

	foreach ( kobo19_demo_works() as $work ) {
		$existing = get_page_by_path( sanitize_title( $work['no'] ), OBJECT, 'work' );

		if ( $existing ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'work',
				'post_status'  => 'publish',
				'post_title'   => $work['title'],
				'post_name'    => sanitize_title( $work['no'] ),
				'post_content' => kobo19_to_blocks( $work['content'] ),
				'post_excerpt' => $work['excerpt'],
				'menu_order'   => $work['order'],
			)
		);

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}

		wp_set_object_terms( $post_id, $work['category'], 'work_category' );

		update_post_meta( $post_id, '_kobo19_drawing_no', $work['no'] );
		update_post_meta( $post_id, '_kobo19_year', $work['year'] );
		update_post_meta( $post_id, '_kobo19_stack', $work['stack'] );
		update_post_meta( $post_id, '_kobo19_status', $work['status'] );

		if ( ! empty( $work['client'] ) ) {
			update_post_meta( $post_id, '_kobo19_client', $work['client'] );
		}
	}

	kobo19_install_demo_pages();

	update_option( 'kobo19_demo_installed', KOBO19_VERSION );
}

/**
 * 「工房について」「相談する」の固定ページを用意する。
 */
function kobo19_install_demo_pages() {
	$pages = array(
		'about' => array(
			'title'   => '工房について',
			'content' => "19工房は、サイトも業務の道具もMacアプリも3Dモデルも、同じ手つきでつくる一人の工房です。\n\n扱う素材は毎回変わります。WordPressのテーマを書く週もあれば、Pythonで検索順位を数える週も、Swiftでメニューバーに常駐させる週も、Rhinocerosで椅子の曲面を追う週もあります。それでもやっていることは変わりません。何が要るのかを聞いて、図面を引いて、寸法を決めて、動くところまで持っていく。\n\n## 仕事の進めかた\n\n**動くものを早く見せます。**　説明より現物のほうが早いので、まず触れる形にしてから相談します。\n\n**あとから触れるように残します。**　引き渡したあと、担当の方が自分で直せることを設計の条件に入れています。手順書もあわせてお渡しします。\n\n**必要のないものは足しません。**　外部サービスもプラグインも、無くて済むなら使いません。増えた分だけ、あとで面倒を見る人の手間が増えるからです。\n\n## できること\n\n- WordPressサイトの設計・テーマ制作・公開後の運用\n- 業務の自動化（集計・通知・記録・スクレイピング）\n- macOSアプリ、メニューバー常駐ツール\n- 3Dモデリングとレンダリング（Rhinoceros・Blender）",
		),
		'contact' => array(
			'title'   => '相談する',
			'content' => "つくりたいものが決まっていなくても構いません。「この作業に毎週2時間かかっている」くらいの話から始めましょう。\n\nお問い合わせの際、次のことが分かると話が早いです。\n\n- 今困っていること、手作業でしのいでいること\n- いつまでに要るか\n- すでにお使いのサービスや道具（WordPress、LINE WORKS、Excelなど）\n\nこのページに問い合わせフォームを置く場合は、お使いのフォームプラグインのブロックをここに挿入してください。",
		),
	);

	foreach ( $pages as $slug => $page ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => kobo19_to_blocks( $page['content'] ),
			)
		);
	}
}
