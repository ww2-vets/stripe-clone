<?php
require './shared.php';

define('BASE_URL', 'http://localhost/stripe-clones/starter-php/server/public/');

// 1. $stripe is a php variable of stripe instance from shared.php
// created with stripe Secret Key, and used by Stripe to create $paymentIntent,

// 2. further down stripe is a Javascript constant variable of stripe instance
// used to create the Stripe Element UI (paymentElement) using the CLIENT-SECRET
$paymentIntent = $stripe->paymentIntents->create([
  'amount' => 2000,
  'currency' => 'sgd',
  'automatic_payment_methods' => ['enabled' => true], // for sdg includes PayNow
  //   'payment_method_types' => ['card'],  // has limited payment options
]);

// at this point $paymentIntent already has Id and CLIENT_SECRET
echo $paymentIntent->client_secret;
echo '<br>';
echo $_ENV["STRIPE_PUBLISHABLE_KEY"];

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
    <h1>Stripe Sample Payment Page</h1>
    <form id="payment-form">
      <div id="payment-element"></div>
      <br>
      <button>Pay</button>
      <div id="error-messages"></div>
    </form>
  </main>

  <script src="https://js.stripe.com/v3/"></script>

  <script>
    // 1. Create instance of Stripe with publishable key
    // 2. Create elements with client_secret from paymentIntent
    // 3. Create the Stripe UI using the just created elements and mount ...
    const stripe = Stripe('<?= $_ENV["STRIPE_PUBLISHABLE_KEY"] ?>');
    const elements = stripe.elements({ clientSecret: '<?= $paymentIntent->client_secret ?>' });
    const paymentElement = elements.create('payment'); 
    
    paymentElement.mount('#payment-element');

    // HANDLE FORM SUBMIT //////////////
    // listen for error on submit
    // preventDefault() -> validate locally before sending formData to Stripe
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // 1. elements has client_secret used by Stripe to ID and process payment
      // 2. confirmPayment() -> redirects to custom success page complete.php or webhook.php
      // also carries errors, if any, to be handled here ...
      const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          // redirect url
          // return_url: window.location.href.split('?')[0] + 'complete.php'
          return_url: 'http://localhost/stripe-clones/starter-php/server/public/webhook.php'
        }
      })
      if (error) {
        const messages = document.getElementById('error-messages');
        messages.innerText = error.message;
      }
    })
  </script>
  
</body>

</html>