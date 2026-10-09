<?php

namespace Duplicator\Installer\Package;

/**
 * Theme descriptor
 */
class DescriptorTheme
{
    /** @var string */
    public $slug = '';
    /** @var string */
    public $themeName = '';
    /** @var string */
    public $version = '';
    /** @var string */
    public $themeURI = '';
    /** @var string */
    public $parentTheme = '';
    /** @var string */
    public $template = '';
    /** @var string */
    public $stylesheet = '';
    /** @var string */
    public $description = '';
    /** @var string */
    public $author = '';
    /** @var string */
    public $authorURI = '';
    /** @var string[] */
    public $tags = [];
    /** @var bool */
    public $isAllowed = false;
    /** @var bool|int[] list of subsites ids if is multisite */
    public $isActive = false;
    /** @var bool */
    public $defaultTheme = false;

    const DEFAULT_DATA = [
        'slug'         => '',
        'themeName'    => '',
        'version'      => '',
        'themeURI'     => '',
        'parentTheme'  => '',
        'template'     => '',
        'stylesheet'   => '',
        'description'  => '',
        'author'       => '',
        'authorURI'    => '',
        'tags'         => [],
        'isAllowed'    => false,
        'isActive'     => false,
        'defaultTheme' => false,
    ];

    /**
     * Class constructor
     *
     * @param array<string, mixed> $themeData theme info data
     */
    public function __construct($themeData = [])
    {
        $data               = array_merge(self::DEFAULT_DATA, $themeData);
        $this->slug         = $data['slug'];
        $this->themeName    = $data['themeName'];
        $this->version      = $data['version'];
        $this->themeURI     = $data['themeURI'];
        $this->parentTheme  = $data['parentTheme'];
        $this->template     = $data['template'];
        $this->stylesheet   = $data['stylesheet'];
        $this->description  = $data['description'];
        $this->author       = $data['author'];
        $this->authorURI    = $data['authorURI'];
        $this->tags         = $data['tags'];
        $this->isAllowed    = $data['isAllowed'];
        $this->isActive     = $data['isActive'];
        $this->defaultTheme = $data['defaultTheme'];
    }
}
