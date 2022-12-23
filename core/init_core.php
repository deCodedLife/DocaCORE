<?php

/**
 * @file
 * Инициализация ядра
 */


class API {

    /**
     * Конфигурация
     */
    static public $configs = [

        /**
         * Пути
         */
        "paths" => [],

        /**
         * База данных
         */
        "db" => []

    ]; // $configs

    /**
     * Тело запроса
     */
    public $request = null;

    /**
     * База данных (PDO)
     */
    public $DB = null;

    /**
     * Подключение базы данных (MySQL)
     */
    public $DB_connection = null;

    /**
     * JWT объект
     */
    public $JWT = null;

    /**
     * JWT код
     */
    public $JWT_code = null;

    /**
     * Подключенные модули
     */
    public $modules = [];

    /**
     * Детальная информация о текущем Пользователе
     */
    static public $userDetail = null;


    /**
     * Возвращение ответа API на запрос
     *
     * @param $data    mixed    Тело ответа
     * @param $status  integer  Статус ответа
     * @param $detail  array    Дополнительная информация в запросе
     *
     * @return null
     */
    public function returnResponse ( $data = true, $status = 200, $detail = [] ) {

        /**
         * Формирование ответа на запрос
         */
        $result = [
            "status" => $status,
            "data" => $data,
            "detail" => $detail
        ];


        /**
         * Вывод ответа на запрос, и завершение работы скрипта
         */
        exit( json_encode( $result ) );

    } // function. returnResponse


    /**
     * Загрузка схемы команды
     *
     * @return mixed
     */
    public function loadCommandScheme () {

        /**
         * Схема метода
         */
        $scheme = null;


        /**
         * Формирование пути к схеме метода
         */

        $publicSchemePath = $this::$configs[ "paths" ][ "public_command_schemes" ] . "/";
        $publicSchemePath .= $this->request->object . "/" . $this->request->command . ".json";

        $systemSchemePath = $this::$configs[ "paths" ][ "system_command_schemes" ] . "/";
        $systemSchemePath .= $this->request->object . "/" . $this->request->command . ".json";


        /**
         * Подключение схемы метода
         */
        if ( file_exists( $publicSchemePath ) ) $scheme = file_get_contents( $publicSchemePath );
        elseif ( file_exists( $systemSchemePath ) ) $scheme = file_get_contents( $systemSchemePath );
        else $this->returnResponse( "Отсутствует схема команды", 500 );


        /**
         * Декодирование схемы метода
         */
        try {

            $scheme = json_decode( $scheme, true );

            if ( $scheme === null ) $this->returnResponse( "Ошибка обработки схемы команды", 500 );

        } catch ( Exception $error ) {

            $this->returnResponse( "Несоответствие схеме команды", 500 );

        } // try. json_decode. $scheme


        return $scheme;

    } // function. loadCommandScheme

    /**
     * Загрузка схемы объекта
     *
     * @return mixed
     */
    public function loadObjectScheme ( $objectSchemeArticle ) {

        /**
         * Схема объекта
         */
        $scheme = null;


        /**
         * Формирование пути к схеме объекта
         */
        $publicSchemePath = $this::$configs[ "paths" ][ "public_object_schemes" ] . "/$objectSchemeArticle.json";
        $systemSchemePath = $this::$configs[ "paths" ][ "system_object_schemes" ] . "/$objectSchemeArticle.json";


        /**
         * Подключение схемы объекта
         */
        if ( file_exists( $publicSchemePath ) ) $scheme = file_get_contents( $publicSchemePath );
        elseif ( file_exists( $systemSchemePath ) ) $scheme = file_get_contents( $systemSchemePath );
        else $this->returnResponse( "Отсутствует схема объекта $objectSchemeArticle", 500 );


        /**
         * Декодирование схемы объекта
         */
        try {

            $scheme = json_decode( $scheme, true );

            if ( $scheme === null ) $this->returnResponse( "Ошибка обработки схемы объекта", 500 );

        } catch ( Exception $error ) {

            $this->returnResponse( "Несоответствие схеме объекта", 500 );

        } // try. json_decode. $scheme


        return $scheme;

    } // function. loadObjectScheme


