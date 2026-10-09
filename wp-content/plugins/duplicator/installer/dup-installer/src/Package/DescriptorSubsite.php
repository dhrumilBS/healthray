<?php

namespace Duplicator\Installer\Package;

/**
 * Subsite descriptor
 */
class DescriptorSubsite
{
    /** @var int */
    public $id = 0;
    /** @var string */
    public $domain = '';
    /** @var string */
    public $path = '';
    /** @var string */
    public $blogname = '';
    /** @var string */
    public $blog_prefix = '';
    /** @var string[] */
    public $filteredTables = [];
    /** @var array<object{ID: int, user_login: string}> */
    public $adminUsers = [];
    /** @var string */
    public $fullHomeUrl = '';
    /** @var string */
    public $fullSiteUrl = '';
    /** @var string */
    public $uploadPath = '';
    /** @var string */
    public $fullUploadPath = '';
    /** @var string */
    public $fullUploadSafePath = '';
    /** @var string */
    public $fullUploadUrl = '';
    /** @var string[] */
    public $filteredPaths = [];
}
