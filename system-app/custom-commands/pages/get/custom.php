<?php

/**
 * Подключение вспомогательных функций
 */
require_once( "functions/component-types.php" );
require_once( "functions/block-types.php" );


/**
 * Язык по умолчанию
 */
if ( !$API::$configs[ "system_components" ][ "lang" ] ) $API::$configs[ "system_components" ][ "lang" ] = "ru";


/**
 * Загрузка языков
 */

$langs = [];

if ( file_exists(
    $API::$configs[ "paths" ][ "system_langs" ] . "/" . $API::$configs[ "system_components" ][ "lang" ] . ".json"
) ) {

    $lang = json_decode(
        file_get_contents(
            $API::$configs[ "paths" ][ "system_langs" ] . "/" . $API::$configs[ "system_components" ][ "lang" ] . ".json"
        )
    );

    foreach ( $lang as $langPhraseKey => $langPhraseValue ) $langs[ $langPhraseKey ] = $langPhraseValue;

}

if ( file_exists(
    $API::$configs[ "paths" ][ "public_langs" ] . "/" . $API::$configs[ "system_components" ][ "lang" ] . ".json"
) ) {

    $lang = json_decode(
        file_get_contents(
            $API::$configs[ "paths" ][ "public_langs" ] . "/" . $API::$configs[ "system_components" ][ "lang" ] . ".json"
        )
    );

    foreach ( $lang as $langPhraseKey => $langPhraseValue ) $langs[ $langPhraseKey ] = $langPhraseValue;

}


/**
 * Вывод текста
 *
 * @param $text  string  Текст для вывода
 *
 * @return string
 */
function localizationText ( $text ) {

    global $langs;


    /**
     * Подстановка языка
     */
    if ( $text[ 0 ] === "=" ) {

        /**
         * Ключ фразы
         */
        $phraseKey = substr( $text, 1 );

        /**
         * Обновление текста для вывода
         */
        $text = $langs[ $phraseKey ];
        if ( !$text ) $text = "..";

    } // if. $text[ 0 ] === "="


    return $text;

} // function. localizationText


/**
 * Локализация блоков
 *
 * @param $block  array  Блок
 *
 * @return array
 */
function blockLocalization ( $block ) {

    $resultBlock = [];


    foreach ( $block as $itemKey => $itemValue ) {

        switch ( gettype( $itemValue ) ) {

            case "string":
                $itemValue = localizationText( $itemValue );
                break;

            case "array":
            case "object":
                $itemValue = blockLocalization( $itemValue );
                break;

        } // switch. gettype( $itemValue )

        $resultBlock[ $itemKey ] = $itemValue;

    } // foreach. $block


    return $resultBlock;

} // function. blockLocalization


/**
 * Подстановка переменных
 *
 * @param $block       array  Блок
 * @param $pageDetail  array  Информация о странице
 *
 * @return array
 */
function variablesPushing ( $block, $pageDetail ) {

    $resultBlock = [];


    foreach ( $block as $propertyKey => $property ) {

        $resultBlock[ $propertyKey ] = $property;


        /**
         * Обработка переменных
         */
        if ( $property[ 0 ] === ":" ) {

            /**
             * Обработка переменной
             */

            /**
             * Получение переменной в строке
             */
            $stringVariable = substr( $property, 1 );


            /**
             * Получение значения из списка
             */
            if ( gettype( $pageDetail[ "row_detail" ][ $stringVariable ] ) === "array" )
                $pageDetail[ "row_detail" ][ $stringVariable ] = $pageDetail[ "row_detail" ][ $stringVariable ][ 0 ]->value;


            /**
             * Формирование строки
             */
            $resultBlock[ $propertyKey ] = (int) $pageDetail[ "row_detail" ][ $stringVariable ];

        } // if. $property[ 0 ] === ":"


        /**
         * Обработка внутренних св-в
         */
        if (
            ( gettype( $property ) === "array" ) ||
            ( gettype( $property ) === "object" )
        )
            $resultBlock[ $propertyKey ] = variablesPushing( $property, $pageDetail );

    } // foreach. $block


    return $resultBlock;

} // function. variablesPushing


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
    global $public_customCommandDirPath;


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
        case "logs":
        case "radio":
        case "accordion":
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
            if ( $structureBlock[ "type" ] == "list" ) $responseBlock[ "settings" ][ "headers" ] = $listStructure[ "headers" ];
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

        case "documents":

            /**
             * Календарь
             */

            $responseBlock[ "settings" ][ "fields_list" ] = processingBlockType_document( $structureBlock );

            break;

        case "tabs":

            /**
             * Обработка табов
             */
            foreach ( $structureBlock[ "settings" ] as $tabKey => $tab ) {

                /**
                 * Обработка структуры таба
                 */
                foreach ( $tab[ "body" ] as $tabBlockKey => $tabBlock ) {

                    /**
                     * @hook
                     * Формирование таба
                     */

                    $generatedTab = generateStructureBlock( $tabBlock );
                    $tabHookPath = $pageDetail[ "section" ];

                    if ( isset( $pageDetail[ "url" ][ 1 ] ) ) $tabHookPath .= "/" . $pageDetail[ "url" ][ 1 ];
                    else $tabHookPath .= "/index";

                    $tabHookPath .= "/tabs/$tabKey";

                    if ( file_exists( $public_customCommandDirPath . "/hooks/$tabHookPath/field-values.php" ) )
                        require( $public_customCommandDirPath . "/hooks/$tabHookPath/field-values.php" );

                    $structureBlock[ "settings" ][ $tabKey ][ "body" ][ $tabBlockKey ] = $generatedTab;

                } // foreach. $tab[ "body" ]

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

                    if ( $structureComponent[ "settings" ][ "data" ] ) {

                        $buttonData = variablesPushing(
                            $structureComponent[ "settings" ][ "data" ],
                            $pageDetail
                        );


                        /**
                         * Обновление схемы запроса
                         */
                        $responseComponent[ "settings" ][ "data" ] = $buttonData;

                    } // if. $structureComponent[ "settings" ][ "data" ]

                    break;

            } // switch. $structureComponentType


            /**
             * Добавление компонента в блок страницы
             */

            $responseBlock[ "components" ][ $structureComponentType ][] = $responseComponent;

        } // foreach. $structureComponents

    } // foreach. $structureBlock[ "components" ]


    return blockLocalization( $responseBlock );

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
 * Обработка пользовательских разделов
 */

