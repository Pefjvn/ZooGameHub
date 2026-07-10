<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Chatbot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 600px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
        }
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            animation: slideIn 0.3s ease-in;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .message.user {
            justify-content: flex-end;
        }
        .message.bot {
            justify-content: flex-start;
        }
        .message-content {
            padding: 10px 15px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .user .message-content {
            background: #667eea;
            color: white;
        }
        .bot .message-content {
            background: #e0e0e0;
            color: #333;
        }
        .input-area {
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #667eea;
        }
        button {
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
        }
        button:hover {
            transform: scale(1.05);
        }
        button:active {
            transform: scale(0.95);
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            transform: scale(1.05);
        }
        .back-button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <a href="homepage.html" class="back-button">← Back</a>
    <div class="container">
        <div class="header">
            <h1> Zoo Chatbot </h1>
        </div>
        <div class="messages" id="messages">
            <div class="message bot">
                <div class="message-content">
                    Hello! I'm your zoo chatbot. Ask me anything about animals in the zoo!
                </div>
            </div>
        </div>
        <div class="input-area">
            <input type="text" id="userInput" placeholder="Ask about an animal..." />
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        const animalKnowledge = {
            'hi':'Hi! what animal do you want to learn about today?',
            'lion': 'The lion is known as the king of the jungle. Lions are large carnivores that live in groups called prides. They are found in Africa and a small population in India.',
            'elephant': 'Elephants are the largest land animals. They are highly intelligent, emotional, and social creatures. They can live up to 70 years in the wild.',
            'zebra': 'Zebras are striped herbivores native to Africa. Their stripes are unique to each individual, like human fingerprints. They live in herds for protection.',
            'monkey': 'Monkeys are primates known for their intelligence and agility. They are highly social animals and use complex communication methods.',
            'penguin': 'Penguins are flightless birds adapted to life in water. They are excellent swimmers and are found mainly in the Southern Hemisphere.',
            'giraffe': 'Giraffes are the tallest animals on Earth. Their long necks help them reach leaves high in trees. They are found in African savannas.',
            'bear': 'Bears are large carnivores found in various habitats. Despite their size, some species like pandas are herbivores. They are known for their strength.',
            'snake': 'Snakes are legless reptiles found in diverse environments. They are carnivores that swallow their prey whole. They shed their skin as they grow.',
            'peacock': 'Peacocks are known for their stunning, iridescent plumage. Males display their colorful feathers to attract mates. They are native to Asia.',
            'tiger': 'Tigers are the largest cats in the world. They are solitary hunters with beautiful orange and black stripes. They are endangered and found in Asia.',
            'crane': 'Cranes are large, long-legged, and long-necked wading birds belonging to the biological family Gruidae. Famous for their elaborate dancing displays and loud, bugling calls, these omnivorous birds inhabit wetlands, grasslands, and marshes across every continent except Antarctica and South America.',
            'turtle':'A turtle is a reptile belonging to the order Testudines, easily recognized by a special bony or cartilaginous shell. This shell—consisting of a top part called the carapace and a bottom part called the plastron—is fused to their spine and rib cage, meaning a turtle can never leave its shell.',
            'parrot':'Parrots are highly intelligent, vibrant birds belonging to the order Psittaciformes. They are primarily distinguished by two physical traits: a strong, curved bill used for cracking and crushing, and zygodactyl feet (two toes pointing forward, two backward) that allow them to climb and grasp food with dexterity.',
            'panda':'A panda—specifically the giant panda—is a large bear species native to the misty, mountainous forests of central China. Famous for its distinctive black-and-white coat and primarily bamboo-based diet, the giant panda is recognized as a global symbol of wildlife conservation.',
            'cheetah':'The cheetah is a unique and highly specialized wildcat (family Felidae) native to Africa and parts of Iran. Renowned as the fastest land animal on Earth, it can accelerate from 0 to 60 mph in just three seconds and reach peak speeds between 58 and 70 mph.',
            'shrimp':'Shrimp are small, ten-legged aquatic crustaceans found in oceans and freshwater habitats worldwide. Known for their curved bodies, elongated tails, and whiplike antennae, they are highly valued as a global seafood delicacy due to their mild, sweet flavor and versatility in cooking.',
            'falcon':'A falcon is a diurnal bird of prey belonging to the genus Falco and the family Falconidae. They are renowned for their swift, agile flight, sharply pointed wings, and notched, curved beaks used for hunting.',
            'hippo':'A hippo (short for hippopotamus) is a massive, semi-aquatic herbivorous mammal native to sub-Saharan Africa. Known for their barrel-shaped bodies and huge mouths, they are the third-largest land mammals on Earth, recognized as the "horse of the river" in ancient Greek.',
            'dog':'A dog (scientifically known as Canis lupus familiaris) is a domesticated carnivorous mammal belonging to the canine family. As humanitys first domesticated animal, it was selectively bred from an extinct wolf ancestor thousands of years ago and has evolved into hundreds of diverse breeds.',
            'cat':'The domestic cat (Felis catus) is a small, domesticated carnivorous mammal. As the only domesticated species in the family Felidae, it is prized for its companionship and ability to hunt small pests. Cats are characterized by flexible bodies, quick reflexes, sharp retractable claws, and keen senses adapted for killing small prey.',
            'wolf':'A wolf is a highly adaptable, carnivorous mammal belonging to the canine (dog) family. As the largest wild members of the dog family, they are renowned apex predators built for long-distance travel.',
            'dolphin':'Dolphins are highly intelligent, warm-blooded marine mammals belonging to the cetacean family, which also includes whales and porpoises. There are over 40 species of dolphins found in oceans and some freshwater rivers globally. Because they breathe air, they must regularly surface to breathe through a blowhole.',
            'red panda':'A red panda (Ailurus fulgens) is a small, tree-dwelling mammal native to the eastern Himalayas and southwestern China. Weighing about 10 to 14 pounds, it features dense reddish-brown fur, a long ringed tail, and a primarily bamboo diet. Despite their name, they are not closely related to giant pandas and belong to their own unique taxonomic family.',
            'komodo dragon':'The Komodo dragon (Varanus komodoensis) is the largest and heaviest living species of lizard on Earth, native to a few Indonesian islands like Komodo and Flores. As an apex predator, it can grow up to 10 feet long and weigh over 300 pounds, using venomous bites, sharp teeth, and a keen sense of smell to hunt.',
            'goldfish':'A goldfish (Carassius auratus) is a small, freshwater fish in the carp and minnow family. Originally domesticated in ancient China nearly 2,000 years ago from the Prussian carp, it remains one of the most popular and iconic aquarium and pond pets in the world.',
            'sugar glider':'A sugar glider (Petaurus breviceps) is a small, nocturnal, tree-dwelling marsupial native to Australia, Indonesia, and Papua New Guinea. Famous for their oversized eyes and ability to glide up to 150 feet (45 meters) using a skin membrane called a patagium, they are named for their strong appetite for sweet foods like nectar and sap.',
        };


        function sendMessage() {
            const userInput = document.getElementById('userInput');
            const messagesDiv = document.getElementById('messages');
            const message = userInput.value.trim();

            if (message === '') return;

            // Display user message
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'message user';
            userMessageDiv.innerHTML = `<div class="message-content">${escapeHtml(message)}</div>`;
            messagesDiv.appendChild(userMessageDiv);

            // Generate bot response
            const botResponse = getBotResponse(message);
            setTimeout(() => {
                const botMessageDiv = document.createElement('div');
                botMessageDiv.className = 'message bot';
                botMessageDiv.innerHTML = `<div class="message-content">${botResponse}</div>`;
                messagesDiv.appendChild(botMessageDiv);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }, 300);

            userInput.value = '';
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function getBotResponse(userMessage) {
            const message = userMessage.toLowerCase();

            for (const [animal, info] of Object.entries(animalKnowledge)) {
                if (message.includes(animal)) {
                    return info;
                }
            }

            if (message.includes('help') || message.includes('?')) {
                return 'I can answer questions about: hi, lion, elephant. zebra, monkey, penguin, giraffe, bear, snake, peacock, tiger, crane, turtle, parrot, panda, cheetah, shrimp, falcon, hippo, dog, cat, wolf, dolphin, red panda, komodo dragon, goldfish, sugar glider. What would you like to know?';
            }

            return 'I don\'t have information about that. Try asking me about: lion, elephant, zebra, monkey, penguin, giraffe, bear, snake, peacock, or tiger!';
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Allow Enter key to send message
        document.getElementById('userInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
