<?php
declare(strict_types=1);

session_start();

const INPUT_COLUMNS = ['Εκδότης', 'Επίπεδο', 'Σειρά', 'Τίτλος', 'Έτος', 'Συγγραφέας', 'ISBN', 'Τιμή', 'Περιγραφή', 'Εικόνα', 'Σύνθεση', 'Κατηγορία προϊόντος'];

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
        'columns' => ['Κατασκευαστής', 'Τίτλος', 'Κωδικός', 'Σύνθεση', 'Κατηγορία', 'Τιμή', 'Περιγραφή', 'Εικόνες'],
        'indexes' => [0, 3, 6, 10, 11, 7, 8, 9],
    ],
];

const PRESTASHOP_VERSIONS = [
    '1.7' => [
        'label' => 'PrestaShop 1.7',
        'template' => 'products_import-presta17.csv',
        'fields' => ['id'=>0, 'active'=>1, 'title'=>2, 'category'=>3, 'price'=>4, 'tax'=>5, 'wholesale'=>6, 'onsale'=>7, 'discount'=>9, 'reference'=>12, 'publisher'=>15, 'ean'=>16, 'weight'=>22, 'quantity'=>25, 'summary'=>33, 'description'=>34, 'tags'=>35, 'meta_title'=>36, 'meta_keywords'=>37, 'meta_description'=>38, 'slug'=>39, 'in_stock'=>40, 'available'=>42, 'available_date'=>43, 'creation_date'=>44, 'show_price'=>45, 'images'=>46, 'feature'=>49, 'condition'=>51],
    ],
    '8' => [
        'label' => 'PrestaShop 8',
        'template' => 'products_import-presta8.csv',
        'fields' => ['id'=>0, 'active'=>1, 'title'=>2, 'category'=>3, 'price'=>4, 'tax'=>5, 'wholesale'=>6, 'onsale'=>7, 'discount'=>9, 'reference'=>12, 'publisher'=>15, 'ean'=>16, 'weight'=>22, 'quantity'=>25, 'summary'=>33, 'description'=>34, 'tags'=>35, 'meta_title'=>36, 'meta_keywords'=>37, 'meta_description'=>38, 'slug'=>39, 'in_stock'=>40, 'available'=>42, 'available_date'=>43, 'creation_date'=>44, 'show_price'=>45, 'images'=>46, 'feature'=>49, 'condition'=>51],
    ],
    '9' => [
        'label' => 'PrestaShop 9',
        'template' => 'products_import-presta9.csv',
        'fields' => ['id'=>0, 'active'=>1, 'title'=>2, 'category'=>3, 'price'=>4, 'tax'=>5, 'wholesale'=>6, 'onsale'=>7, 'discount'=>9, 'reference'=>12, 'publisher'=>15, 'ean'=>16, 'weight'=>24, 'quantity'=>27, 'summary'=>36, 'description'=>37, 'tags'=>38, 'meta_title'=>39, 'meta_description'=>40, 'slug'=>41, 'in_stock'=>42, 'available'=>44, 'available_date'=>45, 'creation_date'=>46, 'show_price'=>47, 'images'=>48, 'feature'=>51, 'condition'=>53],
    ],
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
    if (function_exists('mb_strlen')) return mb_strlen($value, 'UTF-8');
    $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    return is_array($characters) ? count($characters) : strlen($value);
}

function textSlice(string $value, int $start, ?int $length = null): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, $start, $length, 'UTF-8');
    }
    $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    if (!is_array($characters)) return $length === null ? substr($value, $start) : substr($value, $start, $length);
    return implode('', array_slice($characters, $start, $length));
}

function cleanText(string $value): string
{
    $value = strip_tags($value);
    $value = preg_replace('/[\x{0000}-\x{0008}\x{000B}\x{000C}\x{000E}-\x{001F}\x{007F}]/u', '', $value) ?? $value;
    return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
}

function limitAtWord(string $value, int $limit): string
{
    $value = cleanText($value);
    if (textLength($value) <= $limit) return $value;
    $candidate = textSlice($value, 0, $limit + 1);
    $candidate = preg_replace('/\s+\S*$/u', '', $candidate) ?? $candidate;
    if ($candidate === '') $candidate = textSlice($value, 0, $limit);
    return preg_replace('/[\s\-–—,.;:!?]+$/u', '', $candidate) ?? $candidate;
}

function splitTitle(string $value, int $limit = 128): array
{
    $original = cleanText($value);
    $title = limitAtWord($original, $limit);
    $overflow = textLength($original) > textLength($title)
        ? (preg_replace('/^[\s\-–—,.;:!?]+/u', '', textSlice($original, textLength($title))) ?? '')
        : '';
    return [$title, $overflow, $original];
}

