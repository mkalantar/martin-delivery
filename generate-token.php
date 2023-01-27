<?php
require __DIR__.'/vendor/autoload.php';

use Firebase\JWT\JWT;

$tokens_file = 'tokens.txt';
$key_file = '.secret';
$key = readline("Enter Key: ");

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


file_put_contents($tokens_file, implode(PHP_EOL, $output));
file_put_contents($key_file, $key);