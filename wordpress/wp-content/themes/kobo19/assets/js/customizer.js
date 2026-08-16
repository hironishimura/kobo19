/**
 * カスタマイザーのプレビューを、入力に合わせてその場で書き換えます。
 */
( function ( $ ) {
	'use strict';

	wp.customize( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.brand__name' ).text( to );
		} );
	} );

	wp.customize( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '.brand__tagline' ).text( to );
		} );
	} );
} )( jQuery );
