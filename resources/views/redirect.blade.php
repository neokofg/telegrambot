<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>redirect</title>
    <script src="js/telegram-passport.js"></script>
</head>
<body>
<div id="telegram_passport_auth"></div>
</body>
<script>
    const multi = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA05FL3StCKstAZgOh4Bk1
QEodBenu+BM1jwbYPWi0wyzLwrdglUgP3LnGQJk+jOoHaGtNbHJb5ejJZ7ETLkJY
/dsmsi52+l2QE6CzosBPsbY1M3MUrVJvDUQZFWAs3BO+Y/2CimNNcGC0HQn1AEYO
soNrZN1GqdIjQlNCfvBoaqm8BvmkKEL3hiZPQfO0TUwPpLaf9ERHzIuYyVpyhroG
sZ8jaN14br259ZVuQl9k1qMBX8/AqNvthjhI3mSc0vNquBDRUEFReLPO8ai/U9sm
S8DSg/b50hcP56EA6fY1NK7Yhz4V4yeqeKU+vbxxDkhnN1aub10M/5Ay94cbJPUc
eQIDAQAB
-----END PUBLIC KEY-----`;
    Telegram.Passport.createAuthButton('telegram_passport_auth', {
        bot_id:       5716304295, // place id of your bot here
        scope:        {data: [{type: 'passport'}], v: 1},
        public_key:   multi, // place public key of your bot here
        nonce:        '234262347532', // place nonce here
    });
</script>
</html>
