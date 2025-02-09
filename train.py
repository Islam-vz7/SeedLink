import pymysql
import pandas as pd
from sklearn.cluster import KMeans
import joblib

# Database connection
try:
    connection = pymysql.connect(
        host="localhost",
        user="root",
        password="",
        database="sensor_readings"
    )
    print("Connected to the database successfully!")
except pymysql.Error as e:
    print(f"Error connecting to the database: {e}")
    exit()

# Fetch data from the database
query = "SELECT light, humidity, temperature FROM data"
try:
    data = pd.read_sql(query, connection)
    print("Data fetched successfully!")
    print(data.head())  # Print the first few rows for debugging
except Exception as e:
    print(f"Error fetching data: {e}")
    connection.close()
    exit()

# Close the connection
connection.close()

# Perform K-Means clustering
try:
    kmeans = KMeans(n_clusters=3, random_state=42)  # Adjust the number of clusters as needed
    data["cluster"] = kmeans.fit_predict(data[["light", "humidity", "temperature"]])
    print("Clustering completed successfully!")
except Exception as e:
    print(f"Error during clustering: {e}")
    exit()

# Save the clustering model
try:
    joblib.dump(kmeans, "plant_clustering_model.pkl")
    print("Model saved successfully!")
except Exception as e:
    print(f"Error saving the model: {e}")
    exit()

# Print cluster centers (average conditions for each cluster)
print("Cluster Centers:")
print(kmeans.cluster_centers_)