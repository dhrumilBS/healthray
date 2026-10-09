<?php

namespace Duplicator\Addons\DupCloudAddon\Utils;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Addons\DupCloudAddon\Exceptions\PresignedUrlExpiredException;
use Duplicator\Addons\DupCloudAddon\Utils\RemoteStorageInfo;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Models\Storages\StoragePathInfo;
use Error;
use Exception;
use VendorDuplicator\WpOrg\Requests\Requests;

class DupCloudClient
{
    /** @var int Number of part URLs to request */
    const MAX_PART_REQUEST_COUNT  = 50;
    const BACKUP_TYPE_STANDARD    = 'standard';
    const BACKUP_TYPE_INCREMENTAL = 'incremental';

    /** @var int Seconds to wait for requests that make the cloud finalize, fail or cancel a backup */
    const BACKUP_REQUEST_TIMEOUT = 300;
    /** @var int Seconds to wait for the storage status request, which also runs on admin pages */
    const STATUS_REQUEST_TIMEOUT = 30;

    const SNAPSHOT_STATUS_PENDING = 'pending';
    const SNAPSHOT_STATUS_READY   = 'ready';

    /** @var string Upload processing states reported by the cloud */
    const UPLOAD_STATE_QUEUED     = 'queued';
    const UPLOAD_STATE_PROCESSING = 'processing';
    const UPLOAD_STATE_COMPLETED  = 'completed';
    const UPLOAD_STATE_FAILED     = 'failed';
    /** @var string Client-side state: the cloud has no record of the upload anymore */
    const UPLOAD_STATE_NOT_FOUND = 'not_found';

    /**
     * @var string The API URL
     */
    const API_PATH = '/api/';

    /**
     * @var string The API URL
     */
    const AUTH_PATH = '/dashboard/websites/create/';

    /** @var string backup storage token */
    private string $storageToken = '';
    /** @var string backup type advertised on every request */
    private string $backupType = self::BACKUP_TYPE_STANDARD;

    /**
     * Class constructor
     *
     * @param string $storageToken The storage token
     * @param string $backupType   The backup type advertised on every request
     *
     * @return void
     */
    public function __construct(string $storageToken = '', string $backupType = self::BACKUP_TYPE_STANDARD)
    {
        $this->storageToken = $storageToken;
        $this->backupType   = $backupType;
    }

    /**
     * Get Manage license storage Url
     *
     * @return string
     */
    public static function getManageLicenseStorageUrl(): string
    {
        return DUPLICATOR_STORE_URL . '/my-account/storages/';
    }

    /**
     * Get the public Cloud landing URL.
     *
     * @return string
     */
    public static function getLandingUrl(): string
    {
        return trailingslashit(DUPLICATOR_CLOUD_HOST);
    }

    /**
     * Get the Duplicator Cloud register URL
     *
     * @return string
     */
    public static function getRegisterUrl(): string
    {
        return DUPLICATOR_CLOUD_HOST . '/dashboard/register';
    }

    /**
     * Get create new remote bucker URL
     *
     * @return string
     */
    public static function manageWebsitesUrl(): string
    {
        return DUPLICATOR_CLOUD_HOST . '/dashboard/websites';
    }

    /**
     * Set the storage token
     *
     * @param string $storageToken The storage token
     *
     * @return void
     */
    public function setStorageToken(string $storageToken): void
    {
        $this->storageToken = $storageToken;
    }

    /**
     * The backup type advertised on every outgoing request.
     *
     * @return string
     */
    public function getBackupType(): string
    {
        return $this->backupType;
    }