    /**
     * Пре-обработка запроса
     *
     * @param $objectScheme  object  Схема объекта, по которой будет проверяться запрос
     * @param $requestData   object  Тело запроса
     * @param $command       string  Команда запроса
     *
     * @return object
     */
    public function requestDataPreprocessor ( $objectScheme, $requestData, $command ) {

        /**
         * Обработанный запрос
         */
        $processedRequest = [];


        /**
         * Обработка системных параметров
         */

        if ( $requestData->id && ( gettype( $requestData->id ) === "integer" ) )
            $processedRequest[ "id" ] = $requestData->id;

        if ( $requestData->page && ( gettype( $requestData->page ) === "integer" ) )
            $processedRequest[ "page" ] = $requestData->page;

        if ( $requestData->limit && ( gettype( $requestData->limit ) === "integer" ) )
            $processedRequest[ "limit" ] = $requestData->limit;

        if ( $requestData->is_list && ( gettype( $requestData->is_list ) === "boolean" ) )
            $processedRequest[ "is_list" ] = $requestData->is_list;


        /**
         * Обход св-в в схеме объекта
         */
        foreach ( $objectScheme[ "properties" ] as $objectProperty ) {

            /**
             * Св-во в запросе
             */
            $requestProperty = $requestData->{ $objectProperty[ "article" ] };


            if ( $requestProperty === null ) {

                /**
                 * Проверка обязательных св-в
                 */

                if ( in_array( $command, $objectProperty[ "require_in_commands" ] ) )
                    $this->returnResponse(
                        "Отсутствует обязательное св-во '{$objectProperty[ "title" ]}'",
                        400
                    );

            } else {

                /**
                 * Игнорирование св-ва
                 */
                $isContinue = false;


                /**
                 * Проверка на использование поля в команде
                 */
                if ( !in_array( $command, $objectProperty[ "use_in_commands" ] ) ) continue;


                /**
                 * Обработка нестандартных типов
                 */

                switch ( $objectProperty[ "data_type" ] ) {

                    case "boolean":

                        /**
                         * Форматирование boolean в Y/N
                         */

                        if ( $requestData->{ $objectProperty[ "article" ] } === false )
                            $requestData->{ $objectProperty[ "article" ] } = "N";
                        elseif ( $requestData->{ $objectProperty[ "article" ] } === true )
                            $requestData->{ $objectProperty[ "article" ] } = "Y";
                        else
                            $isContinue = true;

                        break;

                    case "float":

                        /**
                         * Округление числа
                         */
                        $requestData->{ $objectProperty[ "article" ] } = round(
                            $requestData->{ $objectProperty[ "article" ] }, 2, PHP_ROUND_HALF_DOWN
                        );

                        break;

                    case "email":

                        /**
                         * Проверка правильности заполнения email
                         */
                        if ( !filter_var( $requestData->{ $objectProperty[ "article" ] }, FILTER_VALIDATE_EMAIL ) )
                            $this->returnResponse(
                                "Неправильно заполнен email",
                                400
                            );

                        /**
                         * Обработка email
                         */
                        $requestData->{ $objectProperty[ "article" ] } = mb_strtolower(
                            $requestData->{ $objectProperty[ "article" ] }
                        );

                        break;

                    case "password":

                        /**
                         * Проверка на минимальную длину
                         */
                        if ( mb_strlen( $requestData->{ $objectProperty[ "article" ] } ) < 6 )
                            $this->returnResponse(
                                "Пароль должен быть не короче 6 символов",
                                400
                            );

                        /**
                         * Кодирование пароля
                         */
                        $requestData->{ $objectProperty[ "article" ] } = md5(
                            $requestData->{ $objectProperty[ "article" ] }
                        );

                        break;

                    case "phone":

                        /**
                         * Очистка лишних символов
                         */
                        $requestData->{ $objectProperty[ "article" ] } = preg_replace(
                            '/[^0-9]/', "", $requestData->{ $objectProperty[ "article" ] }
                        );

                        /**
                         * Проверка длины строки
                         */
                        if ( mb_strlen( $requestData->{ $objectProperty[ "article" ] } ) !== 11 )
                            $this->returnResponse(
                                "Неправильный формат телефона",
                                400
                            );

                        break;

                } // switch. $objectProperty[ "data_type" ]

                if ( $isContinue ) continue;


                /**
                 * Проверка типов св-в
                 */

                if ( gettype( $requestProperty ) !== $objectProperty[ "data_type" ] ) {

                    /**
                     * Ошибка типа св-ва
                     */
                    $is_error = true;


                    switch ( gettype( $requestProperty ) ) {

                        case "integer":

                            if (
                                $objectProperty[ "data_type" ] == "array" &&
                                (
                                    ( $command === "get" ) ||
                                    ( $command === "schedule" )
                                )
                            ) $is_error = false;

                        case "float":
                        case "double":

                            if (
                                ( $objectProperty[ "data_type" ] === "float" ) ||
                                ( $objectProperty[ "data_type" ] === "double" )
                            ) $is_error = false;

                            break;

                        case "string":

                            if (
                                ( $objectProperty[ "data_type" ] === "date" ) ||
                                ( $objectProperty[ "data_type" ] === "datetime" ) ||
                                ( $objectProperty[ "data_type" ] === "password" ) ||
                                ( $objectProperty[ "data_type" ] === "email" ) ||
                                ( $objectProperty[ "data_type" ] === "phone" ) ||
                                ( $objectProperty[ "data_type" ] === "image" )
                            ) $is_error = false;

                            break;

                        case "array":

                            if (
                                ( $objectProperty[ "data_type" ] === "image" )
                            ) $is_error = false;

                            break;

                        case "object":

                            if (
                                ( $objectProperty[ "data_type" ] === "array" )
                            ) $is_error = false;

                            break;

                    } // switch. gettype( $requestProperty )


                    /**
                     * Не проверять пустые значения
                     */
                    if ( !$requestProperty ) $is_error = false;


                    if ( $is_error ) $this->returnResponse(
                        "Неверный тип параметра '{$objectProperty[ "title" ]}' ",
                        400
                    );

                } // if. gettype( $requestProperty ) !== $objectProperty[ "data_type" ]


                /**
                 * Проверка минимального/максимального значения.
                 * Для типов integer и string
                 */

                if ( $objectProperty[ "min-value" ] || $objectProperty[ "max-value" ] ) {

                    /**
                     * Учет типа параметра
                     */
                    switch ( $objectProperty[ "data_type" ] ) {

                        /**
                         * Проверка чисел
                         */
                        case "integer":

                            /**
                             * Минимальное значение
                             */
                            if ( $objectProperty[ "min-value" ] && ( $objectProperty[ "min-value" ] > $requestProperty ) )
                                $this->returnResponse(
                                    "Значение '{$objectProperty[ "title" ]}' не должно быть менее " .
                                    $objectProperty[ "min-value" ]
                                    , 400 );

                            /**
                             * Максимальное значение
                             */
                            if ( $objectProperty[ "max-value" ] && ( $objectProperty[ "max-value" ] < $requestProperty ) )
                                $this->returnResponse(
                                    "Значение '{$objectProperty[ "title" ]}' не должно быть больше " .
                                    $objectProperty[ "max-value" ]
                                    , 400 );

                            break;

                        /**
                         * Проверка строк
                         */
                        case "string":

                            /**
                             * Минимальное значение
                             */
                            if ( $objectProperty[ "min-value" ] && ( $objectProperty[ "min-value" ] > strlen( $requestProperty ) ) )
                                $this->returnResponse(
                                    "Длина '{$objectProperty[ "title" ]}' не должна быть менее " .
                                    $objectProperty[ "min-value" ]
                                    , 400 );

                            /**
                             * Максимальное значение
                             */
                            if ( $objectProperty[ "max-value" ] && ( $objectProperty[ "max-value" ] < strlen( $requestProperty ) ) )
                                $this->returnResponse(
                                    "Длина '{$objectProperty[ "title" ]}' не должна быть больше " .
                                    $objectProperty[ "max-value" ]
                                    , 400 );

                            break;

                    } // switch. $param[ "data_type" ]

                } // if. $objectParam[ "min-value" ] || $objectParam[ "max-value" ]


                /**
                 * Проверка кастомных списков
                 */

                if ( $objectProperty[ "custom_list" ] ) {

                    $is_error = true;

                    foreach ( $objectProperty[ "custom_list" ] as $customListItem )
                        if ( $requestProperty === $customListItem[ "value" ] ) $is_error = false;

                    if ( $is_error ) $this->returnResponse(
                        "Недопустимое значение '{$objectProperty[ "title" ]}'", 400
                    );

                } // if. $objectParam[ "custom_list" ]


                /**
                 * Добавление св-ва в запрос
                 */
                $processedRequest[ $objectProperty[ "article" ] ] = $requestData->{ $objectProperty[ "article" ] };

            } // if. $requestProperty === null

        } // foreach. $objectScheme[ "properties" ]


        return (object) $processedRequest;

    } // function. requestDataPreprocessor


