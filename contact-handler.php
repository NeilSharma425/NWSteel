<?php
/**
 * Northwest Steel & Pipe Inc. - Contact Form Handler
 * Professional backend for processing contact form submissions
 */

// Security headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS settings (adjust domain for production)
$allowed_origins = [
    'http://localhost:8080',
    'https://www.nwsteel.net',
    'https://nwsteel.net'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400');
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting (simple file-based)
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = sys_get_temp_dir() . '/nwsteel_rate_limit_' . md5($ip);
$rate_limit = 5; // Max 5 submissions per hour
$rate_window = 3600; // 1 hour

if (file_exists($rate_limit_file)) {
    $data = json_decode(file_get_contents($rate_limit_file), true);
    if ($data['count'] >= $rate_limit && (time() - $data['timestamp']) < $rate_window) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'Too many requests. Please try again later.'
        ]);
        exit;
    }
    if ((time() - $data['timestamp']) < $rate_window) {
        $data['count']++;
    } else {
        $data = ['count' => 1, 'timestamp' => time()];
    }
} else {
    $data = ['count' => 1, 'timestamp' => time()];
}
file_put_contents($rate_limit_file, json_encode($data));

// Get and validate input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['name', 'email', 'subject', 'message'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Missing required field: $field"
        ]);
        exit;
    }
}

// Sanitize inputs
$name = filter_var($input['name'], FILTER_SANITIZE_STRING);
$email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
$phone = isset($input['phone']) ? filter_var($input['phone'], FILTER_SANITIZE_STRING) : '';
$subject = filter_var($input['subject'], FILTER_SANITIZE_STRING);
$message = filter_var($input['message'], FILTER_SANITIZE_STRING);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address'
    ]);
    exit;
}

// Honeypot check
if (!empty($input['website'])) {
    // Silently accept but don't process (spam)
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message!'
    ]);
    exit;
}

// Validate message length
if (strlen($message) < 10) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Message must be at least 10 characters'
    ]);
    exit;
}

// ===========================
// EMAIL CONFIGURATION
// ===========================

// Recipient email (Northwest Steel sales)
$to = 'sales@nwsteel.net';

// Email subject
$email_subject = "Website Contact Form: $subject";

// Email body
$email_body = "
New contact form submission from NW Steel website

===========================
Contact Information
===========================
Name: $name
Email: $email
Phone: $phone

===========================
Subject
===========================
$subject

===========================
Message
===========================
$message

===========================
Additional Information
===========================
Submitted: " . date('Y-m-d H:i:s') . "
IP Address: $ip
User Agent: {$_SERVER['HTTP_USER_AGENT']}
";

// Email headers
$headers = [
    "From: noreply@nwsteel.net",
    "Reply-To: $email",
    "X-Mailer: PHP/" . phpversion(),
    "MIME-Version: 1.0",
    "Content-Type: text/plain; charset=UTF-8"
];

// Send email
$mail_sent = mail($to, $email_subject, $email_body, implode("\r\n", $headers));

// ===========================
// OPTIONAL: Save to database
// ===========================
/*
try {
    $pdo = new PDO('mysql:host=localhost;dbname=nwsteel', 'username', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        INSERT INTO contact_submissions
        (name, email, phone, subject, message, ip_address, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([$name, $email, $phone, $subject, $message, $ip]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
*/

// ===========================
// OPTIONAL: Send to CRM (e.g., HubSpot, Salesforce)
// ===========================
/*
$crm_data = [
    'fields' => [
        ['name' => 'firstname', 'value' => explode(' ', $name)[0]],
        ['name' => 'lastname', 'value' => explode(' ', $name)[1] ?? ''],
        ['name' => 'email', 'value' => $email],
        ['name' => 'phone', 'value' => $phone],
        ['name' => 'message', 'value' => $message]
    ]
];

$ch = curl_init('https://api.hubapi.com/contacts/v1/contact');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($crm_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer YOUR_HUBSPOT_API_KEY'
]);
curl_exec($ch);
curl_close($ch);
*/

// ===========================
// OPTIONAL: Use SendGrid/Mailgun for better deliverability
// ===========================
/*
// SendGrid example:
$ch = curl_init('https://api.sendgrid.com/v3/mail/send');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'personalizations' => [['to' => [['email' => $to]]]],
    'from' => ['email' => 'noreply@nwsteel.net', 'name' => 'NW Steel Website'],
    'reply_to' => ['email' => $email, 'name' => $name],
    'subject' => $email_subject,
    'content' => [['type' => 'text/plain', 'value' => $email_body]]
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer YOUR_SENDGRID_API_KEY'
]);
$result = curl_exec($ch);
$mail_sent = (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 202);
curl_close($ch);
*/

// Response
if ($mail_sent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you soon.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Please try again or call us at (253) 531-2950.'
    ]);
}
