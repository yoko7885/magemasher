<?php

class utils
{

    const KEY = 'M@GEM@SHER';

    public static function crypt($plain_text)
    {
        // 暗号化＆復号化キー
        $key = md5(self::KEY);

        // 暗号化モジュール使用開始
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $key = substr($key, 0, mcrypt_enc_get_key_size($td));
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        // 暗号化モジュール初期化
        if (mcrypt_generic_init($td, $key, $iv) < 0) {
            exit('error.');
        }

        // データを暗号化
        $plain_text = mcrypt_generic($td, $plain_text);
        $crypt_text = base64_encode($plain_text);

        // 暗号化モジュール使用終了
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        $crypt_text = urlencode($crypt_text);

        return $crypt_text;
    }

    public static function plain($crypt_text)
    {
        // 暗号化＆復号化キー
        $key = md5(self::KEY);

        $crypt_text = urldecode($crypt_text);

        // 暗号化モジュール使用開始
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $key = substr($key, 0, mcrypt_enc_get_key_size($td));
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        // 暗号化モジュール初期化
        if (mcrypt_generic_init($td, $key, $iv) < 0) {
            exit('error.');
        }

        // データを復号化
        $crypt_text = base64_decode($crypt_text);
        $plain_text = rtrim(mdecrypt_generic($td, $crypt_text));

        // 暗号化モジュール使用終了
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $plain_text;
    }
}
?>