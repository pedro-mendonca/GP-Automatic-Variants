<?php
/**
 * Class file for setting the Automatic Variants.
 *
 * @package GP_Automatic_Variants
 *
 * @since 1.0.0
 */

namespace GP_Automatic_Variants;

use GP;
use GP_Locales;
use GP_Translation;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( __NAMESPACE__ . '\Automatic_Variants' ) ) {

	/**
	 * Class Automatic_Variants.
	 */
	class Automatic_Variants {


		/**
		 * Registers actions.
		 *
		 * @return void
		 */
		public static function init() {

			/**
			 * Check GlotPress minimum requirements.
			 */
			if ( ! self::check_required_glotpress() ) {
				return;
			}

			/**
			 * Check GlotPress automatically converted variants.
			 */
			add_action( 'admin_init', array( self::class, 'check_automatic_variants' ) );

			/**
			 * Converts a root translation into the matching variant translation set after creating a translation.
			 */
			add_action( 'gp_translation_created', array( self::class, 'queue_translation_for_conversion' ) );

			/**
			 * Converts a root translation into the matching variant translation set after saving an existing translation.
			 */
			add_action( 'gp_translation_saved', array( self::class, 'queue_translation_for_conversion' ) );

		}


		/**
		 * Check GlotPress minimum requirements.
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		public static function check_required_glotpress() {

			// Check if GlotPress is installed and activated.
			if ( ! class_exists( 'GP' ) || ! defined( 'GP_VERSION' ) ) {

				$message = esc_html__( 'GlotPress not found. Please install and activate it.', 'gp-automatic-variants' );

				add_action(
					'admin_notices',
					function() use ( $message ) {
						self::admin_notice( $message, 'error' );
					}
				);

				return false;
			}

			// Check for GlotPress required version.
			if ( version_compare( GP_AUTOMATIC_VARIANTS_REQUIRED_GLOTPRESS, GP_VERSION, '>' ) ) {

				$message = sprintf(
					/* translators: 1: Version number. 2: Version number. */
					esc_html__( 'Current GlotPress version (%1$s) does not meet minimum requirements. This plugin requires GlotPress %2$s.', 'gp-automatic-variants' ),
					GP_VERSION,
					GP_AUTOMATIC_VARIANTS_REQUIRED_GLOTPRESS
				);

				add_action(
					'admin_notices',
					function() use ( $message ) {
						self::admin_notice( $message, 'error' );
					}
				);

				return false;
			}

			// Check for GlotPress required version.
			if ( version_compare( GP_AUTOMATIC_VARIANTS_TESTED_GLOTPRESS, GP_VERSION, '<' ) ) {

				$message = wp_kses_post(
					sprintf(
						/* translators: 1: Version number. 2: Version number. */
						__( 'This plugin <strong>has not been tested</strong> with your current version of GlotPress (%1$s). Tested up to GlotPress %2$s.', 'gp-automatic-variants' ),
						GP_VERSION,
						GP_AUTOMATIC_VARIANTS_TESTED_GLOTPRESS
					)
				);

				add_action(
					'admin_notices',
					function() use ( $message ) {
						self::admin_notice( $message, 'warning' );
					}
				);
			}

			return true;
		}


		/**
		 * Check GlotPress automatically converted variants.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function check_automatic_variants() {

			// Check automatically converted variants and output error admin notices.
			self::get_automatic_variants();

		}


		/**
		 * Render admin notice.
		 *
		 * @since 1.0.0
		 *
		 * @param string $message   Admin notice message to output.
		 * @param string $type      Type of admin notice (e.g.: 'error', 'info', 'success', 'warning' ). Defaults to 'info'.
		 *
		 * @return void
		 */
		public static function admin_notice( $message, $type = 'info' ) {

			?>
			<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible">
				<p>
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: 1: Plugin name. 2: Error message. */
							esc_html__( '%1$s: %2$s', 'gp-automatic-variants' ),
							'<b>' . esc_html_x( 'Automatic Variants for GlotPress', 'Plugin name', 'gp-automatic-variants' ) . '</b>',
							$message
						)
					);
					?>
				</p>
			</div>
			<?php

		}


		/**
		 * Set the automatically converted variants.
		 *
		 * @since 1.0.0
		 *
		 * @return array<string, array<int, string>>   Array of WP_Locales of the variants to set as automatically converted.
		 */
		public static function get_automatic_variants() {

			// Array of root locales with array of variants and callbacks.
			$locales = array();

			$automatic_variants = array();

			/**
			 * Filter the Locale variants to set as automatic converted and read-only.
			 * Array of variant locales slugs.
			 *
			 * Example:
			 *     array(
			 *         'ca-valencia',
			 *         'de-at',
			 *         'de-ch',
			 *         'en-gb',
			 *         'pt-ao90',
			 *     );
			 *
			 * @since 1.0.0
			 */
			$automatic_variants = apply_filters( 'gp_automatic_variants_list', $automatic_variants );

			// Remove duplicates.
			$automatic_variants = array_unique( $automatic_variants );

			// Sort alphabetically.
			sort( $automatic_variants );

			$locale_errors  = array();
			$variant_errors = array();

			// Check if Locales exist and are variants.
			foreach ( $automatic_variants as $locale_slug ) {

				// Get the locale by the given slug.
				$locale = GP_Locales::by_slug( $locale_slug ); // @phpstan-ignore-line

				// Check if locale exist.
				if ( ! $locale ) {
					$locale_errors[] = '<code>' . esc_html( $locale_slug ) . '</code>';
					continue;
				}

				// Check if locale is a variant.
				if ( null === $locale->variant_root ) {
					$variant_errors[] = '<code>' . esc_html( $locale_slug ) . '</code>';
					continue;
				}

				// Set the locale.
				$locales[ strval( $locale->variant_root ) ][] = strval( $locale_slug );

			}

			// Output Locale errors.
			if ( ! empty( $locale_errors ) ) {

				sort( $locale_errors );

				$message = wp_kses_post(
					wp_sprintf(
						/* translators: 1: Coma separated list of Locales. 2. Version number. */
						_n(
							'The Locale %1$l is not supported by GlotPress %2$s.',
							'The Locales %1$l are not supported by GlotPress %2$s.',
							count( $locale_errors ),
							'gp-automatic-variants'
						),
						$locale_errors,
						GP_VERSION // @phpstan-ignore-line
					)
				);

				add_action(
					'admin_notices',
					function() use ( $message ) {
						self::admin_notice( $message, 'error' );
					}
				);

			}

			// Output Variant errors.
			if ( ! empty( $variant_errors ) ) {

				sort( $variant_errors );

				$message = wp_kses_post(
					wp_sprintf(
						/* translators: %l: Coma separated list of Locales. */
						_n(
							'The Locale %l is not a variant.',
							'The Locales %l are not variants.',
							count( $variant_errors ),
							'gp-automatic-variants'
						),
						$variant_errors
					)
				);

				add_action(
					'admin_notices',
					function() use ( $message ) {
						self::admin_notice( $message, 'error' );
					}
				);

			}

			// Output Variants.
			if ( ! empty( $locales ) ) {

				$variants = array();

				foreach ( $locales as $locale ) {
					foreach ( $locale as $variant ) {
						$variants[] = '<code>' . $variant . '</code>';
					}
				}

				$message = wp_kses_post(
					wp_sprintf(
						/* translators: %l: Coma separated list of Locales. */
						_n(
							'The Locale %l is a read-only automatically converted variant.',
							'The Locales %l are read-only automatically converted variants.',
							count( $variants ),
							'gp-automatic-variants'
						),
						$variants
					)
				);

				add_action(
					'admin_notices',
					function() use ( $message ) {
						self::admin_notice( $message, 'success' );
					}
				);

			}

			return $locales;
		}


		/**
		 * Converts the translation string to the variant translation set.
		 *
		 * @since 1.0.0
		 *
		 * @param object $translation   \GP_Translation Created/updated translation.
		 *
		 * @return void
		 */
		public static function queue_translation_for_conversion( $translation ) {

			// Get automatically converted variants.
			$automatic_variants = self::get_automatic_variants();

			// Check for automatic variants.
			if ( empty( $automatic_variants ) ) {
				return;
			}

			// Only process on root Locales translation sets of the automatic variants.
			$root_set = GP::$translation_set->get( $translation->translation_set_id ); // @phpstan-ignore-line
			if ( ! $root_set || ! array_key_exists( $root_set->locale, $automatic_variants ) || 'default' !== $root_set->slug ) {
				return;
			}

			// Get translation original.
			$original = GP::$original->get( $translation->original_id ); // @phpstan-ignore-line
			if ( ! $original ) {
				return;
			}

			// Check multiple variants per each root.
			foreach ( $automatic_variants[ $root_set->locale ] as $variant ) {

				// Only process if the variant translation set exist.
				$variant_set = GP::$translation_set->by_project_id_slug_and_locale( $original->project_id, 'default', $variant ); // @phpstan-ignore-line
				if ( ! $variant_set ) {
					continue;
				}

				$project = GP::$project->get( $variant_set->project_id ); // @phpstan-ignore-line

				// Process if root translation is set to current without warnings.
				if ( 'current' === $translation->status && empty( $translation->warnings ) ) { // @phpstan-ignore-line
					// Create translation on the variant set.
					self::create( $translation, $project, $variant_set );
				} else {
					// Delete translation on the variant set.
					self::delete( $translation, $project, $variant_set, true );
				}
			}

		}


		/**
		 * Create translation on the variant set, if the conversion changes the root translation.
		 * Also deletes any previous variant set translation if the new translation remains unchanged with the conversion.
		 *
		 * @since 1.0.0
		 *
		 * @param object $translation   \GP_Translation  Created/updated translation.
		 * @param object $project       \GP_Project  GlotPress project.
		 * @param object $variant_set   \GP_Translation_Set  GlotPress translation set of the variant.
		 *
		 * @return void
		 */
		public static function create( $translation, $project, $variant_set ) {

			$translation_changed = self::convert_translation( $translation, $variant_set );

			// Check if the conversion produces changes.
			if ( ! $translation_changed ) {

				// Deletes any existent mathing conversions.
				self::delete( $translation, $project, $variant_set, false );

				return;

			}

			// Add converted translation to the variant translation set and set as current.
			$variant_translation = GP::$translation->create( $translation_changed ); // @phpstan-ignore-line
			if ( ! $variant_translation ) {
				return;
			}

			gp_clean_translation_set_cache( $variant_set->id ); // @phpstan-ignore-line

		}


		/**
		 * Delete the variant translation if the matching root translation has no conversion.
		 * Keeping no history for a read-only variant makes it lighter.
		 *
		 * @since 1.0.0
		 *
		 * @param object $translation   \GP_Translation  Created/updated translation.
		 * @param object $project       \GP_Project  GlotPress project.
		 * @param object $variant_set   \GP_Translation_Set  GlotPress variant translation set.
		 * @param bool   $all           Delete all translations or just the last for performance. Defaults to false.
		 *
		 * @return void
		 */
		public static function delete( $translation, $project, $variant_set, $all = false ) {

			// Get existing translations on the variant translation set for the original_id.
			$variant_translations = GP::$translation->for_translation( // @phpstan-ignore-line
				$project,
				$variant_set,
				'no-limit',
				array(
					'original_id' => $translation->original_id, // @phpstan-ignore-line
					'status'      => $all ? 'either' : 'current',
				)
			);

			// Set the status of the variant translation set as the root translation set for the same original_id.
			foreach ( $variant_translations as $variant_translation ) {
				$variant_translation = GP::$translation->get( $variant_translation ); // @phpstan-ignore-line
				if ( ! $variant_translation ) {
					continue;
				}
				$variant_translation->delete();
			}

			gp_clean_translation_set_cache( $variant_set->id ); // @phpstan-ignore-line

		}


		/**
		 * Convert the translation for the variant, including all plurals.
		 *
		 * @since 1.0.0
		 *
		 * @param object $translation   \GP_Translation  GlotPress translation.
		 * @param object $variant_set   \GP_Translation_Set  GlotPress variant set.
		 *
		 * @return object|false   Returns a converted translation, or false if the result remains unchanged.
		 */
		public static function convert_translation( $translation, $variant_set ) {

			$locale = GP_Locales::by_slug( $variant_set->locale ); // @phpstan-ignore-line

			$translation_converted                     = new GP_Translation( $translation->fields() ); // @phpstan-ignore-line
			$translation_converted->translation_set_id = $variant_set->id; // @phpstan-ignore-line
			$translation_converted->status             = 'current'; // @phpstan-ignore-line

			$translation_changed = false;

			for ( $i = 0; $i < $locale->nplurals; $i++ ) {

				// Skip if plural don't exist.
				if ( null === $translation->{"translation_{$i}"} ) {
					continue;
				}

				/**
				 * Filter to apply the actual conversion to the string.
				 * Example for the variant 'pt-ao90':
				 *   add_filter( 'gp_automatic_variants_convert_pt-ao90', 'conversion_callback' );
				 *   function conversion_callback( $translation ) {
				 *      // Actual convertion of the string.
				 *      return $converted_translation;
				 *   }
				 *
				 * @since 1.0.0
				 */
				$converted = apply_filters( "gp_automatic_variants_convert_{$locale->slug}", $translation->{"translation_{$i}"} );

				// Check if the conversion process changes the translation.
				if ( $converted !== $translation->{"translation_{$i}"} ) {

					// Set converted string as the variant translation.
					$translation_converted->{"translation_{$i}"} = $converted;

					// The translation plural have changed.
					$translation_changed = true;

				}
			}

			// Check if any of the translation plurals have changed.
			if ( ! $translation_changed ) {
				return false;
			}

			return $translation_converted;

		}

	}

}
