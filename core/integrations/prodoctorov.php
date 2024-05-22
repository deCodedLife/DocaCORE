<?php

if ( property_exists( $API->request, "doctor" ) ) {

    $clientDetails = $API->request->client;
    $visitDetails = $API->request->appointment;
    $userDetails = $API->request->doctor;

    $clientsID = $API->sendRequest( "clients", "search", [ "search" => $clientDetails->mobile_phone ] );

    if ( empty( $clientsID ) ) {

        $request = [];
        $request[ "first_name" ] = $clientDetails->first_name;
        if ( property_exists( $clientDetails, "last_name" ) ) $request[ "last_name" ] =  $clientDetails->last_name;
        if ( property_exists( $clientDetails, "second_name" ) ) $request[ "patronymic" ] =  $clientDetails->second_name;
        if ( property_exists( $clientDetails, "mobile_phone" ) ) $request[ "phone" ] =  $clientDetails->mobile_phone;
        if ( property_exists( $clientDetails, "birthday" ) ) $request[ "birthday" ] =  $clientDetails->birthday;

        $advirtiseID = $API->DB->from( "advertise" )
            ->where(
                "( title like :title OR title like :online)",
                [
                    ":title" => "Продокторов",
                    ":online" => "Онлайн"
                ] )
            ->fetch();

        if ( !empty( $advirtiseID ) ) $request[ "advertise_id" ] = $advirtiseID[ "id" ];
        else $request[ "advertise_id" ] = 0;

        $clientID = $API->sendRequest( "clients", "add", $request );

    } else {

        $clientDetails = (array) $clientsID[ 0 ];
        $clientID = $clientDetails[ "id" ];

    }

    $services = $API->sendRequest( "services", "search", [
        "search" => "первичный",
        "users_id" => $userDetails->id
    ] );

    $services_ids = [];

    foreach ( $services as $service ) {

        if ( $visitDetails->is_online && str_contains( $service->title, "онлайн" ) ) $services_ids[] = $service->id;
        if ( !$visitDetails->is_online && !str_contains( $service->title, "онлайн" ) ) $services_ids[] = $service->id;

    }

    $serviceDetails = visits\getFullService( $services_ids[ 0 ], $userDetails->id );
    $visit_end = date( "Y-m-d H:i:s", strtotime( $visitDetails->dt_start . " +{$serviceDetails[ "take_minutes" ]} minutes" ) );


    $filters = [
        "event_from >= ?" => date( "Y-m-d 00:00:00", strtotime( $visitDetails->dt_start ) ),
        "event_to < ?" => date( "Y-m-d 23:59:59", strtotime( $visit_end ) ),
        "user_id" => $userDetails->id,
        "store_id" => $userDetails->lpu_id,
        "is_weekend" => 'Y'
    ];

    $is_weekend = $API->DB->from( "scheduleEvents" )
        ->where( $filters )
        ->fetch();

    if ( $is_weekend ) $API->returnResponse( false, 500 );
    unset( $filters[ "is_weekend" ] );
    $filters[ "is_rule" ] = 'N';

    $hasEvents = $API->DB->from( "scheduleEvents" )
        ->where( $filters )
        ->fetch();

    if ( !$hasEvents ) unset( $filters[ "is_rule" ] );

    $performerWorkSchedule = $API->DB->from( "scheduleEvents" )
        ->where( $filters )
        ->orderBy( "event_from ASC" )
        ->fetch();

    $request = [];

    $request[ "user_id" ] = $userDetails->id;
    $request[ "store_id" ] = $userDetails->lpu_id;
    $request[ "author_id" ] = 3;
    $request[ "clients_id" ] = [ $clientID ];
    $request[ "start_at" ] = date( "Y-m-d H:i:00", strtotime( $visitDetails->dt_start ) );
    $request[ "end_at" ] = $visit_end;
    $request[ "comment" ] = $visitDetails->comment;
    $request[ "services_id" ] = $services_ids;
    $request[ "notify" ] = true;
    $request[ "send_review" ] = true;
    $request[ "status" ] = "prodoctorov";
    $request[ "price" ] = $serviceDetails[ "price" ];

    if ( $performerWorkSchedule ) $request[ "cabinet_id" ] = $performerWorkSchedule[ "cabinet_id" ];

    $status = $API->sendRequest( "visits", "add", $request );

    if ( !$status || gettype( $status ) != "integer" ) exit( json_encode( [ "status_code" => 500 ] ) );
    else exit( json_encode( [ "status_code" => 204, "claim_id" => $status ] ) );

}

if ( property_exists( $API->request, "claim_id" ) ) {

    $API->sendRequest( "visits", "update", [
        "id" => $API->request->claim_id,
        "is_active" => false
    ] );
    exit( json_encode( [ "status_code" => 204 ] ) );

}