<?php
$base_path = $modx->getOption('base_path');
$uploaddir = $base_path.'upload/';
$uploadfile = $uploaddir . basename($_FILES['filename']['name']);

if ($_FILES && $_FILES['filename']['error']== UPLOAD_ERR_OK)
{
    $name = $_FILES['filename']['name'];
    move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile);
    echo "Файл загружен";

$base_path = $modx->getOption('base_path');
$filepath = $uploadfile;
//$filepath = $base_path ."struct.xlsx";

    $base_path = $modx->getOption('base_path');
    $path_php_excel = $base_path ."assets/components/phpexcel/PHPExcel.php";
    require_once $path_php_excel;
    $arEx=array(); // инициализируем массив

    $inputFileType = PHPExcel_IOFactory::identify($filepath);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
    $objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
    $objPHPExcel = $objReader->load($filepath); // загружаем данные файла в объект
    $arEx = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив
    
foreach ($arEx as $item){
    $where = array('value' => $item[0], 'tmplvarid' => '2');
    $tvs = $modx -> getCollection('modTemplateVarResource', $where);
    $json = "";
    if (empty($tvs)){
        
        //$str="Вес:750 ml;Срок годности: 18 месяцев;Колличество штук в коробке: 12 шт.;Температура применения:от 5 градусов;";
        /*
        $tvMgnix = explode(";", $item[15]);
        array_pop($tvMgnix);
        $i=0;
        foreach ($tvMgnix as $key => $items)
        {
            $ar[] = explode(":", $items);
            
            $art[] = array('MIGX_id' => $i, 'title' => $ar[$key][0], 'value' => $ar[$key][1]);
            //array_push($art, array('MIGX_id' => $i, 'title' => $ar[$key][0], 'value' => $ar[$key][1]));
            $i++;
        }
        $json = json_encode($art);
        */
        echo "Нет такого элемента, с артикулом ".$item[0]." создаем! <br>";
        $resource1 = $modx->newObject('modResource');                        
        $resource1->set('template', 2);             // Назначаем ему нужный шаблон
        $resource1->set('isfolder', 0);             // Указываем, что это не контейнер   
        $resource1->set('published', 1);            // Неопубликован
        $resource1->set('createdon', time());       // Время создания
        $resource1->set('pagetitle', $item[1]);        // Заголовок
        $resource1->set('alias', $item[0]);   // Псевдоним
        $resource1->setContent($item[11]);           // Содержимое
        $resource1->set('parent', 2);              // Родительский ресурс
        $resource1->save();
        
        $resource1->setTVValue('article_item', $item[0]);
//        $resource1->setTVValue('char_item_tv', $json);
        
        $resource1->setTVValue('extra_countitem_1_tv', $item[7]);   //Количество штук №2
        $resource1->setTVValue('extra_countitem_2_tv', $item[10]);  //Количество штук №3
        $resource1->setTVValue('extra_price_1_tv', $item[5]);       //Цена №2 За единицу товара
        $resource1->setTVValue('extra_price_2_tv', $item[8]);       //Цена №3 За единицу товара
        $resource1->setTVValue('extra_saleprice_1_tv', $item[6]);   //Цена со скидкой №2
        $resource1->setTVValue('extra_saleprice_2_tv', $item[9]);   //Цена со скидкой №3
        $resource1->setTVValue('base_countitem_tv', $item[4]);      //Количество штук
        $resource1->setTVValue('base_price_tv', $item[2]);          //Цена  За единицу товара
        $resource1->setTVValue('base_saleprice_tv', $item[3]);      //Цена скидка   Зачеркнутая цена
        $resource1->setTVValue('showonmain_item', $item[12]);       //Показывать на главной
        $resource1->setTVValue('salesleader_item', $item[13]);      //Лидер продаж
        $resource1->setTVValue('presence_item', $item[14]);         //В наличии


        $resource1->save();                         // Сохраняем
        unset($ar);
        unset($json);
        unset($art);
        
    }
    else{
        
        foreach ($tvs as $k => $tv){
 /*           $tvMgnix = explode(";", $item[15]);
            array_pop($tvMgnix);
            $i=0;
            foreach ($tvMgnix as $key => $items)
            {
                $ar[] = explode(":", $items);
                
                $art[] = array('MIGX_id' => $i, 'title' => $ar[$key][0], 'value' => $ar[$key][1]);
                //array_push($art, array('MIGX_id' => $i, 'title' => $ar[$key][0], 'value' => $ar[$key][1]));
                $i++;
            }
            $json = json_encode($art);
*/            
            
            $tvs[$k] = $tv->toArray();
            print '<br>Товар уже есть, его id=';
            print_r ($tvs[$k][contentid]);
            print ' обновим цену '.$item[0].' <br>';
            
            $resource2 = $modx->getObject('modResource', $tvs[$k][contentid]);
            $resource2->setTVValue('article_item', $item[0]);
//            $resource2->setTVValue('char_item_tv', $json);
/*            
            $resource2->setTVValue('extra_countitem_1_tv', $item[7]);   //Количество штук №2
            $resource2->setTVValue('extra_countitem_2_tv', $item[10]);  //Количество штук №3
            $resource2->setTVValue('extra_price_1_tv', $item[5]);       //Цена №2 За единицу товара
            $resource2->setTVValue('extra_price_2_tv', $item[8]);       //Цена №3 За единицу товара
            $resource2->setTVValue('extra_saleprice_1_tv', $item[6]);   //Цена со скидкой №2
            $resource2->setTVValue('extra_saleprice_2_tv', $item[9]);   //Цена со скидкой №3
            $resource2->setTVValue('base_countitem_tv', $item[4]);      //Количество штук
            $resource2->setTVValue('base_price_tv', $item[2]);          //Цена  За единицу товара
            $resource2->setTVValue('base_saleprice_tv', $item[3]);      //Цена скидка   Зачеркнутая цена
            $resource2->setTVValue('showonmain_item', $item[12]);       //Показывать на главной
            $resource2->setTVValue('salesleader_item', $item[13]);      //Лидер продаж
            $resource2->setTVValue('presence_item', $item[14]);         //В наличии
            $resource2->save();
 */           
            $resource2->setTVValue('article_item', $item[0]);
//            $resource2->setTVValue('char_item_tv', $json);
        
            $resource2->setTVValue('extra_countitem_1_tv', $item[7]);   //Количество штук №2
            $resource2->setTVValue('extra_countitem_2_tv', $item[10]);  //Количество штук №3
            $resource2->setTVValue('extra_price_1_tv', $item[5]);       //Цена №2 За единицу товара
            $resource2->setTVValue('extra_price_2_tv', $item[8]);       //Цена №3 За единицу товара
            $resource2->setTVValue('extra_saleprice_1_tv', $item[6]);   //Цена со скидкой №2
            $resource2->setTVValue('extra_saleprice_2_tv', $item[9]);   //Цена со скидкой №3
            $resource2->setTVValue('base_countitem_tv', $item[4]);      //Количество штук
            $resource2->setTVValue('base_price_tv', $item[2]);          //Цена  За единицу товара
            $resource2->setTVValue('base_saleprice_tv', $item[3]);      //Цена скидка   Зачеркнутая цена
            $resource2->setTVValue('showonmain_item', $item[12]);       //Показывать на главной
            $resource2->setTVValue('salesleader_item', $item[13]);      //Лидер продаж
            $resource2->setTVValue('presence_item', $item[14]);         //В наличии
            $resource2->set('pagetitle', $item[1]);                     // Заголовок
            $resource2->set('alias', $item[0]);                         // Псевдоним
            $resource2->setContent($item[11]);                          // Содержимое
            $resource2->save();
            
            unset($ar);
            unset($json);
            unset($art);
        }
    }
}

function createModXDocument($name){
    
}
function transliterate($st) {
    $st=strtr($st, 
        "абвгдежзийклмнопрстуфыэАБВГДЕЖЗИЙКЛМНОПРСТУФЫЭ",
        "abvgdegziyklmnoprstufieABVGDEGZIYKLMNOPRSTUFIE"
    );
    $st=strtr($st, array(
        'ё'=>"yo",    'х'=>"h",  'ц'=>"ts",  'ч'=>"ch", 'ш'=>"sh",  
        'щ'=>"shch",  'ъ'=>'',   'ь'=>'',    'ю'=>"yu", 'я'=>"ya",
        'Ё'=>"Yo",    'Х'=>"H",  'Ц'=>"Ts",  'Ч'=>"Ch", 'Ш'=>"Sh",
        'Щ'=>"Shch",  'Ъ'=>'',   'Ь'=>'',    'Ю'=>"Yu", 'Я'=>"Ya",
    ));
    return $st;
}
}