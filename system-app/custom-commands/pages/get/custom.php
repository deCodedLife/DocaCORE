<?php

/**
 * Подключение вспомогательных функций
 */
require_once( "functions/component-types.php" );
require_once( "functions/block-types.php" );


/**
 * Генерация структурного блока
 *
 * @param $structureBlock  array  Схема структурного блока
 *
 * @return array
 */
function generateStructureBlock ( $structureBlock ) {

    global $API;
    global $pageDetail;


    /**
     * Сформированный блок страницы
     */

    $responseBlock = [
        "type" => $structureBlock[ "type" ],
        "size" => $structureBlock[ "size" ],
        "settings" => $structureBlock[ "settings" ],
        "components" => []
    ];

    if ( $structureBlock[ "hook" ] ) $responseBlock[ "hook" ] = $structureBlock[ "hook" ];


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
        case "schedule_list":

            /**
             * Списки: https://tppr.me/JELn0
             */


            /**
             * Получение структуры списка
             */
            $listStructure = processingBlockType_list( $structureBlock );
            if ( !$listStructure[ "headers" ] ) { $isContinue = true; break; }

            /**
             * Указание заголовков списка
             */
            $responseBlock[ "settings" ][ "headers" ] = $listStructure[ "headers" ];
            $responseBlock[ "settings" ][ "filters" ] = (object) $listStructure[ "filters" ];

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
            $formStructure = processingBlockType_form( $structureBlock );
            if ( !$formStructure || !$formStructure[ "areas" ] ) { $isContinue = true; break; }

            /**
             * Указание типа формы
             */
            $responseBlock[ "settings" ][ "type" ] = $formStructure[ "type" ];

            /**
             * Указание областей формы
             */
            $responseBlock[ "settings" ][ "areas" ] = $formStructure[ "areas" ];


            /**
             * Получение типа команды формы
             */

            if ( $formStructure[ "command_type" ] ) {

                $responseBlock[ "settings" ][ "command_type" ] = $formStructure[ "command_type" ];

            } else {

                $formCommandScheme = $API->loadCommandScheme(
                    $structureBlock[ "settings" ][ "object" ] . "/" . $structureBlock[ "settings" ][ "command" ]
                );

                $responseBlock[ "settings" ][ "command_type" ] = $formCommandScheme[ "type" ];

            } // if. $formStructure[ "command_type" ]

            break;

        case "tabs":

            /**
             * Обработка табов
             */
            foreach ( $structureBlock[ "settings" ] as $tabKey => $tab ) {

                /**
                 * Обработка структуры таба
                 */
                foreach ( $tab[ "body" ] as $tabBlockKey => $tabBlock )
                    $structureBlock[ "settings" ][ $tabKey ][ "body" ][ $tabBlockKey ] = generateStructureBlock( $tabBlock );

            } // foreach. $structureBlock[ "settings" ]

            $responseBlock[ "settings" ] = $structureBlock[ "settings" ];

            break;

        case "analytic_widgets":

            /**
             * Виджеты аналитики
             */

            $responseBlock[ "settings" ] = processingBlockType_analyticWidgets( $structureBlock );

            break;

        case "calendar":

            /**
             * Календарь
             */

            $responseBlock[ "settings" ] = processingBlockType_calendar( $structureBlock );

            break;

    } // switch. $structureBlock[ "type" ]

    if ( $isContinue ) return [];


    /**
     * Обход типов компонентов
     */

    foreach ( $structureBlock[ "components" ] as $structureComponentType => $structureComponents ) {

        /**
         * Обработка одиночных компонентов
         */

        $isContinue = false;

        switch ( $structureComponentType ) {

            case "search":

                $responseBlock[ "components" ][ $structureComponentType ] = $structureComponents;

                $isContinue = true;
                break;

        } // switch. $structureComponentType

        if ( $isContinue ) continue;


        /**
         * Обработка множественных компонентов
         */

        foreach ( $structureComponents as $structureComponent ) {

            if ( !$structureComponent[ "required_permissions" ] )
                $structureComponent[ "required_permissions" ] = [];


            /**
             * Проверка доступов
             */
            if ( !$API->validatePermissions( $structureComponent[ "required_permissions" ] ) ) continue;


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

                    $responseComponent[ "title" ] = $structureComponent[ "title" ];
                    $responseComponent[ "settings" ][ "list" ] = processingComponentType_filter( $structureComponent );

                    break;

                case "buttons":

                    /**
                     * Сформированное тело запроса
                     */
                    $scriptBody = [];

                    if ( $structureComponent[ "settings" ][ "data" ] ) {

                        foreach ( $structureComponent[ "settings" ][ "data" ] as $scriptPropertyKey => $scriptProperty ) {

                            $scriptBody[ $scriptPropertyKey ] = $scriptProperty;


                            if ( $scriptProperty[ 0 ] === ":" ) {

                                /**
                                 * Обработка переменной
                                 */

                                /**
                                 * Получение переменной в строке
                                 */
                                $stringVariable = substr( $scriptProperty, 1 );


                                /**
                                 * Получение значения из списка
                                 */
                                if ( gettype( $pageDetail[ "row_detail" ][ $stringVariable ] ) === "array" )
                                    $pageDetail[ "row_detail" ][ $stringVariable ] = $pageDetail[ "row_detail" ][ $stringVariable ][ 0 ]->value;

                                /**
                                 * Формирование строки
                                 */
                                $scriptBody[ $scriptPropertyKey ] = (int) $pageDetail[ "row_detail" ][ $stringVariable ];

                            } // if. $widgetFilter[ "value" ][ 0 ] === ":"

                        } // foreach. $structureComponent[ "settings" ][ "body" ]


                        /**
                         * Обновление схемы запроса
                         */
                        $responseComponent[ "settings" ][ "data" ] = $scriptBody;

                    } // if. $structureComponent[ "settings" ][ "data" ]

                    break;

            } // switch. $structureComponentType


            /**
             * Добавление компонента в блок страницы
             */
            $responseBlock[ "components" ][ $structureComponentType ][] = $responseComponent;

        } // foreach. $structureComponents

    } // foreach. $structureBlock[ "components" ]


    return $responseBlock;

} // functions. generateStructureBlock



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
 * @hook
 * Формирование значений формы
 */

