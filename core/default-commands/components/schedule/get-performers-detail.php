<?php

/**
 * @file
 * Получение детальной информации об Исполнителях
 */

$performersRows = $API->DB->from( $requestData->performers_table )
    ->where( $performersFilter );

/**
 * @hook
 * Отработка стандартного get запроса
 */
if ( file_exists( $public_customCommandDirPath . "/hooks/get-performers.php" ) )
    require( $public_customCommandDirPath . "/hooks/get-performers.php" );


foreach ( $performersRows as $performersRow )
    $performersDetail[ $performersRow[ "id" ] ] = $performersRow[ $requestData->performers_title ];