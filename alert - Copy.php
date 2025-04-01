<?php
// Gmail credentials
$gmailEmail = ''; // Your Gmail address
$gmailPassword = ''; // Your Gmail password

// WhatsApp number
$whatsappNumber = $_POST['whatsapp'];

// Fetch unread emails
$inbox = imap_open('{imap.gmail.com:993/ssl}INBOX', $gmailEmail, $gmailPassword);
$emails = imap_search($inbox, 'UNSEEN');

if ($emails) {
    $message = "You have " . count($emails) . " unread email(s) in your Gmail inbox.";
    sendWhatsAppMessage($whatsappNumber, $message);
    $response = array('success' => true, 'message' => 'Alert sent successfully.');
} else {
    $response = array('success' => false, 'message' => 'No unread emails found.');
}

echo json_encode($response);

// Function to send WhatsApp message using Twilio API
function sendWhatsAppMessage($to, $body) {
    $accountSid = ''; // Your Twilio account SID
    $authToken = ''; // Your Twilio auth token
    $from = ''; // Your Twilio phone number

    $client = new Twilio\Rest\Client($accountSid, $authToken);
    $message = $client->messages->create(
        "whatsapp:$to",
        array(
            'from' => "whatsapp:$from",
            'body' => $body
        )
    );
}
?>
