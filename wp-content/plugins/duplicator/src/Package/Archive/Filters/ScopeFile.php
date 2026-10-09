<?php

namespace Duplicator\Package\Archive\Filters;

class ScopeFile extends ScopeBase
{
    /**
     * @var array<array<string,mixed>> Items that are too large
     */
    public array $Size = [];
}
