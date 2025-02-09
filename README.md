# Seed Link

Hackathon project University of Leeds 2025 developed by Beckett Sphinx
A comprehensive project that integrates Arduino, HTML, CSS, PHP, and databases to monitor and manage soil 

## Introduction

Seedlink is an innovative project designed to monitor soil moisture levels in real-time. It uses an Arduino to collect sensor data, which is then transmitted to a web server. The data is displayed on a web interface built with HTML, CSS, and PHP, and stored in a database for further analysis.

## Table of Contents

- [Introduction](#introduction)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Usage](#usage)
- [Arduino Setup](#arduino-setup)
- [Web Interface](#web-interface)
- [Database](#database)
- [APIs](#apis)

## Project Structure

- **Arduino**: Collects soil moisture data using a capacitive moisture sensor.
- **HTML/CSS**: Provides a user-friendly web interface to display data.
- **PHP**: Handles server-side processing and database interactions.
- **Database**: Stores sensor data for historical analysis and reporting.

## Installation

Follow these steps to set up the Seedlink project:

1. **Install Dependencies**:
    - A requirements.txt can be found which outlines the different requirements that need installing to run the project

2. **Upload Arduino Sketch**:
    - Open the Arduino IDE and upload the provided sketch to your Arduino board.
    - Ensure that the assigned pins are the correct ones, edit the file to match yours

3. **Set Up Local Server**:
    - There is a python file that starts a local server

4. **Set Up Database**:
    - Import the provided SQL file (`database.sql`) to your MySQL database.
    - PHP is used for this project

## Usage

1. **Run the Local Server**:
    - Start the Python server program

2. **Access Web Interface**:
    - Click on the server link provided by the terminal (provided it is being hosted locally)

3. **Monitor Soil Moisture**:
    - View real-time soil moisture data and historical records.

## Arduino Setup

1. **Connect the Sensors**:
    - Connect the capacitive soil moisture sensor, light sensitivity sensor, humidity and temperature sensor to the Arduino board.

2. **Upload Code**:
    - Upload the provided Arduino sketch to the Arduino board using the Arduino IDE.

3. **Serial Communication**:
    - Ensure the Arduino board is connected to the server via serial communication for data transmission.

## Web Interface

- **HTML**: Structures the web pages for displaying data.
- **CSS**: Styles the web pages for an improved user experience.
- **JavaScript**: (Optional) Adds interactive elements to the web interface.
- **PHP**: Handles form submissions, data retrieval, and database interactions.

## Database

1. **Create Database**:
    - Use the provided SQL script to create the necessary database and tables.
    - Ensure your PHP code has the correct database credentials for connection.

2. **Store Data**:
    - Sensor data is stored in the database and can be retrieved 

## APIs
- **Typo.js**: detects spelling mistakes
- **Plant api**: an AI database that has information about plants. This information is compared to give insight