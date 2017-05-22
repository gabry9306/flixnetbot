<?php 

define("BOT_TOKEN", "369850827:AAGQjHVeEF9RwNK51OpyC2vkvzq5MZHoXV4");

$botToken = "369850827:AAGQjHVeEF9RwNK51OpyC2vkvzq5MZHoXV4";
$website = "https://api.telegram.org/bot".$botToken;
 
$content = file_get_contents('php://input');
$update = json_decode($content, TRUE);
 
$msg = $update["message"]["text"];
$msg_id = $update["message"]["id"];
$nome = $update["message"]["chat"]["first_name"];
$cognome = $update["message"]["chat"]["last_name"];
$username = $update["message"]["chat"]["username"]; 

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";

$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";

$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";
$longitudine = isset($message['location']['longitude']) ? $message['location']['longitude'] : "";
$latitudine = isset($message['location']['latitude']) ? $message['location']['latitude'] : "";

$callback_data = $update['callback_query']['data'];

$callback_message_id = $update['callback_data']['message']['chat']['id'];
$callback_user_id = $update['callback_data']['from']['id'];


// *********************************************** FUNZIONI ******************************************** //

function sendMessage($chatId,$message)
{
	$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message;
	file_get_contents($url);

}

function TastieraMenuPrincipale($chatId,$message)
{

	$tastiera = '&reply_markup={"keyboard":[["🔎 CERCA SERIE"],["NEWS","INFO BOT"]],"resize_keyboard":true}';
	$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message.$tastiera;
	file_get_contents($url);

}

function TastieraErrore($chatId)
{
	$message = "Non ho capito, riprova!";
	$tastiera = '&reply_markup={"keyboard":[["HELP"]],"resize_keyboard":true}';
	$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message.$tastiera;
	file_get_contents($url);

}

function Typing($chatId)
{
	$url = $GLOBALS[website].'/sendChatAction?chat_id='.$chatId.'&parse_mode=HTML&action=typing';
	file_get_contents($url);
}

function timestamp_to_date($timestamp){
   return date("r", $timestamp);
}

function GetSerie($chatId,$title)
{

		$content_imdb = file_get_contents('http://api.myapifilms.com/tvdb/searchSeries?title=Breaking+Bad&token=11e08191-2011-4679-9fb6-caaaef23eae7&format=json&language=it');
		$update_1 = json_decode($content_imdb, TRUE);
	
		$title_film = $update_1["object"]["data"]["0"]["seriesName"];
		$year_film = $update_1["object"]["data"]["0"]["firstAired"];
		$network = $update_1["object"]["data"]["0"]["network"];
		$regista = $update_1["object"]["data"]["0"]["directors"]["0"]["name"];
		$genere = $update_1["object"]["data"]["0"]["genres"]["0"];
		$durata = $update_1["object"]["data"]["0"]["runtime"];
		$premi = $update_1["object"]["data"]["0"]["awards"]["0"][""];
		$trailer = $update_1["object"]["data"]["0"]["trailer"]["videoURL"];
		$trama = $update_1["object"]["data"]["0"]["overview"];
		$locandina = $update_1["object"]["data"]["0"]["banner"];

		/*$content_yadex = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20170520T205327Z.87b5aa9c5b1a21ee.578062198537d96ec63800ae1d0292d6911ee90f&text='.$trama.'&lang=it&options=1');
		$update_2 = json_decode($content_yadex, TRUE)

		$text_traslate = $update_2["text"]["0"];*/

		for ($x = 0; $x <= 10; $x++) 
		{
			$premi[$x] = $update_1["object"]["0"]["data"]["movies"]["0"]["awards"][$x]["titlesAwards"]["0"]["titleAwardOutcome"];
			$array_premi = implode(', ', $premi);

			if($premi[$x] == "")
			{
				break;
			}  
		} 

		$date_film = timestamp_to_date($date_film);

		$message1 = "<b>Titolo Film:</b>%0A".$title_film."%0A %0A"."<b>Prima apparizione:</b>%0A".$year_film."%0A %0A"."<b>Produttore:</b>%0A".$network."%0A %0A"."<b>Durata:</b>%0A".$durata."%0A %0A"."<b>Trama:</b>%0A".$trama;

		$tastiera_1 = '&reply_markup={"inline_keyboard":[[{"text":"TRAILER","url":"'.$trailer.'"}]]}';
		$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message1.$tastiera_1;
		file_get_contents($url);

		$url = $GLOBALS[website].'/sendPhoto?chat_id='.$chatId.'&parse_mode=HTML&photo='.$locandina;
		file_get_contents($url);

}