    /**
     * Обработка ответа на запросы типа get
     *
     * @param $rows          array    Строки для вывода
     * @param $objectScheme  object   Схема объекта
     * @param $is_list       boolean  Выводятся ли записи в списке
     *
     * @return array
     */
    public function getResponseBuilder ( $rows, $objectScheme, $is_list = false ) {

        /**
         * Ответ на запрос
         */
        $response = [];


        /**
         * Обработка записей
         */
        foreach ( $rows as $row ) {

            /**
             * Обработка системных параметров
             */

            if ( $row[ "id" ] ) $row[ "id" ] = (int) $row[ "id" ];


            /**
             * Обработка нестандартных типов данных
             */
            foreach ( $objectScheme[ "properties" ] as $property ) {

                switch ( $property[ "data_type" ] ) {

                    case "boolean":

                        if ( $row[ $property[ "article" ] ] === "Y" ) $row[ $property[ "article" ] ] = true;
                        else $row[ $property[ "article" ] ] = false;

                        break;

                    case "integer":

                        $row[ $property[ "article" ] ] = (int) $row[ $property[ "article" ] ];

                        break;

                    case "image":

                        /**
                         * Проверка на существование изображения
                         */
                        if (
                            !$row[ $property[ "article" ] ] ||
                            !file_exists( $this::$configs[ "paths" ][ "root" ] . $row[ $property[ "article" ] ] )
                        ) {
                            $row[ $property[ "article" ] ] = "";
                            break;
                        }


                        /**
                         * Определение пути к изображению
                         */
                        $imagePath = "https://" . $_SERVER[ "HTTP_HOST" ] . $row[ $property[ "article" ] ];

                        $row[ $property[ "article" ] ] = $imagePath;

                } // switch. $property[ "data_type" ]


                /**
                 * Списки
                 */
                if (
                    $property[ "list_donor" ][ "table" ] && $property[ "list_donor" ][ "properties_title" ]
                ) {

                    /**
                     * Получение детальной информации о записи
                     */
                    $detailRow = $this->DB->from( $property[ "list_donor" ][ "table" ] )
                        ->select( null )->select( [ "id", $property[ "list_donor" ][ "properties_title" ] ] )
                        ->where( [ "id" => $row[ $property[ "article" ] ] ] )
                        ->limit( 1 )
                        ->fetch();


                    if (
                        !$detailRow[ $property[ "list_donor" ][ "properties_title" ] ] ||
                        !$detailRow[ "id" ]
                    ) {

                        $row[ $property[ "article" ] ] = null;
                        continue;

                    }

                    $row[ $property[ "article" ] ] = [
                        "title" => $detailRow[ $property[ "list_donor" ][ "properties_title" ] ],
                        "value" => (int) $detailRow[ "id" ]
                    ];

                } // if. $property[ "list_donor" ][ "table" ] && $property[ "list_donor" ][ "properties_title" ]


                /**
                 * Кастомные списки
                 */
                if ( $property[ "custom_list" ] ) {

                    /**
                     * Получение заголовка св-ва
                     */

                    $propertyTitle = "";

                    foreach ( $property[ "custom_list" ] as $customProperty ) {

                        if ( $row[ $property[ "article" ] ] == $customProperty[ "value" ] )
                            $propertyTitle = $customProperty[ "title" ];

                    } // foreach. $param[ "custom_list" ]


                    $row[ $property[ "article" ] ] = [
                        "title" => $propertyTitle,
                        "value" => $row[ $property[ "article" ] ]
                    ];

                } // if. $property[ "list_donor" ]


                /**
                 * Связанные таблицы
                 */
                if ( $property[ "join" ] ) {

                    /**
                     * Данные записи из связанных таблиц
                     */
                    $joinDetailInputValues = [];


                    /**
                     * Получение связанных записей
                     */
                    $joinRows = $this->DB->from( $property[ "join" ][ "connection_table" ] )
                        ->select( null )->select( $property[ "join" ][ "filter_property" ] )
                        ->where( [ $property[ "join" ][ "insert_property" ] => $row[ "id" ] ] );

                    /**
                     * Обработка связанных записей
                     */
                    foreach ( $joinRows as $joinRow ) {

                        /**
                         * Получение детальной информации о связанной записи
                         */
                        $joinDetailRow = $this->DB->from( $property[ "join" ][ "donor_table" ] )
                            ->select( null )->select( [ "id", $property[ "join" ][ "property_article" ] ] )
                            ->where( [ "id" => $joinRow[ $property[ "join" ][ "filter_property" ] ] ] )
                            ->limit( 1 )
                            ->fetch();

                        $joinDetailInputValues[] = [
                            "title" => $joinDetailRow[ $property[ "join" ][ "property_article" ] ],
                            "value" => (int) $joinDetailRow[ "id" ]
                        ];

                    } // foreach. $joinRows as $joinRow


                    $row[ $property[ "article" ] ] = $joinDetailInputValues;

                } // if. $property[ "join" ]

            } // foreach. $objectScheme[ "properties" ]


            /**
             * Очистка системных параметров
             */
            unset( $row[ "password" ] );
            unset( $row[ "is_system" ] );
            unset( $row[ "is_active" ] );


            /**
             * Вывод кнопок и ссылок в списке
             */

            if ( $is_list && $objectScheme[ "action_buttons" ] ) {

                $row[ "row_href_type" ] = "update";


                foreach ( $objectScheme[ "action_buttons" ] as $actionButton ) {

                    /**
                     * Добавление кнопки в запись
                     */
                    $row[ "buttons" ][] = $this->buildActionButton( $actionButton, $row );

                } // foreach. $objectScheme[ "action_buttons" ]

            } // if. $is_list && $objectScheme[ "action_buttons" ]


            $response[] = $row;

        } // foreach. $rows


        return $response;

    } // function. getResponseBuilder


