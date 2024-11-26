<?php

namespace LiteView\Support;

class IDCard
{

    //身份证号
    public static function IDCard($id, $pattern = [], &$info = null)
    {
        $id   = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        if (!preg_match($regx, $id)) {
            return '不正确';
        }

        $arr_split = array();
        //检查15位
        if (15 == strlen($id)) {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return '不正确';
            }
            $sexint = (int)substr($id, 14, 1);
            $sex    = $sexint % 2 === 0 ? '女' : '男';
            $info   = array('birth' => $dtm_birth, 'sex' => $sex);
            return 0;
        }

        //检查18位
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) { //检查生日日期是否正确
            return '不正确';
        }
        //检验18位身份证的校验码是否正确。
        //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
        $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $arr_ch  = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $sign    = 0;
        for ($i = 0; $i < 17; $i++) {
            $b    = (int)$id[$i];
            $w    = $arr_int[$i];
            $sign += $b * $w;
        }
        $n       = $sign % 11;
        $val_num = $arr_ch[$n];
        if ($val_num != substr($id, 17, 1)) {
            // 身份证的校验码不正确
            return '不正确';
        }
        $sexInt = (int)substr($id, 16, 1);
        $sex    = $sexInt % 2 === 0 ? '女' : '男';
        $info   = array('birth' => $dtm_birth, 'sex' => $sex);
        return 0;
    }

}