    /**
     * Send a request to the cloud.
     *
     * The bearer token is read from $this->storageToken — call setStorageToken()
     * first (or pass a token to the constructor) for authenticated requests.
     * Endpoints that must not present an Authorization header (e.g. the auth
     * exchange itself) call this while the token is still empty.
     *
     * @param string              $path    The API path
     * @param string              $type    The request type
     * @param array<string,mixed> $headers The headers
     * @param array<string,mixed> $data    The data
     * @param array<string,mixed> $options The options
     *
     * @return array{success:bool,httpCode:int,data:array<string,mixed>,message:string} The result
     */
    protected function request(
        string $path,
        string $type = Requests::GET,
        array $headers = [],
        array $data = [],
        array $options = []
    ): array {
        $url    = self::getApiUrl($path);
        $result = [
            'success'  => false,
            'httpCode' => -1,
            'data'     => [],
            'message'  => '',
        ];

        try {
            if (DupCloudRateLimitHandler::isBlocked($url)) {
                DupLog::infoTrace("DupCloud rate limit hit for $url. Not sending request.");
                return [
                    'success'  => false,
                    'httpCode' => 429,
                    'data'     => [],
                    'message'  => __('DupCloud rate limit hit for this URL. Please try again later.', 'duplicator'),
                ];
            }

            $headers['Accept'] = 'application/json';
            if (strlen($this->storageToken) > 0) {
                $headers['Authorization'] = 'Bearer ' . $this->storageToken;
            }

            $request = [
                'url'     => $url,
                'type'    => $type,
                'headers' => $headers,
                'data'    => $data,
            ];

            $response           = Requests::request(self::getApiUrl($path), $headers, $data, $type, $options);
            $result['success']  = false;
            $result['httpCode'] = $response->status_code;
            $bodyDecoded        = !empty($response->body) ? $response->decode_body() : [];
            $result['data']     = ($bodyDecoded['data'] ?? []);

            if ($response->status_code === 429) {
                $retryAfter = $bodyDecoded['retry_after'] ?? 60;
                do_action('duplicator_dup_cloud_rate_limit_error', $url, $retryAfter);
            }

            if (isset($bodyDecoded['message']) && strlen($bodyDecoded['message']) > 0) {
                $result['message'] = $bodyDecoded['message'];
            } else {
                $result['message'] = '';
            }

            if ($response->status_code < 200 || $response->status_code >= 300) {
                $result['success'] = false;
                $result['message'] = (strlen($result['message']) > 0 ?
                    $result['message'] :
                    sprintf(__('Remote server error code: %s', 'duplicator'), $response->status_code)
                );
                DupLog::traceBacktrace('ERROR ON CLOUD HTTP REQUEST msg: ' . $result['message']);
                DupLog::traceObject('REQUEST:', $request);
                DupLog::traceObject('RESPONSE:', $bodyDecoded);
            } elseif (isset($bodyDecoded['success']) && $bodyDecoded['success'] === false) {
                $result['success'] = false;
                $result['message'] = (strlen($result['message']) > 0 ?
                    $result['message'] :
                    __('Remote server error', 'duplicator')
                );
                DupLog::traceBacktrace('ERROR ON CLOUD REQUEST FUNCTION msg: ' . $result['message']);
                DupLog::traceObject('REQUEST:', $request);
                DupLog::traceObject('RESPONSE:', $bodyDecoded);
            } else {
                $result['success'] = true;
                $result['message'] = (strlen($result['message']) > 0) ? $result['message'] : __('Success', 'duplicator');

                if (!isset($bodyDecoded['data'])) {
                    $result['data'] = [];
                    DupLog::traceObject('REQUEST:', $request);
                    DupLog::traceObject('RESPONSE DATA NOT FOUND IN BODY:', $bodyDecoded);
                }
            }
        } catch (Exception | Error $e) {
            $result['success']  = false;
            $result['httpCode'] = -1;
            $result['message']  = $e->getMessage();
            DupLog::traceException($e, 'ERROR EXCEPTION ON CLOUD REQUEST');
            DupLog::traceObject('REQUEST:', $request);
        }

        return $result;
    }


    /**
     * Checks if the Cloud is available and ready to use.
     * if success is false meant that request failed
     * if authorized is false means that the storage token is not valid or expired
     * if ready is false means that the storage is not ready to use, probably another upload is in progress
     *
     * Endpoint: website/verify-storage
     * Response: [
     *     'success' => bool,
     *     'message' => string,
     *     'data' => [
     *         'authorized' => bool,
     *         'user_name' => string,
     *         'user_email' => string,
     *         'ready_for_upload' => bool,
     *         'total_space' => int,
     *         'free_space' => int,
     *         'message' => string
     *     ]
     * ]
     *
     * @param string $message Reference for any error message.
     *
     * @return RemoteStorageInfo Returns RemoteStorageInfo instance with default values if request fails
     */
    public function remoteStorageInfo(string &$message = ''): RemoteStorageInfo
    {
        if (strlen($this->storageToken) === 0) {
            // If token is empty return success and authorized false
            return new RemoteStorageInfo(true, false);
        }

        $result  = $this->request(
            'website/verify-storage',
            Requests::GET,
            [],
            [],
            ['timeout' => self::STATUS_REQUEST_TIMEOUT]
        );
        $message = $result['message'];

        if (!$result['success']) {
            // Unauthorized 401 HTTP code is considered success
            $success = ($result['httpCode'] === 401);
            return new RemoteStorageInfo($success);
        }

        return new RemoteStorageInfo(
            true,
            (bool) $result['data']['authorized'],
            (bool) $result['data']['ready_for_upload'],
            (int) $result['data']['total_space'],
            (int) $result['data']['free_space'],
            (string) $result['data']['user_name'],
            (string) $result['data']['user_email'],
            (string) $result['data']['website_uuid']
        );
    }

