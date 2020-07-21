<?php

//Приводим POST и JSON к одному формату
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)){
        $_POST = json_decode(file_get_contents('php://input'), true);
}

if (isset($_GET['zd_echo'])) exit($_GET['zd_echo']); //Для того, чтобы задарма восприняла скрипт, как разрешенный адрес.

//Телефон определить до serach
if (isset($_POST['caller_id'])) {
	$phone = $_POST['caller_id'];
	$ltype = 'Звонок по номеру на сайте z';
}
elseif (isset($_POST['ot_kogo'])) {
	$phone = $_POST['ot_kogo'];
	$ltype = 'Звонок по номеру на сайте';
}
elseif (isset($_POST['phone'])) {
	$phone = $_POST['phone'];
}
elseif (isset($_POST['Phone'])) {
	$phone = $_POST['Phone'];
}
elseif (isset($_POST['user_column_data'])){
	$phone = $_POST['user_column_data'][0]['string_value'];
}

if (isset($_POST['user_email'])) {
	$email = $_POST['user_email'];
}
elseif (isset($_POST['email'])) {
	$email = $_POST['email'];
}

///Serach вызываем до лога. ибо не логично.
$log_file = getcwd() . '/lead-collect.log';

if ($phone) {
	$serach = search_in_file($phone,$log_file); // вызываем функцию поиска по логу. ищем телефон в том виде, в котором пришел
	$phone = phoneClear($phone);
}


if ($serach === false) {
	$serach = search_in_file($phone,$log_file); //ищем телефон в логе без доп символов
}