function friendlyUrl(string $value, int $limit = 128): string
{
    $greek = [
        'α'=>'a','ά'=>'a','β'=>'v','γ'=>'g','δ'=>'d','ε'=>'e','έ'=>'e','ζ'=>'z','η'=>'i','ή'=>'i',
        'θ'=>'th','ι'=>'i','ί'=>'i','ϊ'=>'i','ΐ'=>'i','κ'=>'k','λ'=>'l','μ'=>'m','ν'=>'n','ξ'=>'x',
        'ο'=>'o','ό'=>'o','π'=>'p','ρ'=>'r','σ'=>'s','ς'=>'s','τ'=>'t','υ'=>'y','ύ'=>'y','ϋ'=>'y','ΰ'=>'y',
        'φ'=>'f','χ'=>'ch','ψ'=>'ps','ω'=>'o','ώ'=>'o',
    ];
    $value = cleanText(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $value = str_replace(
        ['Α','Ά','Β','Γ','Δ','Ε','Έ','Ζ','Η','Ή','Θ','Ι','Ί','Ϊ','Κ','Λ','Μ','Ν','Ξ','Ο','Ό','Π','Ρ','Σ','Τ','Υ','Ύ','Ϋ','Φ','Χ','Ψ','Ω','Ώ'],
        ['α','ά','β','γ','δ','ε','έ','ζ','η','ή','θ','ι','ί','ϊ','κ','λ','μ','ν','ξ','ο','ό','π','ρ','σ','τ','υ','ύ','ϋ','φ','χ','ψ','ω','ώ'],
        $value
    );
    $value = strtr(lowerText($value), $greek);
    if (function_exists('iconv')) {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($ascii !== false) $value = $ascii;
    }
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
    $value = trim(preg_replace('/-+/', '-', $value) ?? $value, '-');
    $value = textSlice($value, 0, $limit);
    return rtrim($value, '-');
}

function cleanEan(string $reference): string
{
    return textSlice(preg_replace('/\D+/', '', $reference) ?? '', 0, 13);
}

function cleanImages(string $value): string
{
    if (trim($value) === '') return 'https://sdvigpress.org/images/publi/_default.jpg';
    $images = array_filter(array_map(fn($url) => textSlice(trim($url), 0, 2048), explode(',', $value)));
    return implode(',', $images);
}

function geminiApiKey(): string
{
    return trim((string) (getenv('GOOGLE_API_KEY') ?: getenv('GEMINI_API_KEY') ?: ''));
}

function translateClothingBatch(array $items): array
{
    $apiKey = geminiApiKey();
    if ($apiKey === '') throw new RuntimeException('Δεν έχει οριστεί GOOGLE_API_KEY ή GEMINI_API_KEY στον server.');
    if (!function_exists('curl_init')) throw new RuntimeException('Η επέκταση PHP cURL απαιτείται για τη μετάφραση μέσω Google AI.');
    $model = trim((string) (getenv('GEMINI_MODEL') ?: 'gemini-3.8-flash'));
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $model)) throw new RuntimeException('Το GEMINI_MODEL δεν είναι έγκυρο.');

    $inputJson = json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $prompt = "Translate the following clothing product data from Greek to natural ecommerce English. "
        ."Translate only title and description. Preserve brand names, product codes, HTML tags, URLs, numbers and formatting exactly. "
        ."Do not add information. Return every id exactly once. Input JSON:\n".$inputJson;
    $payload = [
        'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'temperature' => 0.1,
            'maxOutputTokens' => 8192,
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'ARRAY',
                'items' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'id' => ['type' => 'INTEGER'],
                        'title' => ['type' => 'STRING'],
                        'description' => ['type' => 'STRING'],
                    ],
                    'required' => ['id', 'title', 'description'],
                ],
            ],
        ],
    ];
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $curl = curl_init('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent');
    if ($curl === false) throw new RuntimeException('Δεν ήταν δυνατή η αρχικοποίηση σύνδεσης με το Google AI.');
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: '.$apiKey],
        CURLOPT_POSTFIELDS => $body,
    ]);
    $response = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);
    if (!is_string($response)) throw new RuntimeException('Η σύνδεση με το Google AI απέτυχε: '.$curlError);
    $decoded = json_decode($response, true);
    if ($status < 200 || $status >= 300) {
        $message = is_array($decoded) ? (string) ($decoded['error']['message'] ?? 'Άγνωστο σφάλμα API') : 'Άγνωστο σφάλμα API';
        throw new RuntimeException('Το Google AI επέστρεψε σφάλμα (HTTP '.$status.'): '.$message);
    }
    $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
    $translations = is_string($text) ? json_decode($text, true) : null;
    if (!is_array($translations)) throw new RuntimeException('Το Google AI δεν επέστρεψε έγκυρη δομημένη μετάφραση.');
    return $translations;
}

function processSingleRequestWithFallback(array $pending, int $fallbackSize, callable $processor): void
{
    if ($pending === []) return;
    try {
        $processor($pending);
    } catch (Throwable $exception) {
        if (count($pending) <= $fallbackSize) throw $exception;
        foreach (array_chunk($pending, $fallbackSize, true) as $chunk) $processor($chunk);
    }
}

function translateClothingRows(array $records): array
{
    if (!isset($_SESSION['clothing_translations']) || !is_array($_SESSION['clothing_translations'])) {
        $_SESSION['clothing_translations'] = [];
    }
    $pending = []; $recordKeys = [];
    foreach ($records as $index => $record) {
        $data = $record['data'];
        $key = hash('sha256', (string) $data[3]."\0".(string) $data[8]);
        $recordKeys[$index] = $key;
        if (!isset($_SESSION['clothing_translations'][$key])) {
            $pending[$key] = ['title' => (string) $data[3], 'description' => (string) $data[8]];
        }
    }
    $processChunk = function (array $chunk): void {
        $request = []; $ids = [];
        foreach ($chunk as $key => $texts) {
            $id = count($request); $ids[$id] = $key;
            $request[] = ['id' => $id, 'title' => $texts['title'], 'description' => $texts['description']];
        }
        $translated = translateClothingBatch($request);
        $received = [];
        foreach ($translated as $item) {
            if (!is_array($item) || !isset($item['id'], $item['title'], $item['description'])) continue;
            $id = (int) $item['id'];
            if (!isset($ids[$id])) continue;
            $_SESSION['clothing_translations'][$ids[$id]] = [
                'title' => trim((string) $item['title']),
                'description' => trim((string) $item['description']),
            ];
            $received[$id] = true;
        }
        if (count($received) !== count($request)) throw new RuntimeException('Δεν μεταφράστηκαν όλα τα προϊόντα από το Google AI.');
    };
    processSingleRequestWithFallback($pending, 20, $processChunk);
    foreach ($records as $index => &$record) {
        $translation = $_SESSION['clothing_translations'][$recordKeys[$index]] ?? null;
        if (!is_array($translation)) throw new RuntimeException('Λείπει μετάφραση για προϊόν του αρχείου.');
        $record['data'][3] = $translation['title'];
        $record['data'][8] = $translation['description'];
    }
    unset($record);
    return $records;
}