    /**
     * Get user info
     *
     * @return array{name:string,email:string,email_verified_at:string,created_at:string}
     */
    public function getUserInfo()
    {
        $result = $this->request('user');
        if (!$result['success']) {
            DupLog::traceObject('Failed to get user info', $result);
            throw new Exception($result['message']);
        }
        return $result['data'];
    }

    /**
     * Revokes authorization
     *
     * @return bool
     */
    public function revoke()
    {
        $result = $this->request(
            'revoke',
            Requests::POST
        );

        return $result['success'];
    }

    /**
     * Start Upload
     *
     * @param array<stirng, mixed> $backupDetails The backup details
     *
     * @return array{'uuid': string, 'url': string}
     */
    public function startUpload($backupDetails)
    {
        if (!isset($backupDetails['file_info']['backup_filename'])) {
            throw new Exception('Invalid backup details');
        }
        if (!preg_match(DUPLICATOR_ARCHIVE_REGEX_PATTERN, $backupDetails['file_info']['backup_filename'])) {
            throw new Exception('Invalid backup name');
        }

        $result = $this->request(
            'website/upload',
            Requests::POST,
            [],
            [
                'backup_details' => $backupDetails,
                'backup_type'    => $this->backupType,
            ]
        );
        if (!$result['success']) {
            DupLog::traceObject('Failed to start upload to website', $result);
            throw new Exception($result['message']);
        }

        return $result['data'];
    }

    /**
     * Complete upload
     *
     * @param string $uuid       The uuid
     * @param string $etag       The etag
     * @param int    $maxBackups The max backups
     *
     * @return bool
     */
    public function completeUpload($uuid, $etag, $maxBackups)
    {
        $result = $this->request(
            'website/upload/' . $uuid,
            Requests::POST,
            [],
            [
                'etag'        => $etag,
                'max_backups' => $maxBackups,
            ],
            ['timeout' => self::BACKUP_REQUEST_TIMEOUT]
        );

        if (!$result['success']) {
            DupLog::traceObject('Failed to complete upload to website', $result);
            throw new Exception($result['message']);
        }

        return $result['success'];
    }

    /**
     * Mark an upload as failed on the remote server
     *
     * @param string $backupName The backup name to cancel
     *
     * @return bool True on success
     * @throws Exception If the request fails or backup name is invalid
     */
    public function failUploadByName(string $backupName): bool
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        if (empty($backupName)) {
            throw new Exception('Backup name is required');
        }

        $result = $this->request(
            'website/backups/' . rawurlencode($backupName) . '/fail',
            Requests::POST,
            [],
            [],
            ['timeout' => self::BACKUP_REQUEST_TIMEOUT]
        );

        if (!$result['success']) {
            DupLog::traceObject('Failed to fail upload for backup ' . $backupName, $result);
            throw new Exception($result['message']);
        }

