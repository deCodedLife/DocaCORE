<?php

/**
 * @file
 * Обработка типов блоков
 */


/**
 * Обработка списков
 *
 * @param $structureBlock  object  Структура блока
 *
 * @return mixed
 */
function processingBlockType_list ( $structureBlock ) {

    global $API;


    /**
     * Заголовки списка.
     * Используются для вывода в админке
     */
    $listHeaders = [];


    /**
     * Проверка обязательных св-в
     */
    if ( !$structureBlock[ "settings" ][ "object" ] ) return false;


    /**
     * Загрузка схемы объекта
     */

    $objectScheme = $API->loadObjectScheme( $structureBlock[ "settings" ][ "object" ] );

    if ( !$objectScheme ) return false;


    /**
     * Формирование заголовков списка
     */

    foreach ( $objectScheme[ "properties" ] as $property ) {

        if ( $property[ "is_default_in_list" ] ) $listHeaders[] = [
            "title" => $property[ "title" ],
            "article" => $property[ "article" ],
            "type" => $property[ "field_type" ]
        ];

    } // foreach. $objectScheme[ "properties" ]


    return $listHeaders;

} // function. processingBlockType_list


/**
 * Обработка форм
 *
 * @param $structureBlock  object  Структура блока
 *
 * @return mixed
 */
function processingBlockType_form ( $structureBlock ) {

    global $API;
    global $pageDetail;


    /**
     * Области формы.
     * Используются для вывода в админке
     */
    $formAreas = $structureBlock[ "settings" ][ "areas" ];

    /**
     * Список св-в Объекта
     */
    $objectProperties = [];


    /**
     * Проверка обязательных св-в
     */
    if ( !$structureBlock[ "settings" ][ "object" ] ) return false;


    /**
     * Загрузка схемы объекта
     */
    $objectScheme = $API->loadObjectScheme( $structureBlock[ "settings" ][ "object" ] );
    if ( !$objectScheme ) return false;


    /**
     * Получение св-в Объекта
     */
    foreach ( $objectScheme[ "properties" ] as $property )
        $objectProperties[ $property[ "article" ] ] = $property;


    /**
     * Обработка областей формы
     */
    foreach ( $formAreas as $areaKey => $area ) {

        /**
         * Обработка блоков формы
         */
        foreach ( $area[ "blocks" ] as $blockKey => $block ) {

            /**
             * Обработанные поля формы
             */
            $blockFields = [];


            /**
             * Обработка полей формы
             */
            foreach ( $block[ "fields" ] as $field ) {

                /**
                 * Сформированное поле формы
                 */
                $blockField = [];


                /**
                 * Получение детальной информации о поле формы
                 */
                $fieldDetail = $objectProperties[ $field ];
                if ( !$fieldDetail ) continue;

                /**
                 * Проверка обязательности поля
                 */
                $isRequired = false;
                if ( in_array( $pageDetail[ "url" ][ 1 ], $fieldDetail[ "require_in_commands" ] ) ) $isRequired = true;


                /**
                 * Формирование поля формы
                 */

                $blockField = [
                    "title" => $fieldDetail[ "title" ],
                    "article" => $fieldDetail[ "article" ],
                    "data_type" => $fieldDetail[ "data_type" ],
                    "field_type" => $fieldDetail[ "field_type" ],
                    "settings" => $fieldDetail[ "settings" ],
                    "is_required" => $isRequired,
                    "is_disabled" => $fieldDetail[ "is_disabled" ]
                ];

                if ( $fieldDetail[ "min_value" ] ) $blockField[ "min_value" ] = $fieldDetail[ "min_value" ];
                if ( $fieldDetail[ "max_value" ] ) $blockField[ "max_value" ] = $fieldDetail[ "max_value" ];


                /**
                 * Обработка связанных таблиц
                 */

                if (
                    ( $fieldDetail[ "list_donor" ][ "table" ] || $fieldDetail[ "join" ][ "donor_table" ] ) &&
                    ( $fieldDetail[ "field_type" ] === "list" )
                ) {

                    /**
                     * Определение типа связанной таблицы
                     * (list_donor / join)
                     */
                    if ( !$fieldDetail[ "list_donor" ][ "table" ] ) {

                        $fieldDetail[ "list_donor" ][ "table" ] = $fieldDetail[ "join" ][ "donor_table" ];
                        $fieldDetail[ "list_donor" ][ "properties_title" ] = $fieldDetail[ "join" ][ "property_article" ];

                    } // if. !$fieldDetail[ "list_donor" ][ "table" ]


                    /**
                     * Загрузка схемы объекта связанной таблицы
                     */
                    $objectScheme = $API->loadObjectScheme( $fieldDetail[ "list_donor" ][ "table" ] );
                    if ( !$objectScheme ) continue;

                    /**
                     * Получение данных из связанной таблицы
                     */
                    $joinedTableRows = $API->DB->from( $fieldDetail[ "list_donor" ][ "table" ] );
                    if ( $objectScheme[ "is_trash" ] ) $joinedTableRows->where( "is_active", "Y" );


                    /**
                     * Обновление списка
                     */
                    foreach ( $joinedTableRows as $joinedTableRow )
                        $blockField[ "list" ][] = [
                            "title" => $joinedTableRow[ $fieldDetail[ "list_donor" ][ "properties_title" ] ],
                            "value" => $joinedTableRow[ "id" ]
                        ];

                } // if. $fieldDetail[ "field_type" ] === "list"


                /**
                 * Заполнение значения поля
                 */

                if ( $pageDetail[ "row_detail" ] ) {

                    $blockField[ "value" ] = $pageDetail[ "row_detail" ][ $fieldDetail[ "article" ] ];
                    settype( $blockField[ "value" ], $fieldDetail[ "data_type" ] );


                    /**
                     * Получение значения связанной таблицы
                     */

                    if ( $structureBlock[ "type" ] === "info" ) {

                        foreach ( $blockField[ "list" ] as $joinedTableRow ) {

                            if ( $joinedTableRow[ "value" ] != $blockField[ "value" ] ) continue;

                            $blockField[ "value" ] = $API->DB->from( $fieldDetail[ "list-donor" ][ "table" ] )
                                ->where( "id", $blockField[ "value" ] )
                                ->limit( 1 )
                                ->fetch()[ $fieldDetail[ "list-donor" ][ "properties_title" ] ];

                        } // foreach. $blockField[ "list" ]

                    } // if. $structureBlock[ "type" ] === "info"


                    /**
                     * Обработка системных св-в
                     */
                    if ( $fieldDetail[ "data_type" ] === "password" ) $blockField[ "value" ] = null;

                } // if. $pageDetail[ "row_detail" ]


                /**
                 * Учет поля формы
                 */
                $blockFields[] = $blockField;

            } // foreach. $block[ "fields" ]


            /**
             * Обновление полей формы
             */
            $formAreas[ $areaKey ][ "blocks" ][ $blockKey ][ "fields" ] = $blockFields;

        } // foreach. $area[ "blocks" ]

    } // foreach. $formAreas


    return $formAreas;

} // function. processingBlockType_form