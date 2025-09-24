document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Element References ---
    const chatBubble = document.getElementById('chat-bubble');
    const chatWindow = document.getElementById('chat-window');
    const closeChatBtn = document.getElementById('close-chat');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const typingIndicator = document.getElementById('typing-indicator');

    // --- Agent's General Knowledge & Personality (Wichy Company) ---
    const systemInstruction = `You are a friendly and professional AI assistant for Wichy Plantation Company.
        Your primary goal is to accurately answer user questions about our company, our values, and our wide range of coconut-based products.

        Here is some important information about our business:
        - Nethmina is the owner of Wichy Plantation company.
        - The creators are Anjana,Hiranya,Nimantha,Pasan,Avishka,Dasun.
        - We are Wichy Plantation Company (Pvt) Ltd, a renowned Sri Lankan company with over 30 years of experience in high-quality coconut-based products.
        - Our mission is to provide healthy, natural food and beverages to the world while supporting local communities and practicing sustainability.
        - Our main product categories include:
          * Cooking Ingredients: Such as coconut oil, coconut milk, and coconut cream.
          * Bakery Products: Including coconut flour.
          * Beverages: Like natural coconut water.
          * Specialty Foods: Innovative spreads and items with Asian flavors.
        - Many of our products are available in eco-friendly Tetra Recart packaging.
        - Certifications: Our products are certified organic, 100% natural, vegan, and gluten-free.

        Use the information above to answer questions about our main products, their health benefits, our eco-friendly practices, and how we support local communities.
        If a user asks where to buy our products, advise them to visit our official website for the most accurate information.
        If you don't have the information to answer a question, politely say so. Always be helpful and professional.
        `;

    // --- NEW: Mock Database for Wichy Products ---
    const mockWichyProductDB = [
        { id: 1, name: "Organic Virgin Coconut Oil", type: "cooking", dietary: ["organic", "vegan", "gluten-free"], feature: "cooking" },
        { id: 2, name: "Coconut Milk Classic", type: "cooking", dietary: ["vegan", "gluten-free"], feature: "cooking" },
        { id: 3, name: "Coconut Cream", type: "cooking", dietary: ["vegan", "gluten-free"], feature: "cooking" },
        { id: 4, name: "Organic Coconut Flour", type: "bakery", dietary: ["organic", "gluten-free"], feature: "baking" },
        { id: 5, name: "Natural Coconut Water", type: "beverage", dietary: ["natural", "vegan"], feature: "hydration" },
        { id: 6, name: "Spicy Coconut Spread", type: "specialty", dietary: ["vegan"], feature: "spread" },
        { id: 7, name: "Coconut Milk in Tetra Recart", type: "cooking", dietary: ["vegan"], feature: "eco-friendly packaging" }
    ];

    // --- NEW: Agent's Tool for Wichy Products ---
    const tools = [{
        "functionDeclarations": [
            {
                "name": "find_wichy_products",
                "description": "Searches for Wichy company products based on user criteria like product type or dietary needs.",
                "parameters": {
                    "type": "OBJECT",
                    "properties": {
                        "product_type": { "type": "STRING", "description": "The category of the product, e.g., 'cooking', 'beverage', 'bakery'." },
                        "dietary_need": { "type": "STRING", "description": "A dietary requirement, e.g., 'organic', 'vegan', 'gluten-free'." },
                        "feature": { "type": "STRING", "description": "A specific product feature, e.g., 'eco-friendly packaging'." }
                    },
                    "required": []
                }
            }
        ]
    }];

    // --- NEW: JavaScript Implementation for the Wichy Tool ---
    const find_wichy_products = (args) => {
        console.log("Searching for Wichy products with criteria:", args);
        let results = [...mockWichyProductDB];

        if (args.product_type) {
            results = results.filter(p => p.type.toLowerCase() === args.product_type.toLowerCase());
        }
        if (args.dietary_need) {
            results = results.filter(p => p.dietary.includes(args.dietary_need.toLowerCase()));
        }
        if (args.feature) {
            results = results.filter(p => p.feature.toLowerCase() === args.feature.toLowerCase());
        }

        if (results.length > 0) {
            return { products_found: results.map(p => p.name) };
        } else {
            return { products_found: [], message: "No products found matching those criteria." };
        }
    };

    const availableTools = {
        find_wichy_products
    };

    // --- State Management ---
    let conversationHistory = [];

    // --- CORRECTED UI & Message Handling Logic ---
    const toggleChatWindow = (forceOpen = null) => {
        const isHidden = chatWindow.classList.contains('hidden');
        if (forceOpen === true || (forceOpen === null && isHidden)) {
            chatWindow.classList.remove('hidden');
            setTimeout(() => chatWindow.classList.remove('opacity-0', 'translate-y-4'), 10);
        } else if (forceOpen === false || (forceOpen === null && !isHidden)) {
            chatWindow.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => chatWindow.classList.add('hidden'), 300);
        }
    };

    // Open/Close by clicking the bubble
    chatBubble.addEventListener('click', () => toggleChatWindow());
    // Close by clicking the close button
    closeChatBtn.addEventListener('click', () => toggleChatWindow(false));
    // Close chat panel ONLY when clicking OUTSIDE of it
    document.addEventListener('mousedown', (e) => {
        if (!chatWindow.classList.contains('hidden') && !chatWindow.contains(e.target) && !chatBubble.contains(e.target)) {
            toggleChatWindow(false);
        }
    });

    const appendMessage = (sender, message) => {
        const sanitizedMessage = message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const msgHTML = `<div class="flex mb-4 ${sender === 'user' ? 'justify-end' : 'justify-start'}">
                            <div class="p-3 rounded-lg max-w-xs break-words ${sender === 'user' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-800'}">
                                <p>${sanitizedMessage}</p>
                            </div>
                         </div>`;
        chatMessages.insertAdjacentHTML('beforeend', msgHTML);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    const handleSendMessage = async () => {
        const userMessage = chatInput.value.trim();
        if (!userMessage) return;
        appendMessage('user', userMessage);
        chatInput.value = '';
        typingIndicator.classList.remove('hidden');
        conversationHistory.push({ role: "user", parts: [{ text: userMessage }] });

        try {
            const botMessage = await getGeminiResponse();
            appendMessage('bot', botMessage);
        } catch (error) {
            console.error("Error:", error);
            appendMessage('bot', "Sorry, something went wrong. Please try again.");
        } finally {
            typingIndicator.classList.add('hidden');
        }
    };

    sendBtn.addEventListener('click', handleSendMessage);
    chatInput.addEventListener('keydown', (e) => (e.key === 'Enter' && !e.shiftKey) ? (e.preventDefault(), handleSendMessage()) : null);

    // --- AI Agent Logic with Function Calling ---
    const getGeminiResponse = async () => {
        // IMPORTANT: Replace with your actual API key and secure it properly!
        const apiKey = "AIzaSyA1smBCJlHQGtSaTjF-nwC8Jg849F93Ghg"; // <--- PASTE YOUR GEMINI API KEY HERE
        const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${apiKey}`;

        const payload = {
            contents: conversationHistory,
            tools: tools,
            systemInstruction: { parts: [{ text: systemInstruction }] }
        };

        let response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (!response.ok) throw new Error(`API Error: ${response.status} ${await response.text()}`);
        let result = await response.json();

        const content = result.candidates?.[0]?.content;
        if (!content) return "I'm sorry, I couldn't process that.";

        const functionCall = content.parts.find(part => part.functionCall)?.functionCall;

        if (functionCall) {
            const functionName = functionCall.name;
            const functionArgs = functionCall.args;
            if (availableTools[functionName]) {
                const functionResult = availableTools[functionName](functionArgs);
                conversationHistory.push(content);
                conversationHistory.push({
                    role: "tool",
                    parts: [{ functionResponse: { name: functionName, response: functionResult } }]
                });

                const secondPayload = { ...payload, contents: conversationHistory };
                response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(secondPayload)
                });
                if (!response.ok) throw new Error(`API Error: ${response.status} ${await response.text()}`);
                result = await response.json();

                const finalResponse = result.candidates?.[0]?.content?.parts?.[0]?.text;
                conversationHistory.push({ role: "model", parts: [{ text: finalResponse }] });
                return finalResponse || "Task completed.";
            } else {
                return `I tried to use a tool named '${functionName}', but I couldn't find it.`
            }
        }

        const textResponse = content.parts[0].text;
        conversationHistory.push({ role: "model", parts: [{ text: textResponse }] });
        return textResponse;
    };

    // --- UPDATED: Initial Greeting for Wichy Company ---
    appendMessage('bot', 'Welcome to Wichy Plantation Company! How can I help you with our coconut products today? 🥥');
});
// --- FINAL FIX: More Robust UI & Message Handling Logic ---

