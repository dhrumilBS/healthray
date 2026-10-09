<?php

namespace Duplicator\Package\Archive\Filters;

class ScopeDirectory extends ScopeBase
{
    /**
     * @var string[] Directories containing other WordPress installs
     */
    public array $AddonSites = [];
    /**
     * @var array<array<string,mixed>> Items that are too large
     */
    public array $Size = [];
}
