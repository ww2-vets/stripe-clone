<?php

require_once 'shared.php';
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$body = json_decode($input);
$event = null;

$webhookEvent= json_decode( $input, false );
$status = $webhookEvent->data->object->status;

echo "<pre>" . json_encode($webhookEvent, JSON_PRETTY_PRINT) . "<pre/>";

echo "endpoint secret: " . $_ENV['STRIPE_WEBHOOK_SECRET'];
echo '<br>';
echo "HTTP_STRIPE_SIGNATURE: " . $_SERVER['HTTP_STRIPE_SIGNATURE'];
echo '<br>';


// process the incoming Stripe webhook event
try {
  // Make sure the event is coming from Stripe by checking the signature header
  $event = \Stripe\Webhook::constructEvent(
    $input,
    $_SERVER['HTTP_STRIPE_SIGNATURE'],
    $_ENV['STRIPE_WEBHOOK_SECRET']
  );
}
catch (Exception $e) {
  http_response_code(403);
  echo json_encode([ 'error' => $e->getMessage() ]);
  exit;
}

if ($event->type == 'payment_intent.succeeded') {
  // Fulfill any orders, e-mail receipts, etc
  // To cancel the payment you will need to issue a Refund (https://stripe.com/docs/api/refunds)
  error_log('💰 Payment received!');
}
else if ($event->type == 'payment_intent.payment_failed') {
  error_log('❌ Payment failed.');
}

echo json_encode(['status' => 'success']);