    /**
     * Сборка кнопки для вывода списка
     *
     * @param $button  object  Схема кнопки
     * @param $row     object  Запись, в которой находится кнопка
     */
    private function buildActionButton ( $button, $row ) {

        /**
         * Обработка типа кнопки
         */
        switch ( $button[ "type" ] ) {

            case "href":

                $button[ "settings" ][ "page" ] = $this->generatingStringFromVariables(
                    $button[ "settings" ][ "page" ], $row
                );

                break;

            case "script":

                /**
                 * Обход св-в запроса
                 */

                foreach ( $button[ "settings" ][ "data" ] as $buttonPropertyKey => $buttonPropertyValue ) {

                    if ( $buttonPropertyValue[ 0 ] === ":" )
                        $button[ "settings" ][ "data" ][ $buttonPropertyKey ] = $this->generatingStringFromVariables(
                            [ $buttonPropertyValue ], $row
                        );

                } // foreach. $button[ "settings" ][ "data" ]

                break;

        } // switch. $button[ "type" ]


        return $button;

    } // function. buildActionButton


    /**
     * Перевод изображения в формат WebP
     *
     * @param $imagePath  string  Путь к изображению
     */
    public function imageToWepP ( $imagePath ) {

        /**
         * Получение детальной информации об изображении
         */
        $imageDetails = pathinfo( $imagePath );

        /**
         * Получение названия изображения
         */
        $imageTitle = $imageDetails[ "filename" ];

        /**
         * Получение формата изображения
         */
        $imageExtension = $imageDetails[ "extension" ];

        /**
         * Получение пути к директории изображения
         */
        $imageDirPath = $imageDetails[ "dirname" ];


        /**
         * Создание изображения для функции imageWebp
         */

        $createdImage = null;

        switch ( $imageExtension ) {

            case "jpg":
            case "jpeg":
                $createdImage = imageCreateFromJpeg( $imagePath );
                break;

            case "png":
                $createdImage = imageCreateFromPng( $imagePath );
                break;

            case "gif":
                $createdImage = imageCreateFromGif( $imagePath );
                break;

        } // switch. $imageExtension

        $imageWebpPath = "$imageDirPath/$imageTitle.webp";

        imageWebp( $createdImage, $imageWebpPath );
        imagedestroy( $createdImage );


        if ( file_exists( $imageWebpPath ) ) {

            /**
             * Удаление не сжатого изображения
             */
            unlink( $imagePath );

            return true;

        } else return false;

    } // function. imageToWepP

