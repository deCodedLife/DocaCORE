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
        $API->DB->update( $objectScheme[ "table" ] )
            ->set( "is_active", "N" )
            ->where( [
                "id" => $requestData->id,
                "is_system" => "N"
            ] )
            ->execute();

    } else {

        /**
         * Удаление записи
         */
        $API->DB->deleteFrom( $objectScheme[ "table" ] )
            ->where( [
                "id" => $requestData->id,
                "is_system" => "N"
            ] )
            ->execute();

    } // if. $objectScheme[ "is_trash" ]

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. remove