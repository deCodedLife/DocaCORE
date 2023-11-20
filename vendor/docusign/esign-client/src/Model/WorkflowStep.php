<?php
/**
 * WorkflowStep
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
 * WorkflowStep Class Doc Comment
 *
 * @category    Class
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class WorkflowStep implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'workflowStep';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'action' => 'string',
        'completed_date' => 'string',
        'item_id' => 'string',
        'recipient_routing' => '\DocuSign\eSign\Model\RecipientRouting',
        'status' => 'string',
        'triggered_date' => 'string',
        'trigger_on_item' => 'string',
        'workflow_step_id' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'action' => null,
        'completed_date' => null,
        'item_id' => null,
        'recipient_routing' => null,
        'status' => null,
        'triggered_date' => null,
        'trigger_on_item' => null,
        'workflow_step_id' => null
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
        'action' => 'action',
        'completed_date' => 'completedDate',
        'item_id' => 'itemId',
        'recipient_routing' => 'recipientRouting',
        'status' => 'status',
        'triggered_date' => 'triggeredDate',
        'trigger_on_item' => 'triggerOnItem',
        'workflow_step_id' => 'workflowStepId'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'action' => 'setAction',
        'completed_date' => 'setCompletedDate',
        'item_id' => 'setItemId',
        'recipient_routing' => 'setRecipientRouting',
        'status' => 'setStatus',
        'triggered_date' => 'setTriggeredDate',
        'trigger_on_item' => 'setTriggerOnItem',
        'workflow_step_id' => 'setWorkflowStepId'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'action' => 'getAction',
        'completed_date' => 'getCompletedDate',
        'item_id' => 'getItemId',
        'recipient_routing' => 'getRecipientRouting',
        'status' => 'getStatus',
        'triggered_date' => 'getTriggeredDate',
        'trigger_on_item' => 'getTriggerOnItem',
        'workflow_step_id' => 'getWorkflowStepId'
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
        $this->container['action'] = isset($data['action']) ? $data['action'] : null;
        $this->container['completed_date'] = isset($data['completed_date']) ? $data['completed_date'] : null;
        $this->container['item_id'] = isset($data['item_id']) ? $data['item_id'] : null;
        $this->container['recipient_routing'] = isset($data['recipient_routing']) ? $data['recipient_routing'] : null;
        $this->container['status'] = isset($data['status']) ? $data['status'] : null;
        $this->container['triggered_date'] = isset($data['triggered_date']) ? $data['triggered_date'] : null;
        $this->container['trigger_on_item'] = isset($data['trigger_on_item']) ? $data['trigger_on_item'] : null;
        $this->container['workflow_step_id'] = isset($data['workflow_step_id']) ? $data['workflow_step_id'] : null;
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
     * Gets action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->container['action'];
    }

    /**
     * Sets action
     *
     * @param string $action 
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->container['action'] = $action;

        return $this;
    }

    /**
     * Gets completed_date
     *
     * @return string
     */
    public function getCompletedDate()
    {
        return $this->container['completed_date'];
    }

    /**
     * Sets completed_date
     *
     * @param string $completed_date 
     *
     * @return $this
     */
    public function setCompletedDate($completed_date)
    {
        $this->container['completed_date'] = $completed_date;

        return $this;
    }

    /**
     * Gets item_id
     *
     * @return string
     */
    public function getItemId()
    {
        return $this->container['item_id'];
    }

    /**
     * Sets item_id
     *
     * @param string $item_id 
     *
     * @return $this
     */
    public function setItemId($item_id)
    {
        $this->container['item_id'] = $item_id;

        return $this;
    }

    /**
     * Gets recipient_routing
     *
     * @return \DocuSign\eSign\Model\RecipientRouting
     */
    public function getRecipientRouting()
    {
        return $this->container['recipient_routing'];
    }

    /**
     * Sets recipient_routing
     *
     * @param \DocuSign\eSign\Model\RecipientRouting $recipient_routing recipient_routing
     *
     * @return $this
     */
    public function setRecipientRouting($recipient_routing)
    {
        $this->container['recipient_routing'] = $recipient_routing;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string $status Indicates the envelope status. Valid values are:  * sent - The envelope is sent to the recipients.  * created - The envelope is saved as a draft and can be modified and sent later.
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets triggered_date
     *
     * @return string
     */
    public function getTriggeredDate()
    {
        return $this->container['triggered_date'];
    }

    /**
     * Sets triggered_date
     *
     * @param string $triggered_date 
     *
     * @return $this
     */
    public function setTriggeredDate($triggered_date)
    {
        $this->container['triggered_date'] = $triggered_date;

        return $this;
    }

    /**
     * Gets trigger_on_item
     *
     * @return string
     */
    public function getTriggerOnItem()
    {
        return $this->container['trigger_on_item'];
    }

    /**
     * Sets trigger_on_item
     *
     * @param string $trigger_on_item 
     *
     * @return $this
     */
    public function setTriggerOnItem($trigger_on_item)
    {
        $this->container['trigger_on_item'] = $trigger_on_item;

        return $this;
    }

    /**
     * Gets workflow_step_id
     *
     * @return string
     */
    public function getWorkflowStepId()
    {
        return $this->container['workflow_step_id'];
    }

    /**
     * Sets workflow_step_id
     *
     * @param string $workflow_step_id 
     *
     * @return $this
     */
    public function setWorkflowStepId($workflow_step_id)
    {
        $this->container['workflow_step_id'] = $workflow_step_id;

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

