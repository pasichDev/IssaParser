<? 
/**
 * @name        IssaParser
 * @author      pasich
 * @link        https://t.me/andriipasichnik
 */
define('pasichDev', 1);


$root = '';
$start_time = microtime();
$start_array = explode(" ",$start_time);
$start_time = $start_array[1] + $start_array[0];


/*--------------------------------
  Автозагрузка класов
--------------------------------*/
spl_autoload_register(function ($class) {
    include  'class/' . $class . '.class.php';
});
 
$core = new core() or die('Error: Core System'); //загрузим ядро
unset($core);
core::includ_parser(); // вызвем библотеку парсера
ob_start();


/*--------------------------------
  Обявим системные переменные
--------------------------------*/
$home = core::$home;  //Ссылка на ваш сайт    
$get_url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : false;
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : false;
$description = core::$description;
$description = isset($description) ? $description : '';
$keywords = core::$keywords;
$keywords = isset($keywords) ? $keywords : '';
if($get_url ) $html = file_get_html($get_url); //получим ссылку

/*--------------------------------
  Шапка сайта
--------------------------------*/
echo '<!DOCTYPE html><html><head>
<link rel="stylesheet" href="src/bulma.css"> 
<link rel="stylesheet" href="src/style.css"> 
<script src="src/js.js" ></script> 
<link rel="apple-touch-icon" href="src/img/apple_icon.png" type="image/png"/>
<link rel="icon" href="favicon.ico" type="image/x-icon"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="'.$description.'" />
<meta name="keywords" content="'.$keywords.'" />
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
<title>IssaParser</title></head><body>
<div class="container">
<nav class="navbar is-dark" role="navigation" aria-label="main navigation"><div class="navbar-brand">
<div class="navbar-item"><a></a></div>
<div class="navbar-item"><a href="/">
<img class="image is-32x32" src="src/img/logo_issa.png"></a></div>
<a role="button" class="navbar-burger burger" aria-expanded="false" data-target="navbarBasicExample">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span></a> </div>
  <div id="navbarBasicExample" class="navbar-menu">
<div class="navbar-start">
</nav><div class="body">';


if($type!='downloadlist') //форма поиска
  echo '<form action="/index.php">
         <div class="field  is-grouped"><div class="control is-expanded">
           <input name="url" class="input  is-danger" type="text" placeholder="Введіть url" value="'.$get_url.'" required>
           <input name="type" class="input" type="hidden"  value="search"></div>
         <div class="control">
           <input class="button is-danger" type="submit" value="Відкрити">
            </div></div></form>';


