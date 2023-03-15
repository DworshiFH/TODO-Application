<?php

//Database credentials.
const DB_SERVER = 'localhost';
const DB_USERNAME = 'todoApp';
const DB_PASSWORD = '7TUq6Fryb1zKZMS9CyhY'; //Secret redacted, for security purposes
const DB_NAME = 'todoApp';

//Cyphering Values
$ciphering = "AES-256-CBC";
$iv_length = openssl_cipher_iv_length($ciphering);
$options   = 0;
$encryption_iv = 'N7Lp6XU1lLGmSlTvPEAe';
$encryption_key = "FgyYHtQtBFNUfuoTKBT5";

// Attempt to connect to MySQL database

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($link === false) {
    echo("ERROR: Could not connect. " . mysqli_connect_error());
}

// echo "success";