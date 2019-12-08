#include <ArduinoJson.h>
#include <SPI.h>
#include <WiFiNINA.h>
#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>
#include <Wire.h>
#include "secret.h" // This is where WiFi and MySQL credentials are saved

char ssid[] = SECRET_SSID;
char pass[] = SECRET_PASS; // WPA / WEP password
int status = WL_IDLE_STATUS; // Wifi radio's status

IPAddress server_addr(SECRET_IP); // IP of the MySQL *server* here
char user[] = SECRET_USERNAME; // MySQL user login username
char password[] = SECRET_PASSWORD; // MySQL user login password

WiFiSSLClient client; // Change this from WiFiSSLClient -> WiFiClient if you DO NOT use SSL on API
WiFiClient sqlClient; // MySQL does not support SSL protocol. We have to create a custom client for the Arduino to connect to database

int port = 443; // Change this from 443 -> 80 if you DO NOT use SSL on API

MySQL_Connection conn((Client *)&sqlClient);

const int MPU_addr=0x68; // I2C address of the MPU-6050
int16_t AcX,AcY,AcZ,Tmp,GyX,GyY,GyZ;

long randNumber; // Random Pokemon ID
long rollCatch; // Roll a chance to catch a Pokemon

char INSERT_DATA[] = "INSERT INTO arduinomon.catches (pokemon_id) VALUES (%d)"; // Query for inserting generated Pokemon after successful catch
char query[128];

void setup() {
  Wire.begin();
  Wire.beginTransmission(MPU_addr);
  Wire.write(0x6B);  // PWR_MGMT_1 register
  Wire.write(0);     // Set to zero (wakes up the MPU-6050)
  Wire.endTransmission(true);

  // Initialize Serial port
  Serial.begin(9600);
  while (!Serial) continue;

  // Check for the WiFi module
  if (WiFi.status() == WL_NO_MODULE) {
    Serial.println("Communication with WiFi module failed!");
    // Don't continue
    while (true);
  }

  String fv = WiFi.firmwareVersion();
  if (fv < "1.0.0") {
    Serial.println("Please upgrade the firmware");
  }

  // Attempt to connect to WiFi network
  while (status != WL_CONNECTED) {
    Serial.print("Attempting to connect to SSID: ");
    Serial.println(ssid);
    // Connect to WPA/WPA2 network. Change this line if using open or WEP network
    status = WiFi.begin(ssid, pass);

    // Wait 10 seconds for connection:
    delay(1000);
  }
  Serial.println("Connected to WiFi!");
  // If connected, print your board's IP address
  IPAddress ip = WiFi.localIP();
  Serial.print("IP Address: ");
  Serial.println(ip);
}

