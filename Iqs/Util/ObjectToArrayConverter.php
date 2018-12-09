<?php
/**
 * ObjectToArrayConverter
 *
 * Provides static method to convert complex objects to arrays for Json Encoding
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Util {


    class ObjectToArrayConverter {

        static public function convertObjectToArray($d) {
            if (is_object($d)) {
                // Gets the properties of the object
                $d = get_object_vars($d);
            }

            if (is_array($d)) {
                return array_map('self::convertObjectToArray', $d);
            } else {
                // Return array
                return $d;
            }
        }
    }

}