$formFieldValues = [];
$hookPath = $pageDetail[ "section" ];

if ( isset( $pageDetail[ "url" ][ 1 ] ) ) $hookPath .= "/" . $pageDetail[ "url" ][ 1 ];
else $hookPath .= "/index";

if ( file_exists( $public_customCommandDirPath . "/hooks/$hookPath/field-values.php" ) )
    require( $public_customCommandDirPath . "/hooks/$hookPath/field-values.php" );


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
 * Проверка доступов
 */
if ( !$API->validatePermissions( $pageScheme[ "required_permissions" ] ) )
    $API->returnResponse( "Нет доступа к странице", 403 );


/**
 * Получение детальной информации о запрошенной записи
 */

if ( $pageDetail[ "row_id" ] && $pageDetail[ "section" ] )
    $pageDetail[ "row_detail" ] = (array) $API->sendRequest(
        $pageDetail[ "section" ],
        "get",
        [ "id" => (int) $pageDetail[ "row_id" ] ],
        $_SERVER[ "SERVER_NAME" ]
    )[ 0 ];

if ( $pageDetail[ "section" ] === "settings" )
    $pageDetail[ "row_detail" ] = (array) $API->sendRequest(
        "settings",
        "get",
        [],
        $_SERVER[ "SERVER_NAME" ]
    );


/**
 * @hook
 * Формирование страницы
 */

$formConstructorHookPath = $API::$configs[ "paths" ][ "public_custom_commands" ] . "/pages/get/hooks/" . $pageDetail[ "section" ] . "/";
$formConstructorHookPath .= str_replace( ".json", ".php", $pageDetail[ "scheme_name" ] );

if ( file_exists( $formConstructorHookPath ) )
    require( $formConstructorHookPath );


/**
 * Формирование структуры страницы
 */

foreach ( $pageScheme[ "structure" ] as $structureBlockKey => $structureBlock ) {

    if ( !$structureBlock[ "required_permissions" ] ) $structureBlock[ "required_permissions" ] = [];


    /**
     * Проверка доступов
     */
    if ( !$API->validatePermissions( $structureBlock[ "required_permissions" ] ) ) continue;

    /**
     * Формирование блока
     */
    $responseBlock = generateStructureBlock( $structureBlock );

    /**
     * Добавление Блока страницы в ответ
     */
    if ( $responseBlock ) $response[ "data" ][] = $responseBlock;

} // foreach. $pageScheme->structure