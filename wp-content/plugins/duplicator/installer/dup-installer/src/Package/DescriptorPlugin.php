<?php

namespace Duplicator\Installer\Package;

/**
 * Plugin descriptor
 */
class DescriptorPlugin
{
    /** @var string */
    public $slug = '';
    /** @var string */
    public $name = '';
    /** @var string */
    public $version = '';
    /** @var string */
    public $pluginURI = '';
    /** @var string */
    public $author = '';
    /** @var string */
    public $authorURI = '';
    /** @var string */
    public $description = '';
    /** @var string */
    public $title = '';
    /** @var bool */
    public $networkActive = false;
    /** @var bool|int[] list of subsites ids if is multisite */
    public $active = false;
    /** @var bool */
    public $mustUse = false;
    /** @var bool */
    public $dropIns = false;
    /** @var bool */
    public $isInArchive = true;

    const DEFAULT_DATA = [
        'slug'          => '',
        'name'          => '',
        'version'       => '',
        'pluginURI'     => '',
        'author'        => '',
        'authorURI'     => '',
        'description'   => '',
        'title'         => '',
        'networkActive' => false,
        'active'        => false,
        'mustUse'       => false,
        'dropIns'       => false,
        'isInArchive'   => true,
    ];

    /**
     * Class constructor
     *
     * @param array<string, mixed> $pluginData plugin info data
     */
    public function __construct(array $pluginData = [])
    {
        $data                = array_merge(self::DEFAULT_DATA, $pluginData);
        $this->slug          = $data['slug'];
        $this->name          = $data['name'];
        $this->version       = $data['version'];
        $this->pluginURI     = $data['pluginURI'];
        $this->author        = $data['author'];
        $this->authorURI     = $data['authorURI'];
        $this->description   = $data['description'];
        $this->title         = $data['title'];
        $this->networkActive = $data['networkActive'];
        $this->active        = $data['active'];
        $this->mustUse       = $data['mustUse'];
        $this->dropIns       = $data['dropIns'];
        $this->isInArchive   = $data['isInArchive'];
    }
}
