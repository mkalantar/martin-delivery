<?php
require __DIR__.'/vendor/autoload.php';

use Firebase\JWT\JWT;

$file = 'tokens.ini';
$key = readline("Enter Key: ");

$output = ["[Key]", $key];

$output[] = "[Customers]";
for ($i=0; $i < random_int(3, 10); $i++) {
  $payload = [
    'iss' => 'Martin',
    'aud' => sprintf("Customer %d", $i+1),
    'exp' => strtotime("+1 day")
  ];
  $output[] = JWT::encode($payload, $key, 'HS256');
}

$output[] = "[Couriers]";
for ($i=0; $i < random_int(5, 15); $i++) {
  $payload = [
    'iss' => 'Martin',
    'aud' => sprintf("Courier %d", $i+1),
    'exp' => strtotime("+1 day")
  ];
  $output[] = JWT::encode($payload, $key, 'HS256');
}


file_put_contents($file, implode(PHP_EOL, $output));
// file_put_contents($output, implode(PHP_EOL, $customers), FILE_APPEND);
// file_put_contents($output, "\n\nCouriers Tokens:\n", FILE_APPEND);
// file_put_contents($output, implode(PHP_EOL, $couriers), FILE_APPEND);