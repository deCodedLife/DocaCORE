<?php

/**
 * @file
 * Конфигурация Приложения.
 * Уточнения к основной конфигурации API
 */


/**
 * URL админки
 */
$API::$configs[ "admin_url" ] = "docacrm.ru";

/**
 * Определение артикула компании
 */

if ( $API::$configs[ "company" ] == "oxapi-v3" ) $API::$configs[ "company" ] = "dev";


/**
 * База данных
 */

$API::$configs[ "db" ][ "host" ] = "localhost";
$API::$configs[ "db" ][ "user" ] = "doca";
$API::$configs[ "db" ][ "password" ] = 'zX3aN9tT0vdZ3f';
$API::$configs[ "db" ][ "name" ] = "doca_" . $API::$configs[ "company" ];