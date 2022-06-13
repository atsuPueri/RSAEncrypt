<?php
// 鍵の生成
// 引数の素数は莫大な大きさの素数を使うことを推奨する

/**
 * RSA鍵を生成する
 * @param GMP|string|int $p 素数１
 * @param GMP|string|int $q 素数2
 * 
 * @return array [$d, $n, $e] 秘密鍵:d 公開鍵:n,e
 * 
 * 引数の素数は莫大な大きさの素数を使うことを推奨する
 */
function rsa_key_generate($p, $q):array
{
    // 素数が一緒だとfalse
    if($p === $q){
        return false;
    }

    // 鍵生成
    $n = (string)gmp_mul($p, $q); // mul-> 乗算
    $n_ = gmp_mul( gmp_sub($p, '1'), gmp_sub($q, '1')); //sub-> 減算 (p-1)^(q-1)
    $rand = gmp_random_range( 0, gmp_sub($n_, '1')); // random_range-> ランダムな数 0 ~ (n')

    // 互いに素な数が見つかるまで
    while (true) {
        $coprime_numbers = (string)gmp_gcd($rand, $n_); // gcd-> 最大公約数を返す

        // 最大公約数が1なら互いに素な数
        if ($coprime_numbers === '1') {
            $e = (string)$rand;
            break;
        }
        // 非互いに素な数減算し再計算
        $rand = gmp_sub($rand, '1'); // rand--;
    }

    $d = (string)gmp_invert($e, $n_); // n'を法としたeの逆数

    // 秘密鍵:d 公開鍵:n,e
    return [$d, $n, $e];
}
// ========================================================

/**
 * RSA暗号化
 * @param array $a 暗号化対象の数値配列
 * @param string $n 公開鍵１
 * @param string $e 公開鍵２
 * 
 * @return array 暗号化された数値配列
 */
function rsa_encryption(array $a, string $n, string $e):array
{
    $b = [];
    foreach ($a as $value) {
        // aの一つをe乗する
        $a_e = gmp_pow($value, $e); // pow-> べき乗
        // e乗した値をnで割った余りを格納
        $b[] = (string)gmp_div_r($a_e, $n); // div_r-> 剰余
    }
    return $b;
}
// ========================================================

/**
 * RSA復号
 * @param array $b 暗号文
 * @param string $d 秘密鍵
 * @param string $n 公開鍵１
 * 
 * @return array 復号文
 */
function rsa_composite(array $b, string $d, string $n):array
{
    $a = [];
    foreach ($b as $value) {
        // bの一つをd乗する
        $b_d = gmp_pow($value, $d); // pow-> べき乗
        // d乗した値をnで割った余りを格納
        $a[] = (string)gmp_div_r($b_d, $n); // div_r-> 剰余
    }
    return $a;
}
// ========================================================

/**
 * 文字列をUnicodeポイントの数値配列に変換
 * @param string $str 文字列を
 * @return array<int,int>
 */
function convert_string_to_integer(string $str): array
{
    // 初期配列
    $ord_array = [];
    // 文字列を全て数値配列に
    for ($i = 0; $i < mb_strlen($str); $i++) {
        // 一文字取得
        $value= mb_substr($str, $i, 1);
        // 文字を数値に
        $ord_array[] = mb_ord($value);
    }
    return $ord_array;
}
// ========================================================

/**
 * Unicodeポイントの数値配列を文字列に変換
 * @param array<int,int> 数値が格納された配列
 * @return string
 */
function convert_integer_to_string(array $int_array): string
{
    // 初期文字列
    $chr = '';
    // 配列の数値を全て文字列に変換
    foreach ($int_array as $value) {
        // 数値を文字列に
        $chr .=  mb_chr($value);
    }
    return $chr;
}
?>