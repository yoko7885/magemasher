<?php

class commons
{

    public static function get_user($db, $account_id = null)
    {
        $where = array();
        if ($account_id) {
            $where[] = "account_id = \"{$account_id}\"";
            $where[] = "and";
        }
        $where[] = "invalid_flg != 1";

        $result = $db->select(array(
            'table' => 'user',
            'where' => $where
        ));
        if ($account_id) {
            if (count($result) > 0)
                $result = $result[0];
        }
        return $result;
    }

    public static function get_fields($db, $field_id = null)
    {
        $where = array();
        if ($field_id) {
            $where[] = "field_id = \"{$field_id}\"";
            $where[] = "and";
        }
        $where[] = "invalid_flg != 1";

        $result = $db->select(array(
            'table' => 'field',
            'where' => $where
        ));
        if ($field_id) {
            if (count($result) > 0)
                $result = $result[0];
        }
        return $result;
    }

    public static function get_sch_pt($db)
    {
        $sch_pt = $db->select(array(
            'table' => 'sch_pt'
        ));

        $time_now = time();

        if (count($sch_pt) <= 0)
            return array(
                'now' => 0,
                'last' => $time_now
            );
        return $sch_pt[0];
    }

    public static function get_bag($db)
    {
        $result = $db->select(array(
            'table' => 'bag'
        ));
        return $result;
    }

    public static function get_bag_one($db, $pkey)
    {
        $where = array();
        $where[] = "p_key = \"{$pkey}\"";

        $result = $db->select(array(
            'table' => 'bag',
            'where' => $where
        ));
        if (count($result) > 0)
            $result = $result[0];
        return $result;
    }

    public static function get_item_color($item, $need_sharp = true)
    {
        $r = 0;
        $g = 0;
        $b = 0;
        $fnc = function ($p, $c1, $c2, $c3) use (&$r, &$g, &$b) {
            $r += $p * ($c1 / 100);
            $g += $p * ($c2 / 100);
            $b += $p * ($c3 / 100);
        };
        // 希少性
        $fnc($item['scarcity'], 0, 0, 70);
        // 固さ
        $fnc($item['rigidity'], 40, - 20, - 30);
        // 大きさ
        $fnc($item['size'], 10, 10, 10);
        // 重さ
        $fnc($item['weight'], - 10, - 10, - 10);
        // 毒性
        $fnc($item['toxicit'], 50, 10, 70);
        // 自然性
        $fnc($item['naturally'], 10, 50, 00);
        // 可食性
        $fnc($item['edibility'], 80, 80, 20);
        // 獣性
        $fnc($item['animality'], 70, 0, 0);
        // 神聖
        $fnc($item['holy'], 70, 70, 70);
        // 邪悪
        $fnc($item['wicked'], - 70, - 70, - 70);

        $r_l = $r + 10;
        $g_l = $g + 10;
        $b_l = $b + 10;
        $r_d = $r - 10;
        $g_d = $g - 10;
        $b_d = $b - 10;

        if ($r_l < 0)
            $r_l = 0;
        if ($r_l > 100)
            $r_l = 100;
        if ($g_l < 0)
            $g_l = 0;
        if ($g_l > 100)
            $g_l = 100;
        if ($b_l < 0)
            $b_l = 0;
        if ($b_l > 100)
            $b_l = 100;
        if ($r_d < 0)
            $r_d = 0;
        if ($r_d > 100)
            $r_d = 100;
        if ($g_d < 0)
            $g_d = 0;
        if ($g_d > 100)
            $g_d = 100;
        if ($b_d < 0)
            $b_d = 0;
        if ($b_d > 100)
            $b_d = 100;

        $r_l = 255 * ($r_l / 100);
        $g_l = 255 * ($g_l / 100);
        $b_l = 255 * ($b_l / 100);
        $r_d = 255 * ($r_d / 100);
        $g_d = 255 * ($g_d / 100);
        $b_d = 255 * ($b_d / 100);

        $colors = array();
        $colors['light'] = ($need_sharp ? '#' : '') . str_pad(dechex($r_l), 2, "0", STR_PAD_LEFT) . str_pad(dechex($g_l), 2, "0", STR_PAD_LEFT) . str_pad(dechex($b_l), 2, "0", STR_PAD_LEFT);
        $colors['dark'] = ($need_sharp ? '#' : '') . str_pad(dechex($r_d), 2, "0", STR_PAD_LEFT) . str_pad(dechex($g_d), 2, "0", STR_PAD_LEFT) . str_pad(dechex($b_d), 2, "0", STR_PAD_LEFT);
        return $colors;
    }
}
?>