    /**
     * Загрузка base-64 изображений
     *
     * @param $imageTitle   string  Название изображения
     * @param $base64Image  string  Base-64 изображение
     * @param $imageTable   string  Таблица объекта, к которому привязывается изображение
     */
    public function uploadBase64Image ( $imageTitle, $base64Image, $imageTable ) {

        /**
         * Чтение изображения
         */
        $imageSource = fopen( $base64Image, "r" );


        /**
         * Получение формата изображения
         */

        $imageExtension = substr(
            $base64Image, strpos( $base64Image, "/" ) + 1
        );
        $imageExtension = substr(
            $imageExtension, 0, strpos( $imageExtension, ";" )
        );

        if ( !$imageExtension ) return false;


        /**
         * Формирование названия изображения
         */
        $imageName = "$imageTitle.$imageExtension";

        /**
         * Формирование пути к директории с изображениями
         */
        $imagesDirPath = $this::$configs[ "paths" ][ "uploads" ] . "/" . $this::$configs[ "company" ] . "/$imageTable";
        if ( !is_dir( $imagesDirPath ) ) mkdir( $imagesDirPath );

        /**
         * Формирование пути к изображению
         */
        $imagePath = "$imagesDirPath/$imageName";


        /**
         * Сохранение изображения
         */

        $imagePathSource = fopen( $imagePath, "w" );
        stream_copy_to_stream( $imageSource, $imagePathSource );

        fclose( $imageSource );
        fclose( $imagePathSource );


        /**
         * Перевод изображения в формат WebP
         */
        if ( $imageExtension !== "webp" )
            if ( $this->imageToWepP( $imagePath ) ) $imagePath = "$imagesDirPath/$imageTitle.webp";

        return substr( $imagePath, strlen( $_SERVER[ "DOCUMENT_ROOT" ] ) );

    } // function. uploadBase64Image

