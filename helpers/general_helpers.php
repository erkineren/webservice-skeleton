<?php

//region Csv Araçları

/**
 * @param $data
 * @param string $filename
 * @param string $delimiter
 * @param string $enclosure
 * @param string $escape_char
 * @param null $headings
 * @param callable|null $callback
 */
function csv_flush($data, $filename = '', $delimiter = ';', $enclosure = '"', $escape_char = "\\", $headings = null, callable $callback = null)
{
    if (empty($filename)) {
        $filename = date('YmdHis');
    }
    $filename = preg_replace('/(.csv)$/i', '', $filename);;
    $output = fopen("php://output", 'w') or die("Can't open php://output");
    header("Content-Type:application/csv");
    header("Content-Disposition:attachment;filename=" . $filename . ".csv");

    $fputcsv = function ($data) use ($escape_char, $enclosure, $delimiter, $output) {
        fputcsv($output, $data, $delimiter, $enclosure, $escape_char);
    };

    $tmp = true;
    $i = 0;
    foreach ($data as $item) {
        if ($callback) {
            $callback($i, $fputcsv);
//            if ($callaback_data) fputcsv($output, $callaback_data, $delimiter, $enclosure, $escape_char);
        }
        if ($tmp) {
            fputcsv($output, $headings ? $headings : array_keys($item), $delimiter, $enclosure, $escape_char);
            $tmp = false;
        }
        fputcsv($output, $item, $delimiter, $enclosure, $escape_char);
        $i++;
    }
    fclose($output) or die("Can't close php://output");
    die;
}

/**
 * @param $data
 * @param string $filename
 * @param string $delimiter
 */
function array_to_csv($data, $filename = '', $delimiter = ';')
{
    if (empty($filename)) {
        $filename = date('YmdHis') . 'csv';
    }
    $output = fopen($filename, 'w') or die("Can't open {$filename}");
    header("Content-Type:application/csv");
    header("Content-Disposition:attachment;filename=" . basename($filename) . ".csv");

    $tmp = true;
    foreach ($data as $item) {
        if ($tmp) {
            fputcsv($output, array_keys($item), $delimiter);
            $tmp = false;
        }
        fputcsv($output, $item, $delimiter);
    }
    fclose($output) or die("Can't close {$filename}");
}

/**
 * @param $filename
 * @param string $delimiter
 * @param bool $associative
 * @param bool $associative_key_index
 * @param bool $utf8_encode
 * @param array $fields
 * @param int $rowoffset
 * @return array
 */
function csv_to_array(
    $filename,
    $delimiter = ';',
    $associative = true,
    $associative_key_index = false,
    $utf8_encode = true,
    $fields = [],
    $rowoffset = 0
)
{
    $csv_array = [];
    $i = 0;
    if (($handle = fopen($filename, "r")) !== false) {
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($i < $rowoffset) {
                $i++;
                continue;
            }
            if ($utf8_encode) {
                $row = array_map("utf8_encode", $row);
            } //added

            if ($associative) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }

                $csv_array_item_key = $i;
                if ($associative_key_index !== false && $associative_key_index >= 0) {
                    $csv_array_item_key = $row[$associative_key_index];
                }

                foreach ($row as $k => $value) {
                    $csv_array[$csv_array_item_key][$fields[$k]] = $value;
                }

            } else {
                $csv_array[$i] = $row;
            }
            $i++;

            //print_r($row);
        }
        fclose($handle);
    }

    return $csv_array;
}


//endregion

//region String Araçları

/**
 * @param $string
 * @param string $encoding
 * @return string
 */
function mb_ucfirst($string, $encoding = 'utf8')
{
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}

/**
 * @param $search
 * @param $subject
 * @param bool $case_insensitive
 * @return mixed
 */
function clearStr($search, $subject, $case_insensitive = true)
{
    if (!$subject || !$search) return $subject;

    if ($case_insensitive)
        return str_ireplace($search, '', $subject);

    return str_replace($search, '', $subject);
}

/**
 * @param $s
 * @return mixed|string|string[]|null
 */
