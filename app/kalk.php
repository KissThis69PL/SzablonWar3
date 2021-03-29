<?php
// KONTROLER strony kalkulatora
require_once dirname(__FILE__).'/../config.php';

// Kontroler podzielono na część definicji etapów (funkcje)
// oraz część wykonawczą, która te funkcje odpowiednio wywołuje.
// Na koniec wysłaniem odpowiedzi zajmie się odpowiedni widok.
// Parametry do widoku przekazujemy  przez zmienne.

//pobranie parametrów
function getParams(&$form){
	$form['x'] = isset($_REQUEST['x']) ? $_REQUEST['x'] : null;
	$form['y'] = isset($_REQUEST['y']) ? $_REQUEST['y'] : null;
	$form['op'] = isset($_REQUEST['op']) ? $_REQUEST['op'] : null;	
}

//walidacja parametrów z przygotowaniem zmiennych dla widoku
function validate(&$form,&$infos,&$msgs,&$hide_intro){

	//sprawdzenie, czy parametry zostały przekazane - jeśli nie to zakończ walidację
	if ( ! (isset($form['x']) && isset($form['y']) && isset($form['op']) ))	return false;	
	
	//parametry przekazane zatem
	//nie pokazuj wstępu strony gdy tryb obliczeń (aby nie trzeba było przesuwać)
	// - ta zmienna zostanie użyta w widoku aby nie wyświetlać całego bloku itro z tłem 
	$hide_intro = true;

	$infos [] = 'Przekazano parametry.';

	// sprawdzenie, czy potrzebne wartości zostały przekazane
	if ( $form['x'] == "") $msgs [] = 'Kwota nie została podana';
	if ( $form['y'] == "") $msgs [] = 'Czas spłaty kredytu nie został podany';
	
	//nie ma sensu walidować dalej gdy brak parametrów
	if ( count($msgs)==0 ) {
		// sprawdzenie, czy $x i $y są liczbami całkowitymi
		if (! is_numeric( $form['x'] )) $msgs [] = 'Proszę kwotę w PLN, używając liczby całkowitej.';
		if (! is_numeric( $form['y'] )) $msgs [] = 'Proszę podać czas spłaty w latach, używając liczby całkowitej.';
	}
	
	if (count($msgs)>0) return false;
	else return true;
}
	
// wykonaj obliczenia
function process(&$form,&$infos,&$msgs,&$result){
	$infos [] = 'Parametry sie zgadzają. Robię obliczenia.';
	
	//konwersja parametrów na int
	$form['x'] = floatval($form['x']);
	$form['y'] = floatval($form['y']);
	
	//wykonanie operacji
	switch ($form['op']) {
	case '4%' :
		$result = ($form['x'] / ($form['y'] * 12)) * 1.04;
		$form['op_name'] = '4%';
		break;
	case '9%' :
		$result = ($form['x'] / ($form['y'] * 12)) * 1.09;
		$form['op_name'] = '9%';
		break;
	case '15%' :
		$result = ($form['x'] / ($form['y'] * 12)) * 1.15;
		$form['op_name'] = '15%';
		break;
	default :
		$result = ($form['x'] / ($form['y'] * 12)) * 1.01;
		$form['op_name'] = '1%';
		break;
	}
}

//inicjacja zmiennych
$form = null;
$infos = array();
$messages = array();
$result = null;
//domyślnie pokazuj wstęp strony (tytuł i tło)
$hide_intro = false;
	
getParams($form);
if ( validate($form,$infos,$messages,$hide_intro) ){
	process($form,$infos,$messages,$result);
}

//Wywołanie widoku, wcześniej ustalenie zawartości zmiennych elementów szablonu
$page_title = 'Przykład nr 3';
$page_description = 'Prosty szablon oparty na widoku z dołączeniem kolejnych części HTML w plikach .php';
$page_header = '<p><font color=#FF7A2F>Prosty szablon</p>';
$page_footer = '<p> <font color=red>Stopka Storny</font> </p>';

include 'kalk_view.php';