<?php
declare(strict_types=1);

session_start();

const INPUT_COLUMNS = ['Εκδότης', 'Επίπεδο', 'Σειρά', 'Τίτλος', 'Έτος', 'Συγγραφέας', 'ISBN', 'Τιμή', 'Περιγραφή', 'Εικόνα'];

const PRODUCT_TYPES = [
    'foreign_books' => [
        'label' => 'Ξενόγλωσσα βιβλία',
        'columns' => ['Εκδότης', 'Επίπεδο', 'Σειρά', 'Τίτλος', 'Έτος', 'ISBN', 'Τιμή', 'Περιγραφή', 'Εικόνες'],
        'indexes' => [0, 1, 2, 3, 4, 6, 7, 8, 9],
    ],
    'greek_books' => [
        'label' => 'Ελληνικά βιβλία',
        'columns' => ['Εκδότης', 'Τίτλος', 'Έτος', 'Συγγραφέας', 'ISBN', 'Τιμή', 'Περιγραφή', 'Εικόνες'],
        'indexes' => [0, 3, 4, 5, 6, 7, 8, 9],
    ],
    'clothes' => [
        'label' => 'Ρούχα',
        'columns' => ['Εκδότης', 'Τίτλος', 'Έτος', 'ISBN', 'Τιμή', 'Περιγραφή', 'Εικόνες'],
        'indexes' => [0, 3, 4, 6, 7, 8, 9],
    ],
];

const OUTPUT_HEADERS = [
    'id', 'active', 'title', 'level', 'pricenotax', 'taxrule', 'wholesale', 'onsale',
    '', 'discount%', '', '', 'isbn', '', '', 'ekdotis', '', '', '', '', '', '', '',
    'weight', '', '', 'quantity', '', '', '', '', '', '', '', 'ekdotis', 'description',
    'tags', 'meta-title', 'meta-keywords', 'metadescription', 'url-rewritten',
    'text when in stock', '', 'available for order', 'available date', 'creation date',
    'showprice', 'img urls', '', '', 'etos ekdosis', '', 'condition', '', '', '', '', '', '', '', ''
];

const PUBLISHERS = [
    'english' => [
        'Andrew Betsis Elt' => ['etsis'], 'Archer Boukouvalas' => ['oukouval'],
        'Cambridge University Press' => ['ambridge'], 'Burlington' => ['urlington'],
        'Grivas Publications' => ['rivas'], 'Express Publishing' => ['xpress'],
        'Hamilton House' => ['amilton'], 'Hillside Press' => ['illside'],
        'Pearson' => ['earson', 'ongman'], 'Macmillan' => ['acmillan'],
        'MM Publications' => ['mm', 'combo'],
        'National Geographic Cengage Learning' => ['national geographic', 'engage', 'new editions'],
        'Sylvia Kar Publications' => ['ylvia'], 'SuperCourse' => ['uper'], 'York Press' => []
    ],
    'french' => [
        "Trait D'Union" => ['rait'], 'Hachette' => ['achette'], 'CLE International' => ['cle'],
        'HATIER' => ['atier'], 'Katranidou-Raptakis' => ['katranid'], 'Le Livre Ouvert' => ['ouvert'],
        'Nouvel Esprit' => ['ouvel', 'kosvo'], 'Maison Des Langues' => ['aison']
    ],
    'german' => [
        'Hueber Hellas' => ['ueber'], 'Klett Hellas' => ['lett'], 'Langenscheidt' => ['langensch'],
        'Vlachos die Lupe' => ['lachos'], 'Praxis' => ['raxis']
    ],
    'italian' => ['Edilingua' => ['dilingua'], 'Guerra' => ['uerra']],
    'spanish' => ['Edelsa' => ['delsa'], 'Difusion' => ['difus'], 'Edinumen' => ['dinumen'], 'S.G.E.L' => ['sgell']]
];

