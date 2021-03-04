<?
/**
 * @name        IssaParser
 * @author      pasich
 * @link        https://t.me/andriipasichnik
 */

 
 //settings
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


defined('pasichDev') or die('Доступ запрещено');

class core {

//обявим свойства
	public static $home = 'http://localhost/'; // Ссылка на ваш сайт (Нужно изменить, в конце оставить слеш)
  public static $description ='ff'; // description
  public static $keywords ='ff'; // keywords

/*--------------------------------
  Методы
--------------------------------*/
 public static function includ_parser(){
   include_once 'SHDparser.php'; }


 public static function file_check($url){// если файл рабоатет отдаст 1, если нет 0
  $run = 0;
  if (@fopen($url, "r"))  $run = '1';         
    return $run;
 }

 public static function art_check($art){
  $art_array = array("/",":"," ");
  return str_replace($art_array, "_", $art); //заменить спец символ на подчеркование
 }

 public static function url_check($url){ //проверка на существование ссылки
  $run = 'true';
  if(!file_get_contents($url)){ $run = "false";}
 }

 public static function get_size($bytes){
 if ($bytes<1000*1024){
  return number_format($bytes/1024,2)." KB";
  }elseif($bytes<1000*1048576){
  return number_format($bytes/1048576,2)." MB";
  } }


 public static function file_size($name) {
 if (file_exists($name)) $size = core::get_size(filesize($name));
 else $size = '';
 return $size; } //Создаем функцию для проверки существования файла и определения размера файла

 public static function deleteDirectory($dir) { 
  if (!file_exists($dir)) {  return true; }
  if (!is_dir($dir)) {  return unlink($dir);}
  foreach (scandir($dir) as $item) {
      if ($item == '.' || $item == '..') {
          continue; }
      if (!core::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
          return false; }
 }  return rmdir($dir); } //удвление каталога


 
 public static function addFileRecursion($zip, $dir, $start = '') {
   if (empty($start)) { $start = $dir; }
   if ($objs = glob($dir . '/*')) {
     foreach($objs as $obj) { 
       if (is_dir($obj)) {
         core::addFileRecursion($zip, $obj, $start); } else {
         $zip->addFile($obj, str_replace(dirname($start) . '/', '', $obj));
       } } } }
 } //рекрусивно добавить файлы в архив




