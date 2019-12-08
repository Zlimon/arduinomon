# Arduinomon

**Velkommen til Arduinomon!**

Arduinomon er et simplifisert Pokemon spill som skal simulere aktiviteten å fange Pokemon med en Pokeball. Gjennom å ha muligheten til å kaste en fysisk ball er målet med "spillet" at man skal oppleve en simulering av at du som spiller skal kunne gå ute i fri luft og fange Pokemon. Dette prosjektet er inspirert av spillet Pokemon Go ut ifra dette aspektet, men med en liten tvist. Istedenfor å benytte en smarttelefon for å fange Pokemon, benytter du heller en fysisk gjenstand. I tillegg legger vi opp for ett system som skal la spillere kunne lagre sine fangede Pokemon, men også å kunne vise dem på en nettside.

## Vårt mål

Hovedmålet med Arduinomon er å kunne være ute i fri luft og fange Pokemon. For øyeblikket er prosjektet et konsept og ett forsøk på å legge grunnlag for å videreutvikle dette til noe større. En grundigere oversikt over hva vi ønsker å utvikle videre for Arduinomon finner du på [prosjekt](https://github.com/Zlimon/Arduinomon/projects) siden vår.

# Funksjonalitet

Arduinomon består av to hovedkomponenter: en Pokeball og en nettside.

Slik statusen på prosjektet er nå så brukes ballen til å "fange" Pokemon. Hvordan denne prosessen foregår kan du lese om i vår [systemarkitektur](https://github.com/Zlimon/Arduinomon/wiki/Systemarkitektur), men kort fortalt så genereres det en ID respektiv til en Pokemon. Hvis fangsten er suksessfull lagres den i databasen; som gjør at den kan vises på nettsiden. Ideer og planer for hvordan funksjonaliteten til ballen kan utvides er forklart på [prosjektsiden for ballen](https://github.com/Zlimon/Arduinomon/projects/1).

Nettsiden sin hovedfunksjon for øyeblikket ganske simpel: å vise alle fangede Pokemon. En grundigere plan for hvordan nettsiden vil og kan utvides er beskrevet i [prosjektsiden for web serveren](https://github.com/Zlimon/Arduinomon/projects/2), men et eksempel på våre ideer er blant annet å implementere bruker system for å kunne differensiere hvem som har fanget hvilken Pokemon.

## Utstyr

For å sette opp Arduinomon anbefaler vi følgende programvare og maskinvare:

Programvare:
* **Nginx**
* **PHP** versjon **7.1.3**
* **MySQL** DBMS
* Arduino IDE

Maskinvare:
* Arduino Uno WiFi Rev2
* MPU-6050

_Du kan lese mer om disse i detaljer på [programvare og maskinvare siden](https://github.com/Zlimon/Arduinomon/wiki/Programvare-og-maskinvare) vår._

## Oppsett

Arduinomon kan settes opp på forskjellige måter avhengig av hvilke programvare og maskinvare du benytter. Ønsker du å følge vår oppskrift finner du den [her](https://github.com/Zlimon/Arduinomon/wiki/Oppsett-av-Arduinomon).

### Eksterne ressurser
Arduinomon bruker to open source-prosjekter for å fungere:
* [bblanchon/ArduinoJson](https://github.com/bblanchon/ArduinoJson) - 📟 JSON library for Arduino and embedded C++. Simple and efficient. https://arduinojson.org
* [arduino-libraries/WiFiNINA](https://github.com/arduino-libraries/WiFiNINA) - ArduinoJson is a C++ JSON library for Arduino and IoT (Internet Of Things).
* [ChuckBell/MySQL_Connector_Arduino](https://github.com/ChuckBell/MySQL_Connector_Arduino) - Database connector library for using MySQL with your Arduino projects.

### Lisens
Arduinomon er lisensiert under MIT License. [Se lisensen i den respektive filen for å se hva dette betyr](https://github.com/Zlimon/Arduinomon/blob/master/LICENSE).

Årsaken til dette er de eksterne ressursene Arduinomon bruker, og i tillegg eiendeler som Pokemon som er intellektuell eid og copyright av The Pokemon Company. Dette betyr at Arduinomon kun vil tilby innhold som er begrenset innenfor denne lisensen, og vil alltid forsikre at disse eiendelene eies av The Pokemon Company.
