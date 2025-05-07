<?php
// Simple AI content bypass tool in PHP
// Features: Synonym replacement, sentence length variation

// Synonym dictionary
$synonyms = [
    "good" => ["great", "excellent", "wonderful"],
    "bad" => ["poor", "awful", "terrible"],
    "big" => ["large", "huge", "massive"],
    "small" => ["tiny", "little", "petite"],
    "said" => ["stated", "mentioned", "remarked"]
];

function get_synonym($word) {
    global $synonyms;
    $lower_word = strtolower($word);
    if (isset($synonyms[$lower_word])) {
        return $synonyms[$lower_word][array_rand($synonyms[$lower_word])];
    }
    return $word;
}

function vary_sentence_length($sentences) {
    $result = [];
    $i = 0;
    while ($i < count($sentences)) {
        if (rand(0, 100) < 30 && $i + 1 < count($sentences)) {
            // Combine sentences
            $result[] = $sentences[$i] . " Furthermore, " . $sentences[$i + 1];
            $i += 2;
        } elseif (rand(0, 100) < 20 && count(explode(" ", $sentences[$i])) > 10) {
            // Split long sentences
            $words = explode(" ", $sentences[$i]);
            $mid = floor(count($words) / 2);
            $result[] = implode(" ", array_slice($words, 0, $mid)) . ".";
            $result[] = implode(" ", array_slice($words, $mid)) . ".";
            $i++;
        } else {
            $result[] = $sentences[$i];
            $i++;
        }
    }
    return $result;
}

function humanize_text($text) {
    // Split into sentences
    $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    
    // Paraphrase by replacing words with synonyms
    $paraphrased = [];
    foreach ($sentences as $sentence) {
        $words = explode(" ", $sentence);
        $new_words = [];
        foreach ($words as $word) {
            if (rand(0, 100) < 30 && strlen($word) > 3) {
                $new_words[] = get_synonym($word);
            } else {
                $new_words[] = $word;
            }
        }
        $paraphrased[] = implode(" ", $new_words);
    }
    
    // Vary sentence lengths
    $humanized = vary_sentence_length($paraphrased);
    
    // Add human-like quirks
    foreach ($humanized as &$sentence) {
        if (rand(0, 100) < 20) {
            $sentence = "By the way, " . lcfirst($sentence);
        }
        $sentence = str_replace(" is ", "'s ", $sentence);
    }
    
    return implode(" ", $humanized);
}

// Handle form submission
$output = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["input_text"])) {
    $output = humanize_text($_POST["input_text"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Content Bypass Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea { width: 100%; height: 150px; margin-bottom: 10px; }
        button { padding: 10px 20px; }
        .output { margin-top: 20px; border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <h1>AI Content Bypass Tool</h1>
    <form method="post">
        <textarea name="input_text" placeholder="Enter AI-generated text here..."><?php echo isset($_POST['input_text']) ? htmlspecialchars($_POST['input_text']) : ''; ?></textarea>
        <button type="submit">Humanize Text</button>
    </form>
    <?php if ($output): ?>
        <div class="output">
            <h3>Humanized Text:</h3>
            <p><?php echo htmlspecialchars($output); ?></p>
        </div>
    <?php endif; ?>
</body>
</html>