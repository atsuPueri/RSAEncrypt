<?php
// Qiita
// https://qiita.com/atsuPueri/items/3d796c093fefcb564aa7

require_once 'function.php';

// 処理が長くなった時にタイムアウトしないようにする
set_time_limit(5000);


$plain_text = "こんにちは!PHP!";
echo "読める平文<br>";
echo $plain_text;
echo "<br>======<br>";


// 平文の数値配列化
$a = convert_string_to_integer($plain_text);

echo "平文(数値配列)<br>";
var_dump($a);
echo "<br>======<br>";


// 鍵生成
// -------------------------
$p = gmp_nextprime(512); // 素数取得
$q = gmp_nextprime(256);

// 鍵生成
$keys = rsa_key_generate($p, $q);
$d = $keys[0]; // 秘密鍵
$n = $keys[1]; // 公開鍵
$e = $keys[2]; // 公開鍵

// 素数p,qを破棄
$p = null;
$q = null;

echo "鍵<br>";
var_dump($keys);
echo "<br>======<br>";



// -------------------------
// 暗号化
$b = rsa_encryption($a, $n, $e);

echo "暗号文<br>";
var_dump($b);
echo "<br>======<br>";
file_put_contents("cat_enc.txt", implode(",", $b) . "keys:{d:{$d},n:{$n},e:{$e}");




// -------------------------
// 復号
$a_ = rsa_composite($b, $d, $n);

echo "復号文<br>";
var_dump($a_);
echo "<br>======<br>";



// ------------------------
echo "読める復号文<br>";
echo convert_integer_to_string($a_);
echo "<br>======<br>";
