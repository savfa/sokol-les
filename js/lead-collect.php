<?php

ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/php_errors_lc.log');

//Приводим POST и JSON к одному формату
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)){
        $_POST = json_decode(file_get_contents('php://input'), true);
}

$post = $_POST;

if (isset($_GET['zd_echo'])) exit($_GET['zd_echo']); //Для того, чтобы задарма восприняла скрипт, как разрешенный адрес.

//Телефон определить до serach
if (isset($post['caller_id'])) {
	$phone = $post['caller_id'];
	$ltype = 'Звонок по номеру на сайте z';
	$type_lead = 'CALL';
}
elseif (isset($post['tel'])) {
	$phone = $post['tel'];
}
elseif (isset($post['ot_kogo'])) {
	$phone = $post['ot_kogo'];
	$ltype = 'Коллтрекинг alloka';
	$type_lead = 'CALL';
}
elseif (isset($post['phone'])) {
	$phone = $post['phone'];
}
elseif (isset($post['Phone'])) {
	$phone = $post['Phone'];
}
elseif (isset($post['caller'])) {
	$phone = $post['caller'];
	$type_lead = 'CALL';
}
elseif (isset($post['client_phone'])) {
	$phone = $post['client_phone'];
}

if (isset($post['email'])) {
	$email = $post['email'];
}
elseif (isset($post['client_mail'])) {
	$email = $post['client_mail'];
}


///Serach вызываем до лога. ибо не логично.
$log_file = getcwd() . '/lead-collect.log';

if (isset($phone)) {
	$serach = search_in_file($phone,$log_file); // вызываем функцию поиска по логу. ищем телефон в том виде, в котором пришел
	$phone = phoneClear($phone);
	if ($serach === false) {
		$serach = search_in_file($phone,$log_file); //ищем телефон в логе без доп символов
	}
}


if ($serach === false or empty($serach)) {
	if (isset($email)) {
		$serach = search_in_file($email,$log_file);
	}
}

//Описываем уникальность лида для таблицы
if($serach===false){
	$uniq = 'уникальный';
} 
elseif ($serach) {
	$uniq = 'дубль';
}



/**
 * Заполнить значение переменных
 */
$UA = 'UA-128472659-1';
//$url_crm = 'http://sokol-les.ru/amo/leadadd.php'; // 'XXXXXXX/bitrix/rest.php'
$url_crm = 'http://evl-analytics.ru/allles/amoCRM/amo_add.php';
$url_google = "https://docs.google.com/forms/u/0/d/e/1FAIpQLSdpUBZ1-zM03hdcuXSZEPHFb-7BuRQzv5XWwBKcHIy2rwysZA/formResponse";
$draftResponse = "4399428071361797202";

$token_telegram = ''; //Токен телеграм бота
$token_alloka = '?guest_token=6_Jiq-3undBR20keSyqv9g'; //Гостевой токена аллоки, чтобы можно было не логинясь слушать записи в срм

$tags = '';
$project = 'solol-les.ru';

if (isset($post['name'])){
	$name  = htmlspecialchars($post['name']);
}
elseif (isset($post['Name'])) {
	$name  = htmlspecialchars($post['Name']);
}

$page = isset($post['page']) ? htmlspecialchars($post['page']) : '';


//Telegram
$chat_id_list = array( //ID тех, кому шлем сообщения. Последний элемент БЕЗ ЗАПЯТОЙ //чтобы найти id нового пользователя надо перейти в бота @userinfobot
	'94875250' //Евгений Луценко
); 


//Переменная ltype - из формы
if (isset($post['title'])) {
	$ltype = $post['title'];
}
elseif (isset($post['ltype'])) {
	$ltype = $post['ltype'];
}
elseif (isset($post['formname'])) {
	$ltype = $post['formname'];
}
elseif (isset($post['callee'])){
	$ltype = "Коллтрекинг Roistat";
}

	
if (isset($post['COOKIES'])) { //Если куки из тильды, то разбираем строку в массив, находим нужное значение и обрабатываем его ниже
	$cookies = str_replace('; ', '&', $post['COOKIES']);
	parse_str($cookies, $arrCookies);
}


