from flask import Flask, render_template, request, jsonify
from better_profanity import profanity
from flask_cors import CORS
from chat import get_response
import subprocess
import mysql.connector
import json

conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',
    database='test_db'
)

cursor = conn.cursor()

app = Flask(__name__)
suggested_topics = ['Admission result', 'Eligibility criteria', 'Documentation requirements', 'Application status', 'Application period', 'Other']
CORS(app, resources={r"/train": {"origins": "*"}})  # Allow requests to /train from any origin

with open('intents.json', 'r') as intents_file:
    intents = json.load(intents_file)

@app.get("/")
def index_get():
    return render_template("base.php")

@app.post("/predict")
def predict():
    text = request.get_json().get("message")

    # Check if the clicked topic is in the suggested topics
    
    #kapag yung message nung user ay may matching intent 
    if text in suggested_topics:
        # If it's a suggested topic, find the matching intent
        matching_intent = find_matching_intent(text)

        if matching_intent:
            responses = matching_intent["responses"]
            response = "\n".join(responses)
        else:
            response = "I don't have specific information for that topic."
    else:
        # Otherwise, proceed with the regular chatbot response
        response = get_response(text)

    # Censor profanity in the response
    censored_response = profanity.censor(response)

    message = {"answer": censored_response}
    return jsonify(message)
#hinahanap yung nasa intent.json kung match pa yung tag and pattern

def find_matching_intent(text):
    for intent in intents["intents"]:
        
        if text.lower() in [pattern.lower() for pattern in intent["patterns"]]:
           
            return intent
    return None



@app.route('/train', methods=['POST'])
def train_chatbot():
    subprocess.run(['python', 'train.py'])
    return 'Training started'

def contains_profanity(text):
    return profanity.contains_profanity(text)

@app.route('/save_feedback', methods=['POST'])
def save_feedback():
    feedmessage = request.form["feedmessage"]

    # Check if the feedback message contains profanity
    if contains_profanity(feedmessage):
        return '''
        <script>
            alert("Feedback contains profanity and cannot be saved.");
            window.location.href = '/';  
        </script>
        '''

    # If no profanity is detected, proceed to insert the feedback into the database
    insert_query = "INSERT INTO feedback (message) VALUES (%s)"
    cursor.execute(insert_query, (feedmessage,))
    conn.commit()

    return '''
    <script>
        alert("Thanks for the feedback!");
        window.location.href = '/';  
    </script>
    '''

if __name__ == "__main__":
    app.run(debug=True)
