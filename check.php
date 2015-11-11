<?
session_start();
$sessionid = session_id(); // получение id сессии
include('db/db_login.php'); // подключаем БД
$checkplaces = '/\s|\,|\./'; // регулярка на проверку, что пары слов являются одиночными словами
if (isset($_POST['txtsubject'])) { // получение запроса на рерайт
if (!isset($_COOKIE['timeoutquery'])) { // проверка на таймаут
setcookie('timeoutquery', 'yes', time() + 15); // установка таймаута
$subject = strip_tags($_POST['txtsubject']);
$subject = htmlspecialchars($subject);
if (strlen($subject)>90) { // минимальное кол-во символов для запроса рерайта
$result = mysql_query("(SELECT * FROM items) UNION (SELECT id, fromplace, toplace FROM preitems WHERE addsession = '".$sessionid."')"); //выборка из таблиц items и preitems
$patterns = array();
$replacements = array();
while($places = mysql_fetch_array($result)) {
      $patterns[] = '/(\,|\.|\s|^)'.$places['fromplace'].'(\,|\.|\s|\:|\;)/i'; // регулярное выражение для рерайта
	  $replacements[] = '<span style="color:#b94a48">$1'.$places['toplace'].'$2</span> '; // значение, на которое будет сменено слово по регулярному выражению
}
$result = preg_replace($patterns, $replacements, $subject, -1 , $count); // замена слов
similar_text($subject, $result, $similar_percent); // степень схожести текстов
$originality = 100-(int)($similar_percent); 
switch ($originality) {
case ($originality<=25):
$originality = '<span style="color:#b94a48">+ '.$originality.'%</span>';
break;
case ($originality<=50):
$originality = '<span style="color:#ff7518">+ '.$originality.'%</span>';
break;
case ($originality<=100):
$originality = '<span style="color:#3fb618">+ '.$originality.'%</span>';
break;
}
$fp = fopen('files/'.$sessionid.'.doc', 'w'); // Открытие файла
$string_to_write = iconv ('utf-8', 'windows-1251', $result); // Перекодировка строки
fwrite($fp, $string_to_write); // Запись в файл
fclose($fp); //Закрытие файла
 echo '<div class="panel panel-default">
  <div class="panel-heading">Результат '.$originality.' оригинальности<span class="label label-default pull-right">'.$count.'</span></div>
  <div class="panel-body">
  '.nl2br($result).'
  </div>
  <div class="panel-footer"><a href = "files/'.$sessionid.'.doc"><i class="icon-download-alt"></i> Скачать</a></div>
</div>';
} else {
echo '<div class="alert alert-danger messageatshowing">переписываемый текст не может быть меньше 50 символов</div>'; //ошибка кол-ва символов в запросе на перепись
}
 }
else {
 echo '<div class="alert alert-danger messageatshowing">запросы можно отправлять с периодичностью в 15 секунд</div>';
 }
 }
if (isset($_POST['fromplaceparam']) && isset ($_POST['toplaceparam'])) { // получение пар слов
$fromplaceparam = strip_tags($_POST['fromplaceparam']);
$fromplaceparam = htmlspecialchars($fromplaceparam);
$fromplaceparam = mysql_real_escape_string($fromplaceparam);
$fromplaceparam = mb_strtolower($fromplaceparam, 'utf-8'); 
$toplaceparam = strip_tags($_POST['toplaceparam']);
$toplaceparam = htmlspecialchars($toplaceparam);
$toplaceparam = mysql_real_escape_string($toplaceparam);
$toplaceparam = mb_strtolower($toplaceparam, 'utf-8'); 
if (strlen($fromplaceparam)<6 or strlen($toplaceparam)<6) {
echo '<div class="alert alert-danger messageatshowing">пары слов не могут быть меньше 4-х символов</div>';
} else {
if (preg_match($checkplaces, $fromplaceparam) or preg_match($checkplaces, $toplaceparam)) {
echo '<div class="alert alert-danger messageatshowing">допустимо использовать только одно слово для каждой из пар</div>';
}
else {
$result = mysql_query("SELECT * FROM items WHERE fromplace = '".$fromplaceparam."'"); // запрос на заменяемое слово
if (mysql_num_rows($result) > 0) {
echo '<div class="alert alert-danger messageatshowing">заменяемое слово уже есть в базе</div>';
}
else {
$result2 = mysql_query("SELECT * FROM preitems WHERE fromplace = '".$fromplaceparam."' and addsession = '".$sessionid."'");
if (mysql_num_rows($result2) > 0) {
$fromplaceparambig = mb_convert_case($fromplaceparam, MB_CASE_TITLE, "UTF-8");
$toplaceparambig = mb_convert_case($toplaceparam, MB_CASE_TITLE, "UTF-8");
$upquote = mysql_fetch_array($result2);
mysql_query("UPDATE preitems SET fromplace = '".$fromplaceparam."', toplace = '".$toplaceparam."' WHERE id = ".$upquote['id']);
mysql_query("UPDATE preitems SET fromplace = '".$fromplaceparambig."', toplace = '".$toplaceparambig."' WHERE id = ".($upquote['id']+1));
echo '<div class="alert alert-success messageatshowing">успешно обновлено</div>';
}
else {
$fromplaceparambig = mb_convert_case($fromplaceparam, MB_CASE_TITLE, "UTF-8");
$toplaceparambig = mb_convert_case($toplaceparam, MB_CASE_TITLE, "UTF-8");
mysql_query ("INSERT INTO preitems(fromplace, toplace, addsession) VALUES('$fromplaceparam', '$toplaceparam', '$sessionid'), ('$fromplaceparambig', '$toplaceparambig', '$sessionid')"); //добавление пары в премодерационную базу
echo '<div class="alert alert-success messageatshowing">успешно добавлено</div>';
}
}
}
}
}
if (isset($_POST['synonym'])) {
$synonymword = strip_tags($_POST['synonym']);
$synonymword = htmlspecialchars($synonymword);
$synonymword = mysql_real_escape_string($synonymword);
if (strlen($synonymword)>6) {
if (!preg_match($checkplaces, $synonymword)) {
include('lib/simple_html_dom.php');
$html = file_get_html('http://jeck.ru/tools/SynonymsDictionary/'.$synonymword);
echo '<br>';
foreach($html->find('.word a') as $element)
echo mb_strtolower('<a class="synonymelement" href="#">'.$element->plaintext.'</a> ', 'utf-8'); 
echo '<br>';
}
else {
echo '<div class="messageatshowing"><br><div class="alert alert-danger">допустимо использовать только одно слово</div></div>';
}
}
else {
echo '<div class="messageatshowing"><br><div class="alert alert-danger">искомое слово не может быть меньше 4-х символов</div></div>'; //ошибка кол-ва символов в запросе на поиск синонима
}
}
?>
	<script type="text/javascript">
	$(function() {
	 $(".synonymelement").click(function() { /* нажата ссылка синонима */
	 $("#toplacetext").val($(this).text());
	  $("#toplacetext").focus();
	 });
	 $(".messageatshowing").fadeOut(5000);
	 		});
	</script>