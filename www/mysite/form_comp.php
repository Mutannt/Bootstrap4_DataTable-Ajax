<?php
require_once("db_init.php");
require_once("images.php");

//��������� ������ ����������� ������
$Nakopitely=Array();
$res=mysql_query("SELECT * FROM nakopitely ",$db_link);
if($res)
    while($nakopitel=mysql_fetch_array($res, MYSQL_ASSOC))
    $Nakopitely[$nakopitel["ID"]]=$nakopitel["Nazvanie"];

//��������� ������ ������� �����������
$Interfeisy=Array();
$res=mysql_query("SELECT * FROM interfeisy ",$db_link);
if($res)
    while($interfeis=mysql_fetch_array($res, MYSQL_ASSOC))
    $Interfeisy[$interfeis["ID"]]=$interfeis["Nazvanie"];

// ���� ������ ������ ���������
if (isset($_POST["Sohranit"]))
{
    $soobsh_oshibka = "";
    // ��������� �������� �� ��������� ����������
    $pNazv = $_POST["pNazv"]; // ��������
    $pCena = $_POST["pCena"]; // ����
    $pKol = $_POST["pKol"]; // ����������
    $pGod = $_POST["pGod"]; // ���
    $pProc = $_POST["pProc"]; // ������� ����������
    $pPamyat = $_POST["pPamyat"]; // ������ ����������� ������

    // ���������� ��������� ��� �������� �������� ��������
    $reg_pCena = '/^[0-9]+(\.[0-9]{2})?$/';
    $reg_pKol = '/^[0-9]+$/';
    $reg_pGod = '/^[0-9]+$/';
    $reg_pProc = '/^[0-9]+$/';
    $reg_pPamyat = '/^[0-9]+$/';

    $polya_s_oshibkoy = Array();

    // �������� ���������� ���� "��������"
    if (trim($pNazv)=="")
    {
        $soobsh_oshibka.="php ���� <<��������>>" ."�� ���������</br>";
        // ��������� ������� ���������������� ���� �����
        $polya_s_oshibkoy[]="#pNazv";
    }

    // �������� ������������� � ������������ ���������� ���� "����"
    if (!preg_match($reg_pCena, $pCena))
    {
        $soobsh_oshibka .="php ���� <<����>> �� ���������"
        ."��� ����� �������� ������</br>";
        $polya_s_oshibkoy[]="#pCena";
    }
    // �������� ������������� � ������������ ���������� ���� "����������"
    if (!preg_match($reg_pKol, $pKol))
    {
        $soobsh_oshibka .="php ���� <<����������>> �� ���������"
        ."��� ����� �������� ������</br>";
        $polya_s_oshibkoy[]="#pKol";
    }
    // �������� ������������� � ������������ ���������� ���� "���"
    $d = getdate();
    if (!preg_match($reg_pGod, $pGod) || 
    !( (int)$pGod > 1900 && 
    (int)$pGod <= $d["year"]))
    {
        $soobsh_oshibka .="php ���� <<���>> �� ���������"
        ."��� ����� �������� ������</br>";
        $polya_s_oshibkoy[]="#pGod";
    }
    // �������� ������������� � ������������ ���������� ���� "������� ����������"
    if (!preg_match($reg_pProc, $pProc))
    {
        $soobsh_oshibka .="php ���� <<������� ����������>> �� ���������"
        ."��� ����� �������� ������</br>";
        $polya_s_oshibkoy[]="#pProc";
    }
    // �������� ������������� � ������������ ���������� ���� "����� ����������� ������"
    if (!preg_match($reg_pPamyat, $pPamyat))
    {
        $soobsh_oshibka .="php ���� <<����� ����������� ������>> �� ���������"
        ."��� ����� �������� ������</br>";
        $polya_s_oshibkoy[]="#pPamyat";
    }
    if (trim($soobsh_oshibka) == "")
    {
        //��������� ������ �����

        //���������� ������ � ���������� � �� - ������������ SQL-�������
        $pNazv = mysql_real_escape_string($_POST["pNazv"]);//��������
        $pCena = (double)$_POST["pCena"];//����
        $pKol= (int)$_POST["pKol"];//����������
        $pGod = (int)$_POST["pGod"];//���
        $pStrana = (int)$_POST["pStrana"];//������
        $pOpisanie = mysql_real_escape_string($_POST["pOpisanie"]);// ��������

        $pProc=(int)$_POST["pProc"]; // ������� ����������
        $pPamyat=(int)$_POST["pPamyat"]; // ����� ����������� ������
        $pNakopitely=Array(); // ���������� ������
        if(isset($_POST["pNakopitely"]))
        foreach($_POST["pNakopitely"] As $k=>$v)
            $pNakopitely[(int)$k]=(int)$k;
        $pInterfeisy=Array(); // ���������� ����������
        if(isset($_POST["pInterfeisy"]))
        foreach($_POST["pInterfeisy"] As $k=>$v)
            $pInterfeisy[(int)$k]=(int)$k;

        if(isset($_POST["ID"]))// ����������, ��������� �� ���������� ��� ������� �������.
        {
            //----���������� ������������ ������------------------------------------------
            $ID_Tovara = (int)$_POST["ID"];
            mysql_query(
                "UPDATE Tovary SET Nazvanie = '$pNazv',".
                "Cena = '$pCena', Kol = '$pKol',".
                "God = '$pGod', Strana = '$pStrana',".
                "Opisanie = '$pOpisanie' ".
                "WHERE ID = '$ID_Tovara'"
                , $db_link);

                // ���������� ���������� � ����������
                mysql_query(
                "UPDATE Comp SET Proc='$pProc', ".
                "Pamyat='$pPamyat' ".
                "WHERE ID='$ID_Tovara'"
                , $db_link);

                // ���������� ���������� � ����������� ������,
                // ������������ � ������� ����������
                $res=mysql_query(
                "SELECT * FROM CompNakopitely ".
                "WHERE ID_Tovara='$ID_Tovara'"
                , $db_link);

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
                
                foreach($Nakopitely As $k=>$v){
                    if(isset($_POST["pNakopitely"][(int)$k])){
                        if(!isset($Nakopitely_bd[(int)$k]))
                        {
                            $sql_insert_values.=
                                "$zap_insert_values('$ID_Tovara', '$k')";
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
                        "CompNakopitely(ID_Tovara, ID_Nakopitelya) ".
                        "VALUES $sql_insert_values"
                        , $db_link);
                }
                
                if($flag_delete)
                {
                    mysql_query(
                        "DELETE FROM CompNakopitely ".
                        "WHERE ID_Tovara='$ID_Tovara' AND ".
                        "ID_Nakopitelya IN($sql_delete_in)"
                        , $db_link);
                }

                // ���������� ���������� � ������� �����������,
                // ������������ � ������� ����������
                $res=mysql_query(
                "SELECT * FROM CompInterfeisy ".
                "WHERE ID_Tovara='$ID_Tovara'"
                , $db_link);

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
                    foreach($Interfeisy As $k=>$v){
                        if(isset($_POST["pInterfeisy"][(int)$k])){
                            if(!isset($Interfeisy_bd[(int)$k]))
                            {
                                $sql_insert_values.=
                                    "$zap_insert_values('$ID_Tovara', '$k')";
                                $zap_insert_values=",";
                                $flag_insert=true;
                            }
                        } else {
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
                        "CompInterfeisy(ID_Tovara, ID_Interfeisa) ".
                        "VALUES $sql_insert_values"
                        , $db_link);
                    }
                    
                    if($flag_delete)
                    {
                        mysql_query(
                        "DELETE FROM CompInterfeisy ".
                        "WHERE ID_Tovara='$ID_Tovara' AND ".
                        "ID_Interfeisa IN($sql_delete_in)"
                        , $db_link);
                    }
        }
        else
        {
            //-----�������� ����� ������-----------------------
            mysql_query(
                "INSERT INTO ".
                "Tovary (Nazvanie, Cena, Kol, God, ".
                "Strana, Opisanie) ".
                "VALUES ('$pNazv', '$pCena', '$pKol', '$pGod', ".
                "'$pStrana', '$pOpisanie')"
                , $db_link);
                //��������� �������������� ���������� ������������ ������
                $ID_Tovara=mysql_insert_id();

                mysql_query(
                    "INSERT INTO ".
                    "Comp(ID_Tovara, Proc, Pamyat) ".
                    "VALUES('$ID_Tovara', '$pProc', '$pPamyat')"
                    ,$db_link);
                
                foreach($pNakopitely As $k=>$Nakopitel)
                    mysql_query(
                    "INSERT INTO ".
                    "CompNakopitely(ID_Tovara, ID_Nakopitelya) ".
                    "VALUES('$ID_Tovara', '$Nakopitel')"
                    , $db_link);
                
                foreach($pInterfeisy As $k=>$Interfeis)
                    mysql_query(
                    "INSERT INTO ".
                    "CompInterfeisy(ID_Tovara, ID_Interfeisa) ".
                    "VALUES('$ID_Tovara', '$Interfeis')"
                    , $db_link);

        }

        //�������� ����������� � ���������� ��� ���������� � ��
        $ext=upload_image("pIzobraj", $ID_Tovara);
        if(trim($ext)!="")
            mysql_query(
            "UPDATE Tovary ".
            "SET RashIzobraj='$ext' ".
            "WHERE id='$ID_Tovara'"
            , $db_link);

        // �������� ��������������� �� ��� �� �������� ��� ������� POST-����������
        // � �������������� ����������� ����������� � �� ��� ���������� ��������
        header("Location: $_SERVER[PHP_SELF]");
    }
}

// �������� ��������� � ����� �� ���������
$fields_default = $_POST;
// ���� ������ ������ "�������������"
// ����� � ����������� ������� �� ��������� ������. ��������������
// ������ � ������� �� ������ �����������
if (isset($_GET["edit_id"]))
{
    $id = (int)$_GET["edit_id"];

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
        , $db_link);

    if($res)
    {
        $db_rec = mysql_fetch_array($res,MYSQL_ASSOC);
        // ���� ������ $_POST ������ (��� ������ ����� ����� �������� �� ������ "�������������") - 
        // - ��������� �� ��������� ����������� �������������� ������ � ID = edit_id
        if(count($_POST) == 0)
        {
            $fields_default["ID"] = $db_rec["ID"];
            $fields_default["pNazv"] = $db_rec["Nazvanie"];
            $fields_default["pCena"] = $db_rec["Cena"];
            $fields_default["pKol"] = $db_rec["Kol"];
            $fields_default["pGod"] = $db_rec["God"];
            $fields_default["pStrana"] = $db_rec["Strana"];
            $fields_default["pOpisanie"] = $db_rec["Opisanie"];
            $fields_default["pProc"]=$db_rec["Proc"];
            $fields_default["pPamyat"]=$db_rec["Pamyat"];

            $id=$db_rec["ID"];
            $res=mysql_query(
                "SELECT * ".
                "FROM compnakopitely ".
                "WHERE ID_Tovara='$id'"
                , $db_link);

            if($res)
            {
                $fields_default["pNakopitely"]=Array();
                while($db_rec=mysql_fetch_array($res, MYSQL_ASSOC)) {
                    $fields_default["pNakopitely"][
                        (int)$db_rec["ID_Nakopitelya"]]="on";
                }
            }

            $res=mysql_query(
                "SELECT * FROM CompInterfeisy".
                " WHERE ID_Tovara='$id'", $db_link);

            if($res)
            {
                $fields_default["pInterfeisy"]=Array();
                while($db_rec=mysql_fetch_array($res, MYSQL_ASSOC)) {
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


// ���� ������ ������ "�������"
if (isset($_GET["delete_id"]))
{
    $id = (int)$_GET["delete_id"];
    mysql_query(
        "DELETE FROM Tovary ".
        "WHERE ID=$id"
        , $db_link);

    //������� ���� �����������
    array_map(
    "unlink",
    glob("$_SERVER[DOCUMENT_ROOT]/mysite/images/$id.*"));

    // ������� ������� ����� � ������������ � ������������
    //mysql_query(
    //   "DELETE FROM ".
    //    "Comp, Compnakopitely, Compinterfeisy ".
    //    "WHERE ID_Tovara=$id", $db_link);

    mysql_query(
        "DELETE FROM Comp ".
        "WHERE ID_Tovara=$id"
        , $db_link);
    mysql_query(
        "DELETE FROM Compnakopitely ".
        "WHERE ID_Tovara=$id"
        , $db_link);
    mysql_query(
        "DELETE FROM Compinterfeisy ".
        "WHERE ID_Tovara=$id"
        , $db_link);


    header("Location: $_SERVER[PHP_SELF]");
}
?>


<!DOCTYPE html>
<html>
    <head>
        <title>�������������� ������� �����</title>
        <meta charset="Windows-1251"/>
        <!--<meta charset="UTF-8"/>-->
         <!--�������������� �����������-->
         <meta http-equiv="Cache-Control" content="no-store, no-cache">
         <!--��������� ������� ������ ������� ����������� �����-->
         <? if(count($polya_s_oshibkoy)>0):?>
         <style>
            <?=implode(",", $polya_s_oshibkoy)?>
            {
                border-color:red;
            }
         </style>
         <?endif;?>

	<script type="text/javascript" src="jquery-3.4.1.min.js"></script>
        <script type="text/javascript">
            $(document).ready (function(){
                $("#Sohranit").click(function (){ //��� ������� ������ "���������"
                    
                    var soobsh_oshibka = "";

                    // ��������� �������� �� ��������� ����������
                    var pNazv = $("#pNazv").val(); // ��������
                    var pCena = $("#pCena").val(); // ����
                    var pKol = $("#pKol").val(); // ����������
                    var pGod = $("#pGod").val(); // ���
                    var pProc = $("#pProc").val(); // ������� ����������
                    var pPamyat = $("#pPamyat").val(); // ������ ����������� ������
                    
                    // ���������� ��������� ��� �������� �������� ��������

                    var reg_pCena = /^[0-9]+(\.[0-9]{2})?$/;
                    var reg_pKol = /^[0-9]+$/;
                    var reg_pGod = /^[0-9]+$/;
                    var reg_pProc = /^[0-9]+$/;
                    var reg_pPamyat = /^[0-9]+$/;

                    // ����� �������� ��� ���� ��������� ����������.
                    // ����� ������������ ���� �� ���� ��������.
                    $("input").css("border-color", "color"); 
                    $("input").css("border-color", "default"); 

                    // �������� ���������� ���� "��������"
                    if ($.trim(pNazv) == "")
                    {
                        soobsh_oshibka +="js ���� <<��������>>" + "�� ���������</br>";
                        // ��������� ������� ���������������� ���� �����
                        $("#pNazv").css("border-color", "red");
                    }

                    // �������� ������������� � ������������ ���������� ���� "����"
                    if (!reg_pCena.test(pCena))
                    {
                        soobsh_oshibka +="js ���� <<����>> �� ���������" + 
                        "��� ����� �������� ������</br>";
                        $("#pCena").css("border-color", "red");
                    }

                    // �������� ������������� � ������������ ���������� ���� "����������"
                    if (!reg_pKol.test(pKol))
                    {
                        soobsh_oshibka +="js ���� <<����������>> �� ���������" + 
                        "��� ����� �������� ������</br>";
                        $("#pKol").css("border-color", "red");
                    }

                    // �������� ������������� � ������������ ���������� ���� "���"
                    if (!reg_pGod.test(pGod) || 
                    !(parseInt(pGod) > 1900 && 
                    parseInt(pGod) <= (new Date()).getFullYear()))
                    {
                        soobsh_oshibka +="js ���� <<���>> �� ���������" + 
                        "��� ����� �������� ������</br>";
                        $("#pGod").css("border-color", "red");
                    }


                    // �������� ������������� � ������������ ���������� ���� "������� ����������"
                     if (!reg_pProc.test(pProc))
                    {
                        soobsh_oshibka +="js ���� <<������� ����������>> �� ���������" + 
                        "��� ����� �������� ������</br>";
                        $("#pProc").css("border-color", "red");
                    }

                    // �������� ������������� � ������������ ���������� ���� "����� ����������� ������"
                     if (!reg_pPamyat.test(pPamyat))
                    {
                        soobsh_oshibka +="js ���� <<����� ����������� ������>> �� ���������" + 
                        "��� ����� �������� ������</br>";
                        $("#pPamyat").css("border-color", "red");
                    }

                    if ($.trim(soobsh_oshibka) != "")
                    {
                        $("font").html(soobsh_oshibka);
                        return false;
                    }

                });
            });
        </script>
    </head>
    <body>
        <h1>���������� ������ � ������ "�������������� �������"</h1>
        <font color="red"><?=$soobsh_oshibka?></font>
        <form method="POST" action="" enctype="multipart/form-data">
        <table>
            <tr>
                <td><b>��������:</b></td>
                <td><input id="pNazv" name="pNazv" type="text" size="50" value="<?=$fields_default["pNazv"]?>"/></td>
            </tr>
            <tr>
                <td><b>�����������:</b></td>
                <td>
                    <?if(isset($fields_default["pIzobraj"])):?>
                        <img src="<?=$fields_default["pIzobraj"]?>"
                            height="200px" alt="�����������"/><br/>
                    <?endif;?>
                    <input name="pIzobraj" type="file"/>
                </td>
            </tr>
            <tr>
                <td><b>����:</b></td>
                <td><input id="pCena" name="pCena" type="text" size="5" value="<?=$fields_default["pCena"]?>"/></td>
            </tr>
            <tr>
                <td><b>����������:</b></td>
                <td><input id="pKol" name ="pKol" type="text" size="2" value="<?=$fields_default["pKol"]?>"/></td>
            </tr>
            <tr>
                <td><b>��� �������:</b></td>
                <td><input id="pGod" name="pGod" type="text" size="4" value="<?=$fields_default["pGod"]?>"/></td>
            </tr>
            <tr>
                <td><b>������ �������:</b></td>
                <td>
                    <select id="pStrana" name="pStrana">
                        <option value="1">������</option>
                        <option value="2">���</option>
                        <option value="3">��������</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>��������:</b></td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="pOpisanie" name="pOpisanie" rows="10" cols="70" > <?=$fields_default["pOpisanie"]?> </textarea>
                </td>
            </tr>
        </table>
        <hr>
        <h2>��������������</h2>
        <table>
            <tr>
                <td><b>������� ���������� � (���):</b></td>
                <td><input id="pProc" name="pProc" type="text" size="5" value="<?=$fields_default["pProc"]?>"/></td>
            </tr>
            <tr>
                <td><b>����� ����������� ������ (��):</b></td>
                <td><input id="pPamyat" name="pPamyat" type="text" size="5" value="<?=$fields_default["pPamyat"]?>"/></td>
            </tr>
            <tr>
                <td colspan="2"><b>���������� ������:</b></td>
            </tr>
            <tr>
                <td colspan="2">
                    <ul>
                        <?foreach($Nakopitely As $t_ID=>$t_Nazvanie):?>
                        <input name="pNakopitely[<?=$t_ID?>]" type="checkbox"
                        <?=(isset($fields_default["pNakopitely"][(int)$t_ID])?
                            "checked=\"checked\"":
                            "")?>/><?=$t_Nazvanie?><br/>
                        <?endforeach;?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td><b>���������� ����������:</b></td>
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
            <?// � ������ ��������������
            // � ������� ���� ����� ��������������?>
            <input name="ID" type="hidden" value="<?=$fields_default["ID"]?>"/>
        <?endif;?>
        <input id="Sohranit" name="Sohranit" type="submit" value="���������"/>
        </form>
        <br/>
        <table border="1" width = "100%">
            <col widtch = "10%"/>
            <col/>
            <col widtch = "2%"/>
            <col widtch = "2%"/>
            <tr>
                <th>ID</th>
                <th>��������</th>
                <th>����</th>
                <th></th>
            </tr>
            <?$res=mysql_query(
                "SELECT * FROM Tovary"
                , $db_link);
            if ($res)
            while (
                $db_rec=mysql_fetch_array(
                                $res,MYSQL_ASSOC)
                ):?>
            <tr>
                <td><?=$db_rec["ID"]?></td>
                <td><?=$db_rec["Nazvanie"]?></td>
                <td><?=$db_rec["Cena"]?></td>
                <td>
                    <a href = "?edit_id=<?=$db_rec["ID"]?>">
                    �������������
                    </a><br/>
                    <a href = "?delete_id=<?=$db_rec["ID"]?>"
                    onclick = "return confirm('������������� �������?')">
                    �������
                    </a><br/>
                </td>
            </tr>
            <?endwhile;?>
        </table>
    </body>
</html>