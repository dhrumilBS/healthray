<?php

declare(strict_types=1);

namespace Duplicator\Models;

use InvalidArgumentException;
use Throwable;

/**
 * A user-facing error message for a detected problem, optionally
 * carrying an automated remedy.
 *
 * Not tied to backup builds: any plugin subsystem can persist one to
 * surface a failure to the user.
 *
 * @phpstan-type FixArrayData array{
 *     key:string,
 *     type:string,
 *     errorText:string,
 *     description:string,
 *     suggestionText:string,
 *     actionKey:?string,
 *     payload:array<string,mixed>,
 *     title:string,
 *     troubleshooting:string[],
 *     codeSnippet:string,
 *     docUrl:string,
 *     docLabel:string,
 *     activityLogLink:bool,
 *     activityLogId:int,
 *     autoTuneSuggestion:bool
 * }
 * @phpstan-type FixViewData array{
 *     type:string,
 *     errorText:string,
 *     description:string,
 *     suggestionText:string,
 *     title:string,
 *     troubleshooting:string[],
 *     codeSnippet:string,
 *     docUrl:string,
 *     docLabel:string,
 *     activityLogLink:bool,
 *     activityLogId:int,
 *     autoTuneSuggestion:bool
 * }
 */
final class Fix
{
    const TYPE_NOTICE = 'notice';
    const TYPE_ACTION = 'action';

    const ACTION_UPDATE_GLOBAL  = 'global.update';
    const ACTION_SET_BASIC_AUTH = 'basic_auth.configure';

    private const DEFAULT_DATA = [
        'key'                => '',
        'type'               => self::TYPE_NOTICE,
        'errorText'          => '',
        'description'        => '',
        'suggestionText'     => '',
        'actionKey'          => null,
        'payload'            => [],
        'title'              => '',
        'troubleshooting'    => [],
        'codeSnippet'        => '',
        'docUrl'             => '',
        'docLabel'           => '',
        'activityLogLink'    => true,
        'activityLogId'      => 0,
        'autoTuneSuggestion' => false,
    ];

    private string $key            = '';
    private string $type           = self::TYPE_NOTICE;
    private string $errorText      = '';
    private string $suggestionText = '';
    private ?string $actionKey     = null;

    /** @var array<string, mixed> */
    private array $payload = [];

    /** @var string Extended explanation of the problem, HTML allowed */
    private string $description = '';

    /** @var string Notice box title; empty means the renderer default */
    private string $title = '';

    /** @var string[] Resolution hints, HTML allowed */
    private array $troubleshooting = [];

    /** @var string Plain-text configuration snippet */
    private string $codeSnippet = '';

    /** @var string Documentation article URL */
    private string $docUrl = '';

    /** @var string Documentation article label */
    private string $docLabel = '';

    /** @var bool Whether the notice links to the Activity Log */
    private bool $activityLogLink = true;

    /** @var int Related Activity Log event id, 0 when none */
    private int $activityLogId = 0;

    /** @var bool Whether the troubleshooting suggests running AutoTune */
    private bool $autoTuneSuggestion = false;

    /**
     * @param string               $key            Stable fix identifier
     * @param string               $type           One of self::TYPE_*
     * @param string               $errorText      Detected problem
     * @param string               $suggestionText What the automated action does, empty for notices
     * @param ?string              $actionKey      Action identifier
     * @param array<string, mixed> $payload        Action payload
     */
    private function __construct(
        string $key,
        string $type,
        string $errorText,
        string $suggestionText,
        ?string $actionKey = null,
        array $payload = []
    ) {
        $this->key            = $key;
        $this->type           = $type;
        $this->errorText      = $errorText;
        $this->suggestionText = $suggestionText;
        $this->actionKey      = $actionKey;
        $this->payload        = $payload;

        $this->validate();
    }

    /**
     * Create a non-actionable notice.
     *
     * @param string   $key             Stable fix identifier
     * @param string   $errorText       Detected problem
     * @param string[] $troubleshooting Resolution hints, HTML allowed
     *
     * @return self
     */
    public static function notice(string $key, string $errorText, array $troubleshooting = []): self
    {
        return (new self($key, self::TYPE_NOTICE, $errorText, ''))->setTroubleshooting($troubleshooting);
    }

