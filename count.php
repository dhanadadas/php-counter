<?php

/*
 * Данные базы
 *
 */
$INFO['sql_host'] = "localhost";
$INFO['sql_user'] = "count";
$INFO['sql_pass'] = "pass";
$INFO['sql_database'] = "count";


/*
 * Проверка существования записи с данным url
 *
 */
function searchID($id)
{
$result = mysql_query ("SELECT * FROM `my_log` WHERE `page_id` LIKE '".$id."'");
$num_rows = mysql_num_rows($result);
if ($num_rows>0)
{
    return True;
}
else
{
    return False;
}
}

/*
 * Чтение из базы данных
 *
 */
function MySQLRead($id)
{
$id = addslashes($id);
$result = mysql_query ("SELECT * FROM `my_log` WHERE `page_id` LIKE '".$id."'");
return (array)mysql_fetch_assoc($result);
}

/*
 * Обновление времени
 *
 */
function UpdateTime($id, $time)
{
$id = addslashes($id);
$time = addslashes($time);
$result = mysql_query ("UPDATE `my_log` SET `date` = '".$time."' WHERE `page_id` = '".$id."'");
return $result;
}

/*
 * Обновление счетчика
 *
 */
function UpdateCount($id, $all, $today)
{
$id = addslashes($id);
$result = mysql_query ("UPDATE `my_log` SET `all` = '".$all."',`today` = '".$today."' WHERE `page_id` = '".$id."'");
return $result;
}

/*
 * Запись значений по умолчанию
 *
 */
function Default_Write($id)
{
$id = addslashes($id);
$result = mysql_query ("INSERT INTO `my_log` ( `page_id` , `all` , `today` , `date` ) VALUES ('".$id."' , 1 , 1 , '".(time()+60*60*24)."');");
return $result;
}

$unical_page_id_gid = md5($_SERVER['REQUEST_URI']); // получение md5() хэша из url страницы

$link = mysql_connect($INFO['sql_host'], $INFO['sql_user'], $INFO['sql_pass']); // Соединение с MySQL
mysql_select_db ($INFO['sql_database']); // Выбор базы данных

if (!searchID($unical_page_id_gid)) // существует ли запись с таким id
{
    Default_Write($unical_page_id_gid); // запись всех значений по умолчанию
}
else // если не существует
{
$tmp = MySQLRead($unical_page_id_gid); // считаем значения
$all = $tmp['all'] + 1;
$today = $tmp['today'] +1;
if (time()>=$tmp['date']) // если сутки с момента записи прошли
{
    UpdateTime($unical_page_id_gid, (time()+60*60*24)); // обновим дату
    UpdateCount($unical_page_id_gid, $all, $today/4); // обновим счетчики
    $tmp1 = MySQLRead($unical_page_id_gid); // считаем значения
    $today = $tmp['today'];
}
else // если еще нет
{
    UpdateCount($unical_page_id_gid, $all, $today);

    function num2word($num, $words)
	{
    $num = $num % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    switch ($num) {
        case 1: {
            return($words[0]);
        }
        case 2: case 3: case 4: {
            return($words[1]);
        }
        default: {
            return($words[2]);
        }
    }
}
//$people = num2word($today, array('человек', 'человека', 'человек'));
    $hit= num2word($today, array('просмотр', 'просмотра', 'просмотра'));
}

/*
 * Определяем получившиеся значения в константу
 *
 */
define("Today_and_all_counter", "Популярный товар: $today $hit за 24 часа");
//define("Today_and_all_counter", "Популярный товар: $all. За 24 часа: $today");
define("Today_and_all_counter1", "За последние 24 часа: $today $hit");
}
mysql_close($link);
?>

<?php
// примеры вывода на странице
    echo "<div style=\"font-size: 11px;\"><img src=\"/https://ir.ebaystatic.com/rs/v/tnj4p1myre1mpff12w4j1llndmc.png\" width=\"11\" height=\"9\" alt=\"Популярный товар\"> ".Today_and_all_counter1."</div>";

?>