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
    $API->addLog( [
        "table_name" => $objectScheme[ "table" ],
        "description" => "Удалена запись ${objectScheme[ "title" ]} № $requestData->id",
        "row_id" => $requestData->id
    ], $requestData );

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. remove