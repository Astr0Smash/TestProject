<?php
session_start();

require ('func_main.php');
require ('functions_bookings.php');

date_default_timezone_set('Africa/Johannesburg');
$today = date("Y-m-d");
$now = date("H:i");


if (!isset($_REQUEST['bookdate']))
{
	$_REQUEST['bookdate'] = $today;
}


if (isset($_REQUEST['booktimes']))
{
	$noReservations = false;
	$noReservationsStore = false;
	if (isset($_SESSION['uBooking_UID']))
	{
		$iCust = 0;
		
		
		GetNoBookingDays($_SESSION['Sel_StoreID'], $NoBookingID, $StoreID, $SDate, $STime, $ETime, $AllDay, $Reason, $Active_Tag);
		
		for ($i = 0; $i < count($NoBookingID); $i++) 
		{
			if (($_REQUEST['bookdate'] ==  $SDate[$i]) && ($AllDay[$i] == 1)) 
			{
				$noReservationsStore = true;
				$TheReason = $Reason[$i];
			}
		}
		
	}
	else 
	{
		//1
		$iCust = 1;
		GetNoBookingDays($_SESSION['Sel_StoreID'], $NoBookingID, $StoreID, $SDate, $STime, $ETime, $AllDay, $Reason, $Active_Tag);
		
		for ($i = 0; $i < count($NoBookingID); $i++) 
		{
			if (isset($_REQUEST['bookdate']))
			{
				if (($_REQUEST['bookdate'] ==  $SDate[$i]) && ($AllDay[$i] == 1)) 
				{
					$noReservations = true;
					$TheReason = $Reason[$i];
				}
			}
		}
		
		
	}
	
		if ($noReservations == true)
		{
	
			print "<br /><span style='color:#ff0000' >".$TheReason. "</span><br />Please contact store to hear if there are any tables available.";
			
		}
		else
		{
			if ($noReservationsStore == true)
			{
		
				print "<br /><span style='color:#ff0000' >".$TheReason. "</span><br />Please contact store to hear if there are any tables available.";
				
			}
	
		if (isset($_REQUEST['SPID']))
		{

			GetSPDetails($_REQUEST['SPID'], $SPID, $Special, $spDStamp, $spSDate, $spEDate, $spSTime, $spETime, $BLimit, $Active_Tag, $MinPPL, $CutoffTime, $spDescr, $spEmail, $spLimit, $spTerms);
			$_REQUEST['bookdate'] = $spSDate ;
			print "<input name='date1' type='hidden' value='".$spSDate."'  />";
			//GetSPBookingHours($_SESSION['Sel_StoreID'], $spSDate, $_REQUEST['SPID'], $StoreID, $DTime);
			GetSPBookingHours($_SESSION['Sel_StoreID'], $spSDate, $_REQUEST['SPID'], $iCust, $StoreID, $DTime);
			//print "SP";
		}
		else
		{
			GetBookingHours($_SESSION['Sel_StoreID'], $_REQUEST['bookdate'], $now,$iCust, $StoreID, $DTime );
			//print "NOSP";
		}
		
		$weekday = date('l', strtotime($_REQUEST['bookdate']));
		print "<div>";
		if ($_REQUEST['bookdate'] != '')
		{
			print "<span style='color:#00affd' >". $_REQUEST['bookdate'] . " " . $weekday . "</span><br /><br />";
		}
		
		print "Available Times<br /><span style='font-size:11px'>Note: Online bookings must be made more than four hours in advance</span><br/>";
	
		print "</div>";
		if (count($DTime) > 0)
		{
			print "<select id='book_Time' data-native-menu='false'  name='book_Time'   class='required'>";
			for ($i = 0; $i < count($DTime); $i++) 
			{
				if ($_SESSION['book_Time'] == substr($DTime[$i],11,5))
				{
					print "<option selected='selected'>".substr($DTime[$i],11,5)."</option>";
				}
				else
				{
					print "<option>".substr($DTime[$i],11,5)."</option>";
				}
			}				
			
			print "</select>";
		}
		else
		{
			print "<br /><strong style='color:red'>No time slot available or fully booked</strong>";
			
			
		}
		if ((isset($_SESSION['uBooking_StoreID'])) && ($_SESSION['uBooking_StoreID'] > 0) && ($_SESSION['Sel_StoreID'] == $_SESSION['uBooking_StoreID']))
		{
			GetStoreBookings($_SESSION['uBooking_StoreID'], $_REQUEST['bookdate'],$_REQUEST['bookdate'], $tdyBookingID, $tdyStoreID, $tdyStoreCode, $tdyCustID, $tdyemail, $tdyRefCode, $tdyBStatusID, $tdyBStatus, $tdyTableNo, $tdyFirstName, $tdySurName, $tdyCell, $tdyTel, $tdyTStamp, $tdySDate, $tdySTime, $tdyETime, $tdyGuests, $tdyAdults, $tdyChildren, $tdyInfants, $tdySpecialRequirement, $tdyStore, $tdySeatingTypeID, $tdySeatingType, $tdyTableNos, $ospSpecial);
			//GetStoreTodayBookings($_SESSION['uBooking_StoreID'],$tdyBookingID, $tdyStoreID, $tdyStoreCode, $tdyCustID, $tdyemail, $tdyRefCode, $tdyBStatusID, $tdyBStatus, $tdyTableNo, $tdyFirstName, $tdySurName, $tdyCell, $tdyTel, $tdyTStamp, $tdySDate, $tdySTime, $tdyETime, $tdyGuests, $tdyAdults, $tdyChildren, $tdyInfants, $tdySpecialRequirement, $tdyStore, $tdySeatingTypeID, $tdySeatingType, $tdyTableNos);
			$todayBookings = "<h4>Bookings on ".$_REQUEST['bookdate'].":</h4>";
			$todayBookings .= "<table border='0' cellspacing='1'  cellpadding='2' /> ";
			$todayBookings .= "<tr>"; 
			$todayBookings .= "<th>RefCode</th>";
			$todayBookings .= "<th>Time</th>"; 
			$todayBookings .= "<th>Guests</th>";
			$todayBookings .= "<th>Area</th>";  
			$todayBookings .= "<th>Status</th>"; 
			$todayBookings .= "</tr>"; 
			for ($i = 0; $i < count($tdyBookingID); $i++) 
			{
				if ($tdyBStatus[$i] != 'Cancelled')
				{
					$todayBookings .= "<tr>";
					$todayBookings .= "<td style='background-color:#6699cc'>";
					$todayBookings .= $tdyRefCode[$i];
					$todayBookings .= "</td>";
					$todayBookings .= "<td style='background-color:#6699cc'>";
					$todayBookings .= $tdySTime[$i];
					$todayBookings .= "</td>";
					$todayBookings .= "<td style='background-color:#6699cc'>";
					$todayBookings .= $tdyGuests[$i];
					$todayBookings .= "</td>";
					$todayBookings .= "<td style='background-color:#6699cc'>";
					$todayBookings .= $tdySeatingType[$i];
					$todayBookings .= "</td>";
					$todayBookings .= "<td style='background-color:#6699cc'>";
					$todayBookings .= $tdyBStatus[$i];
					$todayBookings .= "</td>";
					$todayBookings .= "</tr>";
				}
			}
			$todayBookings .= "</table>";
			print $todayBookings;
		}
	}
	
	
}

