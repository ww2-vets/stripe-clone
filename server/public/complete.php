<?php
require './shared.php';

$paymentIntent = $stripe->paymentIntents->retrieve($_GET["payment_intent"]);

// echo $paymentIntent;

// echo "<pre>" . json_encode($paymentIntent, JSON_PRETTY_PRINT) . "<pre/>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Stripe Sample</title>

    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/normalize.css" />
</head>

<body>
    <main>
        <h1>Complete</h1>

        <?php echo "<pre>" . json_encode($paymentIntent, JSON_PRETTY_PRINT) . "<pre/>" ?>

    </main>
</body>

</html>