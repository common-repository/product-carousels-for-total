<?php
/*
 * Plugin Name: Product Carousels for Total
 * Description: Transforms the WooCommerce related and upsell columns into carousels in the Total WordPress theme.
 * Author: WPExplorer
 * Author URI: https://www.wpexplorer.com/
 * License: GPLv2
 * Version: 1.1
 * Text Domain: product-carousels-for-total
 * Domain Path: /languages
 * WC tested up to: 7.4.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Product_Carousels_For_Total' ) ) {

	class Product_Carousels_For_Total {

		/**
		 * Our single Product_Carousels_For_Total instance.
		 */
		private static $instance;

		/**
		 * Disable instantiation.
		 */
		private function __construct() {
			// Private to disabled instantiation.
		}

		/**
		 * Disable the cloning of this class.
		 *
		 * @return void
		 */
		final public function __clone() {
			throw new Exception( 'You\'re doing things wrong.' );
		}

		/**
		 * Disable the wakeup of this class.
		 *
		 * @return void
		 */
		final public function __wakeup() {
			throw new Exception( 'You\'re doing things wrong.' );
		}

		/**
		 * Create or retrieve the instance of Product_Carousels_For_Total.
		 *
		 * @return Product_Carousels_For_Total
		 */
		public static function instance() {
			if ( is_null( static::$instance ) ) {
				static::$instance = new Product_Carousels_For_Total;
				static::$instance->init_hooks();
			}

			return static::$instance;
		}

		/**
		 * Initialization hooks.
		 *
		 * @since 1.0
		 */
		public function init_hooks() {
			add_action( 'wp_footer', [ $this, 'add_carousels' ] );
		}

		/**
		 * Add Carousels.
		 *
		 * @since 1.0
		 */
		public function add_carousels() {
			if ( ! is_singular( 'product' ) ) {
				return;
			}

			$dir_url = trailingslashit( plugin_dir_url( __FILE__ ) );

			if ( wp_script_is( 'wpex-owl-carousel', 'registered' ) ) {
				wp_enqueue_script( 'wpex-owl-carousel' );
			} else {
				wp_enqueue_script(
					'wpex-owl-carousel',
					$dir_url . 'assets/wpex-owl-carousel.min.js',
					[ 'jquery' ],
					'1.0',
					true
				);
			}

			if ( wp_style_is( 'wpex-owl-carousel', 'registered' ) ) {
				wp_enqueue_style( 'wpex-owl-carousel' );
			} else {
				wp_enqueue_style(
					'wpex-owl-carousel',
					$dir_url . 'assets/wpex-owl-carousel.min.css',
					[],
					'1.0',
				);
			}

			wp_enqueue_script(
				'total-woocommerce-carousels',
				$dir_url . 'assets/product-carousels-for-total.js',
				[ 'jquery', 'wpex-owl-carousel' ],
				'1.0',
				true
			);

			$settings = [
				'upsellsItems' => $this->get_items( 'upsells' ),
				'relatedItems' => $this->get_items( 'related' ),
				'navClass' => [
					'owl-nav__btn owl-prev',
					'owl-nav__btn owl-next',
				],
				'i18n' => [
					'next' => esc_html__( 'next slide', 'product-carousels-for-total' ),
					'prev' => esc_html__( 'previous slide', 'product-carousels-for-total' ),
				],
				'prevIcon'
			];

			if ( is_rtl() ) {
				$settings['rtl'] = true;
			}

			$settings = apply_filters( 'total_woocommerce_carousel_settings', $settings );

			wp_localize_script(
				'total-woocommerce-carousels',
				'totalWooCarouselSettings',
				$settings
			);

			wp_enqueue_script( 'imagesloaded' );
		}

		/**
		 * Returns upsell columns.
		 *
		 * @since 1.1
		 */
		protected function get_items( $instance = '' ) {
			if ( ! function_exists( 'wpex_get_array_first_value' ) ) {
				return;
			}

			$cols = get_theme_mod( "woocommerce_{$instance}_columns", 4 );

			if ( is_scalar( $cols ) ) {
				$desktop_cols = $cols;
			} elseif ( is_array( $cols ) ) {
				$cols = array_map( 'absint', $cols );
				$desktop_cols = $cols['d'] ?? 4;
			}

			return [
				'items' => $desktop_cols,
				'responsive' => [
					'0' => [
						'items' => $cols['pp'] ?? $cols['pl'] ?? $cols['tp'] ??  $cols['tl'] ?? 1,
					],
					'480' => [
						'items' => $cols['pl'] ?? $cols['tp'] ??  $cols['tl'] ?? 2,
					],
					'768' => [
						'items' => $cols['tp'] ??  $cols['tl'] ?? $desktop_cols ?? 2,
					],
					'960' => [
						'items' => $cols['tl'] ?? $desktop_cols ?? 3,
					],
					'1025' => [
						'items' => $desktop_cols,
					],
				]
			];
		}

	}

	Product_Carousels_For_Total::instance();

}