//Переменные о звонке.
$rec = isset($post['rec']) ? htmlspecialchars($post['rec']) : '';
$call_id = isset($post['call_id']) ? htmlspecialchars($post['call_id']) : '';
$duration = isset($post['duration']) ? htmlspecialchars($post['duration']) : '';
$disposition = isset($post['disposition']) ? htmlspecialchars($post['disposition']) : '';
$status = ''; // Объявляем переменную
if ($ltype == "Звонок по номеру на сайте a") {
	$status = $post['status'];
} else {
		if ($ltype == "Звонок по номеру на сайте z") {
		switch ($disposition) {
		case 'answered':
			$status = 'разговор';
			break;
		case 'busy':
			$status = 'занято';
			break;
		case 'cancel':
			$status = 'отменен';
			break;
		case 'no answer':
			$status = 'без ответа';
			break;	
		case 'failed':
			$status = 'не удался';
			break;	
		case 'no money':
			$status = 'нет средств, превышен лимит';
			break;
		case 'unallocated number':
			$status = 'номер не существует';
			break;
		case 'no limit':
			$status = 'превышен лимит';
			break;
		case 'no day limit':
			$status = 'превышен дневной лимит';
			break;
		case 'line limit':
			$status = 'превышен лимит линий';
			break;
		case 'no money, no limit':
			$status = 'превышен лимит';
			break;								
		default:
			$status = 'не удалось определить';
			break;
		} 
	}

}


//UTM Source
if (isset($post['utm']['utm_source'])) {
	$utm_source = $post['utm']['utm_source'];
} elseif (isset($post['utm_source'])) {
	$utm_source = $post['utm_source'];
}
elseif (empty($utm_source)) {
	$utm_source = '';
}

//UTM Medium
if (isset($post['utm']['utm_medium'])) {
	$utm_medium = $post['utm']['utm_medium'];
} elseif (isset($post['utm_medium'])) {
	$utm_medium = $post['utm_medium'];
}
elseif (empty($utm_medium)) {
	$utm_medium = '';
}

//UTM Campaign
if (isset($post['utm']['utm_campaign'])) {
	$utm_campaign = $post['utm']['utm_campaign'];
} elseif (isset($post['utm_campaign'])) {
	$utm_campaign = $post['utm_campaign'];
}
elseif (empty($utm_campaign)) {
	$utm_campaign = '';
}

//UTM Content
if (isset($post['utm']['utm_content'])) {
	$utm_content = $post['utm']['utm_content'];
} elseif (isset($post['utm_content'])) {
	$utm_content = $post['utm_content'];
}
elseif (empty($utm_content)) {
	$utm_content = '';
}

//UTM Term
if (isset($post['utm']['utm_term'])) {
	$utm_term = $post['utm']['utm_term'];
} elseif (isset($post['utm_term'])) {
	$utm_term = $post['utm_term'];
}
elseif (empty($utm_term)) {
	$utm_term = '';
}


//Переменная cid из аллоки, задармы form или генерируем.
if (isset($post['cid'])) {
	$cid = $post['cid'];
}
elseif (isset($post['ga_client_id'])) {
	$cid = $post['ga_client_id'];
}
elseif (isset($post['google_client_id'])) {
	$cid = $post['google_client_id'];
}
elseif (isset($arrCookies['_ga'])) { //Тильда
	$cid = substr($arrCookies['_ga'], 6, 21); 
}   

elseif (isset($post['extra']['cookies']['_ga'])) { //marquiz
	$cid = substr($post['extra']['cookies']['_ga'], 6, 21);
}
elseif (empty($cid) or $cid == 'cid' or $cid == '–' or $cid == '–') {
	$cid = genCid();
	$data_mp =  buildMpCall($post, $utm_source,$utm_medium,$utm_campaign, $utm_term, $utm_content);
	$data_log['data_mp'] = $data_mp;

	if ($serach===false and $type_lead == 'CALL') {
		$data_log['session'] = session($UA, $cid, $data_mp);
	}
}

if (isset($post['custom_data'])) { 
	$custom_data = json_decode($post['custom_data'], true);
}	
if ($custom_data['roistat_visit']) {
    $roistat_visit = $custom_data['roistat_visit'];
} 
elseif ($post['roistat']) {
    $roistat_visit = $post['roistat'];
}
elseif ($post['visit_id']) {
    $roistat_visit = $post['visit_id'];
}

if ($post['name']) {
	$name = $post['name'];
}
elseif ($post['client_name']) {
	$name = $post['client_name'];
}

$form_name = $ltype;

$calc = '';
if (isset($post['a'])) { $calc .= "Наименование {$post['a']} \n"; }
if (isset($post['width'])) { $calc .= "Ширина {$post['width']} \n"; }
if (isset($post['thickness'])) { $calc .= "Длина {$post['thickness']} \n"; }
if (isset($post['count'])) { $calc .= "Кол-во {$post['count']}"; }
	
if (!empty($phone)) { $data['phone'] = $phone; }
if (!empty($email)) { $data['email'] = $email; }
if (!empty($uniq)) { $data['uniq'] = $uniq; }
if (!empty($project)) { $data['project'] = $project; }
if (!empty($roistat_visit)) { $data['roistat_visit'] = $roistat_visit; }

