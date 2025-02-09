from flask import Flask, request, jsonify
import google.generativeai as genai
import os
# from plant_data import plant_data

app = Flask(__name__)

# Set up Google Gemini AI API key
GEMINI_API_KEY = "AIzaSyCHrn97JgjevHx3Hb79EnIcAt-92eTS-wo"  # Replace with your actual API key
os.environ["GOOGLE_API_KEY"] = GEMINI_API_KEY

genai.configure(api_key=GEMINI_API_KEY)
model = genai.GenerativeModel("gemini-pro")

# Example plant data
plant_data = [
    {"name": "Tomato", "requirements": "Tomatoes need light between 500-1000 lux, humidity between 60-80%, temperature between 20-30°C, soil moisture between 40-70%, and CO2 below 1000 ppm."},
    {"name": "Lettuce", "requirements": "Lettuce needs light between 300-600 lux, humidity between 50-70%, temperature between 15-25°C, soil moisture between 30-60%, and CO2 below 1200 ppm."},
    {"name": "Cactus", "requirements": "Cacti need light between 1000-2000 lux, humidity between 10-30%, temperature between 25-35°C, soil moisture between 10-30%, and CO2 below 1500 ppm."},
    {"name": "Succulents", "requirements": "Succulents thrive in light levels between 500-1000 lux, humidity between 10-30%, temperature between 20-30°C, and soil moisture levels that are low, as they are adapted to arid conditions."},
    {"name": "Parlor Palm (Chamaedorea elegans)", "requirements": "Parlor Palms prefer bright, indirect light but can adapt to lower light conditions. They thrive in temperatures between 18-24°C and can tolerate average humidity levels, making them suitable for your environment."},
    {"name": "ZZ Plant (Zamioculcas zamiifolia)", "requirements": "ZZ Plants are highly adaptable and can tolerate low to medium light conditions. They prefer temperatures between 18-24°C and average humidity levels. Allow the soil to dry out between waterings to prevent overwatering."},
    {"name": "Philodendron", "requirements": "Philodendrons thrive in bright, indirect light but can adapt to lower light conditions. They prefer temperatures between 15-24°C and average humidity levels. Keep the soil moist but not waterlogged."},
    {"name": "Dracaena", "requirements": "Dracaenas prefer bright, indirect light but can tolerate lower light conditions. They thrive in temperatures between 20-25°C and average humidity levels. Allow the top inch of soil to dry out between waterings."},
    {"name": "Peace Lily (Spathiphyllum)", "requirements": "Peace Lilies prefer low to medium light conditions and thrive in temperatures between 18-24°C. They prefer higher humidity levels but can adapt to average indoor humidity. Keep the soil consistently moist but not soggy."},
    {"name": "Kalanchoe", "requirements": "Kalanchoe plants thrive in bright, indirect light and can tolerate light levels around 844 lux. They prefer temperatures between 15-25°C and can adapt to average indoor humidity levels, such as 33%. As succulents, they require well-draining soil and should be watered sparingly, allowing the soil to dry out between waterings."}]

@app.route("/get-plants", methods=["GET"])
def get_plants():
    plants = [plant["name"] for plant in plant_data]
    return jsonify({"plants": plants})

@app.route("/check-plant", methods=["POST"])
def check_plant():
    data = request.json
    light = data["light"]
    humidity = data["humidity"]
    temperature = data["temperature"]
    moisture = data["moisture"]
    selected_plant = data["plant"]

    plant_info = next((plant for plant in plant_data if plant["name"] == selected_plant), None)
    if not plant_info:
        return jsonify({"error": "Plant not found."})

    prompt = f"Sensor Readings: Light={light} lux, Humidity={humidity}%, Temperature={temperature}°C, Moisture={moisture}unit. Plant Requirements: {plant_info['requirements']} Is the environment suitable for {selected_plant} based on my readings? Explain why or why not in simple HTML compatibale format, with colors and icons."
    
    response = model.generate_content(prompt)
    
    return jsonify({
        "plant": selected_plant,
        "recommendation": response.text.strip()
    })

if __name__ == "__main__":
    app.run(debug=True)