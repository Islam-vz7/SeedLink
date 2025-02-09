import serial
import mysql.connector
from datetime import datetime

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',  # Default XAMPP MySQL username
    'password': '',  # Default XAMPP MySQL password (empty)
    'database': 'sensor_readings'  # Your database name
}

def create_table():
    """Create a table to store light, temperature, humidity and moisture readings if it doesn't exist."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS data (
                id INT AUTO_INCREMENT PRIMARY KEY,
                timestamp DATETIME NOT NULL,
                light INT NOT NULL,
                temperature INT NOT NULL,
                humidity INT NOT NULL,
                moisture INT NOT NULL
            )
        """)
        conn.commit()
        print("Table 'data' created or already exists.")
    except mysql.connector.Error as e:
        print("Database error:", e)
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def save_reading(light, temp, humidity, moisture):
    """Save light, temperature, and humidity readings to the MySQL database."""
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("""
            INSERT INTO data (timestamp, light, temperature, humidity, moisture)
            VALUES (%s, %s, %s, %s, %s)
        """, (timestamp, light, temp, humidity, moisture))
        conn.commit()
        print("Data saved to database:", light, temp, humidity, moisture)
    except mysql.connector.Error as e:
        print("Database error:", e)
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

# Main program
try:
    # Create the table if it doesn't exist
    create_table()

    # Open the serial connection
    with serial.Serial('COM5', 9600, timeout=1) as ser:
        print("Listening for serial data...")
        while True:
            data = ser.readline().decode('utf-8').strip()
            if data:  # Check if data is not empty
                print("Received Data:", data)
                try:
                    # Split the data into light, temp, and humidity
                    light, temp, humidity, moisture = map(int, data.split(','))
                    # Save the readings to the database
                    save_reading(light, temp, humidity, moisture)
                except ValueError as e:
                    print("Error parsing data:", e)
except serial.SerialException as e:
    print("Serial port error:", e)
except KeyboardInterrupt:
    print("Program terminated by user")
except Exception as e:
    print("An unexpected error occurred:", e)