const CATEGORIES = [
    '2110' => ['junior a', 'junior a & b'], '2111' => ['junior b'],
    '2112' => ['class a', 'a class', 'a1', 'senior a'], '2113' => ['senior b', 'class b', 'b class'],
    '2115' => ['intermediate', 'senior c', 'class c', 'c class', 'senior d', 'class d', 'd class'],
    '2116' => ['upper intermediate'], '2117' => ['begginer', 'beginner'], '2118' => ['elementary'],
    '2119' => ['pre-intermediate'], '2120' => ['b1+', 'senior e'], '2121' => ['c1'],
    '2122' => ['proficiency', 'c2', 'ecpe'], '2128' => ['pre junior'], '2130' => ['dictionary'],
    '2131' => ['προετοιμασία εξετάσεων', 'toeic', 'pte general', 'ielts'],
    '2132' => ['first certificate', 'ecce'],
    '183769' => ['κρατικό πιστοποιητικό γλωσσομάθειας'], '2129' => ['b2']
];

function lowerText(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function textLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function textSlice(string $value, int $start, ?int $length = null): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, $start, $length, 'UTF-8');
    }
    return $length === null ? substr($value, $start) : substr($value, $start, $length);
}

function containsText(string $haystack, string $needle): bool
{
    if ($needle === '') return false;
    return function_exists('mb_strpos')
        ? mb_strpos($haystack, $needle, 0, 'UTF-8') !== false
        : strpos($haystack, $needle) !== false;
}

function detectEncoding(string $sample): string
{
    if (strncmp($sample, "\xEF\xBB\xBF", 3) === 0) return 'UTF-8-BOM';
    if (preg_match('//u', $sample) === 1) return 'UTF-8';
    if (function_exists('mb_detect_encoding')) {
        $found = mb_detect_encoding($sample, ['Windows-1253', 'ISO-8859-7', 'Windows-1252'], true);
        if ($found !== false) return $found;
    }
    return 'unknown';
}

function toUtf8(string $value, string $encoding): string
{
    if ($encoding === 'UTF-8' || $encoding === 'UTF-8-BOM') return $value;
    if ($encoding === 'unknown') return $value;
    if (function_exists('mb_convert_encoding')) return mb_convert_encoding($value, 'UTF-8', $encoding);
    if (function_exists('iconv')) {
        $converted = iconv($encoding, 'UTF-8//IGNORE', $value);
        return $converted === false ? $value : $converted;
    }
    return $value;
}

function detectDelimiter(string $line): string
{
    $counts = [];
    foreach ([";", ",", "\t"] as $delimiter) {
        $counts[$delimiter] = count(str_getcsv($line, $delimiter));
    }
    arsort($counts);
    return (string) array_key_first($counts);
}

function looksLikeHeader(array $row): bool
{
    $joined = lowerText(implode('|', $row));
    return containsText($joined, 'isbn') && (containsText($joined, 'τιμ') || containsText($joined, 'price'));
}

function parsePrice(string $raw): array
{
    $original = trim($raw);
    if ($original === '') return ['value' => null, 'fixed' => null, 'error' => 'Η τιμή είναι κενή.'];

    $value = preg_replace('/[\s\x{00A0}€]/u', '', $original);
    if ($value === null || !preg_match('/^[+-]?[0-9.,]+$/', $value)) {
        return ['value' => null, 'fixed' => null, 'error' => 'Περιέχει χαρακτήρες που δεν ανήκουν σε αριθμό (επιτρέπονται ψηφία, κόμμα ή τελεία).'];
    }

    $comma = strrpos($value, ',');
    $dot = strrpos($value, '.');
    if ($comma !== false && $dot !== false) {
        $decimal = $comma > $dot ? ',' : '.';
        $thousands = $decimal === ',' ? '.' : ',';
        $normalized = str_replace($thousands, '', $value);
        $normalized = str_replace($decimal, '.', $normalized);
    } elseif ($comma !== false || $dot !== false) {
        $separator = $comma !== false ? ',' : '.';
        if (substr_count($value, $separator) > 1) {
            $parts = explode($separator, $value);
            $last = end($parts);
            if (strlen((string) $last) !== 3) {
                return ['value' => null, 'fixed' => null, 'error' => 'Έχει πολλαπλά δεκαδικά σύμβολα και δεν μπορεί να αναγνωριστεί με ασφάλεια.'];
            }
            $normalized = str_replace($separator, '', $value);
        } else {
            $digitsAfter = strlen($value) - (int) strrpos($value, $separator) - 1;
            if ($digitsAfter === 3 && strlen(preg_replace('/\D/', '', $value)) > 3) {
                return ['value' => null, 'fixed' => null, 'error' => "Το '$separator' είναι αμφίσημο: μπορεί να είναι δεκαδικό ή διαχωριστικό χιλιάδων."];
            }
            $normalized = str_replace($separator, '.', $value);
        }
    } else {
        $normalized = $value;
    }

    if (!is_numeric($normalized)) return ['value' => null, 'fixed' => null, 'error' => 'Δεν αναγνωρίζεται ως έγκυρος αριθμός.'];
    $number = (float) $normalized;
    if ($number < 0) return ['value' => null, 'fixed' => null, 'error' => 'Η τιμή δεν μπορεί να είναι αρνητική.'];
    return ['value' => $number, 'fixed' => number_format($number, 2, '.', ''), 'error' => null];
}

