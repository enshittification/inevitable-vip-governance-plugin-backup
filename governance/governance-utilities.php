<?php

namespace WPCOMVIP\Governance;

defined( 'ABSPATH' ) || die();

use Exception;

class GovernanceUtilities {
	/**
	* Retrieve parsed governance rules from the private directory, or the plugin directory if not found.
	*/
	public static function get_parsed_governance_rules() {
		$governance_rules_json = self::get_governance_rules_json();
		$governance_rules      = RulesParser::parse( $governance_rules_json );

		if ( is_wp_error( $governance_rules ) ) {
			/* translators: %s: governance file name */
			throw new Exception( $governance_rules->get_error_message() );
		}

		return $governance_rules;
	}

	/**
	* Get raw governance rules content from the private directory, or the plugin directory if not found.
	*/
	public static function get_governance_rules_json() {
		$governance_file_path = WPCOM_VIP_PRIVATE_DIR . '/' . WPCOMVIP_GOVERNANCE_RULES_FILENAME;

		if ( ! file_exists( $governance_file_path ) ) {
			$governance_file_path = WPCOMVIP_GOVERNANCE_ROOT_PLUGIN_DIR . '/' . WPCOMVIP_GOVERNANCE_RULES_FILENAME;

			if ( ! file_exists( $governance_file_path ) ) {
				/* translators: %s: governance file name */
				throw new Exception( sprintf( __( 'Governance rules (%s) could not be found in private or plugin folders.', 'vip-governance' ), WPCOMVIP_GOVERNANCE_RULES_FILENAME ) );
			}
		}

		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$governance_rules_json = file_get_contents( $governance_file_path );
		return $governance_rules_json;
	}

	/**
	 * Get the rules for the current user, with a default fallback rule set of
	 * allowing core/heading, core/paragraph and core/image
	 */
	public static function get_rules_for_user( $governance_rules ) {
		if ( empty( $governance_rules ) ) {
			return array();
		}

		$current_user = wp_get_current_user();
		$user_roles   = $current_user->roles;

		$allowed_features = array();
		$allowed_blocks   = array();
		$block_settings   = array();

		foreach ( $governance_rules as $rule ) {
			// The allowed blocks can be merged together with the default role to get a super set
			// The Block Settings and Allowed Features are only to be picked up from the default role, if a role specific one doesn't exist
			if ( isset( $rule['type'] ) && 'role' === $rule['type'] && isset( $rule['roles'] ) && array_intersect( $user_roles, $rule['roles'] ) ) {
				$allowed_blocks   = isset( $rule['allowedBlocks'] ) ? array_merge( $allowed_blocks, $rule['allowedBlocks'] ) : $allowed_blocks;
				$block_settings   = isset( $rule['blockSettings'] ) ? $rule['blockSettings'] : $block_settings;
				$allowed_features = isset( $rule['allowedFeatures'] ) ? $rule['allowedFeatures'] : $allowed_features;
			} elseif ( isset( $rule['type'] ) && 'default' === $rule['type'] ) {
				$allowed_blocks   = isset( $rule['allowedBlocks'] ) ? array_merge( $allowed_blocks, $rule['allowedBlocks'] ) : $allowed_blocks;
				$block_settings   = isset( $rule['blockSettings'] ) && empty( $block_settings ) ? $rule['blockSettings'] : $block_settings;
				$allowed_features = isset( $rule['allowedFeatures'] ) && empty( $allowed_features ) ? $rule['allowedFeatures'] : $allowed_features;
			}
		}

		// return array of allowed_blocks and block_settings
		return array(
			'allowedBlocks'   => $allowed_blocks,
			'blockSettings'   => $block_settings,
			'allowedFeatures' => $allowed_features,
		);
	}
}