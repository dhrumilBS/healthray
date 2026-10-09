<?php

namespace Duplicator\Libs\Shell;

class ShellOutput
{
    /** @var string[] */
    private $output = [];
    private int $code;

    /**
     * Initialise the Shell Output Response with real output
     *
     * @param null|string|string[] $output Shell Output Lines
     * @param int                  $code   Shell Output return code
     */
    public function __construct($output, $code)
    {
        if (is_scalar($output) || is_null($output)) {
            $output = (string) $output;
            if (strlen($output) == 0) {
                $output = [];
            } elseif (($output = preg_split("/(\r\n|\n|\r)/", (string) $output)) === false) {
                $output = [];
            }
        }

        $this->output = self::formatOutput($output);
        $this->code   = (int) $code;
    }

    /**
     * Format the Shell Output
     *
     * @param string[] $output Initial Shell Output
     *
     * @return string[] return Array of formatted Shell Output Lines
     */
    private static function formatOutput($output)
    {
        foreach ($output as $key => $line) {
            $line = preg_replace('~\r\n?~', "\n", $line);
            if (strlen($line) == 0 || substr($line, -1) !== "\n") {
                $line .= "\n";
            }
            $output[$key] = $line;
        }
        return $output;
    }

    /**
     * Get Shell Output as a string
     *
     * @param int $limitLines 0 returns the complete output, a positive value only
     *                        the first N lines, a negative value only the last N lines
     *
     * @return string Shell output as a string
     */
    public function getOutputAsString(int $limitLines = 0): string
    {
        if ($limitLines > 0) {
            $lines = array_slice($this->output, 0, $limitLines);
        } elseif ($limitLines < 0) {
            $lines = array_slice($this->output, $limitLines);
        } else {
            $lines = $this->output;
        }

        return implode('', $lines);
    }

    /**
     * Get complete Shell Output
     *
     * @return string[] complete Shell output as array lines
     */
    public function getArrayWithAllOutputLines()
    {
        return $this->output;
    }


    /**
     * Get complete Shell output return code
     *
     * @return integer shell output return code
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Check if Shell Output response is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return (strlen(trim($this->getOutputAsString())) == 0);
    }
}
