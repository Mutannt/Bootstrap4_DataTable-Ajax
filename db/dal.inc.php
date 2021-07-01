<?php require_once("$_SERVER[DOCUMENT_ROOT]/../db/common.dal.inc.php");

//CRUD - Create Read Update Delete
//Создание нового пользователя (Create)
function DBCreateTovar($f_name,$f_price,$f_quantity, $f_year, $f_country, $f_description, $id, $f_req, $f_ram, $ext) {
	//Предотвращение SQL-инъекций
	$f_name=_DBEscString($f_name);
	$f_price=(int)$f_price;
	$f_quantity=(int)$f_quantity;
	$f_year=(int)$f_year;
	$f_country=(int)$f_country;
	$f_description=_DBEscString($f_description);
	$id=(int)$id;// В базе один, id-шник а тут другой????
	$f_req=(int)$f_req;
	$f_ram=(int)$f_ram;
	
	//Выполнение запроса к БД
	_DBQuery("
		INSERT INTO tovary(Nazvanie,Cena,Kol,God,Strana,Opisanie,Rashizobraj)
		VALUES ('$f_name','$f_price','$f_quantity','$f_year','$f_country','$f_description','$ext')
	");
	//Выполнение запроса 2 к БД
	//_DBQuery("
	//	INSERT INTO compinterfeisy(ID_Tovara,ID_Interfeisa)
	//	VALUES ('$ID_Tovara,'$')
	//");
	//Выполнение запроса к БД
	_DBQuery("
		INSERT INTO comp(ID_Tovara,Proc,Pamyat)
		VALUES ('$id','$f_req','$f_ram')
	");

}

//Получение одного пользователя (Read)
function DBGetUser($id) {
	//Предотвращение SQL-инъекций
	$id=(int)$id;
	//Выполнение запроса
	return _DBGetQuery("SELECT * FROM tovary WHERE ID=$id");
}

//Получение списка пользователей (Read)
function DBFetchTovar($search_string,$sort,$dir,$s,$l) {
	//Предотвращение SQL-инъекций
	$search_string=_DBEscString($search_string);
	$sort=(int)$sort;
	$dir=_DBEscString($dir);
	$s=(int)$s;
	$l=(int)$l;	
	
	//Формирование запроса
	$limit="LIMIT $s,$l";
	
	$where_like="";
	if(trim($search_string)!="") {
		$search_string=_DBEscString($search_string);
		$where_like="WHERE Nazvanie LIKE \"%$search_string%\"";
	}

	$order="";
	if(trim($sort)!="" && $dir!="") 
		$order="ORDER BY ".((int)$sort+2)." $dir";	
	
	//Выполнение запроса
	return _DBFetchQuery("SELECT * FROM tovary $where_like $order $limit");
}

//Подсчёт общего числа пользователей в базе (Read)
function DBCountAllUsers() { 
	return _DBRowsCount(_DBQuery("SELECT * from tovary"));
}

//Редактирование элемента (Update)
function DBUpdateTovar($id,$f_name,$f_price,$f_quantity, $f_year, $f_country, $f_description, $id, $f_req, $f_ram) {
	//Предотвращение SQL-инъекций
	$id=(int)$id;
	$f_name=_DBEscString($f_name);
	$f_price=(int)$f_price;
	$f_quantity=(int)$f_quantity;
	$f_year=(int)$f_year;
	$f_country=(int)$f_country;
	$f_description=_DBEscString($f_description);
	
	//Выполнение запроса	
	_DBQuery("
		UPDATE Tovary 
		SET	Nazvanie='$f_name',
			Cena='$f_price',
			Kol='$f_quantity',
			God='$f_year',
			Strana='$f_country',
			Opisanie='$f_description'
		WHERE 
			ID=$id
	");

	//Выполнение запроса	
	_DBQuery("
		UPDATE comp 
		SET	Proc='$f_req',
				Pamyat='$f_ram'
			WHERE 
			ID_Tovara=$id
	");
}

//Удаление элемента (Delete)
function DBDeleteUser($id) {
	//Предотвращение SQL-инъекций
	$id=(int)$id;
	
	//Выполнение запроса
	_DBQuery("DELETE FROM Tovary WHERE id=$id");
}
