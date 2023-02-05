<?php

/**
 * Сформированное меню
 */
$responseMenu = [];


/**
 * Получение доступов к разделам
 */

/**
 * Доступы к разделам.
 *
 * Содержат связку пару Артикул раздела и ID ролей, у которых есть доступ к ним.
 * Если раздела нет в $sectionPermissions - то считается, что доступ к нему общий
 */
$sectionPermissions = [];

$permissions = $API->DB->from( "permissions" );

foreach ( $permissions as $permission ) {

    if ( strpos( $permission[ "article" ], "menu_" ) === false ) continue;


    /**
     * Артикул раздела
     */
    $sectionArticle = substr( $permission[ "article" ], 5 );

    /**
     * Учет доступов раздела
     */
    $sectionPermissions[ $sectionArticle ] = [];


    /**
     * Получение ролей с доступом к разделу
     */

    $roles_permissions = $API->DB->from( "roles_permissions" )
        ->select( null )->select( "role_id" )
        ->where( "permission_id", $permission[ "id" ] );

    foreach ( $roles_permissions as $role_permission )
        $sectionPermissions[ $sectionArticle ][] = $role_permission[ "role_id" ];

} // foreach. $permissions


/**
 * Обработка пунктов меню
 */

foreach ( $response[ "data" ] as $menuItem ) {

    /**
     * Проверка доступа
     */
    if (
        ( $API::$userDetail->role_id != 1 ) &&
        array_key_exists( $menuItem[ "href" ], $sectionPermissions ) &&
        !in_array( $API::$userDetail->role_id, $sectionPermissions[ $menuItem[ "href" ] ] )
    ) continue;


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