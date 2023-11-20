<?php
/**
 * RecipientPhoneAuthentication
 *
 * PHP version 5
 *
 * @category Class
 * @package  DocuSign\eSign
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * DocuSign REST API
 *
 * The DocuSign REST API provides you with a powerful, convenient, and simple Web services API for interacting with DocuSign.
 *
 * OpenAPI spec version: v2.1
 * Contact: devcenter@docusign.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.13-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace DocuSign\eSign\Model;

use \ArrayAccess;
use \DocuSign\eSign\ObjectSerializer;

/**
 * RecipientPhoneAuthentication Class Doc Comment
 *
 * @category    Class
 * @description A complex type that Contains the elements:  * recipMayProvideNumber - Boolean. When set to **true**, the recipient can use whatever phone number they choose. * senderProvidedNumbers - ArrayOfString.  A list of phone numbers the recipient can use. * recordVoicePrint - Reserved. * validateRecipProvidedNumber - Reserved.
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class RecipientPhoneAuthentication implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'recipientPhoneAuthentication';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'recip_may_provide_number' => 'string',
        'recip_may_provide_number_metadata' => '\DocuSign\eSign\Model\PropertyMetadata',
        'record_voice_print' => 'string',
        'record_voice_print_metadata' => '\DocuSign\eSign\Model\PropertyMetadata',
        'sender_provided_numbers' => 'string[]',
        'sender_provided_numbers_metadata' => '\DocuSign\eSign\Model\PropertyMetadata',
        'validate_recip_provided_number' => 'string',
        'validate_recip_provided_number_metadata' => '\DocuSign\eSign\Model\PropertyMetadata'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'recip_may_provide_number' => null,
        'recip_may_provide_number_metadata' => null,
        'record_voice_print' => null,
        'record_voice_print_metadata' => null,
        'sender_provided_numbers' => null,
        'sender_provided_numbers_metadata' => null,
        'validate_recip_provided_number' => null,
        'validate_recip_provided_number_metadata' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'recip_may_provide_number' => 'recipMayProvideNumber',
        'recip_may_provide_number_metadata' => 'recipMayProvideNumberMetadata',
        'record_voice_print' => 'recordVoicePrint',
        'record_voice_print_metadata' => 'recordVoicePrintMetadata',
        'sender_provided_numbers' => 'senderProvidedNumbers',
        'sender_provided_numbers_metadata' => 'senderProvidedNumbersMetadata',
        'validate_recip_provided_number' => 'validateRecipProvidedNumber',
        'validate_recip_provided_number_metadata' => 'validateRecipProvidedNumberMetadata'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'recip_may_provide_number' => 'setRecipMayProvideNumber',
        'recip_may_provide_number_metadata' => 'setRecipMayProvideNumberMetadata',
        'record_voice_print' => 'setRecordVoicePrint',
        'record_voice_print_metadata' => 'setRecordVoicePrintMetadata',
        'sender_provided_numbers' => 'setSenderProvidedNumbers',
        'sender_provided_numbers_metadata' => 'setSenderProvidedNumbersMetadata',
        'validate_recip_provided_number' => 'setValidateRecipProvidedNumber',
        'validate_recip_provided_number_metadata' => 'setValidateRecipProvidedNumberMetadata'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'recip_may_provide_number' => 'getRecipMayProvideNumber',
        'recip_may_provide_number_metadata' => 'getRecipMayProvideNumberMetadata',
        'record_voice_print' => 'getRecordVoicePrint',
        'record_voice_print_metadata' => 'getRecordVoicePrintMetadata',
        'sender_provided_numbers' => 'getSenderProvidedNumbers',
        'sender_provided_numbers_metadata' => 'getSenderProvidedNumbersMetadata',
        'validate_recip_provided_number' => 'getValidateRecipProvidedNumber',
        'validate_recip_provided_number_metadata' => 'getValidateRecipProvidedNumberMetadata'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['recip_may_provide_number'] = isset($data['recip_may_provide_number']) ? $data['recip_may_provide_number'] : null;
        $this->container['recip_may_provide_number_metadata'] = isset($data['recip_may_provide_number_metadata']) ? $data['recip_may_provide_number_metadata'] : null;
        $this->container['record_voice_print'] = isset($data['record_voice_print']) ? $data['record_voice_print'] : null;
        $this->container['record_voice_print_metadata'] = isset($data['record_voice_print_metadata']) ? $data['record_voice_print_metadata'] : null;
        $this->container['sender_provided_numbers'] = isset($data['sender_provided_numbers']) ? $data['sender_provided_numbers'] : null;
        $this->container['sender_provided_numbers_metadata'] = isset($data['sender_provided_numbers_metadata']) ? $data['sender_provided_numbers_metadata'] : null;
        $this->container['validate_recip_provided_number'] = isset($data['validate_recip_provided_number']) ? $data['validate_recip_provided_number'] : null;
        $this->container['validate_recip_provided_number_metadata'] = isset($data['validate_recip_provided_number_metadata']) ? $data['validate_recip_provided_number_metadata'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets recip_may_provide_number
     *
     * @return string
     */
    public function getRecipMayProvideNumber()
    {
        return $this->container['recip_may_provide_number'];
    }

    /**
     * Sets recip_may_provide_number
     *
     * @param string $recip_may_provide_number Boolean. When set to **true**, the recipient can supply a phone number their choice.
     *
     * @return $this
     */
    public function setRecipMayProvideNumber($recip_may_provide_number)
    {
        $this->container['recip_may_provide_number'] = $recip_may_provide_number;

        return $this;
    }

    /**
     * Gets recip_may_provide_number_metadata
     *
     * @return \DocuSign\eSign\Model\PropertyMetadata
     */
    public function getRecipMayProvideNumberMetadata()
    {
        return $this->container['recip_may_provide_number_metadata'];
    }

    /**
     * Sets recip_may_provide_number_metadata
     *
     * @param \DocuSign\eSign\Model\PropertyMetadata $recip_may_provide_number_metadata recip_may_provide_number_metadata
     *
     * @return $this
     */
    public function setRecipMayProvideNumberMetadata($recip_may_provide_number_metadata)
    {
        $this->container['recip_may_provide_number_metadata'] = $recip_may_provide_number_metadata;

        return $this;
    }

    /**
     * Gets record_voice_print
     *
     * @return string
     */
    public function getRecordVoicePrint()
    {
        return $this->container['record_voice_print'];
    }

    /**
     * Sets record_voice_print
     *
     * @param string $record_voice_print Reserved.
     *
     * @return $this
     */
    public function setRecordVoicePrint($record_voice_print)
    {
        $this->container['record_voice_print'] = $record_voice_print;

        return $this;
    }

    /**
     * Gets record_voice_print_metadata
     *
     * @return \DocuSign\eSign\Model\PropertyMetadata
     */
    public function getRecordVoicePrintMetadata()
    {
        return $this->container['record_voice_print_metadata'];
    }

    /**
     * Sets record_voice_print_metadata
     *
     * @param \DocuSign\eSign\Model\PropertyMetadata $record_voice_print_metadata record_voice_print_metadata
     *
     * @return $this
     */
    public function setRecordVoicePrintMetadata($record_voice_print_metadata)
    {
        $this->container['record_voice_print_metadata'] = $record_voice_print_metadata;

        return $this;
    }

    /**
     * Gets sender_provided_numbers
     *
     * @return string[]
     */
    public function getSenderProvidedNumbers()
    {
        return $this->container['sender_provided_numbers'];
    }

    /**
     * Sets sender_provided_numbers
     *
     * @param string[] $sender_provided_numbers An Array containing a list of phone numbers the recipient may use for SMS text authentication.
     *
     * @return $this
     */
    public function setSenderProvidedNumbers($sender_provided_numbers)
    {
        $this->container['sender_provided_numbers'] = $sender_provided_numbers;

        return $this;
    }

    /**
     * Gets sender_provided_numbers_metadata
     *
     * @return \DocuSign\eSign\Model\PropertyMetadata
     */
    public function getSenderProvidedNumbersMetadata()
    {
        return $this->container['sender_provided_numbers_metadata'];
    }

    /**
     * Sets sender_provided_numbers_metadata
     *
     * @param \DocuSign\eSign\Model\PropertyMetadata $sender_provided_numbers_metadata sender_provided_numbers_metadata
     *
     * @return $this
     */
    public function setSenderProvidedNumbersMetadata($sender_provided_numbers_metadata)
    {
        $this->container['sender_provided_numbers_metadata'] = $sender_provided_numbers_metadata;

        return $this;
    }

    /**
     * Gets validate_recip_provided_number
     *
     * @return string
     */
    public function getValidateRecipProvidedNumber()
    {
        return $this->container['validate_recip_provided_number'];
    }

    /**
     * Sets validate_recip_provided_number
     *
     * @param string $validate_recip_provided_number Reserved.
     *
     * @return $this
     */
    public function setValidateRecipProvidedNumber($validate_recip_provided_number)
    {
        $this->container['validate_recip_provided_number'] = $validate_recip_provided_number;

        return $this;
    }

    /**
     * Gets validate_recip_provided_number_metadata
     *
     * @return \DocuSign\eSign\Model\PropertyMetadata
     */
    public function getValidateRecipProvidedNumberMetadata()
    {
        return $this->container['validate_recip_provided_number_metadata'];
    }

    /**
     * Sets validate_recip_provided_number_metadata
     *
     * @param \DocuSign\eSign\Model\PropertyMetadata $validate_recip_provided_number_metadata validate_recip_provided_number_metadata
     *
     * @return $this
     */
    public function setValidateRecipProvidedNumberMetadata($validate_recip_provided_number_metadata)
    {
        $this->container['validate_recip_provided_number_metadata'] = $validate_recip_provided_number_metadata;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}