if ( $userScheme ) {

    $userSchemeArticle = "";
    $userSchemeObject = [];

    foreach ( $userScheme as $objectArticle => $object )
        if ( $objectArticle == $pageDetail[ "section" ] ) {
            $userSchemeArticle = $objectArticle;
            $userSchemeObject = $object;
        }


    if ( $userSchemeObject && $userSchemeObject->title ) {

        switch ( $pageDetail[ "url" ][ 1 ] ) {

            case "add":
            case "update":

                /**
                 * Тексты в форме
                 */

                $formText = [
                    "description" => "Добавление",
                    "button" => "Добавить"
                ];

                if ( $pageDetail[ "url" ][ 1 ] === "update" ) $formText = [
                    "description" => "Редактирование",
                    "button" => "Сохранить"
                ];


                /**
                 * Получение значений формы
                 */

                if ( $pageDetail[ "url" ][ 1 ] === "update" ) $pageDetail[ "row_detail" ] = (array) $API->sendRequest(
                    $pageDetail[ "section" ],
                    "get",
                    [ "id" => (int) $pageDetail[ "row_id" ] ],
                    $_SERVER[ "SERVER_NAME" ]
                )[ 0 ];


                /**
                 * Формирование тела формы
                 */

                $formAreas = [];

                /**
                 * Добавление пользовательских областей
                 */
                foreach ( $userSchemeObject->areas as $area )
                    $formAreas[] = [
                        "size" => $area->size,
                        "blocks" => []
                    ];

                /**
                 * Добавление пользовательских блоков и св-в
                 */
                foreach ( $userSchemeObject->properties as $propertyArticle => $property ) {

                    $propertyValue = "";
                    if ( $pageDetail[ "row_detail" ][ "us__$propertyArticle" ] )
                        $propertyValue = $pageDetail[ "row_detail" ][ "us__$propertyArticle" ];

                    $formAreas[ $property->area_position ][ "blocks" ][ $property->block_position ][ "fields" ][] = [
                        "title" => $property->title,
                        "article" => $propertyArticle,
                        "data_type" => $property->field_type,
                        "field_type" => $property->field_type,
                        "is_required" => false,
                        "is_disabled" => false,
                        "is_visible" => true,
                        "value" => $propertyValue
                    ];

                    if ( !$formAreas[ $property->area_position ][ "blocks" ][ $property->block_position ][ "title" ] )
                        $formAreas[ $property->area_position ][ "blocks" ][ $property->block_position ][ "title" ] = "";

                } // foreach. $userSchemeObject->properties


                $API->returnResponse( [
                    [
                        "title" => "Шапка",
                        "type" => "header",
                        "size" => 4,
                        "settings" => [
                            "description" => $formText[ "description" ],
                            "title" => [ $userSchemeObject->title ]
                        ],
                        "components" => []
                    ],
                    [
                        "title" => "Форма",
                        "type" => "form",
                        "size" => 4,
                        "settings" => [
                            "object" => $userSchemeArticle,
                            "command" => $pageDetail[ "url" ][ 1 ],
                            "areas" => $formAreas,
                        ],
                        "components" => [
                            "buttons" => [[
                                "type" => "submit",
                                "settings" => [
                                    "title" => $formText[ "button" ],
                                    "background" => "dark",
                                    "href" => "$userSchemeArticle"
                                ]
                            ]]
                        ]
                    ]
                ] );

                break;

            default:

                /**
                 * Получение заголовков списка
                 */

                $listHeaders = [];

                foreach ( $userSchemeObject->properties as $propertyArticle => $property ) {

                    $listHeaders[] = [
                        "title" => $property->title,
                        "article" => "us__$propertyArticle",
                        "type" => $property->field_type
                    ];

                } // foreach. $userSchemeObject->properties


                $API->returnResponse( [
                    [
                        "title" => "Шапка",
                        "type" => "header",
                        "size" => 4,
                        "settings" => [
                            "description" => $userSchemeObject->title,
                            "title" => [ $userSchemeObject->title ]
                        ],
                        "components" => []
                    ], [
                        "title" => "Список",
                        "type" => "list",
                        "size" => 4,
                        "settings" => [
                            "object" => $userSchemeArticle,
                            "headers" => $listHeaders,
                            "filters" => []
                        ],
                        "components" => [
                            "buttons" => [[
                                "type" => "href",
                                "settings" => [
                                    "title" => "Добавить",
                                    "background" => "dark",
                                    "page" => "$userSchemeArticle/add"
                                ]
                            ]]
                        ]
                    ]
                ] );

                break;

        } // switch. $pageDetail[ "url" ][ 1 ]

    } // if. $userSchemeObject

} // if. $userScheme


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