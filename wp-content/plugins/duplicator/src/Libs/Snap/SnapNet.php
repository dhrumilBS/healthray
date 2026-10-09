<?php

namespace Duplicator\Libs\Snap;

use Exception;

class SnapNet
{
    /**
     * @param string $filepath     path to file to be downloaded
     * @param string $downloadName name to be downloaded as
     * @param int    $bufferSize   file chunks to be served
     * @param bool   $limitRate    if set to true the download rate will be limited to $bufferSize/seconds
     *
     * @return never
     */
    public static function serveFileForDownload($filepath, $downloadName, $bufferSize = 0, $limitRate = false): void
    {
        // Process download
        if (!file_exists($filepath)) {
            throw new Exception(__("File does not exist!", 'duplicator'));
        }

        if (!is_file($filepath)) {
            $msg = sprintf(__("'%s' is not a file!", 'duplicator'), $filepath);
            throw new Exception($msg);
        }

        // Clean output buffers
        SnapUtil::obCleanAll(false);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush(); // Flush system output buffer

        if ($bufferSize <= 0) {
            readfile($filepath);
            exit;
        }

        $fp = @fopen($filepath, 'r');
        if (!is_resource($fp)) {
            throw new Exception('Fail to open the file ' . $filepath);
        }

        while (!feof($fp) && ($data = fread($fp, $bufferSize)) !== false) {
            echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            if ($limitRate) {
                sleep(1);
            }
        }
        @fclose($fp);
        exit;
    }

    /**
     * Server content for download
     *
     * @param string $content      content to be downloaded
     * @param string $downloadName name to be downloaded as
     *
     * @return never
     */
    public static function serveContentForDownload($content, $downloadName): void
    {
        // Process download
        if (empty($content)) {
            throw new Exception(__("Content is empty!", 'duplicator'));
        }

        // Clean output buffers
        SnapUtil::obCleanAll(false);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($content));
        flush(); // Flush system output buffer

        echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }

    /**
     * Serve error 500 and exit
     *
     * @return never
     */
    public static function serverError500(): void
    {
        header('HTTP/1.1 500 Internal Server Error');
        exit;
    }

    /**
     * Get server IP
     *
     * @return string Return server IP or empty string if not found
     */
    public static function getServerIP(): string
    {
        $serverAddr = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SERVER_ADDR', '');
        $serverName = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SERVER_NAME', '');
        if ($serverAddr !== '') {
            $ip = $serverAddr;
        } elseif (strlen($serverName) > 0 && function_exists('gethostbyname')) {
            $ip = gethostbyname($serverName);
        } else {
            $ip = '';
        }
        return $ip;
    }

    /**
     * Get the IP of a client machine
     *
     * @return string IP of the client machine or empty string if not found
     */
    public static function getClientIP(): string
    {
        $result = '';
        if (($result = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', '')) !== '') {
            return $result;
        }
        if (($result = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'REMOTE_ADDR', '')) !== '') {
            return $result;
        }
        if (($result = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_CLIENT_IP', '')) !== '') {
            return $result;
        }
        return '';
    }

    /**
     * Download a remote file directly to disk via streaming (no memory buffering)
     *
     * @param string $url      The remote URL to download from
     * @param string $destPath Local file path to write to
     * @param int    $timeout  Request timeout in seconds
     *
     * @return void
     * @throws Exception If the download or write fails
     */
    public static function downloadFileStreamed(string $url, string $destPath, int $timeout = 300): void
    {
        $response = wp_remote_get($url, [
            'timeout'  => $timeout,
            'stream'   => true,
            'filename' => $destPath,
        ]);

        if (is_wp_error($response)) {
            SnapIO::unlink($destPath);
            throw new Exception('Failed to download file: ' . $response->get_error_message());
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            SnapIO::unlink($destPath);
            throw new Exception('Failed to download file (HTTP ' . $code . ')');
        }
    }

    /**
     * Get outbound IP
     *
     * @return string Return outbound IP or empty string if not found
     */
    public static function getOutboundIP(): string
    {
        $context = stream_context_create([
            'http' =>
            ['timeout' => 15],
        ]);

        $outboundIP = file_get_contents('https://checkip.amazonaws.com', false, $context);

        if ($outboundIP !== false) {
            // Make sure it's a properly formatted IP address
            if (preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $outboundIP) !== 1) {
                $outboundIP = false;
            }
        }

        return $outboundIP !== false ? trim($outboundIP) : '';
    }
}
