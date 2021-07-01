<? 
 require_once("$_SERVER[DOCUMENT_ROOT]/../includes/flight/Flight.php");
 require_once("$_SERVER[DOCUMENT_ROOT]/../db/dal.inc.php");
  
 function CreateUser() {
	//Получаем имя принятого от клиента файла
	$imgFileName=Flight::request()->data["ImageFileName"];
	//Извлекаем из мени файла расширение
	$ext=substr($imgFileName, 1+strpos($imgFileName,"."));
	//Создаём новый  товар
	DBCreateTovar(
		Flight::request()->data["Nazvanie"],
		Flight::request()->data["Cena"],
		Flight::request()->data["Kol"],
		Flight::request()->data["God"],
		Flight::request()->data["Strana"],
		Flight::request()->data["Opisanie"],
		Flight::request()->data["ID"],// В базе один, id-шник а тут другой????
		Flight::request()->data["Proc"],
		Flight::request()->data["Pamyat"],
		$ext
	);
	//Получаем его id
	$tovar_id=_DBInsertID()-8;
	//Список расширений файлов, разрешённых к загрузке
    $allowed_ext=Array("png", "jpg", "gif", "bmp");
    //Путь к католог, куда должны быть загружены файлы.$_COOKIE
    $image_path="$_SERVER[DOCUMENT_ROOT]/mysite/images";
	//Проверяем 
	if(in_array(strtolower($ext), $allowed_ext))
    {
		//Сохранение принятого файла на диск сервера
		file_put_contents(
			//"$_SERVER[DOCUMENT_ROOT]/../images/$imgFileName",
			"$image_path/$tovar_id.$ext",
			base64_decode(Flight::request()->data["Image"])
		);
	}
	
 }
 Flight::route('PUT /rest/tovar',"CreateUser");
 
 function ReadUser($id) {
	Flight::json(DBGetUser($id));
 }
 Flight::route('GET /rest/tovar\?id=@id',"ReadUser");
 
 function ReadUsers() {
	$data=Array();
	//$db_rec = mysql_fetch_array($res,MYSQL_ASSOC);

	while($row=DBFetchTovar(
		$_POST["search"]["value"],
		$_POST['order']['0']['column'],
		$_POST['order']['0']['dir'],
		$_POST['start'],$_POST["length"])) 
	{
		$img_path="mysite/images/$row[ID].$row[RashIzobraj]";
		$data[]=Array('<img src="'.$img_path.'" class="img-thumbnail center-block\">',
		$row["Nazvanie"],$row["Cena"],$row["Kol"],$row["God"],$row["Strana"],$row["Opisanie"],
		'<button type="button" name="update" id="'.$row["ID"].'" class="btn btn-warning btn-xs update">Редактировать</button>',
		'<button type="button" name="delete" id="'.$row["ID"].'" class="btn btn-danger btn-xs delete">Удалить</button>');

	}


	//Отправка данных клиенту в формате JSON (JavaScript Object Notation)
	Flight::json(Array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	count($data),
			"recordsFiltered"	=>	DBCountAllUsers(),
			"data"				=>	$data
	));
	
 }
 Flight::route('POST /rest/tovary',"ReadUsers");
 
 function UpdateUser() {
	 DBUpdateTovar(
		Flight::request()->data["ID"],
		Flight::request()->data["Nazvanie"],
		Flight::request()->data["Cena"],
		Flight::request()->data["Kol"],
		Flight::request()->data["God"],
		Flight::request()->data["Strana"],
		Flight::request()->data["Opisanie"],
		Flight::request()->data["ID"],
		Flight::request()->data["Proc"],
		Flight::request()->data["Pamyat"]
	);
 }
 Flight::route('PATCH /rest/tovar',"UpdateUser");
 
 function DeleteUser($id) {
	 DBDeleteUser($id);
 }
 Flight::route('DELETE /rest/tovar\?id=@id',"DeleteUser"); 

 Flight::start();