function matchPublisher(string $raw): array
{
    $value = lowerText(trim($raw));
    foreach (PUBLISHERS as $language => $publishers) {
        foreach ($publishers as $canonical => $aliases) {
            if (containsText($value, lowerText($canonical))) return [$canonical, $language];
            foreach ($aliases as $alias) {
                if (containsText($value, lowerText($alias))) return [$canonical, $language];
            }
        }
    }
    return [trim($raw), 'unassigned'];
}

function categoryId(string $level, string $language): string
{
    if ($language !== 'english') return '';
    $needle = lowerText(trim($level));
    foreach (CATEGORIES as $id => $names) {
        foreach ($names as $name) if ($needle === lowerText($name)) return $id;
    }
    return '';
}

function productTypeConfig(string $productType): array
{
    if (!isset(PRODUCT_TYPES[$productType])) throw new RuntimeException('Επιλέξτε έγκυρο είδος προϊόντων.');
    return PRODUCT_TYPES[$productType];
}

function normalizeInputRow(array $row, array $indexes): array
{
    $normalized = array_fill(0, count(INPUT_COLUMNS), '');
    foreach ($indexes as $source => $target) $normalized[$target] = $row[$source];
    return $normalized;
}

function analyzeCsv(string $path, string $productType): array
{
    $typeConfig = productTypeConfig($productType);
    $sample = (string) file_get_contents($path, false, null, 0, 65536);
    $encoding = detectEncoding($sample);
    $firstLine = strtok($sample, "\r\n") ?: '';
    $delimiter = detectDelimiter(toUtf8($firstLine, $encoding));
    $handle = fopen($path, 'rb');
    if ($handle === false) throw new RuntimeException('Δεν ήταν δυνατό να ανοιχτεί το αρχείο.');

    $rows = []; $issues = []; $line = 0; $headerSkipped = false;
    while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
        $line++;
        $row = array_map(fn($v) => trim(toUtf8((string) $v, $encoding)), $row);
        if ($line === 1 && isset($row[0])) $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
        if (count($row) === 1 && $row[0] === '') continue;
        if ($line === 1 && looksLikeHeader($row)) { $headerSkipped = true; continue; }

        $expectedFields = count($typeConfig['columns']);
        if (count($row) !== $expectedFields) {
            $issues[] = ['type' => 'error', 'line' => $line, 'cell' => 'ολόκληρη γραμμή',
                'reason' => 'Βρέθηκαν '.count($row).' πεδία αντί για '.$expectedFields.'.',
                'suggestion' => 'Ελέγξτε μήπως υπάρχει ασχολίαστο '.$delimiter.' μέσα σε κείμενο ή λείπει πεδίο.'];
            continue;
        }
        $row = normalizeInputRow($row, $typeConfig['indexes']);
        $price = parsePrice($row[7]);
        if ($price['error'] !== null) {
            $issues[] = ['type' => 'error', 'line' => $line, 'cell' => 'H (Τιμή)',
                'reason' => $price['error'], 'suggestion' => 'Χρησιμοποιήστε π.χ. 12,50 ή 12.50, χωρίς σύμβολα/κείμενο.'];
        } elseif ($row[7] !== $price['fixed']) {
            $issues[] = ['type' => 'fix', 'line' => $line, 'cell' => 'H (Τιμή)',
                'reason' => "Η τιμή '{$row[7]}' θα κανονικοποιηθεί.", 'suggestion' => "Μετατροπή σε {$price['fixed']} (τελεία και 2 δεκαδικά)."];
            $row[7] = $price['fixed'];
        }
        foreach ([0 => 'A (Εκδότης)', 3 => 'D (Τίτλος)', 6 => 'G (ISBN)'] as $index => $cell) {
            if ($row[$index] === '') $issues[] = ['type' => 'error', 'line' => $line, 'cell' => $cell,
                'reason' => 'Υποχρεωτικό πεδίο χωρίς τιμή.', 'suggestion' => 'Συμπληρώστε το κελί στο αρχικό CSV.'];
        }
        [$publisher, $language] = matchPublisher($row[0]);
        if ($productType === 'foreign_books' && $language === 'unassigned') $issues[] = ['type' => 'warning', 'line' => $line, 'cell' => 'A (Εκδότης)',
            'reason' => "Ο εκδότης '{$row[0]}' δεν υπάρχει στον πίνακα αντιστοίχισης.",
            'suggestion' => 'Θα διατηρηθεί όπως είναι, αλλά η κατηγορία δεν μπορεί να εξαχθεί αυτόματα.'];
        elseif ($productType === 'foreign_books' && $publisher !== $row[0]) $issues[] = ['type' => 'fix', 'line' => $line, 'cell' => 'A (Εκδότης)',
            'reason' => "Αναγνωρίστηκε ως '$publisher'.", 'suggestion' => "Χρήση της κανονικής ονομασίας '$publisher'."];
        if ($productType === 'foreign_books' && categoryId($row[1], $language) === '') $issues[] = ['type' => 'warning', 'line' => $line, 'cell' => 'B (Επίπεδο)',
            'reason' => "Δεν βρέθηκε κατηγορία PrestaShop για '{$row[1]}'.", 'suggestion' => 'Το πεδίο κατηγορίας θα μείνει κενό.'];

        $rows[] = ['line' => $line, 'data' => $row];
    }
    fclose($handle);
    return compact('rows', 'issues', 'encoding', 'delimiter', 'headerSkipped');
}

