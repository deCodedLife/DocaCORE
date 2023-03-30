<?php

/**
 * Сформированная схема пользователя
 */
$resultUserScheme = [];


/**
 * Получение текущей схемы пользователя
 */
$requestData->scheme_body = str_replace( "'", "\"", $requestData->scheme_body );
$currentScheme = json_decode( $requestData->scheme_body, true );


/**
 * Обход объектов
 */

foreach ( $currentScheme as $schemeObjectArticle => $schemeObject ) {

    /**
     * Обход областей формы
     */

    foreach ( $schemeObject[ "form" ] as $formAreaPosition => $formArea ) {

        /**
         * Добавление области в сформированную схему
         */
        if ( $formArea[ "type" ] == "custom" ) {

            $resultUserScheme[ $schemeObjectArticle ][ "areas" ][] = [
                "position" => $formAreaPosition,
                "size" => $formArea[ "size" ]
            ];

        } // if. $formArea[ "type" ] == "custom"


        /**
         * Обход блоков формы
         */

        foreach ( $formArea[ "blocks" ] as $formBlockPosition => $formBlock ) {

            /**
             * Добавление блока в сформированную схему
             */
            if ( $formBlock[ "type" ] == "custom" ) {

                $resultUserScheme[ $schemeObjectArticle ][ "blocks" ][] = [
                    "area_position" => $formAreaPosition,
                    "block_position" => $formBlockPosition
                ];

            } // if. $formBlock[ "type" ] == "custom"


            /**
             * Обход полей формы
             */

            foreach ( $formBlock[ "fields" ] as $formPropertyPosition => $formProperty ) {

                /**
                 * Добавление поля в сформированную схему
                 */
                if ( $formProperty[ "type" ] == "custom" ) {

                    $resultUserScheme[ $schemeObjectArticle ][ "properties" ][ $formProperty[ "article" ] ] = [
                        "title" => $formProperty[ "title" ],
                        "field_type" => $formProperty[ "field_type" ],
                        "area_position" => $formAreaPosition,
                        "block_position" => $formBlockPosition,
                        "property_position" => $formPropertyPosition
                    ];

                } // if. $formProperty[ "type" ] == "custom"

            } // foreach. $formBlock[ "fields" ]

        } // foreach. $formArea[ "blocks" ]

    } // foreach. $schemeObject[ "form" ]

} // foreach. $currentScheme


/**
 * Обновление схемы
 */
if (
    !file_put_contents(
        $API::$configs[ "paths" ][ "public_user_schemes" ] . "/" . $API::$configs[ "company" ] . ".json",
        json_encode( $resultUserScheme )
    )
) $API->returnResponse( false );


$API->returnResponse( true );