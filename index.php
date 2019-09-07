<?php
  

    
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); 

    $sura = $_REQUEST['sura'];
    
    $quranFile = 'quran-simple.txt';   // quran file
    $transFile = 'trans-english.txt';  // translation file
    $metadataFile = 'quran-data.xml';  // quran metadata file

    initSuraData();   // initialize sura data array
    

    //------------------ General Functions ---------------------


    // initialize sura data array
    function initSuraData()
    {
        global $suraData, $metadataFile;
        $dataItems = Array("index", "start", "ayas", "name", "tname", "ename", "type", "rukus");

        $quranData = file_get_contents($metadataFile);
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, $quranData, $values, $index);
        xml_parser_free($parser);

        for ($i=1; $i<=114; $i++) 
        {
            $j = $index['SURA'][$i-1];
            foreach ($dataItems as $item)
                $suraData[$i][$item] = $values[$j]['attributes'][strtoupper($item)]; 
        }
    }


    // return given property of a sura
    function getSuraData($sura, $property) 
    {
        global $suraData;
        return $suraData[$sura][$property]; 
    }


    // return contents of a sura
    function getSuraContents($sura, $file) 
    {
        $text = file($file);
        $startAya = getSuraData($sura, 'start');
        $endAya = $startAya+ getSuraData($sura, 'ayas');
        $content = array_slice($text, $startAya, $endAya- $startAya); 
        return $content;
    }


    //------------------ Display Functions ---------------------

    
    if ($sura < 1) $sura = 1; 
    if ($sura > 114) $sura = 114; 


    // show sura contents
    function showSura($sura)
    {
        global $quranFile, $transFile;
        $suraName = getSuraData($sura, 'tname');
        $suraText = getSuraContents($sura, $quranFile);
        $transText = getSuraContents($sura, $transFile);
          $showBismillah = false; // change to true to show Bismillahs
        $ayaNum = 1; 

        
        echo "<div class=suraName> $suraName</div>";
		echo'<br>';
		
		echo '<p class=arabic>'.mb_strtolower('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ').'</p>';
				
        foreach ($suraText as $aya)
        {
            $trans = $transText[$ayaNum- 1];
            // remove bismillahs, except for suras 1 and 9
            if (!$showBismillah && $ayaNum == 1 && $sura !=1 && $sura !=9)
				
                $aya = preg_replace('/^(([^ ]+ ){4})/u', '', $aya);
            // display waqf marks in different style
            $aya = preg_replace('/ ([ۖ-۩])/u', '<span class="sign">&nbsp;$1</span>', $aya);
			
            echo "<div class=aya>";
			echo"<br>";
            echo "<div class=quran><span class=ayaNum>$ayaNum. </span>$aya</div>";
			echo '<p class ="latin1">SAHIH INTERNATIONAL </p><br>';
            echo "<div class=trans>$trans </div>";
			echo"<br>";
			echo"<br>";

			echo "</div>";


            $ayaNum++;
        }
    }
    

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title> Show Sura <?php echo $sura ?> </title>
</head>

<style>
main, nav, section {
  display: block;
}

