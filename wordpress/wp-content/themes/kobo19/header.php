<?php
/**
 * ヘッダー。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">本文へ移動</a>

<div class="site">

	<header class="site-header">
		<div class="wrap site-header__inner">

			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<span class="brand__mark">19</span>
					<span class="brand__name"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
				<?php $tagline = get_bloginfo( 'description', 'display' ); ?>
				<?php if ( $tagline ) : ?>
					<span class="brand__tagline"><?php echo esc_html( $tagline ); ?></span>
				<?php endif; ?>
			</a>

			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
				メニュー
			</button>

			<nav class="nav" id="site-nav" aria-label="メインメニュー">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
				} else {
					// メニューが未設定のときの初期表示。
					$fallback = array();

					$manual = get_post_type_archive_link( 'manual' );
					if ( $manual ) {
						$fallback[] = array( 'url' => $manual, 'label' => '使い方' );
					}

					foreach ( array( 'support' => 'サポート', 'privacy' => 'プライバシー' ) as $slug => $label ) {
						$page = kobo19_page_link( $slug );
						if ( $page ) {
							$fallback[] = array( 'url' => $page['url'], 'label' => $label );
						}
					}

					$store = kobo19_option( 'kobo19_appstore_url' );
					if ( $store ) {
						$fallback[] = array( 'url' => $store, 'label' => 'App Store' );
					}

					echo '<ul>';
					foreach ( $fallback as $item ) {
						printf(
							'<li><a href="%s">%s</a></li>',
							esc_url( $item['url'] ),
							esc_html( $item['label'] )
						);
					}
					echo '</ul>';
				}
				?>
			</nav>

		</div>
	</header>

	<main id="main">
