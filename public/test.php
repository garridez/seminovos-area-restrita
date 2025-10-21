<?php
$ch = curl_init('http://api2.seminovos.com.br/v1/veiculos/123');
curl_setopt_array($ch, [
  CURLOPT_CUSTOMREQUEST => 'PUT',
  CURLOPT_RETURNTRANSFER => true,
]);
$out = curl_exec($ch);
var_dump(curl_error($ch));
echo $out;