package com.example.demo.controller;

import com.example.demo.model.MessagesRequest;
import com.example.demo.service.ChatbotService;
import org.springframework.web.bind.annotation.*;

@RestController
public class ChatbotController {

    private final ChatbotService chatbotService;

    public ChatbotController(ChatbotService chatbotService) {
        this.chatbotService = chatbotService;
    }

    @PostMapping("/chat")
    public String handleMessage(@RequestBody MessagesRequest request) {
        String userMessage = request.getMessage().toLowerCase();

        // Réponses basiques
        if (userMessage.contains("hi") || userMessage.contains("hello") || userMessage.contains("how are you")) {
            return "Bonjour ! Pose-moi une question sur Java 😊.";
        }

        try {
            // Plus besoin de passer les fichiers directement
            return chatbotService.getResponse(userMessage);
        } catch (Exception e) {
            e.printStackTrace();
            return "Désolé, une erreur est survenue : " + e.getMessage();
        }
    }
}