function translateCompositionBatch(array $items): array
{
    $apiKey = geminiApiKey();
    if ($apiKey === '') throw new RuntimeException('Δεν έχει οριστεί GOOGLE_API_KEY ή GEMINI_API_KEY στον server.');
    if (!function_exists('curl_init')) throw new RuntimeException('Η επέκταση PHP cURL απαιτείται για τη μετάφραση μέσω Google AI.');
    $model = trim((string) (getenv('GEMINI_MODEL') ?: 'gemini-3.8-flash'));
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $model)) throw new RuntimeException('Το GEMINI_MODEL δεν είναι έγκυρο.');

    $inputJson = json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $prompt = "Translate only the clothing material composition values from English to natural Greek. "
        ."Preserve percentages, numbers, fiber abbreviations and separators exactly. Do not add information. "
        ."Return every id exactly once. Input JSON:\n".$inputJson;
    $payload = [
        'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'temperature' => 0.1,
            'maxOutputTokens' => 4096,
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'ARRAY',
                'items' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'id' => ['type' => 'INTEGER'],
                        'composition' => ['type' => 'STRING'],
                    ],
                    'required' => ['id', 'composition'],
                ],
            ],
        ],
    ];
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $curl = curl_init('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent');
    if ($curl === false) throw new RuntimeException('Δεν ήταν δυνατή η αρχικοποίηση σύνδεσης με το Google AI.');
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: '.$apiKey],
        CURLOPT_POSTFIELDS => $body,
    ]);
    $response = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);
    if (!is_string($response)) throw new RuntimeException('Η σύνδεση με το Google AI απέτυχε: '.$curlError);
    $decoded = json_decode($response, true);
    if ($status < 200 || $status >= 300) {
        $message = is_array($decoded) ? (string) ($decoded['error']['message'] ?? 'Άγνωστο σφάλμα API') : 'Άγνωστο σφάλμα API';
        throw new RuntimeException('Το Google AI επέστρεψε σφάλμα (HTTP '.$status.'): '.$message);
    }
    $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
    $translations = is_string($text) ? json_decode($text, true) : null;
    if (!is_array($translations)) throw new RuntimeException('Το Google AI δεν επέστρεψε έγκυρη μετάφραση σύνθεσης.');
    return $translations;
}

function translateCompositionRows(array $records): array
{
    if (!isset($_SESSION['composition_translations']) || !is_array($_SESSION['composition_translations'])) {
        $_SESSION['composition_translations'] = [];
    }
    $pending = []; $recordKeys = [];
    foreach ($records as $index => $record) {
        $composition = trim((string) $record['data'][10]);
        if ($composition === '') continue;
        $key = hash('sha256', $composition);
        $recordKeys[$index] = $key;
        if (!isset($_SESSION['composition_translations'][$key])) $pending[$key] = $composition;
    }
    $processChunk = function (array $chunk): void {
        $request = []; $ids = [];
        foreach ($chunk as $key => $composition) {
            $id = count($request); $ids[$id] = $key;
            $request[] = ['id' => $id, 'composition' => $composition];
        }
        $translated = translateCompositionBatch($request);
        $received = [];
        foreach ($translated as $item) {
            if (!is_array($item) || !isset($item['id'], $item['composition'])) continue;
            $id = (int) $item['id'];
            if (!isset($ids[$id])) continue;
            $_SESSION['composition_translations'][$ids[$id]] = trim((string) $item['composition']);
            $received[$id] = true;
        }
        if (count($received) !== count($request)) throw new RuntimeException('Δεν μεταφράστηκαν όλες οι συνθέσεις από το Google AI.');
    };
    processSingleRequestWithFallback($pending, 30, $processChunk);
    foreach ($records as $index => &$record) {
        if (!isset($recordKeys[$index])) continue;
        $translation = $_SESSION['composition_translations'][$recordKeys[$index]] ?? null;
        if (!is_string($translation)) throw new RuntimeException('Λείπει μετάφραση σύνθεσης για προϊόν του αρχείου.');
        $record['data'][10] = $translation;
    }
    unset($record);
    return $records;
}

function normalizeGreekTitleBatch(array $items): array
{
    $apiKey = geminiApiKey();
    if ($apiKey === '') throw new RuntimeException('Δεν έχει οριστεί GOOGLE_API_KEY ή GEMINI_API_KEY στον server.');
    if (!function_exists('curl_init')) throw new RuntimeException('Η επέκταση PHP cURL απαιτείται για τη διόρθωση τίτλων μέσω Google AI.');
    $model = trim((string) (getenv('GEMINI_MODEL') ?: 'gemini-3.8-flash'));
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $model)) throw new RuntimeException('Το GEMINI_MODEL δεν είναι έγκυρο.');

    $inputJson = json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $prompt = "Convert the following all-uppercase Greek clothing product titles to natural Greek sentence case. "
        ."Restore correct Greek accents. Keep the first letter uppercase and preserve proper nouns, brand names, Latin text, product codes and numbers. "
        ."Do not translate, paraphrase, add or remove words. Return every id exactly once. Input JSON:\n".$inputJson;
    $payload = [
        'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'temperature' => 0.1,
            'maxOutputTokens' => 4096,
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'ARRAY',
                'items' => [
                    'type' => 'OBJECT',
                    'properties' => ['id' => ['type' => 'INTEGER'], 'title' => ['type' => 'STRING']],
                    'required' => ['id', 'title'],
                ],
            ],
        ],
    ];
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $curl = curl_init('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent');
    if ($curl === false) throw new RuntimeException('Δεν ήταν δυνατή η αρχικοποίηση σύνδεσης με το Google AI.');
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: '.$apiKey],
        CURLOPT_POSTFIELDS => $body,
    ]);
    $response = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);
    if (!is_string($response)) throw new RuntimeException('Η σύνδεση με το Google AI απέτυχε: '.$curlError);
    $decoded = json_decode($response, true);
    if ($status < 200 || $status >= 300) {
        $message = is_array($decoded) ? (string) ($decoded['error']['message'] ?? 'Άγνωστο σφάλμα API') : 'Άγνωστο σφάλμα API';
        throw new RuntimeException('Το Google AI επέστρεψε σφάλμα (HTTP '.$status.'): '.$message);
    }
    $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
    $translations = is_string($text) ? json_decode($text, true) : null;
    if (!is_array($translations)) throw new RuntimeException('Το Google AI δεν επέστρεψε έγκυρη διόρθωση τίτλων.');
    return $translations;
}

function isAllUppercaseTitle(string $title): bool
{
    $title = cleanText($title);
    preg_match_all('/\p{Lu}/u', $title, $uppercase);
    preg_match_all('/\p{Ll}/u', $title, $lowercase);
    $upperCount = count($uppercase[0]); $lowerCount = count($lowercase[0]);
    return $upperCount >= 2 && $upperCount / max(1, $upperCount + $lowerCount) >= 0.8;
}