if (!empty($ltype)) { $data['ltype'] = $ltype; }
if (!empty($name)) { $data['name'] = $name; }
if (!empty($calc)) { $data['calc'] = $calc; }

if (!empty($tags)) { $data['tags'] = $tags; }
if (!empty($page)) { $data['page'] = $page; }

if (!empty($status)) { $data['status'] = $status; }
if (!empty($rec)) { $data['rec'] = $rec; }
if (!empty($duration)) { $data['duration'] = $duration; }
if (!empty($call_id)) { $data['call_id'] = $call_id; }

if (!empty($token_alloka)) { $data['token_alloka'] = $token_alloka; }
if (!empty($token_telegram)) { $data['token_telegram'] = $token_telegram; }

if (!empty($draftResponse)) { $data['draftResponse'] = $draftResponse; }
if (!empty($url_google)) { $data['url_google'] = $url_google; }

if (!empty($url_crm)) { $data['url_crm'] = $url_crm; }
if (!empty($UA)) { $data['UA'] = $UA; }


if (!empty($cid)) { $data['cid'] = $cid; }
if (!empty($utm_source)) { $data['utm_source'] = $utm_source; }
if (!empty($utm_campaign)) { $data['utm_campaign'] = $utm_campaign; }
if (!empty($utm_content)) { $data['utm_content'] = $utm_content; }
if (!empty($utm_term)) { $data['utm_term'] = $utm_term; } 

$data_log['data'] = $data;
$data_log['post'] = $post;
/**
 * Активируем модули
 */

//telegram($data, $url_telegram, $token_telegram, $chat_id_list); //Активировать уведомления телеграм
googleTab($data, $data['draftResponse'], $data['url_google']); //Таблица лидов - все обращения

if ($phone or $email) {
	if ($ltype != "Коллтрекинг Roistat") {
		$result = crm($data); // Отправляем данные в скрипты CRM
		echo "---";
	}
	
}

$url_telegram = 'http://evl-analytics.ru/allles/telegrambot.php';
request($url_telegram,$data,"POST");

//Логируем все 
writeToLog($data_log, 'incoming',$log_file);







function googleTab($data, $draftResponse, $url_google)
{

	if (isset($data['phone'])) { $post_data['entry.420253738'] = $data['phone']; } 
	if (isset($data['email'])) { $post_data['entry.1627466087'] = $data['email']; }
	if (isset($data['calc'])) { $post_data['entry.2013225711'] = $data['calc']; }
	if (isset($data['ltype'])) { $post_data['entry.13449806'] = $data['ltype']; }
	if (isset($data['name'])) { $post_data['entry.1668560639'] = $data['name']; }

	if (isset($data['cid'])) { $post_data['entry.61073483'] = $data['cid']; }
	if (isset($data['roistat_id'])) { $post_data['entry.1113193256'] = $data['roistat_id']; }
	if (isset($data['utm_source'])) { $post_data['entry.34190923'] = $data['utm_source']; }
	if (isset($data['utm_campaign'])) { $post_data['entry.1353703111'] = $data['utm_campaign']; }
	if (isset($data['utm_source'])) { $post_data['entry.767910141'] = $data['utm_source']; }
	if (isset($data['utm_content'])) { $post_data['entry.376934658'] = $data['utm_content']; }
	if (isset($data['utm_term'])) { $post_data['entry.849387769'] = $data['utm_term']; }

	$post_data['draftResponse'] = "[,,&quot;-".$draftResponse."&quot;]";
	$post_data['pageHistory'] = "0";
	$post_data['fbzx'] = $draftResponse;

	return request($url_google,$post_data,'POST');
}
 


function crm($data) 
{
	request($data['url_crm'],$data,'POST');
}

function telegram($data, $chat_id_list)
	{
		for ($i=0; $i < count($chat_id_list) ; $i++) {

		$chat_id = $chat_id_list[$i];
		$url_telegram = 'https://api.telegram.org/bot' . $data['token_telegram'] . '/sendMessage?chat_id=' . $chat_id . "&text=" . $data['phone'] . "%0A";

		if (!empty($data['ltype'])) { $url_telegram .= $data['ltype'] . "%0A"; }
		if (!empty($data['name'])) { $url_telegram .= $data['name'] . "%0A"; }
		if (!empty($data['email'])) { $url_telegram .= $data['email'] . "%0A"; }
		if (!empty($data['utm_source'])) { $url_telegram .= $data['utm_source'] . "%0A"; }
		if (!empty($data['utm_term'])) { $url_telegram .= $data['utm_term'] . "%0A"; }
		if (!empty($data['calc'])) { $url_telegram .= str_replace("\n", "%0A", $data['calc']) . "%0A"; }

	       // postRequest(null,$url_telegram);
	        request($url_telegram,null,'POST');
		}
		
	}

