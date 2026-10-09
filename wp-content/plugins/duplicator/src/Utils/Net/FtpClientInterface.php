<?php

namespace Duplicator\Utils\Net;

/**
 * Interface for FTP clients
 *
 * @todo Update camel caps methodd names when old classes are moved in src/Utils/Net
 */
interface FtpClientInterface
{
    /**
     * Creates a directory on the FTP server
     *
     * @param string $directory The directory to create
     *
     * @return bool True on success, false on failure
     */
    public function create_directory($directory); // phpcs:ignore

    /**
     * Checks if a directory exists on the FTP server
     *
     * @param string $directory The directory to check
     *
     * @return bool True if the directory exists, false otherwise
     */
    public function directory_exists($directory); // phpcs:ignore

    /**
     * Uploads a file to the FTP server
     *
     * @param string $source_filepath The full path to the file to upload
     * @param string $storage_folder  The folder on the FTP server to upload to
     * @param string $dest_filename   The name of the file on the FTP server.  If blank then the source file name is used
     *
     * @return bool True on success, false on failure
     */
    public function upload_file($source_filepath, $storage_folder, $dest_filename = ''); // phpcs:ignore

    /**
     * upload file by chunking mode
     *
     * @param string $source_filepath        local file path
     * @param string $storage_folder         remote folder
     * @param int    $max_upload_time_in_sec max upload time in sec in a chunk
     * @param int    $offset                 file offset
     * @param int    $server_load_delay      server load delay
     * @param string $dest_filename          remote file name
     *
     * @return FTPUploadInfo
     */
    public function upload_chunk( // phpcs:ignore
        $source_filepath,
        $storage_folder,
        $max_upload_time_in_sec = 15,
        $offset = 0,
        $server_load_delay = 0,
        $dest_filename = ''
    );

    /**
     * get information of the FTP
     *
     * @return string
     */
    public function get_info(); // phpcs:ignore

    /**
     * Gets a list of files in a directory on the FTP server
     *
     * @param string $directory The directory to get the file list from
     *
     * @return string[]|false
     */
    public function get_filelist($directory = '.'); // phpcs:ignore

    /**
     * Deletes a file from the FTP server
     *
     * @param string $file_path The full path to the file to delete
     *
     * @return bool True on success, false on failure
     */
    public function delete($file_path); // phpcs:ignore

    /**
     * Downloads a file from the FTP server
     *
     * @param string $remote_source_filepath The full path to the file to download
     * @param string $local                  The local path to download to
     * @param bool   $is_local_directory     Whether the local path is a directory or a file
     *
     * @return bool True on success, false on failure
     */
    public function download_file( // phpcs:ignore
        $remote_source_filepath,
        $local,
        $is_local_directory = true
    );
}
