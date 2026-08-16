<?php
/**
 * 制作物一覧の中身。アーカイブと区分別ページの両方から呼びます。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_is_tax   = is_tax( 'work_category' );
$kobo19_term     = $kobo19_is_tax ? get_queried_object() : null;
$kobo19_title    = $kobo19_term ? $kobo19_term->name : '制作物';
$kobo19_lead     = $kobo19_term && $kobo19_term->description
	? $kobo19_term->description
	: 'ホームページ、プログラム、プロダクツ。19工房でつくったものの一覧です。';
$kobo19_archive  = get_post_type_archive_link( 'work' );
?>

<section class="page-head">
	<div class="wrap">
		<p class="eyebrow">Works<?php echo $kobo19_term ? ' ／ ' . esc_html( $kobo19_term->name ) : ''; ?></p>
		<h1 class="page-head__title"><?php echo esc_html( $kobo19_title ); ?></h1>
		<p class="page-head__lead"><?php echo esc_html( $kobo19_lead ); ?></p>

		<nav class="filters" aria-label="区分で絞り込む">
			<?php if ( $kobo19_archive ) : ?>
				<a href="<?php echo esc_url( $kobo19_archive ); ?>"<?php echo $kobo19_is_tax ? '' : ' aria-current="page"'; ?>>すべて</a>
			<?php endif; ?>

			<?php foreach ( kobo19_ordered_categories() as $kobo19_item ) : ?>
				<?php
				$kobo19_link = get_term_link( $kobo19_item );
				if ( is_wp_error( $kobo19_link ) ) {
					continue;
				}
				$kobo19_current = ( $kobo19_term && $kobo19_term->term_id === $kobo19_item->term_id );
				?>
				<a href="<?php echo esc_url( $kobo19_link ); ?>"<?php echo $kobo19_current ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $kobo19_item->name ); ?>（<?php echo esc_html( (string) $kobo19_item->count ); ?>）
				</a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<section class="section" style="padding-top:0;">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="work-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/work-card', null, array( 'heading' => 'h2' ) );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'class'     => 'pagination',
					'mid_size'  => 2,
					'prev_text' => '前へ',
					'next_text' => '次へ',
				)
			);
			?>
		<?php else : ?>
			<div class="notice">
				<p class="notice__code">該当なし</p>
				<p>この区分にはまだ制作物がありません。</p>
			</div>
		<?php endif; ?>
	</div>
</section>
