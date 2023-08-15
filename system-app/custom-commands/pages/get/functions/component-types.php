<?php

/**
 * @file
 * Обработка типов компонентов
 */


/**
 * Обработка фильтров
 *
 * @param $structureComponent  object  Структура компонента
 *
 * @return $array
 */
function processingComponentType_filter ( $structureComponent ) {

    global $API;


    /**
     * Сформированный список значений фильтра
     */
    $filterList = [];

    /**
     * Список св-в Объекта
     */
    $objectProperties = [];


    /**
     * Фиксированные значения фильтра
     */
    if ( $structureComponent[ "settings" ][ "list" ] ) return $structureComponent[ "settings" ][ "list" ];


    /**
     * Проверка обязательных параметров
     */
    if (
        !$structureComponent[ "settings" ][ "donor_object" ] ||
        !$structureComponent[ "settings" ][ "donor_property_title" ] ||
        !$structureComponent[ "settings" ][ "donor_property_value" ]
    ) return [];


    /**
     * Загрузка схемы объекта
     */
    $objectScheme = $API->loadObjectScheme( $structureComponent[ "settings" ][ "donor_object" ] );
    if ( !$objectScheme ) return false;


    /**
     * Получение св-в Объекта
     */
    foreach ( $objectScheme[ "properties" ] as $property )
        $objectProperties[ $property[ "article" ] ] = $property;


    /**
     * Получение записей из таблицы донора
     */
    $donorRows = $API->DB->from( $structureComponent[ "settings" ][ "donor_object" ] );
    if ( $objectScheme[ "is_trash" ] ) $donorRows->where( "is_active", "Y" );


    /**
     * Обработка записей из таблицы донора
     */

    foreach ( $donorRows as $donorRow ) {

        /**
         * Получение детальной информации о св-ве записи
         */

        $propertyDetail = $objectProperties[ $structureComponent[ "settings" ][ "donor_property_value" ] ];

        if ( $structureComponent[ "settings" ][ "donor_property_value" ] === "id" )
            $propertyDetail[ "data_type" ] = "integer";

        if ( !$propertyDetail ) continue;


        /**
         * Обработка нестандартных св-в
         */

        switch ( $structureComponent[ "settings" ][ "donor_property_title" ] ) {

            case "first_name":
            case "last_name":

                $fio = $donorRow[ "last_name" ] . " " . $donorRow[ "first_name" ];
                if ( $donorRow[ "patronymic" ] ) $fio .= " " .  $donorRow[ "patronymic" ];

                $donorRow[ $structureComponent[ "settings" ][ "donor_property_title" ] ] = $fio;

        } // switch. $structureComponent[ "settings" ][ "donor_property_title" ]


        settype(
            $donorRow[ $structureComponent[ "settings" ][ "donor_property_value" ] ],
            $propertyDetail[ "data_type" ]
        );

        $filterList[] = [
            "title" => $donorRow[ $structureComponent[ "settings" ][ "donor_property_title" ] ],
            "value" => $donorRow[ $structureComponent[ "settings" ][ "donor_property_value" ] ]
        ];

    } // foreach. $donorRows


    return $filterList;

} // function. processingComponentType_filter