    /**
     * Загрузка изображений по ссылке
     *
     * @param $imageUrl     string  Ссылка на изображение
     * @param $imageObject  string  Объект, к которому принадлежит изображение (clients, products, etc)
     * @param $imageTitle   string  Название изображения
     */
    public function uploadImageByUrl ( $imageUrl, $imageObject = "", $imageTitle = "" ) {

        /**
         * Получение информации об изображении
         */
        $imageDetail = pathinfo( $imageUrl );
        if ( !isset( $imageDetail[ "extension" ] ) || !$imageDetail[ "extension" ] ) return false;

        /**
         * Названия изображения по умолчанию
         */
        if ( !$imageTitle ) $imageTitle = date( "YmdHis" ) . rand( 10, 99 );


        /**
         * Получение пути к директории загрузок
         */
        $imagesDirPath = $_SERVER[ "DOCUMENT_ROOT" ] . "/uploads/" . $this::$configs[ "company" ];
        if ( !is_dir( $imagesDirPath ) ) mkdir( $imagesDirPath );

        /**
         * Получение пути к директории загрузок, для отдельного объекта
         */
        if ( $imageObject ) {

            $imagesDirPath .= "/$imageObject";
            if ( !is_dir( $imagesDirPath ) ) mkdir( $imagesDirPath );

        } // if. $imageObject

        /**
         * Получение пути к изображению на сервере
         */
        $imagePath = "$imagesDirPath/$imageTitle." . $imageDetail[ "extension" ];


        /**
         * Загрузка изображения
         */
        $uploadedImage = file_get_contents( $imageUrl );

        /**
         * Сохранение изображения на сервер
         */
        file_put_contents( $imagePath, $uploadedImage );

        /**
         * Перевод изображения в формат WebP
         */
        if ( $imageDetail[ "extension" ] !== "webp" )
            if ( $this->imageToWepP( $imagePath ) ) $imagePath = "$imagesDirPath/$imageTitle.webp";


        return substr( $imagePath, strlen( $_SERVER[ "DOCUMENT_ROOT" ] ) );

    } // function. uploadImageByUrl


