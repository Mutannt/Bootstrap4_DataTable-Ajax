<?
require_once("db_init.php");
require_once("images.php");

//Получение списка накопителей данных
$Nakopitely=Array();
$res=mysql_query("SELECT * FROM nakopitely ",$db_link);
if($res)
    while($nakopitel=mysql_fetch_array($res,MYSQL_ASSOC))
        $Nalopitely[$nakopitel["ID"]]=$nakopitel["Nazvanie"];

//Получение списка внешних интерфейсов
$Interfeisy=Array();
$res=mysql_query("SELECT * FROM interfeisy ",$db_link);
if($res)
    while($interfeis=mysql_fetch_array($res,MYSQL_ASSOC))
        $Interfeisy[$interfeis["ID"]]=$interfeis["Nazvanie"];
//Если нажата кнопка "Сохранить"
if(isset($_POST[@"Sohranit"])) {
    $soobsh_oshibka="";

  //Получение значений из элементов управления
  $pNazv=$_POST["pNazv"];//Название
  $pCena=$_POST["pCena"];//Цена
  $pKol=$_POST["pKol"];//Количество
  $pGod=$_POST["pGod"];//Год
  $pProc=$_POST["pProc"];//Частота процессора
  //Объём оперативной памяти
  $pPamyat=$_POST["pPamyat"];

  //Регулярные выражения для проверки
  //введенных значений
  $reg_pCena='/^[0-9]+(\.[0-9]{2})?$/';
  $reg_pKol='/^[0-9]+$/';
  $reg_pGod='/^[0-9]+$/';
  $reg_pProc='/^[0-9]+$/';
  $reg_pPamyat='/^[0-9]+$/';

  $polya_s_oshibkoy=Array();

  //Проверка заполненности поля "Название"
  if(trim($pNazv)=="")
   {
       $soobsh_oshibka.="Поле <<Название>>"
       ."не заполнено <br/>";
       $polya_s_oshibkoy[]="#pNazv";
   }

  //Проверка заполненности и правильности
  //Заполнения поля "Цена"
  if(!preg_match($reg_pCena,$pCena))
   {
       $soobsh_oshibka.="Поле <<Цена>>"
       ."не заполнено "
       ."или имеет неверный формат <br/>";
       $polya_s_oshibkoy[]="#pCena";
   }

  //Проверка заполненности и правильности
  //Заполнения поля "Количетсво".
  if(!preg_match($reg_pKol,$pKol))
   {
       $soobsh_oshibka.="Поле <<Количество>>"
       ."не заполнено "
       ."или имеет неверный формат <br/>";
       $polya_s_oshibkoy[]="#pKol";
   }

  //Првоерка заполненности и правильности
  //заполнения поля "год".
  $d=getdate();
  if(
      ! preg_match($reg_pGod,$pGod) ||
      !( (int)$pGod > 1900 &&
         (int)$pGod <=$d["year"]))
   {
       $soobsh_oshibka.="Поле <<Год>> не заполнено ".
       "или имеет неверный формат <br/>";
       $polya_s_oshibkoy[]="#pGod";
   }

  //Проверказаполненности и правильности
  //заполнения поля "Частота процессора".
  if(!preg_match($reg_pProc,$pProc))
   {
       $soobsh_oshibka.="Поле <<Частота процессора>> ".
       "не заполнено или имеет неверный формат<br/>";
       $polya_s_oshibkoy[]="#pProc";
   }

  //Проверка заполненности и правильности заполнения
  //поля "Объём оперативной памяти".
  if(!preg_match($reg_pPamyat,$pPamyat))
   {
       $soobsh_oshibka.="Поле <<Объём оперативной памяти>>"
       ."не заполнено или имеет неверный формат <br/>";
       $polya_s_oshibkoy[]="#pPamyat";
   }
  
  if(trim($soobsh_oshibka)=="")
  {
      //Сохраняем данные формы
      //Подготовка данных к сахранению в БД -
      //профилактика SQL-инъекций.
      //Назавние
      $pNAzv=mysql_real_escape_string($_POST["pNazv"]);
      $pCena=(double)$_POST["pCena"];//Цена
      $pKol=(int)$_POST["pKol"]; //Количество
      $pGod=(int)$_POST["pGod"]; //Год
      $pStrana=(int)$_POST["pStrana"];//Страна
      //Описание
      $pOpisanie=mysql_real_escape_string(
                            $_POST["pOpisanie"]);

      $pProc=(int)$_POST["pProc"]; //Частота процессора
      //Объём оперативной памяти
      $pPamyat=(int)$_POST["pPamyat"];
      //Накопители данных
      $pNakopitely=Array();
      if(isset($_POST["pNakopitely"]))
      foreach($_POST["pNakopitely"] As $k=>$v)
        $pNakopitely[(int)$k]=(int)$k;
      //Интерфейсы расширения
      $pInterfeisy=Array();
      if(isset($_POST["pInterfeisy"]))
      foreach($_POST["pInterfeisy"] As $k=>$v)
        $pInterfeisy[(int)$k]=(int)$k;

      if(isset($_POST["ID"]))
      {
        //---Обновление существующей записи---------------------------------
        $ID_Tovara=(int)$_POST["ID"];
        mysql_query(
        "UPDATE Tovary SET Nazvanie='$pNazv',".
        "Cena='$pCena', Kol='$pKol',".
        "God='$pGod', Strana='$pStrana',".
        "Opisanie='$pOpisanie' ".
        "WHERE ID='$ID_Tovara'"
        ,$db_link);

        //Обновление информации о компьютере
        mysql_query(
            "UPDATE Comp SET Proc='$pProc', ".
            "Pamyat='$pPamyat' ".
            "WHERE ID_Tovara='$ID_Tovara'"
            ,$db_link);

        //Обновление информации о накопителях данных,
        //прикреплённых к данному компьютеру
        $res=mysql_query(
            "SELECT * FROM CompNakopitely ".
            "WHERE ID_Tovara='$ID_Tovara'"
            ,$db_link);

        while($tmp=mysql_fetch_array($res,MYSQL_BOTH))
            $Nakopitely_bd[
                (int)$tmp["ID_Nakopitelya"]
            ]=$tmp["ID_Tovara"];
            
        $sql_insert_values="";
        $sql_delete_in="";
        $zap_insert_values="";
        $zap_delete_in="";
        $flag_insert=false;
        $flag_delete=false;
        foreach($Nakopitely As $k=>$v) {
            if(isset($_POST["pNakopitely"][(int)$k])) {
                if(!isset($Nakopitely_bd[(int)$k]))
                {
                    $sql_insert_values.=
                        "$zap_insert_values('$ID_Tovara','$k')";
                    $zap_insert_values=",";
                    $flag_insert=true;
                }
            } else {
                if(isset($Nakopitely_bd[(int)$k]))
                {
                    $sql_delete_in.="$zap_delete_in'$k'";
                    $zap_delete_in=",";
                    $flag_delete=true;
                }
            }
        }

        if($flag_insert)
        {
            mysql_query(
                "INSERT INTO ".
                "CompNakopitely(ID_Tovara,ID_Nakopitelya) ".
                "VALUES $sql_insert_values"
                ,$db_link);
        }
        if($flag_delete)
        {
            mysql_query(
                "DELETE FROM CompNakopitely ".
                "WHERE ID_Tovara='$ID_Tovara' AND ".
                "ID_Nakopitelya IN($sql_delete_in)"
                ,$db_link);
        }
        //Обновление информации о внешних интерфейсах,
        //прикрепленных к данному компьютеру
        $res=mysql_query(
            "SELECT * FROM CompInterfeisy ".
            "WHERE ID_Tovara='$ID_Tovara'"
            ,$db_link);
            while($tmp=mysql_fetch_array($res,MYSQL_BOTH))
                $Interfeisy_bd[
                    (int)$tmp["ID_Interfeisa"]
                ]=$tmp["ID_Tovara"];
            $sql_insert_values="";
            $sql_delete_in="";
            $zap_insert_values="";
            $zap_delete_in="";
            $flag_insert=false;
            $flag_delete=false;
            foreach($Interfeisy As $k=>$v) {
                if(isset($_POST["pInterfeisy"][(int)$k])){
                    if(!isset($Interfeisy_bd[(int)$k]))
                    {
                        $sql_insert_values.=
                            "$zap_insert_values('ID_Tovara','$k')";
                        $zap_insert_values=",";
                        $flag_insert=true;
                    }
                }
                else {
                    if(isset($Interfeisy_bd[(int)$k]))
                    {
                        $sql_delete_in.="$zap_delete_in'$k'";
                        $zap_delete_in=",";
                        $flag_delete=true;
                    }
                }
            }
            if($flag_insert)
            {
                mysql_query(
                    "INSERT INTO ".
                    "CompInterfeisy(ID_Tovara,ID_Interfeisa) ".
                    "VALUES $sql_insert_values"
                    ,$db_link);
            }

            if($flag_delete)
            {
                mysql_query(
                    "DELETE FROM CompInterfeisy ".
                    "WHERE ID_Tovara='$ID_Tovara' AND ".
                    "ID_Interfeisa IN($sql_delete_in)"
                    ,$db_link);
            }
      }
      else
      {//die("!!");
        //---Создание новой записи------------------------------------------
       mysql_query(
        "INSERT INTO ".
        "Tovary(Nazvanie,Cena,Kol,God,".
        "Strana,Opisanie) ".
        "VALUES ('$pNazv','$pCena','$pKol','$pGod',".
        "'$pStrana','$pOpisanie')"
        ,$db_link);

        $ID_Tovara=mysql_insert_id();

        mysql_query(
            "INSERT INTO ".
            "Comp(ID_Tovara,Proc,Pamyat) ".
            "VALUES('$ID_Tovara','$pProc','$pPamyat')"
            ,$db_link);
        foreach($pNakopitely As $k=>$Nakopitel)
            mysql_query(
                "INSERT INTO ".
                "CompNakopitely(ID_Tovara,Id_Nakopitelya) ".
                "VALUES('$ID_Tovara','$Nakopitel')"
                ,$db_link);
        foreach($pInterfeisy As $k=>$pInterfeis)
            mysql_query(
                "INSERT INTO ".
                "CompInterfeisy(ID_Tovara,ID_Interfeisa) ".
                "VALUES('$ID_Tovara','$Interfeis')"
                ,$db_link);

        
        //Загрузка изображения и сохранение его расширения в базу данных
        $ext=upload_image("pIzobraj",$ID_Tovara);
        if(trim($ext)!="")
            mysql_query(
            "UPDATE Tovary ".
            "SET RashIzobraj='$ext' ".
            "WHERE id='$ID_Tovara'"
            ,$db_link);
      }
      //Выполним перенаправление на эту же страницу
      //для сброса POST - параметров и рпедотвращения
      //дублирования информации в БД
      //при обновлениии страницы. 
      header("Location: $_SERVER[PHP_SELF]");
    }
}
//Если нажата ссылка "Удалить"
if(isset($_GET["delete_id"]))
{
    $id=(int)$_GET["delete_id"];
    mysql_query(
        "DELETE FROM Tovary ".
        "WHERE ID=$id"
        ,$db_link);

    //Удаляем файл изображения
    array_map(
    "unlink",
    glob("$_SERVER[DOCUMENT_ROOT]/mysite/images/$id.*"));

    mysql_query(
        "DELETE FROM ".
        "Comp,Compnakopitely,CompInterfeisy ".
        "WHERE ID_Tovara=$id",$db_link);
    

    header("Location: $_SERVER[PHP_SELF]");
}
//Значения, вводимые в форме про умолчанию
$fields_default=$_POST;
//Если нажата ссылка "Редактировать"
if(isset($_GET["edit_id"]))
{
    $id=(int)$_GET["edit_id"];

    $res=mysql_query(
    "SELECT Tovary.ID As ID,". 
    "Nazvanie, ".
    "Cena, ".
    "Kol, ".
    "God, ".
    "Strana, ".
    "Opisanie, ".
    "Proc, ".
    "Pamyat ".
    "FROM Tovary,Comp ".
    "WHERE Tovary.ID=$id AND ".
		  "Tovary.ID=Comp.ID_Tovara"
	,$db_link);

    if($res)
    {
	$db_rec=mysql_fetch_array($res,MYSQL_ASSOC);
		//Если массив $_POST (так бывает сразу после перехода
		//по ссылке "Редактировать") - значениям по умолчанию 
		//присваиваем характеристики товара с ID=edit_id
		if(count($_POST)==0) {
		$fields_default["ID"]=$db_rec["ID"];
		$fields_default["pNazv"]=$db_rec["Nazvanie"];
		$fields_default["pCena"]=$db_rec["Cena"];
		$fields_default["pKol"]=$db_rec["Kol"];
		$fields_default["pGod"]=$db_rec["God"];
		$fields_default["pStrana"]=$db_rec["Strana"];
        $fields_default["pOpisanie"]=$db_rec["Opisanie"];
        $fields_default["pProc"]=$db_rec["Proc"];
        $fields_default["pPamyat"]=$db_rec["Pamyat"];

        $id=$db_rec["ID"];
        $res=mysql_query(
            "SELECT * ".
            "FROM compnakopitely ".
            "WHERE ID_Tovara='$id'"
            ,$db_link);

        if($res)
        {
            $fields_default["pNakopitely"]=Array();
            while($db_rec=mysql_fetch_array($res,MYSQL_ASSOC)) {
                $fields_default["pNakopitely"][
                    (int)$db_rec["ID_Nakopitelya"]]="on";
            }
        }

        $res=mysql_query(
            "SELECT * FROM CompInterfeisy".
            " WHERE ID_Tovara='$id'",$db_link);
        
        if($res)
        {
            $fields_default["pInterfeisy"]=Array();
            while($db_rec=mysql_fetch_array($res,MYSQL_ASSOC)) {
                $fields_default["pInterfeisy"][
                    (int)$db_rec["ID_Interfeisa"]]="on";
            }
        }
    }

        if(trim($db_rec["RashIzobraj"])!="")
            $fields_default["pIzobraj"]=
                "/mysite/images/".
                $db_rec["ID"].".".$db_rec["RashIzobraj"];
    }
}
?>