body {
  margin: 0px;
  padding: 0px;
  font-size: 1rem;
  color: #212529;
  height:auto;
  background-color: #F5FFFA;
  text-decoration:none;
  overflow-x: hidden;
}
@font-face {
   font-family: 'noorehuda';
   src: url('noorehuda.ttf'); 
} 
@font-face {
   font-family: 'quran';
   src: url('quran.ttf'); 
} 

    .suraName {text-align: center; font-size: 60px; padding: 10px 0px;
        border: 1px solid #000; background-color: #fff; margin-top: 7px;}
  
	.aya {background-color: #fff; border: 2px solid black; border-top: 0px;}
    .quran {color:#000	;
     font-family:noorehuda; 
	font-size: 90px;
    direction:rtl;
	padding : 0 15px;
	padding-bottom:5px;
	padding:10px;
	margin : 0;
	
}
	.latin1 {
	color:#2E8B57;
	font-family:'Arial Black';
	font-size: 36px; font-weight: bold;
	text-align:left;
	direction:ltr;
	padding : 0 15px;
	margin : 0;
}
.arabic {text-align: center; font-size: 60px;
        border: 1px solid #000; background-color: #fff; padding : 45px;margin : 0;font-family:quran;}
	.trans { color:#222	;font-size: 46px; text-align:left;direction:ltr; background-color: #fff;padding : 0 15px;margin : 0;}
    .ayaNum {color: green; font-size: 90px;direction:ltr;
	padding : 0 15px;
	padding-bottom:5px;
	padding:10px;
	margin : 0;}
    .sign {font-family: times new roman; font-size: 0.9em; color: #FB7600;}
    .footer {text-align: center; margin: 20px 0px; color: #222; font-family: Arial;
        background-color: #f4f4ff; border: 1px solid #ccd; padding: 3px; font: 12px Verdana;}
		.inner-triangle{
    border-left: 20px solid transparent;
    border-right: 20px solid green;
    border-bottom: 20px solid transparent;
    height: 0;
    width: 0;
    position: absolute;
    right: 0px;
    z-index: 2;
}
	select{
		font-size:48px;
	}
</style>

<body>

<br>
<br>

		

   <form method="post" action='' id='surat'<?php echo $_SERVER['PHP_SELF']; ?>">
 			 <select name='sura' value='<?php echo $sura ?>'id='sura' onchange='submitForm();'style='height:100px;width: 480px;' size='1'>

            <option value="#" selected="selected">Select Surah</option><option value="1">1. Al-Fatiah [6]</option><option value="2">2. Al-Baqara [286]</option><option value="3">3. Aal-e-Imran [200]</option><option value="4">4. An-Nisa [176]</option><option value="5">5. Al-Maeda [120]</option><option value="6">6. Al-Anaam [165]</option><option value="7">7. Al-Araf [206]</option><option value="8">8. Al-Anfal [75]</option><option value="9">9. At-Taubah [129]</option><option value="10">10. Yunus [109]</option><option value="11">11. Hud [123]</option><option value="12">12. Yusuf [111]</option><option value="13">13. Ar-Rad [43]</option><option value="14">14. Ibrahim [52]</option><option value="15">15. Al-Hijr [99]</option><option value="16">16. An-Nahl [128]</option><option value="17">17. Al-Isra [111]</option><option value="18">18. Al-Kahf [110]</option><option value="19">19. Maryam [98]</option><option value="20">20. Taha [135]</option><option value="21">21. Al-Anbiya [112]</option><option value="22">22. Al-Hajj [78]</option><option value="23">23. Al-Mumenoon [118]</option><option value="24">24. An-Noor [64]</option><option value="25">25. Al-Furqan [77]</option><option value="26">26. Ash-Shuara [227]</option><option value="27">27. An-Naml [93]</option><option value="28">28. Al-Qasas [88]</option><option value="29">29. Al-Ankaboot [69]</option><option value="30">30. Ar-Room [60]</option><option value="31">31. Luqman [34]</option><option value="32">32. As-Sajda [30]</option><option value="33">33. Al-Ahzab [73]</option><option value="34">34. Saba [54]</option><option value="35">35. Fatir [45]</option><option value="36">36. Ya Seen [83]</option><option value="37">37. As-Saaffat [182]</option><option value="38">38. Sad [88]</option><option value="39">39. Az-Zumar [75]</option><option value="40">40. Ghafir [85]</option><option value="41">41. Fussilat [54]</option><option value="42">42. Ash-Shura [53]</option><option value="43">43. Az-Zukhruf [89]</option><option value="44">44. Ad-Dukhan [59]</option><option value="45">45. Al-Jathiya [37]</option><option value="46">46. Al-Ahqaf [35]</option><option value="47">47. Muhammad [38]</option><option value="48">48. Al-Fath [29]</option><option value="49">49. Al-Hujraat [18]</option><option value="50">50. Qaf [45]</option><option value="51">51. Adh-Dhariyat [60]</option><option value="52">52. At-tur [49]</option><option value="53">53. An-Najm [62]</option><option value="54">54. Al-Qamar [55]</option><option value="55">55. Al-Rahman [78]</option><option value="56">56. Al-Waqia [96]</option><option value="57">57. Al-Hadid [29]</option><option value="58">58. Al-Mujadila [22]</option><option value="59">59. Al-Hashr [24]</option><option value="60">60. Al-Mumtahina [13]</option><option value="61">61. As-Saff [14]</option><option value="62">62. Al-Jumua [11]</option><option value="63">63. Al-Munafiqoon [11]</option><option value="64">64. At-Taghabun [18]</option><option value="65">65. At-Talaq [12]</option><option value="66">66. At-Tahrim [12]</option><option value="67">67. Al-Mulk [30]</option><option value="68">68. Al-Qalam [52]</option><option value="69">69. Al-Haaqqa [52]</option><option value="70">70. Al-Maarij [44]</option><option value="71">71. Nooh [28]</option><option value="72">72. Al-Jinn [28]</option><option value="73">73. Al-Muzzammil [20]</option><option value="74">74. Al-Muddathir [56]</option><option value="75">75. Al-Qiyama [40]</option><option value="76">76. Al-Insan [31]</option><option value="77">77. Al-Mursalat [50]</option><option value="78">78. An-Naba [40]</option><option value="79">79. An-Naziat [46]</option><option value="80">80. Abasa [42]</option><option value="81">81. At-Takwir [29]</option><option value="82">82. AL-Infitar [19]</option><option value="83">83. Al-Mutaffifin [36]</option><option value="84">84. Al-Inshiqaq [25]</option><option value="85">85. Al-Burooj [22]</option><option value="86">86. At-Tariq [17]</option><option value="87">87. Al-Ala [19]</option><option value="88">88. Al-Ghashiya [26]</option><option value="89">89. Al-Fajr [30]</option><option value="90">90. Al-Balad [20]</option><option value="91">91. Ash-Shams [15]</option><option value="92">92. Al-Lail [21]</option><option value="93">93. Ad-Dhuha [11]</option><option value="94">94. Al-Inshirah [8]</option><option value="95">95. At-Tin [8]</option><option value="96">96. Al-Alaq [19]</option><option value="97">97. Al-Qadr [5]</option><option value="98">98. Al-Bayyina [8]</option><option value="99">99. Al-Zalzala [8]</option><option value="100">100. Al-Adiyat [11]</option><option value="101">101. Al-Qaria [11]</option><option value="102">102. At-Takathur [8]</option><option value="103">103. Al-Asr [3]</option><option value="104">104. Al-Humaza [9]</option><option value="105">105. Al-fil [5]</option><option value="106">106. Quraish [4]</option><option value="107">107. Al-Maun [7]</option><option value="108">108. Al-Kauther [3]</option><option value="109">109. Al-Kafiroon [6]</option><option value="110">110. An-Nasr [3]</option><option value="111">111. Al-Masadd [5]</option><option value="112">112. Al-Ikhlas [4]</option><option value="113">113. Al-Falaq [5]</option><option value="114">114. An-Nas [6]</option>              
			</select>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="select-translation" onchange="location = this.value;"style="height:100px;width: 380px;" size="1">
   <option value="#">Select Lang</option>
  <option value="index.php">English</option>
 <option value="hindi.php">Hindi</option>
  <option value="urdu.php">Urdu</option>
</select>
</form>		
<br>
<br>




<script type='text/javascript'>
		function submitForm(){
		document.getElementById('surat').submit();
			
		}
		</script>
<?php
    showSura($sura); 
?>

</body>