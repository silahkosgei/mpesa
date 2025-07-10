# Laravel M-PESA Integration Package

A Laravel package for integrating with Safaricom's M-PESA STK Push and B2C APIs. Simple, extensible, and production-ready.

---

##  Installation

```bash
composer require SilahKosgei/mpesa:^1.1
```

Then publish the configuration file:

```bash
php artisan vendor:publish --tag=mpesa-config
```

---

## Configuration

Add these to your `.env` file:

```env
MPESA_ENV=sandbox                      # or production
MPESA_SHORTCODE=601234
MPESA_BUY_GOODS_TILL=174379
MPESA_SHORTCODE_TYPE=PayBill          # or BuyGoods
MPESA_PASSKEY=YOUR_PASSKEY_HERE
MPESA_CONSUMER_KEY=YOUR_CONSUMER_KEY
MPESA_CONSUMER_SECRET=YOUR_CONSUMER_SECRET
MPESA_CALLBACK_URL=https://yourapp.com/api/complete-payment
MPESA_CONFIRMATION_URL=https://yourapp.com/api/confirmation-payment
```

---

##  Usage

###  Initiate STK Push

```php
use SilahKosgei\Mpesa\Mpesa;

$mpesa = new Mpesa();

$response = $mpesa->initiateStkPush(
    '254712345678',                  // phone
    100,                             // amount
    'ORDER-12345',                   // account reference
    'Payment for Order #12345',      // description
    12345                            // reference ID for callback
);
```

###  Lookup STK Push Payment

```php
$response = $mpesa->lookupPayment('ws_CO_123456789');
```


---

## 👤 Author

**Silh Kosgei**  
📧 silakosy@gmail.com  
🔗 [GitHub: @silahkosgei](https://github.com/silahkosgei)

---

##  Version

**v1.1.1**