<head>
<!--Область заголовка-->
    <meta charset="UTF-8"/>
        <!--Предотвращение кэширования-->
        <meta thhp-equiv="Cache-Control"
            content="no-store, no-cache">
        <!--Выделение красным цветом неверно заполненных полей-->
        <?if(count($polya_s_oshibkoy)>0):?>
        <style>
            <?=implode(",",$polya_s_oshibkoy)?>
            {
                border-color:red;
            }
        </style>
        <?endif;?>
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
    $(document) .ready(function(){
        $("#sohraint") .click(function() {
        var soobsh_oshibka="";

        //Получение значений из элкментов управления
        var pNazv = $("#pNazv") .val(); //Название
        var pCena = $("#pCena") .val(); //Цена
        var pKol = $("#pKol") .val(); //Количество
        var pGod = $("#pGod") .val(); //Год
        var pProc = $("#pProc") .val(); //Частота цроцесса
        //Объём оперативной памяти
        var pPamyat = $("#pPamyat") .val();

        //Регулярные выражения для проверки
        //введённых значений
        var reg_pCena=/^[0-9]+(\.[0-9]{2})?$/;
        var reg_pKol=/^[0-9]+$/;
        var reg_pGod=/^[0-9]+$/;
        var reg_pPamyat=/^[0-9]+$/;

        //Сброс форматов для всех элентов управления.
        //чтобы исправленные поля не были красными.
        $("input") .css("border-color","");

        //Проверка заполненности поля "Название"
        if($.trim(pNazv)=="")
            {
                soobsh_oshibka+="Поле <<Название>>" +"Не заполнено <br/>";
                //Выделение красным соответсвующего поля ввода
                $("#pNazv") .css("border-color","red");
            }
        //Проверка заполненности и правильности
        //заполнения поля "Цена"
        if(!reg_pCena.test(pCena))
            {
                soobsh_oshibka+="Поле <<Цена>> не заполнено"+"или имеет неверный формат <br/>";
                $("#pKol").css("border-color","red");
            }
        //Проверка заполненности и правильности
        //Заполнения поля "Год"
        if(
            !reg_pGod.test(pGod) ||
            !(parseInt(pGod) > 1900 && 
              parseInt(pGod) <= (new Date()) .getFullYear()))
            {
              soobsh_oshibka+="Поле <<Год>> не заполнено"+"или имеет неверный формат <br/>";
              $("#pGod").css("border-color","red");
            }
        //Проверка заполненности и правильности заполнения
        //поля "Частота процессора".
        if(!reg_pProc.test(pProc))
            {
              soobsh_oshibka+="Поле <<Частота процессора>>"+"не заполнено или имеет неверный формат<br/>";
              $("#pProc").css("border-color","red");  
            }
        //Проверка заполненности и правильности заполнения
        //поля "объем оперативности памяти".
        if(!reg_pPamyat.test(pPamyat))
            {
              soobsh_oshibka+="Поле"
              +"<<Объём оперативной памяти"
              +"не заполнено или имеет неверный формат <br/>";
              $("#pPamyat").css("border-color","red");
            }


        if($.trim(soobsh_oshibka)!="")
                $("front").html(soobsh_oshibka);
                return false;
        });
    });
    </script>
