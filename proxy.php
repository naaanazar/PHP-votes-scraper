

<?php
$count = 0;
/*$proxyd = [ '77.68.87.225:3128',
    '77.68.87.225:3128',
    '77.68.82.20:3128',
    '188.166.144.126:8118',
    '212.67.220.137:80',
    '139.59.164.21:8118',
    '81.128.165.5:3128',
    '178.62.33.147:8118',
    '77.68.86.164:3128',
    '62.96.10.98:3128',
    '91.194.42.51:80',
    '46.101.30.203:8118',
    '46.101.23.167:8118',
    '46.101.27.218:8118',
    '81.199.154.115:8080',
    '172.93.148.247:80',
    '104.236.166.203:80',
    '67.205.159.165:80',
    '104.199.210.46:80',
    '213.233.57.135:80',
    '178.62.123.38:8118'];*/


$str = "163.172.28.22:80	HTTP	HIA	Франция	80% (82) +	07:07:17 14:27:38
159.255.167.131:8080	HTTP !	NOA	Ирак	42% (348) -	07:07:17 14:27:34
36.71.227.226:8080	HTTPS !	NOA	Индонезия	новый +	07:07:17 14:26:56
109.200.155.196:8080	HTTPS !	NOA	Украина	42% (32) -	07:07:17 14:26:22
117.202.20.70:555	HTTP !	NOA	Индия !	новый +	07:07:17 14:25:46
36.67.145.155:53281	HTTPS !	NOA	Индонезия	61% (11) +	07:07:17 14:24:15
200.29.191.151:3128	HTTPS !	NOA	Чили !	100% (40) -	07:07:17 14:23:52
200.68.38.30:8080	HTTP	ANM	Чили	43% (3) +	07:07:17 14:23:27
121.50.170.58:3128	HTTP !	NOA	Гонг Конг	58% (7) -	07:07:17 14:22:44
223.164.250.78:8080	HTTPS !	NOA	Индонезия	26% (23) +	07:07:17 14:21:46
110.137.253.30:8080	HTTP !	NOA	Индонезия	40% (34) -	07:07:17 14:21:09
177.136.39.102:3128	HTTP !	NOA	Бразилия	50% (2) -	07:07:17 14:20:42
212.213.128.50:80	HTTP	HIA	Финляндия	100% (2) +	07:07:17 14:18:15
70.32.89.160:3128	HTTP	HIA	США	74% (149) -	07:07:17 14:15:57
41.203.183.50:8080	HTTP !	NOA	Лесото !	32% (120) -	07:07:17 14:15:17
203.130.209.101:8080	HTTPS !	NOA	Индонезия	31% (75) +	07:07:17 14:14:06
202.152.18.166:3128	HTTP !	NOA	Индонезия	100% (15) +	07:07:17 14:13:03
219.127.253.43:80	HTTP	HIA	Япония	48% (201) -	07:07:17 14:11:49
119.252.174.211:8080	HTTP !	NOA	Индонезия !	20% (18) -	07:07:17 14:10:59
82.78.191.206:8080";




$strlist = file_get_contents('proxyFromHidemy.txt', true);

//$proxyd = hidemyStrToProxyList($strlist);
$proxyd = strToProxyList($str);

var_dump($proxyd);

$proxyList = checkProxyForURL($proxyd, "http://rada.gov.ua/news/hpz8");




function hidemyStrToProxyList($str){

    preg_match_all ("/[0-9]+.[0-9]+.[0-9]+.[0-9]+\s[0-9]+/", $str , $proxyd);

    foreach ($proxyd[0] as $key=>$value){
       $proxyList[] = preg_replace('/\s/', ':', $value);
    }

    return $proxyList;
}

function strToProxyList($str){

    preg_match_all ("/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+:[0-9]+/", $str , $proxyd);

    foreach ($proxyd[0] as $key=>$value){
        $proxyList[] = preg_replace('/\s/', ':', $value);
    }

    return $proxyList;
}


/*$proxyd = [ '172.93.148.247:80',
    '104.236.166.203:80',
    '67.205.159.165:80',
    '104.199.210.46:80'];*/



function checkProxyForURL($proxyd, $url)
{
    $waitTimeoutInSeconds = 1;
    foreach ($proxyd as $prox) {

        $proxy = explode(":", $prox);

        if ($fp = @fsockopen($proxy[0], $proxy[1], $err, $err2, $waitTimeoutInSeconds)) {

            $aContext = array(
                'http' => array(
                    'proxy' => 'tcp://' . $prox,
                    'request_fulluri' => true,
                ),
            );

            $cxContext = stream_context_create($aContext);

            $sFile = @file_get_contents($url, False, $cxContext);

            if ($sFile !== false) {
                echo "'" . $prox . "',<br>";
                $proxyList[] = $prox;
            }

            @fclose($fp);
        }
    }

    return $proxyList;
}



/*

$waitTimeoutInSeconds = 1;
for ($i = 1; $i <= 5; $i++) {
    $rand_keys = array_rand($proxyd, 1);

    var_dump($proxyd[$rand_keys]);

    $str = (string) $proxyd[$rand_keys];
    $proxy = explode(":", $str);

    if($fp = @fsockopen($proxy[0],$proxy[1],$err,$err2,$waitTimeoutInSeconds)){


        $aContext = array(
            'http' => array(
                'proxy' => 'tcp://' . $proxyd[$rand_keys],
                'request_fulluri' => true,
            ),
        );
        $cxContext = stream_context_create($aContext);

        $sFile = @file_get_contents("http://www.seocheckpoints.com/my-ip-address", False, $cxContext);

        if($sFile !== false){
            $count++;


            echo "'" . $proxyd[$rand_keys] . "',<br>";
        }else{

        }

        @fclose($fp);
    } else {

    }



}

*/

/*
 *

'185.184.241.2:8080',
'36.67.162.247:53281',
'101.109.242.136:80',
'80.1.116.80:80',
'31.173.209.111:8080',
'51.255.48.61:9999',
'95.180.225.7:8080',
'41.204.32.194:53281',
'200.108.35.60:8087',
'188.0.168.205:8080',
'31.28.108.99:53281',
'70.32.89.160:3128',
'212.58.203.219:8080',
'203.172.210.70:8080',
'111.68.115.22:53281',
'137.59.44.47:8080',
'91.236.61.253:3128',
'185.184.241.2:8080',
'36.67.162.247:53281',
'80.1.116.80:80',
'51.255.48.61:9999',
'200.108.35.60:8087',
'181.39.128.178:53281',
'188.0.168.205:8080',
'36.67.161.226:53281',
'167.249.68.66:8080',
'212.58.203.219:8080',
'203.172.210.70:8080',
'111.68.115.22:53281',
'137.59.44.47:8080',
'91.236.61.253:3128',
'200.29.191.151:3128',
'200.68.38.30:8080',
'223.164.250.78:8080',
'110.137.253.30:8080',

 * */