function slug($s)
{
    $tr = array('ş', 'Ş', 'ı', 'I', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç', '(', ')', '/', ':', ',');
    $eng = array('s', 's', 'i', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c', '', '', '-', '-', '');
    $s = str_replace($tr, $eng, $s);
    $s = strtolower($s);
    $s = preg_replace('/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/', '', $s);
    $s = preg_replace('/\s+/', '-', $s);
    $s = preg_replace('|-+|', '-', $s);
    $s = preg_replace('/#/', '', $s);
    $s = str_replace('.', '', $s);
    $s = trim($s, '-');

    return $s;
}

/**
 * @param $s
 * @return mixed
 */
function tr_to_en($s)
{
    $tr = array('ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç');
    $eng = array('s', 'S', 'i', 'I', 'g', 'G', 'u', 'U', 'o', 'O', 'C', 'c');

    return str_replace($tr, $eng, $s);
}

/**
 * @param int $length
 * @return string
 */
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

/**
 * @param $data
 * @return string
 */
function makeHash($data)
{
    return base64_encode(hash_hmac('SHA256', $data, "689f84920a5c949492633726a4a578d4", true));
}

/**
 * @param $name
 * @param bool $append_random_to_end
 * @param string $delimiter
 * @return string
 */
function append_date($name, $append_random_to_end = true, $delimiter = '-')
{
    $random = $append_random_to_end ? generateRandomString(6) : '';

    return $name . $delimiter . date("Y.m.d.H.i.s") . $delimiter . $random;
}

/**
 * @return string
 */
function uniqToken()
{
    return md5(uniqid(mt_rand(), true));
}

/**
 * @param $arr
 * @return false|string
 */
function prettyPreJSON($arr)
{
    $json = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $json = "<pre>" . $json . "</pre>";

    return $json;
}

/**
 * @param $action
 * @param $string
 * @return bool|string
 */
function dec_enc($action, $string)
{
    if ($string === '') return $string;
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = ')H80F &u]n-0|Yz{_+cIkckr4+;9j4~h@,0-{31IE|ojVw^gG,M<zRj@{FFFd8-F';
    $secret_iv = '?_+.tg:~$`  !7]=`f7cMAuk >a!-TgXYD$mXc1L{8E=<zOLtjJJJ]J}f#.dA$;o';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

/**
 * @param $str
 * @return bool|string
 */
function my_enc($str)
{
    return dec_enc('encrypt', $str);
}

/**
 * @param $str
 * @return bool|string
 */
function my_dec($str)
{
    return dec_enc('decrypt', $str);
}

/**
 * @param $from
 * @param $to
 * @return array
 */
function computeDiff($from, $to)
{
    $diffValues = array();
    $diffMask = array();

    $dm = array();
    $n1 = count($from);
    $n2 = count($to);

    for ($j = -1; $j < $n2; $j++) $dm[-1][$j] = 0;
    for ($i = -1; $i < $n1; $i++) $dm[$i][-1] = 0;
    for ($i = 0; $i < $n1; $i++) {
        for ($j = 0; $j < $n2; $j++) {
            if ($from[$i] == $to[$j]) {
                $ad = $dm[$i - 1][$j - 1];
                $dm[$i][$j] = $ad + 1;
            } else {
                $a1 = $dm[$i - 1][$j];
                $a2 = $dm[$i][$j - 1];
                $dm[$i][$j] = max($a1, $a2);
            }
        }
    }

    $i = $n1 - 1;
    $j = $n2 - 1;
    while (($i > -1) || ($j > -1)) {
        if ($j > -1) {
            if ($dm[$i][$j - 1] == $dm[$i][$j]) {
                $diffValues[] = $to[$j];
                $diffMask[] = 1;
                $j--;
                continue;
            }
        }
        if ($i > -1) {
            if ($dm[$i - 1][$j] == $dm[$i][$j]) {
                $diffValues[] = $from[$i];
                $diffMask[] = -1;
                $i--;
                continue;
            }
        }
        {
            $diffValues[] = $from[$i];
            $diffMask[] = 0;
            $i--;
            $j--;
        }
    }

    $diffValues = array_reverse($diffValues);
    $diffMask = array_reverse($diffMask);

    return array('values' => $diffValues, 'mask' => $diffMask);
}

/**
 * @param $line1
 * @param $line2
 * @return string
 */
function diffline($line1, $line2)
{
    $diff = computeDiff(str_split($line1), str_split($line2));
    $diffval = $diff['values'];
    $diffmask = $diff['mask'];

    $n = count($diffval);
    $pmc = 0;
    $result = '';
    for ($i = 0; $i < $n; $i++) {
        $mc = $diffmask[$i];
        if ($mc != $pmc) {
            switch ($pmc) {
                case -1:
                    $result .= '</del>';
                    break;
                case 1:
                    $result .= '</ins>';
                    break;
            }
            switch ($mc) {
                case -1:
                    $result .= '<del>';
                    break;
                case 1:
                    $result .= '<ins>';
                    break;
            }
        }
        $result .= $diffval[$i];

        $pmc = $mc;
    }
    switch ($pmc) {
        case -1:
            $result .= '</del>';
            break;
        case 1:
            $result .= '</ins>';
            break;
    }

    return $result;
}

/**
 * @param $formul
 * @param null $args
 * @param null $_
 * @return mixed
 */
function calculateExp($formul, $args = null, $_ = null)
{
    $args = func_get_args();
    unset($args[0]);
    $args = array_values($args);
//    $formul = "%d/1.19+5";
    return eval('return ' . (vsprintf($formul, $args)) . ';');
}

/**
 * @param $currencycode
 * @param null $ifemtpystr
 * @return string|null
 */
function currenycode2symbol($currencycode, $ifemtpystr = null)
{
    switch (strtolower($currencycode)) {
        case 'eur':
            $currency_symbol = ' €';
            break;
        case 'usd':
            $currency_symbol = ' $';
            break;
        case 'try':
            $currency_symbol = ' ₺';
            break;
        case 'gbp':
            $currency_symbol = ' £';
            break;
        default:
            $currency_symbol = " $currencycode";
            break;
    }
    if ($ifemtpystr !== null && !$currencycode) return $ifemtpystr;
    return $currency_symbol;
}

/**
 * @param $currency
 * @param $currenycode
 * @param null $ifemtpystr
 * @return string|null
 */
function currencyWithSymbol($currency, $currenycode, $ifemtpystr = null)
{
    if ($ifemtpystr !== null && !$currency) return $ifemtpystr;
    return number_format($currency, 2, ',', '.') . currenycode2symbol($currenycode);
}

/**
 * @param $length
 * @return string
 */
function randomNumber($length)
{
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}

/**
 * @return string
 */
function generateRandomEan()
{
    $code = randomNumber(12);

    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        if ($i % 2 == 1) $sum = $sum + ($code[$i] * 3);
        else $sum = $sum + $code[$i];
    }

    $checkDigit = 10 - ($sum % 10);
    $checkDigit = $checkDigit % 10;

    $code = $code . '' . $checkDigit;


    return $code;
}

/**
 * @param $code12length
 * @return bool|string
 */
function generate_ean($code12length)
{
    if (strlen($code12length) != 12) return false;

    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        if ($i % 2 == 1) $sum = $sum + ($code12length[$i] * 3);
        else $sum = $sum + $code12length[$i];
    }

    $checkDigit = 10 - ($sum % 10);
    $checkDigit = $checkDigit % 10;

    $code12length = $code12length . '' . $checkDigit;


    return $code12length;
}

/**
 * @param $barcode
 * @return bool
 */
function validate_EAN13Barcode($barcode)
{
    // check to see if barcode is 13 digits long
    if (!preg_match("/^[0-9]{13}$/", $barcode)) {
        return false;
    }

    $digits = $barcode;

    // 1. Add the values of the digits in the
    // even-numbered positions: 2, 4, 6, etc.
    $even_sum = $digits[1] + $digits[3] + $digits[5] +
        $digits[7] + $digits[9] + $digits[11];

    // 2. Multiply this result by 3.
    $even_sum_three = $even_sum * 3;

    // 3. Add the values of the digits in the
    // odd-numbered positions: 1, 3, 5, etc.
    $odd_sum = $digits[0] + $digits[2] + $digits[4] +
        $digits[6] + $digits[8] + $digits[10];

    // 4. Sum the results of steps 2 and 3.
    $total_sum = $even_sum_three + $odd_sum;

    // 5. The check character is the smallest number which,
    // when added to the result in step 4, produces a multiple of 10.
    $next_ten = (ceil($total_sum / 10)) * 10;
    $check_digit = $next_ten - $total_sum;

    // if the check digit and the last digit of the
    // barcode are OK return true;
    if ($check_digit == $digits[12]) {
        return true;
    }

    return false;
}

/**
 * @param $str
 * @return mixed
 */
function normalizeDecimalSeperator($str)
{
    return str_replace(',', '.', str_replace('.', '', $str));
}

/**
 * @param $data
 * @return string
 */
function generate_hash($data)
{
    return base64_encode(hash_hmac('SHA256', $data, "hgnfjru13769asdn30714mnvb7", true));
}

/**
 * @param $hash
 * @param $data
 * @return bool
 */
function validate_hash($hash, $data)
{
    return $hash == generate_hash($data);
}

//endregion

//region Array Araçları

/**
 * @param $arr
 * @param $columns
 * @return array
 */
function array_select_columns($arr, $columns)
{
    return array_map(function ($a) use ($columns) {
        return array_intersect_key($a, array_flip($columns));
    }, $arr);
}

/**
 * @param $arr
 * @param $columns
 * @return array
 */
function array_unselect_columns($arr, $columns)
{
    return array_map(
        function ($a) use ($columns) {
            return array_diff_key($a, array_flip($columns));
        },
        $arr
    );
}

/**
 * @param $arr
 * @param $columns
 * @return array
 */
function array_select_column($arr, $columns)
{
    return array_intersect_key($arr, array_flip($columns));
}

/**
 * @param $arr
 * @param $columns
 * @return array
 */
function array_unselect_column($arr, $columns)
{
    return array_diff_key($arr, array_flip($columns));
}

/**
 * @param $array
 * @param $on
 * @param int $order
 * @return array
 */
function array_sort($array, $on, $order = SORT_ASC)
{

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

/**
 * @param $a
 * @param $b
 * @param $column
 * @return array
 */
function compare_array($a, $b, $column)
{
    $a_c = array_column($a, $column);
    $b_c = array_column($b, $column);

    $diff = array_diff($a_c, $b_c);

    $data = [];
    foreach ($diff as $key => $item) {
        $data[$key] = $a[$key];
    }

    return $data;
}

/**
 * @param $a
 * @param $b
 * @param $column
 * @return array
 */
function my_array_intersect($a, $b, $column)
{
    $a_c = array_column($a, $column);
    $b_c = array_column($b, $column);

    $intersect = array_intersect($a_c, $b_c);

    $data = [];
    foreach ($intersect as $key => $item) {
        $data[$key] = $a[$key];
    }

    return $data;
}

/**
 * @param $array
 * @param $column
 * @param bool $remove_column
 * @return array
 */
function array_make_column_key($array, $column, $remove_column = false)
{
    $result = [];
    foreach ($array as $key => $value) {
        $result[$value[$column]] = $value;
        if ($remove_column) {
            unset($result[$value[$column]][$column]);
        }
    }

    return $result;
}

/**
 * @param $array
 * @param $column
 * @param bool $remove_column
 * @return array
 */
function array_make_column_key_accumulate($array, $column, $remove_column = false)
{
    $result = [];
    foreach ($array as $key => $value) {
        $_key = $value[$column];
        if ($remove_column)
            unset($value[$column]);
        $result[$_key][$key] = $value;

    }

    return $result;
}

/**
 * @param $array
 * @param $column
 * @param $delimiter
 * @param bool $trim_values
 * @return mixed
 */
function explode_column($array, $column, $delimiter, $trim_values = true)
{
    foreach ($array as &$item)
        foreach ($item as $key => &$value) {
            if ($key == $column) {
                $value = explode($delimiter, $value);
                if ($trim_values) $value = array_map('trim', $value);
            }
        }
    return $array;
}

/**
 * @param $array
 * @param $column
 * @param int $dept
 * @return array
 */
function array_column_to_single_array($array, $column, $dept = 1)
{

    $callback = function ($item) use ($column) {
        if (isset($item[$column]) && is_array($item[$column])) {
            $codes = [];
            foreach ($item[$column] as $value) {
                $codes[] = $value;
            }
            return $codes;
        }
    };

    $result = [];
    foreach ($array as $item) {
        $result = array_merge($result, $callback($item));
    }


    return $result;
}

/**
 * @param $array
 * @param $key_column
 * @param $value_column
 * @return array
 */
function array_make_key_value_pair($array, $key_column, $value_column)
{
    $result = [];
    foreach ($array as $key => $value) {
        $result[$value[$key_column]] = $value[$value_column];
    }

    return $result;
}

/**
 * @param $arr1
 * @param $arr2
 * @return array
 */
function array_merge_multi($arr1, $arr2)
{
    $data = [];
    foreach ($arr1 as $key => $item) {
        $data[$key] = array_merge($item, $arr2[$key]);
    }
    return $data;
}

/**
 * @param $categories
 * @param int $parent
 * @return mixed
 */
function categoriesToTree(&$categories, $parent = 1)
{
    $map = array(
        $parent => array('childs' => array()),
    );

    foreach ($categories as &$category) {
        $category['childs'] = array();
        $map[$category['id']] = &$category;
    }

    foreach ($categories as &$category) {
        $map[$category['parent']]['childs'][] = &$category;
    }

    return $map[$parent];

}

/**
 * @param $items
 * @return array
 */
function buildTree($items)
{

    $childs = array();

    foreach ($items as $item) {
        $childs[$item['parent']][] = $item;
    }

    foreach ($items as $item) {
        if (isset($childs[$item['id']])) {
            $item['childs'] = $childs[$item['id']];
        }
    }

    return $childs;
}

/**
 * @param $array
 * @return mixed
 */
function intToBool($array)
{
    foreach ($array as $key => &$val) {
        if (strpos($key, 'is') === 0) {
            $val = boolval($val);
        }
    }

    return $array;
}

/**
 * @return mixed
 */
function readInputStreamArray()
{
    return json_decode(file_get_contents('php://input'), TRUE);
}

/**
 * @param $file
 * @return mixed
 */
function readJSON($file)
{
    return json_decode(file_get_contents($file), true);
}

/**
 * @param $callback
 * @param $arr
 * @return mixed
 */
function array_map_recursive($callback, $arr)
{
    if (is_string($arr)) {
        return $callback($arr);
    } elseif (is_array($arr)) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $result[$key] = array_map_recursive($callback, $value);
            } else {
                $result[$key] = $callback($value);
            }
        }
        return $result;
    } else {
        return $arr;
    }
}

