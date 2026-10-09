<?php
/**
 * WPCF7R_Dashboard.
 *
 * Class for handling the admin dashboard interface, analytics, and data display.
 *
 * @package WPCF7_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPCF7R_Save_File class.
 *
 * Manages the files saving for Save Entry
 */
class WPCF7R_Save_File {

	/**
	 * Register REST API endpoints for file management.
	 *
	 * @return void
	 */
	public function register_endpoints() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'wpcf7r/v1',
					'/download-file',
					array(
						'methods'             => 'GET',
						'callback'            => array( $this, 'download_file' ),
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
						'args'                => array(
							'file_key'      => array(
								'required'          => true,
								'validate_callback' => function ( $param ) {
									return is_string( $param ) && ! empty( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
							'entry_post_id' => array(
								'required'          => true,
								'validate_callback' => function ( $param ) {
									if ( ! function_exists( 'absint' ) ) {
										return is_numeric( $param ) && (int) $param > 0;
									}
									return is_numeric( $param ) && absint( $param ) > 0;
								},
								'sanitize_callback' => function ( $param ) {
									if ( ! function_exists( 'absint' ) ) {
										return (int) $param;
									}
									return absint( $param );
								},
							),
						),
					)
				);
			}
		);
	}

	/**
	 * Register the hooks to delete the images from the folder when an entry is deleted.
	 */
	public function register_file_deletion() {
		add_action( 'before_delete_post', array( $this, 'delete_associated_files' ), 10, 2 );
	}

	/**
	 * Delete files associated with a lead entry when the entry is deleted.
	 *
	 * @param int     $postid The post ID.
	 * @param WP_Post $post   The post object.
	 */
	public function delete_associated_files( $postid, $post ) {
		if ( 'wpcf7r_leads' !== $post->post_type ) {
			return;
		}

		$files_meta = get_post_meta( $postid, 'files', true );

		if ( ! is_array( $files_meta ) || empty( $files_meta ) ) {
			return;
		}

		$upload_dir         = $this->get_uploads_dir();
		$normalized_uploads = wp_normalize_path( $upload_dir );

		global $wp_filesystem;
		$this->filesystem_init();

		foreach ( $files_meta as $file_data ) {
			foreach ( self::get_stored_paths( $file_data ) as $stored_path ) {
				$file_name = sanitize_file_name( basename( $stored_path ) );
				$file_path = path_join( $normalized_uploads, $file_name );

				if ( $wp_filesystem->exists( $file_path ) ) {
					$wp_filesystem->delete( $file_path );
				}
			}
		}
	}

	/**
	 * Read the stored path(s) of a single `files` meta entry.
	 *
	 * Entries saved by older versions — and multi-file upload fields — store
	 * `path` as an array of paths instead of a string, so every reader has to
	 * accept both shapes. Malformed entries yield no paths rather than a
	 * TypeError from the filesystem functions downstream.
	 *
	 * @param mixed $file_data A single entry of the `files` post meta.
	 * @return string[] Stored paths, empty when the entry holds none.
	 */
	private static function get_stored_paths( $file_data ) {
		$paths = is_array( $file_data ) && isset( $file_data['path'] ) ? $file_data['path'] : array();

		if ( ! is_array( $paths ) ) {
			$paths = array( $paths );
		}

		return array_values(
			array_filter(
				$paths,
				static function ( $path ) {
					return is_string( $path ) && '' !== $path;
				}
			)
		);
	}

	/**
	 * Handle file download requests via REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error Response or error.
	 */
	public function download_file( $request ) {
		$file_key      = $request->get_param( 'file_key' );
		$entry_post_id = $request->get_param( 'entry_post_id' );

		$files_post_meta = get_post_meta( $entry_post_id, 'files', true );

		if ( false === $files_post_meta || ! is_array( $files_post_meta ) ) {
			return new WP_Error( 'entry_meta_not_found', __( 'File information not found', 'wpcf7-redirect' ), array( 'status' => 404 ) );
		}

		$stored_paths = isset( $files_post_meta[ $file_key ] ) ? self::get_stored_paths( $files_post_meta[ $file_key ] ) : array();

		if ( ! $stored_paths ) {
			return new WP_Error( 'file_not_found_in_entry', __( 'File path not found', 'wpcf7-redirect' ), array( 'status' => 404 ) );
		}

		// A multi-file field serves its first file, which is the only one the
		// Save Entry action keeps. Add a file_index request arg if the rest
		// ever need to be reachable.
		$file_path = $stored_paths[0];

		$upload_dir = $this->get_uploads_dir();

		// Verify the file is within our uploads directory for security.
		$normalized_path    = wp_normalize_path( $file_path );
		$normalized_uploads = wp_normalize_path( $upload_dir );

		if ( 0 !== strpos( $normalized_path, $normalized_uploads ) ) {
			return new WP_Error( 'invalid_path', __( 'Invalid file path', 'wpcf7-redirect' ), array( 'status' => 403 ) );
		}

		if ( ! file_exists( $file_path ) ) {
			return new WP_Error( 'file_not_found_in_storage', __( 'File not found in storage', 'wpcf7-redirect' ), array( 'status' => 404 ) );
		}

		$file_info = pathinfo( $file_path );
		$filename  = sanitize_file_name( $file_info['basename'] );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $file_path ) );

		ob_clean();
		flush();
		readfile( $file_path );
		exit;
	}

	/**
	 * Move a file to the upload directory with a random hash appended to the filename.
	 *
	 * @param string $file_path The path to the original file.
	 * @return string|false The destination path if successful, false otherwise.
	 */
	public function move_file_to_upload( $file_path ) {

		$validate = wp_check_filetype( $file_path );
		if ( ! $validate['type'] || preg_match( '#^[a-zA-Z0-9+.-]+://#', $file_path ) ) {
			die( esc_html__( 'File type is not allowed', 'wpcf7-redirect' ) );
		}

		global $wp_filesystem;
		$this->filesystem_init();

		$upload_dir = $this->get_uploads_dir();

		// Prepare file with a random suffix for security.
		$file_info     = pathinfo( $file_path );
		$base_name     = $file_info['filename'];
		$extension     = isset( $file_info['extension'] ) ? '.' . $file_info['extension'] : '';
		$random_hash   = $this->generate_random_hash();
		$new_file_name = $base_name . '.' . $random_hash . $extension;

		// Make sure the destination filename is unique.
		$destination      = wp_unique_filename( $upload_dir, $new_file_name );
		$destination_path = path_join( $upload_dir, $destination );

		$moved = $wp_filesystem->copy( $file_path, $destination_path, true );

		if ( ! $moved ) {
			return false;
		}

		$wp_filesystem->chmod( $destination_path );

		return $destination_path;
	}

	/**
	 * Initializes the WordPress filesystem API.
	 *
	 * @return void
	 */
	private function filesystem_init() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();
	}

	/**
	 * Generates a random hash.
	 *
	 * @param string $seed Optional. An initial string to include in the value to be hashed. Defaults to an empty string.
	 * @return string The generated WordPress hash string.
	 */
	private function generate_random_hash( $seed = '' ) {
		$seed = $seed . microtime( true ) . mt_rand();
		return wp_hash( $seed );
	}

	/**
	 * Get the upload folder. Create if it does not exists.
	 *
	 * @return string The path to the folder.
	 */
	public function get_uploads_dir() {
		if ( defined( 'WPCF7R_UPLOADS_DIR' ) ) {
			$dir = path_join( WP_CONTENT_DIR, WPCF7R_UPLOADS_DIR );
			wp_mkdir_p( $dir );

			if ( wpcf7_is_file_path_in_content_dir( $dir ) ) {
				return $dir;
			}
		}

		// Get the folder in the same folder as 'wpcf7_uploads'.
		$dir = path_join( wpcf7_upload_dir( 'dir' ), 'wpcf7r_uploads' );
		wp_mkdir_p( $dir );
		return $dir;
	}

	/**
	 * Initializes the uploads directory by creating a secure .htaccess file.
	 *
	 * This is the same way Contact Form 7 secure its upload folder.
	 *
	 * @access public
	 * @see https://github.com/rocklobster-in/contact-form-7/blob/7cc9b0fa9fa428c34f8f60ddf89157c4873ed524/includes/file.php#L259-L290
	 * @return void
	 */
	public function init_uploads_dir() {
		$dir = $this->get_uploads_dir();

		if ( is_dir( $dir ) && is_writable( $dir ) ) {
			$this->init_index_file( $dir );
			$htaccess_file = path_join( $dir, '.htaccess' );

			if ( file_exists( $htaccess_file ) ) {
				list( $first_line_comment ) = (array) file(
					$htaccess_file,
					FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
				);

				if ( '# Apache 2.4+' === $first_line_comment ) {
					return;
				}
			}

			$handle = @fopen( $htaccess_file, 'w' );

			if ( $handle ) {
				fwrite( $handle, "# Apache 2.4+\n" );
				fwrite( $handle, "<IfModule authz_core_module>\n" );
				fwrite( $handle, "    Require all denied\n" );
				fwrite( $handle, "</IfModule>\n" );
				fwrite( $handle, "\n" );
				fwrite( $handle, "# Apache 2.2\n" );
				fwrite( $handle, "<IfModule !authz_core_module>\n" );
				fwrite( $handle, "    Deny from all\n" );
				fwrite( $handle, "</IfModule>\n" );

				fclose( $handle );
			}
		}
	}

	/**
	 * Initializes the index.php file.
	 *
	 * @param string $dir Upload dir path.
	 * @return void
	 */
	private function init_index_file( $dir ) {
		$index_file    = path_join( $dir, 'index.php' );

		if ( file_exists( $index_file ) ) {
			list( , $second_line_comment ) = (array) file(
				$index_file,
				FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
			);

			if ( '// Silence is golden.' === $second_line_comment ) {
				return;
			}
		}

		$handle = @fopen( $index_file, 'w' );

		if ( $handle ) {
			fwrite( $handle, "<?php\n" );
			fwrite( $handle, '// Silence is golden.' );

			fclose( $handle );
		}
	}
}
