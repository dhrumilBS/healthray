<?php

namespace VendorDuplicator\Cron;

interface FieldFactoryInterface
{
    public function getField(int $position): FieldInterface;
}
