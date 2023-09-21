<?php
/**
 * Plugin Name: VIP Governance Plugin
 * Plugin URI: https://wpvip.com
 * Description: Plugin bundle to manage block governance options in Gutenberg.
 * Author: WordPress VIP
 * Text Domain: vip-governance
 * Version: 0.6.0
 * Requires at least: 5.8.0
 * Tested up to: 6.3.0
 * Requires PHP: 7.4
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package vip-governance
 */

namespace WPCOMVIP\Governance;

if ( ! defined( 'VIP_BLOCK_GOVERNANCE_LOADED' ) ) {
	define( 'VIP_BLOCK_GOVERNANCE_LOADED', true );

	define( 'WPCOMVIP__GOVERNANCE__PLUGIN_VERSION', '0.6.0' );
	define( 'WPCOMVIP__GOVERNANCE__RULES_SCHEMA_VERSION', '0.2.0' );

	if ( ! defined( 'WPCOMVIP_GOVERNANCE_ROOT_PLUGIN_FILE' ) ) {
		define( 'WPCOMVIP_GOVERNANCE_ROOT_PLUGIN_FILE', __FILE__ );
	}

	if ( ! defined( 'WPCOMVIP_GOVERNANCE_ROOT_PLUGIN_DIR' ) ) {
		define( 'WPCOMVIP_GOVERNANCE_ROOT_PLUGIN_DIR', __DIR__ );
	}

	define( 'WPCOMVIP_GOVERNANCE_RULES_FILENAME', 'governance-rules.json' );

	define( 'WPCOMVIP__GOVERNANCE__RULES_REST_ROUTE', 'vip-governance/v1' );

	define( 'WPCOMVIP__GOVERNANCE__STAT_NAME___USAGE', 'vip-governance-usage' );
	define( 'WPCOMVIP__GOVERNANCE__STAT_NAME___ERROR', 'vip-governance-usage-error' );

	// Composer Dependencies.
	require_once __DIR__ . '/vendor/autoload.php';

	// Analytics.
	require_once __DIR__ . '/governance/analytics.php';

	// Block Locking.
	require_once __DIR__ . '/governance/block-locking.php';

	// Utilities.
	require_once __DIR__ . '/governance/governance-utilities.php';

	// Initialize Governance.
	require_once __DIR__ . '/governance/init-governance.php';
	require_once __DIR__ . '/governance/nested-governance-processing.php';

	// Rules Parser and Validator.
	require_once __DIR__ . '/governance/rules-parser.php';

	// Settings Panel.
	require_once __DIR__ . '/governance/settings/settings.php';

	// /wp-json/ API.
	require_once __DIR__ . '/governance/rest/rest-api.php';
}
