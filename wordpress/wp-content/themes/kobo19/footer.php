<?php
/**
 * フッター。「相談する」の呼びかけと、サイト情報。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_contact_page = get_page_by_path( 'contact' );
$kobo19_email        = kobo19_option( 'kobo19_contact_email' );
?>
	</main>

	<?php if ( $kobo19_contact_page || $kobo19_email ) : ?>
	<section class="contact">
		<div class="wrap contact__inner">
			<div>
				<h2 class="contact__title"><?php echo esc_html( kobo19_option( 'kobo19_contact_label', '相談する' ) ); ?></h2>
				<p class="contact__text"><?php echo esc_html( kobo19_option( 'kobo19_contact_text', 'つくりたいものが決まっていなくても構いません。困っている作業の話から始めましょう。' ) ); ?></p>
			</div>
			<div>
				<?php if ( $kobo19_contact_page ) : ?>
					<a class="btn" href="<?php echo esc_url( get_permalink( $kobo19_contact_page ) ); ?>">問い合わせる</a>
				<?php elseif ( $kobo19_email ) : ?>
					<a class="btn" href="mailto:<?php echo esc_attr( $kobo19_email ); ?>">メールを送る</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<footer class="site-footer">
		<div class="wrap">

			<div class="site-footer__inner">
				<div>
					<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="brand__mark">19</span>
						<span class="brand__name"><?php bloginfo( 'name' ); ?></span>
					</a>
					<?php if ( $kobo19_email ) : ?>
						<p style="margin-top:1rem;">
							<a href="mailto:<?php echo esc_attr( $kobo19_email ); ?>"><?php echo esc_html( $kobo19_email ); ?></a>
						</p>
					<?php endif; ?>
				</div>

				<nav aria-label="フッターメニュー">
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
					} else {
						echo '<ul>';
						foreach ( kobo19_ordered_categories() as $term ) {
							$link = get_term_link( $term );
							if ( is_wp_error( $link ) ) {
								continue;
							}
							printf(
								'<li><a href="%s">%s</a></li>',
								esc_url( $link ),
								esc_html( $term->name )
							);
						}
						echo '</ul>';
					}
					?>
				</nav>

				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
					<div><?php dynamic_sidebar( 'footer-1' ); ?></div>
				<?php endif; ?>
			</div>

			<div class="colophon">
				<span>&copy; <?php echo esc_html( kobo19_option( 'kobo19_established', '2026' ) ); ?>–<?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
				<span>制作物 <?php echo esc_html( (string) kobo19_work_count() ); ?> 点</span>
			</div>

		</div>
	</footer>

</div><!-- /.site -->

<?php wp_footer(); ?>
</body>
</html>