// ***************************************************************************************************************************** //


date_default_timezone_set('Europe/Rome');
$today_date = date("d-m-Y");
$today_hour = date("H:i:s");


$text = trim($text);
$text = strtolower($text);
header("Content-Type: application/json");

switch($text)
{
  case "/start":

		{
			Typing($chatId);
		
			$botUrl = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendSticker";
			$postFields = array('chat_id' => $chatId, 
								'sticker' => 'CAADBAADVAADQr7mAQABvI1Juz6pgAI'); // 
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
			curl_setopt($ch, CURLOPT_URL, $botUrl); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			
			$output = curl_exec($ch);

			TastieraMenuPrincipale($chatId,"\xF0\x9F\x91\x8B Ciao $firstname (@$username), benvenuto in SerieDbBot!%0A %0A<b>Interegisci con me attraverso la pulsantiera sotto</b> 💬");
		}

    break;

  case "INFO BOT":

	{
		Typing($chatId);
	    TastieraMenuPrincipale($chatId,"In <b>SerieDbBot</b> troverai tutte le info sulle Serie Tv che cerchi%0A %0A<b>CREDITS:</b>%0A %0AIl Bot è stato ideato da <b>Gabriele Dell'Aria</b> (@gabrieledellaria) e sviluppato sfruttando le API fornite da MyApi");
	}

    break;

  case "info bot":

	{
		Typing($chatId);
	    TastieraMenuPrincipale($chatId,"In <b>SerieDbBot</b> troverai tutte le info sulle Serie Tv che cerchi%0A %0A<b>CREDITS:</b>%0A %0AIl Bot è stato ideato da <b>Gabriele Dell'Aria</b> (@gabrieledellaria) e sviluppato sfruttando le API fornite da MyApi");
	}

    break; 

  case "NEWS":

  	{
  		Typing($chatId);
    	TastieraMenuPrincipale($chatId,"Per restare sempre aggiornato sulle news cinematografiche segui il Canale Telegram @CinePassionCh");
    }
    
    break;

  case "🔎 CERCA SERIE":

  	{
  		Typing($chatId);
    	sendMessage($chatId,"<b>Qual'è il titolo della Serie Netflix che cerchi?</b>%0A %0ASe il titolo contiene spazi scrivere secondo la seguente sintassi%0A %0AES. Master+of+none");

    	break;
	}
    
  case "🔎 cerca serie":

  	{
  		Typing($chatId);
    	sendMessage($chatId,"<b>Qual'è il titolo della Serie Netflix che cerchi?</b>%0A %0ASe il titolo contiene spazi scrivere secondo la seguente sintassi%0A %0AES. Master+of+none");

    	break;
	} 

  case "news":
    
    {
    	Typing($chatId);
    	TastieraMenuPrincipale($chatId,"Per restare sempre aggiornato sulle news cinematografiche segui il Canale Telegram @CinePassionCh");
    }
    
    break;

  case "master":
    
    {
    	Typing($chatId);
   
    	$text_in = "Master+of+none";
		GetSerie($chatId,$text_in);
		break;  
	}

  case "help":

	  	{	
	  		Typing($chatId);

			TastieraMenuPrincipale($chatId,"Per usare il Bot utilizza la tastiera sotto per accedere alla sezione che ti interessa");
		}

  	break;  

  case "HELP":

	  	{	
	  		Typing($chatId);

			TastieraMenuPrincipale($chatId,"Per usare il Bot utilizza la tastiera sotto per accedere alla sezione che ti interessa");
		}

  	break;  	

  default:

    {
    	Typing($chatId);

		TastieraErrore($chatId); 
	}

	break;

}
