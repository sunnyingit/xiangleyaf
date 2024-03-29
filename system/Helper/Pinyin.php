<?php

/**
 * 中文转拼音基类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Pinyin.php 41 2012-09-09 14:41:32Z silverd30@gmail.com $
 */

class Helper_Pinyin
{
    public static function convert($str)
    {
        $result = '';

        $str = mb_convert_encoding($str, 'gb2312', 'UTF-8');
        for ($i = 0; $i < mb_strlen($str, 'gb2312'); $i++) {
            $result .= self::_single(mb_substr($str, $i, 1, 'gb2312'));
        }

        return $result;
    }

    /*
     * 获取首字母
     * @author Jimi.Pan
     */
    public static function getFirstWord($str)
    {
        $result = '';

        $str = mb_convert_encoding($str, 'gb2312', 'UTF-8');
        for ($i = 0; $i < mb_strlen($str, 'gb2312'); $i++) {
            $rs = self::_single(mb_substr($str, $i, 1, 'gb2312'));
            $result .= mb_substr($rs,0, 1, 'gb2312');
        }

        return $result;
    }

    private static function _getData()
    {
        return array(
            'zuo' => '-10254', 'zun' => '-10256', 'zui' => '-10260', 'zuan' => '-10262', 'zu' => '-10270',
            'zou' => '-10274', 'zong' => '-10281', 'zi' => '-10296', 'zhuo' => '-10307', 'zhun' => '-10309',
            'zhui' => '-10315', 'zhuang' => '-10322', 'zhuan' => '-10328', 'zhuai' => '-10329', 'zhua' => '-10331',
            'zhu' => '-10519', 'zhou' => '-10533', 'zhong' => '-10544', 'zhi' => '-10587', 'zheng' => '-10764',
            'zhen' => '-10780', 'zhe' => '-10790', 'zhao' => '-10800', 'zhang' => '-10815', 'zhan' => '-10832',
            'zhai' => '-10838', 'zha' => '-11014', 'zeng' => '-11018', 'zen' => '-11019', 'zei' => '-11020',
            'ze' => '-11024', 'zao' => '-11038', 'zang' => '-11041', 'zan' => '-11045', 'zai' => '-11052',
            'za' => '-11055',

            'yun' => '-11067', 'yue' => '-11077', 'yuan' => '-11097', 'yu' => '-11303', 'you' => '-11324',
            'yong' => '-11339', 'yo' => '-11340', 'ying' => '-11358', 'yin' => '-11536', 'yi' => '-11589',
            'ye' => '-11604', 'yao' => '-11781', 'yang' => '-11798', 'yan' => '-11831', 'ya' => '-11847',

            'xun' => '-11861', 'xue' => '-11867', 'xuan' => '-12039', 'xu' => '-12058', 'xiu' => '-12067',
            'xiong' => '-12074', 'xing' => '-12089', 'xin' => '-12099', 'xie' => '-12120', 'xiao' => '-12300',
            'xiang' => '-12320', 'xian' => '-12346', 'xia' => '-12359', 'xi' => '-12556',

            'wu' => '-12585', 'wo' => '-12594', 'weng' => '-12597', 'wen' => '-12607', 'wei' => '-12802',
            'wang' => '-12812', 'wan' => '-12829', 'wai' => '-12831', 'wa' => '-12838',

            'tuo' => '-12849', 'tun' => '-12852', 'tui' => '-12858', 'tuan' => '-12860', 'tu' => '-12871',
            'tou' => '-12875', 'tong' => '-12888', 'ting' => '-13060', 'tie' => '-13063', 'tiao' => '-13068',
            'tian' => '-13076', 'ti' => '-13091', 'teng' => '-13095', 'te' => '-13096', 'tao' => '-13107',
            'tang' => '-13120', 'tan' => '-13138', 'tai' => '-13147', 'ta' => '-13318',

            'suo' => '-13326', 'sun' => '-13329', 'sui' => '-13340', 'suan' => '-13343', 'su' => '-13356',
            'sou' => '-13359', 'song' => '-13367', 'si' => '-13383', 'shuo' => '-13387', 'shun' => '-13391',
            'shui' => '-13395', 'shuang' => '-13398', 'shuan' => '-13400', 'shuai' => '-13404', 'shua' => '-13406',
            'shu' => '-13601', 'shou' => '-13611', 'shi' => '-13658', 'sheng' => '-13831', 'shen' => '-13847',
            'she' => '-13859', 'shao' => '-13870', 'shang' => '-13878', 'shan' => '-13894', 'shai' => '-13896',
            'sha' => '-13905', 'seng' => '-13906', 'sen' => '-13907', 'se' => '-13910', 'sao' => '-13914',
            'sang' => '-13917', 'san' => '-14083', 'sai' => '-14087', 'sa' => '-14090',

            'ruo' => '-14092', 'run' => '-14094', 'rui' => '-14097', 'ruan' => '-14099', 'ru' => '-14109',
            'rou' => '-14112', 'rong' => '-14122', 'ri' => '-14123', 'reng' => '-14125', 'ren' => '-14135',
            're' => '-14137', 'rao' => '-14140', 'rang' => '-14145', 'ran' => '-14149',

            'qun' => '-14151', 'que' => '-14159', 'quan' => '-14170', 'qu' => '-14345', 'qiu' => '-14353',
            'qiong' => '-14355', 'qing' => '-14368', 'qin' => '-14379', 'qie' => '-14384', 'qiao' => '-14399',
            'qiang' => '-14407', 'qian' => '-14429', 'qia' => '-14594', 'qi' => '-14630',

            'pu' => '-14645', 'po' => '-14654', 'ping' => '-14663', 'pin' => '-14668', 'pie' => '-14670',
            'piao' => '-14674', 'pian' => '-14678', 'pi' => '-14857', 'peng' => '-14871', 'pen' => '-14873',
            'pei' => '-14882', 'pao' => '-14889', 'pang' => '-14894', 'pan' => '-14902', 'pai' => '-14908',
            'pa' => '-14914',

            'ou' => '-14921', 'o' => '-14922',

            'nuo' => '-14926', 'nue' => '-14928', 'nuan' => '-14929', 'nv' => '-14930', 'nu' => '-14933', 'nong' => '-14937',
            'niu' => '-14941', 'ning' => '-15109', 'nin' => '-15110', 'nie' => '-15117', 'niao' => '-15119', 'niang' => '-15121',
            'nian' => '-15128', 'ni' => '-15139', 'neng' => '-15140', 'nen' => '-15141', 'nei' => '-15143', 'ne' => '-15144',
            'nao' => '-15149', 'nang' => '-15150', 'nan' => '-15153', 'nai' => '-15158', 'na' => '-15165',

            'mu' => '-15180', 'mou' => '-15183', 'mo' => '-15362', 'miu' => '-15363', 'ming' => '-15369', 'min' => '-15375',
            'mie' => '-15377', 'miao' => '-15385', 'mian' => '-15394', 'mi' => '-15408', 'meng' => '-15416', 'men' => '-15419',
            'mei' => '-15435', 'me' => '-15436', 'mao' => '-15448', 'mang' => '-15454', 'man' => '-15625', 'mai' => '-15631',
            'ma' => '-15640',

            'luo' => '-15652', 'lun' => '-15659', 'lue' => '-15661', 'luan' => '-15667', 'lv' => '-15681', 'lu' => '-15701',
            'lou' => '-15707', 'long' => '-15878', 'liu' => '-15889', 'ling' => '-15903', 'lin' => '-15915', 'lie' => '-15920',
            'liao' => '-15933', 'liang' => '-15944', 'lian' => '-15958', 'lia' => '-15959', 'li' => '-16155', 'leng' => '-16158',
            'lei' => '-16169', 'le' => '-16171', 'lao' => '-16180', 'lang' => '-16187', 'lan' => '-16202', 'lai' => '-16205', 'la' => '-16212',

            'kuo' => '-16216', 'kun' => '-16220', 'kui' => '-16393', 'kuang' => '-16401', 'kuan' => '-16403', 'kuai' => '-16407',
            'kua' => '-16412', 'ku' => '-16419', 'kou' => '-16423', 'kong' => '-16427', 'keng' => '-16429', 'ken' => '-16433',
            'ke' => '-16448', 'kao' => '-16452', 'kang' => '-16459', 'kan' => '-16465', 'kai' => '-16470', 'ka' => '-16474',

            'jun' => '-16647', 'jue' => '-16657', 'juan' => '-16664', 'ju' => '-16689', 'jiu' => '-16706', 'jiong' => '-16708',
            'jing' => '-16733', 'jin' => '-16915', 'jie' => '-16942', 'jiao' => '-16970', 'jiang' => '-16983', 'jian' => '-17185',
            'jia' => '-17202', 'ji' => '-17417',

            'huo' => '-17427', 'hun' => '-17433', 'hui' => '-17454', 'huang' => '-17468', 'huan' => '-17482', 'huai' => '-17487',
            'hua' => '-17496', 'hu' => '-17676', 'hou' => '-17683', 'hong' => '-17692', 'heng' => '-17697', 'hen' => '-17701', 'hei' => '-17703', 'he' => '-17721',
            'hao' => '-17730', 'hang' => '-17733', 'han' => '-17752', 'hai' => '-17759', 'ha' => '-17922',

            'guo' => '-17928', 'gun' => '-17931', 'gui' => '-17947', 'guang' => '-17950', 'guan' => '-17961',
            'guai' => '-17964', 'gua' => '-17970', 'gu' => '-17988', 'gou' => '-17997', 'gong' => '-18012',
            'geng' => '-18181', 'gen' => '-18183', 'gei' => '-18184', 'ge' => '-18201', 'gao' => '-18211',
            'gang' => '-18220', 'gan' => '-18231', 'gai' => '-18237', 'ga' => '-18239',

            'fu' => '-18446', 'fou' => '-18447', 'fo' => '-18448', 'feng' => '-18463', 'fen' => '-18478',
            'fei' => '-18490', 'fang' => '-18501', 'fan' => '-18518', 'fa' => '-18526', 'er' => '-18696',

            'en' => '-18697', 'e' => '-18710',

            'duo' => '-18722', 'dun' => '-18731', 'dui' => '-18735', 'duan' => '-18741', 'du' => '-18756',
            'dou' => '-18763', 'dong' => '-18773', 'diu' => '-18774', 'ding' => '-18783', 'die' => '-18952',
            'diao' => '-18961', 'dian' => '-18977', 'di' => '-18996', 'deng' => '-19003', 'de' => '-19006',
            'dao' => '-19018', 'dang' => '-19023', 'dan' => '-19038', 'dai' => '-19212', 'da' => '-19218',

            'cuo' => '-19224', 'cun' => '-19227', 'cui' => '-19235', 'cuan' => '-19238', 'cu' => '-19242',
            'cou' => '-19243', 'cong' => '-19249', 'ci' => '-19261', 'chuo' => '-19263', 'chun' => '-19270',
            'chui' => '-19275', 'chuang' => '-19281', 'chuan' => '-19288', 'chuai' => '-19289', 'chu' => '-19467',
            'chou' => '-19479', 'chong' => '-19484', 'chi' => '-19500', 'cheng' => '-19515', 'chen' => '-19525',
            'che' => '-19531', 'chao' => '-19540', 'chang' => '-19715', 'chan' => '-19725', 'chai' => '-19728',
            'cha' => '-19739', 'ceng' => '-19741', 'ce' => '-19746', 'cao' => '-19751', 'cang' => '-19756',
            'can' => '-19763', 'cai' => '-19774', 'ca' => '-19775',

            'bu' => '-19784', 'bo' => '-19805', 'bing' => '-19976', 'bin' => '-19982', 'bie' => '-19986',
            'biao' => '-19990', 'bian' => '-20002', 'bi' => '-20026', 'beng' => '-20032', 'ben' => '-20036',
            'bei' => '-20051', 'bao' => '-20230', 'bang' => '-20242', 'ban' => '-20257', 'bai' => '-20265',
            'ba' => '-20283',

            'ao' => '-20292', 'ang' => '-20295', 'an' => '-20304', 'ai' => '-20317', 'a' => '-20319',
        );
    }