if ($serach === false or empty($serach)) {
	if ($email) {
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


//Логируем все 
writeToLog($_POST, 'incoming',$log_file);



/**
 * Заполнить значение переменных
 */
$UA = 'UA-141412574-1';
$url_crm = 'http://evl-analytics.ru/alfagroup/amo/leadadd.php'; // 'XXXXXXX/bitrix/rest.php'

$url_google = "https://docs.google.com/forms/d/e/1FAIpQLSeQcsKVF8Pz7PYh5Mko40ClLLC4Uj5RIOLWf9bS0EhFR5mnPg/formResponse";
$draftResponse = "3101374577412315386";

$token_telegram = '1XXXXXXXXXXX1'; //Токен телеграм бота
$token_alloka = '?guest_token=8sDehXKGG-nyzkMu_p8LdQ'; //Гостевой токена аллоки, чтобы можно было не логинясь слушать записи в срм

$tags = 'срорф.рф';

$name = $_POST['name'];

$page = $_POST['page'];

//Telegram
$chat_id_list = array( //ID тех, кому шлем сообщения. Последний элемент БЕЗ ЗАПЯТОЙ //чтобы найти id нового пользователя надо перейти в бота @userinfobot
	'94875250' //Евгений Луценко
); 


//Переменная ltype - из формы
if ($_POST['title']) {
	$ltype = $_POST['title'];
} 
elseif ($_POST['ltype']) {
	$ltype = $_POST['ltype'];
}
elseif ($_POST['formname']) {
	$ltype = $_POST['formname'];
}
	



//Переменные о звонке.
$duration = $_POST['duration']; //длительность вызова
$disposition = $_POST['disposition']; // статус (код)
$rec = $_POST['rec']; //Ссылка на запись разговора из аллоки
$call_id = $_POST['call_id'];
$status = ''; // Объявляем переменную
if ($ltype == "Звонок по номеру на сайте a") {
	$status = $_POST['status'];
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

$call_status = '';
switch ($status) {
	case 'Отмена':
		$call_status = '6';
		break;
	case 'Отвечен':
		$call_status = '4';
		break;
	case 'Занято':
		$call_status = '7';
		break;
	
	default:
		$call_status = '4';
		break;
}


//UTM Source
if ($_POST['utm']['utm_source']) {
	$utm_source = $_POST['utm']['utm_source'];
} else {
	$utm_source = $_POST['utm_source'];
}

//UTM Medium
if ($_POST['utm']['utm_medium']) {
	$utm_medium = $_POST['utm']['utm_medium'];
} 
elseif ($_POST['utm_medium']) {
	$utm_medium = $_POST['utm_medium'];
}
else {
	$utm_medium = 'cpc';
}


//UTM Source
if (isset($_POST['utm']['utm_source'])) {
	$utm_source = $_POST['utm']['utm_source'];
} elseif (isset($_POST['utm_source'])) {
	$utm_source = $_POST['utm_source'];
}
elseif (empty($utm_source)) {
	$utm_source = '';
}

//UTM Medium
if (isset($_POST['utm']['utm_medium'])) {
	$utm_medium = $_POST['utm']['utm_medium'];
} elseif (isset($_POST['utm_medium'])) {
	$utm_medium = $_POST['utm_medium'];
}
elseif (empty($utm_medium)) {
	$utm_medium = '';
}

//UTM Campaign
if (isset($_POST['utm']['utm_campaign'])) {
	$utm_campaign = $_POST['utm']['utm_campaign'];
} elseif (isset($_POST['utm_campaign'])) {
	$utm_campaign = $_POST['utm_campaign'];
}
elseif (isset($_POST['campaign_id'])){
	$utm_campaign = $_POST['campaign_id'];
}
elseif (empty($utm_campaign)) {
	$utm_campaign = '';
}

//UTM Content
if (isset($_POST['utm']['utm_content'])) {
	$utm_content = $_POST['utm']['utm_content'];
} elseif (isset($_POST['utm_content'])) {
	$utm_content = $_POST['utm_content'];
}
elseif (empty($utm_content)) {
	$utm_content = '';
}

//UTM Term
if (isset($_POST['utm']['utm_term'])) {
	$utm_term = $_POST['utm']['utm_term'];
} elseif (isset($_POST['utm_term'])) {
	$utm_term = $_POST['utm_term'];
}
elseif (isset($_POST['google_key'])){
	$utm_term = $_POST['google_key'];
}
elseif (empty($utm_term)) {
	$utm_term = '';
}

//Метки сеанса (если не будет client id создадим сеанс)
if (isset($_POST['ot_kogo'])) {
	$session = 'alloka call';
	$ec = 'call';
	$ea = 'track';
	$cs = isset($session) ? $session : ' '; //источник
	$cn = isset($session) ? $session : ' '; // название рк
	$cm = isset($session) ? $session : ' '; //канал
	$ck = isset($session) ? $session : ' '; //ключ
	$cc = isset($session) ? $session : ' '; //содержание
} else {
	if (isset($_POST['caller_id'])) {
		$session = 'zadarma call';
		$ec = 'call';
		$ea = 'track';
		$cs = isset($session) ? $session : ' '; //источник
		$cn = isset($session) ? $session : ' '; // название рк
		$cm = isset($session) ? $session : ' '; //канал
		$ck = isset($session) ? $session : ' '; //ключ
		$cc = isset($session) ? $session : ' '; //содержание
	} else {
		if (isset($phone)) {
			$ec = 'not cid';
			$ea = 'not cid';
			$cs = isset($utm_source) ? $utm_source : ' ';//источник
			$cn = isset($utm_campaign) ? $utm_campaign : ' '; // название рк
			$cm = isset($utm_medium) ? $utm_medium : ' '; //канал
			$ck = isset($utm_term) ? $utm_term : ' ';//ключ
			$cc = isset($utm_content) ? $utm_content : ' '; //содержание
		}
	}
}




//Переменная cid из аллоки, задармы form или генерируем.
if (isset($_POST['cid'])) {
	$cid = $_POST['cid'];
}
if (isset($_POST['ga_client_id'])) {
	$cid = $_POST['ga_client_id'];
}
if (isset($arrCookies['_ga'])) { //Тильда
	$cid = substr($arrCookies['_ga'], 6, 21); 
}   
if (empty($cid) or $cid == 'cid' or $cid == '–' or $cid == '–') {
	$cid = genCid();
	if ($serach===false) {
		//session($UA, $cid, $cs, $cn, $cm, $ck, $cc, $ec, $ea, $log_file);
	}
}

	

//Описать поля, отправляемые в уведомления телеграм
$url_telegram = 'https://api.telegram.org/bot' . $token . '/sendMessage?chat_id=' . $chat_id . "&text=" . $phone . ": " . $ltype . ": " . $utm_source . ':' . $utm_term . ":" . $duration; 




$data = array(
	'phone' => $phone,
	'cid' => $cid,
	'ltype' => $ltype,
	'name' => $name,
	'calc' => $calc,
	'email' => $email,
	'page' => $page,

	'rec' => $rec,
	'status' => $status,
	'duration' => $duration,
	'token_alloka' => $token_alloka,
	'call_id' => $call_id,
	'call_status' => $call_status,

	'uniq' => $uniq,
	'draftResponseClear' => $draftResponseClear,
	'draftResponse' => $draftResponse,
	'url_google' => $url_google,
	'url_google_clear' => $url_google_clear,

	'UA' => $UA,
	'url_crm' => $url_crm,
	'tags' => $tags,

	'utm_source' => $utm_source,
	'utm_campaign' => $utm_campaign,
	'utm_content' => $utm_content,
	'utm_term' => $utm_term

);





/**
 * Активируем модули
 */

//telegram($data, $url_telegram, $token_telegram, $chat_id_list); //Активировать уведомления телеграм
googleTab($data, $data['draftResponse'], $data['url_google']); //Таблица лидов - все обращения

if ($phone || $email) {
	crm($data); // Отправляем данные в скрипты CRM
}







function googleTab($data, $draftResponse, $url_google)
	{
		$post_data = array (
			"entry.500692132" => $data['cid'],
			"entry.538801486" => $data['phone'],
			"entry.821702325" => $data['email'],
			"entry.844571569" => $data['calc'],
			"entry.1113960352" => $data['ltype'],
			"entry.1923570467" => $data['name'],
			"entry.779676278" => $data['uniq'],
		
			"entry.1051237361" => $data['utm_source'],
			"entry.267667083" => $data['utm_campaign'],
			"entry.1971513980" => $data['utm_content'],
			"entry.317500494" => $data['utm_term'],

			"draftResponse" => "[,,&quot;-".$draftResponse."&quot;]",
			"pageHistory" => "0",
			"fbzx" => $draftResponse
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_google);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
	}
 
function crm($data) //Интеграция с CRM
	{
		$result = file_get_contents($data['url_crm'], false, stream_context_create(array(
			'http' => array(
		    'method'  => 'POST',
		    'header'  => 'Content-type: application/x-www-form-urlencoded',
		    'content' => http_build_query($data)
			    )
		)));
	}

function telegram($data, $url_telegram, $token_telegram, $chat_id_list)
	{
		for ($i=0; $i < count($chat_id_list) ; $i++) {

			$chat_id = $chat_id_list[$i];	
	        $result = file_get_contents($url_telegram, false, stream_context_create(array(
	    	'http' => array(
	        'method'  => 'POST',
	        'header'  => 'Content-type: application/x-www-form-urlencoded',
	        'content' => http_build_query("1")
			    )
		)));
		}
	}


function phoneClear($phone) //Приводим телефон к единому формату (чистим лишние символы)
	{
	    //проверяем на наличие чего-то явно не телефонного и пустой строки
	    if (preg_match('/[^( +)\-\d]/', $phone) || !strlen(trim($phone)) || strlen(trim($phone)) <= 7) {
	        return $phone;
	    }

	    //убираем пробелы и дефисы со скобками
	    $phone = preg_replace("/ |-|[(]|[)]|\+/", "", $phone);

	    //если номер городской, то добавляем префикс как город по умолчанию спб
	    // if (strlen($trimmed) == 7) {
	    //     $trimmed = '7812' . $trimmed;
	    // }

	    //берем 'основной' номер (7 цифр с конца)
	    // preg_match('/.{7}$/', $trimmed, $main);
	    // if (array_key_exists(0, $main)) {
	    //     $main = $main[0];
	    // } else {
	    //     return $phone;
	    // }
	    if ($phone[0] == '9') {
	    	$phone = '7' . $phone;
	    }
	    //получаем префиксы
	    $prefix = substr($trimmed, 0, strpos($trimmed, $main));
	    //выделяем среди префиксов код города
	    preg_match('/\d{3}$/', $prefix, $cityCode);
	    if (array_key_exists(0, $cityCode)) {
	        $cityCode = $cityCode[0];
	    } else {
	        return $phone;
	    }
	    //если кроме кода города в префиксе что-то есть, то это код страны
	    if (strlen($prefix) - strlen($cityCode)) {
	        $countryCode = substr($prefix, 0, strpos($prefix, $cityCode));
	        $countryCode = ($countryCode == 8) ? '+7' : $countryCode;
	        if (preg_match('/^[+]/', $countryCode) && strlen($countryCode)) {
	            $countryCode = preg_replace('/^[+]/', '', $countryCode);
	        }
	    } else {
	        $countryCode = '7';
	    }
	    $cityCode = preg_replace('/[()]/', '', $cityCode);
	    return $cityCode . $main;
	}

function session($UA, $cid, $cs, $cn, $cm, $ck, $cc, $ec, $ea, $log_file) //отправляем событие о звонке и сеанс в Analytics
	{
		$data = array(
			'v' => 1,
			'tid' => $UA, //Номер счетчика Google Analytics
			'cid' => $cid,
			't' => 'event',
			'ec' => $ec,      //Категория цели
			'ea' => $ea,	//Действие цели
			'cs' => $cs, //источник
			'cn' => $cn, // название рк
			'cm' => $cm, //канал
			'ck' => $ck, //ключ
			'cc' => $cc, //содержание
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

		writeToLog($data, 'Analytics',$log_file);
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