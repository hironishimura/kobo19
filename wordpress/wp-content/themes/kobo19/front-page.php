<?php
/**
 * トップページ。制作物を新しい順に一列で並べます。
 *
 * 並び順は「ページ属性 → 順序」の数字が小さいものから。数字が同じときは
 * 新しいものが先に来ます。新しく追加した制作物は順序が 0 なので、
 * 何も設定しなければ自動で一番上に並びます。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_total       = kobo19_work_count();
$kobo19_archive_url = get_post_type_archive_link( 'work' );
$kobo19_shown       = 12;

$kobo19_query = new WP_Query(
	array(
		'post_type'      => 'work',
		'posts_per_page' => $kobo19_shown,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
	)
);
?>

<section class="hero">
	<div class="wrap">
		<p class="hero__eyebrow"><?php echo esc_html( kobo19_option( 'kobo19_hero_eyebrow', 'WORKSHOP 19' ) ); ?><?php echo $kobo19_total ? ' ／ 制作物 ' . esc_html( (string) $kobo19_total ) . ' 点' : ''; ?></p>

		<h1 class="hero__title"><?php echo esc_html( kobo19_option( 'kobo19_hero_title', "つくるものは違っても、\nやることは同じです。" ) ); ?></h1>

		<p class="hero__lead"><?php echo esc_html( kobo19_option( 'kobo19_hero_lead', 'サイトも、業務の道具も、Macアプリも、3Dモデルも。何が要るのかを聞いて、図面を引いて、寸法を決めて、動くところまで持っていく。19工房はその繰り返しでできています。' ) ); ?></p>

		<div class="hero__actions">
			<?php if ( $kobo19_archive_url ) : ?>
				<a class="btn" href="<?php echo esc_url( $kobo19_archive_url ); ?>">制作物をすべて見る</a>
			<?php endif; ?>

			<?php $kobo19_about = get_page_by_path( 'about' ); ?>
			<?php if ( $kobo19_about ) : ?>
				<a class="btn btn--quiet" href="<?php echo esc_url( get_permalink( $kobo19_about ) ); ?>">工房について</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">

		<p class="eyebrow">Index ／ 新しい順</p>

		<?php if ( $kobo19_query->have_posts() ) : ?>
			<div class="work-grid">
				<?php
				while ( $kobo19_query->have_posts() ) :
					$kobo19_query->the_post();
					get_template_part( 'template-parts/work-card', null, array( 'heading' => 'h2' ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $kobo19_total > $kobo19_shown && $kobo19_archive_url ) : ?>
				<p class="section-more">
					<a class="btn btn--quiet" href="<?php echo esc_url( $kobo19_archive_url ); ?>">
						残りの<?php echo esc_html( (string) ( $kobo19_total - $kobo19_shown ) ); ?>点も見る
					</a>
				</p>
			<?php endif; ?>
		<?php else : ?>
			<div class="notice">
				<p class="notice__code">まだ登録がありません</p>
				<p>管理画面の「制作物」から追加すると、ここに新しい順で並びます。</p>
			</div>
		<?php endif; ?>

	</div>
</section>

<?php
$kobo19_about_page = get_page_by_path( 'about' );

if ( $kobo19_about_page ) :
	?>
<section class="section">
	<div class="wrap">
		<p class="eyebrow">About</p>
		<h2 class="section-title">仕事の進めかた</h2>

		<div class="notes">
			<div class="note reveal">
				<h3 class="note__title">動くものを早く見せます</h3>
				<p class="note__text">説明より現物のほうが早いので、まず触れる形にしてから相談します。</p>
			</div>
			<div class="note reveal">
				<h3 class="note__title">あとから触れるように残します</h3>
				<p class="note__text">引き渡したあと、担当の方が自分で直せることを設計の条件に入れています。</p>
			</div>
			<div class="note reveal">
				<h3 class="note__title">必要のないものは足しません</h3>
				<p class="note__text">外部サービスもプラグインも、無くて済むなら使いません。面倒を見る人の手間が増えるからです。</p>
			</div>
		</div>

		<p class="section-more">
			<a class="btn btn--quiet" href="<?php echo esc_url( get_permalink( $kobo19_about_page ) ); ?>">工房についてもっと読む</a>
		</p>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