</head>
<body>
<!--Область тела документа-->
    <h1>Добавление товара в группу
        "Вычислительная техника" </h1>
        <font color=red><?=$soobsh_oshibka?></font>
        <form action="" method="POST"
              enctype="multipart/form-data">
    <table>
        <tr>
            <td><b>Название:</b></td>
            <td><input name="pNazv" id="pNazv"
            type="text" size="50"
            value="<?=$fields_default["pNazv"]?>"/></td>
        </tr>
        <tr>
            <td><b>Изображение:</b></td>
            <td>
                <?if(isset($fields_default["pIzobraj"])):?>
                    <img src="<?=$fields_default["pIzobraj"]?>"
                        height="200px" alt="Изображение"/><br/>
                <?endif;?>
                <input name="pIzobraj" type="file"/>
            </td>
        </tr>
        <tr>
            <td><b>Цена:</b></td>
            <td><input name="pCena" id="pCena" type="text" size="5"
            value="<?=$fields_default["pCena"]?>"/></td>
        </tr>
        <tr>
            <td><b>Количество:</b></td>
            <td><input name="pKol" id="pKol" type="text" size="2"
            value="<?=$fields_default["pKol"]?>"/></td>
        </tr>
        <tr>
            <td><b>Год выпуска:</b></td>
            <td><input name="pGod" id="pGod" type="text" size="4"
            value="<?=$fields_default["pGod"]?>"/></td>
        </tr>
        <tr>
            <td><b>Стана выпуска:</b></td>
            <td>
                <select>
                    <option value="1">Россия</option>
                    <option value="1">США</option>
                    <option value="1">Германия</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>Описание:</b></td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea name="pOpisanie" rows="10" cols="70">
                <?=$fields_default["pOpisanie"]?></textarea>
            </td>
        </tr>
    </table>
    <hr>
    <h2>Характеристики</h2>
    <table>
        <tr>
            <td><b>Частота процессора (МГц):</b></td>
            <td><input name="pProc" id="pProc" type="text" size="5"
            value="<?=$fields_default["pProc"]?>"/></td>
        </tr>
        <tr>
            <td><b>Объём оперативной памяти (Кб):</td>
            <td><input name="pPamyat" id="pPamyat" type="text" size="5"
            value="<?=$fields_default["pPamyat"]?>"/></td>
        </tr>
        <tr>
            <td colspan="2"><b>Накопители данных:</b></td>
        </tr>
        <tr>
            <td colspan="2">
                <ul>
                    <?foreach($Nakopitely As $t_ID=>$t_Nazvanie):?>
                    <input name="pNakopitely[<?=$t_ID?>]" type="checkbox"
                    <?=(isset($fields_default["pNalopitely"][(int)$t_ID])?
                        "checked=\"checked\"":
                        "")?>/><?=$t_Nazvanie?><br/>
                    <?endforeach;?>
                </ul>
            </td>
        </tr>
        <tr>
            <td><b>Интерфейсы расширения:</b></td>
        </tr>
        <tr>
            <td colspan="2">
                <ul>
                    <?foreach($Interfeisy As $t_ID=>$t_Nazvanie):?>
                    <input name="pInterfeisy[<?=$t_ID?>]" type="checkbox"
                    <?=(isset($fields_default["pInterfeisy"][(int)$t_ID])?
                    "checked=\"checked\"":
                    "")?>/><?=$t_Nazvanie?><br/>
                    <?endforeach;?>
                </ul>
            </td>
        </tr>
    </table>
    <?if(isset($fields_default["ID"])):?>
        <?//В режиме редактирования
        //в скрытом поле вывож идентификатора ?>
        <input name="ID" type="hidden"
            value="<?=$fields_default["ID"]?>"/>
    <?endif;?>
    <input type="submit" name="Sohranit" id="Sohranit" value="Сохранить"/>
    </form>
    <table border="1" width="100%">
        <col width="10%"/>
        <col/>
        <col width="2%"/>
        <col width="2%"/>
        <tr>
            <th>ID</th>
            <th>Назавние</th>
            <th>Цена</th>
            <th></th>
        </tr>
        <?$res=mysql_query(
                "SELECT * FROM tovary"
                ,$db_link);
        if($res)
          while(
            $db_rec=mysql_fetch_array(
                          $res,MYSQL_ASSOC)
            ):?>
        <tr>
            <td><?=$db_rec["ID"]?></td>
            <td><?=$db_rec["Nazvanie"]?></td>
            <td><?=$db_rec["Cena"]?></td>
            <td>
                <a href="?edit_id=<?=$db_rec["ID"]?>">
                Редактировать
                </a></br>
                <a href="?delete_id=<?=$db_rec["ID"]?>"
                onclick="
                return confirm('Действительно удалить?')">
                Удалить
                </a><br/>
            </td>
        </tr>
        <?endwhile;?>
    </table>
</body>

