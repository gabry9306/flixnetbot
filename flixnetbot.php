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

	$tastiera = '&reply_markup={"keyboard":[["🔎 CERCA SERIE"],["INFO BOT"]],"resize_keyboard":true}';
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

function TastieraInfo($chatId,$message)
{
	$tastiera = '&reply_markup={"inline_keyboard":[[{"text":"Gabriele Dell\'Aria","url":"http://t.me/gabrieledellaria"},{"text":"CinePassion","url":"http://cinepassion.blogsocial.it"}]]}';
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

		$content_imdb = file_get_contents('http://api.tvmaze.com/singlesearch/shows?q='.$title.'&embed=episodes');
		$update_1 = json_decode($content_imdb, TRUE);
	
		$id_show = $update_1["id"];
		$title_film = $update_1["name"];
		$date_serie = $update_1["premiered"];
		$genere_1 = $update_1["genres"]["0"];
		$genere_2 = $update_1["genres"]["1"];
		$genere_3 = $update_1["genres"]["2"];
		$durata = $update_1["runtime"];
		$produttore = $update_1["webChannel"]["name"];
		$id_imdb = $update_1["externals"]["imdb"];
		$link_imdb = "http://www.imdb.com/title/".$id_imdb."/";
		
		$locandina = $update_1["image"]["original"];

		if( $genere_1 != "" ){

			$genere = "".$genere_1;
		}

		if( $genere_2 != "" ){

			$genere = "".$genere_1."/%0A".$genere_2;
		}

		if( $genere_3 != "" ){

			$genere = "".$genere_1."/%0A".$genere_2."/%0A".$genere_3;
		}

		$content_show_cast = file_get_contents('http://api.tvmaze.com/shows/'.$id_show.'?&embed=cast');
		$update_2 = json_decode($content_show_cast, TRUE);

		$cast = $update_2["_embedded"]["cast"]["0"]["person"]["name"];
		$cast_2 = $update_2["_embedded"]["cast"]["1"]["person"]["name"];
		$cast_3 = $update_2["_embedded"]["cast"]["2"]["person"]["name"];
		$cast_4 = $update_2["_embedded"]["cast"]["3"]["person"]["name"];

		$content_trailer = file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet&q='.$title_film.'official+trailer&key=AIzaSyAiMTE7edL3D-klp0y-nbtyyuv5IGLIlhU&maxResults=25');
		$update_3 = json_decode($content_trailer, TRUE);

		$trailer_base = $update_3["items"]["0"]["id"]["videoId"];
		$trailer = "www.youtube.com/watch?v=".$trailer_base."/";


		$message1 = "<b>Nome Serie:</b>%0A".$title_film."%0A %0A"."<b>Genere:</b>%0A".$genere."%0A %0A"."<b>Data uscita 1° Episodio:</b>%0A".$date_serie."%0A %0A"."<b>Durata Media Episodio:</b>%0A".$durata."%0A %0A"."<b>Produttore:</b>%0A".$produttore."%0A %0A"."<b>Cast:</b>%0A".$cast.",%0A".$cast_2.",".$cast_3.",%0A".$cast_4."%0A..."."%0A %0A";

		$tastiera_1 = '&reply_markup={"inline_keyboard":[[{"text":"MAGGIORI INFO","url":"'.$link_imdb.'"},{"text":"TRAILER","url":"'.$trailer.'"}]]}';
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
	    TastieraInfo($chatId,"In <b>FlixNetBot</b> troverai tutte le info sulle Serie Tv che cerchi%0A %0A<b>CREDITS:</b>%0A %0AIl Bot è stato ideato da <b>Gabriele Dell'Aria</b> (@gabrieledellaria) e sviluppato sfruttando le API fornite da MyApi");
	}

    break;

  case "info bot":

	{
		Typing($chatId);
	    TastieraInfo($chatId,"In <b>FlixNetBot</b> troverai tutte le info sulle Serie Tv che cerchi%0A %0A<b>CREDITS:</b>%0A %0AIl Bot è stato ideato da <b>Gabriele Dell'Aria</b> (@gabrieledellaria)");
	}

    break; 

  case "🔎 CERCA SERIE":

  	{
  		Typing($chatId);
    	TastieraMenuPrincipale($chatId,"<b>Qual'è il titolo della Serie Netflix che cerchi?</b>%0A %0ASe il titolo contiene spazi scrivere secondo la seguente sintassi%0A %0AES. Master+of+none");

    	break;
	}
    
  case "🔎 cerca serie":

  	{
  		Typing($chatId);
    	TastieraMenuPrincipale($chatId,"<b>Qual'è il titolo della Serie Netflix che cerchi?</b>%0A %0ASe il titolo contiene spazi scrivere secondo la seguente sintassi%0A %0AES. Master+of+none");

    	break;
	} 

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
