<?php

declare(strict_types=1);

namespace Duplicator\Ajax;

use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Views\UI\UiViewState;
use Exception;

class ServicesUi extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_view_state_update', 'viewStateUpdate');
    }

    /**
     * View state update handler
     *
     * @return void
     */
    public function viewStateUpdate(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'viewStateUpdateCallback',
            ],
            'duplicator_view_state_update',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * View state update callback
     *
     * @return array<string, mixed>
     */
    public static function viewStateUpdateCallback(): array
    {
        $inputData = filter_input_array(INPUT_POST, [
            'states' => [
                'filter'  => FILTER_UNSAFE_RAW,
                'flags'   => FILTER_FORCE_ARRAY,
                'options' => [
                    'default' => [],
                ],
            ],
            'key'    => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'options' => ['default' => false],
            ],
            'value'  => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'options' => ['default' => false],
            ],
        ]);

        $result = [
            'update-success' => false,
            'key'            => '',
            'value'          => '',
        ];

        if (isset($inputData['states']) && !empty($inputData['states'])) {
            foreach ($inputData['states'] as $index => $state) {
                $filteredState = filter_var_array($state, [
                    'key'   => [
                        'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                        'options' => ['default' => false],
                    ],
                    'value' => [
                        'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                        'options' => ['default' => false],
                    ],
                ]);
                if ($filteredState['key'] === false && $filteredState['value']) {
                    throw new Exception("Sent data is not valid.");
                }
                $inputData['states'][$index] = $filteredState;
            }

            $view_state = UiViewState::getArray();
            $last_key   = '';
            foreach ($inputData['states'] as $state) {
                $view_state[$state['key']] = $state['value'];
                $last_key                  = $state['key'];
            }
            $result['update-success'] = UiViewState::setArray($view_state);
            $result['key']            = esc_html($last_key);
            $result['value']          = esc_html($view_state[$last_key]);
        } elseif ($inputData['key'] !== false && $inputData['value'] !== false) {
            $result['update-success'] = UiViewState::save($inputData['key'], $inputData['value']);
            $result['key']            = esc_html($inputData['key']);
            $result['value']          = esc_html($inputData['value']);
        } else {
            throw new Exception("Sent data is not valid.");
        }

        return $result;
    }
}
