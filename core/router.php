<?php

/**
 * @file
 * Маршрутизация
 *
 * Обработка и перенаправление запроса к нужной команде
 */


/**
 * Настройки запроса
 */
$requestSettings = [

    /**
     * Фильтрация.
     * Массив объектов (ключ - значение)
     */
    "filter" => [],

    /**
     * Связанные фильтры.
     * Массив объектов (ключ - значение)
     */
    "join_filter" => [],

    /**
     * Ограничение на кол-во затрагиваемых записей.
     * Если 0 - то ограничение не действует
     */
    "limit" => 0,

    /**
     * Пагинация. Игнорирует limit * page записей.
     * Если 0 - то пагинация не действует
     */
    "page" => 0,

    /**
     * Св-во, по которому будет проводиться сортировка
     */
    "sort_by" => "id",

    /**
     * Направление сортировки (asc/desc)
     */
    "sort_order" => "asc"

];


/**
 * Ответ на запрос
 */
$response = [

    /**
     * Результат выполнения запроса
     */
    "data" => true,

    /**
     * Код статуса запроса
     */
    "status" => 200,

    /**
     * Дополнительная информация о результате выполнения запроса.
     * Массив объектов (ключ - значение)
     */
    "detail" => []

];


/**
 * Получение запроса
 */
$API->request = json_decode( file_get_contents( "php://input" ) );


/**
 * Подключение интеграций
 */

if ( file_exists( $API::$configs[ "paths" ][ "core" ] . "/init_integrations.php" ) ) {

    require_once( $API::$configs[ "paths" ][ "core" ] . "/init_integrations.php" );

} else {

    $API->returnResponse( "Интеграции не отвечают", 500 );

} // if. file_exists. /core/init_integrations.php


/**
 * Обработка формы с файлами
 */

if ( !$API->request ) {

    /**
     * Перевод формы в формат OxAPI
     */

    foreach ( $_POST as $postPropertyKey => $postProperty ) {

        switch ( $postPropertyKey ) {

            case "jwt":
            case "object":
            case "command":

                $API->request->$postPropertyKey = $postProperty;
                break;

            default:

                $API->request->data->$postPropertyKey = $postProperty;

        } // switch. $postPropertyKey

    } // foreach. $_POST


    /**
     * Добавление файлов в тело запроса
     */
    foreach ( $_FILES as $propertyArticle => $file )
        $API->request->data->$propertyArticle = $file;

} // if. !$API->request


/**
 * Проверка обязательных параметров
 */
if ( !$API->request ) $API->returnResponse( "Пустой запрос", 400 );
if ( !$API->request->object ) $API->returnResponse( "Не указан объект в запросе", 400 );
if ( !$API->request->command ) $API->returnResponse( "Не указана команда в запросе", 400 );


/**
 * Проверка на недоступные символы
 */

if ( !preg_match( "#^[aA-zZ0-9\-_]+$#", $API->request->object ) )
    $API->returnResponse( "Недопустимые символы в запросе", 400 );

if ( !preg_match( "#^[aA-zZ0-9\-_]+$#", $API->request->command ) )
    $API->returnResponse( "Недопустимые символы в запросе", 400 );


/**
 * Подключение пользовательских схем
 */
$userSchemePath = $API::$configs[ "paths" ][ "public_user_schemes" ] . "/" . $API::$configs[ "company" ] . ".json";
$userScheme = json_decode( file_get_contents( $userSchemePath ) );


/**
 * Формирование команды для пользовательских схем
 */

$userSchemeCommand = [];

foreach ( $userScheme as $objectArticle => $object )
    if ( $API->request->object == $objectArticle ) $userSchemeCommand = [
        "object_scheme" => $objectArticle,
        "required_permissions" => [],
        "required_modules" => [],
        "type" => $API->request->command
    ];


/**
 * Загрузка схемы метода
 */
if ( !$userSchemeCommand ) $commandScheme = $API->loadCommandScheme( $API->request->object . "/" . $API->request->command );
else $commandScheme = $userSchemeCommand;


/**
 * Загрузка схемы объекта
 */
$objectScheme = $API->loadObjectScheme( $commandScheme[ "object_scheme" ] );

/**
 * Пре-обработка тела запроса
 */
$requestData = $API->requestDataPreprocessor( $objectScheme, $API->request->data, $API->request->command );


/**
 * Проверка прав
 */
if ( !$API->validatePermissions( $commandScheme[ "required_permissions" ] ) )
    $API->returnResponse( "Недостаточно прав", 403 );

/**
 * Проверка подключения необходимых модулей
 */
if ( !$API->validateModules( $commandScheme[ "required_modules" ] ) )
    $API->returnResponse( "Не подключены необходимые модули", 403 );


/**
 * Формирование пути к команде по умолчанию
 */
$defaultCommandPath = $API::$configs[ "paths" ][ "default_commands" ] . "/" . $commandScheme[ "type" ] . ".php";


/**
 * Путь к системной/публичной нестандартной команде
 */
$system_customCommandDirPath = $API::$configs[ "paths" ][ "system_custom_commands" ] . "/" . $API->request->object . "/" . $API->request->command;
$public_customCommandDirPath = $API::$configs[ "paths" ][ "public_custom_commands" ] . "/" . $API->request->object . "/" . $API->request->command;


/**
 * Формирование пути к директории нестандартной команды
 */

$customCommandDirPath = "";

if ( is_dir( $system_customCommandDirPath ) ) $customCommandDirPath = $system_customCommandDirPath;
elseif ( is_dir( $public_customCommandDirPath ) ) $customCommandDirPath = $public_customCommandDirPath;


/**
 * Формирование пути к префиксу команды
 */
$commandPrefixPath = "$customCommandDirPath/prefix.php";

/**
 * Формирование пути к нестандартной команде
 */
$customCommandPath = "$customCommandDirPath/custom.php";

/**
 * Формирование пути к постфиксу команды
 */
$commandPostfixPath = "$customCommandDirPath/postfix.php";


/**
 * Префикс команды
 */
if ( file_exists( $commandPrefixPath ) ) require_once( $commandPrefixPath );

/**
 * Инициализация команды
 */
if ( file_exists( $customCommandPath ) ) require_once( $customCommandPath );
else if ( file_exists( $defaultCommandPath ) ) require_once( $defaultCommandPath );
else $API->returnResponse( "Отсутствует тип команды", 500 );

/**
 * Постфикс команды
 */
if ( file_exists( $commandPostfixPath ) ) require_once( $commandPostfixPath );


/**
 * Ответ на запрос
 */
$API->returnResponse( $response[ "data" ], $response[ "status" ], $response[ "detail" ] );