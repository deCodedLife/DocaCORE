<?php
/**
 * ReportInProductListItem
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
 * ReportInProductListItem Class Doc Comment
 *
 * @category    Class
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class ReportInProductListItem implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'reportInProductListItem';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'get_uri' => 'string',
        'last_scheduled_execution_date' => 'string',
        'last_scheduled_execution_success_date' => 'string',
        'report_customized_id' => 'string',
        'report_description' => 'string',
        'report_id' => 'string',
        'report_name' => 'string',
        'report_type' => 'string',
        'run_uri' => 'string',
        'save_uri' => 'string',
        'schedule_create_date' => 'string',
        'schedule_end_date' => 'string',
        'schedule_id' => 'string',
        'schedule_renew_duration_days' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'get_uri' => null,
        'last_scheduled_execution_date' => null,
        'last_scheduled_execution_success_date' => null,
        'report_customized_id' => null,
        'report_description' => null,
        'report_id' => null,
        'report_name' => null,
        'report_type' => null,
        'run_uri' => null,
        'save_uri' => null,
        'schedule_create_date' => null,
        'schedule_end_date' => null,
        'schedule_id' => null,
        'schedule_renew_duration_days' => null
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
        'get_uri' => 'getUri',
        'last_scheduled_execution_date' => 'lastScheduledExecutionDate',
        'last_scheduled_execution_success_date' => 'lastScheduledExecutionSuccessDate',
        'report_customized_id' => 'reportCustomizedId',
        'report_description' => 'reportDescription',
        'report_id' => 'reportId',
        'report_name' => 'reportName',
        'report_type' => 'reportType',
        'run_uri' => 'runUri',
        'save_uri' => 'saveUri',
        'schedule_create_date' => 'scheduleCreateDate',
        'schedule_end_date' => 'scheduleEndDate',
        'schedule_id' => 'scheduleId',
        'schedule_renew_duration_days' => 'scheduleRenewDurationDays'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'get_uri' => 'setGetUri',
        'last_scheduled_execution_date' => 'setLastScheduledExecutionDate',
        'last_scheduled_execution_success_date' => 'setLastScheduledExecutionSuccessDate',
        'report_customized_id' => 'setReportCustomizedId',
        'report_description' => 'setReportDescription',
        'report_id' => 'setReportId',
        'report_name' => 'setReportName',
        'report_type' => 'setReportType',
        'run_uri' => 'setRunUri',
        'save_uri' => 'setSaveUri',
        'schedule_create_date' => 'setScheduleCreateDate',
        'schedule_end_date' => 'setScheduleEndDate',
        'schedule_id' => 'setScheduleId',
        'schedule_renew_duration_days' => 'setScheduleRenewDurationDays'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'get_uri' => 'getGetUri',
        'last_scheduled_execution_date' => 'getLastScheduledExecutionDate',
        'last_scheduled_execution_success_date' => 'getLastScheduledExecutionSuccessDate',
        'report_customized_id' => 'getReportCustomizedId',
        'report_description' => 'getReportDescription',
        'report_id' => 'getReportId',
        'report_name' => 'getReportName',
        'report_type' => 'getReportType',
        'run_uri' => 'getRunUri',
        'save_uri' => 'getSaveUri',
        'schedule_create_date' => 'getScheduleCreateDate',
        'schedule_end_date' => 'getScheduleEndDate',
        'schedule_id' => 'getScheduleId',
        'schedule_renew_duration_days' => 'getScheduleRenewDurationDays'
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
        $this->container['get_uri'] = isset($data['get_uri']) ? $data['get_uri'] : null;
        $this->container['last_scheduled_execution_date'] = isset($data['last_scheduled_execution_date']) ? $data['last_scheduled_execution_date'] : null;
        $this->container['last_scheduled_execution_success_date'] = isset($data['last_scheduled_execution_success_date']) ? $data['last_scheduled_execution_success_date'] : null;
        $this->container['report_customized_id'] = isset($data['report_customized_id']) ? $data['report_customized_id'] : null;
        $this->container['report_description'] = isset($data['report_description']) ? $data['report_description'] : null;
        $this->container['report_id'] = isset($data['report_id']) ? $data['report_id'] : null;
        $this->container['report_name'] = isset($data['report_name']) ? $data['report_name'] : null;
        $this->container['report_type'] = isset($data['report_type']) ? $data['report_type'] : null;
        $this->container['run_uri'] = isset($data['run_uri']) ? $data['run_uri'] : null;
        $this->container['save_uri'] = isset($data['save_uri']) ? $data['save_uri'] : null;
        $this->container['schedule_create_date'] = isset($data['schedule_create_date']) ? $data['schedule_create_date'] : null;
        $this->container['schedule_end_date'] = isset($data['schedule_end_date']) ? $data['schedule_end_date'] : null;
        $this->container['schedule_id'] = isset($data['schedule_id']) ? $data['schedule_id'] : null;
        $this->container['schedule_renew_duration_days'] = isset($data['schedule_renew_duration_days']) ? $data['schedule_renew_duration_days'] : null;
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
     * Gets get_uri
     *
     * @return string
     */
    public function getGetUri()
    {
        return $this->container['get_uri'];
    }

    /**
     * Sets get_uri
     *
     * @param string $get_uri 
     *
     * @return $this
     */
    public function setGetUri($get_uri)
    {
        $this->container['get_uri'] = $get_uri;

        return $this;
    }

    /**
     * Gets last_scheduled_execution_date
     *
     * @return string
     */
    public function getLastScheduledExecutionDate()
    {
        return $this->container['last_scheduled_execution_date'];
    }

    /**
     * Sets last_scheduled_execution_date
     *
     * @param string $last_scheduled_execution_date 
     *
     * @return $this
     */
    public function setLastScheduledExecutionDate($last_scheduled_execution_date)
    {
        $this->container['last_scheduled_execution_date'] = $last_scheduled_execution_date;

        return $this;
    }

    /**
     * Gets last_scheduled_execution_success_date
     *
     * @return string
     */
    public function getLastScheduledExecutionSuccessDate()
    {
        return $this->container['last_scheduled_execution_success_date'];
    }

    /**
     * Sets last_scheduled_execution_success_date
     *
     * @param string $last_scheduled_execution_success_date 
     *
     * @return $this
     */
    public function setLastScheduledExecutionSuccessDate($last_scheduled_execution_success_date)
    {
        $this->container['last_scheduled_execution_success_date'] = $last_scheduled_execution_success_date;

        return $this;
    }

    /**
     * Gets report_customized_id
     *
     * @return string
     */
    public function getReportCustomizedId()
    {
        return $this->container['report_customized_id'];
    }

    /**
     * Sets report_customized_id
     *
     * @param string $report_customized_id 
     *
     * @return $this
     */
    public function setReportCustomizedId($report_customized_id)
    {
        $this->container['report_customized_id'] = $report_customized_id;

        return $this;
    }

    /**
     * Gets report_description
     *
     * @return string
     */
    public function getReportDescription()
    {
        return $this->container['report_description'];
    }

    /**
     * Sets report_description
     *
     * @param string $report_description 
     *
     * @return $this
     */
    public function setReportDescription($report_description)
    {
        $this->container['report_description'] = $report_description;

        return $this;
    }

    /**
     * Gets report_id
     *
     * @return string
     */
    public function getReportId()
    {
        return $this->container['report_id'];
    }

    /**
     * Sets report_id
     *
     * @param string $report_id 
     *
     * @return $this
     */
    public function setReportId($report_id)
    {
        $this->container['report_id'] = $report_id;

        return $this;
    }

    /**
     * Gets report_name
     *
     * @return string
     */
    public function getReportName()
    {
        return $this->container['report_name'];
    }

    /**
     * Sets report_name
     *
     * @param string $report_name 
     *
     * @return $this
     */
    public function setReportName($report_name)
    {
        $this->container['report_name'] = $report_name;

        return $this;
    }

    /**
     * Gets report_type
     *
     * @return string
     */
    public function getReportType()
    {
        return $this->container['report_type'];
    }

    /**
     * Sets report_type
     *
     * @param string $report_type 
     *
     * @return $this
     */
    public function setReportType($report_type)
    {
        $this->container['report_type'] = $report_type;

        return $this;
    }

    /**
     * Gets run_uri
     *
     * @return string
     */
    public function getRunUri()
    {
        return $this->container['run_uri'];
    }

    /**
     * Sets run_uri
     *
     * @param string $run_uri 
     *
     * @return $this
     */
    public function setRunUri($run_uri)
    {
        $this->container['run_uri'] = $run_uri;

        return $this;
    }

    /**
     * Gets save_uri
     *
     * @return string
     */
    public function getSaveUri()
    {
        return $this->container['save_uri'];
    }

    /**
     * Sets save_uri
     *
     * @param string $save_uri 
     *
     * @return $this
     */
    public function setSaveUri($save_uri)
    {
        $this->container['save_uri'] = $save_uri;

        return $this;
    }

    /**
     * Gets schedule_create_date
     *
     * @return string
     */
    public function getScheduleCreateDate()
    {
        return $this->container['schedule_create_date'];
    }

    /**
     * Sets schedule_create_date
     *
     * @param string $schedule_create_date 
     *
     * @return $this
     */
    public function setScheduleCreateDate($schedule_create_date)
    {
        $this->container['schedule_create_date'] = $schedule_create_date;

        return $this;
    }

    /**
     * Gets schedule_end_date
     *
     * @return string
     */
    public function getScheduleEndDate()
    {
        return $this->container['schedule_end_date'];
    }

    /**
     * Sets schedule_end_date
     *
     * @param string $schedule_end_date 
     *
     * @return $this
     */
    public function setScheduleEndDate($schedule_end_date)
    {
        $this->container['schedule_end_date'] = $schedule_end_date;

        return $this;
    }

    /**
     * Gets schedule_id
     *
     * @return string
     */
    public function getScheduleId()
    {
        return $this->container['schedule_id'];
    }

    /**
     * Sets schedule_id
     *
     * @param string $schedule_id 
     *
     * @return $this
     */
    public function setScheduleId($schedule_id)
    {
        $this->container['schedule_id'] = $schedule_id;

        return $this;
    }

    /**
     * Gets schedule_renew_duration_days
     *
     * @return string
     */
    public function getScheduleRenewDurationDays()
    {
        return $this->container['schedule_renew_duration_days'];
    }

    /**
     * Sets schedule_renew_duration_days
     *
     * @param string $schedule_renew_duration_days 
     *
     * @return $this
     */
    public function setScheduleRenewDurationDays($schedule_renew_duration_days)
    {
        $this->container['schedule_renew_duration_days'] = $schedule_renew_duration_days;

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

