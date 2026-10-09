<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Requirements;

use Closure;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Throwable;

/**
 * Atomic environment fact, independent from the plugin configuration.
 *
 * The check callable wraps an existing utility (e.g. SnapUtil::isZlibEnabled())
 * and is evaluated lazily, at most once per request.
 */
class Requirement
{
    /** @var string */
    private string $id;
    /** @var string|Closure(): string resolved to a string at the first getLabel() call */
    private $label;
    /** @var callable(): bool */
    private $check;
    /** @var string|Closure(): string resolved to a string at the first getFailMessage() call */
    private $failMessage;
    /** @var string|Closure(): string resolved to a string at the first getFixHint() call */
    private $fixHint;
    /** @var string */
    private string $docUrl;
    /** @var ?bool cached check result, null if not evaluated yet */
    private ?bool $result = null;

    /**
     * Class constructor
     *
     * @param string                   $id          Unique requirement id
     * @param string|Closure(): string $label       Human readable label
     * @param callable(): bool         $check       Environment check, evaluated lazily and cached per request
     * @param string|Closure(): string $failMessage Message shown when the check fails
     * @param string|Closure(): string $fixHint     Optional hint on how to fix the failure (can contain HTML).
     *                                              A Closure is resolved lazily at the first getFixHint() call,
     *                                              so the hint can depend on the admin page context (rendered
     *                                              templates, action URLs) not available at registration time.
     * @param string                   $docUrl      Optional documentation URL
     */
    public function __construct(
        string $id,
        $label,
        callable $check,
        $failMessage,
        $fixHint = '',
        string $docUrl = ''
    ) {
        if (strlen($id) === 0) {
            throw new DupliException(
                'Backup requirement id cannot be empty.',
                DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
                __('The backup requirements configuration is invalid. Check the backup log for details.', 'duplicator')
            );
        }
        $this->id          = $id;
        $this->label       = $label;
        $this->check       = $check;
        $this->failMessage = $failMessage;
        $this->fixHint     = $fixHint;
        $this->docUrl      = $docUrl;
    }

    /**
     * Get the requirement id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the human readable label
     *
     * @return string
     */
    public function getLabel(): string
    {
        if ($this->label instanceof Closure) {
            $this->label = ($this->label)();
        }
        return $this->label;
    }

    /**
     * True if the environment satisfies the requirement.
     * The check runs at most once per request, the result is cached.
     * A check that throws is fail-safe: the requirement is considered not met
     * and the failure is logged, it never propagates to the caller.
     *
     * @return bool
     */
    public function isMet(): bool
    {
        if ($this->result === null) {
            try {
                $this->result = (bool) call_user_func($this->check);
            } catch (Throwable $e) {
                $this->result = false;
                DupLog::trace('Backup requirement check failed. Requirement: ' . $this->id . ' Error: ' . $e->getMessage());
            }
        }
        return $this->result;
    }

    /**
     * Clear the cached check result: the next isMet() call re-runs the check.
     * For the few flows that change the environment the check reads within
     * the same request (see OptionsManager::resetRequirementResults()).
     *
     * @return void
     */
    public function resetResult(): void
    {
        $this->result = null;
    }

    /**
     * Get the failure message
     *
     * @return string
     */
    public function getFailMessage(): string
    {
        if ($this->failMessage instanceof Closure) {
            $this->failMessage = ($this->failMessage)();
        }
        return $this->failMessage;
    }

    /**
     * Get the fix hint, resolving and caching it at the first call when it
     * was registered as a Closure
     *
     * @return string
     */
    public function getFixHint(): string
    {
        if ($this->fixHint instanceof Closure) {
            $this->fixHint = ($this->fixHint)();
        }
        return $this->fixHint;
    }

    /**
     * Get the documentation URL
     *
     * @return string
     */
    public function getDocUrl(): string
    {
        return $this->docUrl;
    }
}
