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
    global $pageDetail;


    /**
     * Заголовки списка.
     * Используются для вывода в админке
     */
    $listHeaders = [];

    /**
     * Фильтр списка.
     * Передается в get запрос
     */
    $listFilters = [];


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
     * Формирование фильтров списка
     */

    foreach ( $structureBlock[ "settings" ][ "filters" ] as $listFilter ) {

        /**
         * Подстановка переменных
         */

        if ( $listFilter[ "value" ][ 0 ] === ":" ) {

            /**
             * Обработка переменной
             */

            /**
             * Получение переменной в строке
             */
            $stringVariable = substr( $listFilter[ "value" ], 1 );


            /**
             * Получение значения из списка
             */
            if ( gettype( $pageDetail[ "row_detail" ][ $stringVariable ] ) === "array" )
                $pageDetail[ "row_detail" ][ $stringVariable ] = $pageDetail[ "row_detail" ][ $stringVariable ][ 0 ]->value;

            /**
             * Формирование строки
             */
            $listFilter[ "value" ] = (int) $pageDetail[ "row_detail" ][ $stringVariable ];

        } // if. $widgetFilter[ "value" ][ 0 ] === ":"


        $listFilters[ $listFilter[ "property" ] ] = $listFilter[ "value" ];

    } // foreach. $structureBlock[ "settings" ][ "filters" ]


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


    return [
        "headers" => $listHeaders,
        "filters" => $listFilters
    ];

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
    global $formFieldValues;


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
            foreach ( $block[ "fields" ] as $fieldKey => $field ) {

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
                 * Проверка блокировки поля
                 */
                $isDisabled = false;
                if ( !in_array( $pageDetail[ "url" ][ 1 ], $fieldDetail[ "use_in_commands" ] ) ) $isDisabled = true;


                /**
                 * Формирование поля формы
                 */

                $blockField = [
                    "title" => $fieldDetail[ "title" ],
                    "article" => $fieldDetail[ "article" ],
                    "data_type" => $fieldDetail[ "data_type" ],
                    "field_type" => $fieldDetail[ "field_type" ],
                    "settings" => $fieldDetail[ "settings" ],
                    "search" => $fieldDetail[ "search" ],
                    "is_required" => $isRequired,
                    "is_disabled" => $isDisabled
                ];

                if ( $fieldDetail[ "min_value" ] ) $blockField[ "min_value" ] = $fieldDetail[ "min_value" ];
                if ( $fieldDetail[ "max_value" ] ) $blockField[ "max_value" ] = $fieldDetail[ "max_value" ];


                /**
                 * Обработка хуков
                 */
                if ( $fieldDetail[ "is_hook" ] )
                    $blockField[ "hook" ] = $objectScheme[ "table" ];


                /**
                 * Обработка связанных таблиц
                 */

                if (
                    ( $fieldDetail[ "list_donor" ][ "table" ] || $fieldDetail[ "join" ][ "donor_table" ] ) &&
                    ( $fieldDetail[ "field_type" ] === "list" )
                ) {

                    if ( $fieldDetail[ "joined_field" ] )
                        $blockField[ "joined_field" ] = $fieldDetail[ "joined_field" ];


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
                    $propertyObjectScheme = $API->loadObjectScheme( $fieldDetail[ "list_donor" ][ "table" ] );
                    if ( !$propertyObjectScheme ) continue;

                    /**
                     * Получение данных из связанной таблицы
                     */
                    $joinedTableRows = $API->DB->from( $fieldDetail[ "list_donor" ][ "table" ] );
                    if ( $propertyObjectScheme[ "is_trash" ] ) $joinedTableRows->where( "is_active", "Y" );


                    /**
                     * Обновление списка
                     */
                    foreach ( $joinedTableRows as $joinedTableRow ) {

                        /**
                         * Сформированный пункт списка
                         */
                        $joinedRow = [];

                        /**
                         * Название поля
                         */
                        $fieldTitle = $joinedTableRow[ $fieldDetail[ "list_donor" ][ "properties_title" ] ];


                        /**
                         * Нестандартные названия полей
                         */
                        switch ( $fieldDetail[ "list_donor" ][ "properties_title" ] ) {

                            case "first_name":
                            case "last_name":
                            case "patronymic":

                                /**
                                 * Получение ФИО
                                 */

                                $fio = [
                                    "first_name" => "",
                                    "last_name" => "",
                                    "patronymic" => ""
                                ];

                                foreach ( $propertyObjectScheme[ "properties" ] as $property ) {

                                    if (
                                        ( $property[ "article" ] === "first_name" ) ||
                                        ( $property[ "article" ] === "last_name" ) ||
                                        ( $property[ "article" ] === "patronymic" )
                                    ) $fio[ $property[ "article" ] ] = $joinedTableRow[ $property[ "article" ] ];

                                } // foreach. $propertyObjectScheme[ "properties" ]

                                $fieldTitle = "${fio[ "last_name" ]} ${fio[ "first_name" ]} ${fio[ "patronymic" ]}";

                                break;

                        } // switch. $fieldDetail[ "list_donor" ][ "properties_title" ]


                        /**
                         * Заполнение пункта списка
                         */

                        $joinedRow = [
                            "title" => $fieldTitle,
                            "value" => $joinedTableRow[ "id" ]
                        ];

                        if ( $fieldDetail[ "joined_field" ] )
                            $joinedRow[ "joined_field_value" ] = $joinedTableRow[ $fieldDetail[ "joined_field" ] ];


                        $blockField[ "list" ][] = $joinedRow;

                    } // foreach. $joinedTableRows

                } // if. $fieldDetail[ "field_type" ] === "list"

                /**
                 * Обработка кастомных списков
                 */
                if ( ( $fieldDetail[ "field_type" ] === "list" ) && $fieldDetail[ "custom_list" ] ) {

                    foreach ( $fieldDetail[ "custom_list" ] as $listItem ) {

                        $blockField[ "list" ][] = [
                            "title" => $listItem[ "title" ],
                            "value" => $listItem[ "value" ]
                        ];

                    } // foreach. $fieldDetail[ "custom_list" ]

                } // if. ( $fieldDetail[ "field_type" ] === "list" ) && $fieldDetail[ "custom_list" ]


                /**
                 * Заполнение значения поля
                 */

                if ( $pageDetail[ "row_detail" ] ) {

                    /**
                     * Получение значения поля
                     */
                    $blockField[ "value" ] = $pageDetail[ "row_detail" ][ $fieldDetail[ "article" ] ];

                    /**
                     * Обработка списков
                     */
                    switch ( gettype( $blockField[ "value" ] ) ) {

                        case "array":

                            $blockFieldValues = [];

                            foreach ( $blockField[ "value" ] as $blockFieldValue )
                                $blockFieldValues[] = $blockFieldValue->value;

                            $blockField[ "value" ] = $blockFieldValues;
                            
                            break;

                        case "object":
                            $blockField[ "value" ] = $blockField[ "value" ]->value;

                    } // switch. gettype( $blockField[ "value" ] )
                    

                    /**
                     * Перевод значения в указанный в схеме тип
                     */
                    settype( $blockField[ "value" ], $fieldDetail[ "data_type" ] );


                    /**
                     * Получение значения связанной таблицы
                     */
                    if (
                        !$blockField[ "value" ] &&
                        ( $blockField[ "data_type" ] === "array" )
                    ) {

                        /**
                         * Схема св-ва
                         */
                        $objectSchemeProperty = $objectProperties[ $blockField[ "article" ] ];

                        /**
                         * Значения св-ва
                         */
                        $objectPropertyValues = $API->DB->from( $objectSchemeProperty[ "join" ][ "connection_table" ] )
                            ->where( $objectSchemeProperty[ "join" ][ "insert_property" ], $pageDetail[ "row_detail" ][ "id" ] );


                        /**
                         * Добавление значений
                         */
                        foreach ( $objectPropertyValues as $objectPropertyValue )
                            $blockField[ "value" ][] = $objectPropertyValue[
                                $objectSchemeProperty[ "join" ][ "filter_property" ]
                            ];

                    } // if. !$blockField[ "value" ]


                    /**
                     * Добавление значения связанной таблицы
                     */
                    foreach ( $blockField[ "list" ] as $joinedTableRow ) {

                        if ( !$fieldDetail[ "list_donor" ][ "table" ] ) continue;
                        if ( $joinedTableRow[ "value" ] != $blockField[ "value" ] ) continue;

                        $blockField[ "value" ] = $API->DB->from( $fieldDetail[ "list_donor" ][ "table" ] )
                            ->where( "id", $blockField[ "value" ] )
                            ->limit( 1 )
                            ->fetch()[ "id" ];

                    } // foreach. $blockField[ "list" ]


                    /**
                     * Обработка системных св-в
                     */
                    if ( $fieldDetail[ "data_type" ] === "password" ) $blockField[ "value" ] = null;

                } // if. $pageDetail[ "row_detail" ]


                /**
                 * Заполнение значения поля из хука
                 */
                if ( $formFieldValues[ $fieldDetail[ "article" ] ] )
                    $blockField[ "value" ] = $formFieldValues[ $fieldDetail[ "article" ] ];


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


/**
 * Обработка виджетов аналитики
 *
 * @param $structureBlock  object  Структура блока
 *
 * @return mixed
 */
function processingBlockType_analyticWidgets ( $structureBlock ) {

    global $API;
    global $pageDetail;


    /**
     * Сформированные настройки виджета
     */
    $widgetSettings = [
        "widgets_group" => $structureBlock[ "settings" ][ "widgets_group" ],
        "filters" => []
    ];


    /**
     * Формирование фильтров списка
     */

    foreach ( $structureBlock[ "settings" ][ "filters" ] as $widgetFilter ) {

        /**
         * Подстановка переменных
         */

        if ( $widgetFilter[ "value" ][ 0 ] === ":" ) {

            /**
             * Обработка переменной
             */

            /**
             * Получение переменной в строке
             */
            $stringVariable = substr( $widgetFilter[ "value" ], 1 );


            /**
             * Получение значения из списка
             */
            if ( gettype( $pageDetail[ "row_detail" ][ $stringVariable ] ) === "array" )
                $pageDetail[ "row_detail" ][ $stringVariable ] = $pageDetail[ "row_detail" ][ $stringVariable ][ 0 ]->value;

            /**
             * Формирование строки
             */
            $widgetFilter[ "value" ] = (int) $pageDetail[ "row_detail" ][ $stringVariable ];

        } // if. $widgetFilter[ "value" ][ 0 ] === ":"


        $widgetSettings[ "filters" ][ $widgetFilter[ "property" ] ] = $widgetFilter[ "value" ];

    } // foreach. $structureBlock[ "settings" ][ "filters" ]


    return $widgetSettings;

} // function. processingBlockType_analyticWidgets


/**
 * Обработка календарей
 *
 * @param $structureBlock  object  Структура блока
 *
 * @return mixed
 */
function processingBlockType_calendar ( $structureBlock ) {

    global $API;
    global $pageDetail;


    /**
     * Сформированные настройки
     */
    $settings = [
        "object" => $structureBlock[ "settings" ][ "object" ],
        "filters" => []
    ];


    /**
     * Формирование фильтров
     */

    foreach ( $structureBlock[ "settings" ][ "filters" ] as $filter ) {

        /**
         * Подстановка переменных
         */

        if ( $filter[ "value" ][ 0 ] === ":" ) {

            /**
             * Обработка переменной
             */

            /**
             * Получение переменной в строке
             */
            $stringVariable = substr( $filter[ "value" ], 1 );


            /**
             * Получение значения из списка
             */
            if ( gettype( $pageDetail[ "row_detail" ][ $stringVariable ] ) === "array" )
                $pageDetail[ "row_detail" ][ $stringVariable ] = $pageDetail[ "row_detail" ][ $stringVariable ][ 0 ]->value;

            /**
             * Формирование строки
             */
            $filter[ "value" ] = (int) $pageDetail[ "row_detail" ][ $stringVariable ];

        } // if. $filter[ "value" ][ 0 ] === ":"


        $settings[ "filters" ][ $filter[ "property" ] ] = $filter[ "value" ];

    } // foreach. $structureBlock[ "settings" ][ "filters" ]


    return $settings;

} // function. processingBlockType_analyticWidgets