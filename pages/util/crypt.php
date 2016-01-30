<?php  // $Id: crypt.php 1573 2012-08-30 19:18:14Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

/**
 * This module contains utility functions for
 * hashing and comparing passwords;
 * en/decrypting fields, etc.
 */

/**
 * CRYPT_BLOWFISH - Blowfish hashing with a salt as follows: "$2a$"
 * a two digit cost parameter, "$"
 */
define("CRYPT_BLOWFISH_INSTRUCTION", "$2a$07$");

// CRYPT_BLOWFISH - Blowfish hashing ... and 22 digits from the alphabet "./0-9A-Za-z".
define("BASE64_STRING", "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./");

/**
 * Use to generate the password hash for storage.
 * http://us3.php.net/manual/en/function.crypt.php
 */
function hashField($field)
{
    $salt = generateRandomBase64String(22); // get unique string
    return crypt($field, CRYPT_BLOWFISH_INSTRUCTION . $salt);
} // hashField

/**
 * Use to compare the password to that in storage.
 * http://us3.php.net/manual/en/function.crypt.php
 */
function compareField($field, $hashed)
{
    return ($hashed == crypt($field, $hashed));
} // compareField

/*
 * uncomment to test
$hash = hashField('hello world');
echo "test positive " . (compareField('hello world', $hash) ? "true" : "false");
echo "\n";
echo "test negative " . (compareField('hello worle', $hash) ? "true" : "false");
 */

/**
 * This function supports hashField's salt generation
 */
function generateRandomBase64String($length)
{
    $returnString = "";
    for ($k = 0; $k < $length; $k++)
    {
        $returnString .= substr(BASE64_STRING, mt_rand(0, 63), 1); // get i'th character
    }
    return $returnString;
} // generateRandomBase64String


/**
 * http://www.php.net/manual/en/book.openssl.php
 * http://www.php.net/manual/en/book.gnupg.php
 */
function encryptField($field)
{
    // $key is set here
    return $field;
} // encryptField

/**
 */
function decryptField($field)
{
    // $key is set here
    return $field;
} // decryptField


?>
