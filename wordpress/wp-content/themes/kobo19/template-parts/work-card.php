<?php
/**
 * 制作物カード。図面の表題欄をかたどっています。
 *
 * サムネイル画像が無い制作物は、区分ごとに角度の違うハッチングで埋めます。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_slug   = kobo19_work_category_slug();
$kobo19_stack  = kobo19_stack_list();
$kobo19_year   = kobo19_meta( 'year' );
$kobo19_status = kobo19_meta( 'status' );

// 呼び出し側から見出しレベルを受け取る（既定は h3）。
$kobo19_heading = isset( $args['heading'] ) ? $args['heading'] : 'h3';
$kobo19_heading = in_array( $kobo19_heading, array( 'h2', 'h3', 'h4' ), true ) ? $kobo19_heading : 'h3';
?>
<article class="work-card reveal">

	<div class="work-card__plate">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'kobo19-card', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
		<?php else : ?>
			<div class="work-card__hatch hatch--<?php echo esc_attr( $kobo19_slug ); ?>" aria-hidden="true">
				<span><?php echo esc_html( $kobo19_status ? $kobo19_status : kobo19_work_category_name() ); ?></span>
			</div>
		<?php endif; ?>
	</div>

	<div class="work-card__body">
		<<?php echo esc_html( $kobo19_heading ); ?> class="work-card__title"><?php the_title(); ?></<?php echo esc_html( $kobo19_heading ); ?>>

		<?php $kobo19_summary = kobo19_summary( 64 ); ?>
		<?php if ( $kobo19_summary ) : ?>
			<p class="work-card__summary"><?php echo esc_html( $kobo19_summary ); ?></p>
		<?php endif; ?>

		<?php if ( $kobo19_stack ) : ?>
			<ul class="work-card__stack">
				<?php foreach ( array_slice( $kobo19_stack, 0, 3 ) as $kobo19_item ) : ?>
					<li><?php echo esc_html( $kobo19_item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<div class="title-block">
		<div class="title-block__cell title-block__cell--no">
			<span class="title-block__key">図番</span>
			<span class="title-block__value"><?php echo esc_html( kobo19_drawing_no() ); ?></span>
		</div>
		<div class="title-block__cell">
			<span class="title-block__key">区分</span>
			<span class="title-block__value"><?php echo esc_html( kobo19_work_category_name() ); ?></span>
		</div>
		<div class="title-block__cell title-block__cell--year">
			<span class="title-block__key">年</span>
			<span class="title-block__value"><?php echo esc_html( $kobo19_year ? $kobo19_year : get_the_date( 'Y' ) ); ?></span>
		</div>
	</div>

	<a class="work-card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?>を見る</a>
</article>