    /**
     * Create an actionable fix.
     *
     * @param string               $key            Stable fix identifier
     * @param string               $errorText      Detected problem
     * @param string               $suggestionText What the automated action does
     * @param string               $actionKey      Action identifier
     * @param array<string, mixed> $payload        Action payload
     *
     * @return self
     */
    public static function action(
        string $key,
        string $errorText,
        string $suggestionText,
        string $actionKey,
        array $payload = []
    ): self {
        return new self($key, self::TYPE_ACTION, $errorText, $suggestionText, $actionKey, $payload);
    }

    /**
     * @return FixArrayData
     */
    public function toArray(): array
    {
        return [
            'key'                => $this->key,
            'type'               => $this->type,
            'errorText'          => $this->errorText,
            'description'        => $this->description,
            'suggestionText'     => $this->suggestionText,
            'actionKey'          => $this->actionKey,
            'payload'            => $this->payload,
            'title'              => $this->title,
            'troubleshooting'    => $this->troubleshooting,
            'codeSnippet'        => $this->codeSnippet,
            'docUrl'             => $this->docUrl,
            'docLabel'           => $this->docLabel,
            'activityLogLink'    => $this->activityLogLink,
            'activityLogId'      => $this->activityLogId,
            'autoTuneSuggestion' => $this->autoTuneSuggestion,
        ];
    }

