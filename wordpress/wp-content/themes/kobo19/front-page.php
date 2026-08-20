<?php
/**
 * トップページ。SujiCalc の紹介。
 *
 * 文言は 外観 → カスタマイズ → 「アプリの情報」から変えられます。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_name    = kobo19_option( 'kobo19_app_name', 'SujiCalc' );
$kobo19_store   = kobo19_option( 'kobo19_appstore_url' );
$kobo19_manual  = get_post_type_archive_link( 'manual' );
$kobo19_support = kobo19_page_link( 'support' );
$kobo19_privacy = kobo19_page_link( 'privacy' );
?>

<section class="hero">
	<div class="wrap">
		<div class="hero__grid">
			<div class="hero__text">
				<p class="hero__eyebrow">iPhone ／ iPad ／ Mac</p>

				<h1 class="hero__title"><?php echo esc_html( $kobo19_name ); ?></h1>

				<p class="hero__tagline"><?php echo esc_html( kobo19_option( 'kobo19_app_tagline', '打つと右に答えが出る、ノート型の電卓。' ) ); ?></p>

				<p class="hero__lead"><?php echo nl2br( esc_html( kobo19_option( 'kobo19_app_lead', "式を打つと、その行の右側にすぐ答えが出ます。「＝」は要りません。\n書いた式はそのまま残るので、あとから数字を直せば、続きの計算も一度に合い直ります。" ) ) ); ?></p>

				<div class="hero__actions">
					<?php if ( $kobo19_store ) : ?>
						<a class="btn" href="<?php echo esc_url( $kobo19_store ); ?>">App Store で見る</a>
					<?php endif; ?>

					<?php if ( $kobo19_manual ) : ?>
						<a class="btn<?php echo $kobo19_store ? ' btn--quiet' : ''; ?>" href="<?php echo esc_url( $kobo19_manual ); ?>">使い方を読む</a>
					<?php endif; ?>
				</div>

				<?php
				$kobo19_facts = array_filter(
					array(
						'バージョン' => kobo19_option( 'kobo19_app_version', '1.0' ),
						'対応'       => kobo19_option( 'kobo19_app_requires', 'iOS 17 / iPadOS 17 / macOS 14 以降' ),
						'カテゴリ'   => kobo19_option( 'kobo19_app_size', '仕事効率化' ),
						'価格'       => kobo19_option( 'kobo19_app_price' ),
					)
				);
				?>
				<?php if ( $kobo19_facts ) : ?>
					<dl class="facts">
						<?php foreach ( $kobo19_facts as $kobo19_key => $kobo19_value ) : ?>
							<div class="facts__row">
								<dt><?php echo esc_html( $kobo19_key ); ?></dt>
								<dd><?php echo esc_html( $kobo19_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<div class="hero__demo">
				<?php
				// 画面と同じ「左に式・右に答え」の形。中身は説明書の例と同じ書き方です。
				echo do_shortcode(
					'[calc label="打つと、右に出る"]
// 8月分の見積もり |
単価 = 1200円 | 1,200円
個数 = 35 | 35
単価 * 個数 | 42,000円
+ 10% | 46,200円
 |
梁幅 = 120 mm | 120 mm
梁せい = 300 mm | 300 mm
梁幅 * 梁せい | 36,000 mm²
[/calc]'
				);
				?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<p class="eyebrow">できること</p>

		<div class="features">

			<div class="feature reveal">
				<h2 class="feature__title">単位がそのまま通る</h2>
				<p class="feature__text">数字のうしろに単位を書くだけです。掛け算・割り算をすると <code>mm²</code> や <code>N/mm²</code> のように組み立てられ、種類の違う単位を足そうとするとエラーで止まります。</p>
				<?php
				echo do_shortcode(
					'[calc numbers="no"]
120 mm * 300 mm | 36,000 mm²
36 kN / 36000 mm2 | 1 N/mm²
10 km in mile | 6.2137119224 mile
[/calc]'
				);
				?>
			</div>

			<div class="feature reveal">
				<h2 class="feature__title">名前を付けて使い回す</h2>
				<p class="feature__text">値に名前を付けると、ほかの行から呼び出せます。名前を変えれば、その名前を使っている行もまとめて書き換わります。前の行は <code>prev</code>、3行目は <code>#3</code>、空行までの合計は <code>sum</code> です。</p>
				<?php
				echo do_shortcode(
					'[calc numbers="no"]
梁幅 = 120 mm | 120 mm
梁せい = 300 mm | 300 mm
梁幅 * 梁せい | 36,000 mm²
[/calc]'
				);
				?>
			</div>

			<div class="feature reveal">
				<h2 class="feature__title">表にしてまとめて集計</h2>
				<p class="feature__text">値を並べて持てます。<code>sum</code> <code>avg</code> <code>min</code> <code>max</code> <code>sort</code> で扱え、文字も混ぜられるので、計算書の見出しを付けられます。</p>
				<?php
				echo do_shortcode(
					'[calc numbers="no"]
価格 = [4800, 12000, 3200] | [4,800, 12,000, 3,200]
sum(価格) | 20,000
avg(価格) | 6,666.6666666667
[/calc]'
				);
				?>
			</div>

			<div class="feature reveal">
				<h2 class="feature__title">グラフになる</h2>
				<p class="feature__text"><code>plot</code> と書けば曲線が、<code>chart</code> と書けば棒グラフが本文の下に出ます。打ち直すたびにその場で描き直されます。</p>
				<?php
				echo do_shortcode(
					'[calc numbers="no"]
plot(x^2 - 3x) |
chart(家賃, 光熱費, 食費) |
[/calc]'
				);
				?>
			</div>

			<div class="feature reveal">
				<h2 class="feature__title">かんたんなプログラム</h2>
				<p class="feature__text"><code>if</code> <code>for</code> <code>while</code> <code>def</code> が Python と同じ書き方で使えます。繰り返しの計算や、自分用の関数を作れます。作った関数はほかのシートからも呼べます。</p>
				<?php
				echo do_shortcode(
					'[calc numbers="no"]
def 税込(x): |
    return x * 1.1 |
税込(4800) | 5,280
[/calc]'
				);
				?>
			</div>

			<div class="feature reveal">
				<h2 class="feature__title">3つの端末で同じ書類</h2>
				<p class="feature__text">iPhone・iPad・Mac の書類は iCloud で自動的に同期します。現場の iPhone に打ち込んだ数字を、事務所の Mac でそのまま続けられます。別々のシートを編集していれば、どちらも残ります。</p>
			</div>

		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="privacy-note">
			<div>
				<p class="eyebrow">プライバシー</p>
				<h2 class="section-title">何も集めません</h2>
				<p class="section-lead">広告も、利用状況の解析も入っていません。そもそも、外部と通信する機能がアプリに含まれていません。書いた内容は、お使いの端末とご自身の iCloud にだけ保存されます。</p>
			</div>

			<?php if ( $kobo19_privacy ) : ?>
				<p class="section-more">
					<a class="btn btn--quiet" href="<?php echo esc_url( $kobo19_privacy['url'] ); ?>">プライバシーポリシー</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php $kobo19_chapters = kobo19_manual_chapters(); ?>
<?php if ( $kobo19_chapters ) : ?>
<section class="section">
	<div class="wrap">
		<p class="eyebrow">説明書</p>
		<h2 class="section-title">使い方</h2>
		<p class="section-lead">式の書き方から、単位・グラフ・プログラム・同期まで、例つきでまとめています。</p>

		<ol class="chapter-list">
			<?php foreach ( $kobo19_chapters as $kobo19_index => $kobo19_chapter ) : ?>
				<li class="chapter-list__item">
					<a href="<?php echo esc_url( get_permalink( $kobo19_chapter ) ); ?>">
						<span class="chapter-list__no"><?php echo esc_html( sprintf( '%02d', $kobo19_index + 1 ) ); ?></span>
						<span class="chapter-list__body">
							<span class="chapter-list__title"><?php echo esc_html( get_the_title( $kobo19_chapter ) ); ?></span>
							<?php if ( $kobo19_chapter->post_excerpt ) : ?>
								<span class="chapter-list__text"><?php echo esc_html( $kobo19_chapter->post_excerpt ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
