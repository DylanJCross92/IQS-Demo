<?php
/**
 * JsonDataResponsePackage
 *
 * This class generates a JSON object containing the data to be returned to the client
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Model {


    use Iqs\Util\ObjectToArrayConverter;

    class DataResponsePackage implements \JsonSerializable {

        public $responseDataPayload = array();
        public $exception;

        public function addResponseDataPayloadItem($itemKey, $newPayload){
            $this->responseDataPayload[$itemKey]=$newPayload;
        }

        public function removeResponseDataPayloadItem($itemKey){
            unset($this->responseDataPayload,$itemKey);
        }

        public function setException(ClientExceptionPackage $newException){
            $this->exception = $newException;
        }

        public function getJsonDataResponsePackage(){
            return json_encode(ObjectToArrayConverter::convertObjectToArray($this));
        }

        public function jsonSerialize() {
            return ObjectToArrayConverter::convertObjectToArray($this);
        }

    }
}