    /**
     * @param FixArrayData $data Fix data
     *
     * @return ?self Null if the persisted data is invalid
     */
    public static function fromArray(array $data): ?self
    {
        $data = array_merge(self::DEFAULT_DATA, $data);

        try {
            if ($data['type'] === self::TYPE_NOTICE && $data['suggestionText'] !== '') {
                // Legacy persisted notices carried the manual advice in suggestionText.
                $data['troubleshooting'][] = $data['suggestionText'];
                $data['suggestionText']    = '';
            }

            $fix = new self(
                $data['key'],
                $data['type'],
                $data['errorText'],
                $data['suggestionText'],
                $data['actionKey'],
                $data['payload']
            );

            if ($data['title'] !== '') {
                $fix->setTitle($data['title']);
            }
            if ($data['description'] !== '') {
                $fix->setDescription($data['description']);
            }
            if ($data['troubleshooting'] !== []) {
                $fix->setTroubleshooting($data['troubleshooting']);
            }
            if ($data['codeSnippet'] !== '') {
                $fix->setCodeSnippet($data['codeSnippet']);
            }
            if ($data['docUrl'] !== '' || $data['docLabel'] !== '') {
                $fix->setDocReference($data['docUrl'], $data['docLabel']);
            }
            $fix->setActivityLogLink($data['activityLogLink']);
            $fix->setActivityLogId($data['activityLogId']);
            $fix->setAutoTuneSuggestion($data['autoTuneSuggestion']);

            return $fix;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Set the notice box title. Fixes are grouped by title when displayed.
     *
     * @param string $title Notice box title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        if (trim($title) === '') {
            throw new InvalidArgumentException('Fix title can\'t be empty.');
        }

        $this->title = $title;
        return $this;
    }

    /**
     * Set the extended explanation shown under the error message. May contain
     * HTML; useful when the error alone does not make the problem clear.
     *
     * @param string $description Extended explanation, HTML allowed
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        if (trim($description) === '') {
            throw new InvalidArgumentException('Fix description can\'t be empty.');
        }

        $this->description = $description;
        return $this;
    }

    /**
     * Set the resolution hints. Each entry may contain HTML (links,
     * documentation references).
     *
     * @param string[] $items Resolution hints
     *
     * @return self
     */
    public function setTroubleshooting(array $items): self
    {
        foreach ($items as $item) {
            if (!is_string($item) || trim($item) === '') {
                throw new InvalidArgumentException('Fix troubleshooting entries must be non-empty strings.');
            }
        }

        $this->troubleshooting = array_values($items);
        return $this;
    }

    /**
     * Set a plain-text configuration snippet shown with a copy control.
     *
     * @param string $snippet Configuration snippet
     *
     * @return self
     */
    public function setCodeSnippet(string $snippet): self
    {
        if (trim($snippet) === '') {
            throw new InvalidArgumentException('Fix code snippet can\'t be empty.');
        }

        $this->codeSnippet = $snippet;
        return $this;
    }

    /**
     * Set the documentation article reference.
     *
     * @param string $url   Documentation article URL
     * @param string $label Documentation article label
     *
     * @return self
     */
    public function setDocReference(string $url, string $label): self
    {
        if (trim($url) === '' || trim($label) === '') {
            throw new InvalidArgumentException('Fix doc reference requires both url and label.');
        }

        $this->docUrl   = $url;
        $this->docLabel = $label;
        return $this;
    }

    /**
     * Set whether the notice links to the Activity Log.
     *
     * @param bool $enabled True to show the Activity Log link
     *
     * @return self
     */
    public function setActivityLogLink(bool $enabled): self
    {
        $this->activityLogLink = $enabled;
        return $this;
    }

    /**
     * Set whether the troubleshooting suggests running AutoTune. When true the
     * notice automatically appends the hint to run it.
     *
     * @param bool $enabled True when running AutoTune can address the failure
     *
     * @return self
     */
    public function setAutoTuneSuggestion(bool $enabled): self
    {
        $this->autoTuneSuggestion = $enabled;
        return $this;
    }

    /**
     * Set the related Activity Log event: the notice link opens its detail.
     *
     * @param int $logId Activity Log event id, 0 for none
     *
     * @return self
     */
    public function setActivityLogId(int $logId): self
    {
        if ($logId < 0) {
            throw new InvalidArgumentException('Fix activity log id can\'t be negative.');
        }

        $this->activityLogId = $logId;
        return $this;
    }

    /**
     * @return string Stable fix identifier
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string Notice box title, empty when the renderer default applies
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string Extended explanation of the problem, empty when not set
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string[] Resolution hints, HTML allowed
     */
    public function getTroubleshooting(): array
    {
        return $this->troubleshooting;
    }

    /**
     * @return string Plain-text configuration snippet, empty when not set
     */
    public function getCodeSnippet(): string
    {
        return $this->codeSnippet;
    }

    /**
     * @return string Documentation article URL, empty when not set
     */
    public function getDocUrl(): string
    {
        return $this->docUrl;
    }

    /**
     * @return string Documentation article label, empty when not set
     */
    public function getDocLabel(): string
    {
        return $this->docLabel;
    }

    /**
     * @return bool True when the notice links to the Activity Log
     */
    public function showActivityLogLink(): bool
    {
        return $this->activityLogLink;
    }

    /**
     * @return int Related Activity Log event id, 0 when none
     */
    public function getActivityLogId(): int
    {
        return $this->activityLogId;
    }

    /**
     * @return bool True when the troubleshooting suggests running AutoTune
     */
    public function suggestsAutoTune(): bool
    {
        return $this->autoTuneSuggestion;
    }

    /**
     * @return string One of self::TYPE_*
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string Detected problem
     */
    public function getErrorText(): string
    {
        return $this->errorText;
    }

    /**
     * @return string What the automated action does, empty for notices
     */
    public function getSuggestionText(): string
    {
        return $this->suggestionText;
    }

    /**
     * @return bool True if the fix is actionable
     */
    public function isAction(): bool
    {
        return $this->type === self::TYPE_ACTION;
    }

    /**
     * @return string Action identifier
     */
    public function getActionKey(): string
    {
        if (!$this->isAction() || $this->actionKey === null) {
            throw new InvalidArgumentException('Fix has no action.');
        }

        return $this->actionKey;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Return display-only data without action keys or payloads.
     *
     * @return FixViewData
     */
    public function getViewData(): array
    {
        return [
            'type'               => $this->type,
            'errorText'          => $this->errorText,
            'description'        => $this->description,
            'suggestionText'     => $this->suggestionText,
            'title'              => $this->title,
            'troubleshooting'    => $this->troubleshooting,
            'codeSnippet'        => $this->codeSnippet,
            'docUrl'             => $this->docUrl,
            'docLabel'           => $this->docLabel,
            'activityLogLink'    => $this->activityLogLink,
            'activityLogId'      => $this->activityLogId,
            'autoTuneSuggestion' => $this->autoTuneSuggestion,
        ];
    }

    /**
     * @return void
     */
    private function validate(): void
    {
        if ($this->key === '') {
            throw new InvalidArgumentException('Fix key can\'t be empty.');
        }
        if (!in_array($this->type, [self::TYPE_NOTICE, self::TYPE_ACTION], true)) {
            throw new InvalidArgumentException('Invalid fix type.');
        }
        if ($this->type === self::TYPE_ACTION && ($this->actionKey === null || $this->actionKey === '')) {
            throw new InvalidArgumentException('Fix action can\'t be empty.');
        }
        if ($this->type === self::TYPE_NOTICE && ($this->actionKey !== null || count($this->payload) > 0)) {
            throw new InvalidArgumentException('Notice fixes can\'t define an action.');
        }
        if ($this->type === self::TYPE_NOTICE && $this->suggestionText !== '') {
            throw new InvalidArgumentException('Notice fixes can\'t define a suggestion text.');
        }
    }
}
