<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

function sendJson(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Protect the endpoint: only business owners may use it.
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'business_owner'
) {
    sendJson(401, [
        'error' => 'Please log in as a business owner.'
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(405, ['error' => 'Only POST requests are allowed.']);
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (stripos($contentType, 'application/json') === false) {
    sendJson(400, ['error' => 'Invalid request format.']);
}

$request = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($request)) {
    sendJson(400, ['error' => 'The request is not valid JSON.']);
}

$language = $request['language'] ?? 'English';
$messages = $request['messages'] ?? [];

if (!in_array($language, ['English', 'Sesotho'], true)) {
    sendJson(400, ['error' => 'Please select English or Sesotho.']);
}

if (!is_array($messages) || count($messages) < 1 || count($messages) > 10) {
    sendJson(400, ['error' => 'Please send a valid conversation.']);
}

// Validate and limit the conversation.
$conversation = [];

foreach ($messages as $message) {
    if (
        !is_array($message) ||
        !isset($message['role'], $message['content']) ||
        !in_array($message['role'], ['user', 'assistant'], true) ||
        !is_string($message['content'])
    ) {
        sendJson(400, ['error' => 'A message is invalid.']);
    }

    $content = trim($message['content']);

    if ($content === '' || strlen($content) > 8000) {
        sendJson(400, ['error' => 'A message is empty or too long.']);
    }

    $conversation[] = [
        'role' => $message['role'],
        'content' => $content
    ];
}

// The final message must be the user's question.
$lastMessage = end($conversation);

if ($lastMessage['role'] !== 'user') {
    sendJson(400, ['error' => 'Please send a question first.']);
}

// Load the private API configuration outside www.
$configFile = 'C:/wamp64/private/openai_config.php';

if (!is_file($configFile)) {
    sendJson(500, ['error' => 'The AI configuration file was not found.']);
}

$config = require $configFile;

$apiKey = $config['api_key'] ?? '';
$model = $config['model'] ?? '';

if (
    !is_string($apiKey) ||
    trim($apiKey) === '' ||
    strpos($apiKey, 'PASTE_YOUR_') === 0 ||
    !is_string($model) ||
    trim($model) === ''
) {
    sendJson(500, ['error' => 'Please configure the AI API key and model.']);
}

// Instructions for the AI assistant.
$instructions = "
You are the AI Business Assistant inside Small Business Shop,
a platform designed to support small businesses in Lesotho.

Help users with practical, understandable business advice about:
marketing, pricing, customer service, stock management, product
descriptions, online selling, budgeting and business planning.

Respond in {$language}.
If the language is Sesotho, use clear, natural Sesotho that is easy
for small business owners to understand. If the user asks in another
language, still answer in the selected language.

Use simple language and practical examples relevant to small
businesses in Lesotho where appropriate. Give clear steps when useful.
Do not claim to know the user's sales, stock, finances or database
records unless that information has actually been provided.
Do not invent facts, guarantee profits or present uncertain advice
as certain. For legal, tax or major financial decisions, recommend
checking with an appropriate local professional.
";

// Build the Responses API request.
$payload = [
    'model' => $model,
    'instructions' => $instructions,
    'input' => $conversation,
    'max_output_tokens' => 600,
    'store' => false
];

$curl = curl_init('https://api.openai.com/v1/responses');

curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ],
    CURLOPT_POSTFIELDS => json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE
    ),
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 60
]);

$result = curl_exec($curl);
$curlError = curl_error($curl);
$httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

if ($result === false) {
    error_log('AI connection error: ' . $curlError);

    sendJson(502, [
        'error' => 'Could not connect to the AI service. Check your internet connection.'
    ]);
}

$apiResponse = json_decode($result, true);

if ($httpStatus < 200 || $httpStatus >= 300) {
    // Keep the provider's detailed error out of the public page.
    error_log('AI API HTTP status: ' . $httpStatus);

    if ($httpStatus === 401) {
        sendJson(502, [
            'error' => 'The AI API key was rejected. Check your configuration.'
        ]);
    }

    if ($httpStatus === 429) {
        sendJson(502, [
            'error' => 'The AI service limit or available API credits may have been reached.'
        ]);
    }

    sendJson(502, [
        'error' => 'The AI service returned an error. Check your API settings and try again.'
    ]);
}

// Extract text from the Responses API output.
$reply = '';

if (isset($apiResponse['output']) && is_array($apiResponse['output'])) {
    foreach ($apiResponse['output'] as $item) {
        if (
            ($item['type'] ?? '') === 'message' &&
            isset($item['content']) &&
            is_array($item['content'])
        ) {
            foreach ($item['content'] as $part) {
                if (
                    ($part['type'] ?? '') === 'output_text' &&
                    isset($part['text'])
                ) {
                    $reply .= $part['text'];
                }
            }
        }
    }
}

$reply = trim($reply);

if ($reply === '') {
    sendJson(502, [
        'error' => 'The AI did not return a text answer. Please try again.'
    ]);
}

sendJson(200, ['reply' => $reply]);