void loop() {
  randomSeed(AcX + AcY + AcZ); // Initialize a random seed based on a "throw". This is to ensure we get a "true" randomly generated Pokemon

  Wire.beginTransmission(MPU_addr);
  Wire.write(0x3B);  // Starting with register 0x3B (ACCEL_XOUT_H)
  Wire.endTransmission(false);
  Wire.requestFrom(MPU_addr,14,true);  // Request a total of 14 registers
  AcX=Wire.read()<<8|Wire.read();  // 0x3B (ACCEL_XOUT_H) & 0x3C (ACCEL_XOUT_L)    
  AcY=Wire.read()<<8|Wire.read();  // 0x3D (ACCEL_YOUT_H) & 0x3E (ACCEL_YOUT_L)
  AcZ=Wire.read()<<8|Wire.read();  // 0x3F (ACCEL_ZOUT_H) & 0x40 (ACCEL_ZOUT_L)
  Tmp=Wire.read()<<8|Wire.read();  // 0x41 (TEMP_OUT_H) & 0x42 (TEMP_OUT_L)
  GyX=Wire.read()<<8|Wire.read();  // 0x43 (GYRO_XOUT_H) & 0x44 (GYRO_XOUT_L)
  GyY=Wire.read()<<8|Wire.read();  // 0x45 (GYRO_YOUT_H) & 0x46 (GYRO_YOUT_L)
  GyZ=Wire.read()<<8|Wire.read();  // 0x47 (GYRO_ZOUT_H) & 0x48 (GYRO_ZOUT_L)

  // TODO This value is just for testing. This needs to be balanced for a proper product
  if (AcX > 5000) {
    Serial.println("Collision detected!");
    Serial.print("AcX = "); Serial.print(AcX);
    Serial.print(" | AcY = "); Serial.print(AcY);
    Serial.print(" | AcZ = "); Serial.println(AcZ);
    // TODO Initialize a better way to detect a collision / throw
    //Serial.print(" | Tmp = "); Serial.print(Tmp/340.00+36.53);  // Equation for temperature in degrees C from datasheet TODO Use temperature to generate Pokemon?
    //Serial.print(" | GyX = "); Serial.print(GyX);
    //Serial.print(" | GyY = "); Serial.print(GyY);
    //Serial.print(" | GyZ = "); Serial.println(GyZ);

    // Generate random Pokemon from Gen x (currently x = 151 which means generation 1. Increase to the respective maximum amount of Pokemon in wanted generation to increase the amount of Pokemon to generate)
    Serial.println("Generating Pokemon...");
    randNumber = random(1, 151);

    Serial.println(F("Attempting to connect to API..."));

    // Connect to the local API via HTTP
    client.setTimeout(10000);
    if (!client.connect("pokemon.habski.me", 443)) {
      Serial.println(F("Connection failed"));
      return;
    }

    Serial.println(F("Connected!"));

    // Send HTTP request to get Pokemon data from local API
    client.print(F("GET /api/index.php?id="));
    client.print(randNumber);
    client.println(F(" HTTP/1.0"));
    client.print(F("Host: "));
    client.println(LOCAL_API);
    client.println(F("Connection: close"));
    if (client.println() == 0) {
      Serial.println(F("Failed to send request"));
      return;
    }

    // Check HTTP status
    char status[32] = {0};
    client.readBytesUntil('\r', status, sizeof(status));
    if (strcmp(status, "HTTP/1.1 200 OK") != 0) {
      Serial.print(F("Unexpected response: "));
      Serial.println(status);
      return;
    }

    // Skip HTTP headers
    char endOfHeaders[] = "\r\n\r\n";
    if (!client.find(endOfHeaders)) {
      Serial.println(F("Invalid response"));
      return;
    }

    // Allocate the JSON document
    // Use arduinojson.org/v6/assistant to compute the capacity. This is only required if you change parameters in your local API
    const size_t capacity = JSON_OBJECT_SIZE(3) + 40;
    DynamicJsonDocument doc(capacity);

    // Parse JSON object
    DeserializationError error = deserializeJson(doc, client);
    if (error) {
      Serial.print(F("deserializeJson() failed: "));
      Serial.println(error.c_str());
      return;
    }

    // Extract values
    Serial.println(F("Found Pokemon:"));
    Serial.print(doc["name"].as<char*>());
    Serial.print(F(" #"));
    Serial.print(F(randNumber));
    Serial.print(F(" - Capture rate: "));
    Serial.println(doc["capture_rate"].as<long>());

    // If Pokemon data retrieved and parsed, disconnect from API
    client.stop();

    // Trying to randomly roll for successfull catch... (255 is the maximum value of the capture rate of a Pokemon)
    rollCatch = random(1,255);

    Serial.print(F("Trying to catch with a chance of "));
    Serial.println(rollCatch);

    // If successfull catch, connect to database
    if (rollCatch <= doc["capture_rate"].as<long>()) {
      Serial.println("Attempting to connect to database...");
      if (conn.connect(server_addr, 3306, user, password)) {
        delay(1000);
      } else {
        Serial.println("Connection to database failed!");
      }

      // Initiate the query class instance
      MySQL_Cursor *cur_mem = new MySQL_Cursor(&conn);

      sprintf(query, INSERT_DATA, randNumber);

      // Execute the query
      cur_mem->execute(query);
      // Note: since there are no results, we do not need to read any data
      // Deleting the cursor also frees up memory used
      delete cur_mem;
      Serial.println("Success!");
    } else {
      Serial.println("Failed!");
    }

    // Cooldown until enabling to catch next Pokemon    
    delay(10000);
  }
  delay(333);
}