/**
 * @param $data
 * @return string
 */
function serializeIfArray($data)
{
    if (is_array($data)) return serialize($data);
    return $data;
}

/**
 * @param $value
 * @return string
 */
function jsObj_encode($value)
{
    return htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8');
}

/**
 * @param array $keys
 * @param array $arr
 * @return bool
 */
function array_keys_exists(array $keys, array $arr)
{
    return !array_diff_key(array_flip($keys), $arr);
}

// endregion

// region File Araçları

/**
 * @param $filename
 * @param $callback
 * @param int $lineoffset
 * @return array|bool
 */
function readLineByLine($filename, $callback, $lineoffset = 0)
{
    $lines = [];
    $handle = fopen($filename, "r");
    if ($handle) {
        $i = 0;
        while (($line = fgets($handle)) !== false) {
            $i++;
            if ($i <= $lineoffset) continue;
            $editedline = $callback($line, $i);
            if ($editedline === null) $editedline = $line;
            $lines[] = $editedline;
        }
        fclose($handle);
        return $lines;
    }
    return false;
}

// endregion

// region File Upload Araçları
/**
 * @param $files
 * @return array
 */
function normalizeFiles($files)
{
    $_files = [];
    $_files_count = count($files['name']);
    $_files_keys = array_keys($files);
    $_files_names = array_keys($files['name']);
//    $_multiple_files_count = is_array($files['name'][reset($_files_names)]) ? count($files['name'][reset($_files_names)]) : 1;

    for ($i = 0; $i < $_files_count; $i++)
        foreach ($_files_keys as $key)
            $_files[$i][$key] = $files[$key][$_files_names[$i]];

    return $_files;
}

