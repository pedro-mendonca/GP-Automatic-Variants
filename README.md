# Automatic Variants for GlotPress

Plugin for GlotPress to manage automatically converted variants.

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/gp-automatic-variants?label=Plugin%20Version&logo=wordpress)](https://wordpress.org/plugins/gp-automatic-variants/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/stars/gp-automatic-variants?label=Plugin%20Rating&logo=wordpress)](https://wordpress.org/support/plugin/gp-automatic-variants/reviews/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/gp-automatic-variants.svg?label=Downloads&logo=wordpress)](https://wordpress.org/plugins/gp-automatic-variants/advanced/)
[![Sponsor](https://img.shields.io/badge/GitHub-🤍%20Sponsor-ea4aaa?logo=github)](https://github.com/sponsors/pedro-mendonca)

[![WordPress Plugin Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/gp-automatic-variants?label=PHP%20Required&logo=php&logoColor=white)](https://wordpress.org/plugins/gp-automatic-variants/)
[![WordPress Plugin: Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/gp-automatic-variants?label=WordPress%20Required&logo=wordpress)](https://wordpress.org/plugins/gp-automatic-variants/)
[![WordPress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/gp-automatic-variants.svg?label=WordPress%20Tested&logo=wordpress)](https://wordpress.org/plugins/gp-automatic-variants/)

[![Coding Standards](https://github.com/pedro-mendonca/GP-Automatic-Variants/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/pedro-mendonca/GP-Automatic-Variants/actions/workflows/coding-standards.yml)
[![Static Analysis](https://github.com/pedro-mendonca/GP-Automatic-Variants/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/pedro-mendonca/GP-Automatic-Variants/actions/workflows/static-analysis.yml)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/545e6b6d121a439498a0d16f72c93851)](https://www.codacy.com/gh/pedro-mendonca/GP-Automatic-Variants/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=pedro-mendonca/GP-Automatic-Variants&amp;utm_campaign=Badge_Grade)

## Description

This plugin for GlotPress customizes the default behavior of a set of chosen pairs of Locales (root/variant), allowing you to automatically convert the approved/current strings in the root to its variant.

Keep the root translations and the variant translations automatically converted and synced in your GlotPress install.

Only translations whose conversion are different from the original root translation are added to the variant translation set.

The strings that don't need any conversion remain untranslated on the variant, falling back to the root Locale.

This plugin was heavily inspired by the [Serbian Latin](https://meta.trac.wordpress.org/ticket/5471) solution for transliteration of Serbian Cyrillic locale from [translate.wordpress.org](https://meta.trac.wordpress.org/browser/sites/trunk/wordpress.org/public_html/wp-content/plugins/wporg-gp-customizations/inc/locales/class-serbian-latin.php?rev=10360).

## Features

* Filter `gp_automatic_variants_list` to add your variant to the array of automatically converted variants.  
* Filter `gp_automatic_variants_convert_{variant_locale}` to process the conversion of strings of the automatic variant.  
* Check for GlotPress minimum requirements.  
* Check if the added Locales are variants supported the installed GlotPress.  
* Convert `current` root translations and add to the variant translation set.  
* Delete variant unused translations instead of keeping as `rejected`, `fuzzy`, `old`.  
* Delete `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.  

## Installation

### Install GlotPress

Install and activate GlotPress 3.0.0-alpha minimum version.
Install and activate this plugin from your plugins page.

### Configure Automatic Variants for GlotPress

1. Set the variants you want to be automatically converted with the filter `gp_automatic_variants_list`:

	```php
	/**
	 * Add my automatically converted variants.
	 */
	function my_automatic_variants( $locales ) {
		$additional_locales = array(
			'ca-valencia',
			'pt-ao90',
			'ca-valencia',
			'en-gb',
			'de-at',
			'de-ch',
		);
		return array_merge( $locales, $additional_locales );
	}

	add_filter( 'gp_automatic_variants_list', 'my_automatic_variants' );
	```

2. Add your Locale actual conversion process with the filter `gp_automatic_variants_convert_{variant_locale}`:

	Example for the variant 'pt-ao90':

	```php
	/**
	 * Actual conversion of the string.
	 */
	function convert_translation( $translation ) {
		return do_something( $translation );
	}

	add_filter( 'gp_automatic_variants_convert_pt-ao90', 'convert_translation' );
	```

## Usage

1. For every translation project, add both root and variant translation sets as usual.

2. Translate only on the root Locale and see the conversions automatically propagate to the variant.

## Requirements

* GlotPress 3.0.0-alpha

## Frequently Asked Questions

### What does this plugin really do?

It extends the translation platform GlotPress used to translate WordPress projects.  
Since GlotPress 3.x there is a new Variants feature, enabling some Locales to be a variant of a root Locale. With this, comes fallback.  
If a translation doesn't exist on the variant, it assumes its root translation.  
This plugin links both Locales in a way that you only need to focus in translating and manage consistency on the root, knowing that the variant is being automatically converted and synced with no human action needed.  
With this tool, the translators can continue to provide both Locales with the minimum effort.  

### Does translate.wp.org supports automatically converted variants?

No(t yet). This is a working proof of concept, it works on any GlotPress 3.x, but isn't running on [translate.wp.org](https://translate.wp.org) (GlotPress based) at the moment.  

### Should this feature be a part of GlotPress itself?

To be discussed.  
The relationship between root/variant depend on each team that uses GlotPress.  
Depending on how the translation team decides to work. It's useful if automatic conversion is wanted.  
For teams that want a root/variant to work automatically, GlotPress could integrate this optional feature of setting a specific variant to be automatically converted from its root with a custom hookable process, and turning the variant read-only.  
This can be used by any Locale team that want to hook an automatic conversion between root and variant Locales.  
This plugin is intended to be a proof of concept to use and test this workflow.  

### Can I contribute to this plugin?

Sure! You are welcome to report any issues or add feature suggestions on the [GitHub repository](https://github.com/pedro-mendonca/GP-Automatic-Variants).

## Changelog

### 1.0.0

* Initial release.
* Filter `gp_automatic_variants_list` to add your variant to the array of automatically converted variants.
* Filter `gp_automatic_variants_convert_{variant_locale}` to process the conversion of strings of the automatic variant.
* Check for GlotPress minimum requirements.
* Check if the added Locales are variants supported the installed GlotPress.
* Convert `current` root translations and add to the variant translation set.
* Delete variant unused translations instead of keeping as `rejected`, `fuzzy`, `old`.
* Delete `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.
