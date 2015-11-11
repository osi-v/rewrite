<?
include('db/db_login.php'); // подключаем БД
?>
<html>
  <head>
    <title>rewriter</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script src="js/cookie/jquery.cookie.js"></script>
	<script type="text/javascript">
	$(function() { 
	 $("#pushtext").click(function() { /* нажата кнопка для отправки формы для рерайта */
	 var searchString = $("#textsubject").val();
        var data = 'txtsubject='+ searchString;
		if(searchString) {
	 $.ajax({
                type: "POST",
                url: "check.php",
                data: data,
                beforeSend: function(html) {
                $("#procession").html('');
               },
               success: function(html){
               $("#procession").show();
               $("#procession").append(html);
              }
            });
			}
        return false;
	 });
	 function searchsynonyms() { /* функция отправки данных для поиска синонимов */
	 var searchString = $("#fromplacetext").val();
        var data = 'synonym='+ searchString;
		if(searchString) {
	 $.ajax({
                type: "POST",
                url: "check.php",
                data: data,
                beforeSend: function(html) {
               },
               success: function(html){
			   $("#pushsyn").html('<i class="icon-search"></i>');
               $("#showsynonyms").show();
               $("#showsynonyms").html(html);
              }
            });
			}
        return false;
			 }
	 	  $("#fromplacetext").keyup(function(){ /* производится действие с полем объект */
		  if($("#fromplacetext").val().length<=3){
		  $("#showsynonyms").html("");
		  } 
	 });
	 	 $("#additemsbutton").click(function() { /* нажата кнопка отправки пар слов */
	 var param1 = $("#fromplacetext").val();
     var param2 = $("#toplacetext").val();
	if  ((param1) && (param2)) {
	 $.ajax({
                type: "POST",
                url: "check.php",
                data: {
			    fromplaceparam: param1,
				toplaceparam: param2
				},
                beforeSend: function(html) {
                $("#addresult").html('');
               },
               success: function(html){
			   $("#fromplacetext").val("");
			   $("#toplacetext").val("");
			   $("#showsynonyms").html("");
               $("#addresult").show();
               $("#addresult").append(html);
              }
            });
			}
        return false;
	 });
	 	 	  $("#pushsyn").click(function(){ /* кнопка поиска синонимов */
		  $(this).html('<i class="icon-refresh icon-spin"></i>');
		  searchsynonyms();
	 });
	 	 	  $("#textsubject").keyup(function(){ /* производится действие с полем текста */
		 $("#symbols").html($("#textsubject").val().length); 
	 });
	 $("#textsubject").select( function () { /* производится выделение текста */
	 $("#showsynonyms").html("");
	 var txt = window.getSelection().toString();
	 txt = txt.trim();
$("#fromplacetext").val(txt);
});
		});
	</script>
		  </head>
  <body>
<div class="container">
<div class="row">
  <div class="col-sm-12 col-md-3 col-lg-3">

  </div>
  <div class="col-sm-12 col-md-6 col-lg-6">
  <a class="navbar-brand" href="http://rerait.ru">rewriter</a>
  
  </div>
  <div class="col-sm-12 col-md-3 col-lg-3">

  </div>
</div>
<div class="row">
  <div class="col-sm-12 col-md-3 col-lg-3">
    <form action="check.php" method="POST">
  <textarea class="form-control" name = "textsubject" id = "textsubject" tabindex="1" rows="11" autofocus></textarea>
  <span class="help-block" id="symbols">0</span>
<button class="btn btn-primary btn-md btn-block" name = "pushtext" id="pushtext"><i class="icon-edit"></i> Переписать</button><br>
  </form>
  </div>
  <div class="col-sm-12 col-md-6 col-lg-6">
  <div id="procession" align="justify">
  <div class="panel panel-default">
  <div class="panel-body">
<p><strong>Автоматическое редактирование</strong><br>
Для начала работы с сервисом, просто поместите текст в поле ввода и нажмите кнопку "Переписать".<br>
Вы получите свой текст, отредактированный с помощью основной базы замен.</p>

<p><strong>Повышение оригинальности вашего текста</strong><br>
Добавляйте новые пары слов или обновляйте старые, используя обширную базу синонимов.<br>
Составленные вами пары замен будут активны в рамках вашей работы с сервисом.</p>

<p><strong>Сохранение результатов</strong><br>
Вы можете скачать отредактированный текст в формате doc.</p>

<p><strong>Улучшение сервиса</strong><br>
Лучшие добавленные пары замен будут перемещены в основную базу и станут доступны всем.</p>
    </div>
</div>
</div>
  </div>
  <div class="col-sm-12 col-md-3 col-lg-3">
<form action="check.php" method="POST">
  <div class="input-group">
<input class="form-control" id="fromplacetext" tabindex="2" placeholder="Объект">
      <span class="input-group-btn">
        <button class="form-control btn btn-default" type="button" name = "pushsyn" id="pushsyn"><i class="icon-search"></i></button>
      </span>
</div>
  <div id="showsynonyms"> 

</div>
<br>
<input class="form-control" id="toplacetext" placeholder="Заменить на" tabindex="3"><br>
<button type="submit" class="btn btn-default btn-md btn-block" id="additemsbutton"><i class="icon-pushpin"></i> Добавить</button>
</form>
  <div id="addresult">
</div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12 col-md-12 col-lg-12">
<div class="well">
rewriter © 2013
</div>
  </div>
</div>
</div>
     </body>
</html>