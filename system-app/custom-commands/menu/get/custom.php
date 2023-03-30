<?php

/**
 * Получение схемы меню
 */

$menuScheme = [];

if ( file_exists( $API::$configs[ "paths" ][ "public_app" ] . "/menu.json" ) )
    $menuScheme = file_get_contents( $API::$configs[ "paths" ][ "public_app" ] . "/menu.json" );
else
    $menuScheme = file_get_contents( $API::$configs[ "paths" ][ "system_app" ] . "/menu.json" );

if ( !$menuScheme ) $API->returnResponse( [] );

$menuScheme = json_decode( $menuScheme, true );


$API->returnResponse( $menuScheme[ "side" ] );