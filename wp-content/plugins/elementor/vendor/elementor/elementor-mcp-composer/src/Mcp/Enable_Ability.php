<?php

namespace Elementor\MCP\Composer\Mcp;

use Elementor\MCP\Composer\Admin\McpSettingsController;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Tool for the adapter default server that enables the Elementor abilities.
 *
 * It is registered only while the abilities are disabled and listed on the
 * default server only (see Server_Bootstrap::register_on_default_server()).
 * Enabling stores the same setting as the admin settings route, through
 * McpSettingsController::set_enabled().
 */
class Enable_Ability {

	const ABILITY_NAME = 'elementor/enable-elementor-abilities';
	const CATEGORY     = 'elementor-mcp-composer';

	/**
	 * Capability required to use the tool.
	 *
	 * IMPORTANT: this MUST be and stay an administrator-only capability. The tool
	 * changes site state (it turns the Elementor abilities on for MCP clients), so
	 * it must never be relaxed to a lower capability such as `edit_posts`.
	 */
	const REQUIRED_CAPABILITY = 'manage_options';

	/**
	 * Hook ability registration.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'wp_abilities_api_categories_init', [ $this, 'register_category' ] );
		add_action( 'wp_abilities_api_init', [ $this, 'register_ability' ] );
	}

	/**
	 * Register the ability category.
	 *
	 * @return void
	 */
	public function register_category(): void {
		if ( McpSettingsController::is_enabled() || ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		\wp_register_ability_category(
			self::CATEGORY,
			[
				'label'       => __( 'Elementor MCP', 'elementor' ),
				'description' => __( 'Elementor MCP setup.', 'elementor' ),
			]
		);
	}

	/**
	 * Register the ability only while the Elementor abilities are disabled.
	 *
	 * @return void
	 */
	public function register_ability(): void {
		if ( McpSettingsController::is_enabled() || ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		\wp_register_ability(
			self::ABILITY_NAME,
			[
				'label'               => __( 'Enable Elementor tools', 'elementor' ),
				'description'         => __( 'The Elementor tools are disabled on this site. Ask the user first whether to enable them, and set consent to true ONLY after the user explicitly agrees in this chat; never set it on your own. With consent true, and an administrator signed in, this tool enables the Elementor tools on the site. After it succeeds, reload your tool list: the Elementor tools are then available and this tool is no longer listed.', 'elementor' ),
				'category'            => self::CATEGORY,
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'consent' => [
							'type'        => 'boolean',
							'description' => __( 'Set to true only after the user explicitly agreed, in this chat, to enabling the Elementor tools.', 'elementor' ),
						],
					],
					'required'             => [ 'consent' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'status'       => [ 'type' => 'string' ],
						'message'      => [ 'type' => 'string' ],
						'reload_tools' => [ 'type' => 'boolean' ],
					],
					'required'   => [ 'status', 'message', 'reload_tools' ],
				],
				'permission_callback' => [ $this, 'check_permission' ],
				'execute_callback'    => [ $this, 'execute' ],
				'meta'                => [
					'show_in_rest' => false,
					'mcp'          => [ 'public' => false ],
					'annotations'  => [
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => false,
					],
				],
			]
		);
	}

	/**
	 * Only administrators may use the tool, and only while the abilities are disabled.
	 *
	 * IMPORTANT: this gate MUST stay admin-only (REQUIRED_CAPABILITY). The tool
	 * changes site state, so do not loosen it.
	 *
	 * @return bool|WP_Error
	 */
	public function check_permission() {
		if ( McpSettingsController::is_enabled() ) {
			return new WP_Error(
				'already_enabled',
				__( 'Elementor tools are already enabled.', 'elementor' )
			);
		}

		if ( ! self::current_user_is_admin() ) {
			return new WP_Error(
				'insufficient_capability',
				__( 'You are not allowed to manage Elementor MCP settings.', 'elementor' )
			);
		}

		return true;
	}

	/**
	 * Enable the Elementor abilities when the model reports user consent.
	 *
	 * The consent flag only records that the model asked the user. The change is
	 * made only for an administrator and only for a strict boolean true.
	 *
	 * @param mixed $input Ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public function execute( $input = null ) {
		// IMPORTANT: re-check here too, so a direct call of the callback cannot skip the admin-only gate.
		$allowed = $this->check_permission();

		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		if ( ! is_array( $input ) || ! array_key_exists( 'consent', $input ) || true !== $input['consent'] ) {
			return new WP_Error(
				'consent_required',
				__( 'Nothing was changed. Ask the user whether to enable the Elementor tools, then call this tool again with consent set to true.', 'elementor' )
			);
		}

		$saved = McpSettingsController::set_enabled( true );

		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		return [
			'status'       => 'enabled',
			'message'      => __( 'The Elementor tools are now enabled. Reload your tool list to see them; this tool will no longer be listed.', 'elementor' ),
			'reload_tools' => true,
		];
	}

	/**
	 * Whether the current user has the administrator capability.
	 *
	 * @return bool
	 */
	private static function current_user_is_admin(): bool {
		return function_exists( 'current_user_can' ) && \current_user_can( self::REQUIRED_CAPABILITY );
	}
}
