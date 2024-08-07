<?php

namespace Wpae\AddonAPI;

abstract class PMXE_Addon_Base {
    use HasError;

    public $slug = 'not-implemented';
    public PMXE_Addon_View $view;
    public PMXE_Addon_Exporter $exporter;

    // Extra fields created by the addon
    public $fields = [];

    // Cast values to something else without having to create a custom field
    public $casts = [];

    public function __construct() {
        $this->preflight();
        $this->initEed();

        $this->view     = new PMXE_Addon_View( $this );
        $this->exporter = new PMXE_Addon_Exporter( $this );

        // Register

        add_filter( "pmxe_addons", [ $this, 'register' ] );

        // Data
        add_filter( "pmxe_get_{$this->slug}_addon_meta_keys", [ $this, 'getExistingMetaKeys' ], 10, 1 );
        add_filter( "pmxe_get_{$this->slug}_addon_available_data", [ $this, 'getAvailableData' ], 10, 1 );
        add_filter( "pmxe_get_{$this->slug}_addon_fields_options", [ $this, 'getFieldOptions' ], 10, 2 );

        // Render
        add_action( "pmxe_render_{$this->slug}_addon", [ $this->view, 'render' ] );
        add_action( "pmxe_render_{$this->slug}_addon_filters", [ $this->view, 'filters' ] );
        add_action( "pmxe_render_{$this->slug}_addon_new_field", [ $this->view, 'newField' ] );

        // Export
        add_filter( "pmxe_{$this->slug}_addon_export_field", [ $this->exporter, 'run' ], 10, 11 );
        add_filter( "pmxe_{$this->slug}_addon_get_headers", [ $this->exporter, 'getHeaders' ], 10, 6 );
        add_filter( "wp_all_export_csv_rows", [ $this->exporter, "filterCsvRows" ], 10, 3 );
    }

    /**
     * Path to the plugin file relative to the plugins directory.
     */
    public function getPluginPath() {
        return $this->rootDir . '/plugin.php';
    }

    public function initEed() {
    }

    /**
     * Do stuff before the plugin is activated
     * @return void
     */
    public function preflight() {
        $results = $this->canRun();

        if ( is_wp_error( $results ) ) {
            $this->showErrorAndDeactivate( $results->get_error_message() );

            return;
        }
    }

    // Getters
    abstract public function name(): string;

    abstract public function description(): string;

    /**
     * Determine if the plugin can run on the current site otherwise disable it.
     * @return bool|\WP_Error
     */
    abstract public function canRun();

    /**
     * Get fields by type
     *
     * @param array $types
     * @param string|null $subtype
     *
     * @return mixed
     */
    abstract public static function fields( array $types, string $subtype = null );

    /**
     * Get groups by type
     *
     * @param array $types
     *
     * @return mixed
     */
    abstract public static function groups( array $types, string $subtype = null );

    public function getGroupsByExportType() {
        return static::groups(
            \XmlExportEngine::$post_types,
            \XmlExportEngine::$exportOptions['taxonomy_to_export']
        );
    }

    public function getFieldsByExportType() {
        $fields = static::fields(
            \XmlExportEngine::$post_types,
            \XmlExportEngine::$exportOptions['taxonomy_to_export']
        );

        return array_map(
            fn( $field ) => array_merge( $field, [
                'addon'     => $this->slug,
                'subfields' => array_map(
                    fn( $subfield ) => array_merge( $subfield, [
                        'addon' => $this->slug,
                    ] ),
                    $field['subfields'] ?? []
                ),
            ] ),
            $fields
        );
    }

    public function getFieldsByGroup( $group_id ) {
        $fields = $this->getFieldsByExportType();

        return array_values(
            array_filter( $fields, function ( $field ) use ( $group_id ) {
                return $field['group'] === $group_id;
            } )
        );
    }

    public function register( $addons ) {
        $addons[] = $this->slug;

        return $addons;
    }

    public function getExistingMetaKeys( $existing_meta_keys = [] ) {
        $fields      = $this->getFieldsByExportType();
        $fields_keys = wp_list_pluck( $fields, 'key' );

        // Remove addon fields so they don't show up in the Custom Fields dropdown.
        foreach ( $existing_meta_keys as $key => $meta_key ) {
            if ( in_array( $meta_key, $fields_keys ) ) {
                unset( $existing_meta_keys[ $key ] );
            }
        }

        return $existing_meta_keys;
    }

    public function getAvailableData( $available_data ) {
        return $available_data;
    }

    public function getFieldOptions( $fields, $field_keys = [] ) {
        $groups = $this->getGroupsByExportType();

        foreach ( $groups as $key => $group ) {
            $group_fields = $this->getFieldsByGroup( $group['id'] );

            foreach ( $group_fields as $field ) {
                // Label is used because that's what's used in the Custom XML export.
                $field_key = $field['label'];

                if ( ! in_array( $field_key, $field_keys ) ) {
                    continue;
                }

                $fields['ids'][]                              = 1; // TODO: Why is this 1 everywhere?
                $fields['cc_label'][]                         = $field['key'];
                $fields['cc_php'][]                           = '';
                $fields['cc_code'][]                          = '';
                $fields['cc_sql'][]                           = '';
                $fields['cc_options'][]                       = serialize( array_merge( $field, [ 'group_id' => $group['id'] ] ) );
                $fields['cc_type'][]                          = $this->slug;
                $fields['cc_value'][]                         = $field['key'];
                $fields['cc_name'][]                          = $field['label'];
                $fields['cc_settings'][]                      = '';
                $fields['cc_combine_multiple_fields'][]       = '';
                $fields['cc_combine_multiple_fields_value'][] = '';
            }
        }

        return $fields;
    }

    /*
     * Get the field's value, either from the post meta or from the repeater row
     */
    public function resolveFieldValue( PMXE_Addon_Field $field, $record, $recordId ) {
        if ( $field->repeater_row_index !== null ) {
            $rows = $field->parent->value;

            return $rows[ $field->repeater_row_index ][ $field->key ] ?? '';
        }

        return get_post_meta( $recordId, $field->key, true );
    }

    /**
     * Potentially change the class of a field at runtime
     *
     * @param array $field
     * @param class-string<PMXI_Addon_Field> $class
     */
    public function resolveFieldClass( $field, $class ) {
        return $class;
    }
}

trait HasError {
    public function showErrorAndDeactivate( string $msg ) {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $notice = new \Wpae\WordPress\AdminErrorNotice( $msg );
        $notice->render();

        deactivate_plugins( $this->getPluginPath() );
    }

    public function getMissingDependencyError( $pluginName, $pluginUrl ) {
        return new \WP_Error( 'missing_dependency', __(
            sprintf( "<b>%s Export Add-on Plugin</b>: <a target=\"_blank\" href=\"%s\">%s</a> must be installed", $this->name(), $pluginUrl, $pluginName ),
            'wp_all_import_plugin'
        ) );
    }
}