// endregion

//region Genel Helpers

/**
 * @param string $data
 * @param bool $die
 */
function _yaz($data = '', $die = true)
{
    echo '<pre>';
    if (is_bool($data) || is_null($data) || $data === "") var_dump($data);
    else print_r($data);
    echo '</pre>';
    if ($die) {
        die;
    }
}

/**
 * @param string $data
 * @param bool $die
 */
function _vardump($data = '', $die = true)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    if ($die) {
        die;
    }
}

/**
 * @param $data
 * @param bool $die
 */
function _varexport($data, $die = true)
{
//    header('Content-Type: text/plain; charset=UTF-8');

//    var_export($data);
    highlight_string("<?php\n " . var_export($data, true) . ";\n ?>");
    echo '<script>document.getElementsByTagName("code")[0].getElementsByTagName("span")[1].remove() ;document.getElementsByTagName("code")[0].getElementsByTagName("span")[document.getElementsByTagName("code")[0].getElementsByTagName("span").length - 1].remove() ; </script>';
    if ($die) {
        die;
    }
}

/**
 * @param $array
 * @param bool $path
 * @param bool $top
 * @return string
 */
function printCode($array, $path = false, $top = true)
{
    $data = "";
    $delimiter = "~~|~~";
    $p = null;
    if (is_array($array)) {
        foreach ($array as $key => $a) {
            if (!is_array($a) || empty($a)) {
                if (is_array($a)) {
                    $data .= $path . "['{$key}'] = array();" . $delimiter;
                } else {
                    $data .= $path . "['{$key}'] = \"" . htmlentities(addslashes($a)) . "\";" . $delimiter;
                }
            } else {
                $data .= printCode($a, $path . "['{$key}']", false);
            }
        }
    }

    if ($top) {
        $return = "";
        foreach (explode($delimiter, $data) as $value) {
            if (!empty($value)) {
                $return .= '$array' . $value . "<br>";
            }
        };

        return $return;
    }

    return $data;
}

