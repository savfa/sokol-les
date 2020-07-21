<?php
header("Content-Type: text/html; charset=utf-8");
$name = htmlspecialchars($_POST["name"]);
$tel = htmlspecialchars($_POST["tel"]);
$width = htmlspecialchars($_POST["width"]);
$thickness = htmlspecialchars($_POST["thickness"]);
$count = htmlspecialchars($_POST["count"]);
$radio = htmlspecialchars($_POST["a"]);

$refferer = getenv('HTTP_REFERER');
$date=date("d.m.y"); // число.месяц.год  
$time=date("H:i:s"); // часы:минуты:секунды 
$myemail = "info@sokol-les.ru"; // e-mail администратора


// Отправка письма администратору сайта

$tema = "Новая заявка на заказ";
$message_to_myemail = "Новый заказ:
<br><br>
Имя: $name<br>
Телефон: $tel<br>
Наименование: $radio<br>
Ширина: $width мм<br>
Толщина: $thickness мм<br>
Количество: $count шт<br>
Источник (ссылка): $refferer
";

mail($myemail, $tema, $message_to_myemail, "From: Сокол Лес <info@sokol-les.ru> \r\n Reply-To: Сокол Лес \r\n"."MIME-Version: 1.0\r\n"."Content-type: text/html; charset=utf-8\r\n" );

// Сохранение инфо о лидах в файл leads.xls

$f = fopen("leads.xls", "a+");
fwrite($f," <tr>");    
fwrite($f," <td>$name</td> <td>$tel</td> <td>$radio</td> <td>$width</td> <td>$thickness</td> <td>$count</td> <td>$date</td> <td>$time</td>");       
fwrite($f," </tr>");  
fwrite($f,"\n ");    
fclose($f);

?>