/**
 * 19工房 ／ 画面まわりの小さな動き。
 *
 * 1. 狭い画面のメニュー開閉
 * 2. スクロールしてきた要素を現す
 *
 * 動きを減らす設定の環境では 2 を行いません。
 */
( function () {
	'use strict';

	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ------------------------------------------------------------------
	   メニューの開閉
	   ------------------------------------------------------------------ */
	function setupNav() {
		var toggle = document.querySelector( '.nav-toggle' );
		var nav = document.getElementById( 'site-nav' );

		if ( ! toggle || ! nav ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var open = nav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			toggle.textContent = open ? '閉じる' : 'メニュー';
		} );

		// メニュー内のリンクを押したら閉じる
		nav.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( 'a' ) && nav.classList.contains( 'is-open' ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.textContent = 'メニュー';
			}
		} );

		// Escで閉じる
		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.textContent = 'メニュー';
				toggle.focus();
			}
		} );
	}

	/* ------------------------------------------------------------------
	   スクロールで現す
	   ------------------------------------------------------------------ */
	function setupReveal() {
		var targets = document.querySelectorAll( '.reveal' );

		if ( ! targets.length ) {
			return;
		}

		if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
			targets.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry, index ) {
					if ( ! entry.isIntersecting ) {
						return;
					}

					// 同時に入ってきたものは少しずつ遅らせる
					entry.target.style.transitionDelay = Math.min( index * 60, 240 ) + 'ms';
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				} );
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
		);

		targets.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	function init() {
		setupNav();
		setupReveal();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
