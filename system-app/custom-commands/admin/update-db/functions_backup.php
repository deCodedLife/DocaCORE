<?php

/**
 * @file
 * Вспомогательные функции
 */


/**
 * Обход схем Баз данных
 *
 * @param $dbSchemesDirPath  string  Путь к директории со схемами Баз данных
 *
 * @return array
 */

function readDatabaseSchemesDir ( $dbSchemesDirPath ) {

    /**
     * Пути к схемам Баз данных
     */
    $dbSchemePaths = [];


    /**
     * Обход директории схем Баз данных
     */

    $dbSchemesDir = opendir( $dbSchemesDirPath );

    while ( false !== ( $dbSchemeFile = readdir( $dbSchemesDir ) ) ) {

        if (
            !$dbSchemeFile ||
            ( $dbSchemeFile == "." ) ||
            ( $dbSchemeFile == ".." )
        ) continue;


        /**
         * Получение пути к схеме базы данных
         */
        $dbSchemeDirPath = "$dbSchemesDirPath/$dbSchemeFile";

        /**
         * Получение названия таблицы
         */
        $tableTitle = substr( $dbSchemeFile, 0, strpos( $dbSchemeFile, "." ) );


        /**
         * Добавление таблицы в очередь на обработку
         */
        $dbSchemePaths[] = [
            "table" => $tableTitle,
            "path" => $dbSchemeDirPath
        ];

    } // readdir. $dbSchemesDir


    return $dbSchemePaths;

} // function. readDatabaseSchemesDir


/**
 * Обработка схем Базы данных
 *
 * @param $dbSchemes  array  Схемы Баз данных
 *
 * @return boolean
 */
