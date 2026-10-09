<?php

namespace VendorDuplicator\GuzzleHttp;

use VendorDuplicator\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
