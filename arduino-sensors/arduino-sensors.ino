#include <dht.h>

dht DHT;

// Pin Definitions
#define LIGHT_SENSOR_PIN A0  // Analog pin for light sensor
#define DHT11_PIN 8         // Digital pin for DHT11 sensor

void setup() {
    Serial.begin(9600);
    DHT.read11(DHT11_PIN);
}

void loop() {
    sendSensorData();
    delay(3000);  // Delay between readings
}

// Function to read and send sensor data via Serial
void sendSensorData() {
    int lightValue = analogRead(LIGHT_SENSOR_PIN);  // Read sensor value
    int lightLevel = 1024 - lightValue;  // Convert to intensity level
    int chk = DHT.read11(DHT11_PIN);
    int temperature = DHT.temperature;
    int humidity = DHT.humidity;

    // Send data in CSV format: light,temp,humidity
    Serial.print(lightLevel);
    Serial.print(",");
    Serial.print(temperature);
    Serial.print(",");
    Serial.println(humidity);
}
