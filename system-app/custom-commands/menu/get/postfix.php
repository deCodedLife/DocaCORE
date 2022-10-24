<?php

/**
 * Сформированное меню
 */
$responseMenu = [];


/**
 * Обработка пунктов меню
 */

foreach ( $response[ "data" ] as $menuItem ) {

    if ( !$menuItem[ "parent_id" ] ) {

        /**
         * Добавление корневых пунктов меню
         */
        $responseMenu[] = [
            "id" => $menuItem[ "id" ],
            "title" => $menuItem[ "title" ],
            "href" => $menuItem[ "href" ],
            "icon" => $menuItem[ "icon" ],
            "children" => []
        ];

    } else {

        /**
         * Добавление дочерних пунктов меню
         */

        foreach ( $responseMenu as $responseMenuItemKey => $responseMenuItem ) {

            /**
             * Проверка родительского пункта меню
             */
            if ( $responseMenuItem[ "id" ] != $menuItem[ "parent_id" ] ) continue;


            /**
             * Добавление дочернего пункта меню
             */
            $responseMenu[ $responseMenuItemKey ][ "children" ][] = [
                "id" => $menuItem[ "id" ],
                "title" => $menuItem[ "title" ],
                "href" => $menuItem[ "href" ],
                "icon" => $menuItem[ "icon" ],
                "children" => []
            ];

        } // foreach. $responseMenu

    } // if. !$menuItem[ "parent_id" ]

} // foreach. $response[ "data" ]


$response[ "detail" ] = [];
$response[ "data" ] = $responseMenu;