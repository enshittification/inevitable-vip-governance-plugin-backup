<?php

namespace WPCOMVIP\Governance;

defined( 'ABSPATH' ) || die();

use Exception;

class GovernanceUtilities {

	/**
	* Get the governance rules from the private directory, or the plugin directory if not found.
	*/
	public static function get_governance_rules( $file_name, $validate_for_ui = false ) {
		$governance_file_path = WPCOM_VIP_PRIVATE_DIR . '/' . $file_name;

		if ( ! file_exists( $governance_file_path ) ) {
			$governance_file_path = WPCOMVIP_GOVERNANCE_ROOT_PLUGIN_DIR . '/' . $file_name;

			if ( ! file_exists( $governance_file_path ) ) {

				if ( $validate_for_ui ) {
					/* translators: %s: governance file name */
					$error_message = sprintf( __( 'Governance rules (%s) could not be found in private, or plugin folders.', 'vip-governance' ), $file_name );
				} else {
					/* translators: %s: governance file name */
					$error_message = __( 'Error loading the governance rules. Please check the VIP Governance panel for errors.', 'vip-governance' );
				}

				throw new Exception( $error_message );
			}
		}

		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$governance_rules_json = file_get_contents( $governance_file_path );

		$governance_rules = RulesParser::parse( $governance_rules_json );

		if ( is_wp_error( $governance_rules ) ) {
			$error_message = $governance_rules->get_error_message();

			if ( $validate_for_ui ) {
				/* translators: %s: governance file name */
				$error_message = sprintf( __( 'Governance rules will not be loaded. %s', 'vip-governance' ), $error_message );
			} else {
				/* translators: %s: governance file name */
				$error_message = __( 'Error loading the governance rules. Please check the VIP Governance panel for errors.', 'vip-governance' );
			}

			throw new Exception( $error_message );
		}

		return $governance_rules;
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
