<?php 
include('../path.php');

#arrays
$scanned_files = array('app', 'assets');
$blackList = array('.git', '.well-known');

#variables
$baseurl = 'https://blissencore.com';
$oldContent = "http://localhost:8080/blissencore";
$newcontent = "<?php header('location: " . $baseurl . "');exit();?>";
$editor_counter = 0;
$adder_counter = 0;

#tests
//dd($results);
//dd(stripos($baseurl . '.....', '...'));

function file_adder($file, $content){
    global $adder_counter;
    if(file_exists($file)){
        return true;
    }else{
        /* $content = "<?php header('location: " . BASE_URL . "');exit();?>"; */
        $myfile = fopen($file, "w") or die("Unable to open file!");
        fwrite($myfile, $content); fclose($myfile);
        $adder_counter++; return $adder_counter;
    }
}

function file_editor($file, $oldContent, $newcontent){
    global $editor_counter; global $adder_counter;
    if(file_exists($file)){
        $fileContent = file_get_contents($file);
        if(stripos($fileContent, $oldContent)){
            $myfile = fopen($file, "w") or die("Unable to open file!");
            fwrite($myfile, $newcontent); fclose($myfile);
            $editor_counter++; return $editor_counter;
        }
    }else{
        $myfile = fopen($file, "w") or die("Unable to open file!");
        fwrite($myfile, $newcontent); fclose($myfile);
        $adder_counter++; return $adder_counter;
    }
}

function file_marker($path, $scanned_files){
    array_push($scanned_files, $path);
    return($scanned_files);
}

function scan($dir){
    global $editor_counter; global $adder_counter; global $oldContent; global $newcontent;
    $results = scandir($dir);
    foreach($results as $key => $result){
        switch ($result) {
            case '.': break; case '..': break;
            case '.git': break;  case '.well-known': break; case 'vendor': break; 
            default:
                $result = $dir . $result;
                if(is_dir($result)){
                    $adder = file_adder($result . '/index.php', $newcontent);
                    $editor = file_editor($result . '/index.php', $oldContent, $newcontent);
                    scan($result . '/');
                }
                break;
        }
    }
}

scan('./');
echo 'Edited ' . $editor_counter . ' old files <br>';
echo 'Added ' . $adder_counter . ' new files <br>';
?>