        return true;
    }

    /**
     * Mark an upload as canceled on the remote server
     *
     * @param string $backupName The backup name to cancel
     *
     * @return bool True on success
     * @throws Exception If the request fails or backup name is invalid
     */
    public function cancelUploadByName(string $backupName): bool
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        if (empty($backupName)) {
            throw new Exception('Backup name is required');
        }

        $result = $this->request(
            'website/backups/' . rawurlencode($backupName) . '/cancel',
            Requests::POST,
            [],
            [],
            ['timeout' => self::BACKUP_REQUEST_TIMEOUT]
        );

        if (!$result['success']) {
            DupLog::traceObject('Failed to cancel upload for backup ' . $backupName, $result);
            throw new Exception($result['message']);
        }

        return true;
    }

    /**
     * Mark an upload as failed on the remote server
     *
     * @param string $uuid The backup upload UUID to cancel
     *
     * @return bool True on success
     * @throws Exception If the request fails or UUID/token invalid
     */
    public function failUpload(string $uuid): bool
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        if (empty($uuid)) {
            throw new Exception('Upload UUID is required');
        }

        $result = $this->request(
            'website/upload/' . $uuid . '/fail',
            Requests::POST,
            [],
            [],
            ['timeout' => self::BACKUP_REQUEST_TIMEOUT]
        );

        if (!$result['success']) {
            DupLog::traceObject('Failed to fail upload ' . $uuid, $result);
            throw new Exception($result['message']);
        }

        return true;
    }

    /**
     * Cancel an ongoing upload on the remote server
     *
     * @param string $uuid The backup upload UUID to cancel
     *
     * @return bool True on success
     * @throws Exception If the request fails or UUID/token invalid
     */
    public function cancelUpload(string $uuid): bool
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        if (empty($uuid)) {
            throw new Exception('Upload UUID is required');
        }

        $result = $this->request(
            'website/upload/' . $uuid . '/cancel',
            Requests::POST,
            [],
            [],
            ['timeout' => self::BACKUP_REQUEST_TIMEOUT]
        );

        if (!$result['success']) {
            DupLog::traceObject('Failed to cancel upload ' . $uuid, $result);
            throw new Exception($result['message']);
        }

        return true;
    }

    /**
     * Get the processing state of an upload.
     *
     * A 404 is not an error: the cloud drops the record of a backup whose
     * processing failed, so it is reported as the not-found state.
     *
     * @param string $uploadUuid The backup upload UUID
     *
     * @return array{state:string,percent:?int,stage:?string,filesProcessed:?int,totalFiles:?int}
     *
     * @throws Exception If the token or UUID is missing, or the request fails with a non-404 error.
     */
    public function getUploadStatus(string $uploadUuid): array
    {
        if (strlen($this->storageToken) === 0) {
            throw new Exception('No storage token provided', 401);
        }

        if (strlen($uploadUuid) === 0) {
            throw new Exception('Upload UUID is required');
        }

        $result = $this->request('website/upload/' . rawurlencode($uploadUuid) . '/status');

        if (!$result['success']) {
            if ($result['httpCode'] === 404) {
                return self::normalizeUploadStatus(['status' => self::UPLOAD_STATE_NOT_FOUND]);
            }

            DupLog::trace('Failed to get upload status for ' . $uploadUuid . ': ' . $result['message']);
            throw new Exception($result['message'], $result['httpCode']);
        }

        return self::normalizeUploadStatus($result['data']);
    }

    /**
     * Normalize an upload status payload to the plugin-internal shape.
     *
     * @param array<string,mixed> $data The response data payload
     *
     * @return array{state:string,percent:?int,stage:?string,filesProcessed:?int,totalFiles:?int}
     */
    private static function normalizeUploadStatus(array $data): array
    {
        $progress = isset($data['progress']) && is_array($data['progress']) ? $data['progress'] : [];

        return [
            'state'          => isset($data['status']) ? (string) $data['status'] : self::UPLOAD_STATE_QUEUED,
            'percent'        => isset($progress['percent']) ? (int) $progress['percent'] : null,
            'stage'          => isset($progress['stage']) ? (string) $progress['stage'] : null,
            'filesProcessed' => isset($progress['files_processed']) ? (int) $progress['files_processed'] : null,
            'totalFiles'     => isset($progress['total_files']) ? (int) $progress['total_files'] : null,
        ];
    }

    /**
     * Upload the contents of the file
     *
     * @param string $path     The path of the file
     * @param string $fileType The file type
     * @param string $saveAs   The save as name
     *
     * @return bool
     */
    public function directUpload($path, $fileType, $saveAs = '')
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception("File does not exist or is not readable: $path");
        }

        $size = filesize($path);
        if ($size === false) {
            throw new Exception("File is empty: $path");
        }

        $fileName = strlen($saveAs) > 0 ? $saveAs : basename($path);
        if (!self::isAllowedFileName($fileName)) {
            throw new Exception('The provided file name is invalid: ' . $fileName);
        }

        DupLog::infoTrace("directUpload: file_name={$fileName} file_type={$fileType} size={$size}");

        $result = $this->request(
            'website/direct_upload',
            Requests::POST,
            [],
            [
                'file_name'   => $fileName,
                'file_type'   => $fileType,
                'size'        => $size,
                'backup_type' => $this->backupType,
            ]
        );

        if (!$result['success']) {
            DupLog::infoTrace(
                "directUpload failed: httpCode={$result['httpCode']} message={$result['message']} data=" . print_r($result['data'], true)
            );
            DupLog::traceObject('Failed to get direct upload URL', $result);
            throw new Exception($result['message']);
        }

        if (!isset($result['data']['url'])) {
            DupLog::traceObject('No url returned', $result);
            throw new Exception('No url returned');
        }

        if (!isset($result['data']['headers'])) {
            DupLog::traceObject('No headers returned', $result);
            throw new Exception('No headers returned');
        }

        $url = $result['data']['url'];
        if (($content = file_get_contents($path)) === false) {
            throw new Exception("Can't read file: $path");
        }

        $response = Requests::request(
            $url,
            [],
            $content, // @phpstan-ignore-line
            Requests::PUT,
            ['timeout' => 300]
        );
        if (!$response->success) {
            DupLog::traceObject('Failed to upload file to pre-signed URL', $response);
            throw new Exception('Failed to upload file to pre-signed URL');
        }

        return $response->success;
    }

    /**
     * Upload the contents of the file
     *
     * @param string               $path          The path of the file
     * @param array<stirng, mixed> $backupDetails The backup details
     * @param int                  $maxBackups    Max backups
     * @param string               $uploadUuid    Set to the upload UUID assigned by the cloud
     *
     * @return bool
     */
    public function upload($path, $backupDetails, $maxBackups, string &$uploadUuid = '')
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception("File does not exist or is not readable: $path");
        }

        if (($content = @file_get_contents($path)) === false) {
            throw new Exception("Could not read file $path");
        }

        $uploadData = $this->startUpload($backupDetails);
        if (empty($uploadData) || !isset($uploadData['url'], $uploadData['uuid'])) {
            DupLog::traceObject('Failed to start upload', $uploadData);
            throw new Exception('Could not start upload');
        }

        $uploadUuid = (string) $uploadData['uuid'];

        $response = Requests::put(
            $uploadData['url'],
            [],
            $content,
            ['timeout' => 300]
        );

        if ($response->status_code !== 200) {
            DupLog::traceObject('Failed to upload file to pre-signed URL', $response);
            throw new Exception('Failed to upload file to pre-signed URL');
        }

        return $this->completeUpload($uploadData['uuid'], md5($content), $maxBackups);
    }

    /**
     * Start multipart upload
     *
     * @param array<string, mixed> $backupDetails The backup details
     *
     * @return array{uuid: string, urls: array<int, string>} URLs indexed by part number (starting from 1)
     */
    public function startMultipart($backupDetails): array
    {
        if (!isset($backupDetails['file_info']['backup_filename'])) {
            throw new Exception('Invalid backup details');
        }
        if (!preg_match(DUPLICATOR_ARCHIVE_REGEX_PATTERN, $backupDetails['file_info']['backup_filename'])) {
            throw new Exception("Invalid backup name: " . $backupDetails['file_info']['backup_filename']);
        }

        $result = $this->request(
            'website/multipart',
            Requests::POST,
            [],
            [
                'range'          => '1-' . self::MAX_PART_REQUEST_COUNT,
                'backup_type'    => $this->backupType,
                'backup_details' => $backupDetails,
            ]
        );

        if (!$result['success']) {
            DupLog::traceObject('Failed to start multipart upload', $result);
            throw new Exception($result['message']);
        }

        // Check if uuid and urls are set and not empty
        if (empty($result['data']['uuid']) || empty($result['data']['urls'])) {
            DupLog::traceObject('UUID or URLs are not set or empty', $result);
            throw new Exception('UUID or URLs are not set or empty');
        }

        return [
            'uuid' => $result['data']['uuid'],
            'urls' => self::reIndexArray($result['data']['urls'], 1),
        ];
    }

    /**
     * Upload Part
     *
     * @param string $url     The url
     * @param mixed  $content The content
     *
     * @return void
     *
     * @throws PresignedUrlExpiredException If the presigned URL has expired
     * @throws Exception If the upload fails for other reasons
     */
    public function uploadPart($url, $content): void
    {
        $response = Requests::request(
            $url,
            [],
            $content,
            Requests::PUT,
            ['timeout' => 300]
        );

        if ($response->status_code !== 200) {
            DupLog::traceObject('Failed to upload part', $response);
            if ($response->status_code === 403) {
                // most likely an expired URL
                throw new PresignedUrlExpiredException('Presigned URL has expired');
            }

            throw new Exception('Request to upload part failed');
        }

        if ($response->success === false) {
            throw new Exception('Request to upload part failed');
        }
    }

    /**
     * Get presigned URLs for uploading parts
     *
     * @param string $uuid            The uuid
     * @param int    $startPartNumber The start part number (1-indexed)
     *
     * @return array<int, string> URLs indexed by part number (starting from $startPartNumber)
     */
    public function getPartUrls($uuid, int $startPartNumber): array
    {
        $result = $this->request(
            'website/multipart/' . $uuid,
            Requests::GET,
            [],
            ['range' => $startPartNumber . '-' . ($startPartNumber + self::MAX_PART_REQUEST_COUNT - 1)]
        );

        if (!$result['success']) {
            DupLog::traceObject('Failed to get multipart upload URLs', $result);
            throw new Exception($result['message']);
        }

        if (empty($result['data']['urls'])) {
            DupLog::traceObject('URLs are not set or empty', $result);
            throw new Exception('URLs are not set or empty');
        }

        if (count($result['data']['urls']) !== self::MAX_PART_REQUEST_COUNT) {
            DupLog::traceObject('URLs count is not equal to the requested range', $result);
            throw new Exception('URLs count is not equal to the requested range');
        }

        return self::reIndexArray($result['data']['urls'], $startPartNumber);
    }

    /**
     * Complete upload
     *
     * @param string                                          $uuid       The uuid
     * @param array<array{'ETag': string, 'PartNumber': int}> $parts      The parts
     * @param int                                             $maxBackups The max backups
     *
     * @return bool
     * @throws Exception If encoding parts as JSON fails
     */
    public function completeMultipart($uuid, $parts, $maxBackups = 0)
    {
        // Encode parts as JSON string to avoid max_input_vars limit
        $partsJson = json_encode($parts);
        if ($partsJson === false) {
            throw new Exception('Failed to encode parts as JSON');
        }

        DupLog::info("Sending complete request with parts: " . $partsJson);
        $result = $this->request(
            'website/multipart/' . $uuid,
            Requests::POST,
            [],
            [
                'parts'       => $partsJson,
                'max_backups' => $maxBackups,
            ],
            ['timeout' => self::BACKUP_REQUEST_TIMEOUT]
        );
        if (!$result['success']) {
            DupLog::traceObject('Failed to complete multipart upload', $result);
            throw new Exception($result['message']);
        }

        return $result['success'];
    }

    /**
     * Upload Part
     *
     * @param string $url    The url
     * @param int    $offset The offset
     * @param int    $length The length, if < 0 download the whole file
     *
     * @return string|false The response body or false on failure
     */
    public function downloadChunk($url, $offset, $length = -1)
    {
        $headers = [];
        if ($length > 0) {
            $headers['Range'] = 'bytes=' . $offset . '-' . ($offset + $length - 1);
        }

        $response = Requests::get(
            $url,
            $headers,
            ['timeout' => 300]
        );

        if ($length < 0 && $response->status_code !== 200) {
            DupLog::traceObject('Error downloading whole file', $response);
            return false;
        } elseif ($length > 0 && $response->status_code !== 206) {
            DupLog::traceObject('Error downloading chunk', $response);
            return false;
        }

        return $response->body;
    }

    /**
     * Get the auth url
     *
     * @param string $url The url
     *
     * @return string
     * @throws \Exception
     */
    public static function getAuthUrl($url): string
    {
        if (empty($url)) {
            throw new Exception('URL is required');
        }

        return DUPLICATOR_CLOUD_HOST . self::AUTH_PATH . base64_encode($url);
    }

    /**
     * Get the API URL
     *
     * @param string $path The path
     *
     * @return string
     */
    private static function getApiUrl(string $path = ''): string
    {
        return DUPLICATOR_CLOUD_HOST . self::API_PATH . trim($path, '/');
    }

    /**
     * Authenticate site using compound token (license key + sanctum token)
     *
     * @param string $compoundToken  The compound token in format: {license_key}.{sanctum_token}
     * @param string $siteIdentifier The unique site identifier
     *
     * @return array{token:string,website:array{id:int,name:string,url:string},storage:array{id:int,name:string,total_space:int,used_space:int}}
     * @throws Exception If the authentication fails
     */
    public function authenticateSite(string $compoundToken, string $siteIdentifier): array
    {
        DupLog::trace('AUTHENTICATE SITE !!!!');
        // Parse compound token
        $parts = explode('.', $compoundToken, 2);
        if (count($parts) !== 2) {
            throw new Exception(__('Invalid token format. Please ensure you copied the complete token.', 'duplicator'));
        }

        $licenseKey   = $parts[0];
        $sanctumToken = $parts[1];

        if (empty($licenseKey) || empty($sanctumToken)) {
            throw new Exception(__('Invalid token. Both license key and authentication token are required.', 'duplicator'));
        }

        // Get site info for the request
        $siteUrl  = get_home_url();
        $siteName = get_bloginfo('name');
        if (empty($siteName)) {
            $siteName = parse_url($siteUrl, PHP_URL_HOST) ?: 'WordPress Site';
        }

        $requestData = [
            'license_key'     => $licenseKey,
            'sanctum_token'   => $sanctumToken,
            'site_identifier' => $siteIdentifier,
            'name'            => $siteName,
            'url'             => $siteUrl,
            'backup_type'     => self::BACKUP_TYPE_STANDARD, // Default to standard
        ];

        // Call Laravel authenticate-site endpoint. The Authorization header is
        // skipped automatically because $this->storageToken is still empty at
        // this point — setStorageToken() runs below once the exchange succeeds.
        $result = $this->request(
            'auth/authenticate-site',
            Requests::POST,
            [],
            $requestData
        );

        if (!$result['success']) {
            DupLog::trace('Failed to authenticate site: ' . $result['message']);
            throw new Exception($result['message']);
        }

        // Validate response has required fields
        if (empty($result['data']['token'])) {
            DupLog::traceObject('No token received from authenticate-site', $result);
            throw new Exception(__('Authentication succeeded but no storage token was provided.', 'duplicator'));
        }

        // Set the storage token for future API calls
        $this->setStorageToken($result['data']['token']);

        $redactedData          = $result['data'];
        $redactedData['token'] = SnapString::obfuscateString($result['data']['token'], 5);
        DupLog::traceObject('AUTH TOKEN DATA ', $redactedData);

        // Return the response data directly
        return $result['data'];
    }

    /**
     * Downloads a backup from the cloud storage
     *
     * @param string $filename The name of the file to download
     *
     * @return array{download_url:string, size:int, expires_at:int} The download data
     * @throws Exception If the request fails or backup name is invalid
     */
    public function getDownloadData(string $filename): array
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        if (!self::isAllowedFileName($filename)) {
            throw new Exception('The provided file name is invalid: ' . $filename);
        }

        $result = $this->request(
            'backups/' . $filename . '/download',
            Requests::GET,
            [],
            ['backup_type' => $this->backupType]
        );

        if (!$result['success']) {
            DupLog::trace('Failed to get download data for backup ' . $filename . ' ' . $result['message']);
            throw new Exception($result['message']);
        }

        return $result['data'];
    }

    /**
     * Ask the server to generate a downloadable snapshot for a backup.
     *
     * Idempotent — repeated calls for the same filename do not double-queue.
     * Returns 'pending' when the job has been queued and 'ready' when a snapshot
     * is already on disk.
     *
     * @param string $filename Archive filename
     *
     * @return array{status:string}
     *
     * @throws Exception If the token is missing, the filename is invalid, or the
     *                   server returns a non-success response.
     */
    public function requestSnapshot(string $filename): array
    {
        if (strlen($this->storageToken) === 0) {
            throw new Exception('No storage token provided', 401);
        }

        if (!self::isAllowedFileName($filename)) {
            throw new Exception('The provided file name is invalid: ' . $filename);
        }

        $result = $this->request(
            sprintf('backups/%s/request-snapshot', urlencode($filename)),
            Requests::POST,
            [],
            [],
            ['timeout' => 30]
        );

        if (!$result['success']) {
            $message = strlen($result['message']) > 0
                ? $result['message']
                : sprintf('Failed to request snapshot for %s', $filename);
            DupLog::trace('DupCloudClient::requestSnapshot failed: HTTP ' . $result['httpCode'] . ' ' . $message);
            throw new Exception($message);
        }

        return [
            'status' => (string) ($result['data']['status'] ?? self::SNAPSHOT_STATUS_PENDING),
        ];
    }

    /**
     * Get the latest snapshot metadata (index download URL + metadata).
     *
     * Returns the response data even on a 404 (no previous completed snapshot),
     * because that body may still carry lifecycle flags such as
     * can_start_new_backup; it is empty only when the server sends no
     * data. Other failures bubble up as exceptions so callers can distinguish
     * "nothing to report" from "server is broken".
     *
     * @return array<string, mixed> Snapshot data (may be flag-only on a 404) or empty array
     *
     * @throws Exception If the token is missing or the request fails with a non-404 error.
     */
    public function getSnapshotMeta(): array
    {
        if (strlen($this->storageToken) === 0) {
            throw new Exception('No storage token provided', 401);
        }

        $result = $this->request(
            'backups/snapshot',
            Requests::GET
        );

        if (!$result['success']) {
            if ($result['httpCode'] === 404) {
                // A 404 body may still carry lifecycle flags (e.g.
                // can_start_new_backup) when a website's first-ever backup
                // is still processing with no completed snapshot to return, so the
                // data must be threaded through rather than discarded.
                return is_array($result['data']) ? $result['data'] : [];
            }

            DupLog::trace('Failed to get snapshot: ' . $result['message']);
            throw new Exception($result['message']);
        }

        return $result['data'];
    }

    /**
     * Reset the incremental backup chain: deletes every incremental backup on
     * the cloud so the next backup starts a fresh chain.
     *
     * @return int Number of incremental backups deleted
     *
     * @throws Exception If the token is missing or the server rejects the reset.
     */
    public function resetIncrementalChain(): int
    {
        if (strlen($this->storageToken) === 0) {
            throw new Exception('No storage token provided', 401);
        }

        $result = $this->request(
            'backups/incremental/reset',
            Requests::POST,
            [],
            [],
            ['timeout' => 30]
        );

        if (!$result['success']) {
            DupLog::trace('Failed to reset incremental chain: ' . $result['message']);
            throw new Exception($result['message']);
        }

        return (int) ($result['data']['backups_deleted'] ?? 0);
    }

    /**
     * Deletes all backups off the website from the cloud storage
     *
     * @return bool Returns true if deletion was successful
     * @throws Exception If the request fails or backup name is invalid
     */
    public function deleteAllBackups(): bool
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        $result = $this->request(
            'backups',
            Requests::DELETE
        );

        if (!$result['success']) {
            DupLog::trace('Failed to delete backup all backups ' . $result['message']);
            throw new Exception($result['message']);
        }

        return true;
    }

    /**
     * Delete a backup from the cloud storage
     *
     * @param string $filename The backup name to delete
     *
     * @return bool Returns true if deletion was successful
     * @throws Exception If the request fails or backup name is invalid
     */
    public function deleteFile(string $filename): bool
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        if (!self::isAllowedFileName($filename)) {
            throw new Exception('The provided backup name is invalid');
        }

        $result = $this->request(
            'backups/' . urlencode($filename),
            Requests::DELETE
        );

        if (!$result['success']) {
            DupLog::trace('Failed to delete backup ' . $filename . ' ' . $result['message']);
            throw new Exception($result['message']);
        }

        return true;
    }

    /**
     * Get list of backups from the cloud storage
     *
     * @return ?StoragePathInfo[] Returns array of backup information
     *
     * @throws Exception If the request fails or authentication is invalid
     */
    public function getFileList(): ?array
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        $result = $this->request(
            'backups',
            Requests::GET
        );

        if (!$result['success']) {
            DupLog::trace('Failed to get backup list ' . $result['message']);
            throw new Exception($result['message']);
        }

        $infoList = [];
        foreach ($result['data'] as $key => $value) {
            $info           = new StoragePathInfo();
            $info->path     = $value['path'];
            $info->exists   = $value['exists'];
            $info->isDir    = $value['isDir'];
            $info->size     = $value['size'];
            $info->created  = $value['created'];
            $info->modified = $value['modified'];

            $infoList[$key] = $info;
        }

        return $infoList;
    }

    /**
     * Get detailed information about a specific backup
     *
     * @param string $filename The file name to get info for
     *
     * @return StoragePathInfo Returns detailed backup information
     *
     * @throws Exception If the request fails or backup name is invalid
     */
    public function getFileInfo(string $filename): StoragePathInfo
    {
        if (empty($this->storageToken)) {
            throw new Exception('No storage token provided', 401);
        }

        // Validate backup name is a single filename
        if (!self::isAllowedFileName($filename)) {
            throw new Exception('The provided file name is invalid: ' . $filename);
        }

        $result = $this->request(
            'backups/' . urlencode($filename),
            Requests::GET,
            [],
            ['backup_type' => $this->backupType]
        );

        if (!$result['success']) {
            DupLog::trace('Failed to get backup info ' . $filename . ' ' . $result['message']);
            $emptyFile       = new StoragePathInfo();
            $emptyFile->path = $filename;
            return $emptyFile;
        }

        $info           = new StoragePathInfo();
        $info->path     = $result['data']['path'];
        $info->exists   = $result['data']['exists'];
        $info->isDir    = $result['data']['isDir'];
        $info->size     = $result['data']['size'];
        $info->created  = $result['data']['created'];
        $info->modified = $result['data']['modified'];

        return $info;
    }

    /**
     * Duplicator Cloud Storage only allows file operations on backup files
     *
     * @param string $path The path to check
     *
     * @return bool
     */
    public static function isAllowedFileName(string $path): bool
    {
        $filename = ltrim(rtrim($path, '\\/'), '\\/');

        if (DupCloudStorageAdapter::getDirectUploadFileType($filename) !== null) {
            return true;
        }

        return preg_match(DUPLICATOR_GEN_FILE_REGEX_PATTERN, $filename) === 1;
    }

    /**
     * Duplicator Cloud Storage only allows directory operations on the root directory.
     *
     * @param string $path The path to check
     *
     * @return bool
     */
    public static function isRootDir(string $path): bool
    {
        return $path === '/' || $path === '';
    }

    /**
     * Reindex array starting from $startIndex
     *
     * @param array<mixed> $array      Array to reindex
     * @param int          $startIndex Starting index
     *
     * @return array<int, mixed> Reindexed array
     */
    private static function reIndexArray(array $array, int $startIndex): array
    {
        $result = [];
        foreach (array_values($array) as $index => $value) {
            $result[$startIndex + $index] = $value;
        }
        return $result;
    }
}