    private static function _single($str)
    {
        if (strlen($str) == 1) {
            $ascii = ord($str);
            if (($ascii >= 65 && $ascii <= 90) || ($ascii >= 97 && $ascii <= 122)) {
                return strtolower($str);
            }
        }

        $result = '';
        for ($i = 0; $i < strlen($str); $i++) {

            $ascii = ord(substr($str, $i, 1));
            ($ascii > 160)
                && $ascii = $ascii * 256 + ord(substr($str, ++$i, 1)) - 65536;

            $r = self::_pinyin($ascii);

            if  (!$r) {
                $extra = include dirname(__FILE__) . '/PinyinExtra.php';
                $extraCn = mb_convert_encoding($str, 'UTF-8', 'gb2312');
                $result .= isset($extra[$extraCn]) ? $extra[$extraCn] : '';
            } else {
                $result .= $r;
            }
        }

        return preg_replace('/[^a-z0-9]*/', '', $result);
    }

    private static function _pinyin($ascii)
    {
        if ($ascii > 0 && $ascii < 160) {
            return chr($ascii);
        }

        if ($ascii <- 20319 || $ascii > -10247) {
            return '';
        }

        $data = self::_getData();

        foreach ($data as $key => $value) {
            if ($value <= $ascii) {
                return $key;
            }
        }
    }
}