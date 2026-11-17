<?php

namespace Elementor\MCP\Composer\Mcp;

use Elementor\MCP\Composer\Admin\McpSettingsController;
use WP\MCP\Core\McpAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Boots the unified Elementor MCP server from the shared ability registry.
 */
class Server_Bootstrap {

	/**
	 * First adapter version that reads resource URIs from the `mcp.uri` ability meta.
	 */
	const MIN_NAMESPACED_RESOURCE_META_VERSION = '0.5.0';

	/**
	 * Description for the dedicated (legacy) Elementor MCP server endpoint.
	 *
	 * Points models at the default adapter server (see get_default_server_rest_url()).
	 */
	const ELEMENTOR_MCP_SERVER_DESCRIPTION = 'Read and modify Elementor Editor abilities. This is the legacy Elementor MCP endpoint: switch to the default MCP server (mcp-adapter-default-server, at /mcp/mcp-adapter-default-server), which exposes the same Elementor abilities, and use its tools instead of this server.';

	/**
	 * Whether the mcp_adapter_init hook has already been registered.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * Register the MCP adapter hook when the adapter is available.
	 */
	public function __construct() {
		if ( self::$hooked ) {
			return;
		}

		if ( ! class_exists( McpAdapter::class ) || ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		self::$hooked = true;

		if ( ! self::is_loaded_adapter_supported() ) {
			add_filter( 'mcp_adapter_default_server_config', [ $this, 'exclude_registry_resources' ] );
		}

		add_filter( 'mcp_adapter_default_server_config', [ $this, 'register_on_default_server' ] );
		add_action( 'mcp_adapter_init', [ $this, 'register_server' ] );

		McpAdapter::instance();

		// Added after the adapter hooks. The ability itself is registered on
		// `wp_abilities_api_init`, which the adapter fires from
		// DefaultServerFactory::create() (on `mcp_adapter_init`) before it reads
		// `mcp_adapter_default_server_config` and resolves the tool names, so the
		// enable tool exists by the time the default server lists it.
		( new Enable_Ability() )->register_hooks();
	}

	/**
	 * REST URL for the WordPress MCP Adapter default server (recommended client endpoint).
	 *
	 * @return string
	 */
	public static function get_default_server_rest_url(): string {
		return \rest_url( 'mcp/mcp-adapter-default-server' );
	}

	/**
	 * Create the unified Elementor MCP server.
	 *
	 * @param mixed $adapter McpAdapter instance.
	 * @return void
	 */
	public function register_server( $adapter ): void {
		if ( ! McpSettingsController::is_enabled() ) {
			return;
		}

		if ( ! $adapter instanceof McpAdapter ) {
			return;
		}

		$registry  = Registry::instance();
		$tools     = $registry->get_tools();
		$resources = self::is_loaded_adapter_supported() ? $registry->get_resources() : [];
		$prompts   = $registry->get_prompts();

		if ( empty( $tools ) && empty( $resources ) && empty( $prompts ) ) {
			return;
		}

		$result = $adapter->create_server(
			'elementor-mcp-server',
			'elementor',
			'mcp',
			'Elementor MCP',
			self::ELEMENTOR_MCP_SERVER_DESCRIPTION,
			'v1.0.0',
			[ \WP\MCP\Transport\HttpTransport::class ],
			\WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class,
			\WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
			$tools,
			$resources,
			$prompts
		);

		if ( is_wp_error( $result ) && $result instanceof \WP_Error ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( '[Elementor MCP] Server registration failed: %s', $result->get_error_message() ) );
		}
	}

	/**
	 * Attach every registry slug to the adapter default server, or only the
	 * enable tool while the abilities are disabled.
	 *
	 * @param mixed $config Default server config.
	 * @return mixed
	 */
	public function register_on_default_server( $config ) {
		if ( ! is_array( $config ) ) {
			return $config;
		}

		if ( ! McpSettingsController::is_enabled() ) {
			$config['tools'] = $this->merge_slugs( $config['tools'] ?? [], [ Enable_Ability::ABILITY_NAME ] );

			return $config;
		}

		$registry = Registry::instance();

		$config['tools']   = $this->merge_slugs( $config['tools'] ?? [], $registry->get_tools() );
		$config['prompts'] = $this->merge_slugs( $config['prompts'] ?? [], $registry->get_prompts() );

		if ( self::is_loaded_adapter_supported() ) {
			$config['resources'] = $this->merge_slugs( $config['resources'] ?? [], $registry->get_resources() );
		} else {
			$config['resources'] = $this->remove_slugs( $config['resources'] ?? [], $registry->get_resources() );
		}

		return $config;
	}

	/**
	 * Remove registry resources from the adapter's auto-discovered default server.
	 *
	 * @param mixed $config Default server config.
	 * @return mixed
	 */
	public function exclude_registry_resources( $config ) {
		if ( ! is_array( $config ) || empty( $config['resources'] ) || ! is_array( $config['resources'] ) ) {
			return $config;
		}

		$config['resources'] = array_values( array_diff( $config['resources'], Registry::instance()->get_resources() ) );

		return $config;
	}

	/**
	 * Whether an adapter version reads resource URIs from `mcp.uri`.
	 *
	 * @param string $adapter_version Adapter version.
	 * @return bool
	 */
	public static function supports_namespaced_resource_meta( string $adapter_version ): bool {
		return version_compare( $adapter_version, self::MIN_NAMESPACED_RESOURCE_META_VERSION, '>=' );
	}

	/**
	 * Whether the MCP adapter copy that won class resolution reads `mcp.uri`.
	 *
	 * @return bool
	 */
	private static function is_loaded_adapter_supported(): bool {
		return self::supports_namespaced_resource_meta( self::loaded_adapter_version() );
	}

	/**
	 * Version of the MCP adapter copy that won class resolution.
	 *
	 * @return string
	 */
	private static function loaded_adapter_version(): string {
		if ( isset( $GLOBALS['_test_mcp_adapter_version'] ) && is_string( $GLOBALS['_test_mcp_adapter_version'] ) ) {
			return $GLOBALS['_test_mcp_adapter_version'];
		}

		$version_constant = McpAdapter::class . '::VERSION';

		return defined( $version_constant ) ? (string) constant( $version_constant ) : '0';
	}

	/**
	 * @param mixed    $existing Existing slugs.
	 * @param string[] $slugs    Registry slugs.
	 * @return string[]
	 */
	private function merge_slugs( $existing, array $slugs ): array {
		$existing = is_array( $existing ) ? $existing : [];

		return array_values( array_unique( array_merge( $existing, $slugs ) ) );
	}

	/**
	 * @param mixed    $existing Existing slugs.
	 * @param string[] $slugs    Registry slugs.
	 * @return string[]
	 */
	private function remove_slugs( $existing, array $slugs ): array {
		$existing = is_array( $existing ) ? $existing : [];

		return array_values( array_diff( $existing, $slugs ) );
	}
}
