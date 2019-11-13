<?php
//read Greek
ini_set('mssql.charset', 'UTF-8');
//erp db on sql server
$link = mssql_connect('xyz', 'user', 'password');
 
if (!$link)
    die('Unable to connect!');
 
if (!mssql_select_db('db', $link))
	 die('Unable to select database!');
 ini_set('mssql.charset', 'UTF-8');
//pda db on my sql
$mysql_db_hostname = "zyx";
$mysql_db_user = "user";
$mysql_db_password = "password";
$mysql_db_database = "db";

$con = mysql_connect($mysql_db_hostname, $mysql_db_user, $mysql_db_password) or die("Could not connect database");
mysql_select_db($mysql_db_database, $con) or die("Could not select database");

mysql_query("SET NAMES 'utf8';"); 

//get receipts not bridged to the erp
$receipts = mysql_query("SELECT Receipts_ID FROM orders where is_sent = 0 group by Receipts_ID LIMIT 20")or die (mysql_error($con));
$receipt_rows=mysql_num_rows($receipts);
if ($receipt_rows>0){
	while ($receipt = mysql_fetch_array($receipts)) { //for each Receipts_ID
			$mtrlines=array(); //this is the rows of items in the receipt			
			//get receipt items
			$receipt_items = mysql_query("SELECT * from orders where Receipts_ID = '".$receipt['Receipts_ID']."'")or die (mysql_error($con));
			$num_items = mysql_num_rows($receipt_items);
			$ct=0;
			while ($item = mysql_fetch_array($receipt_items)) {
			$receipt_year=substr($item["ReceiptDate"],0,4);
			echo $receipt_year;
			$receipt_month=substr($item["ReceiptDate"],5,2);
			$receipt_month=ltrim($receipt_month, '0');
			$currentYear=date("Y");
			$current_datetime=date('Y-m-d H:i:s');
			$current_month=date('m');
			$current_month = ltrim($current_month, '0');
			echo "receipt date";
			echo $item["ReceiptDate"];
			
				// for each item
				if ($item["TaxPercent"]==24){$item["TaxPercent"]=1410;}
				elseif ($item["TaxPercent"]==13){$item["TaxPercent"]=1131;}
				else{echo "vat not in s1 for ";print_r ($item);}
				//check if itemid exists in s1 mtrl codes. if not insert it in the erp
				if ($item["ProductCategoryCode"]>0){
				$item_found=False;
				$get_codes = mssql_query('SELECT CODE FROM MTRL where company = 1006 and code = "'.$item["ProductCode"].'" ') or die('MSSQL error: SELECT CODE FROM MTRL where company = 1006 and code = "'.$item["ProductCode"].'" ' . mssql_get_last_message());
				while ($s1_code = mssql_fetch_array($get_codes)) {
					if ($s1_code["CODE"]==$item["ProductCode"]){$item_found=True; 
					$get_mtrgroups = mssql_query('SELECT MTRGROUP FROM MTRL where company = 1006 and MTRGROUP = "'.$item["ProductCategoryCode"].'"') or die('MSSQL error: SELECT MTRGROUP FROM MTRL where company = 1006 and MTRGROUP = "'.$item["ProductCategoryCode"].'"' . mssql_get_last_message());
					if (mssql_num_rows($get_codes)==0){$item["ProductCategoryCode"]=50;}
					break;}
				}
				if ($item_found==False){ 
					$get_mtrgroups = mssql_query('SELECT MTRGROUP FROM MTRL where company = 1006 and MTRGROUP = "'.$item["ProductCategoryCode"].'"') or die('MSSQL error: SELECT MTRGROUP FROM MTRL where company = 1006 and MTRGROUP = "'.$item["ProductCategoryCode"].'"' . mssql_get_last_message());
					if (mssql_num_rows($get_codes)==0){$item["ProductCategoryCode"]=50;}
					$current_datetime=date('Y-m-d H:i:s');
					$ins_mtrl= mssql_query("INSERT INTO MTRL (COMPANY,SODTYPE,LOCKID,CODE,NAME,ISACTIVE,MTRTYPE,MTRTYPE1,CRDCARDMODE,MTRGASTYPE,VAT,MTRUNIT1,MTRUNIT3,MTRUNIT4,MU31,MU41,MU13MODE,MU14MODE,MTRGROUP,SOCURRENCY,KEPYO,MUMD,REMAINMODE,DIMMD,DIMMTRUNIT,FROMVAL,CHKMAXPRCDISC,CALCONCREDIT,REPLPUR,REPLSAL,REPLITE,AUTOUPDPUR,AUTOUPDSAL,AUTOUPDITE,PRINTPURMD,PRINTSALMD,PRINTITEMD,UNIQSUB,MTRLOTUSE,MTRSNUSE,ISTOTSRVCARD,MTRTHIRD,USESTBIN,MTRONORDER,TURNOVR,HASBAIL,INSDATE,INSUSER,UPDDATE,UPDUSER) VALUES (1006,51,0,'".strval($item["ProductCode"])."','".strval($item["Product"])."',1 ,0 ,0 ,0 ,0 ,'".$item["TaxPercent"]."',101 ,101 ,101 ,1 ,1 ,1 ,1 ,'".$item["ProductCategoryCode"]."',100 ,1 ,1 ,1 ,0 ,0 ,0 ,0 ,1 ,0 ,0 ,0 ,0,0 ,0 ,0 ,0 ,0 ,1 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,'".$current_datetime."' ,1 ,'".$current_datetime."',1 ); SELECT  SCOPE_IDENTITY() AS mtrl;")or die("MSSQL error: INSERT INTO MTRL (COMPANY,SODTYPE,LOCKID,CODE,NAME,ISACTIVE,MTRTYPE,MTRTYPE1,CRDCARDMODE,MTRGASTYPE,VAT,MTRUNIT1,MTRUNIT3,MTRUNIT4,MU31,MU41,MU13MODE,MU14MODE,MTRGROUP,SOCURRENCY,KEPYO,MUMD,REMAINMODE,DIMMD,DIMMTRUNIT,FROMVAL,CHKMAXPRCDISC,CALCONCREDIT,REPLPUR,REPLSAL,REPLITE,AUTOUPDPUR,AUTOUPDSAL,AUTOUPDITE,PRINTPURMD,PRINTSALMD,PRINTITEMD,UNIQSUB,MTRLOTUSE,MTRSNUSE,ISTOTSRVCARD,MTRTHIRD,USESTBIN,MTRONORDER,TURNOVR,HASBAIL,INSDATE,INSUSER,UPDDATE,UPDUSER) VALUES (1006,51,0,'".strval($item["ProductCode"])."','".strval($item["Product"])."',1 ,0 ,0 ,0 ,0 ,'".$item["TaxPercent"]."',101 ,101 ,101 ,1 ,1 ,1 ,1 ,'".$item["ProductCategoryCode"]."',100 ,1 ,1 ,1 ,0 ,0 ,0 ,0 ,1 ,0 ,0 ,0 ,0,0 ,0 ,0 ,0 ,0 ,1 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,'".$current_datetime."' ,1 ,'".$current_datetime."',1 );" . mssql_get_last_message());
					while ($r=mssql_fetch_array($ins_mtrl)){$item["mtrl"]=$r[0];}
					$ins_mtrextra=mssql_query("INSERT INTO MTREXTRA (COMPANY,SODTYPE,MTRL,BOOL01,BOOL02,BOOL03,BOOL04) VALUES (1006 ,51 ,'".$item["mtrl"]."',0 ,0 ,0 ,0 )") or die("MSSQL error: INSERT INTO MTREXTRA (COMPANY,SODTYPE,MTRL,BOOL01,BOOL02,BOOL03,BOOL04) VALUES (1006 ,51 ,'".$item["mtrl"]."',0 ,0 ,0 ,0 )" . mssql_get_last_message());
					$ins_xtrdocdata=mssql_query("INSERT INTO XTRDOCDATA (REFOBJID,SOSOURCE,LNUM,LINENUM,DBWHOUSED,DEFXTRDOC,XDOCTYPE,SOMD) VALUES ('".$item["mtrl"]."',51 ,0 ,0 ,0 ,0 ,0 ,0 )") or die("MSSQL error: INSERT INTO XTRDOCDATA (REFOBJID,SOSOURCE,LNUM,LINENUM,DBWHOUSED,DEFXTRDOC,XDOCTYPE,SOMD) VALUES ('".$item["mtrl"]."',51 ,0 ,0 ,0 ,0 ,0 ,0 )" . mssql_get_last_message());
					}
				}else{//for the extras category representiong toppings etc
					$item["ProductCategoryCode"]=35;
					if ($item["TaxPercent"]==24){$item["ProductCode"]='9924';$item["TaxPercent"]=1410;}
					else{$item["ProductCode"]='9913';$item["TaxPercent"]=1131;}
					}
			$ct++;
			
			$ph=array_push($mtrlines,$item);
			
			if($ct==$num_items){ //this will run at the last loop of the items to insert receipt as an order to the erp
								//seriesnum is the number of the document for the series of the year
				$q = mssql_query("UPDATE SERIESNUM SET SERIESNUM=SERIESNUM+1 WHERE COMPANY=1006 AND SOSOURCE=1351 AND SERIES=7071 AND FISCPRD='".intval($receipt_year)."'") or die("MSSQL error: UPDATE SERIESNUM SET SERIESNUM=SERIESNUM+1 WHERE COMPANY=1006 AND SOSOURCE=1351 AND SERIES=7071 AND FISCPRD='".intval($receipt_year)."'" . mssql_get_last_message());
				$qseriesnum = mssql_query("SELECT SERIESNUM FROM SERIESNUM WHERE COMPANY=1006 AND SOSOURCE=1351 AND SERIES=7071 AND FISCPRD='".intval($receipt_year)."'") or die("MSSQL error: SELECT SERIESNUM FROM SERIESNUM WHERE COMPANY=1006 AND SOSOURCE=1351 AND SERIES=7071 AND FISCPRD='".intval($receipt_year)."'" . mssql_get_last_message());
				while( $w = mssql_fetch_array( $qseriesnum) ) {
				$seriesnum =  $w['SERIESNUM'];}
				$seriesnum=sprintf('%07d', $seriesnum);
				echo $seriesnum;
				$fincode="ΑΛΠ".$seriesnum;
				//inserting the order's document
			$ins=mssql_query("INSERT INTO FINDOC (COMPANY,LOCKID,SOSOURCE,SOREDIR,TRNDATE,FISCPRD,PERIOD,SERIES,SERIESNUM,FPRMS,TFPRMS,FINCODE,BRANCH,SODTYPE,TRDR,VATSTS,SOCURRENCY,TRDRRATE,LRATE,ORIGIN,GLUPD,SXUPD,PRDCOST,ISCANCEL,ISPRINT,ISREADONLY,APPRVDATE,APPRVUSER,APPRV,FULLYTRANSF,PAYMENT,LTYPE1,LTYPE2,LTYPE3,LTYPE4,SOTIME,TURNOVR,TTURNOVR,LTURNOVR,VATAMNT,TVATAMNT,LVATAMNT,EXPN,TEXPN,LEXPN,DISC1PRC,DISC1VAL,TDISC1VAL,LDISC1VAL,DISC2PRC,DISC2VAL,TDISC2VAL,LDISC2VAL,NETAMNT,TNETAMNT,LNETAMNT,SUMAMNT,SUMTAMNT,SUMLAMNT,FXDIFFVAL,KEPYOMD,KEPYOHANDMD,KEPYOQT,LKEPYOVAL,GSISMD,GSISQTY,GSISNET,GSISVAT,GSISPACKAGES,GSISFLG,CHANGEVAL,INTVAL,INTVAT,ISTRIG,BGDOCDATE,INSDATEN,INPAYVAT,INSDATE,INSUSER,UPDDATE,UPDUSER,NUM01,NUM02) VALUES (1006,7,1351,0 ,'".$item["ReceiptDate"]."','".$receipt_year."','".$receipt_month."',7071,'".$seriesnum."',7071,131 ,'".$fincode."',1001,13,1029,1 ,100 ,1,1 ,1 ,0 ,0 ,0 ,0 ,0 ,0 ,'".$item["ReceiptDate"]."',1 ,1 ,0 ,1,1 ,0 ,0 ,0 ,'".$item["ReceiptDate"]."',0,0,0,0,0,0,0 ,0 ,0 ,0,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0,0,0,0,1 ,1 ,0 ,1 ,0 ,0 ,0 ,1 ,1 ,0,0,3 ,1 ,0 ,0 ,0 ,0 ,'".$item["ReceiptDate"]."','".$item["ReceiptDate"]."',0 ,'".$item["ReceiptDate"]."',1 ,'".$item["ReceiptDate"]."',1,'".$item["OrderID"]."','".$item["Receipts_ID"]."' ); SELECT SCOPE_IDENTITY() AS findoc;") or die("MSSQL error: INSERT INTO FINDOC (COMPANY,LOCKID,SOSOURCE,SOREDIR,TRNDATE,FISCPRD,PERIOD,SERIES,SERIESNUM,FPRMS,TFPRMS,FINCODE,BRANCH,SODTYPE,TRDR,VATSTS,SOCURRENCY,TRDRRATE,LRATE,ORIGIN,GLUPD,SXUPD,PRDCOST,ISCANCEL,ISPRINT,ISREADONLY,APPRVDATE,APPRVUSER,APPRV,FULLYTRANSF,LTYPE1,LTYPE2,LTYPE3,LTYPE4,SOTIME,TURNOVR,TTURNOVR,LTURNOVR,VATAMNT,TVATAMNT,LVATAMNT,EXPN,TEXPN,LEXPN,DISC1PRC,DISC1VAL,TDISC1VAL,LDISC1VAL,DISC2PRC,DISC2VAL,TDISC2VAL,LDISC2VAL,NETAMNT,TNETAMNT,LNETAMNT,SUMAMNT,SUMTAMNT,SUMLAMNT,FXDIFFVAL,KEPYOMD,KEPYOHANDMD,KEPYOQT,LKEPYOVAL,GSISMD,GSISQTY,GSISNET,GSISVAT,GSISPACKAGES,GSISFLG,CHANGEVAL,INTVAL,INTVAT,ISTRIG,BGDOCDATE,INSDATEN,INPAYVAT,INSDATE,INSUSER,UPDDATE,UPDUSER,NUM01,NUM02) VALUES (1006,7,1351,0 ,'".$item["ReceiptDate"]."','".$receipt_year."','".$receipt_month."',7071,'".$seriesnum."',7071,131 ,'".$fincode."',1001,13,1029,1 ,100 ,1,1 ,1 ,0 ,0 ,0 ,0 ,0 ,0 ,'".$item["ReceiptDate"]."',1 ,1 ,0 ,1 ,0 ,0 ,0 ,'".$item["ReceiptDate"]."',0,0,0,0,0,0,0 ,0 ,0 ,0,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0,0,0,0,1 ,1 ,0 ,1 ,0 ,0 ,0 ,1 ,1 ,0,0,3 ,1 ,0 ,0 ,0 ,0 ,'".$item["ReceiptDate"]."','".$item["ReceiptDate"]."',0 ,'".$item["ReceiptDate"]."',1 ,'".$item["ReceiptDate"]."',1,'".$item["OrderID"]."','".$item["Receipts_ID"]."')" . mssql_get_last_message());
			echo "fincode: ".$fincode." ";
				while ($smm=mssql_fetch_array($ins)){$findoc=$smm[0];}
				$abs_qty=0;
				for ($i = 0; $i < count($mtrlines); $i++) {$abs_qty=$abs_qty+$mtrlines["Quantity"];}
				$ins=mssql_query("INSERT INTO MTRDOC (COMPANY,FINDOC,WHOUSE,QTY,QTY1,QTY2,QTY1S,QTY1A,WASTE,COSTCOEF,SALESCVAL,QTY1H,QTY2H,BGINTCOUNTRY) VALUES (1006 ,'".$findoc."',1006 ,'".$abs_qty."' ,'".$abs_qty."' ,'".$abs_qty."' ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,1000 )")or die("MSSQL error: INSERT INTO MTRDOC (COMPANY,FINDOC,WHOUSE,QTY,QTY1,QTY2,QTY1S,QTY1A,WASTE,COSTCOEF,SALESCVAL,QTY1H,QTY2H,BGINTCOUNTRY) VALUES (1006 ,'".$findoc."',1006 ,'".$abs_qty."' ,'".$abs_qty."' ,'".$abs_qty."' ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,1000 )" . mssql_get_last_message());
				//print_r ($mtrlines);
				$i=1;
				foreach ($mtrlines as $mtrline) { //inserting the items to the newly created order
				if ($mtrline["Value"]!=0){
				$mtrl=mssql_query("SELECT mtrl from mtrl where code ='".$mtrline["ProductCode"]."'")or die("MSSQL error: SELECT mtrl from mtrl where code ='".$mtrline["ProductCode"]."'" . mssql_get_last_message());
				while ($ar=mssql_fetch_array($mtrl)){$mtrline["mtrl"]=$ar["mtrl"];}
				if($mtrline["ProductCategoryCode"]=35 and $mtrline["TaxPercent"]==24){
				$mtrline["ProductCode"]='9924';}else{$mtrline["ProductCode"]='9913';}
				$ins=mssql_query("INSERT INTO MTRLINES (COMPANY,FINDOC,MTRLINES,LINENUM,SODTYPE,MTRL,PENDING,SOSOURCE,SOREDIR,MTRTYPE,SOTYPE,WHOUSE,MTRUNIT,VAT,QTY,QTY1,QTY2,QTY1COV,QTY1CANC,QTY1FCOV,WEIGHT,VOLUME,BGINTCOUNTRY,PRICE,PRICE1,LINEVAL,LLINEVAL,EXPVAL,LEXPVAL,NETLINEVAL,LNETLINEVAL,VATAMNT,LVATAMNT,LVATNOEXM,EFKVAL,TRNLINEVAL,LTRNLINEVAL,SXPERC,BGLEXCISE,AUTOPRDDOC) VALUES (1006 ,'".intval($findoc)."','".($i)."' ,'".($i)."' ,51 ,'".$mtrline["mtrl"]."' ,0 ,1351 ,0 ,0 ,0 ,1006 ,101 ,'".$mtrline["TaxPercent"]."' ,'".$mtrline["Quantity"]."' ,'".$mtrline["Quantity"]."' ,'".$mtrline["Quantity"]."' ,0 ,0 ,0 ,0 ,0 ,1000 ,'".$mtrline["Value"]/$mtrline["Quantity"]."' ,'".$mtrline["Value"]."' ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,2 ,0 ,1 )") or die("MSSQL error: INSERT INTO MTRLINES (COMPANY,FINDOC,MTRLINES,LINENUM,SODTYPE,MTRL,PENDING,SOSOURCE,SOREDIR,MTRTYPE,SOTYPE,WHOUSE,MTRUNIT,VAT,QTY,QTY1,QTY2,QTY1COV,QTY1CANC,QTY1FCOV,WEIGHT,VOLUME,BGINTCOUNTRY,PRICE,PRICE1,LINEVAL,LLINEVAL,EXPVAL,LEXPVAL,NETLINEVAL,LNETLINEVAL,VATAMNT,LVATAMNT,LVATNOEXM,EFKVAL,TRNLINEVAL,LTRNLINEVAL,SXPERC,BGLEXCISE,AUTOPRDDOC) VALUES (1006 ,'".intval($findoc)."','".($i)."' ,'".($i)."' ,51 ,'".$mtrline["mtrl"]."' ,0 ,1351 ,0 ,0 ,0 ,1006 ,101 ,'".$mtrline["TaxPercent"]."' ,'".$mtrline["Quantity"]."' ,'".$mtrline["Quantity"]."' ,'".$mtrline["Quantity"]."' ,0 ,0 ,0 ,0 ,0 ,1000 ,'".$mtrline["Value"]/$mtrline["Quantity"]."' ,'".$mtrline["Value"]."' ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,2 ,0 ,1 )" . mssql_get_last_message());
				$i++;
				}
				
				}
				echo "receipt".$mtrline["Receipts_ID"]."<br />";
				//$sqlmy = "UPDATE orders set is_sent=1 WHERE Receipts_ID = '".$receipt['Receipts_ID']."'";
				//$query = mysql_query($sqlmy)or die (mysql_error($con));
				
				
			}
		}
		
	$upd = mysql_query("UPDATE orders SET is_sent = 1 WHERE Receipts_ID = '".$receipt['Receipts_ID']."'")or die (mysql_error($con));	
	} 

}else{echo 'no receipts found';}
