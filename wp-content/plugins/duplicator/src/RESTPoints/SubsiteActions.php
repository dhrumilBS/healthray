<?php

declare(strict_types=1);

namespace Duplicator\RESTPoints;

/**
 * Back-compat stub — do not delete, do not add behavior.
 *
 * Versions <= 4.5.25.4 instantiate this class on rest_api_init. During an
 * in-place update the old code stays in memory after the new files land,
 * so a REST request late in the update fatals with "Class not found"
 * without this stub. Not an AbstractRESTPoint subclass on purpose, so the
 * old registration filter discards it and nothing gets registered.
 * Remove once updates from <= 4.5.25.4 are negligible.
 */
class SubsiteActions
{
}
