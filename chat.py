from flask import Flask, request, jsonify
import google.generativeai as genai
from dotenv import load_dotenv
import os

# Load environment variables from .env file
dotenv_path = os.path.join(os.path.dirname(__file__), ".env")
load_dotenv(dotenv_path)

# Retrieve API key
# api_key = os.getenv("GEMINI_API_KEY")
api_key = "AIzaSyBnfb_FKaTj2qXiDZMpm08Jrl_RgLuB8_4"

# Debugging: Print API Key
print(f"🔹 GEMINI_API_KEY: {api_key}")  # Check if it's loading

if not api_key:
    raise ValueError("⚠️ Google AI API key not found! Make sure it's set in the .env file.")

# Configure Google AI with API key
genai.configure(api_key=api_key)

# Define model configuration
generation_config = {
    "temperature": 0.7,
    "top_p": 0.95,
    "top_k": 50,
    "max_output_tokens": 250,
    "response_mime_type": "text/plain",
}
safety_settings = [
    {"category": "HARM_CATEGORY_HARASSMENT", "threshold": "BLOCK_NONE"},
    {"category": "HARM_CATEGORY_HATE_SPEECH", "threshold": "BLOCK_MEDIUM_AND_ABOVE"},
    {"category": "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold": "BLOCK_MEDIUM_AND_ABOVE"},
    {"category": "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold": "BLOCK_MEDIUM_AND_ABOVE"},
]

# Initialize Generative Model
try:
    model = genai.GenerativeModel(
        model_name="gemini-1.5-pro",
        safety_settings=safety_settings,
        generation_config=generation_config,
        system_instruction = "You are a legal AI assistant specializing in Indian law. Provide accurate and concise information on IPC (Indian Penal Code) sections, punishments, and related legal provisions. If needed, offer explanations in simple terms for better understanding."

    )
    print("✅ AI Model initialized successfully.")
except Exception as e:
    print(f"❌ Error initializing AI Model: {str(e)}")
    model = None

# Initialize Flask app
app = Flask(__name__)

@app.route("/")
def home():
    return jsonify({"message": "✅ LawAssistAI Chatbot API is running!"})

@app.route("/chat", methods=["POST"])
def chat():
    """Handles chatbot queries via POST request."""
    if not model:
        return jsonify({"error": "AI model not initialized properly."}), 500

    try:
        data = request.get_json()
        if not data or "message" not in data:
            return jsonify({"error": "Missing 'message' in request body."}), 400

        user_input = data["message"]
        print(f"🔹 User input: {user_input}")  # Debugging log

        # Create a new chat session
        chat_session = model.start_chat(history=[])
        response = chat_session.send_message(user_input)

        print(f"🔹 AI Response: {response.text}")  # Debugging log
        return jsonify({"response": response.text})

    except Exception as e:
        print(f"❌ Error: {str(e)}")  # Debugging log
        return jsonify({"error": "An internal error occurred.", "details": str(e)}), 500

# Run the Flask app
if __name__ == "__main__":
    print("🚀 Starting Flask server at http://127.0.0.1:8000")
    app.run(host="0.0.0.0", port=8000, debug=True)
