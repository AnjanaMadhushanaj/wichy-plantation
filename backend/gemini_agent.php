<?php
// Gemini API integration for Wichy Plantation AI agent
// Only answers using company and website details
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$question = trim($input['question'] ?? '');

if (!$question) {
    echo json_encode(['answer' => 'Please ask a question about Wichy Plantation.']);
    exit;
}

// Company details context
$company_info = "Wichy Plantation Company (Pvt) Ltd connects consumers around the world with coconut farmers in Sri Lanka, promoting global health and wellbeing while enriching local communities through sustainable agricultural practices. Products: Coconut Oil, Coconut Milk, Coconut Water, Coconut Flour. Contact: +94 770 88 45 45, +(94) 11-2891693, 107, UDA Industrial Estate, Katuwana, Homagama, Sri Lanka. Website: wichy plantation.";

// Gemini API call (replace YOUR_API_KEY with your actual Gemini API key)
$api_key = 'AIzaSyCOgMXU9xsntwH-dxOZc4l-AYWPOi-eoZY';
$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $api_key;

$payload = [
    'contents' => [
        ['parts' => [
            ['text' => "Company info: $company_info\nUser question: $question\nOnly answer using the company and website details above."]
        ]]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($payload),
        'timeout' => 10
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);

if ($response === FALSE) {
    echo json_encode(['answer' => 'AI agent is currently unavailable.']);
    exit;
}

$data = json_decode($response, true);
$answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, no answer available.';
echo json_encode(['answer' => $answer]);

