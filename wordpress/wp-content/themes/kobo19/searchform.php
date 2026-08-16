<?php
/**
 * 検索フォーム。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="kobo19-search">サイト内を検索</label>
	<input type="search" id="kobo19-search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="制作物や記事を探す">
	<button type="submit">検索</button>
</form>
