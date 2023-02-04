<?php

/**
 * @file Стандартная команда remove.
 * Используется для удаления записей в базе данных
 */


try {

    if ( $objectScheme[ "is_trash" ] ) {

        /**
         * Перемещение записи в корзину
         */

        switch ( gettype( $requestData->id ) ) {

            case "integer":
            case "string":

                $API->DB->update( $objectScheme[ "table" ] )
                    ->set( "is_active", "N" )
                    ->where( [
                        "id" => $requestData->id,
                        "is_system" => "N"
                    ] )
                    ->execute();

                break;

            case "array":

                foreach ( $requestData->id as $id )
                    $API->DB->update( $objectScheme[ "table" ] )
                        ->set( "is_active", "N" )
                        ->where( [
                            "id" => $id,
                            "is_system" => "N"
                        ] )
                        ->execute();

                break;

        } // switch. gettype( $requestData->id )

    } else {

        /**
         * Удаление записи
         */

        switch ( gettype( $requestData->id ) ) {

            case "integer":
            case "string":

                $API->DB->deleteFrom( $objectScheme[ "table" ] )
                    ->where( [
                        "id" => $requestData->id,
                        "is_system" => "N"
                    ] )
                    ->execute();

                break;

            case "array":

                foreach ( $requestData->id as $id )
                    $API->DB->deleteFrom( $objectScheme[ "table" ] )
                        ->where( [
                            "id" => $id,
                            "is_system" => "N"
                        ] )
                        ->execute();

                break;

        } // switch. gettype( $requestData->id )

    } // if. $objectScheme[ "is_trash" ]


    /**
     * Добавление лога
     */

    $logData = $requestData;
    $logDescription = "Удалена запись ${objectScheme[ "title" ]} № $requestData->id";

    /**
     * @hook
     * Формирование описания логах
     */
    if ( file_exists( $public_customCommandDirPath . "/hooks/log.php" ) )
        require( $public_customCommandDirPath . "/hooks/log.php" );

    $API->addLog( [
        "table_name" => $objectScheme[ "table" ],
        "description" => $logDescription,
        "row_id" => $requestData->id
    ], $logData );

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. remove