=== Automatic Variants for GlotPress ===
Contributors: pedromendonca
Donate link: https://github.com/sponsors/pedro-mendonca
Tags: localization, translation, glotpress, variants
Requires at least: 5.3
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin for GlotPress to manage automatically converted variants.

== Description ==

This plugin for GlotPress customizes the default behavior of a set of chosen pairs of Locales (root/variant), allowing you to automatically convert the approved/current strings in the root to its variant.

Keep the root translations and the variant translations automatically converted and synced in your GlotPress install.

Only translations whose conversion are different from the original root translation are added to the variant translation set.

The strings that don't need any conversion remain untranslated on the variant, falling back to the root Locale.

This plugin was heavily inspired by the [Serbian Latin](https://meta.trac.wordpress.org/ticket/5471) solution for transliteration of Serbian Cyrillic locale from [translate.wordpress.org](https://meta.trac.wordpress.org/browser/sites/trunk/wordpress.org/public_html/wp-content/plugins/wporg-gp-customizations/inc/locales/class-serbian-latin.php?rev=10360).

== Features ==

*   Filter `gp_automatic_variants_list` to add your variant to the array of automatically converted variants.
*   Filter `gp_automatic_variants_convert_{variant_locale}` to process the conversion of strings of the automatic variant.
*   Check for GlotPress minimum requirements.
*   Check if the added Locales are variants supported the installed GlotPress.
*   Convert `current` root translations and add to the variant translation set.
*   Delete variant unused translations instead of keeping as `rejected`, `fuzzy`, `old`.
*   Delete `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.

== Requirements ==

*   GlotPress 3.0.0-alpha

== Frequently Asked Questions ==

= So what does this plugin really do, after all?
It extends the translation platform GlotPress used to translate WordPress projects.
Since GlotPress 3.x there is a new Variants feature, enabling some Locales to be a variant of a root Locale. With this, comes fallback.
If a translation doesn't exist on the variant, it assumes its root translation.
This plugin links both Locales in a way that you only need to focus in translating and manage consistency on the root, knowing that the variant is being automatically converted and synced with no human action needed.
With this tool, the translators can continue to provide both Locales with the minimum effort.

= Does this means that translations it's possible to have automatically converted variants on translate.wp.org?
No(t yet). This is a working proof of concept, it works on any GlotPress 3.x, but isn't running on [translate.wp.org](https://translate.wp.org) (GlotPress based) at the moment.

= Should this feature be a part of GlotPress itself?
To be discussed.
The relationship between root/variant depend on each team that uses GlotPress.
Depending on how the translation team decides to work. It's useful if automatic conversion is wanted.
For teams that want a root/variant to work automatically, GlotPress could integrate this optional feature of setting a specific variant to be automatically converted from its root with a custom hookable process, and turning the variant read-only.
This can be used by any Locale team that want to hook an automatic conversion between root and variant Locales.
This plugin is intended to be a proof of concept to use and test this workflow.

= Can I contribute to this plugin? =
Sure! You are welcome to report any issues or add feature suggestions on the [GitHub repository](https://github.com/pedro-mendonca/GP-Automatic-Variants).

== Changelog ==

= 1.0.0 =
*   Initial release.
*   Filter `gp_automatic_variants_list` to add your variant to the array of automatically converted variants.
*   Filter `gp_automatic_variants_convert_{variant_locale}` to process the conversion of strings of the automatic variant.
*   Check for GlotPress minimum requirements.
*   Check if the added Locales are variants supported the installed GlotPress.
*   Convert `current` root translations and add to the variant translation set.
*   Delete variant unused translations instead of keeping as `rejected`, `fuzzy`, `old`.
*   Delete `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.
