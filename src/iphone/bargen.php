<?php 
//error_reporting(E_ALL);


include("lib.php");
$player = check_user($secret_key, $db);

include('barclass.php');

$bar = new barGen();	// Load the class
$bar->setWidth(150);	// Set the width
$bar->setHeight(12);	// Set the height
$bar->setFontSize(5);	// Set the font size
$bar->makeBar();		// Start the bar


if(isset($_REQUEST['exp']))
{
	$bar->setFillColor(184, 148, 1);
	$bar->setData($player->maxexp, $player->exp);
}

elseif(isset($_REQUEST['hp']))
{
	$bar->setFillColor(167, 3, 1);
	$bar->setData($player->maxhp, $player->hp);
}

elseif(isset($_REQUEST['mana']))
{
	$bar->setFillColor(9, 42, 83);
	$bar->setData($player->maxmana, $player->mana);
}

elseif(isset($_REQUEST['energy']))
{
	$bar->setFillColor(0, 81, 0);
	$bar->setData($player->maxenergy, $player->energy);
}
else 
{
	exit();
}

$bar->generateBar();

?>