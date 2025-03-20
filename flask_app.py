from flask import Flask
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# DEBUG: Check if API key is loaded
api_key = os.getenv("GOOGLE_API_KEY")
if not api_key:
    print("❌ Google AI API key NOT found!")
    raise ValueError("Google AI API key not found! Make sure it's set in the .env file.")
else:
    print(f"✅ Loaded API Key: {api_key[:5]}*****")  # Print first 5 characters for security

app = Flask(__name__)

@app.route("/")
def home():
    return "LawAssistAI API is running!"

if __name__ == "__main__":
    app.run(port=8000, debug=True)
