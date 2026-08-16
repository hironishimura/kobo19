<?php
/**
 * トップページ。見出し・数量表・区分ごとの制作物一覧。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_categories  = kobo19_ordered_categories();
$kobo19_total       = kobo19_work_count();
$kobo19_archive_url = get_post_type_archive_link( 'work' );
$kobo19_signs       = array(
	'homepage' => 'W',
	'program'  => 'P',
	'product'  => 'D',
);
?>

<section class="hero">
	<div class="wrap">

		<div class="hero__grid">
			<div>
				<p class="hero__eyebrow"><?php echo esc_html( kobo19_option( 'kobo19_hero_eyebrow', 'WORKSHOP 19 ／ 制作物一覧' ) ); ?></p>

				<h1 class="hero__title"><?php echo esc_html( kobo19_option( 'kobo19_hero_title', "つくるものは違っても、\nやることは同じです。" ) ); ?></h1>

				<p class="hero__lead"><?php echo esc_html( kobo19_option( 'kobo19_hero_lead', 'サイトも、業務の道具も、Macアプリも、椅子の3Dモデルも。図面を引いて、寸法を決めて、かたちにする。19工房はその繰り返しでできています。' ) ); ?></p>

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

			<?php if ( get_theme_mod( 'kobo19_show_hero_figure', true ) ) : ?>
				<?php get_template_part( 'template-parts/hero-figure' ); ?>
			<?php endif; ?>
		</div>

		<?php if ( $kobo19_categories ) : ?>
			<div class="tally">
				<div class="tally__cell">
					<span class="tally__label">制作物</span>
					<span class="tally__value"><?php echo esc_html( (string) $kobo19_total ); ?> 点</span>
				</div>

				<?php foreach ( $kobo19_categories as $kobo19_term ) : ?>
					<?php
					$kobo19_link = get_term_link( $kobo19_term );
					if ( is_wp_error( $kobo19_link ) ) {
						continue;
					}
					?>
					<a class="tally__cell" href="<?php echo esc_url( $kobo19_link ); ?>">
						<span class="tally__label"><?php echo esc_html( isset( $kobo19_signs[ $kobo19_term->slug ] ) ? $kobo19_signs[ $kobo19_term->slug ] : '—' ); ?></span>
						<span class="tally__value">
							<?php echo esc_html( $kobo19_term->name ); ?>
							<span class="tally__count"><?php echo esc_html( (string) $kobo19_term->count ); ?></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>

<section class="section">
	<div class="wrap">

		<p class="eyebrow">Index</p>
		<h2 class="section-title">つくったもの</h2>
		<p class="section-lead">区分ごとに並べています。図番の頭文字は区分の符号です。図版のないものは、区分ごとに角度の違うハッチングで埋めてあります。</p>

		<?php foreach ( $kobo19_categories as $kobo19_term ) : ?>
			<?php
			$kobo19_query = new WP_Query(
				array(
					'post_type'      => 'work',
					'posts_per_page' => 6,
					'orderby'        => array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
					),
					'tax_query'      => array(
						array(
							'taxonomy' => 'work_category',
							'field'    => 'term_id',
							'terms'    => $kobo19_term->term_id,
						),
					),
				)
			);

			if ( ! $kobo19_query->have_posts() ) {
				wp_reset_postdata();
				continue;
			}

			$kobo19_term_link = get_term_link( $kobo19_term );
			?>

			<div class="category-block">
				<div class="category-block__head">
					<div class="category-block__heading">
						<span class="category-block__sign"><?php echo esc_html( isset( $kobo19_signs[ $kobo19_term->slug ] ) ? $kobo19_signs[ $kobo19_term->slug ] : '—' ); ?></span>
						<h3 class="category-block__title"><?php echo esc_html( $kobo19_term->name ); ?></h3>
					</div>

					<?php if ( $kobo19_term->description ) : ?>
						<p class="category-block__desc"><?php echo esc_html( $kobo19_term->description ); ?></p>
					<?php endif; ?>
				</div>

				<div class="work-grid">
					<?php
					while ( $kobo19_query->have_posts() ) :
						$kobo19_query->the_post();
						get_template_part( 'template-parts/work-card', null, array( 'heading' => 'h4' ) );
					endwhile;
					?>
				</div>

				<?php if ( $kobo19_query->found_posts > 6 && ! is_wp_error( $kobo19_term_link ) ) : ?>
					<p style="margin-top:1.8rem;">
						<a class="btn btn--quiet" href="<?php echo esc_url( $kobo19_term_link ); ?>"><?php echo esc_html( $kobo19_term->name ); ?>をすべて見る（<?php echo esc_html( (string) $kobo19_query->found_posts ); ?>点）</a>
					</p>
				<?php endif; ?>
			</div>

			<?php wp_reset_postdata(); ?>
		<?php endforeach; ?>

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

		<div class="work-grid" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
			<div class="work-card reveal" style="padding:1.6rem;">
				<h3 class="work-card__title">動くものを早く見せます</h3>
				<p class="work-card__summary">説明より現物のほうが早いので、まず触れる形にしてから相談します。</p>
			</div>
			<div class="work-card reveal" style="padding:1.6rem;">
				<h3 class="work-card__title">あとから触れるように残します</h3>
				<p class="work-card__summary">引き渡したあと、担当の方が自分で直せることを設計の条件に入れています。</p>
			</div>
			<div class="work-card reveal" style="padding:1.6rem;">
				<h3 class="work-card__title">必要のないものは足しません</h3>
				<p class="work-card__summary">外部サービスもプラグインも、無くて済むなら使いません。面倒を見る人の手間が増えるからです。</p>
			</div>
		</div>

		<p style="margin-top:2rem;">
			<a class="btn btn--quiet" href="<?php echo esc_url( get_permalink( $kobo19_about_page ) ); ?>">工房についてもっと読む</a>
		</p>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