/**
 * @param $url
 * @param null $post
 * @param null $requestHeader
 * @param string $cookie
 * @return array
 */
function cURL($url, $post = null, $requestHeader = null, $cookie = 'cookie.txt')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, realpath($cookie));
    curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($cookie));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($requestHeader) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);
    }

    if ($post) {
        if (!is_array($post)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        } else {
            $postvars = http_build_query($post);
            curl_setopt($ch, CURLOPT_POST, count($post));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        }
    }

    $result = curl_exec($ch);
    //$result = iconv('ISO-8859-9','UTF-8',$result);


    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($result, 0, $header_size);
    $body = substr($result, $header_size);

    curl_close($ch);
    $arr = array(
        'header_size' => $header_size,
        'header' => $header,
        'body' => $body,
    );

    return $arr;
}

function open_error()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

/**
 * @param $url
 * @return bool
 */
function isLinkDead($url)
{
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($handle);
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);

    return $httpCode == 404;
}

/**
 * @param $dir
 */
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object))
                    rrmdir($dir . "/" . $object);
                else
                    unlink($dir . "/" . $object);
            }
        }
        rmdir($dir);
    }
}

/**
 * @param $zipfile
 * @param string $destination
 * @return bool
 */
function unzip_file($zipfile, $destination = './')
{
    $zip = new ZipArchive;
    $res = $zip->open($zipfile);
    if ($res === TRUE) {
        $zip->extractTo($destination);
        $zip->close();
        return true;
    }
    return false;
}


