<?php

/**
 * Детальная информация о странице
 */

$pageDetail = [

    /**
     * URL страницы
     */
    "url" => explode( "/", $requestData->page ),

    /**
     * Раздел страницы
     */
    "section" => "",

    /**
     * Название схемы страницы
     */
    "scheme_name" => "index.json",

    /**
     * Запрашиваемая запись
     */
    "row_id" => null

]; // $pageDetail

$pageDetail[ "section" ] = $pageDetail[ "url" ][ 0 ];
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

if ( file_exists( $systemSchemePath ) ) $pageScheme = file_get_contents( $systemSchemePath );
elseif ( file_exists( $publicSchemePath ) ) $pageScheme = file_get_contents( $publicSchemePath );
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
 * Сформированная структура страницы
 */
$response[ "data" ] = [];


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

        case "list":

            /**
             * Заголовки списка.
             * Используются для вывода в админке
             */
            $listHeaders = [];


            /**
             * Проверка обязательных св-в
             */
            if ( !$structureBlock[ "settings" ][ "object" ] ) {
                $isContinue = true; break;
            }


            /**
             * Загрузка схемы объекта
             */

            $objectScheme = $API->loadObjectScheme( $structureBlock[ "settings" ][ "object" ] );

            if ( !$objectScheme ) {
                $isContinue = true; break;
            }


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


            /**
             * Указание заголовков списка
             */
            $responseBlock[ "settings" ][ "headers" ] = $listHeaders;

            break;

    } // switch. $structureBlock[ "type" ]

    if ( $isContinue ) continue;


    /**
     * Обработка типов компонентов
     */

    foreach ( $structureBlock[ "components" ] as $structureComponentType => $structureComponents ) {

        /**
         * Обработка типов компонентов
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