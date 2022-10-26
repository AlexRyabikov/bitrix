<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="win-1251">
<title>Interface</title>
<style>
body{background: #333; color: beige;}
p{margin: 0 0 4px;}
</style>
<script type="text/javascript" src="/bitrix/js/main/jquery/jquery-2.1.3.min.js"></script>
</head>
<body>

<div>
	<h1>Interface</h1>
	<form id="form-ajax">
		<p>ИБ характеристик</p>
		<input type="number" name="IB" min="1" value="84"><br>
		
		<p>Размер части</p>
		<input type="number" min="1" name="num" value="5"><br>		
		<label><input type="checkbox" name="auto"><span> Авто</span></labl><br><br>
		
		<input type="submit" name="submit" value="submit"><br>
	</form>
	<button onclick="$('#holding').html('');">Clear</button>
	<div id="holding"></div>
</div>

<script>
var dataAjax = {
	totalPages: 0,
	IB: 0,
	pageSize: 0,
	pageNum: 1,
	section: '',
};

function ajaxRequest(){
	$.ajax({
		type: 'POST',
		url: '/heredoc/ajax.php',
		dataType: 'json',
		data: dataAjax,
		success: function(data){
			//$("#holding").append(data);
			console.log(data);
			dataAjax.pageNum = +data.curPart+1;
			dataAjax.totalPages = data.totalParts;
			
			if(data.error != ''){
				$("#holding").append(data.error+"<br>");
			}else if(dataAjax.pageNum <= dataAjax.totalPages){
				$("#holding").append("<br>Партия "+data.curPart+"/"+data.totalParts+"<br>"+data.items+"------------");
				if($('#form-ajax input[name="auto"]').is(":checked"))
					ajaxRequest();//alert("autostart");
				
			}else{
				$("#holding").append("<br>Партия "+data.curPart+"/"+data.totalParts+"<br>"+data.items+"------------");
				$("#holding").append("<br>~~~ Конец ~~~");
			}
		},
		error:  function(xhr, str){
			alert('Возникла ошибка: ' + xhr.responseCode);
		}
	});
}

$('#form-ajax').submit(function(){
	//var res = $(this).serialize();
	dataAjax.IB = $('#form-ajax input[name="IB"]').val();
	dataAjax.pageSize = $('#form-ajax input[name="num"]').val();
	dataAjax.section = $('#form-ajax input[name="section"]').val();
	ajaxRequest();	
	event.preventDefault();
});
</script>
</body>
</html>