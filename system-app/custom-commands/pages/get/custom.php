<?php

/**
 * Подключение вспомогательных функций
 */
require_once( "functions/component-types.php" );
require_once( "functions/block-types.php" );


/**
 * Сформированная структура страницы
 */
$response[ "data" ] = [];

/**
 * Детальная информация о странице
 */
$pageDetail = [

    /**
     * URL страницы
     */
    "url" => explode( "/", $requestData->page ),

    /**
     * Название схемы страницы
     */
    "scheme_name" => "index.json",

    /**
     * ID запрашиваемой записи
     */
    "row_id" => null,

    /**
     * Детальная информация о запрошенной записи
     */
    "row_detail" => null

]; // $pageDetail

/**
 * Получение раздела страницы
 */
$pageDetail[ "section" ] = $pageDetail[ "url" ][ 0 ];

/**
 * Получение схемы и записи страницы
 */
if ( isset( $pageDetail[ "url" ][ 1 ] ) ) $pageDetail[ "scheme_name" ] = $pageDetail[ "url" ][ 1 ] . ".json";
if ( isset( $pageDetail[ "url" ][ 2 ] ) ) $pageDetail[ "row_id" ] = $pageDetail[ "url" ][ 2 ];


/**
 * Формирование пути к схеме страницы
 */

$pageDetail[ "scheme_path" ] = $pageDetail[ "section" ] . "/" . $pageDetail[ "scheme_name" ];

$publicSchemePath = $API::$configs[ "paths" ][ "public_page_schemes" ] . "/" . $pageDetail[ "scheme_path" ];
$systemSchemePath = $API::$configs[ "paths" ][ "system_page_schemes" ] . "/" . $pageDetail[ "scheme_path" ];




/**
 * Подключение схемы страницы
 */

$pageScheme = [];

if ( file_exists( $publicSchemePath ) ) $pageScheme = file_get_contents( $publicSchemePath );
elseif ( file_exists( $systemSchemePath ) ) $pageScheme = file_get_contents( $systemSchemePath );
else $API->returnResponse( "Отсутствует схема страницы", 500 );


/**
 * Декодирование схемы запроса
 */
try {

    $pageScheme = json_decode( $pageScheme, true );
    if ( $pageScheme === null ) $API->returnResponse( "Ошибка обработки схемы страницы", 500 );

} catch ( Exception $error ) {

    $API->returnResponse( "Несоответствие схеме страницы", 500 );

} // try. json_decode. $pageScheme


/**
 * Получение детальной информации о запрошенной записи
 */
if ( $pageDetail[ "row_id" ] && $pageDetail[ "section" ] )
    $pageDetail[ "row_detail" ] = $API->DB->from( $pageDetail[ "section" ] )
        ->where( "id", $pageDetail[ "row_id" ] )
        ->limit( 1 )
        ->fetch();


/**
 * Формирование структуры страницы
 */

foreach ( $pageScheme[ "structure" ] as $structureBlock ) {

    /**
     * Сформированный блок страницы
     */
    $responseBlock = [
        "type" => $structureBlock[ "type" ],
        "size" => $structureBlock[ "size" ],
        "settings" => $structureBlock[ "settings" ],
        "components" => []
    ];

    /**
     * Игнорирование блока
     */
    $isContinue = false;


    /**
     * Обработка типов блоков
     */

    switch ( $structureBlock[ "type" ] ) {

        case "header":

            /**
             * Шапка страницы: https://tppr.me/kLiXG
             */


            /**
             * Формирование заголовка
             */
            $responseBlock[ "settings" ][ "title" ] = $API->generatingStringFromVariables(
                $structureBlock[ "settings" ][ "title" ], $pageDetail[ "row_detail" ]
            );

            break;

        case "list":

            /**
             * Списки: https://tppr.me/JELn0
             */


            /**
             * Получение заголовков списка
             */
            $listHeaders = processingBlockType_list( $structureBlock );
            if ( !$listHeaders ) { $isContinue = true; break; }

            /**
             * Указание заголовков списка
             */
            $responseBlock[ "settings" ][ "headers" ] = $listHeaders;

            break;

        case "form":
        case "info":

            /**
             * Формы: https://tppr.me/469PZ,
             * Детальная информация о записи
             */


            /**
             * Получение областей формы
             */
            $formAreas = processingBlockType_form( $structureBlock );
            if ( !$formAreas ) { $isContinue = true; break; }

            /**
             * Указание областей формы
             */
            $responseBlock[ "settings" ][ "areas" ] = $formAreas;

            break;

    } // switch. $structureBlock[ "type" ]

    if ( $isContinue ) continue;


    /**
     * Обход типов компонентов
     */

    foreach ( $structureBlock[ "components" ] as $structureComponentType => $structureComponents ) {

        /**
         * Обработка компонентов
         */

        foreach ( $structureComponents as $structureComponent ) {

            /**
             * Сформированный компонент страницы
             */
            $responseComponent = [
                "type" => $structureComponent[ "type" ],
                "settings" => $structureComponent[ "settings" ]
            ];


            /**
             * Обработка типов компонентов
             */

            switch ( $structureComponentType ) {

                case "filters":

                    $responseComponent[ "settings" ][ "list" ] = processingComponentType_filter( $structureComponent );

                    break;

            } // switch. $structureComponentType


            /**
             * Добавление компонента в блок страницы
             */
            $responseBlock[ "components" ][ $structureComponentType ][] = $responseComponent;

        } // foreach. $structureComponents

    } // foreach. $structureBlock[ "components" ]


    /**
     * Добавление Блока страницы в ответ
     */
    if ( $responseBlock ) $response[ "data" ][] = $responseBlock;

} // foreach. $pageScheme->structure