else
{
	$_SESSION['book_StoreID'] = $_REQUEST['StoreID'];



	GetStoreDetails($_REQUEST['StoreID'], $StoreID, $StoreCode, $Store, $StoreTypeID, $CountryAreaID, $Active_Tag, $store_Email, $StoreType, $CountryID, $AreaID, $Country, $Area, $Landline,$Cell);
	
	$_SESSION['Sel_StoreID'] = $_REQUEST['StoreID'];
	GetStoreSeatingTypes($_REQUEST['StoreID'], $StoreID, $SeatingTypeID, $SeatingType, $Cnt);
	
	print "<div style='font-size:11px'> ";
	print "<strong >".$Store." Contact Numbers:</strong><br />";
	if ($Cell != '')
	{
		print "Cell: <span style='color:#00affd' >" . $Cell."</span><br />";
	}
	if ($Landline != '')
	{
		print "Landline: <span style='color:#00affd' >" . $Landline."</span><br /><br />";
	}
	print "</div>";
	/////////////////////////////////////////
	
	
	GetStoreSP($_REQUEST['StoreID'],2, $StoreID, $SPID, $BCnt, $StoreCode, $Store, $Special, $DStamp, $SDate, $EDate, $STime, $ETime, $BLimit, $Active_Tag, $MinPPL, $CutoffTime,$spDescr);
	
	if (count($SPID) > 0)
	{
		print "<div style='box-shadow: 1px 1px 2px #000000;border:1px solid;border-radius:15px;padding:5px;background-color:#ffd386;color:#00affd'><br /><strong><span style='color:red'>Specials &amp; vouchers available at this store:</span></strong>";
		//GetStores(1, $StoreID, $StoreCode, $Store, $StoreTypeID, $Country, $Area);
		
		
		print "<select name='SPID'  onchange=\"$('.SPDescrdiv').hide();  $('#SPDescrdiv_'+this.value).show(); if (this.value > 0 ){     $('#StoreSeatTypesdiv').show();   $('#seating').show();    updateTimesSP(this.value);   $('.showform').show();  $('#HideDateForSpecial').hide(); }else { $('#HideDateForSpecial').show();updateTimes();}   \">";
		print "<option value='-1' >No Special Selected</option>";
		for ($i = 0; $i < count($SPID); $i++) 
		{
			if (($BLimit[$i] > -1) && ($BCnt[$i] <= $BLimit[$i]))
			{
				print "<option value='".$SPID[$i]."' >".$Special[$i]."</option>";
			}
			else
			{
				print "<option value='-1' style='color:#ff0000' disabled='disabled'>".$Special[$i]." - Sold Out</option>";
			}

		}
		print "</select><br />";
		
		for ($i = 0; $i < count($SPID); $i++) 
		{
			print "<div id='SPDescrdiv_".$SPID[$i]."' style='display:none' class='SPDescrdiv'>".$spDescr[$i]."";
			print "<br /><strong style='color:#ff0000'>Note: Email address required to receive your booking confirmation.</strong><br />";
			print "</div>";

		}
		print "</div><br /><br />";
	}
	
	print "Seating Area<br />";
	print "<select  data-native-menu='false'   id='book_StoreSeatingTypeID' name='book_StoreSeatingTypeID'  >";
	
	for ($i = 0; $i < count($SeatingTypeID); $i++)
	{
		if ($_SESSION['book_StoreSeatingTypeID'] == $SeatingTypeID[$i])
		{
			print "<option value='".$SeatingTypeID[$i]."' selected='selected'>".$SeatingType[$i]."</option>";
		}
		else
		{
			print "<option value='".$SeatingTypeID[$i]."'>".$SeatingType[$i]."</option>";
		}
	}
	print "</select>";
	
	
}