function outputRow(array $p, int $id, float $tax, float $wholesaleDiscount, string $productType): array
{
    [$publisher, $language] = matchPublisher($p[0]);
    if ($productType !== 'foreign_books') { $publisher = trim($p[0]); $language = 'unassigned'; }
    $price = parsePrice($p[7]);
    if ($price['value'] === null) throw new RuntimeException('Μη έγκυρη τιμή κατά τη δημιουργία.');
    $gross = (float) $price['value'];
    $title = trim($p[3]); $isbn = trim($p[6]); $series = str_replace('+', 'plus', trim($p[2]));
    $metaTitle = trim("$title $publisher $isbn");
    if (textLength($metaTitle) > 67) $metaTitle = textSlice($metaTitle, 0, 67);
    $metaDescription = $productType === 'clothes'
        ? trim("$title $publisher")
        : "Το βιβλίο $title των εκδόσεων $publisher";
    if (trim($p[5]) !== '') $metaDescription .= ' συγγραφέας '.explode(',', $p[5])[0];
    if (textLength($metaDescription) > 160) $metaDescription = textSlice($metaDescription, 0, 160);
    $slug = preg_replace('/[^\p{L}\p{N}-]+/u', '', str_replace([' ', '+'], '-', $title));
    $slug = trim((string) preg_replace('/-+/', '-', (string) $slug), '-');
    if (textLength($slug) > 150) $slug = textSlice($slug, 0, 150);
    $image = trim($p[9]) === '' ? 'https://sdvigpress.org/images/publi/_default.jpg' : trim($p[9]);
    $description = '<h2 class="book-title"><strong>'.htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</strong></h2><br />'
        .'<strong>Εκδότης:</strong> '.htmlspecialchars($publisher, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />'
        .'<strong>ISBN:</strong> '.htmlspecialchars($isbn, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />';
    if ($productType === 'foreign_books') {
        $description .= '<strong>Τάξη:</strong> '.htmlspecialchars($p[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />'
            .'<strong>Σειρά:</strong> '.htmlspecialchars($p[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />';
    }
    if ($productType === 'greek_books') {
        $description .= '<strong>Συγγραφέας:</strong> '.htmlspecialchars($p[5], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />';
    }
    $description .= '<br /><em>Περιγραφή</em><br />'.$p[8];

    $row = array_fill(0, count(OUTPUT_HEADERS), '');
    $values = [
        0 => (string) $id, 1 => '1', 2 => $title, 3 => categoryId($p[1], $language),
        4 => number_format($gross / $tax, 2, '.', ''), 5 => '14',
        6 => number_format($gross * (1 - $wholesaleDiscount), 2, '.', ''), 7 => '0', 9 => '20',
        12 => $isbn, 15 => $publisher, 23 => '520', 26 => '30', 34 => $publisher,
        35 => $description, 36 => trim($publisher.','.$series, ','), 37 => $metaTitle,
        38 => trim($publisher.','.$p[2], ','), 39 => $metaDescription, 40 => $slug,
        41 => 'Αποστολή σε 1-3 εργάσιμες ημέρες', 43 => $p[7] === '' ? '0' : '1',
        44 => date('Y-m-d'), 45 => date('Y-m-d'), 46 => '1', 47 => $image,
        50 => 'Έτος έκδοσης:'.$p[4], 52 => 'new'
    ];
    foreach ($values as $index => $value) $row[$index] = $value;
    return $row;
}

function saveUpload(): string
{
    if (!isset($_FILES['csv']) || !is_array($_FILES['csv'])) throw new RuntimeException('Δεν επιλέχθηκε αρχείο CSV.');
    if ($_FILES['csv']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Το upload απέτυχε (κωδικός '.$_FILES['csv']['error'].').');
    if ((int) $_FILES['csv']['size'] > 10 * 1024 * 1024) throw new RuntimeException('Το αρχείο ξεπερνά το όριο των 10 MB.');
    $token = bin2hex(random_bytes(16));
    $target = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'csvreformater_'.$token.'.csv';
    if (!move_uploaded_file($_FILES['csv']['tmp_name'], $target)) throw new RuntimeException('Δεν ήταν δυνατή η προσωρινή αποθήκευση.');
    $_SESSION['csv_files'][$token] = ['path' => $target, 'name' => basename((string) $_FILES['csv']['name']), 'time' => time()];
    return $token;
}

function tokenPath(string $token): string
{
    if (!preg_match('/^[a-f0-9]{32}$/', $token) || !isset($_SESSION['csv_files'][$token])) throw new RuntimeException('Η συνεδρία του αρχείου έληξε. Ανεβάστε το ξανά.');
    $path = $_SESSION['csv_files'][$token]['path'];
    if (!is_file($path)) throw new RuntimeException('Το προσωρινό αρχείο δεν βρέθηκε. Ανεβάστε το ξανά.');
    return $path;
}

function generateDownload(string $token, int $startId, float $tax, float $discount, string $productType): never
{
    $analysis = analyzeCsv(tokenPath($token), $productType);
    $errors = array_filter($analysis['issues'], fn($i) => $i['type'] === 'error');
    if ($errors !== []) throw new RuntimeException('Υπάρχουν σφάλματα που δεν διορθώνονται με ασφάλεια. Διορθώστε τα στο αρχικό CSV.');
    $output = fopen('php://temp', 'w+b');
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, OUTPUT_HEADERS, ';', '"', '\\');
    $id = $startId + 1;
    foreach ($analysis['rows'] as $record) fputcsv($output, outputRow($record['data'], $id++, $tax, $discount, $productType), ';', '"', '\\');
    rewind($output);
    $name = 'prestashop_'.date('Ymd_His').'.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$name.'"');
    header('X-Content-Type-Options: nosniff');
    fpassthru($output);
    fclose($output);
    exit;
}

$error = null; $analysis = null; $token = ''; $startId = 0; $tax = 1.06; $discount = 0.30; $productType = 'foreign_books';
try {
    $action = $_POST['action'] ?? '';
    if ($action === 'analyze') {
        $productType = (string) ($_POST['product_type'] ?? '');
        productTypeConfig($productType);
        $startId = filter_var($_POST['start_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($startId === false) throw new RuntimeException('Το αρχικό ID πρέπει να είναι ακέραιος αριθμός από 0 και πάνω.');
        $tax = (float) ($_POST['tax'] ?? 1.06); $discount = (float) ($_POST['discount'] ?? 30) / 100;
        if ($tax <= 0 || $tax > 5) throw new RuntimeException('Ο συντελεστής φόρου δεν είναι έγκυρος.');
        if ($discount < 0 || $discount > 1) throw new RuntimeException('Η έκπτωση χονδρικής πρέπει να είναι από 0% έως 100%.');
        $token = saveUpload(); $analysis = analyzeCsv(tokenPath($token), $productType);
    } elseif ($action === 'download') {
        $productType = (string) ($_POST['product_type'] ?? '');
        productTypeConfig($productType);
        $token = (string) ($_POST['token'] ?? ''); $startId = max(0, (int) ($_POST['start_id'] ?? 0));
        $tax = (float) ($_POST['tax'] ?? 1.06); $discount = (float) ($_POST['discount'] ?? .30);
        generateDownload($token, $startId, $tax, $discount, $productType);
    }
} catch (Throwable $e) { $error = $e->getMessage(); }

if ($token !== '' && $analysis === null && $error !== null) {
    try { $analysis = analyzeCsv(tokenPath($token), $productType); } catch (Throwable $ignored) {}
}
$errorCount = $analysis ? count(array_filter($analysis['issues'], fn($i) => $i['type'] === 'error')) : 0;
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>CSV → PrestaShop</title>
<style>
:root{font-family:system-ui,sans-serif;color:#172033;background:#f4f6fa}*{box-sizing:border-box}body{margin:0}.wrap{max-width:1100px;margin:36px auto;padding:0 18px}h1{margin-bottom:8px}.lead{color:#536078}.steps{display:flex;gap:10px;margin:24px 0}.step{flex:1;padding:12px;border-radius:10px;background:#e4e9f2}.step.on{background:#2357d8;color:white}.card{background:white;border-radius:14px;padding:22px;box-shadow:0 4px 20px #18223a12;margin-bottom:18px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:15px}label{display:block;font-weight:650}input,select{width:100%;margin-top:6px;padding:10px;border:1px solid #bcc5d4;border-radius:7px;background:white}.hint{font-size:.9rem;color:#647087}.btn{border:0;border-radius:8px;padding:11px 18px;background:#2357d8;color:white;font-weight:700;cursor:pointer}.error{border-left:5px solid #c72e3b;background:#fff0f1}.ok{border-left:5px solid #198754;background:#edf9f2}.stats{display:flex;gap:12px;flex-wrap:wrap}.pill{padding:7px 11px;border-radius:99px;background:#eef1f6}table{width:100%;border-collapse:collapse;font-size:.92rem}th,td{text-align:left;padding:9px;border-bottom:1px solid #e3e7ee;vertical-align:top}.t-error{color:#a51f2b}.t-fix{color:#7a5500}.t-warning{color:#8a4b08}.scroll{overflow:auto;max-height:450px}code{background:#eef1f6;padding:2px 5px;border-radius:4px}
</style>
</head><body><main class="wrap">
<h1>Μετατροπή CSV για PrestaShop</h1>
<p class="lead">Έλεγχος δεδομένων, σαφείς διορθώσεις και ασφαλής εξαγωγή UTF-8 CSV.</p>
<div class="steps"><div class="step <?= $analysis ? '' : 'on' ?>">1. Upload & ρυθμίσεις</div><div class="step <?= $analysis ? 'on' : '' ?>">2. Έλεγχος</div><div class="step <?= $analysis && $errorCount === 0 ? 'on' : '' ?>">3. Download</div></div>
<?php if ($error !== null): ?><div class="card error"><strong>Δεν ολοκληρώθηκε:</strong> <?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($analysis === null): ?>
<form class="card" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="analyze">
<div class="grid">
<label>Είδος προϊόντων<select name="product_type" id="product-type" required><?php foreach (PRODUCT_TYPES as $key => $config): ?><option value="<?= $key ?>"><?= htmlspecialchars($config['label']) ?></option><?php endforeach; ?></select><span class="hint">Όλο το αρχείο πρέπει να περιέχει το ίδιο είδος</span></label>
<label>Αρχείο CSV<input type="file" name="csv" accept=".csv,text/csv" required><span class="hint">Έως 10 MB</span></label>
<label>Τελευταίο υπάρχον ID<input type="number" name="start_id" min="0" value="0" required><span class="hint">Το πρώτο νέο προϊόν θα πάρει +1</span></label>
<label>Συντελεστής ΦΠΑ<input type="number" name="tax" min="0.01" max="5" step="0.01" value="1.06" required><span class="hint">1.06 σημαίνει ΦΠΑ 6%</span></label>
<label>Έκπτωση χονδρικής %<input type="number" name="discount" min="0" max="100" step="0.01" value="30" required></label>
</div><p><button class="btn" type="submit">Έλεγχος αρχείου</button></p>
<p class="hint">Αναμενόμενη σειρά: <strong id="expected-columns"></strong>. Οι πολλές εικόνες γράφονται στο ίδιο πεδίο, χωρισμένες με κόμμα. Αν delimiter είναι επίσης το κόμμα, το πεδίο εικόνων πρέπει να είναι μέσα σε διπλά εισαγωγικά. Ο delimiter ανιχνεύεται αυτόματα.</p>
</form>
<?php else: ?>
<section class="card <?= $errorCount ? 'error' : 'ok' ?>">
<h2><?= $errorCount ? 'Χρειάζονται διορθώσεις' : 'Το αρχείο είναι έτοιμο' ?></h2>
<div class="stats"><span class="pill"><?= count($analysis['rows']) ?> προϊόντα</span><span class="pill"><?= $errorCount ?> σφάλματα</span><span class="pill">Encoding: <?= htmlspecialchars($analysis['encoding']) ?></span><span class="pill">Delimiter: <code><?= $analysis['delimiter'] === "\t" ? 'TAB' : htmlspecialchars($analysis['delimiter']) ?></code></span></div>
<p class="hint">Οι ασφαλείς διορθώσεις (τιμές και ονομασίες εκδοτών) εφαρμόζονται αυτόματα στην έξοδο. Στο τελικό CSV οι δεκαδικοί γράφονται με τελεία και δύο ψηφία, π.χ. <code>12.50</code>.</p>
</section>
<?php if ($analysis['issues']): ?><section class="card"><h2>Αναλυτική αναφορά</h2><div class="scroll"><table><thead><tr><th>Τύπος</th><th>Γραμμή</th><th>Κελί</th><th>Ακριβής λόγος</th><th>Πρόταση</th></tr></thead><tbody>
<?php foreach ($analysis['issues'] as $issue): ?><tr><td class="t-<?= $issue['type'] ?>"><?= ['error'=>'Σφάλμα','fix'=>'Διόρθωση','warning'=>'Προειδοποίηση'][$issue['type']] ?></td><td><?= $issue['line'] ?></td><td><?= htmlspecialchars($issue['cell']) ?></td><td><?= htmlspecialchars($issue['reason']) ?></td><td><?= htmlspecialchars($issue['suggestion']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></section><?php endif; ?>
<section class="card">
<?php if ($errorCount === 0): ?><form method="post"><input type="hidden" name="action" value="download"><input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>"><input type="hidden" name="product_type" value="<?= htmlspecialchars($productType) ?>"><input type="hidden" name="start_id" value="<?= $startId ?>"><input type="hidden" name="tax" value="<?= $tax ?>"><input type="hidden" name="discount" value="<?= $discount ?>"><button class="btn" type="submit">Download PrestaShop CSV</button></form><?php else: ?><p>Διορθώστε τα κόκκινα σφάλματα στο αρχικό αρχείο και ανεβάστε το ξανά. Δεν γίνεται αυθαίρετη αλλαγή όταν η σωστή τιμή δεν είναι βέβαιη.</p><?php endif; ?>
<p><a href="index.php">Έλεγχος άλλου αρχείου</a></p></section>
<?php endif; ?>
</main>
<?php if ($analysis === null): ?><script>
const productColumns = <?= json_encode(array_map(fn($config) => $config['columns'], PRODUCT_TYPES), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const productType = document.getElementById('product-type');
const expectedColumns = document.getElementById('expected-columns');
const updateColumns = () => { expectedColumns.textContent = productColumns[productType.value].join(', '); };
productType.addEventListener('change', updateColumns); updateColumns();
</script><?php endif; ?>
</body></html>
