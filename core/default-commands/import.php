<?php

if ( $requestData->context->template ) {

    ini_set( "display_errors", 1 );
//    require_once( $this::$configs[ "paths" ][ "libs" ] . "/phpExcel.php" );
    require_once( $API::$configs[ "paths" ][ "libs" ] . "/PhpSpreadsheet/Writer/Xls.php" );


    $filePath = "uploads/{$API::$configs[ "company" ]}/talons/test.xlsx";
    $fileUrl = "https://{$_SERVER[ "HTTP_HOST" ]}/$filePath";

    $API->returnResponse( $fileUrl );

    foreach ( $objectScheme[ "properties" ] as $property ) {



    }

}