//endregion

// region Datetime Araçları

/**
 * @param $datetime
 * @param bool $showTime
 * @param bool $showBugun
 * @return false|string
 */
function simpleDate($datetime, $showTime = true, $showBugun = false)
{
    $date = date('d-m-Y', strtotime($datetime));
    $time = date('H:i', strtotime($datetime));
    $isToday = $date == date('d-m-Y');
    $isYesterday = $date == date('d-m-Y', strtotime('-1 day'));
    if ($isToday) {
        $date = '';
        if ($showBugun) $date = 'Bugün';
    }
    if ($isYesterday) $date = 'Dün';
    if ($showTime) $date .= ' ' . $time;
    return $date;
}

/**
 * @param $offset
 * @return bool
 */
function setTimezoneByOffset($offset)
{
    $testTimestamp = time();
    date_default_timezone_set('UTC');
    $testLocaltime = localtime($testTimestamp, true);
    $testHour = $testLocaltime['tm_hour'];


    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr) {
        //echo $abbr."<br>";
        foreach ($abbr as $city) {
            date_default_timezone_set($city['timezone_id']);
            $testLocaltime = localtime($testTimestamp, true);
            $hour = $testLocaltime['tm_hour'];
            $testOffset = $hour - $testHour;
            if ($testOffset == $offset) {
                return true;
            }
        }
    }
    return false;
}

