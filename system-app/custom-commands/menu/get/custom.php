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


/**
 * Добавление пользовательских пунктов меню
 */

if ( $userScheme ) {

    foreach ( $userScheme as $objectArticle => $object )
        if ( $object->title ) $menuScheme[ "side" ][] = [
            "title" => $object->title,
            "href" => $objectArticle,
            "children" => [],
            "icon" => "bullet-list"
        ];

} // if. $userScheme


$API->returnResponse( $menuScheme[ "side" ] );