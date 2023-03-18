<?php

/**
 * @file Загрузка изображений
 */

$API->returnResponse(
    $API->uploadImagesFromForm( $requestData->row_id, $requestData->image, $requestData->scheme_name )
);