function normalizeGreekClothingTitles(array $records): array
{
    if (!isset($_SESSION['normalized_greek_titles']) || !is_array($_SESSION['normalized_greek_titles'])) {
        $_SESSION['normalized_greek_titles'] = [];
    }
    $pending = []; $recordKeys = [];
    foreach ($records as $index => $record) {
        $title = trim((string) $record['data'][3]);
        if (!isAllUppercaseTitle($title)) continue;
        $key = hash('sha256', $title);
        $recordKeys[$index] = $key;
        if (!isset($_SESSION['normalized_greek_titles'][$key])) $pending[$key] = $title;
    }
    $processChunk = function (array $chunk): void {
        $request = []; $ids = [];
        foreach ($chunk as $key => $title) {
            $id = count($request); $ids[$id] = $key;
            $request[] = ['id' => $id, 'title' => $title];
        }
        $normalized = normalizeGreekTitleBatch($request);
        $received = [];
        foreach ($normalized as $item) {
            if (!is_array($item) || !isset($item['id'], $item['title'])) continue;
            $id = (int) $item['id'];
            if (!isset($ids[$id])) continue;
            $_SESSION['normalized_greek_titles'][$ids[$id]] = trim((string) $item['title']);
            $received[$id] = true;
        }
        if (count($received) !== count($request)) throw new RuntimeException('Δεν διορθώθηκαν όλοι οι κεφαλαίοι τίτλοι από το Google AI.');
    };
    processSingleRequestWithFallback($pending, 30, $processChunk);
    foreach ($records as $index => &$record) {
        if (!isset($recordKeys[$index])) continue;
        $normalized = $_SESSION['normalized_greek_titles'][$recordKeys[$index]] ?? null;
        if (!is_string($normalized)) throw new RuntimeException('Λείπει διορθωμένος ελληνικός τίτλος για προϊόν του αρχείου.');
        $record['data'][3] = $normalized;
    }
    unset($record);
    return $records;
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

function looksLikeHeader(array $row, string $productType): bool
{
    $joined = lowerText(implode('|', $row));
    $hasReference = $productType === 'clothes'
        ? containsText($joined, 'κωδικ') || containsText($joined, 'reference')
        : containsText($joined, 'isbn');
    return $hasReference && (containsText($joined, 'τιμ') || containsText($joined, 'price'));
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

function prestashopConfig(string $version): array
{
    if (!isset(PRESTASHOP_VERSIONS[$version])) throw new RuntimeException('Επιλέξτε έγκυρη έκδοση PrestaShop.');
    return PRESTASHOP_VERSIONS[$version];
}

function prestashopHeaders(string $version): array
{
    static $cache = [];
    if (isset($cache[$version])) return $cache[$version];
    $config = prestashopConfig($version);
    $path = __DIR__.DIRECTORY_SEPARATOR.$config['template'];
    $handle = fopen($path, 'rb');
    if ($handle === false) throw new RuntimeException('Δεν βρέθηκε το πρότυπο CSV για PrestaShop '.$version.'.');
    $headers = fgetcsv($handle, 0, ';', '"', '\\');
    fclose($handle);
    if (!is_array($headers) || $headers === []) throw new RuntimeException('Το πρότυπο CSV για PrestaShop '.$version.' δεν έχει έγκυρη κεφαλίδα.');
    $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
    return $cache[$version] = $headers;
}

function normalizeInputRow(array $row, array $indexes): array
{
    $normalized = array_fill(0, count(INPUT_COLUMNS), '');
    foreach ($indexes as $source => $target) $normalized[$target] = $row[$source];
    return $normalized;
}

function readOdsRows(string $path, int $expectedColumns): array
{
    if (!class_exists('ZipArchive')) throw new RuntimeException('Η επέκταση PHP ZipArchive απαιτείται για αρχεία ODS.');
    if (!class_exists('DOMDocument')) throw new RuntimeException('Η επέκταση PHP DOM απαιτείται για αρχεία ODS.');

    $zip = new ZipArchive();
    if ($zip->open($path) !== true) throw new RuntimeException('Το αρχείο ODS δεν είναι έγκυρο ή δεν μπορεί να ανοιχτεί.');
    $stat = $zip->statName('content.xml');
    if (!is_array($stat) || (int) ($stat['size'] ?? 0) > 50 * 1024 * 1024) {
        $zip->close();
        throw new RuntimeException('Το ODS δεν περιέχει έγκυρο content.xml ή είναι υπερβολικά μεγάλο.');
    }
    $xml = $zip->getFromName('content.xml');
    $zip->close();
    if (!is_string($xml) || $xml === '') throw new RuntimeException('Δεν βρέθηκαν δεδομένα στο αρχείο ODS.');

    $previousErrors = libxml_use_internal_errors(true);
    $document = new DOMDocument();
    $loaded = $document->loadXML($xml, LIBXML_NONET | LIBXML_COMPACT);
    libxml_clear_errors();
    libxml_use_internal_errors($previousErrors);
    if (!$loaded) throw new RuntimeException('Το περιεχόμενο του ODS δεν είναι έγκυρο XML.');

    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
    $xpath->registerNamespace('table', 'urn:oasis:names:tc:opendocument:xmlns:table:1.0');
    $xpath->registerNamespace('text', 'urn:oasis:names:tc:opendocument:xmlns:text:1.0');
    $sheets = $xpath->query('//office:body/office:spreadsheet/table:table');
    if ($sheets === false || $sheets->length === 0) throw new RuntimeException('Το ODS δεν περιέχει φύλλο εργασίας.');
    $rowNodes = $xpath->query('.//table:table-row', $sheets->item(0));
    if ($rowNodes === false) throw new RuntimeException('Δεν ήταν δυνατή η ανάγνωση των γραμμών του ODS.');

    $rows = []; $sheetLine = 0;
    foreach ($rowNodes as $rowNode) {
        $repeatRows = max(1, min(10000, (int) $rowNode->getAttributeNS('urn:oasis:names:tc:opendocument:xmlns:table:1.0', 'number-rows-repeated')));
        $cells = $xpath->query('./table:table-cell | ./table:covered-table-cell', $rowNode);
        $row = [];
        if ($cells !== false) foreach ($cells as $cell) {
            $paragraphs = $xpath->query('.//text:p', $cell);
            $parts = [];
            if ($paragraphs !== false) foreach ($paragraphs as $paragraph) $parts[] = $paragraph->textContent;
            $value = trim(implode("\n", $parts));
            if ($value === '') {
                foreach (['string-value', 'value', 'date-value', 'time-value', 'boolean-value'] as $attribute) {
                    $candidate = $cell->getAttributeNS('urn:oasis:names:tc:opendocument:xmlns:office:1.0', $attribute);
                    if ($candidate !== '') { $value = $candidate; break; }
                }
            }
            $repeatCells = max(1, (int) $cell->getAttributeNS('urn:oasis:names:tc:opendocument:xmlns:table:1.0', 'number-columns-repeated'));
            $repeatCells = min($repeatCells, max(0, ($expectedColumns + 1) - count($row)));
            for ($i = 0; $i < $repeatCells; $i++) $row[] = $value;
            if (count($row) > $expectedColumns) break;
        }
        while (count($row) > $expectedColumns && end($row) === '') array_pop($row);
        $hasValue = count(array_filter($row, fn($value) => $value !== '')) > 0;
        if ($hasValue) while (count($row) < $expectedColumns) $row[] = '';
        for ($i = 0; $i < $repeatRows; $i++) {
            $sheetLine++;
            if ($hasValue) $rows[] = ['line' => $sheetLine, 'data' => $row];
        }
    }
    if ($rows === []) throw new RuntimeException('Το πρώτο φύλλο του ODS δεν περιέχει δεδομένα.');
    return $rows;
}

function analyzeInput(string $path, string $productType): array
{
    $typeConfig = productTypeConfig($productType);
    $extension = lowerText((string) pathinfo($path, PATHINFO_EXTENSION));
    $inputRows = [];
    if ($extension === 'ods') {
        $encoding = 'UTF-8'; $delimiter = 'κελιά ODS';
        $inputRows = readOdsRows($path, count($typeConfig['columns']));
    } else {
        $sample = (string) file_get_contents($path, false, null, 0, 65536);
        $encoding = detectEncoding($sample);
        $firstLine = strtok($sample, "\r\n") ?: '';
        $delimiter = detectDelimiter(toUtf8($firstLine, $encoding));
        $handle = fopen($path, 'rb');
        if ($handle === false) throw new RuntimeException('Δεν ήταν δυνατό να ανοιχτεί το αρχείο.');
        $line = 0;
        while (($data = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            $inputRows[] = ['line' => ++$line, 'data' => $data];
        }
        fclose($handle);
    }

    $rows = []; $issues = []; $headerSkipped = false; $firstDataRow = true;
    foreach ($inputRows as $inputRow) {
        $line = $inputRow['line']; $row = $inputRow['data'];
        $row = array_map(fn($v) => trim(toUtf8((string) $v, $encoding)), $row);
        if ($firstDataRow && isset($row[0])) $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
        if (count($row) === 1 && $row[0] === '') continue;
        if ($firstDataRow && looksLikeHeader($row, $productType)) { $headerSkipped = true; $firstDataRow = false; continue; }
        $firstDataRow = false;

        $expectedFields = count($typeConfig['columns']);
        if (count($row) !== $expectedFields) {
            $issues[] = ['type' => 'error', 'line' => $line, 'cell' => 'ολόκληρη γραμμή',
                'reason' => 'Βρέθηκαν '.count($row).' πεδία αντί για '.$expectedFields.'.',
                'suggestion' => $extension === 'ods' ? 'Ελέγξτε αν λείπει ή περισσεύει κελί στη συγκεκριμένη γραμμή.' : 'Ελέγξτε μήπως υπάρχει ασχολίαστο '.$delimiter.' μέσα σε κείμενο ή λείπει πεδίο.'];
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
        $titleToCheck = $productType === 'clothes'
            ? trim(cleanText($row[0]).' '.cleanText($row[3]).' '.cleanText($row[6]))
            : $row[3];
        if (textLength(cleanText($titleToCheck)) > 128) {
            [$shortTitle, $overflow] = splitTitle($titleToCheck);
            $issues[] = ['type' => 'fix', 'line' => $line, 'cell' => 'Τίτλος',
                'reason' => 'Ο τίτλος ξεπερνά το κοινό όριο των 128 χαρακτήρων.',
                'suggestion' => "Ο τίτλος θα γίνει '$shortTitle' και το υπόλοιπο '$overflow' θα μεταφερθεί στη σύντομη περιγραφή."];
        }
        if (textLength(cleanText($row[6])) > 64) {
            $issues[] = ['type' => 'fix', 'line' => $line, 'cell' => 'Reference',
                'reason' => 'Το reference ξεπερνά τους 64 χαρακτήρες.',
                'suggestion' => 'Θα περικοπεί σε όριο λέξης ώστε να είναι αποδεκτό από όλες τις εκδόσεις.'];
        }
        $requiredFields = $productType === 'clothes'
            ? [0 => 'Κατασκευαστής', 3 => 'Τίτλος', 6 => 'Κωδικός']
            : [0 => 'Εκδότης', 3 => 'Τίτλος', 6 => 'ISBN'];
        foreach ($requiredFields as $index => $cell) {
            if ($row[$index] === '') $issues[] = ['type' => 'error', 'line' => $line, 'cell' => $cell,
                'reason' => 'Υποχρεωτικό πεδίο χωρίς τιμή.', 'suggestion' => 'Συμπληρώστε το κελί στο αρχικό αρχείο.'];
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
    return compact('rows', 'issues', 'encoding', 'delimiter', 'headerSkipped');
}

function outputRow(array $p, int $id, float $tax, float $wholesaleDiscount, string $productType, string $prestashopVersion, string $outputLanguage = 'el'): array
{
    [$publisher, $language] = matchPublisher($p[0]);
    if ($productType !== 'foreign_books') { $publisher = trim($p[0]); $language = 'unassigned'; }
    $publisher = limitAtWord($publisher, 64);
    $price = parsePrice($p[7]);
    if ($price['value'] === null) throw new RuntimeException('Μη έγκυρη τιμή κατά τη δημιουργία.');
    $gross = (float) $price['value'];
    $reference = limitAtWord($p[6], 64);
    $generatedTitle = $productType === 'clothes'
        ? trim($publisher.' '.cleanText($p[3]).' '.$reference)
        : $p[3];
    [$title, $titleOverflow, $originalTitle] = splitTitle($generatedTitle);
    $ean = $productType === 'clothes' ? '' : cleanEan($p[6]);
    $series = limitAtWord(str_replace('+', 'plus', $p[2]), 32);
    $composition = limitAtWord($p[10], 255);
    $productCategory = limitAtWord($p[11], 128);
    $metaTitle = $productType === 'clothes'
        ? limitAtWord($originalTitle, 67)
        : limitAtWord(trim("$originalTitle $publisher $reference"), 67);
    $metaDescription = $productType === 'clothes'
        ? $originalTitle
        : "Το βιβλίο $originalTitle των εκδόσεων $publisher";
    if (trim($p[5]) !== '') $metaDescription .= ' συγγραφέας '.explode(',', $p[5])[0];
    $metaDescription = limitAtWord($metaDescription, 160);
    $slug = friendlyUrl($originalTitle);
    if ($slug === '') $slug = 'product-'.$id;
    $image = cleanImages($p[9]);
    $summary = $publisher;
    if ($titleOverflow !== '') $summary = trim($summary.' - '.$titleOverflow);
    $summary = limitAtWord($summary, 400);
    if ($productType === 'clothes') {
        $description = trim($p[8]);
        if ($composition !== '') {
            if ($description !== '') $description .= '<br /><br />';
            $compositionLabel = $outputLanguage === 'en' ? 'Composition' : 'Σύνθεση';
            $description .= '<strong>'.$compositionLabel.':</strong> '.htmlspecialchars($composition, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }
    } else {
        $description = '<h2 class="book-title"><strong>'.htmlspecialchars($originalTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</strong></h2><br />'
            .'<strong>Εκδότης:</strong> '.htmlspecialchars($publisher, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />'
            .'<strong>ISBN:</strong> '.htmlspecialchars($reference, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />';
        if ($productType === 'foreign_books') {
            $description .= '<strong>Τάξη:</strong> '.htmlspecialchars($p[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />'
                .'<strong>Σειρά:</strong> '.htmlspecialchars($p[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />';
        }
        if ($productType === 'greek_books') {
            $description .= '<strong>Συγγραφέας:</strong> '.htmlspecialchars($p[5], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br />';
        }
        $description .= '<strong>Διαθεσιμότητα:</strong> Διαθέσιμο<br />'
            .'<br /><em>Περιγραφή</em><br />'.$p[8];
    }

    $config = prestashopConfig($prestashopVersion);
    $row = array_fill(0, count(prestashopHeaders($prestashopVersion)), '');
    $values = [
        'id' => (string) $id, 'active' => '1', 'title' => $title,
        'category' => $productType === 'clothes' ? $productCategory : categoryId($p[1], $language),
        'price' => number_format($gross / $tax, 2, '.', ''), 'tax' => '14',
        'wholesale' => number_format($gross * (1 - $wholesaleDiscount), 2, '.', ''),
        'onsale' => '0', 'discount' => '20', 'reference' => $reference,
        'publisher' => $publisher, 'ean' => $ean,
        'weight' => '520', 'quantity' => '30', 'summary' => $summary,
        'description' => $description,
        'tags' => $productType === 'clothes'
            ? trim(limitAtWord($publisher, 32).','.limitAtWord($productCategory, 32), ',')
            : trim(limitAtWord($publisher, 32).','.$series, ','),
        'meta_title' => $metaTitle,
        'meta_keywords' => $productType === 'clothes'
            ? limitAtWord(trim($publisher.','.$productCategory, ','), 255)
            : limitAtWord(trim($publisher.','.$series, ','), 255),
        'meta_description' => $metaDescription, 'slug' => $slug,
        'in_stock' => 'Αποστολή σε 1-3 εργάσιμες ημέρες',
        'available' => $p[7] === '' ? '0' : '1', 'available_date' => date('Y-m-d'),
        'creation_date' => date('Y-m-d'), 'show_price' => '1', 'images' => $image,
        'feature' => $productType === 'clothes'
            ? limitAtWord('Σύνθεση:'.$composition, 255)
            : limitAtWord('Έτος έκδοσης:'.cleanText($p[4]), 255),
        'condition' => 'new',
    ];
    foreach ($values as $field => $value) {
        if (isset($config['fields'][$field])) $row[$config['fields'][$field]] = $value;
    }
    return $row;
}

function saveUpload(): string
{
    if (!isset($_FILES['csv']) || !is_array($_FILES['csv'])) throw new RuntimeException('Δεν επιλέχθηκε αρχείο CSV ή ODS.');
    if ($_FILES['csv']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Το upload απέτυχε (κωδικός '.$_FILES['csv']['error'].').');
    if ((int) $_FILES['csv']['size'] > 10 * 1024 * 1024) throw new RuntimeException('Το αρχείο ξεπερνά το όριο των 10 MB.');
    $originalName = basename((string) $_FILES['csv']['name']);
    $extension = lowerText((string) pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, ['csv', 'ods'], true)) throw new RuntimeException('Επιτρέπονται μόνο αρχεία CSV και ODS.');
    $token = bin2hex(random_bytes(16));
    $target = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'csvreformater_'.$token.'.'.$extension;
    if (!move_uploaded_file($_FILES['csv']['tmp_name'], $target)) throw new RuntimeException('Δεν ήταν δυνατή η προσωρινή αποθήκευση.');
    $_SESSION['csv_files'][$token] = ['path' => $target, 'name' => $originalName, 'time' => time()];
    return $token;
}

function tokenPath(string $token): string
{
    if (!preg_match('/^[a-f0-9]{32}$/', $token) || !isset($_SESSION['csv_files'][$token])) throw new RuntimeException('Η συνεδρία του αρχείου έληξε. Ανεβάστε το ξανά.');
    $path = $_SESSION['csv_files'][$token]['path'];
    if (!is_file($path)) throw new RuntimeException('Το προσωρινό αρχείο δεν βρέθηκε. Ανεβάστε το ξανά.');
    return $path;
}

function generateDownload(string $token, int $startId, float $tax, float $discount, string $productType, string $prestashopVersion, string $outputLanguage, bool $normalizeTitles): never
{
    $analysis = analyzeInput(tokenPath($token), $productType);
    $errors = array_filter($analysis['issues'], fn($i) => $i['type'] === 'error');
    if ($errors !== []) throw new RuntimeException('Υπάρχουν σφάλματα που δεν διορθώνονται με ασφάλεια. Διορθώστε τα στο αρχικό CSV.');
    if ($productType === 'clothes') {
        if (function_exists('set_time_limit')) @set_time_limit(300);
        if ($normalizeTitles) $analysis['rows'] = normalizeGreekClothingTitles($analysis['rows']);
        $analysis['rows'] = $outputLanguage === 'en'
            ? translateClothingRows($analysis['rows'])
            : translateCompositionRows($analysis['rows']);
    } elseif ($outputLanguage === 'en') {
        throw new RuntimeException('Η αγγλική εξαγωγή είναι διαθέσιμη μόνο για ρούχα.');
    }
    $output = fopen('php://temp', 'w+b');
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, prestashopHeaders($prestashopVersion), ';', '"', '\\');
    $id = $startId + 1;
    foreach ($analysis['rows'] as $record) fputcsv($output, outputRow($record['data'], $id++, $tax, $discount, $productType, $prestashopVersion, $outputLanguage), ';', '"', '\\');
    rewind($output);
    $name = 'prestashop_'.str_replace('.', '', $prestashopVersion).'_'.$outputLanguage.'_'.date('Ymd_His').'.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$name.'"');
    header('X-Content-Type-Options: nosniff');
    fpassthru($output);
    fclose($output);
    exit;
}

$error = null; $analysis = null; $token = ''; $startId = 0; $tax = 1.06; $discount = 0.30; $productType = 'foreign_books'; $prestashopVersion = '9'; $normalizeTitles = false;
try {
    $action = $_POST['action'] ?? '';
    if ($action === 'analyze') {
        $productType = (string) ($_POST['product_type'] ?? '');
        productTypeConfig($productType);
        $prestashopVersion = (string) ($_POST['prestashop_version'] ?? '');
        prestashopHeaders($prestashopVersion);
        $normalizeTitles = isset($_POST['normalize_titles']) && $_POST['normalize_titles'] === '1';
        $startId = filter_var($_POST['start_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($startId === false) throw new RuntimeException('Το αρχικό ID πρέπει να είναι ακέραιος αριθμός από 0 και πάνω.');
        $tax = (float) ($_POST['tax'] ?? 1.06); $discount = (float) ($_POST['discount'] ?? 30) / 100;
        if ($tax <= 0 || $tax > 5) throw new RuntimeException('Ο συντελεστής φόρου δεν είναι έγκυρος.');
        if ($discount < 0 || $discount > 1) throw new RuntimeException('Η έκπτωση χονδρικής πρέπει να είναι από 0% έως 100%.');
        $token = saveUpload(); $analysis = analyzeInput(tokenPath($token), $productType);
    } elseif ($action === 'download') {
        $productType = (string) ($_POST['product_type'] ?? '');
        productTypeConfig($productType);
        $prestashopVersion = (string) ($_POST['prestashop_version'] ?? '');
        prestashopHeaders($prestashopVersion);
        $token = (string) ($_POST['token'] ?? ''); $startId = max(0, (int) ($_POST['start_id'] ?? 0));
        $tax = (float) ($_POST['tax'] ?? 1.06); $discount = (float) ($_POST['discount'] ?? .30);
        $outputLanguage = (string) ($_POST['output_language'] ?? 'el');
        if (!in_array($outputLanguage, ['el', 'en'], true)) throw new RuntimeException('Η γλώσσα εξαγωγής δεν είναι έγκυρη.');
        $normalizeTitles = isset($_POST['normalize_titles']) && $_POST['normalize_titles'] === '1';
        generateDownload($token, $startId, $tax, $discount, $productType, $prestashopVersion, $outputLanguage, $normalizeTitles);
    }
} catch (Throwable $e) { $error = $e->getMessage(); }

if ($token !== '' && $analysis === null && $error !== null) {
    try { $analysis = analyzeInput(tokenPath($token), $productType); } catch (Throwable $ignored) {}
}
$errorCount = $analysis ? count(array_filter($analysis['issues'], fn($i) => $i['type'] === 'error')) : 0;
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>CSV / ODS → PrestaShop</title>
<style>
:root{font-family:system-ui,sans-serif;color:#172033;background:#f4f6fa}*{box-sizing:border-box}body{margin:0}.wrap{max-width:1100px;margin:36px auto;padding:0 18px}h1{margin-bottom:8px}.lead{color:#536078}.steps{display:flex;gap:10px;margin:24px 0}.step{flex:1;padding:12px;border-radius:10px;background:#e4e9f2}.step.on{background:#2357d8;color:white}.card{background:white;border-radius:14px;padding:22px;box-shadow:0 4px 20px #18223a12;margin-bottom:18px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:15px}label{display:block;font-weight:650}input,select{width:100%;margin-top:6px;padding:10px;border:1px solid #bcc5d4;border-radius:7px;background:white}.version-field{border:0;padding:0;margin:0;min-width:0}.version-field legend{font-weight:650;margin-bottom:6px}.version-options{display:flex;gap:7px;flex-wrap:wrap}.version-option{display:flex;align-items:center;gap:6px;padding:9px 10px;border:1px solid #bcc5d4;border-radius:7px;background:white;cursor:pointer}.version-option input,.check-option input{width:auto;margin:0;padding:0}.check-option{display:flex;align-items:center;gap:8px;padding:10px;border:1px solid #bcc5d4;border-radius:7px;background:white}.hint{font-size:.9rem;color:#647087}.btn{border:0;border-radius:8px;padding:11px 18px;background:#2357d8;color:white;font-weight:700;cursor:pointer}.download-actions{display:flex;gap:10px;flex-wrap:wrap}.error{border-left:5px solid #c72e3b;background:#fff0f1}.ok{border-left:5px solid #198754;background:#edf9f2}.stats{display:flex;gap:12px;flex-wrap:wrap}.pill{padding:7px 11px;border-radius:99px;background:#eef1f6}table{width:100%;border-collapse:collapse;font-size:.92rem}th,td{text-align:left;padding:9px;border-bottom:1px solid #e3e7ee;vertical-align:top}.t-error{color:#a51f2b}.t-fix{color:#7a5500}.t-warning{color:#8a4b08}.scroll{overflow:auto;max-height:450px}code{background:#eef1f6;padding:2px 5px;border-radius:4px}
</style>
</head><body><main class="wrap">
<h1>Μετατροπή CSV ή ODS για PrestaShop</h1>
<p class="lead">Έλεγχος δεδομένων από CSV ή το πρώτο φύλλο ODS και ασφαλής εξαγωγή UTF-8 CSV.</p>
<div class="steps"><div class="step <?= $analysis ? '' : 'on' ?>">1. Upload & ρυθμίσεις</div><div class="step <?= $analysis ? 'on' : '' ?>">2. Έλεγχος</div><div class="step <?= $analysis && $errorCount === 0 ? 'on' : '' ?>">3. Download</div></div>
<?php if ($error !== null): ?><div class="card error"><strong>Δεν ολοκληρώθηκε:</strong> <?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($analysis === null): ?>
<form class="card" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="analyze">
<div class="grid">
<label>Είδος προϊόντων<select name="product_type" id="product-type" required><?php foreach (PRODUCT_TYPES as $key => $config): ?><option value="<?= $key ?>"><?= htmlspecialchars($config['label']) ?></option><?php endforeach; ?></select><span class="hint">Όλο το αρχείο πρέπει να περιέχει το ίδιο είδος</span></label>
<fieldset class="version-field"><legend>Έκδοση PrestaShop</legend><div class="version-options"><?php foreach (PRESTASHOP_VERSIONS as $key => $config): $versionKey = (string) $key; ?><label class="version-option"><input type="radio" name="prestashop_version" value="<?= htmlspecialchars($versionKey) ?>"<?= $versionKey === '9' ? ' checked' : '' ?> required><?= htmlspecialchars($config['label']) ?></label><?php endforeach; ?></div><span class="hint">Καθορίζει τις στήλες και τη σειρά του εξαγόμενου CSV</span></fieldset>
<div id="clothing-options" hidden><label class="check-option"><input type="checkbox" name="normalize_titles" value="1"> Διόρθωση κεφαλαίων τίτλων με AI</label><span class="hint">Μετατρέπει μόνο τους τίτλους που είναι κυρίως κεφαλαίοι σε φυσικά ελληνικά με τόνους.</span></div>
<label>Αρχείο CSV ή ODS<input type="file" name="csv" accept=".csv,.ods,text/csv,application/vnd.oasis.opendocument.spreadsheet" required><span class="hint">Έως 10 MB · Στο ODS διαβάζεται το πρώτο φύλλο</span></label>
<label>Τελευταίο υπάρχον ID<input type="number" name="start_id" min="0" value="0" required><span class="hint">Το πρώτο νέο προϊόν θα πάρει +1</span></label>
<label>Συντελεστής ΦΠΑ<input type="number" name="tax" min="0.01" max="5" step="0.01" value="1.06" required><span class="hint">1.06 σημαίνει ΦΠΑ 6%</span></label>
<label>Έκπτωση χονδρικής %<input type="number" name="discount" min="0" max="100" step="0.01" value="30" required></label>
</div><p><button class="btn" type="submit">Έλεγχος αρχείου</button></p>
<p class="hint">Αναμενόμενη σειρά: <strong id="expected-columns"></strong>. Οι πολλές εικόνες γράφονται στο ίδιο πεδίο, χωρισμένες με κόμμα. Αν delimiter είναι επίσης το κόμμα, το πεδίο εικόνων πρέπει να είναι μέσα σε διπλά εισαγωγικά. Ο delimiter ανιχνεύεται αυτόματα.</p>
</form>
<?php else: ?>
<section class="card <?= $errorCount ? 'error' : 'ok' ?>">
<h2><?= $errorCount ? 'Χρειάζονται διορθώσεις' : 'Το αρχείο είναι έτοιμο' ?></h2>
<div class="stats"><span class="pill"><?= count($analysis['rows']) ?> προϊόντα</span><span class="pill"><?= $errorCount ?> σφάλματα</span><span class="pill"><?= htmlspecialchars(PRESTASHOP_VERSIONS[$prestashopVersion]['label']) ?></span><span class="pill">Encoding: <?= htmlspecialchars($analysis['encoding']) ?></span><span class="pill">Delimiter: <code><?= $analysis['delimiter'] === "\t" ? 'TAB' : htmlspecialchars($analysis['delimiter']) ?></code></span></div>
<p class="hint">Οι ασφαλείς διορθώσεις (τιμές και ονομασίες εκδοτών) εφαρμόζονται αυτόματα στην έξοδο. Στο τελικό CSV οι δεκαδικοί γράφονται με τελεία και δύο ψηφία, π.χ. <code>12.50</code>.</p>
</section>
<?php if ($analysis['issues']): ?><section class="card"><h2>Αναλυτική αναφορά</h2><div class="scroll"><table><thead><tr><th>Τύπος</th><th>Γραμμή</th><th>Κελί</th><th>Ακριβής λόγος</th><th>Πρόταση</th></tr></thead><tbody>
<?php foreach ($analysis['issues'] as $issue): ?><tr><td class="t-<?= $issue['type'] ?>"><?= ['error'=>'Σφάλμα','fix'=>'Διόρθωση','warning'=>'Προειδοποίηση'][$issue['type']] ?></td><td><?= $issue['line'] ?></td><td><?= htmlspecialchars($issue['cell']) ?></td><td><?= htmlspecialchars($issue['reason']) ?></td><td><?= htmlspecialchars($issue['suggestion']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></section><?php endif; ?>
<section class="card">
<?php if ($errorCount === 0): ?><div class="download-actions">
<?php foreach ($productType === 'clothes' ? ['el' => 'Ελληνικά', 'en' => 'Αγγλικά'] : ['el' => 'Ελληνικά'] as $languageCode => $languageLabel): ?><form method="post"><input type="hidden" name="action" value="download"><input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>"><input type="hidden" name="product_type" value="<?= htmlspecialchars($productType) ?>"><input type="hidden" name="prestashop_version" value="<?= htmlspecialchars($prestashopVersion) ?>"><input type="hidden" name="output_language" value="<?= $languageCode ?>"><?php if ($normalizeTitles): ?><input type="hidden" name="normalize_titles" value="1"><?php endif; ?><input type="hidden" name="start_id" value="<?= $startId ?>"><input type="hidden" name="tax" value="<?= $tax ?>"><input type="hidden" name="discount" value="<?= $discount ?>"><button class="btn" type="submit">CSV στα <?= $languageLabel ?></button></form><?php endforeach; ?>
</div><?php if ($productType === 'clothes'): ?><p class="hint">Στο ελληνικό CSV μεταφράζεται η σύνθεση από αγγλικά σε ελληνικά. Στο αγγλικό CSV η σύνθεση μένει όπως είναι και μεταφράζονται ο τίτλος και η περιγραφή μαζί με τα παράγωγα SEO πεδία.</p><?php endif; ?><?php else: ?><p>Διορθώστε τα κόκκινα σφάλματα στο αρχικό αρχείο και ανεβάστε το ξανά. Δεν γίνεται αυθαίρετη αλλαγή όταν η σωστή τιμή δεν είναι βέβαιη.</p><?php endif; ?>
<p><a href="index.php">Έλεγχος άλλου αρχείου</a></p></section>
<?php endif; ?>
</main>
<?php if ($analysis === null): ?><script>
const productColumns = <?= json_encode(array_map(fn($config) => $config['columns'], PRODUCT_TYPES), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const productType = document.getElementById('product-type');
const expectedColumns = document.getElementById('expected-columns');
const clothingOptions = document.getElementById('clothing-options');
const updateColumns = () => { expectedColumns.textContent = productColumns[productType.value].join(', '); clothingOptions.hidden = productType.value !== 'clothes'; };
productType.addEventListener('change', updateColumns); updateColumns();
</script><?php endif; ?>
</body></html>
