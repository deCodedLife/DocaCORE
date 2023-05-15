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
            "title" => localizationText( $object->title ),
            "href" => $objectArticle,
            "children" => [],
            "icon" => "bullet-list"
        ];

} // if. $userScheme


/**
 * Локализация меню
 */

foreach ( $menuScheme[ "side" ] as $menuKey => $menuValue ) {

    $menuScheme[ "side" ][ $menuKey ][ "title" ] = localizationText( $menuValue[ "title" ] );

    foreach ( $menuValue[ "children" ] as $menuChildKey => $menuChildValue )
        $menuScheme[ "side" ][ $menuKey ][ "children" ][ $menuChildKey ][ "title" ] = localizationText( $menuChildValue[ "title" ] );

} // foreach. $menuScheme[ "side" ]


$API->returnResponse( $menuScheme[ "side" ] );