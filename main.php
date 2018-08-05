<!DOCTYPE html>
<html>
  <head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="UTF-8"> 
  <link rel="stylesheet" type="text/css" href="kremowy.css">
  </head>
  <body>
    <h3>Prognoza pogody</h3>
	Prognoza pogody w oparciu o źródła lotnicze. Prognoza powinna być aktualna na 3 do 24 godzin. Należy kliknąć znacznik danego miasta/lotniska, aby wyświetlić okno z prognozą dla niego.
	<?php	
		
		//Ustawienie domyślnej strefy czasowej na polską:
		
		date_default_timezone_set('Europe/Warsaw');
		
		//Wczytanie daty i operacje na niej:
		
		$day = date('d');
		$month = date('m'). '';
		$year = date('Y'). '';
		$hour = date('H');
		
		if ($day !== 1)
		{
			$yesterday = $day - 1;
			$lastmonth = $month;
			$lastyear = $year;
		}
		else if ($month === 2 || $month === 4 || $month === 6 || $month === 8 || $month === 9 || $month === 11)
		{
			$yesterday = 31;
			$lastmonth = $month - 1;
			$lastyear = $year;
		}
		else if ($month === 5 || $month === 7 || $month === 10 || $month === 12)
		{
			$yesterday = 30;
			$lastmonth = $month - 1;
			$lastyear = $year;
		}
		else if ($month === 3 && $year % 4 !== 0 || $month === 3 && $year % 400 !== 0)
		{
			$yesterday = 28;
			$lastmonth = $month - 1;
			$lastyear = $year;
		}
		else if ($month === 3 && $year % 4 !== 0 && $year % 100 !== 0 || $month === 3 && $year % 4 === 0 && $year % 400 === 0)
		{
			$yesterday = 29;
			$lastmonth = $month - 1;
			$lastyear = $year;
		}
		else if ($month === 1)
		{
			$yesterday = 31;
			$lastmonth = 12;
			$lastyear = $year - 1;
		}	
			
		
		//Wczytanie do zmiennych adresów żródeł danych prognozy pogody:
		
		$url_eplb = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPLB&tipo=ALL&ord=REV&nil=NO&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_epkk = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPKK&tipo=ALL&ord=REV&nil=SI&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_epkt = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPKT&tipo=ALL&ord=REV&nil=SI&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_epwr = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPWR&tipo=ALL&ord=REV&nil=SI&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_epwa = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPWA&tipo=ALL&ord=REV&nil=SI&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_eppo = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPPO&tipo=ALL&ord=REV&nil=SI&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_epgd = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPGD&tipo=ALL&ord=REV&nil=NO&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_epra = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPRA&tipo=ALL&ord=REV&nil=NO&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";
		$url_epde = "http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPDE&tipo=ALL&ord=REV&nil=NO&fmt=html&ano=$lastyear&mes=$lastmonth&day=$yesterday&hora=$hour&anof=$year&mesf=$month&dayf=$day&horaf=$hour&minf=59&send=send";



		$rekordy = 'http://www.ogimet.com/display_metars2.php?lang=en&lugar=EPLB&tipo=ALL&ord=REV&nil=NO&fmt=html&ano=2018&mes=02&day=17&hora=07&anof=2018&mesf=02&dayf=18&horaf=07&minf=59&send=send';
		
		$rekordy_linie_eplb = array();
		$surowerekordy = file_get_contents($rekordy);
		
		//Otwarcie strony z zakodowaną prognozą pogody przez skrypt:
		$f_rekordy_eplb = @fopen($url_eplb, "r");
		//Poszukiwanei linii dotyczących bezpośrednio prognozy pogody:
		while (!feof($f_rekordy_eplb))
		{
			$buffer = fgets($f_rekordy_eplb);
			if(strpos($buffer, "TAF EPLB") !== FALSE)
				$rekordy_linie_eplb[] = $buffer;
		}
		fclose($f_rekordy_eplb);
		
		//Wyjęcie prędkości wiatru ze strony:
		$eplb_wind_knots = substr($rekordy_linie_eplb[0], 58, 2);
		//I konwersja na km/h.
		$eplb_wind_kph = $eplb_wind_knots * 0.539957;
		$eplb_wind = "Prędkość wiatru: $eplb_wind_kph kilometrów na godzinę";
		
		//Szukanie danych na temat chmur...
		
		if (strpos($rekordy_linie_eplb[0], "SKC") !== false || strpos($rekordy_linie_eplb[0], "CAVOK") !== false)
		{
			$eplb_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_eplb[0], "OVC") !== false)
		{
			$eplb_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_eplb[0], "BKN") !== false)
		{
			$eplb_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_eplb[0], "SCT") !== false)
		{
			$eplb_clouds = "Zachmurzenie: Niewielkie";
		}
		
		// ... i opadów:
		
		if (strpos($rekordy_linie_eplb[0], "DZ") !== false)
		{
			$eplb_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_eplb[0], "RA") !== false)
		{
			$eplb_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_eplb[0], "-RA") !== false)
		{
			$eplb_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_eplb[0], "+RA") !== false)
		{
			$eplb_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_eplb[0], "RA") == false || strpos($rekordy_linie_eplb[0], "DZ") == false)
		{
			$eplb_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_eplb[0], "SN") !== false)
		{
			$eplb_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_eplb[0], "GR") !== false)
		{
			$eplb_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_eplb[0], "-SN") !== false)
		{
			$eplb_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_eplb[0], "+SN") !== false)
		{
			$eplb_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_eplb[0], "SN") == false || strpos($rekordy_linie_eplb[0], "GR") == false)
		{
			$eplb_snow = "Brak opadów śniegu";
		}
		
		//Analogicznie dla Krakowa...
		
		$f_rekordy_epkk = @fopen($url_epkk, "r");
		while (!feof($f_rekordy_epkk))
		{
			$buffer = fgets($f_rekordy_epkk);
			if(strpos($buffer, "TAF EPKK") !== FALSE)
				$rekordy_linie_epkk[] = $buffer;
		}
		fclose($f_rekordy_epkk);
		
		$epkk_wind_knots = substr($rekordy_linie_epkk[0], 58, 2);
		$epkk_wind_kph = $epkk_wind_knots * 0.539957;
		$epkk_wind = "Prędkość wiatru: $epkk_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_epkk[0], "SKC") !== false || strpos($rekordy_linie_epkk[0], "CAVOK") !== false)
		{
			$epkk_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_epkk[0], "OVC") !== false)
		{
			$epkk_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_epkk[0], "BKN") !== false)
		{
			$epkk_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_epkk[0], "SCT") !== false)
		{
			$epkk_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_epkk[0], "DZ") !== false)
		{
			$epkk_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_epkk[0], "RA") !== false)
		{
			$epkk_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_epkk[0], "-RA") !== false)
		{
			$epkk_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_epkk[0], "+RA") !== false)
		{
			$epkk_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_epkk[0], "RA") == false || strpos($rekordy_linie_epkk[0], "DZ") == false)
		{
			$epkk_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_epkk[0], "SN") !== false)
		{
			$epkk_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_epkk[0], "GR") !== false)
		{
			$epkk_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_epkk[0], "-SN") !== false)
		{
			$epkk_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_epkk[0], "+SN") !== false)
		{
			$epkk_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_epkk[0], "SN") == false || strpos($rekordy_linie_epkk[0], "GR") == false)
		{
			$epkk_snow = "Brak opadów śniegu";
		}
		
		//...Katowic...
		
		$f_rekordy_epkt = @fopen($url_epkt, "r");
		while (!feof($f_rekordy_epkt))
		{
			$buffer = fgets($f_rekordy_epkt);
			if(strpos($buffer, "TAF EPKT") !== FALSE)
				$rekordy_linie_epkt[] = $buffer;
		}
		fclose($f_rekordy_epkt);
		
		$epkt_wind_knots = substr($rekordy_linie_epkt[0], 58, 2);
		$epkt_wind_kph = $epkt_wind_knots * 0.539957;
		$epkt_wind = "Prędkość wiatru: $epkt_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_epkt[0], "SKC") !== false || strpos($rekordy_linie_epkt[0], "CAVOK") !== false)
		{
			$epkt_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_epkt[0], "OVC") !== false)
		{
			$epkt_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_epkt[0], "BKN") !== false)
		{
			$epkt_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_epkt[0], "SCT") !== false)
		{
			$epkt_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_epkt[0], "DZ") !== false)
		{
			$epkt_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_epkt[0], "RA") !== false)
		{
			$epkt_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_epkt[0], "-RA") !== false)
		{
			$epkt_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_epkt[0], "+RA") !== false)
		{
			$epkt_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_epkt[0], "RA") == false || strpos($rekordy_linie_epkt[0], "DZ") == false)
		{
			$epkt_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_epkt[0], "SN") !== false)
		{
			$epkt_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_epkt[0], "GR") !== false)
		{
			$epkt_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_epkt[0], "-SN") !== false)
		{
			$epkt_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_epkt[0], "+SN") !== false)
		{
			$epkt_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_epkt[0], "SN") == false || strpos($rekordy_linie_epkt[0], "GR") == false)
		{
			$epkt_snow = "Brak opadów śniegu";
		}
		
		//...Wrocławia...
		
		$f_rekordy_epwr = @fopen($url_epwr, "r");
		while (!feof($f_rekordy_epwr))
		{
			$buffer = fgets($f_rekordy_epwr);
			if(strpos($buffer, "TAF EPWR") !== FALSE)
				$rekordy_linie_epwr[] = $buffer;
		}
		fclose($f_rekordy_epwr);
		
		$epwr_wind_knots = substr($rekordy_linie_epwr[0], 58, 2);
		$epwr_wind_kph = $epwr_wind_knots * 0.539957;
		$epwr_wind = "Prędkość wiatru: $epwr_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_epwr[0], "SKC") !== false || strpos($rekordy_linie_epwr[0], "CAVOK") !== false)
		{
			$epwr_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_epwr[0], "OVC") !== false)
		{
			$epwr_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_epwr[0], "BKN") !== false)
		{
			$epwr_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_epwr[0], "SCT") !== false)
		{
			$epwr_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_epwr[0], "DZ") !== false)
		{
			$epwr_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_epwr[0], "RA") !== false)
		{
			$epwr_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_epwr[0], "-RA") !== false)
		{
			$epwr_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_epwr[0], "+RA") !== false)
		{
			$epwr_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_epwr[0], "RA") == false || strpos($rekordy_linie_epwr[0], "DZ") == false)
		{
			$epwr_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_epwr[0], "SN") !== false)
		{
			$epwr_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_epwr[0], "GR") !== false)
		{
			$epwr_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_epwr[0], "-SN") !== false)
		{
			$epwr_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_epwr[0], "+SN") !== false)
		{
			$epwr_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_epwr[0], "SN") == false || strpos($rekordy_linie_epwr[0], "GR") == false)
		{
			$epwr_snow = "Brak opadów śniegu";
		}
		
		//...Warszawy...
		
		
		$f_rekordy_epwa = @fopen($url_epwa, "r");
		while (!feof($f_rekordy_epwa))
		{
			$buffer = fgets($f_rekordy_epwa);
			if(strpos($buffer, "TAF EPWA") !== FALSE)
				$rekordy_linie_epwa[] = $buffer;
		}
		fclose($f_rekordy_epwa);
		
		$epwa_wind_knots = substr($rekordy_linie_epwa[0], 58, 2);
		$epwa_wind_kph = $epwa_wind_knots * 0.539957;
		$epwa_wind = "Prędkość wiatru: $epwr_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_epwa[0], "SKC") !== false || strpos($rekordy_linie_epwa[0], "CAVOK") !== false)
		{
			$epwa_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_epwa[0], "OVC") !== false)
		{
			$epwa_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_epwa[0], "BKN") !== false)
		{
			$epwa_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_epwa[0], "SCT") !== false)
		{
			$epwa_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_epwa[0], "DZ") !== false)
		{
			$epwa_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_epwa[0], "RA") !== false)
		{
			$epwa_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_epwa[0], "-RA") !== false)
		{
			$epwa_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_epwa[0], "+RA") !== false)
		{
			$epwa_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_epwa[0], "RA") == false || strpos($rekordy_linie_epwa[0], "DZ") == false)
		{
			$epwa_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_epwa[0], "SN") !== false)
		{
			$epwa_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_epwa[0], "GR") !== false)
		{
			$epwa_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_epwa[0], "-SN") !== false)
		{
			$epwa_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_epwa[0], "+SN") !== false)
		{
			$epwa_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_epwa[0], "SN") == false || strpos($rekordy_linie_epwa[0], "GR") == false)
		{
			$epwa_snow = "Brak opadów śniegu";
		}
		
		//...Poznania...
		
		
		$f_rekordy_eppo = @fopen($url_eppo, "r");
		while (!feof($f_rekordy_eppo))
		{
			$buffer = fgets($f_rekordy_eppo);
			if(strpos($buffer, "TAF EPPO") !== FALSE)
				$rekordy_linie_eppo[] = $buffer;
		}
		fclose($f_rekordy_eppo);
		
		$eppo_wind_knots = substr($rekordy_linie_eppo[0], 58, 2);
		$eppo_wind_kph = $eppo_wind_knots * 0.539957;
		$eppo_wind = "Prędkość wiatru: $eppo_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_eppo[0], "SKC") !== false || strpos($rekordy_linie_eppo[0], "CAVOK") !== false)
		{
			$eppo_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_eppo[0], "OVC") !== false)
		{
			$eppo_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_eppo[0], "BKN") !== false)
		{
			$eppo_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_eppo[0], "SCT") !== false)
		{
			$eppo_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_eppo[0], "DZ") !== false)
		{
			$eppo_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_eppo[0], "RA") !== false)
		{
			$eppo_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_eppo[0], "-RA") !== false)
		{
			$eppo_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_eppo[0], "+RA") !== false)
		{
			$eppo_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_eppo[0], "RA") == false || strpos($rekordy_linie_eppo[0], "DZ") == false)
		{
			$eppo_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_eppo[0], "SN") !== false)
		{
			$eppo_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_eppo[0], "GR") !== false)
		{
			$eppo_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_eppo[0], "-SN") !== false)
		{
			$eppo_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_eppo[0], "+SN") !== false)
		{
			$eppo_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_eppo[0], "SN") == false || strpos($rekordy_linie_eppo[0], "GR") == false)
		{
			$eppo_snow = "Brak opadów śniegu";
		}
		
		//...Dęblina...
		
		$f_rekordy_epde = @fopen($url_epde, "r");
		while (!feof($f_rekordy_epde))
		{
			$buffer = fgets($f_rekordy_epde);
			if(strpos($buffer, "TAF EPDE") !== FALSE)
				$rekordy_linie_epde[] = $buffer;
		}
		fclose($f_rekordy_epde);
		
		$epde_wind_knots = substr($rekordy_linie_epde[0], 58, 2);
		$epde_wind_kph = $epde_wind_knots * 0.539957;
		$epde_wind = "Prędkość wiatru: $epde_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_epde[0], "SKC") !== false || strpos($rekordy_linie_epde[0], "CAVOK") !== false)
		{
			$epde_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_epde[0], "OVC") !== false)
		{
			$epde_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_epde[0], "BKN") !== false)
		{
			$epde_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_epde[0], "SCT") !== false)
		{
			$epde_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_epde[0], "DZ") !== false)
		{
			$epde_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_epde[0], "RA") !== false)
		{
			$epde_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_epde[0], "-RA") !== false)
		{
			$epde_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_epde[0], "+RA") !== false)
		{
			$epde_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_epde[0], "RA") == false || strpos($rekordy_linie_epde[0], "DZ") == false)
		{
			$epde_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_epde[0], "SN") !== false)
		{
			$epde_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_epde[0], "GR") !== false)
		{
			$epde_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_epde[0], "-SN") !== false)
		{
			$epde_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_epde[0], "+SN") !== false)
		{
			$epde_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_epde[0], "SN") == false || strpos($rekordy_linie_epde[0], "GR") == false)
		{
			$epde_snow = "Brak opadów śniegu";
		}
		
		//...Radomia...
		
		$f_rekordy_epra = @fopen($url_epra, "r");
		while (!feof($f_rekordy_epra))
		{
			$buffer = fgets($f_rekordy_epra);
			if(strpos($buffer, "TAF EPRA") !== FALSE)
				$rekordy_linie_epra[] = $buffer;
		}
		fclose($f_rekordy_epra);
		
		$epra_wind_knots = substr($rekordy_linie_epra[0], 58, 2);
		$epra_wind_kph = $epra_wind_knots * 0.539957;
		$epra_wind = "Prędkość wiatru: $epra_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_epra[0], "SKC") !== false || strpos($rekordy_linie_epra[0], "CAVOK") !== false)
		{
			$epra_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_epra[0], "OVC") !== false)
		{
			$epra_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_epra[0], "BKN") !== false)
		{
			$epra_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_epra[0], "SCT") !== false)
		{
			$epra_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_epra[0], "DZ") !== false)
		{
			$epra_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_epra[0], "RA") !== false)
		{
			$epra_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_epra[0], "-RA") !== false)
		{
			$epra_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_epra[0], "+RA") !== false)
		{
			$epra_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_epra[0], "RA") == false || strpos($rekordy_linie_epra[0], "DZ") == false)
		{
			$epra_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_epra[0], "SN") !== false)
		{
			$epra_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_epra[0], "GR") !== false)
		{
			$epra_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_epra[0], "-SN") !== false)
		{
			$epra_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_epra[0], "+SN") !== false)
		{
			$epra_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_epra[0], "SN") == false || strpos($rekordy_linie_epra[0], "GR") == false)
		{
			$epra_snow = "Brak opadów śniegu";
		}
		//... i Gdańska.
		
		$f_rekordy_epgd = @fopen($url_epgd, "r");
		while (!feof($f_rekordy_epgd))
		{
			$buffer = fgets($f_rekordy_epgd);
			if(strpos($buffer, "TAF EPGD") !== FALSE)
				$rekordy_linie_epgd[] = $buffer;
		}
		fclose($f_rekordy_epgd);
		
		$epgd_wind_knots = substr($rekordy_linie_epgd[0], 58, 2);
		$epgd_wind_kph = $epgd_wind_knots * 0.539957;
		$epgd_wind = "Prędkość wiatru: $eppo_wind_kph kilometrów na godzinę";
		
		if (strpos($rekordy_linie_epgd[0], "SKC") !== false || strpos($rekordy_linie_epgd[0], "CAVOK") !== false)
		{
			$epgd_clouds = "Zachmurzenie: Brak / Niemal brak";
		}
		if (strpos($rekordy_linie_epgd[0], "OVC") !== false)
		{
			$epgd_clouds = "Zachmurzenie: Duże";
		}
		if (strpos($rekordy_linie_epgd[0], "BKN") !== false)
		{
			$epgd_clouds = "Zachmurzenie: Średnie";
		}
		if (strpos($rekordy_linie_epgd[0], "SCT") !== false)
		{
			$epgd_clouds = "Zachmurzenie: Niewielkie";
		}
		
		if (strpos($rekordy_linie_epgd[0], "DZ") !== false)
		{
			$epgd_rain = "Opady deszczu: Mżawka";
		}
		if (strpos($rekordy_linie_epgd[0], "RA") !== false)
		{
			$epgd_rain = "Opady deszczu: Średni deszcz";
		}
		if (strpos($rekordy_linie_epgd[0], "-RA") !== false)
		{
			$epgd_rain = "Opady deszczu: Lekki deszcz";
		}
		if (strpos($rekordy_linie_epgd[0], "+RA") !== false)
		{
			$epgd_rain = "Opady deszczu: Ciężki deszcz";
		}
		if (strpos($rekordy_linie_epgd[0], "RA") == false || strpos($rekordy_linie_epgd[0], "DZ") == false)
		{
			$epgd_rain = "Brak opadów deszczu";
		}
		
		if (strpos($rekordy_linie_epgd[0], "SN") !== false)
		{
			$epgd_snow = "Opady śniegu: Zwykły śnieg";
		}
		if (strpos($rekordy_linie_epgd[0], "GR") !== false)
		{
			$epgd_snow = "Opady gradu";
		}
		if (strpos($rekordy_linie_epgd[0], "-SN") !== false)
		{
			$epgd_snow = "Opady śniegu: Lekki śnieg";
		}
		if (strpos($rekordy_linie_epgd[0], "+SN") !== false)
		{
			$epgd_snow = "Opady śniegu: Ciężki śnieg";
		}
		if (strpos($rekordy_linie_epgd[0], "SN") == false || strpos($rekordy_linie_epgd[0], "GR") == false)
		{
			$epgd_snow = "Brak opadów śniegu";
		}

		
		
		echo '<br>';
		echo 'Można również wyszukać miejsce na świecie, wpisując w pole poniżej jego nazwę lub adres. ';
		echo 'Obecna data i godzina:       '. date('Y-m-d') . " " . date('H:i'). ".\n";
