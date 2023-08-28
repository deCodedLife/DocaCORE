<?php

/**
 * @file Стандартная команда get.
 * Используется для вывода записей из базы данных
 */


/**
 * Проверка наличия таблицы в схеме запроса
 */
if ( !$objectScheme[ "table" ] ) $API->returnResponse( "Отсутствует таблица в схеме запроса", 500 );


/**
 * Лимит вывода
 */
$requestSettings[ "limit" ] = 15;
if ( gettype( $requestData->limit ) === "integer" ) $requestSettings[ "limit" ] = $requestData->limit;
if ( $commandScheme[ "no_limit" ] ) $requestSettings[ "limit" ] = 0;

/**
 * Страница вывода
 */
if ( $requestData->page > 1 ) $requestSettings[ "page" ] = $requestData->page - 1;

/**
 * Выбранные св-ва
 */
$selectProperties = [];
if ( $requestData->select ) $selectProperties[] = "id";


/**
 * Формирование фильтра
 */

if ( $requestData->id && ( gettype( $requestData->id ) === "integer" ) )
    $requestSettings[ "filter" ][ "id" ] = $requestData->id;

foreach ( $objectScheme[ "properties" ] as $schemeProperty ) {

    $propertyArticle = $schemeProperty[ "article" ];
    $propertyValue = $requestData->{$propertyArticle};


    /**
     * Учет select
     */
    if ( $requestData->select && in_array( $propertyArticle, $requestData->select ) )
        if ( !$schemeProperty[ "join" ] ) $selectProperties[] = $propertyArticle;


    /**
     * Игнорирование пустых св-в
     */
    if ( $propertyValue === null ) continue;

    /**
     * Игнорирование св-в без автозаполнения
     */
    if ( !$schemeProperty[ "is_autofill" ] ) continue;


    /**
     * Добавление модификатора
     */
    if ( $schemeProperty[ "article_modifier" ] ) $propertyArticle .= " " . $schemeProperty[ "article_modifier" ];


    if ( $schemeProperty[ "join" ] ) {

        /**
         * Св-во со связанной таблицей
         */

        $schemeProperty[ "join" ][ "value" ] = $propertyValue;
        $requestSettings[ "join_filter" ][] = $schemeProperty[ "join" ];

    } else {

        /**
         * Обновление фильтра записей
         */
        $requestSettings[ "filter" ][ $propertyArticle ] = $propertyValue;

    } // if. $schemeProperty[ "join" ]

} // foreach. $objectScheme[ "properties" ]


/**
 * Отправка запроса на получение записей
 */

try {

    /**
     * Кол-во пропускаемых записей
     */
    $offset = $requestSettings[ "page" ] * $requestSettings[ "limit" ];


    /**
     * Фильтр по связанным таблицам
     */

    $joinFilterRows = [];

    foreach ( $requestSettings[ "join_filter" ] as $joinFilter ) {

        $joinRows = $API->DB->from( $joinFilter[ "connection_table" ] )
            ->select( null )->select( $joinFilter[ "insert_property" ] )
            ->where( $joinFilter[ "filter_property" ], $joinFilter[ "value" ] );

        foreach ( $joinRows as $joinRow ) {

            $joinFilterRows[] = $joinRow[ $joinFilter[ "insert_property" ] ];

        } // foreach. $joinRows as $joinRow

    } // foreach. $return[ "join_filter" ] as $joinFilter

    $joinFilterRows = array_unique( $joinFilterRows );

    if ( $requestSettings[ "join_filter" ] && !$joinFilterRows ) $joinFilterRows = [ 0 ];


    /**
     * Сортировка
     */
    if ( $requestData->sort_by ) $requestSettings[ "sort_by" ] = $requestData->sort_by;
    if ( $requestData->sort_order ) $requestSettings[ "sort_order" ] = $requestData->sort_order;


    /**
     * Получение записей
     */

    $rows = $API->DB->from( $objectScheme[ "table" ] )
        ->orderBy( $requestSettings[ "sort_by" ] . " " . $requestSettings[ "sort_order" ] );

    if ( $objectScheme[ "is_trash" ] && !$requestSettings[ "filter" ][ "is_active" ] )
        $requestSettings[ "filter" ][ "is_active" ] = "Y";


    if ( $requestSettings[ "join_filter" ] ) $requestSettings[ "filter" ][ "id" ] = $joinFilterRows;
    if ( $selectProperties ) $rows->select( null )->select( $selectProperties );

    $rows->where( $requestSettings[ "filter" ] );


    /**
     * Получение кол-ва записей и страниц
     */

    $response[ "detail" ][ "rows_count" ] = $rows->count();

    if ( $requestSettings[ "limit" ] )
        $response[ "detail" ][ "pages_count" ] = ceil(
            $response[ "detail" ][ "rows_count" ] / $requestSettings[ "limit" ]
        );
    else $response[ "detail" ][ "pages_count" ] = 1;


    /**
     * Пагинация
     */
    $rows->limit( $requestSettings[ "limit" ] );
    $rows->offset( $offset );


    /**
     * Обработка ответа
     */

    $isCheckActive = true;
    if ( $requestSettings[ "filter" ][ "is_active" ] === "N" ) $isCheckActive = false;

    $response[ "data" ] = $API->getResponseBuilder( $rows, $objectScheme, $requestData->context, $isCheckActive );

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. $API->DB->insertInto