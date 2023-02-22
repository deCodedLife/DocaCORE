<?php
/**
 * DocumentHtmlDefinition
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
 * DocumentHtmlDefinition Class Doc Comment
 *
 * @category    Class
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class DocumentHtmlDefinition implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'documentHtmlDefinition';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'display_anchor_prefix' => 'string',
        'display_anchors' => '\DocuSign\eSign\Model\DocumentHtmlDisplayAnchor[]',
        'display_order' => 'string',
        'display_page_number' => 'string',
        'document_guid' => 'string',
        'document_id' => 'string',
        'header_label' => 'string',
        'max_screen_width' => 'string',
        'remove_empty_tags' => 'string',
        'show_mobile_optimized_toggle' => 'string',
        'source' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'display_anchor_prefix' => null,
        'display_anchors' => null,
        'display_order' => null,
        'display_page_number' => null,
        'document_guid' => null,
        'document_id' => null,
        'header_label' => null,
        'max_screen_width' => null,
        'remove_empty_tags' => null,
        'show_mobile_optimized_toggle' => null,
        'source' => null
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
        'display_anchor_prefix' => 'displayAnchorPrefix',
        'display_anchors' => 'displayAnchors',
        'display_order' => 'displayOrder',
        'display_page_number' => 'displayPageNumber',
        'document_guid' => 'documentGuid',
        'document_id' => 'documentId',
        'header_label' => 'headerLabel',
        'max_screen_width' => 'maxScreenWidth',
        'remove_empty_tags' => 'removeEmptyTags',
        'show_mobile_optimized_toggle' => 'showMobileOptimizedToggle',
        'source' => 'source'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'display_anchor_prefix' => 'setDisplayAnchorPrefix',
        'display_anchors' => 'setDisplayAnchors',
        'display_order' => 'setDisplayOrder',
        'display_page_number' => 'setDisplayPageNumber',
        'document_guid' => 'setDocumentGuid',
        'document_id' => 'setDocumentId',
        'header_label' => 'setHeaderLabel',
        'max_screen_width' => 'setMaxScreenWidth',
        'remove_empty_tags' => 'setRemoveEmptyTags',
        'show_mobile_optimized_toggle' => 'setShowMobileOptimizedToggle',
        'source' => 'setSource'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'display_anchor_prefix' => 'getDisplayAnchorPrefix',
        'display_anchors' => 'getDisplayAnchors',
        'display_order' => 'getDisplayOrder',
        'display_page_number' => 'getDisplayPageNumber',
        'document_guid' => 'getDocumentGuid',
        'document_id' => 'getDocumentId',
        'header_label' => 'getHeaderLabel',
        'max_screen_width' => 'getMaxScreenWidth',
        'remove_empty_tags' => 'getRemoveEmptyTags',
        'show_mobile_optimized_toggle' => 'getShowMobileOptimizedToggle',
        'source' => 'getSource'
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
        $this->container['display_anchor_prefix'] = isset($data['display_anchor_prefix']) ? $data['display_anchor_prefix'] : null;
        $this->container['display_anchors'] = isset($data['display_anchors']) ? $data['display_anchors'] : null;
        $this->container['display_order'] = isset($data['display_order']) ? $data['display_order'] : null;
        $this->container['display_page_number'] = isset($data['display_page_number']) ? $data['display_page_number'] : null;
        $this->container['document_guid'] = isset($data['document_guid']) ? $data['document_guid'] : null;
        $this->container['document_id'] = isset($data['document_id']) ? $data['document_id'] : null;
        $this->container['header_label'] = isset($data['header_label']) ? $data['header_label'] : null;
        $this->container['max_screen_width'] = isset($data['max_screen_width']) ? $data['max_screen_width'] : null;
        $this->container['remove_empty_tags'] = isset($data['remove_empty_tags']) ? $data['remove_empty_tags'] : null;
        $this->container['show_mobile_optimized_toggle'] = isset($data['show_mobile_optimized_toggle']) ? $data['show_mobile_optimized_toggle'] : null;
        $this->container['source'] = isset($data['source']) ? $data['source'] : null;
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
     * Gets display_anchor_prefix
     *
     * @return string
     */
    public function getDisplayAnchorPrefix()
    {
        return $this->container['display_anchor_prefix'];
    }

    /**
     * Sets display_anchor_prefix
     *
     * @param string $display_anchor_prefix 
     *
     * @return $this
     */
    public function setDisplayAnchorPrefix($display_anchor_prefix)
    {
        $this->container['display_anchor_prefix'] = $display_anchor_prefix;

        return $this;
    }

    /**
     * Gets display_anchors
     *
     * @return \DocuSign\eSign\Model\DocumentHtmlDisplayAnchor[]
     */
    public function getDisplayAnchors()
    {
        return $this->container['display_anchors'];
    }

    /**
     * Sets display_anchors
     *
     * @param \DocuSign\eSign\Model\DocumentHtmlDisplayAnchor[] $display_anchors 
     *
     * @return $this
     */
    public function setDisplayAnchors($display_anchors)
    {
        $this->container['display_anchors'] = $display_anchors;

        return $this;
    }

    /**
     * Gets display_order
     *
     * @return string
     */
    public function getDisplayOrder()
    {
        return $this->container['display_order'];
    }

    /**
     * Sets display_order
     *
     * @param string $display_order 
     *
     * @return $this
     */
    public function setDisplayOrder($display_order)
    {
        $this->container['display_order'] = $display_order;

        return $this;
    }

    /**
     * Gets display_page_number
     *
     * @return string
     */
    public function getDisplayPageNumber()
    {
        return $this->container['display_page_number'];
    }

    /**
     * Sets display_page_number
     *
     * @param string $display_page_number 
     *
     * @return $this
     */
    public function setDisplayPageNumber($display_page_number)
    {
        $this->container['display_page_number'] = $display_page_number;

        return $this;
    }

    /**
     * Gets document_guid
     *
     * @return string
     */
    public function getDocumentGuid()
    {
        return $this->container['document_guid'];
    }

    /**
     * Sets document_guid
     *
     * @param string $document_guid 
     *
     * @return $this
     */
    public function setDocumentGuid($document_guid)
    {
        $this->container['document_guid'] = $document_guid;

        return $this;
    }

    /**
     * Gets document_id
     *
     * @return string
     */
    public function getDocumentId()
    {
        return $this->container['document_id'];
    }

    /**
     * Sets document_id
     *
     * @param string $document_id Specifies the document ID number that the tab is placed on. This must refer to an existing Document's ID attribute.
     *
     * @return $this
     */
    public function setDocumentId($document_id)
    {
        $this->container['document_id'] = $document_id;

        return $this;
    }

    /**
     * Gets header_label
     *
     * @return string
     */
    public function getHeaderLabel()
    {
        return $this->container['header_label'];
    }

    /**
     * Sets header_label
     *
     * @param string $header_label 
     *
     * @return $this
     */
    public function setHeaderLabel($header_label)
    {
        $this->container['header_label'] = $header_label;

        return $this;
    }

    /**
     * Gets max_screen_width
     *
     * @return string
     */
    public function getMaxScreenWidth()
    {
        return $this->container['max_screen_width'];
    }

    /**
     * Sets max_screen_width
     *
     * @param string $max_screen_width 
     *
     * @return $this
     */
    public function setMaxScreenWidth($max_screen_width)
    {
        $this->container['max_screen_width'] = $max_screen_width;

        return $this;
    }

    /**
     * Gets remove_empty_tags
     *
     * @return string
     */
    public function getRemoveEmptyTags()
    {
        return $this->container['remove_empty_tags'];
    }

    /**
     * Sets remove_empty_tags
     *
     * @param string $remove_empty_tags 
     *
     * @return $this
     */
    public function setRemoveEmptyTags($remove_empty_tags)
    {
        $this->container['remove_empty_tags'] = $remove_empty_tags;

        return $this;
    }

    /**
     * Gets show_mobile_optimized_toggle
     *
     * @return string
     */
    public function getShowMobileOptimizedToggle()
    {
        return $this->container['show_mobile_optimized_toggle'];
    }

    /**
     * Sets show_mobile_optimized_toggle
     *
     * @param string $show_mobile_optimized_toggle 
     *
     * @return $this
     */
    public function setShowMobileOptimizedToggle($show_mobile_optimized_toggle)
    {
        $this->container['show_mobile_optimized_toggle'] = $show_mobile_optimized_toggle;

        return $this;
    }

    /**
     * Gets source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->container['source'];
    }

    /**
     * Sets source
     *
     * @param string $source 
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->container['source'] = $source;

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