    /**
     * Проверка JWT авторизации
     *
     * @return boolean
     */
    private function validateJWT () {

        /**
         * Проверка передачи JWT кода
         */
        if ( !$this->request->jwt ) return false;


        try {

            /**
             * Декодирование JWT
             */
            $JWT_decoded = $this->JWT::decode( $this->request->jwt, $this::$configs[ "jwt_key" ], [ "HS256" ] );


            /**
             * Проверка. Совпадает ли IP пользователя с переданным через JWT
             */
            if ( $JWT_decoded->ip !== $_SERVER[ "REMOTE_ADDR" ] )
                $this->returnResponse( "Не совпадает IP пользователя", 401 );


            /**
             * Возвращение данных пользователя
             */
            if ( $JWT_decoded ) return $JWT_decoded;

            $this->returnResponse( "Пользователь не авторизован", 403 );

        } catch ( Exception $e ) {

            $this->returnResponse( "Ошибка авторизации", 403 );

        } // try


        return true;

    } // function. validateJWT

    /**
     * Проверка прав
     *
     * @param $permissions  array  Требуемые права
     *
     * @return boolean
     */
    public function validatePermissions ( $permissions ) {

        /**
         * Проверка JWT авторизации
         */
        $this::$userDetail = $this->validateJWT();


        /**
         * Проверка требуемых доступов
         */

        if ( count( $permissions ) < 1 ) return true;

        foreach ( $permissions as $permission ) {

            $rolePermission = $this->DB->from( "permissions" )
                ->leftJoin( "roles_permissions ON roles_permissions.permission_id = permissions.id" )
                ->select( null )->select( [ "permissions.id" ] )
                ->where( [
                    "roles_permissions.role_id" => $this::$userDetail->role_id,
                    "permissions.article" => $permission
                ] )
                ->limit( 1 )
                ->fetch();

            if ( !$rolePermission ) return false;

        } // foreach. $permissions


        return true;

    } // function. validatePermissions

    /**
     * Проверка подключения необходимых модулей
     *
     * @param $modules  array  Требуемые модули
     *
     * @return boolean
     */
    public function validateModules ( $modules ) {

        if ( $modules ) return false;
        return true;

    } // function. validateModules


    /**
     * Формирование строк из переменных.
     * Позволяет собирать строки с использованием переменных
     *
     * @param $string     mixed   Строка
     * @param $rowDetail  object  Детальная информация о записи
     *
     * @return string
     */
    public function generatingStringFromVariables ( $string, $rowDetail ) {

        /**
         * Сформированная строка
         */
        $responseString = "";


        /**
         * Отмена обработки обычной строки
         */
        if ( gettype( $string ) === "string" ) return $string;
        if ( gettype( $string ) === "integer" ) return (int) $string;


        /**
         * Обработка схемы строки
         */

        foreach ( $string as $stringComponent ) {

            if ( $stringComponent[ 0 ] === ":" ) {

                /**
                 * Обработка переменной в строке
                 */

                /**
                 * Получение переменной в строке
                 */
                $stringVariable = substr( $stringComponent, 1 );


                /**
                 * Формирование строки
                 */
                if ( $rowDetail[ $stringVariable ] )
                    $responseString .= $rowDetail[ $stringVariable ];
                else $responseString .= "_";


                continue;

            } // if. $stringComponent[ 0 ] === ":"


            /**
             * Добавление простого текста в строку
             */
            $responseString .= $stringComponent;

        } // foreach. $string


        if ( ctype_digit( $responseString ) )
            $responseString = (int) $responseString;

        return $responseString;

    } // function. generatingStringFromVariables


