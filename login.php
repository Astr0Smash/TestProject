<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name='viewport' content='width=device-width'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Store Rating</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/styles_menu.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>

</head>
<body>


<?php
session_set_cookie_params(3600 * 24 * 7);

session_start();
error_reporting(0); 


//require('functions.php');  extracted db connect functions, moved some to functions.tools  rest moved to functions.general2
require('functions.db_connect.php');
require('functions.general2.php');  //not needed here;

////////////////////////////////////////////////////////
//require('functions_general.php'); split file into:
require('functions.page_access.php');
require('functions.menu.php');
require('functions.tools.php');
require('functions.general.php'); 
////////////////////////////////////////////////////////

////////////////////////////////////////////////////////
//require('functions_reports.php'); split file into:
require('functions.stores.php');
require('functions.reports.php'); 
////////////////////////////////////////////////////////


require('functions_javascript.php');


	require('store_procs.php');
	require('functions_storerating.php');

	
	
	
	
$dev = 0;
$link;

$info_error = '';

if( isset($_POST['login'])) 
{
	require_once('Browser.php');
	$browser = new Browser();
	//echo 'Logging in ';
	
	//$weblogin = webLogon(addslashes($_POST['email']),addslashes($_POST['password']));
	$weblogin = WebLogonOS($_POST['email'], $_POST['password'], $browser->getPlatform(),$browser->getBrowser(), $browser->getVersion());
	
	if ($weblogin >= 0)
	{
		
		//WebLogonOS($_POST['email'], $_POST['password'], $browser->getPlatform(),$browser->getBrowser(), $browser->getVersion());
		$_SESSION['LoggedIn'] = 1;
	//	$url=$_SESSION['logged_in_from'];
		//$url = 'index.php';
		if (CheckUserType($_SESSION['UID'],  'Owner'))
		{
			if ((!isset($_SESSION['logged_in_from'])) || ($_SESSION['logged_in_from'] == ''))
			{
				$_SESSION['logged_in_from'] = 'financials/dashboard.php';
			}
			//$url = 'financials/dashboard.php';
			GetAppUser_Stores($_SESSION['UID'], $oStoreID, $oStoreCode, $oStore, $oStoreTypeID, $oStoreType, $oCountryAreaID, $oCountryID, $oCountry, $oAreaID, $oArea, $oActive_Tag, $oEmail);
			$_SESSION['StoreID'] = $oStoreID[0];
			$_SESSION['Store'] = $oStore[0];
			
			$_SESSION['sc'] = $oStoreCode[0];
			$_SESSION['CurrentStore'] = $oStore[0];
			$_SESSION['StoreTypes'] = $oStoreTypeID[0];
			$_SESSION['StoreTypeID'] = $oStoreTypeID[0];
			$_SESSION['LimitedFinancials'] = true;
		}
		else if (CheckUserType($_SESSION['UID'], 'Store'))
		{
			if ((!isset($_SESSION['logged_in_from'])) || ($_SESSION['logged_in_from'] == ''))
			{
				$_SESSION['logged_in_from'] = 'financials/dashboard.php';
			}
			//$url = 'financials/dashboard.php';
			GetStoreDetails_FromAppUserID($_SESSION['UID'], $oStoreID, $oStoreCode, $oDescr, $oStoreTypeID, $oStoreType, $oCountryAreaID, $oCountryID, $oCountry, $oAreaID, $oArea, $oActive_Tag, $oEmail);
			$_SESSION['uStoreID'] = $oStoreID;
			$_SESSION['uStore'] = $oStore;

			
		}
		
		if (CheckUserType($_SESSION['UID'], 'HQUser'))
		{
			if ((!isset($_SESSION['logged_in_from'])) || ($_SESSION['logged_in_from'] == ''))
			{
				$_SESSION['logged_in_from'] = 'financials/dashboard.php';
			}
		}
			
		if ((!isset($_SESSION['logged_in_from'])) || ($_SESSION['logged_in_from'] == ''))
		{
			$_SESSION['logged_in_from'] = 'index.php';
		}
		$url = $_SESSION['logged_in_from'];
		
		
		changePage($url);
		exit; 
	}
	elseif ($weblogin == -1)
	{
		printHeader();
		print ("<br /><p style='font-size:larger;color:#F00; text-align:center'>Email address doesn't exist</p>");
		generateLoginForm();
	}
	else
	{
		printHeader();
		print ("<br /><p style='font-size:larger;color:#F00; text-align:center'>Username or password is incorrect</p>");
		generateLoginForm();
	}

   	
} 
elseif (isset($_POST['sendpassword']))
{
	printHeader();
	if ($_POST['email'] == '')
	{
		print ("<p style='font-size:larger;color:#F00; text-align:center'>Please enter an email address to send password to</p>");
		generateLoginForm();
	}
	else 
	{
		GetAUL($_POST['email'], $oAppUserID, $oFirstName, $oSurName, $oPWord);
		if ($oAppUserID != -1)
		{
			$CustomSubject = "Store Rating - Password Recovery";
			$CustomMessage = "Your Logon details are as follows: <br /><br />Website: <a href='http://storerating.ctfm.co.za'>http://storerating.ctfm.co.za </a><br />User Name: " . $_POST['email']. "<br />Password: " . $oPWord . "<br /><br />To change your password: Logon to <a href='storerating.ctfm.co.za'>storerating.ctfm.co.za</a>, then go to 'Profile' to change your password.";
			
			
			
			EmailBasic($_POST['email'], $CustomSubject, $CustomMessage, $oFirstName);
			EmailBasic('eddie@ctfm.co.za, gustav@ctfm.co.za', $CustomSubject, $CustomMessage, $oFirstName);
			print ("<p style='color:#F00; text-align:center'>Password sent to " . $_POST['email'] . "</p>");
			generateLoginForm();
		}
		else
		{		
			print ("<p style='color:#F00; text-align:center'>Email address provided does not exist on our system</p>");
			generateLoginForm();
			
		}
	}
	
	
}
else
{
	printHeader();
	getTResultDetails($_SESSION['TResultID']);
	generateLoginForm();
}
?>
</body>
</html>