/*--------------------------------
  Методы
--------------------------------*/

  if($get_url ){//если мы получили ссылку то покажем обьект
    $data = $html->find('.MagicZoom', 0); //получим клас картинки
    foreach($data->find('img') as $photo) //получим ссылку главной картиники
           $url = explode("/", $photo->src); //разобьем ссылку по слешу
           $url_back = $url[11]; //получим унмкальный индетификатор
           $art = $html->find('.col-xs-6, .code', 0)->plaintext; //получим art
           $art = core::art_check($art);
       echo '<nav class="panel is-dark  has-background-white"><p class="message-header">'.$art.'</p>'; //шапка
       for ($i = 0; $i <= 3; $i++) {//цикл фото для каждой обновим индентификатор
        $url_plus = $url_back + $i; //к главному индентифактору добавил +1
        $img = "https:".str_ireplace($url_back,$url_plus, $photo->src); //получим наши картинки с разними индетификаторами
        $zip_check = "false";

        if(core::file_check($img)=='1') //проверка ли существует картинка
        echo '<a class="panel-block is-active" href="'.$img.'">
            <figure class="image image is-32x32">
            <img src="'.$img.'"></figure>
            <span class="mx-2">'.$url_plus.'.jpg </span></a>'; //выведем картинки
    }
  
      foreach($html->find('source') as $video)//вывод видео
      if(core::file_check($video->src)=='1') 
      echo '<a class="panel-block" href="'.$video->src.'">
            <figure class="image image is-32x32"><img src="src/svg/film.svg"></figure>
           </span><span class="mx-2">video.mp4</span></a>';
  
     if(core::file_check("files/zip/$art.zip")=='0') $zip_check = "true"; //проверим ли существует архив

     if($zip_check=="true") {
        $temp_ss = "files/__temp/$art"; //путь для папки темп
       if(!file_exists($temp_ss)) mkdir($temp_ss, 0755, true); //создадим папку для наших фото
     
       for ($i = 0; $i <= 3; $i++) {//цикл фото для кождой обновим индентификатор
         $url_plus = $url_back + $i; //к главному индентифактору добавил +1
         $img = "https:".str_ireplace($url_back,$url_plus, $photo->src); //получим наши картинки с разними индетификаторами
         if(core::file_check($img) == '1'){//скачаем картинки на сервер
         $path = 'files/__temp/'.$art.'/'.$url_back.'.'.$url_plus.'.jpg';
         file_put_contents($path, file_get_contents($img)); }
       }
       if(core::file_check($video->src) == '1'){//скачаем video на сервер
         $path = 'files/__temp/'.$art.'/'.$url_back.'.'.$url_plus.'.mp4';
        file_put_contents($path, file_get_contents($video->src)); }
    
      $zip = new ZipArchive(); 
      $filename = "$art.zip"; //имя архива
      if ($zip->open("files/zip/".$filename, ZipArchive::CREATE)!==TRUE) { //создадим архив
         exit("Невозможно открыть <$filename>\n");}

      core::addFileRecursion($zip,"files/__temp/$art"); //добавим файлы в архив
      $zip->close();
      core::deleteDirectory("files/__temp/".$art); //удалим временую папку
       }



      echo '<div class="panel-block">
             <a class="button is-danger is-outlined is-fullwidth" href="files/zip/'.$art.'.zip">Завантажити медіа</a></div>
             <a class="panel-block is-active"><span class="panel-icon">
               <i class="fas fa-book" aria-hidden="true"></i>
           <figure class="image image is-8x8">
           <img src="src/svg/next.svg"></figure>
           </span><span class="inf has-text-grey">Завантаження може тривати до 1 хв. Очікуйте!</span></a></nav></div>';
           
    




  }elseif(!$get_url and $type=="search"){
      echo '<div class="notification is-danger">Ми не отримали від вас <b>URL</b>, через це завантаження контенту неможливе!</div>';

  }elseif(!$type and !$get_url){ //главная страница
    echo '<div class="message-header">Як користуватися?</div>
    <nav class="level box">
    <div class="level-item has-text-centered">
      <div>
        <p class="title">1</p>
        <p class="heading">Відкрити потрібний товар на <strong>IssaPlus</strong>,</br> та скопіювати URl</p>
      </div>
    </div>
    <div class="level-item has-text-centered">
      <div>
      <p class="title">2</p>
      <p class="heading">Перейти на наш сайт, вставити URL товару в відповідне поле,</br> та натиснути <strong>відкрити</strong></p>
      </div>
    </div>
    <div class="level-item has-text-centered">
      <div>
      <p class="title">3</p>
      <p class="heading">Перед вами зявиться відповідний каталог контенту,</br> натисніть <strong>заванатажити</strong> для того щоб зберегти медіа на свій пристрій </p>
      </div></div></nav>';

    $files = scandir('files/zip'); //откроем файл
    sort($files);  //Сортировка по названию (А, Б, В...)
    $files = array_diff_key($files, [0 => "", "1" => ""]); //удалим ненужные ключи

    echo '<article class="panel is-mobile is-dark has-background-white">
      <p class="message-header">Остані проекти</p>';  //шапка меню
     $i = '0'; foreach($files as $file){ //массыв с файлами
    echo "<a class='panel-block' href='files/zip/$file'>
        <figure class='image image is-32x32'><img src='src/svg/zip.svg'></figure> <span class='mx-2'>$file 
        <span class='is-size-7 has-text-weight-bold'>(".core::file_size("files/zip/$file").")</span></span></a>";    ++$i;
        if($i=="5") break; }
     echo '</article>';  }

 
  



     









//страница сгенерирована за
$end_time = microtime();
$end_array = explode(" ",$end_time);
$end_time = $end_array[1] + $end_array[0];
$time = $end_time - $start_time;

?>

<!-- Футер сайта -->
</div>



<footer class="footer"><div class="content has-text-centered"><p>
<strong class="has-text-danger is-size-7">IssaParser by </strong> <a class="tag is-danger is-light" href="https://github.com/pasichDev">Andrii PasichNIK</a></br>
<small class="is-size-7">Матеріали взято у </small> <a class="tag is-light" href="https://issaplus.com/">IssaPlus</a></br>
<small> Ген: <b><? echo mb_strimwidth($time, 0, 6); ?>с</b> </smal></p></div></footer>  
</div> </body></html>