// endregion

// region DB Araçları

/**
 * @param $table
 * @param $arr
 * @param string $db
 * @return string
 */
function makeInsertSql($table, $arr, $db = '')
{
    if (!$arr) die("Insert sql söz dizimi için Array boş olamaz");

    if (!empty($db)) $db = "`{$db}`.";
    return "INSERT INTO {$db}`$table` ( " .
        implode(',', array_keys($arr)) .
        ") VALUES ( '" .
        implode("','", $arr) .
        "' )";
}

/**
 * @param $table
 * @param $arr
 * @param string $db
 * @return string
 */
function makeBatchInsertSql($table, $arr, $db = '')
{
    if (!$arr) die("Insert sql söz dizimi için Array boş olamaz");

    if (!isset($arr[0])) die("Dizide 0. indeks yok !!!");

    $values_strs = [];
    foreach ($arr as $item) {
        $values_strs[] = "('" . implode("','", $item) . "')";
    }
    if (!empty($db)) $db = "`{$db}`.";
    return "INSERT INTO {$db}`$table` " . PHP_EOL . "(" .
        implode(',', array_keys($arr[0])) .
        ") " . PHP_EOL . "VALUES " . PHP_EOL . implode(',' . PHP_EOL, $values_strs);
}

/**
 * @param $arr
 * @param null $key_name
 * @return string
 */
function array_to_where_in_str($arr, $key_name = null)
{

    if ($key_name != null)
        $filtered = array_column($arr, $key_name);
    else
        $filtered = $arr;
    $str = "('";
    if (!empty($filtered)) {
        $str .= implode("','", array_filter(array_map('trim', $filtered)));
    }
    $str .= "')";
    return $str;
}

/**
 * @param $in_data
 * @return string
 */
function makeInStr($in_data)
{
    $in_str = $in_data;
    if (is_array($in_data)) $in_str = implode("','", $in_data);
    else return makeInStr(explode(',', $in_data));
    return "'" . $in_str . "'";
}

/**
 * @param $db
 * @param $tablenamelike
 * @param null $tablecommentlike
 * @return array
 */
function getDbTables($db, $tablenamelike, $tablecommentlike = null)
{
    $db4 = getDB_full();

    $where = '';
    if ($tablecommentlike) {
        $where .= " AND TABLE_COMMENT LIKE '{$tablecommentlike}'";
    }

    $tables = $db4->query("
        SELECT TABLE_NAME
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME LIKE '{$tablenamelike}' {$where}
    ")->result_array();

    $tables = array_column($tables, 'TABLE_NAME');


    return $tables;
}

// endregion