function updateDatabaseSchemes ( $dbSchemes ) {

    /**
     * Ядро API
     */
    global $API;

    /**
     * Обработанные базы данных
     */
    global $updatedDatabases;


    /**
     * Подключение базы данных
     */
    $DB_connection = mysqli_connect(
        $API::$configs[ "db" ][ "host" ],
        $API::$configs[ "db" ][ "user" ],
        $API::$configs[ "db" ][ "password" ],
        $API::$configs[ "db" ][ "name" ]
    );
    if ( !$DB_connection ) $API->returnResponse( "Не удалось подключится к базе данных" );


    /**
     * Обход схем Базы данных
     */

    foreach ( $dbSchemes as $dbScheme ) {

        /**
         * Структура текущей таблицы базы данных
         */
        $currentTableStructure = [];

        /**
         * Устаревшие колонки таблицы
         */
        $deprecatedColumns = [];


        /**
         * Проверка на обработанность таблицы
         */
        if ( in_array( $dbScheme[ "table" ], $updatedDatabases ) ) continue;


        /**
         * Подключение схемы таблицы
         */
        if ( file_exists( $dbScheme[ "path" ] ) ) $tableScheme = file_get_contents( $dbScheme[ "path" ] );
        else continue;

        /**
         * Декодирование схемы таблицы
         */
        try {

            $tableScheme = json_decode( $tableScheme, true );
            if ( $tableScheme === null ) continue;

        } catch ( Exception $error ) {
            continue;
        }


        /**
         * Проверка наличия обязательных св-в схемы
         */
        if ( !$tableScheme[ "properties" ] ) continue;

        /**
         * Добавление таблицы в список обновленных
         */
        $updatedDatabases[] = $dbScheme[ "table" ];


        /**
         * Получение структуры текущей таблицы базы данных
         */

        $tableColumns = mysqli_query( $DB_connection, "SHOW COLUMNS FROM " . $dbScheme[ "table" ] );

        foreach ( $tableColumns as $tableColumn ) {

            /**
             * Исключение системных полей
             */
            if ( $tableColumn[ "Field" ] == "id" ) continue;


            /**
             * Обновление информации о текущей структуры таблицы
             */
            $deprecatedColumns[] = $tableColumn[ "Field" ];
            $currentTableStructure[ $tableColumn[ "Field" ] ] = $tableColumn;

        } // foreach. $tableColumns


        /**
         * Создание таблицы
         */
        if ( !$tableColumns )
            mysqli_query(
                $DB_connection,
                "CREATE TABLE " . $dbScheme[ "table" ] . " ( id INT PRIMARY KEY AUTO_INCREMENT )"
            );


        /**
         * Добавление обязательных системных полей
         */

        $tableScheme[ "properties" ][] = [
            "title" => "Системное поле",
            "article" => "is_system",
            "type" => "char(1)",
            "is_required" => true,
            "default" => "N"
        ];


        /**
         * Обход св-в таблицы
         */

        foreach ( $tableScheme[ "properties" ] as $tablePropertyScheme ) {

            /**
             * Удаление св-ва из списка неактуальных
             */
            foreach ( $deprecatedColumns as $deprecatedColumnKey => $deprecatedColumn )
                if ( $deprecatedColumn == $tablePropertyScheme[ "article" ] )
                    unset( $deprecatedColumns[ $deprecatedColumnKey ] );


            /**
             * Формирование названия св-ва таблицы.
             * Используется для добавления/изменения в таблице базы данных
             */

            $tablePropertyTitle = $tablePropertyScheme[ "article" ] . " " . $tablePropertyScheme[ "type" ];

            if ( $tablePropertyScheme[ "is_required" ] === true ) $tablePropertyTitle .= " NOT NULL";
            else $tablePropertyTitle .= " NULL";


            if ( !isset( $currentTableStructure[ $tablePropertyScheme[ "article" ] ] ) ) {

                /**
                 * Добавление св-ва таблицы
                 */


                /**
                 * Формирование запроса на добавления св-ва таблицы
                 */
                $addPropertyQuery = "ALTER TABLE " . $dbScheme[ "table" ] . " ADD $tablePropertyTitle";
                $addPropertyQuery .= " COMMENT '" . $tablePropertyScheme[ "title" ] . "'";


                /**
                 * Добавление значения по умолчанию
                 */
                if (
                    ( $tablePropertyScheme[ "default" ] !== null ) &&
                    ( $tablePropertyScheme[ "default" ] !== "" )
                ) {

                    if ( $tablePropertyScheme[ "default" ] !== "CURRENT_TIMESTAMP" )
                        $tablePropertyScheme[ "default" ] = "'" . $tablePropertyScheme[ "default" ] . "'";

                    $addPropertyQuery .= " DEFAULT " . $tablePropertyScheme[ "default" ];

                } // if. $tablePropertyScheme[ "default" ] !== ""


                mysqli_query( $DB_connection, $addPropertyQuery );

            } else {

                /**
                 * Редактирование св-ва таблицы
                 */


                /**
                 * Структура текущего св-ва
                 */
                $currentPropertyStructure = $currentTableStructure[ $tablePropertyScheme[ "article" ] ];


                /**
                 * Перевод IS NULL в boolean тип
                 */
                if ( $currentPropertyStructure[ "Null" ] === "YES" ) $currentPropertyStructure[ "Null" ] = true;
                else $currentPropertyStructure[ "Null" ] = false;

                /**
                 * Проверка наличия изменений
                 */
                if (
                    ( $tablePropertyScheme[ "type" ] != $currentPropertyStructure[ "Type" ] ) ||
                    ( $tablePropertyScheme[ "is_required" ] == $currentPropertyStructure[ "Null" ] )
                ) {

                    /**
                     * Обновление св-ва
                     */

                    mysqli_query(
                        $DB_connection,
                        "ALTER TABLE " . $dbScheme[ "table" ] . " MODIFY COLUMN $tablePropertyTitle COMMENT '" . $tablePropertyScheme[ "title" ] . "'"
                    );

                } // if. Наличие изменений

                /**
                 * Проверка наличия изменений в значении по умолчанию
                 */
                if ( $tablePropertyScheme[ "default" ] != $currentPropertyStructure[ "Default" ] ) {

                    /**
                     * Формирование запроса на изменение значения по умолчанию
                     */

                    $updateDefaultValueQuery = "ALTER TABLE " . $dbScheme[ "table" ] . " ALTER COLUMN " . $tablePropertyScheme[ "article" ];

                    if ( $tablePropertyScheme[ "default" ] !== "" ) {

                        if ( $tablePropertyScheme[ "default" ] != "CURRENT_TIMESTAMP" )
                            $tablePropertyScheme[ "default" ] = "'" . $tablePropertyScheme[ "default" ] . "'";

                        $updateDefaultValueQuery .= " SET DEFAULT " . $tablePropertyScheme[ "default" ];

                    } else {

                        $updateDefaultValueQuery .= " DROP DEFAULT";

                    } // if. $tablePropertyScheme[ "default" ] !== ""


                    mysqli_query( $DB_connection, $updateDefaultValueQuery );

                } // if. $tablePropertyScheme[ "default" ] != $currentPropertyStructure[ "Default" ]

            } // if. isset( $currentTableStructure[ $tablePropertyScheme[ "article" ] ] )

        } // foreach. $tableScheme[ "properties" ]


        /**
         * Удаление устаревших св-в
         */
        foreach ( $deprecatedColumns as $deprecatedColumn )
            mysqli_query(
                $DB_connection, "ALTER TABLE " . $dbScheme[ "table" ] . " DROP COLUMN $deprecatedColumn"
            );


        /**
         * Проверка наличия обязательных записей
         */
        if ( !isset( $tableScheme[ "rows" ] ) ) continue;


        /**
         * Обработка обязательных записей
         */

        foreach ( $tableScheme[ "rows" ] as $row ) {

            /**
             * Проверка на наличие обязательных св-в
             */
            if ( !$tableScheme[ "rows_key" ] ) continue;
            if ( !$row[ $tableScheme[ "rows_key" ] ] ) continue;


            /**
             * Поиск записи
             */

            $rowDetailQuery = "SELECT id FROM `" . $dbScheme[ "table" ] . "`";
            $rowDetailQuery .= " WHERE " . $tableScheme[ "rows_key" ] . " = '" . $row[ $tableScheme[ "rows_key" ] ] . "'";
            $rowDetailQuery .= " LIMIT 1";

            $rowId = mysqli_fetch_array( mysqli_query( $DB_connection, $rowDetailQuery ) );

            if ( !$rowId ) $rowId = false;
            else $rowId = $rowId[ "id" ];


            if ( !$rowId ) {

                /**
                 * Добавление записи
                 */


                /**
                 * Названия полей
                 */
                $addRowQuery_articles = "";

                /**
                 * Значения полей
                 */
                $addRowQuery_values = "";


                /**
                 * Добавление полей в запрос
                 */

                foreach ( $row as $rowPropertyArticle => $rowPropertyValue ) {

                    $addRowQuery_articles .= "$rowPropertyArticle, ";

                    if ( $rowPropertyValue !== null ) $addRowQuery_values .= "'$rowPropertyValue', ";
                    else $addRowQuery_values .= "NULL, ";

                } // foreach. $row as $rowParam


                /**
                 * Обрезка лишних символов после добавления св-в
                 */
                $addRowQuery_articles = mb_substr( $addRowQuery_articles, 0, -2 );
                $addRowQuery_values = mb_substr( $addRowQuery_values, 0, -2 );


                mysqli_query(
                    $DB_connection,
                    "INSERT INTO `" . $dbScheme[ "table" ] . "` ( $addRowQuery_articles ) VALUES ( $addRowQuery_values )"
                );

            } else {

                /**
                 * Обновление существующей записи
                 */


                /**
                 * Запрос на обновление записи
                 */
                $updateRowQuery = "UPDATE `" . $dbScheme[ "table" ] . "` SET ";


                /**
                 * Добавление полей в запрос
                 */

                foreach ( $row as $rowPropertyArticle => $rowPropertyValue ) {

                    $updateRowQuery .= "$rowPropertyArticle = ";

                    if ( $rowPropertyValue !== null ) $updateRowQuery .= "'$rowPropertyValue', ";
                    else $updateRowQuery .= "NULL, ";

                } // foreach. $row as $rowParam


                /**
                 * Обрезка лишних символов после добавления полей
                 */
                $updateRowQuery = mb_substr( $updateRowQuery, 0, -2 );


                $updateRowQuery .= " WHERE id = $rowId LIMIT 1";

                mysqli_query( $DB_connection, $updateRowQuery );

            } // if. !$rowId

        } // foreach. $tableScheme[ "rows" ]

    } // foreach. $dbSchemes


    return true;

} // function. updateDatabaseSchemes