<?
if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, 'http://free.ipwhois.io/json/' . $_POST['curIP']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $out = curl_exec($curl);
    //echo $out;
    $arr = json_decode($out, true);
    foreach ($arr as $key => $item) {
        echo "<b>" . $key . "</b> - " . $item . "<br>";
    }
    curl_close($curl);
}