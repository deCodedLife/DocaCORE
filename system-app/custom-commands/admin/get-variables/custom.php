<?php

/**
 * @file
 * Получение доступных переменных
 */


/**
 * Сформированный список переменных
 */
$result = [];


/**
 * @test
 */
$result = [

    "clients" => [
        "title" => "Клиенты",
        "properties" => [
            [
                "title"=> "Имя",
                "article" => "first_name"
            ],
            [
                "title"=> "Фамилия",
                "article" => "last_name"
            ]
        ]
    ],
    "cars" => [
        "title" => "Автомобили",
        "properties" => [
            [
                "title"=> "VIN",
                "article" => "vin"
            ]
        ]
    ]

];


$response[ "data" ] = $result;