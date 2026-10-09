<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Logging\DupLog;
use Throwable;

/**
 * Collects one build or manual-transfer operation across worker requests.
 */
final class TelemetryOperations
{
    /** @return void */
    public static function init(): void
    {
        add_action('duplicator_manual_transfer_start', [self::class, 'startTransfer'], 10, 2);
        add_action('duplicator_package_after_set_status', [self::class, 'onStatus'], 100, 2);
    }

    /**
     * @param AbstractPackage $package Package being transferred
     * @param int             $offset  First upload info belonging to this operation
     *
     * @return void
     */
    public static function startTransfer(AbstractPackage $package, int $offset): void
    {
        $operation = self::newOperation('storage_transfer');
        foreach (array_slice($package->upload_infos, $offset) as $info) {
            $info->setTelemetryOperationId($operation['id']);
        }
        $package->setTelemetryOperation($operation);
    }

    /**
     * @param string $type Operation type
     *
     * @return array<string,mixed>
     */
    private static function newOperation(string $type): array
    {
        return [
            'id'     => wp_generate_uuid4(),
            'type'   => $type,
            'build'  => [],
            'closed' => false,
        ];
    }

    /**
     * Freeze the build descriptor before transfers can remove local files.
     *
     * @param AbstractPackage     $package Built package
     * @param array<string,mixed> $event   Normalized build result
     *
     * @return void
     */
    public static function captureBuild(AbstractPackage $package, array $event): void
    {
        $operation = $package->getTelemetryOperation();
        if (!empty($operation['closed']) || !empty($operation['build']) || ($operation['type'] ?? '') === 'storage_transfer') {
            return;
        }
        $operation          = $operation ?: self::newOperation('backup_build');
        $operation['build'] = $event;
        foreach ($package->upload_infos as $info) {
            $info->setTelemetryOperationId($operation['id']);
        }
        $package->setTelemetryOperation($operation);
        $package->update(false);
    }

    /**
     * Finish cancellation and normal terminal paths; exceptions finish with their cause.
     *
     * @param AbstractPackage $package Package whose status changed
     * @param int             $status  New status
     *
     * @return void
     */
    public static function onStatus(AbstractPackage $package, int $status): void
    {
        if ($package->getExecutionType() === AbstractPackage::EXECUTION_TYPE_AUTOTUNE) {
            return;
        }
        try {
            if (AbstractPackage::isCancellationStatus($status)) {
                if ($package->getTelemetryOperation() === []) {
                    self::captureBuild($package, TelemetryEvents::buildBackupFailEvent($package, $status, new DupliException()));
                }
                self::finish($package);
            } elseif (in_array($status, [AbstractPackage::STATUS_COMPLETE, AbstractPackage::STATUS_STORAGE_FAILED], true)) {
                self::finish($package);
            }
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry operation finalization failed.');
        }
    }

    /**
     * Persist closure before dispatch, preserving at-most-once transport semantics.
     *
     * @param AbstractPackage $package Package whose operation ended
     * @param ?Throwable      $failure Failure that interrupted the active transfer
     *
     * @return void
     */
    public static function finish(AbstractPackage $package, ?Throwable $failure = null): void
    {
        $operation = $package->getTelemetryOperation();
        if ($operation === [] || !empty($operation['closed'])) {
            return;
        }

        $events = [];
        $index  = 0;
        foreach ($package->upload_infos as $info) {
            if ($info->getTelemetryOperationId() !== $operation['id']) {
                continue;
            }
            if ($info->isDefaultStorage()) {
                if (
                    ($operation['build']['event_sub_type'] ?? '') !== 'complete' ||
                    !is_file($package->Archive->getSafeFilePath()) ||
                    !is_file($package->Installer->getSafeFilePath())
                ) {
                    continue;
                }
                $events[] = TelemetryEvents::buildLocalStorageEvent($info, ++$index);
                continue;
            }
            if (!$info->hasStarted()) {
                continue;
            }
            $events[] = TelemetryEvents::buildStorageTransferEvent($info, ++$index, $failure);
        }
        if (!empty($operation['build'])) {
            $build                              = TelemetryEvents::finalizeBackupEvent($operation['build'], $package);
            $build['fields']['storage_records'] = count($events);
            array_unshift($events, $build);
        }

        $operation['closed'] = true;
        $package->setTelemetryOperation($operation);
        if (!$package->update(false)) {
            return;
        }
        foreach (array_chunk($events, TelemetryEvents::EVENT_CAP) as $part => $chunk) {
            TelemetryEvents::sendBatch($chunk, $operation['type'], $operation['id'], $part);
        }
    }
}
