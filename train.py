import numpy as np
import random
import json
import torch
import torch.nn as nn
from torch.utils.data import Dataset, DataLoader
from nltk_utils import bag_of_words, tokenize, stem
from model import NeuralNet
import nltk
from nltk.corpus import wordnet
# Load intents
with open('intents.json', 'r') as f:
    intents = json.load(f)

# Initialize data lists
all_words = []
tags = []
xy = []

# Loop through each sentence in intents patterns
for intent in intents['intents']:
    tag = intent['tag']
    tags.append(tag)
    for pattern in intent['patterns']:
        w = tokenize(pattern)
        all_words.extend(w)
        xy.append((w, tag, intent.get('id')))

# Stem and lower each word, remove duplicates
ignore_words = ['?', '.', '!',"'"]
all_words = [stem(w) for w in all_words if w not in ignore_words]
all_words = sorted(set(all_words))
tags = sorted(set(tags))

# Data Augmentation: Synonym Replacement


#Paki comment nalang yung nltk.download('wordnet') kung naka install na
#nltk.download('wordnet')

def synonym_replacement(words, n):
    new_words = words.copy()
    random_word_list = list(set([word for word in words if word not in ignore_words]))
    random.shuffle(random_word_list)
    num_replaced = 0
    for random_word in random_word_list:
        synsets = wordnet.synsets(random_word)
        if synsets:
            synonym = synsets[0].lemmas()[0].name()
            new_words = [synonym if word == random_word else word for word in new_words]
            num_replaced += 1
        if num_replaced >= n:
            break

    sentence = ' '.join(new_words)
    return sentence

# Data Augmentation: Adding Noise
def add_noise(sentence, p=0.1):
    words = sentence.split()
    new_words = []
    for word in words:
        if random.random() < p:
            new_word = random.choice(all_words)
            new_words.append(new_word)
        else:
            new_words.append(word)
    noisy_sentence = ' '.join(new_words)
    return noisy_sentence

# Create augmented data
augmented_data = []
num_augmented_samples = 3  # Adjust as needed

for (pattern_sentence, tag, intent_id) in xy:
    augmented_data.append((pattern_sentence, tag, intent_id))  # Add the original example

    # Apply synonym replacement and noise
    for _ in range(num_augmented_samples):
        augmented_sentence = synonym_replacement(pattern_sentence, n=1)  # Apply synonym replacement
        augmented_sentence = add_noise(augmented_sentence, p=0.1)  # Add noise
        augmented_data.append((augmented_sentence.split(), tag, intent_id))

# Update X_train and y_train with the augmented data
X_train = []
y_train = []

for (pattern_sentence, tag, intent_id) in augmented_data:
    bag = bag_of_words(pattern_sentence, all_words)
    X_train.append(bag)
    label = tags.index(tag)
    y_train.append(label)

X_train = np.array(X_train)
y_train = np.array(y_train)

# Hyperparameters
num_epochs = 1000
batch_size = 8
learning_rate = 0.001
input_size = len(X_train[0])
hidden_size = 8
output_size = len(tags)

class ChatDataset(Dataset):

    def __init__(self):
        self.n_samples = len(X_train)
        self.x_data = X_train
        self.y_data = y_train

    def __getitem__(self, index):
        return self.x_data[index], self.y_data[index]

    def __len__(self):
        return self.n_samples

# Initialize dataset and data loader
dataset = ChatDataset()
train_loader = DataLoader(dataset=dataset, batch_size=batch_size, shuffle=True, num_workers=0)

device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')

model = NeuralNet(input_size, hidden_size, output_size).to(device)

# Loss and optimizer
criterion = nn.CrossEntropyLoss()
optimizer = torch.optim.Adam(model.parameters(), lr=learning_rate)

# Train the model
for epoch in range(num_epochs):
    for (words, labels) in train_loader:
        words = words.to(device)
        labels = labels.to(dtype=torch.long).to(device)
        
        # Forward pass
        outputs = model(words)
        loss = criterion(outputs, labels)
        
        # Backward and optimize
        optimizer.zero_grad()
        loss.backward()
        optimizer.step()
        
    if (epoch+1) % 100 == 0:
        print(f'Epoch [{epoch+1}/{num_epochs}], Loss: {loss.item():.4f}')

print(f'Final loss: {loss.item():.4f}')

# Save model and related data
data = {
    "model_state": model.state_dict(),
    "input_size": input_size,
    "hidden_size": hidden_size,
    "output_size": output_size,
    "all_words": all_words,
    "tags": tags
}

FILE = "data.pth"
torch.save(data, FILE)

print(f'Training complete. Model saved to {FILE}')
