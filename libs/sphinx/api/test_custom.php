<?php

require ( "sphinxapi.php" );

$sphinx = new SphinxClient();

$sphinx->SetSortMode( SPH_SORT_RELEVANCE  );
$sphinx->SetFieldWeights( array(
    "title" => 1,
    "name" => 2
) );
$sphinx->SetArrayResult( true );

$result = $sphinx->Query( "Сплин", "artists" );


if ( $result === false ) {
    echo "Query failed: " . $sphinx->GetLastError() . ".\n"; // выводим ошибку если произошла
}
else {
    if ( $sphinx->GetLastWarning() ) {
        // echo "WARNING: " . $sphinx->GetLastWarning() . " // выводим предупреждение если оно было";
    }

    if ( ! empty($result["matches"]) ) { // если есть результаты поиска - обрабатываем их
        foreach ( $result["matches"] as $product => $info ) {
            var_dump( $info );
            echo "<br><br>";
        }
    } else {

        echo "no results<br><br>";
        var_dump( $result );

    }
}