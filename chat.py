import random
import json
import torch

from model import NeuralNet
from nltk_utils import bag_of_words, tokenize
from better_profanity import profanity


device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')

# Load intents and model data
with open('intents.json', 'r') as json_data:
    intents = json.load(json_data)

FILE = "data.pth"
data = torch.load(FILE)

input_size = data["input_size"]
hidden_size = data["hidden_size"]
output_size = data["output_size"]
all_words = data['all_words']
tags = data['tags']
model_state = data["model_state"]

model = NeuralNet(input_size, hidden_size, output_size).to(device)
model.load_state_dict(model_state)
model.eval()

bot_name = "CVSU Admission System"

# Initialize the profanity filter
profanity.load_censor_words_from_file('profanity_wordlist.txt')

def is_profane(msg):
    return profanity.contains_profanity(msg)

def censor_message(msg):
    censored_msg = profanity.censor(msg)
    return censored_msg

def handle_out_of_scope():
    return "I'm sorry, but I didn't quite understand that. If you have a specific question or need assistance, feel free to provide more details, and I'll do my best to help you!"






def get_response(msg):
    if is_profane(msg):
        return "I'm sorry, but I can't engage in discussions or respond to offensive language. If you have any other questions or need assistance with something else, please feel free to ask, and I'll be happy to help."

    
    

    sentence = tokenize(msg)
    X = bag_of_words(sentence, all_words)
    X = X.reshape(1, X.shape[0])
    X = torch.from_numpy(X).to(device)

    output = model(X)
    confidence = torch.softmax(output, dim=1).max()
    confidence_threshold = 0.80

    if confidence >= confidence_threshold:
        predicted = torch.argmax(output)
        tag = tags[predicted.item()]

        print(f"Tag: {tag}")
        print(f"Confidence: {confidence}")

        for intent in intents['intents']:
            if tag == intent["tag"]:
                response = random.choice(intent['responses'])
                if is_profane(response):
                    response = censor_message(response)
                    print(response)
                return response
    else:
        print(f"Confidence below threshold: {confidence}")
        return handle_out_of_scope()

if __name__ == "__main__":
    print("Let's chat! (type 'quit' to exit)")
    while True:
        sentence = input("You: ")
        if sentence == "quit":
            break

        resp = get_response(sentence)
        print(f"{bot_name}: {resp}")
