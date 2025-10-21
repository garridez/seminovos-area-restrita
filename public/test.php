<?php
$ch = curl_init('http://ip-10-0-1-196.us-west-2.compute.internal/v1/veiculos/123');
curl_setopt_array($ch, [
  CURLOPT_CUSTOMREQUEST => 'PUT',
  CURLOPT_RETURNTRANSFER => true,
]);
$out = curl_exec($ch);
var_dump(curl_error($ch));
echo $out;

echo 'v2';