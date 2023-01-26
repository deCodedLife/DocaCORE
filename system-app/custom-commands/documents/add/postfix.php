<?php

/**
 * Добавление структуры документа
 */

foreach ( $requestData->structure as $documentBlock ) {

    /**
     * Добавление блока документа
     */

    $documentBlockId = null;

    switch ( $documentBlock->block_type ) {

        case "text":

            $documentBlockId = $API->DB->insertInto( "documents_$documentBlock->block_type" )
                ->values( [
                    "document_body" => $documentBlock->settings->document_body
                ] )
                ->execute();

            break;

        default:
            break;

    } // switch. $documentBlock->block_type

    if ( !$documentBlockId ) continue;


    /**
     * Добавление блока в структуру документа
     */
    $API->DB->insertInto( "documentBlocks" )
        ->values( [
            "document_id" => $insertId,
            "block_position" => $documentBlock->position,
            "block_type" => $documentBlock->block_type,
            "block_id" => $documentBlockId
        ] )
        ->execute();

} // foreach. $requestData->structure