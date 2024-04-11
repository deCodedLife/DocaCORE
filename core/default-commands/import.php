<?php

$ignoreTypes = [
    "dadata_address",
    "dadata_city",
    "dadata_region",
    "dadata_passport",
    "dadata_country",
    "dadata_local_area",
    "dadata_street",
    "google_address",
    "password",
    "editor",
    "file",
    "image",
    "smart_list",
    "link_list"
];


if (
    property_exists( $requestData, "context" ) &&
    property_exists( $requestData->context, "template" )
) {

    $filePath = "uploads/{$API::$configs[ "company" ]}/exel/{$API->request->object}.xlsx";
    $fileUrl = "https://{$_SERVER[ "HTTP_HOST" ]}/$filePath";

    $spreadSheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadSheet->getProperties()
        ->setTitle( "Astragreen" )
        ->setCompany( "Flowerbloom" )
        ->setCreator( "codedlife" );

    $workSheet = $spreadSheet->createSheet(0);
    $workSheet->setTitle( $objectScheme[ "title" ] );

    $columnsCount = 0;

    foreach ( $objectScheme[ "properties" ] as $key => $property ) {

        if ( !$property[ "is_autofill" ] ) continue;

        $is_required = false;
        $is_ignored = false;

        if ( in_array( $property[ "field_type" ], $ignoreTypes ) ) $is_ignored = true;
        if ( in_array( "add", $property[ "require_in_commands" ] ?? [] ) ) $is_required = true;
        if ( $property[ "field_type" ] == "list" && !$property[ "custom_list" ] ) $is_ignored = true;

        if ( $is_required && $is_ignored ) {

            $API->returnResponse( "Невозможно сформировать импорт для объекта {$objectScheme[ "title" ]} 
            Нет обработчика для обязательного поля {$property[ "title" ]}", 500 );

        }

        if ( $property[ "data_type" ] == "array" ) continue;
        if ( $is_ignored ) continue;

        $cellName = $workSheet->getColumnDimensionByColumn( $columnsCount + 1 )->getColumnIndex() . '1';
        $workSheet->setCellValue( $cellName, $property[ "title" ] );

        $columnsCount++;

    }

    $black = \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK;

    $workSheet->getStyle( 'A1:' . $workSheet->getColumnDimensionByColumn( $columnsCount )->getColumnIndex() . '1' )
        ->applyFromArray([
            "font" => [
                "color" => [
                    "rgb" => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE
                ]
            ],
            "fill" => [
                "fillType" => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                "color" => [
                    "rgb" => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK
                ]
            ]
        ]);

    if ( !is_dir( "uploads/{$API::$configs[ "company" ]}/exel" ) )
        mkdir( "uploads/{$API::$configs[ "company" ]}/exel" );

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $spreadSheet, "Xlsx" );
    $writer->save( $filePath );

    $API->returnResponse( $fileUrl );

}


if ( property_exists( $requestData, "import_file" ) ) {

    $API->returnResponse( $requestData );

}