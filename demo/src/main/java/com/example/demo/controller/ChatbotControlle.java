package com.example.demo.controller;

import com.example.demo.service.ChatbotService;
import com.example.demo.model.MessagesRequest;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/chat")
public class ChatbotController {

    private final ChatbotService chatbotService;

    public ChatbotController(ChatbotService chatbotService) {
        this.chatbotService = chatbotService;
    }

    @PostMapping
    public String handleMessage(@RequestBody MessagesRequest request) {
        try {
            // On envoie la question au service et on récupère la réponse
            String response = chatbotService.getConversationalResponse(request.getMessage()); // Seulement un argument
            return response;
        } catch (Exception e) {
            e.printStackTrace();
            return "Désolé, une erreur est survenue : " + e.getMessage();
        }
    }

}