//		echo '. ';
		$place = $_POST['place'];
		if($place)
		{
			echo "Szukane miejsce to: $place";
		}
	?>
	<div id="floating-panel">
	<form action="" method="post">
	<input id="address" type="text" name="place">
	<button type="submit" name="button" formmethod="post">Szukaj</button>
	</form>
	</div>
    <div id="map"></div>
    <script>
	var geocoder;

	function initMap() 
	    {
	        var lublin = {lat: 51.248056, lng: 22.570278};
		    var eplb = {lat:51.240278, lng: 22.713611};
		    var epkk = {lat:50.077778, lng: 19.784722};
		    var epgd = {lat:54.3775, lng: 18.466111};
		    var epkt = {lat:50.474167, lng: 19.08};
		    var epwr = {lat:51.109444, lng: 16.880278};
		    var epwa = {lat:52.165833, lng: 20.967222};
    		var eppo = {lat:52.421111, lng: 16.826389};
		    var epra = {lat:51.389167, lng: 21.213611};
		    var epde = {lat:51.551111, lng: 21.891667};


	      var geocoder;
        geocoder = new google.maps.Geocoder();
        var map = new google.maps.Map(document.getElementById('map'), 
        {
          zoom: 6,
	        center: lublin,
	        styles: [
		                {featureType: 'poi.park', elementType: 'geometry', stylers: [{color: '#99CCBB'}]},
		                {featureType: 'poi.school', elementType: 'geometry', stylers: [{color: '#BBBBBB'}]},
		                {featureType: 'transit.line', elementType: 'geometry', stylers: [{color: '#999999'}]}
       		        ]
	        //mapTypeId: 'terrain'
        });
	
	
	var place = '<?php echo json_encode($place);?>'
	function address() 
  	{
    		geocoder.geocode
    		( 
      			{ 
        			'address': place
      			}
      			, function
      			(
        			results, status
      			) 
      			{
      				if (status == 'OK') 
      				{
	      				map.setCenter(results[0].geometry.location);
			      		map.setZoom(9);
        				var marker = new google.maps.Marker
        				(
          					{
            						map: map,
            						position: results[0].geometry.location
          					}
        				);
      				} 
      				else 
      				{
        				alert('Geocode was not successful for the following reason: ' + status);
      				}
    			}
		);
  	}
  	if('<?php echo json_encode($place);?>' != 'null') 
  	{
    		address();
  	}
  	//console.log('<?php echo json_encode($place);?>');

    	
    		//Zmienne w Javascripcie biorące dane z php:
    		var contentString_eplb = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Lublin</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Lublin (Świdnik)</b>, <b></b>,  ' +
    		  '<?php echo json_encode($rekordy_linie_eplb[0]); ?>'+
    		  '<?php echo json_encode($eplb_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($eplb_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($eplb_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($eplb_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    		  
    		  var contentString_epkk = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Kraków</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Międzynarodowy Port Lotniczy im. Jana Pawła II Kraków–Balice</b>' +
    		  '<?php echo json_encode($rekordy_linie_epkk[0]); ?>'+
    		  '<?php echo json_encode($epkk_wind); 
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epkk_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epkk_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epkk_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    		  
    		var contentString_epkt = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Katowice</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Międzynarodowy Port Lotniczy Katowice w Pyrzowicach</b>' +
    		  '<?php echo json_encode($rekordy_linie_epkt[0]); ?>'+
    		  '<?php echo json_encode($epkt_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epkt_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epkt_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epkt_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    		  
    		var contentString_epwr = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Wrocław</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Port lotniczy Wrocław-Strachowice</b>' +
    		  '<?php echo json_encode($rekordy_linie_epwr[0]); ?>'+
    		  '<?php echo json_encode($epwr_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epwr_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epwr_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epwr_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    		  
    		var contentString_epwa = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Warszawa</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Port lotniczy Warszawa-Okęcie (Lotnisko Chopina)</b>' +
    		  '<?php echo json_encode($rekordy_linie_epwa[0]); ?>'+
    		  '<?php echo json_encode($epwa_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epwa_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epwa_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epwa_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    		  
    		var contentString_eppo = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Poznań</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Port Lotniczy Poznań-Ławica</b>' +
    		  '<?php echo json_encode($rekordy_linie_eppo[0]); ?>'+
    		  '<?php echo json_encode($eppo_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($eppo_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($eppo_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($eppo_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    		  
    		var contentString_epgd = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Gdańsk</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Port Lotniczy Gdańsk im. Lecha Wałęsy</b>' +
    		  '<?php echo json_encode($rekordy_linie_epgd[0]); ?>'+
    		  '<?php echo json_encode($epgd_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epgd_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epgd_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epgd_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    
    		var contentString_epra = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Radom</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Port Lotniczy Radom-Sadków</b>' +
    		  '<?php echo json_encode($rekordy_linie_epra[0]); ?>'+
    		  '<?php echo json_encode($epra_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epra_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epra_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epra_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    
    		var contentString_epde = '<div id="content">'+
    		  '<h1 id="firstHeading" class="firstHeading">Dęblin</h1>'+
    		  '<div id="bodyContent">'+
    		  '<p><b>Lotnisko Dęblin-Irena</b>' +
    		  '<?php echo json_encode($rekordy_linie_epde[0]); ?>'+
    		  '<?php echo json_encode($epde_wind);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epde_clouds);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epde_rain);  
    					echo "<br>";?>\n'+
    		  '<?php echo json_encode($epde_snow);  
    					echo "<br>";?>\n</p>'+
    		  '<p>Za: Ogimet'+
    		  '</a></p> '+
    		  '</div>'+
    		  '</div>';
    
    
    
    		var infowindow_eplb = new google.maps.InfoWindow
			  ({
    		  	content: contentString_eplb
    		  });
    		
    		var infowindow_epkk = new google.maps.InfoWindow
			  ({
    		  	content: contentString_epkk
    		  });
    		  
    		var infowindow_epkt = new google.maps.InfoWindow
			({
      			content: contentString_epkt
    	    });
    		  
    		var infowindow_epwr = new google.maps.InfoWindow
			  ({
    	  		content: contentString_epwr
    		  });
    
    		var infowindow_epwa = new google.maps.InfoWindow
			  ({
    	  		content: contentString_epwa
    		  });
    		  
    		var infowindow_eppo = new google.maps.InfoWindow
			  ({
    		  	content: contentString_eppo
    		  });
    		
    		var infowindow_epgd = new google.maps.InfoWindow
			  ({
    	  		content: contentString_epgd
    		  });
    		 
    		var infowindow_epra = new google.maps.InfoWindow
			  ({
    		  	content: contentString_epra
    		  });
    		  
    		var infowindow_epde = new google.maps.InfoWindow
			  ({
    		  	content: contentString_epde
    		  });
    		    
    		//Stworzenie znaczników miejscowości/lotnisk:
    		
    		var marker_eplb = new google.maps.Marker
          ({
    	      position: eplb,
            map: map
          });
    		marker_eplb.addListener('click', function() 
			  {
    		  	infowindow_eplb.open(map, marker_eplb);
    		  });
    		  
    		var marker_epkk = new google.maps.Marker
            ({
              position: epkk,
              map: map
            });
    		marker_epkk.addListener('click', function() 
			  {
    	  		infowindow_epkk.open(map, marker_epkk);
    		  });
    		
    		var marker_epgd = new google.maps.Marker
            ({
              position: epgd,
              map: map
            });
    		marker_epgd.addListener('click', function() 
			  {
    	  		infowindow_epgd.open(map, marker_epgd);
    		  });
    		
    		var marker_epkt = new google.maps.Marker
            ({
              position: epkt,
              map: map
            });
    		marker_epkt.addListener('click', function() 
			  {
      			infowindow_epkt.open(map, marker_epkt);
    		  });
    
    		var marker_epwr = new google.maps.Marker
            ({
              position: epwr,
              map: map
            });
    		marker_epwr.addListener('click', function() 
			  {
    	  		infowindow_epwr.open(map, marker_epwr);
    		  });
    		
    		var marker_epwa = new google.maps.Marker
            ({
              position: epwa,
              map: map
            });
    		marker_epwa.addListener('click', function() 
			  {
    	  		infowindow_epwa.open(map, marker_epwa);
    		  });
    
    		var marker_eppo = new google.maps.Marker
            ({
              position: eppo,
              map: map
            });
    		marker_eppo.addListener('click', function() 
			  {
      			infowindow_eppo.open(map, marker_eppo);
    		  });
      	var marker_epra = new google.maps.Marker
           ({
             position: epra,
             map: map
           });
    		marker_epra.addListener('click', function() 
			  {
    	  		infowindow_epra.open(map, marker_epra);
    		  });
      	var marker_epde = new google.maps.Marker
          ({
            position: epde,
            map: map
          });
    		marker_epde.addListener('click', function() 
			  {
    			  infowindow_epde.open(map, marker_epde);
    		  });
	   }
    </script>
    <script async defer
	<?php $keyfile = fopen("key.txt", "r") or die("Error while opening file");?>
    src="https://maps.googleapis.com/maps/api/js?key=<?php 
	echo fread($keyfile,filesize("key.txt")); 
	fclose($keyfile);?>&callback=initMap">
    </script>
	<hr>
	This Maps API Implementation is still in development and testing phase. <br>
	By using this Maps API Implementation you agree to be bound by <a href="https://developers.google.com/maps/terms">Google's Terms of Service.</a> <br>
	<a href="https://www.google.com/policies/privacy/">Google's Privacy Policy</a>
  </body>
</html>

