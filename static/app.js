class Chatbox {
    constructor() {
        this.args = {
            openButton: document.querySelector('.chatbox__button'),
            chatBox: document.querySelector('.chatbox__support'),
            sendButton: document.querySelector('.send__button')
        };

        this.state = false;
        this.messages = [];
    }

    display() {
        const { openButton, chatBox, sendButton } = this.args;

        openButton.addEventListener('click', () => this.toggleState(chatBox));

        sendButton.addEventListener('click', () => this.onSendButton(chatBox));

        const node = chatBox.querySelector('input');
        node.addEventListener('keyup', ({ key }) => {
            if (key === 'Enter') {
                this.onSendButton(chatBox);
            } 
            
        });

        // Call suggestTopics method automatically when the chatbox is displayed
        this.suggestTopics(chatBox);
       
    }

    suggestTopics(chatBox) {
        // Add a message to the messages array
        const suggestMessage = { name: 'CVSU Admission System', message: 'Hello! Here are some suggested topics:' };
        this.messages.push(suggestMessage);

        // Add suggested topics (you can customize these)
        const suggestedTopics = ['Admission result', 'Eligibility criteria', 'Documentation requirements', 'Application status', 'Application period', 'Other'];

        // Loop through suggested topics and add them as clickable messages


        suggestedTopics.forEach((topic) => {
            const suggestTopicMessage = { name: 'CVSU Admission System', message: topic, clickable: true };
            this.messages.push(suggestTopicMessage);
        });

        // Update the chat text
        this.updateChatText(chatBox);

        // Add click event listeners to clickable messages
        const clickableMessages = chatBox.querySelectorAll('.clickable');
        clickableMessages.forEach((message) => {
            message.addEventListener('click', () => this.onSuggestedTopicClick(message.getAttribute('data-topic'), chatBox));
        });
      
    }

    onSuggestedTopicClick(topic, chatBox) {
        fetch('http://127.0.0.1:5000/predict', {
            method: 'POST',
            body: JSON.stringify({ message: topic }),
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json'
            },
        })
        .then(r => r.json())
        .then(r => {
            const suggestMessage = { name: 'CVSU Admission System', message: 'Hello! Here are the FAQs of the topic :' };
            this.messages.push(suggestMessage);
            const responses = r.answer.split('\n');
            responses.forEach(response => {
                let msg = { name: 'CVSU Admission System', message: response, clickable: true };
                this.messages.push(msg);
            });
    
            this.updateChatText(chatBox);
    
            // Add click event listeners to newly generated clickable messages
            const clickableMessages = chatBox.querySelectorAll('.clickable');
            clickableMessages.forEach((message) => {
                message.addEventListener('click', () => this.onUserClickMessage(message.getAttribute('data-topic'), chatBox));
            });
        })
        .catch((error) => {
            console.error('Error:', error);
            this.updateChatText(chatBox);
        });
    }
    
    onUserClickMessage(topic, chatBox) {
        // Add user's message to the chat history
        let msg = { name: 'User', message: topic };
        this.messages.push(msg);
    
        // Send the user's message to the server
        fetch('http://127.0.0.1:5000/predict', {
            method: 'POST',
            body: JSON.stringify({ message: topic }),
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json'
            },
        })
        .then(r => r.json())
        .then(r => {
            // Process the server's response
            let responseMsg = { name: 'CVSU Admission System', message: r.answer };
            this.messages.push(responseMsg);
    
            // Update the chat text
            this.updateChatText(chatBox);
        })
        .catch((error) => {
            // Handle errors from the server
            console.error('Error:', error);
            this.updateChatText(chatBox);
        });
    }

    sendMessage(message, chatBox) {
      
        this.messages.push({ name: 'User', message });
        this.updateChatText(chatBox);
    }

    toggleState(chatbox) {
        this.state = !this.state;

        // show or hide the box
        if (this.state) {
            chatbox.classList.add('chatbox--active');
        } else {
            chatbox.classList.remove('chatbox--active');
        }
    }

    onSendButton(chatbox) {
        var textField = chatbox.querySelector('input');
        let text1 = textField.value;
        if (text1 === '') {
            return;
        }
    
        // Add user's message to the chat history
        let msg1 = { name: 'User', message: text1 };
        this.messages.push(msg1);
    
        // Send the user's message to the server
        fetch('http://127.0.0.1:5000/predict', {
            method: 'POST',
            body: JSON.stringify({ message: text1 }),
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json'
            },
        })
        .then(r => r.json())
        .then(r => {
            // Process the server's response
            let msg2 = { name: 'CVSU Admission System', message: r.answer };
            this.messages.push(msg2);
    
            // Update the chat text and clear the input field
            this.updateChatText(chatbox);
            textField.value = '';
        })
        .catch((error) => {
            // Handle errors from the server
            console.error('Error:', error);
            
            // Update the chat text and clear the input field
            this.updateChatText(chatbox);
            textField.value = '';
        });
    }

    updateChatText(chatbox) {
        var html = '';
        this.messages.slice().reverse().forEach(function (item, index) {
            if (item.name === 'CVSU Admission System') {
                // Display suggested topics as clickable
                html += '<div class="messages__item messages__item--visitor clickable" data-topic="' + item.message + '">' + item.message + '</div>';
            } else {
                // Check if the message is a suggestion or a response
                if (item.clickable) {
                    html += '<div class="messages__item messages__item--operator clickable" data-topic="' + item.message + '">' + item.message + '</div>';
                } else {
                    html += '<div class="messages__item messages__item--operator">' + item.message + '</div>';
                }
            }
        });
    
        const chatmessage = chatbox.querySelector('.chatbox__messages');
        chatmessage.innerHTML = html;
    }
}

const chatbox = new Chatbox();
chatbox.display();
