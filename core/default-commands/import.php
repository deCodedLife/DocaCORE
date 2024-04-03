<?php

if ( $requestData->context->block == "exel" ) {

    if ( property_exists( $requestData->context, "select_properties" ) )
        require_once "./components/exel/get_properties.php";

    foreach ( $objectScheme[ "properties" ] as $property ) {



    }

}