const toggleChatWindow = (forceOpen = null) => {
    const isHidden = chatWindow.classList.contains('hidden');

    if (forceOpen === true || (forceOpen === null && isHidden)) {
        // Open the window
        chatWindow.classList.remove('hidden');
        setTimeout(() => chatWindow.classList.remove('opacity-0', 'translate-y-4'), 10);
    } else if (forceOpen === false || (forceOpen === null && !isHidden)) {
        // Close the window
        chatWindow.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => chatWindow.classList.add('hidden'), 300);
    }
};

// 1. Bubble eka click kalama, window eka open/close karanna
chatBubble.addEventListener('click', () => toggleChatWindow());

// 2. Close button eka click kalama, window eka close karanna
closeChatBtn.addEventListener('click', () => toggleChatWindow(false));

// 3. Document eke (pitatha) click kalama, window eka close karanna
document.addEventListener('mousedown', () => {
    // Window eka open eke thiyenawa nam witharak close karanna
    if (!chatWindow.classList.contains('hidden')) {
        toggleChatWindow(false);
    }
});

// 4. IMPORTANT FIX: Window eka athule saha bubble eka uda click kalama,
// e click eka document ekata yana eka nawaththanawa (stopPropagation).
// Methanin thamai auto-close wena eka 100%ma nathara karanne.
chatWindow.addEventListener('mousedown', (event) => event.stopPropagation());
chatBubble.addEventListener('mousedown', (event) => event.stopPropagation());

// --- END OF FIX ---