    /**
     * Создание события.
     * Используется как аналог веб-соккетов.
     * В админке произойдет обновление указанного блока
     *
     * @param $blockArticle  string   Артикул блока, в котором произошло событие
     * @param $roleId        integer  ID роли, которой предназначено событие
     *
     * @return boolean
     */
    public function addEvent ( $blockArticle, $roleId = null ) {

        /**
         * Проверка обязательных параметров
         */
        if ( !$blockArticle ) return false;


        /**
         * Удаление старых событий
         */
        $this->DB->deleteFrom( "events" )
            ->where( [
                "table_name" => $blockArticle,
                "role_id" => $roleId
            ] )
            ->execute();


        $this->DB->insertInto( "events" )
            ->values( [
                "table_name" => $blockArticle,
                "role_id" => $roleId
            ] )
            ->execute();

        return true;

    } // function. addEvent


    /**
     * Добавление уведомления
     *
     * @param $type         string  Тип
     * @param $title        string  Название
     * @param $description  string  Описание
     * @param $status       string  Статус
     *
     * @return boolean
     */
    public function addNotification ( $type, $title, $description, $status = "info" ) {

        /**
         * Получение детальной информации о типе уведомлений
         */

        $notificationTypesDetail = $this->DB->from( "notificationTypes" )
            ->where( [ "article" => $type ] )
            ->limit( 1 )
            ->fetch();

        if ( !$notificationTypesDetail ) return false;


        /**
         * Получение ролей, которые получат уведомление
         */
        $notificationTypes_roles = $this->DB->from( "roles_notificationTypes" )
            ->where( [ "notificationType_id" => $notificationTypesDetail[ "id" ] ] );


        /**
         * Добавление уведомлений
         */

        foreach ( $notificationTypes_roles as $notificationType_role ) {

            $this->DB->insertInto( "notifications" )
                ->values( [
                    "title" => mb_substr( $title, 0, 75 ),
                    "description" => mb_substr( $description, 0, 255 ),
                    "status" => mb_substr( $status, 0, 15 ),
                    "role_id" => $notificationType_role[ "role_id" ]
                ] )
                ->execute();


            /**
             * Создание события
             */
            $this->addEvent( "notifications", $notificationType_role[ "role_id" ] );

        } // foreach. $notificationTypes_roles


        return true;

    } // function. addNotification


    /**
     * Отправка запроса в API
     *
     * @param $object   string  Объект запроса
     * @param $command  string  Команда запроса
     * @param $body     array   Тело запроса
     * @param $api_url  string  URL запроса
     *
     * @return mixed
     */
    public function sendRequest ( $object, $command, $body = [], $api_url = "" ) {

        if ( !$api_url && $_SERVER[ "REQUEST_URI" ] ) $api_url = $_SERVER[ "REQUEST_URI" ];


        /**
         * Формирование заголовков запроса
         */
        $headers = [
            "Content-Type: application/json",
            "Timeout: 3"
        ];


        /**
         * Формирование запроса
         */

        $data[ "object" ] = $object;
        $data[ "command" ] = $command;
        $data[ "data" ] = $body;

        $data = json_encode( $data );


        /**
         * Отправка запроса в API
         */

        $curl = curl_init( "https://$api_url" );

        curl_setopt_array( $curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "UTF-8",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $data,
        ) );

        $response = json_decode( curl_exec( $curl ) );


        if ( !$response ) return false;
        return $response->data;

    } // function. sendRequest

} // class. API

$API = new API;