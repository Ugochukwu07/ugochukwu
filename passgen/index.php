<?php 

$firstName = "Firstname"; $lastName = "Lastname"; $dobY = 1970; $dobM = 06; $dobD = 17;
$string = $firstName . $lastName . $dobD . $dobM . $dobY;
$string=str_split($string);
$chars = array('&', '@', '%', '-', '_', '+', '$');
$wordlist = array_merge($chars, $string);
//print_r($wordlist);

function wordPlus($file, $word){
    $oldList = file_get_contents($file);
    $newList = $oldList . ' ' . $word;
    $List = fopen($file, "w") or die("Unable to open file!");
    fwrite($List, $newList); fclose($List);
    return true;
}

#format 1: firstname . $chars . dob$
function combinator($string, $number){
    global $chars;
    foreach($chars as $char){
        $pass = $string . $char . $number;
        wordPlus('passlist.txt', $pass);
    }
    wordPlus('passlist.txt', $string . $number);
}

$alphabet = $wordlist;
$result = array();
$arrResult = array();

// recursively create all possible combinations {
combine($alphabet, $result, $arrResult);

function combine($shiftedAlphabet, &$result, &$arrResult) {
    global $alphabet;

    $currentElementStr = '';
    $currentElementArr = array();
    for($i = 0; $i < count($shiftedAlphabet); ++$i) {
        $newElement = $shiftedAlphabet[$i];
        $currentElementStr .= $newElement;
        $currentElementArr[] = $newElement;

        if(!in_array($currentElementStr, $result)) { // if not duplicated => push it to result
            // find right position {
            $thisCount = count($currentElementArr);
            $indexFrom = 0;
            $indexToInsert = 0;

            // find range of indexes with current count of elements {
            foreach ($arrResult as $arrResultKey => $arrResultValue) {
                $indexToInsert = $arrResultKey + 1;
                if ($thisCount > count($arrResultValue)) {
                    $indexFrom = $indexToInsert;
                }
                if ($thisCount < count($arrResultValue)) {
                    --$indexToInsert;
                    break;
                }
            }
            // find range of indexes with current count of elements }

            // find true index inside true range {
            $trueIndex = $indexToInsert;
            $break = false;
            for($j = $indexFrom; $j < $indexToInsert; ++$j) {
                $trueIndex = $j + 1;
                foreach($arrResult[$j] as $key => $value) {
                    if (array_search($value, $alphabet) > array_search($currentElementArr[$key], $alphabet)) {
                        $break = true;
                        break;
                    }
                }
                if($break) {
                    --$trueIndex;
                    break;
                }
            }
            // find true index inside true range }

            array_splice($result, $trueIndex, 0, $currentElementStr);
            array_splice($arrResult, $trueIndex, 0, array($currentElementArr));
        }
    }

    for($i = 0; $i < count($shiftedAlphabet) - 1; ++$i) {
        $tmpShiftedAlphabet = $shiftedAlphabet; // for combining all possible subcombinations
        array_splice($tmpShiftedAlphabet, $i, 1);
        combine($tmpShiftedAlphabet, $result, $arrResult);
    }
}
// recursively create all possible combinations }

foreach($result as $re){
    wordPlus('passlist.txt', $re);
}

//echo file_get_contents("passlist.txt");
?>