<?php 

define("BOT_TOKEN", "369850827:AAGQjHVeEF9RwNK51OpyC2vkvzq5MZHoXV4");

$botToken = "369850827:AAGQjHVeEF9RwNK51OpyC2vkvzq5MZHoXV4";
$website = "https://api.telegram.org/bot".$botToken;
 
$content = file_get_contents('php://input');
$update = json_decode($content, TRUE);

$fHandle=fopen('mioLog.txt','w');
fwrite($fHandle,$content);
 
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

$pagamento_id = $update['pre_checkout_query']['id'];
$pagamento_user = $update['pre_checkout_query']['from']['id'];
$pagamento_valuta = $update['pre_checkout_query']['currency'];
$pagamento_costo = $update['pre_checkout_query']['total_amount'];
$pagamento_payload = $update['pre_checkout_query']['invoice_payload'];

$shipping_id = $update['shipping_query']['id'];
$shipping_user = $update['shipping_query']['from'];
$shipping_payload = $update['shipping_query']['invoice_payload'];
$shipping_address = $update['shipping_query']['shipping_address'];

fwrite($fHandle,"\n\nPostField inviato a telegram:\n".JSON_ENCODE($postField_inline)."\n");


$handle=curl_init();
curl_setopt($handle,CURLOPT_URL,"https://api.telegram.org/bot$botToken/$method");
curl_setopt($handle,CURLOPT_HTTPHEADER,array('Content-type: application/json'));
curl_setopt($handle,CURLOPT_POST,1);
curl_setopt($handle,CURLOPT_POSTFIELDS,JSON_ENCODE($postField_inline));
curl_setopt($handle,CURLOPT_RETURNTRANSFER,1);
curl_setopt($handle,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($handle,CURLOPT_ENCODING,1);
$dati=json_decode( curl_exec($handle) ,true);		

curl_close($handle);

fwrite($fHandle,"\n\nRisposta ricevuta da telegram:\n$dati");

fclose($fHandle);


// *********************************************** FUNZIONI ******************************************** //

function sendMessage($chatId,$message)
{
	$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message;
	file_get_contents($url);

}

function TastieraMenuPrincipale($chatId,$message)
{

	$tastiera = '&reply_markup={"keyboard":[["🔎 CERCA SERIE"],["✳ AGGIORNAMENTI EPISODI","🎦 SERIE PIU\' POPOLARI"],["INFO BOT"]],"resize_keyboard":true}';
	$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message.$tastiera;
	file_get_contents($url);

}

function Pagamento($chatId)
{
	


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

		$title = str_replace_json(' ','+',$title);

		$content_imdb = file_get_contents('http://api.tvmaze.com/singlesearch/shows?q='.$title.'&embed=episodes');
		$update_1 = json_decode($content_imdb, TRUE);
	
		//$id_show = $update_1["id"];
		$title_serie = $update_1["name"];

		$genere_1 = $update_1["genres"]["0"];
		$genere_2 = $update_1["genres"]["1"];
		$genere_3 = $update_1["genres"]["2"];
		$durata = $update_1["runtime"];
		$id_imdb = $update_1["externals"]["imdb"];
		$link_imdb = "http://www.imdb.com/title/".$id_imdb."/";
		
		$locandina = $update_1["image"]["original"];

		$title_serie = str_replace_json(' ','+',$title_serie);

		// PRELEVO TRAILER 

			/*$content_trailer = file_get_contents('https://www.episodate.com/api/show-details?q='.$title_serie.'');
			$update_3 = json_decode($content_trailer, TRUE);

			$trailer_base = $update_3["tvShow"]["youtube_link"];
			$trailer = "www.youtube.com/watch?v=".$trailer_base."/";*/

			$content_trailer = file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet&q='.$title_serie.'official+trailer&key=AIzaSyAiMTE7edL3D-klp0y-nbtyyuv5IGLIlhU&maxResults=25');
			$update_3 = json_decode($content_trailer, TRUE);

			$trailer_base = $update_3["items"]["0"]["id"]["videoId"];
			$trailer = "www.youtube.com/watch?v=".$trailer_base."/";

		// PRELEVO TRAMA 

			$content_trama = file_get_contents('https://api.themoviedb.org/3/find/'.$id_imdb.'?api_key=89a238b8e3407a5052501a516009622a&language=it-IT&external_source=imdb_id');
			//$content_trama = file_get_contents('https://api.themoviedb.org/3/find/'.$id_imdb.'?api_key=89a238b8e3407a5052501a516009622a&language=it-IT&external_source=imdb_id');
			// prima era $content_trama = file_get_contents('https://tv-v2.api-fetch.website/show/'.$id_imdb.'');
			$update_4 = json_decode($content_trama, TRUE);

			$slash = "\"";
			$apo = "'";
			$slash_1 = "\ ";

			$trama = $update_4["tv_results"]["0"]["overview"];

			$trama = str_replace_json('\n',' ',$trama);
			$trama = str_replace_json('\r',' ',$trama);
			$trama = str_replace_json('<p>',' ',$trama);
			$trama = str_replace_json('</p>',' ',$trama);
			$trama = str_replace_json('<br>',' ',$trama);
			$trama = str_replace_json('<strong>',' ',$trama);
			$trama = str_replace_json('</strong>',' ',$trama);
			$trama = str_replace_json('[1]','',$trama);
			$trama = str_replace_json('[2]','',$trama);
			$trama = str_replace_json('.','. ',$trama);
			$trama = str_replace_json($slash_1,' ',$trama);


			$trama = "<b>Trama:</b>%0A".$trama;

			$date_serie = $update_4["tv_results"]["0"]["first_air_date"];
			$date_serie = date("d-m-Y", strtotime($date_serie));
			$date_serie = str_replace_json('-','/',$date_serie);

			$id_show = $update_4["tv_results"]["0"]["id"];
			$title_serie = $update_4["tv_results"]["0"]["original_name"];

			$content_produttore = file_get_contents('https://tv-v2.api-fetch.website/show/'.$id_imdb.'');
			$update_6 = json_decode($content_produttore, TRUE);

			$produttore = $update_6["network"];

		if ( $title_serie != "" ){

			if( $genere_1 != "" ){

				$genere = "".$genere_1;
			}

			if( $genere_2 != "" ){

				$genere = "".$genere_1."/".$genere_2;
			}

			if( $genere_3 != "" ){

				$genere = "".$genere_1."/".$genere_2."/".$genere_3;
			}

			if( $genere_1 == "" & $genere_2 == "" & $genere_3 == "" ){

				$genere = "Non disponibile";
			}


			if ( $produttore == "")
			{
				$produttore = "Non disponibile";
			}

			if ( $produttore == NULL)
			{
				$produttore = "Non disponibile";
			}

			if ( $date_serie == "")
			{
				$date_serie = "Non disponibile";
			}

			if ( $date_serie == NULL)
			{
				$date_serie = "Non disponibile";
			}

			if ( $durata == "")
			{
				$durata = "Non disponibile";
			}

			if ( $durata == NULL)
			{
				$durata = "Non disponibile";
			}

			if ( $trama == "<b>Trama:</b>%0A"){

				$trama = "";
			}

			$content_show_cast = file_get_contents('http://api.tvmaze.com/lookup/shows?imdb='.$id_imdb.'');
			$update_2 = json_decode($content_show_cast, TRUE);

			$rating = $update_2["rating"]["average"];
			$rating = "<b>Rating:</b>%0A".$rating."/10%0A %0A";

			if ( $rating == "<b>Rating:</b>%0A".""."/10%0A %0A"){

				$rating = "<b>Rating:</b>%0A"."Non disponibile"."%0A %0A";
			}

			$content_show_cast = file_get_contents('http://api-public.guidebox.com/v2/search?api_key=5eb82c2cbd1742d036de96361c14af3ef12bb4b5&type=show&field=title&query='.$title);
			$update_7 = json_decode($content_show_cast, TRUE);

			$id_guidebox = $update_7["results"]["0"]["id"];

			$content_show_cast = file_get_contents('http://api-public.guidebox.com/v2/shows/'.$id_guidebox.'?api_key=5eb82c2cbd1742d036de96361c14af3ef12bb4b5');
			$update_5 = json_decode($content_show_cast, TRUE);

			$cast = $update_5["cast"]["0"]["name"];
			$character_1 = $update_5["cast"]["0"]["character_name"];
			$cast_1 = $update_5["cast"]["1"]["name"];
			$character_2 = $update_5["cast"]["1"]["character_name"];
			$cast_2 = $update_5["cast"]["2"]["name"];
			$character_3 = $update_5["cast"]["2"]["character_name"];
			$cast_3 = $update_5["cast"]["3"]["name"];
			$cast_4 = $update_5["cast"]["4"]["name"];

			$casts = "".$cast." (".$character_1.")"." - ".$cast_1." (".$character_2.")"." - ".$cast_2." (".$character_3.")";

			if ( is_null($cast))
			{
				$casts = "".$cast_1."%0A".$cast_2;
			}

			if ( is_null($cast_1))
			{
				$casts = "".$cast."%0A".$cast_2;
			}

			if ( is_null($cast_2)){

				$casts = "".$cast."%0A".$cast_1;
			}

			if ( is_null($cast) & is_null($cast_1) & is_null($cast_2) ){

				$casts = "Non disponibile";
			}

			if ( $cast == "")
			{
				$casts = "".$cast_1."%0A".$cast_2;
			}

			if ( $cast_1 == "")
			{
				$casts = "".$cast."%0A".$cast_2;
			}

			if ( $cast_2 == "" ){

				$casts = "".$cast."%0A".$cast_1;
			}

			if ( $cast == "" & $cast_1 == "" & $cast_2 == "" ){

				$casts = "Non disponibile";
			}

			if ( $casts == "" ){

				$casts = "Non disponibile";
			}

			if ( is_null($casts) ){

				$casts = "Non disponibile";
			}

			if ( empty($casts) ){

				$casts = "Non disponibile";
			}

			if ( $casts == "0" ){

				$casts = "Non disponibile";
			}

			if ( $casts == 0 ){

				$casts = "Non disponibile";
			}

			if ( $casts == 0.0 ){

				$casts = "Non disponibile";
			}


			$message1 = "<b>Nome Serie:</b>%0A".$title_serie."%0A %0A"."<b>Genere:</b>%0A".$genere."%0A %0A"."<b>Data uscita 1° Episodio:</b>%0A".$date_serie."%0A %0A"."<b>Durata Media Episodio:</b>%0A".$durata." min %0A %0A".$rating."<b>Produttore:</b>%0A".$produttore."%0A %0A"."<b>Cast:</b>%0A".$casts;
			$message2 = $trama;

			if ( $trailer == "www.youtube.com/watch?v=/"){

				$tastiera_1 = '&reply_markup={"inline_keyboard":[[{"text":"MAGGIORI INFO","url":"'.$link_imdb.'"}]]}';
				$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message1.$tastiera_1;
				file_get_contents($url);
			} 

			else {

				$tastiera_1 = '&reply_markup={"inline_keyboard":[[{"text":"MAGGIORI INFO","url":"'.$link_imdb.'"},{"text":"TRAILER","url":"'.$trailer.'"}]]}';
				$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message1.$tastiera_1;
				file_get_contents($url);

				$url2 = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message2;
				file_get_contents($url2);

			}

			$url = $GLOBALS[website].'/sendPhoto?chat_id='.$chatId.'&parse_mode=HTML&photo='.$locandina;
			file_get_contents($url);

		}

		else {

			$message1 = "✴ Serie Tv non disponibile ... provare con un'altro nome!";
			$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message1;
			file_get_contents($url);

		}

		

}

function GetMostPopularSeries($chatId)
{

		$content_popular = file_get_contents('https://www.episodate.com/api/most-popular?page=1');
		$update_1 = json_decode($content_popular, TRUE);
	
		$name_show_1 = $update_1["tv_shows"]["0"]["name"];
		$name_show_2 = $update_1["tv_shows"]["1"]["name"];
		$name_show_3 = $update_1["tv_shows"]["2"]["name"];
		$name_show_4 = $update_1["tv_shows"]["3"]["name"];
		$name_show_5 = $update_1["tv_shows"]["4"]["name"];
		$name_show_6 = $update_1["tv_shows"]["5"]["name"];
		$name_show_7 = $update_1["tv_shows"]["6"]["name"];
		$name_show_8 = $update_1["tv_shows"]["7"]["name"];
		$name_show_9 = $update_1["tv_shows"]["8"]["name"];
		$name_show_10 = $update_1["tv_shows"]["9"]["name"];
		$name_show_11 = $update_1["tv_shows"]["10"]["name"];
		$name_show_12 = $update_1["tv_shows"]["11"]["name"];
		$name_show_13 = $update_1["tv_shows"]["12"]["name"];
		$name_show_14 = $update_1["tv_shows"]["13"]["name"];
		$name_show_15 = $update_1["tv_shows"]["14"]["name"];
		$name_show_16 = $update_1["tv_shows"]["15"]["name"];
		$name_show_17 = $update_1["tv_shows"]["16"]["name"];
		$name_show_18 = $update_1["tv_shows"]["17"]["name"];
		$name_show_19 = $update_1["tv_shows"]["18"]["name"];
		$name_show_20 = $update_1["tv_shows"]["19"]["name"];

				$message1 = "<b>Serie più popolari: </b>%0A %0A".$name_show_1."%0A %0A".$name_show_2."%0A %0A".$name_show_3."%0A %0A".$name_show_4."%0A %0A".$name_show_5."%0A %0A".$name_show_6."%0A %0A".$name_show_7."%0A %0A".$name_show_8."%0A %0A".$name_show_9."%0A %0A".$name_show_10."%0A %0A".$name_show_11."%0A %0A".$name_show_12."%0A %0A".$name_show_13."%0A %0A".$name_show_14."%0A %0A".$name_show_15."%0A %0A".$name_show_16."%0A %0A".$name_show_17."%0A %0A".$name_show_18."%0A %0A".$name_show_19."%0A %0A".$name_show_20."%0A %0A";

				$tastiera_1 = '&reply_markup={"inline_keyboard":[[{"text":"MAGGIORI INFO","url":"https://www.episodate.com/most-popular"}]]}';
				$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message1.$tastiera_1;
				file_get_contents($url);

				/*TastieraSeriePopolari($chatId,$name_show_1,$name_show_2,$name_show_3,$name_show_4,$name_show_5,$name_show_6,$name_show_7,$name_show_8,$name_show_9,$name_show_10,$name_show_11,$name_show_12,$name_show_13,$name_show_14,$name_show_15,$name_show_16,$name_show_17,$name_show_18,$name_show_19,$name_show_20);*/
		
}

function GetUpdatesSeries($chatId)
{

		$content_popular = file_get_contents('https://www.episodate.com/api/most-popular?page=1');
		$update_1 = json_decode($content_popular, TRUE);

				$message1 = "Per scoprire tutti gli episodi odierni e settimanali delle tue serie preferite visita il link sotto";

				$tastiera_1 = '&reply_markup={"inline_keyboard":[[{"text":"VAI A ...","url":"https://eurostreaming.club/aggiornamento-episodi/"}]]}';
				$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message1.$tastiera_1;
				file_get_contents($url);

				/*TastieraSeriePopolari($chatId,$name_show_1,$name_show_2,$name_show_3,$name_show_4,$name_show_5,$name_show_6,$name_show_7,$name_show_8,$name_show_9,$name_show_10,$name_show_11,$name_show_12,$name_show_13,$name_show_14,$name_show_15,$name_show_16,$name_show_17,$name_show_18,$name_show_19,$name_show_20);*/
		
}

function GetDiscoverSerie($chatId)
{

		$content_popular = file_get_contents('https://api.themoviedb.org/3/discover/tv?api_key=89a238b8e3407a5052501a516009622a&language=it-IT&sort_by=popularity.desc&page=1&timezone=America%2FNew_York&include_null_first_air_dates=false');
		$update_1 = json_decode($content_popular, TRUE);

				$message1 = "<b>Serie più popolari: </b>%0A %0A".$name_show_1."%0A %0A".$name_show_2."%0A %0A".$name_show_3."%0A %0A".$name_show_4."%0A %0A".$name_show_5."%0A %0A".$name_show_6."%0A %0A".$name_show_7."%0A %0A".$name_show_8."%0A %0A".$name_show_9."%0A %0A".$name_show_10."%0A %0A".$name_show_11."%0A %0A".$name_show_12."%0A %0A".$name_show_13."%0A %0A".$name_show_14."%0A %0A".$name_show_15."%0A %0A".$name_show_16."%0A %0A".$name_show_17."%0A %0A".$name_show_18."%0A %0A".$name_show_19."%0A %0A".$name_show_20."%0A %0A";

				//$tastiera_1 = '&reply_markup={"inline_keyboard":[[{"text":"MAGGIORI INFO","url":"https://www.episodate.com/most-popular"}]]}';
				$url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.$message1;//.$tastiera_1;
				file_get_contents($url);
		
}

function str_replace_json($search, $replace, $subject){ 
     return json_decode(str_replace($search, $replace,  json_encode($subject))); 

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
    	TastieraMenuPrincipale($chatId,"<b>Qual'è il titolo della Serie Netflix che cerchi?</b>%0A %0AES. Master+of+none");

    	break;
	}
    
  case "🔎 cerca serie":

  	{
  		Typing($chatId);
    	TastieraMenuPrincipale($chatId,"<b>Qual'è il titolo della Serie Netflix che cerchi?</b>%0A %0AES. Master+of+none");

    	break;
	}

  case "🎦 SERIE PIU' POPOLARI":

  	{
  		Typing($chatId);
    	GetMostPopularSeries($chatId);

    	break;
	}
    
  case "🎦 serie piu' popolari":

  	{
  		Typing($chatId);
    	GetMostPopularSeries($chatId);

    	break;

	} 

  case "✳ AGGIORNAMENTI EPISODI":

  	{
  		Typing($chatId);
    	GetUpdatesSeries($chatId);

    	break;
	}
    
  case "✳ aggiornamenti episodi":

  	{
  		Typing($chatId);
    	GetUpdatesSeries($chatId);

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
  case "paga":

	  	{	
	  		Typing($chatId);

			$stripe_token = "284685063:TEST:NzRhMGZjY2EyMjBl";

			$botToken = "369850827:AAGQjHVeEF9RwNK51OpyC2vkvzq5MZHoXV4";

			$content = file_get_contents('php://input');
			$update = json_decode($content, TRUE);

			$pagamento_id = $update['pre_checkout_query']['id'];
			$pagamento_user = $update['pre_checkout_query']['from']['id'];
			$pagamento_valuta = $update['pre_checkout_query']['currency'];
			$pagamento_costo = $update['pre_checkout_query']['total_amount'];
			$pagamento_payload = $update['pre_checkout_query']['invoice_payload'];

			$shipping_id = $update['shipping_query']['id'];
			$shipping_user = $update['shipping_query']['from'];
			$shipping_payload = $update['shipping_query']['invoice_payload'];
			$shipping_address = $update['shipping_query']['shipping_address'];
			
			$LabeledPrice = json_encode(array(array('label' => "Nike Shoes", 'amount' => 11000), array('label' => "Shipping", 'amount' => 2500)));

			$url = $GLOBALS[website].'/sendInvoice?chat_id='.$chatId.'&title=nike shoes&photo_url=https://at-cdn-s01.audiotool.com/2014/04/24/documents/CJdSUBI3TeFEiohdPJLsDgwF7Im5rOX/0/cover256x256-c73e8c1831fd4a78801487a2f6dc1de2.jpg&photo_width=50&photo_height=50&description=The best running shoes 2017'.' ID Pagamento: '.$pagamento_id.'&payload=flixnet-test-invoice&provider_token='.$stripe_token.'&start_parameter=pay&currency=EUR&prices='.$LabeledPrice.'';
			
			file_get_contents($url);

			// *************************** //

			$url2 = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text=ID Pagamento: '.$pagamento_id;
			file_get_contents($url2);

			$url3 = $GLOBALS[website].'/answerPreCheckoutQuery?pre_checkout_query_id='.$pagamento_id.'&ok=False&errorMessage=True';

			file_get_contents($url3);
		}

  	break;  

  case "PAGA":

	  	{	
	  		Typing($chatId);

			$stripe_token = "284685063:TEST:NzRhMGZjY2EyMjBl";

			$botToken = "369850827:AAGQjHVeEF9RwNK51OpyC2vkvzq5MZHoXV4";

			$content = file_get_contents('php://input');
			$update = json_decode($content, TRUE);

			$pagamento_id = $update['pre_checkout_query']['id'];
			$pagamento_user = $update['pre_checkout_query']['from']['id'];
			$pagamento_valuta = $update['pre_checkout_query']['currency'];
			$pagamento_costo = $update['pre_checkout_query']['total_amount'];
			$pagamento_payload = $update['pre_checkout_query']['invoice_payload'];

			$shipping_id = $update['shipping_query']['id'];
			$shipping_user = $update['shipping_query']['from'];
			$shipping_payload = $update['shipping_query']['invoice_payload'];
			$shipping_address = $update['shipping_query']['shipping_address'];
			
			$LabeledPrice = json_encode(array(array('label' => "Nike Shoes", 'amount' => 11000), array('label' => "Shipping", 'amount' => 2500)));

			$url = $GLOBALS[website].'/sendInvoice?chat_id='.$chatId.'&title=nike shoes&photo_url=https://at-cdn-s01.audiotool.com/2014/04/24/documents/CJdSUBI3TeFEiohdPJLsDgwF7Im5rOX/0/cover256x256-c73e8c1831fd4a78801487a2f6dc1de2.jpg&photo_width=50&photo_height=50&description=The best running shoes 2017'.' ID Pagamento: '.$pagamento_id.'&payload=flixnet-test-invoice&provider_token='.$stripe_token.'&start_parameter=pay&currency=EUR&prices='.$LabeledPrice.'';
			
			file_get_contents($url);

			// *************************** //

			$url3 = $GLOBALS[website].'/answerPreCheckoutQuery?pre_checkout_query_id='.$pagamento_id.'&ok=False&errorMessage=True';

			file_get_contents($url3);
		}

  	break;  				

  default:

    {
    	Typing($chatId);
   
    	$text_in = $msg;
		GetSerie($chatId,$text_in);
		break;

    	//Typing($chatId);
		//TastieraErrore($chatId); 
	}

	break;

}
