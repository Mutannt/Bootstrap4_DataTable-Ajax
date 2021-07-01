
<? require_once("$_SERVER[DOCUMENT_ROOT]/../db/dal.inc.php");?>
<?php
	//Обработчик нажатия на кнопку "Сохранить"
	if(isset($_POST["#action"])) {
		$f_name=mysqli_real_escape_string($cms_db_link,$_POST["f_name"]);
		$f_price=(double)$_POST["f_price"];
		$f_quantity=(int)$_POST["f_quantity"];
		$f_year=(int)$_POST["f_year"];
		

		$errmsg="";
		try {
			
			DBCreateTovar($f_name,$f_price,$f_quantity, $f_year, $f_country, $f_description, $id, $f_req, $f_ram);
			
			//Редирект (перенаправление) для предотвращения дублирования
			//информации в БД
			header("Location: $_SERVER[PHP_SELF]?success");
		}catch(Exception $ex){
			$errmsg=$ex->getMessage();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
		<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" /> -->
		<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>		
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
		<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> -->

		<!-- Наша собственная библиотека my_input_validator-->
		<script src="my_input_validator.js"></script>
	  
		<script type="text/javascript">
			//Объектный подход	
			$(function() {
			  // Регулярные выражения для проверки введённых значений
			  var reg_pCena = /^[0-9]+(\.[0-9]{2})?$/;
			  var reg_pKol = /^[0-9]+$/;
			  var reg_pGod = /^[0-9]+$/;
			  var reg_pProc = /^[0-9]+$/;
			  var reg_pPamyat = /^[0-9]+$/;
			  
			  //Создание экземпляра объекта формы
			  var my_form = new InputForm("#user_form");
			  
			  //Создание экземпляров объекта полей ввода
			  var f_name = new InputField("#f_name",my_form);
			  var f_price = new InputField("#f_price",my_form);
			  var f_quantity = new InputField("#f_quantity",my_form);
			  var f_year = new InputField("#f_year",my_form);
			  //var f_req = new InputField("#f_req",my_form);
			  //var f_ram = new InputField("#f_ram",my_form);

			  //Подписка на callback-функцию beforesubmit, объявленную в 
			  //классе InputForm, и вызываемую при попытке отправки формы
			  my_form.beforesubmit = function() {
				  //Собственно процесс валидации				
				  f_name.validate(f_name.v()=="");
				  f_price.validate(!reg_pCena.test(f_price.v()));
				  f_quantity.validate(!reg_pKol.test(f_quantity.v()));
				  f_year.validate(!reg_pGod.test(f_year.v()));
				  //f_req.validate(!reg_pProc.test(f_req.v()));
				  //f_ram.validate(!reg_pPamyat.test(f_ram.v()));
			  }	
		  });
		  $(function() {
				var dataTable = $('#tovar_data').DataTable({
						"language": {"url":"http://cdn.datatables.net/plug-ins/1.10.20/i18n/Russian.json"},
						"processing":true,
						"serverSide":true,
						"order":[],
						"ajax":{
							url:"/rest/tovary",
							type:"POST"
						},
						"columnDefs":[
							{
								"targets":[0, 7, 8], // Столбцы, по которым не нужна сортировка
								"orderable":false,
							},
						],
				});	

				dataTable.ajax.reload();
				
				$(document).on('submit', '#user_form', function(event){
					event.preventDefault();					
					
					//data.Nazvanie (получить значение селектора)
					  var tovary_info = {
					  	"Nazvanie":$("#f_name").val(),
					  	"Cena":$("#f_price").val(),
					  	"Kol":$("#f_quantity").val(),
					  	"God":$("#f_year").val(),
					  	"Strana":$("#f_country").val(),
					  	"Opisanie":$("#f_description").val(),
						"Proc":$("#f_req").val(),
						"Pamyat":$("#f_ram").val()

					  }
					
					

					if($("#f_image")[0].files.length>0){
						alert("Привет1");
						var ImageFile = $("#f_image")[0].files[0];
						read = new FileReader();
						read.readAsBinaryString(ImageFile);
						read.onloadend=function(){
							tovary_info.ImageFileName=ImageFile.name;
							tovary_info.Image=window.btoa(read.result);
							send_form_data();
						}
					}
					else {
						alert("Привет2");
						send_form_data();
					}
					

					//Функция отправки данных
					function send_form_data(){
						var method="PUT";
						if($("#userModal #operation").val()==1) {
							method="PATCH";
							tovary_info.ID = $("#tovar_id").val();						
						}		
							
					
						$.ajax({
								url:"/rest/tovar",
								method: method,
								data: JSON.stringify(tovary_info),
								headers: {
									"Content-type":"application/json"
								},
								success:function(data)
								{									
									$('#user_form')[0].reset();
									$('#userModal').modal('hide');
									dataTable.ajax.reload();
								}
						});
					}
				});
				
				$(document).on('click', '.update', function(event){
					//Режим редактирования (кнопка Редактировать)
					var id = $(this).attr("id");					
					
					$.ajax({
								url:"/rest/tovar?id="+id,
								method:'GET',
								dataType: "json",								
								success:function(data)
								{
									//Заголовок окна
									$('.modal-title').text("Редактировать компьютер");
									
									//Вывод принятых с сервера данных в поля формы
									$("#userModal #f_name").val(data.Nazvanie);
									$("#userModal #f_price").val(data.Cena);
									$("#userModal #f_quantity").val(data.Kol);
									$("#userModal #f_year").val(data.God);
									$("#userModal #f_country").val(data.Strana);
									$("#userModal #f_description").val(data.Opisanie);
									$('#userModal #tovar_id').val(id);
									$("#userModal #f_req").val(data.Proc);
									$("#userModal #f_ram").val(data.Pamyat);									
									
									//Флаг операции (1 - редактирование)
									$("#userModal #operation").val("1");
									
									//Текст на кнопке
									$("#userModal #action").val("Сохранить изменения");
									
									//Отобразить форму
									$('#userModal').modal('show');									
								}
							});
					
					event.preventDefault();
				});
				
				$("#add_button").click(function() {
					//Режим добавления (кнопка Добавить)
					
					//Заголовок окна
					$('.modal-title').text("Добавить компьютер");
					//Текст на кнопке
					$("#userModal #action").val("Добавить");
					//Флаг операции (0- добавление)
					$("#userModal #operation").val("0");
				});
				
				$(document).on("click",".delete",function() {
					//Режим удаления (кнопка Удалить)
					var tovar_id = $(this).attr("id");					
					
					if(confirm("Действительно удалить?"))
					{
						$.ajax({
							url:"/rest/tovar?id="+tovar_id,
							method:"DELETE",							
							success:function(data)
							{								
								dataTable.ajax.reload();
							}
						});
					}
					else
					{
						return false;	
					}
				});				
			});
	  	</script>



	</head>
	<body>
		<div class="container box">
			<div class="table-responsive">
				<br />
				<div align="right">
					<button type="button" id="add_button" data-toggle="modal" data-target="#userModal" class="btn btn-info btn-lg">Добавить</button>
				</div>
				<br /><br />
				<table id="tovar_data" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="10%">Изображение</th>
							<th width="10%">Название</th>
							<th width="10%">Цена</th>
							<th width="10%">Количество</th>
							<th width="10%">Год</th>
							<th width="10%">Страна</th>
							<th width="10%">Описание</th>
							<th width="10%"></th>
							<th width="10%"></th>
						</tr>
					</thead>
				</table>				
			</div>
		</div>
		
		<div id="userModal" class="modal fade">
			<div class="modal-dialog">
				<form method="post" id="user_form" enctype="multipart/form-data">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Добавить компьютер</h4>
						</div>
						<div class="modal-body">
							<label for="f_name">Название</label>
							<input type="text" name="f_name" id="f_name" class="form-control" />
							<div class="invalid-feedback">
								<div>Поле Название не заполнено</div>
							</div>
							<br/>
							<label for="f_price">Цена</label>
							<input type="text" name="f_price" id="f_price" class="form-control" />
							<div class="invalid-feedback">
								<div>Поле Цена не заполнено или содержит недопустимые символы</div>
							</div>
							<br/>
							<label for="f_quantity">Количество</label>
							<input type="text" name="f_quantity" id="f_quantity" class="form-control" />
							<div class="invalid-feedback">
								<div>Поле Количество не заполнено или содержит недопустимые символы</div>
							</div>
							<br/>
							<label for="f_year">Год</label>
							<input type="text" name="f_year" id="f_year" class="form-control" />
							<div class="invalid-feedback">
								<div>Поле Год не заполнено или содержит недопустимые символы</div>
							</div>
							<br/>
							<label for="f_country">Страна</label>
							<select class="custom-select" id="f_country" name="f_country">
								<option value="0">-- Выберите страну --</option>
								<option value="1">Россия</option>
								<option value="2">США</option>
								<option value="3">Германия</option>
							</select>
							<br/>
							<label for="f_req">Частота ЦПУ</label>
							<input type="text" class="form-control" id="f_req" name="f_req">
							<div class="invalid-feedback">
								Поле Год выпуска не заполнено или содержит недопустимые символы
							</div>
							<br/>
							<label for="f_ram">Объём ОЗУ</label>
							<input type="text" class="form-control" id="f_ram" name="f_ram">
							<div class="invalid-feedback">
								Поле Объём ОЗУ не заполнено или содержит недопустимые символы
							</div>
							<br/>
							<label>Накопители информации:</label>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" id="customcheckbox1" name="customcheckbox1" class="custom-control-input">
								<label class="custom-control-label" for="customcheckbox1">CD-ROM</label>
							</div>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" id="customcheckbox2" name="customcheckbox2" class="custom-control-input">
								<label class="custom-control-label" for="customcheckbox2">FDD</label>
							</div>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" id="customcheckbox3" name="customcheckbox3" class="custom-control-input">
								<label class="custom-control-label" for="customcheckbox3">Магнитная лента </label>
							</div>
							<br/>
							<label for="f_description">Описание</label>
              				<textarea class="form-control" id="f_description" name="f_description"></textarea>
							<br/>
							<fieldsset class="form-group">
								<label for="f_image">Изображение</label>
								<input type="file" name="f_image" id="f_image"/>
							</fieldsset>


							
						</div>
							<div class="modal-footer"><!-- Подвал модальной формы -->
							<input type="hidden" name="tovar_id" id="tovar_id" />
							<input type="hidden" name="operation" id="operation" />
							<input type="submit" name="action" id="action" class="btn btn-success" value="Добавить" />
							<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>