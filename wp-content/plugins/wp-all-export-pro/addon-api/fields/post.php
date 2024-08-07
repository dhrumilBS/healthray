<?php

namespace Wpae\AddonAPI;

class PMXE_Addon_Post_Field extends PMXE_Addon_Field {

    public function getPermalink( $value ) {
        $entry = empty( $value ) ? false : get_post( $value );

        if ( $entry ) {
            return get_permalink( $entry->ID );
        } else {
            return '';
        }
    }

    public function getSlug( $value ) {
        $entry = empty( $value ) ? false : get_post( $value );

        if ( $entry ) {
            return $entry->post_name;
        } else {
            return '';
        }
    }

    public function getTitle( $value ) {
        $entry = empty( $value ) ? false : get_post( $value );

        if ( $entry ) {
            return $entry->post_title;
        } else {
            return '';
        }
    }

    public function toString() {

        $format       = $this->settings['post_value_format'] ?? 'id';
        $return_value = [];

        if ( ! is_array( $this->value ) ) {
            $value = [ $this->value ];
        } else {
            $value = $this->value;
        }

        foreach ( $value as $current_value ) {

            switch ( $format ) {
                case 'id':
                    $return_value[] = $current_value;
                    break;
                case 'permalink':
                    $return_value[] = $this->getPermalink( $current_value );
                    break;
                case 'slug':
                    $return_value[] = $this->getSlug( $current_value );
                    break;
                default:
                    $return_value[] = $this->getTitle( $current_value );
            }
        }

        return implode( $this->getImplode(), $return_value );
    }
}
