<?php

/**
 * @file
 * Получение системных компонентов
 */

if ( !$API::$configs[ "system_components" ] )
    $API::$configs[ "system_components" ] = [];


$API->returnResponse( $API::$configs[ "system_components" ] );