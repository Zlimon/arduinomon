# Arduinomon Webserver

Webserveren som er inkludert i Arduinomon består av to komponenter: RESTful API og nettside.

API'et er et **krav** for at Arduinomon skal funksjonere, nettsiden er et **alternativ** for å vise fangede Pokemon.

Begge tjenestene er mulig å kjøre på hvilken som helst web server programvare (for eksempel Nginx eller Apache). Dette kan leses om grundigere i [programvare og maskinvare](https://github.com/Zlimon/Arduinomon/wiki/Programvare-og-maskinvare).

## RESTful API
Når du gjør et forsøk på å fange en Pokemon med Pokeballen, må den hente data fra ett API. Dette er nødvendig for å hente informasjon som for eksempel _capture rate_ til en Pokemon, men også viktig for framtidig funksjonalitet som for eksempel dersom et GPS system implementeres. API'et er for øyeblikket kun bygget opp til å lytte etter et parameter kalt _id_, som er den respektive ID'en til Pokemon man ønsker å hente data om. Hvordan API'et fungerer teknisk kan du lese om i [systemarkitekturen](https://github.com/Zlimon/Arduinomon/wiki/Systemarkitektur) vår.

Oppbygning til API:

`http://<webserver hostname eller IP>/api/index.php?id=1`

Parameteret `id` er variabelt og kan endres til hvilken som helst gyldig Pokemon ID for å hente data om spesifikk Pokemon.
Data som returneres er formatert i JSON.

Ett eksempel på hva API'et returnerer for forespurt Pokemon med ID = 1:

`{
"id": 1,
"name": "bulbasaur",
"capture_rate": 45
}`

Åpent og tilgjengelig API kan benyttes dersom du ikke har mulighet å sette opp lokalt API:
[pokemon.habski.me](https://pokemon.habski.me/api/index.php?id=1)

## Nettside (web applikasjon)
Nettsiden som hører med Arduinomon er kun for rent brukergrensesnitt til å bruke Arduinomon. Selve aktiviteten å fange en Pokemon med ballen krever ikke nettsiden, men dersom du ønsker å visualisere fangede Pokemon er nettsiden et alternativ til dette. Webserver med API er dersom et krav. Nettsiden er bygget med PHP for back-end, og ren HTML og CSS for front-end.

Ett utklipp av forsiden til nettsiden. _Dette er fortsatt under utvikling, og kun ment for å representere hvordan nettsiden kan se ut_
![](https://i.imgur.com/vCWPoaK.png)

Denne nettsiden er tilgjengelig for testing på en [live demo](https://pokemon.habski.me/)!