// function getRequest ($url)
// {
//     $curl = curl_init();
//     curl_setopt($curl, CURLOPT_URL, $url);
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
//     $output = curl_exec($curl);
//     curl_close($curl);
//     return $output;
//  }

//  function postRequest($data,$url) //Интеграция с CRM
// {

// 	$ch = curl_init();
// 	curl_setopt($ch, CURLOPT_URL, $url);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 	curl_setopt($ch, CURLOPT_POST, 1);
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// 	$output = curl_exec($ch);
// 	curl_close($ch);
// 	return $output;
// }

function request($url,$data = null,$method = 'GET'){
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    
    if ($method == 'POST'){
	    curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


function buildMpCall(
	$post,
	$utm_source = null,
	$utm_medium = null, 
	$utm_campaign = null,  
	$utm_term = null, 
	$utm_content = null
) 
{
	$data_mp = [];
	if (isset($post['caller_id'])) { $data_mp['session'] = 'zadarma call';	}
	if (isset($post['ot_kogo'])) { $data_mp['session'] = 'alloka call';	}
	if (isset($post['contact_info']['contact_phone_number'])) { $data_mp['session'] = 'uis call'; }
	if (isset($post['callee'])) { $data_mp['session'] = 'roistat call'; }

	$data_mp['ec'] = 'call';
	$data_mp['ea'] = 'track';
	$data_mp['cs'] = isset($utm_source) ? $utm_source : $data_mp['session'];//источник
	$data_mp['cn'] = isset($utm_campaign) ? $utm_campaign : $data_mp['session']; // название рк
	$data_mp['cm'] = isset($utm_medium) ? $utm_medium : $data_mp['session']; //канал
	$data_mp['ck'] = isset($utm_term) ? $utm_term : $data_mp['session'];//ключ
	$data_mp['cc'] = isset($utm_content) ? $utm_content : $data_mp['session']; //содержание

	return $data_mp;
}


function phoneClear($phone)
{
    // плюс оставляем, чтобы 8 заменить дальше
    $resPhone = preg_replace("/[^0-9\+]/", "", $phone);
    $phone = trim($phone);
    // с 8 всего циферок будет 11 и не будет + в начале
    if (strlen($resPhone) === 11) {
        $resPhone = preg_replace("/^8/", "7", $resPhone);
    }       

    if (substr($phone, 0,1) == '8' or substr($phone, 0,1) == '+') {
        //echo $phone . "<br>";
        $phone = preg_replace('/^\+?(8|7)/', '7', $phone);
    }
    // теперь уберём все плюсы
    $phone = preg_replace("/[^0-9]/", "", $phone);
    return $phone;
}


function session($UA, $cid, $data_mp) //отправляем событие о звонке и сеанс в Analytics
	{
		$data = array(
			'v' => 1,
			'tid' => $UA, //Номер счетчика Google Analytics
			'cid' => $cid,
			't' => 'event',
			'ec' => $data_mp['ec'],      //Категория цели
			'ea' => $data_mp['ea'],	//Действие цели
			'cs' => $data_mp['cs'], //источник
			'cn' => $data_mp['cn'], // название рк
			'cm' => $data_mp['cm'], //канал
			'ck' => $data_mp['ck'], //ключ
			'cc' => $data_mp['cc'], //содержание
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.google-analytics.com/collect');
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode(http_build_query($data)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);			
		curl_close($ch);

		return $data;
	}

function genCid() //Генерируем Client ID
	{
		$symbols = '0123456789';
		// Количество символов 
		$amount = 10; 
		$id = 9;
		// Определяем размер будущего числа
		$size = StrLen( $symbols )-1; 
		// Генерируем число
		while ( $amount-- )
		    $random_number .= $symbols[rand( 0, $size )];
		while ( $id-- )
			$random_id .= $symbols[rand( 0, $size)];
		$cid = $random_id . "." . $random_number;
		return $cid;
	}	

function search_in_file($searchfor,$file) 
{
	$contents = file_get_contents($file); //читаем файл
	$pattern = preg_quote($searchfor, '/'); // экранируем символы
	$pattern = "/^.*$pattern.*\$/m"; 
	if(preg_match_all($pattern, $contents, $matches)){
	   return $matches[0];
	} 	else{
	   return false;
	}
}

function writeToLog($data, $title = '',$log_file) {
	$log = "\n------------------------\n";
	$log .= date("Y.m.d G:i:s") . "\n";
	$log .= time() . "\n";
	$log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
	$log .= print_r($data, 1);
	$log .= "\n------------------------\n";
	file_put_contents($log_file, $log, FILE_APPEND);
	return true;
}



?>