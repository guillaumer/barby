Barby Crypto arbitrage
=============

Very fast Silex prototype to visualize crypto spread between 2 currencies.

Uses [cryptowat.ch API](https://docs.cryptowat.ch/rest-api/) to fetch coin rates and [RapidAPI Currency13](https://rapidapi.com/labstack/api/currency13/endpoints) to keep currency pairs updated.

Supports BTC/ETH/BCH with EUR/JPY pairs.
   
## Updating

Bash
```
php bin/console import:fx
php bin/console import:markets

```
or visit /cron_markets and /cron_fx to update coin rates and fx rates. 

## Encoding password

`echo(new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder())->encodePassword('MOT DE PASSE', '');`

![Barby](https://i.imgur.com/KzYe8Lq.png)


