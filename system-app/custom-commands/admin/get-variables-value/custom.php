<?php

/**
 * Сформированный список значений
 */
$resultValues = [];


/**
 * Получение схемы объекта
 */
$objectScheme = $API->loadObjectScheme( $requestData->scheme_name );


/**
 * Исполнение стандартного get-запроса
 */

$rowDetail = $API->sendRequest( $requestData->scheme_name, "get", [
    "id" => $requestData->row_id
] );

if ( !$rowDetail ) $API->returnResponse( [] );

$rowDetail = $rowDetail[ 0 ];


/**
 * Обработка значений
 */
foreach ( $rowDetail as $propertyArticle => $propertyValue ) {

    /**
     * Обработка списков
     */
    if ( isset( $propertyValue->title ) ) $propertyValue = $propertyValue->title;

    /**
     * Обработка связанных объектов
     */
    if ( gettype( $propertyValue ) === "array" ) {

        /**
         * Сформированное значение св-ва
         */
        $resultPropertyValue = [];


        /**
         * Получение схемы внутреннего св-ва объекта
         */
        foreach ( $objectScheme[ "properties" ] as $objectProperty ) {

            if ( $objectProperty[ "article" ] != $propertyArticle ) continue;


            /**
             * Схема внутреннего объекта
             */

            $innerObject = "";

            if ( $objectProperty[ "list_donor" ][ "table" ] ) $innerObject = $objectProperty[ "list_donor" ][ "table" ];
            if ( $objectProperty[ "join" ][ "donor_table" ] ) $innerObject = $objectProperty[ "join" ][ "donor_table" ];

            if ( !$innerObject ) continue;


            /**
             * Получение детальной информации о внутренних записях
             */

            foreach ( $propertyValue as $innerPropertyValue ) {

                $innerRowDetail = $API->sendRequest( $innerObject, "get", [
                    "id" => $innerPropertyValue->value
                ] );

                if ( !$innerRowDetail ) $API->returnResponse( [] );

                $innerRowDetail = $innerRowDetail[ 0 ];


                /**
                 * Обработка значений внутренней записи
                 */

                foreach ( $innerRowDetail as $innerRowPropertyArticle => $innerRowPropertyValue ) {

                    if (
                        ( gettype( $innerRowPropertyValue ) === "array" ) ||
                        ( gettype( $innerRowPropertyValue ) === "object" )
                    ) continue;


                    $resultPropertyValue[ $innerRowPropertyArticle ] = $innerRowPropertyValue;

                } // foreach. $innerRowDetail

            } // foreach. $propertyValue

        } // foreach. $objectScheme


        $propertyValue = $resultPropertyValue;

    } // if. gettype( $propertyValue ) === "array"


    $resultValues[ $propertyArticle ] = $propertyValue;

} // foreach. $rowDetail


$API->returnResponse( $resultValues );