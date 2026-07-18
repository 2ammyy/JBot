
package com.example.demo.service;
import java.util.regex.Pattern;
import java.util.regex.Matcher;

import com.example.demo.model.ConversationHistory;
import org.springframework.stereotype.Service;

@Service
public class ChatbotService {

    private final GeminiApiService geminiApiService;
    private final QuestionFilterService questionFilterService;
    private final ConversationHistory conversationHistory;

    public ChatbotService(GeminiApiService geminiApiService,
                          QuestionFilterService questionFilterService,
                          ConversationHistory conversationHistory) {
        this.geminiApiService = geminiApiService;
        this.questionFilterService = questionFilterService;
        this.conversationHistory = conversationHistory;
    }

    public String getConversationalResponse(String message) {
        // Ajouter le message utilisateur à l'historique
        /*conversationHistory.addUserMessage(message);*/
        conversationHistory.addUserMessage(message.toLowerCase());

        // --- DÉTECTION DES SALUTATIONS ---
        if (message.matches("(?i).*\\b(bonjour|salut|hi|hello|salem)\\b.*")) {
            String friendlyGreeting = "Salut ! Comment puis-je t'aider avec Java aujourd'hui ? 😊";
            conversationHistory.addBotMessage(friendlyGreeting);
            return friendlyGreeting;
        }

        // --- DÉTECTION DES PRÉSENTATIONS ---
       conversationHistory.addUserMessage(message);

        // 🔍 Détection du prénom de l'utilisateur
        Pattern namePattern = Pattern.compile("(?i).*\\b(my name is|je m'appelle|mon nom est)\\s+([\\p{L}]+).*");
        Matcher matcher = namePattern.matcher(message);
        if (matcher.matches()) {
            String name = matcher.group(2);
            String response = "Enchanté, " + name + " 😊 ! Comment puis-je t'aider avec Java ?";
            conversationHistory.addBotMessage(response);
            return response;
        }


        // Vérifie si la question est liée à Java
        if (!questionFilterService.isJavaQuestion(message)) {
            String defaultReply = "Je suis un assistant Java. Pose-moi des questions liées au langage Java, s'il te plaît.";
            conversationHistory.addBotMessage(defaultReply);
            return defaultReply;
        }

        // Vérifier l'historique et fournir une réponse appropriée sans répétition
        if (message.equalsIgnoreCase("C'est quoi un abstract ?")) {
            String staticReply = "Un mot-clé est utilisé pour déclarer des éléments qui ne peuvent pas être instanciés ou implémentés directement. Il sert à définir des méthodes qui devront être concrétisées par les éléments dérivés.";
            conversationHistory.addBotMessage(staticReply);
            return staticReply;
        }

        if (message.equalsIgnoreCase("Pourquoi utiliser ces classes ?")) {
            String staticReply = "Ces éléments permettent de définir des comportements communs tout en forçant les sous-classes à implémenter certains comportements spécifiques. Cela favorise la réutilisation du code et permet une organisation plus claire dans les grandes applications.";
            conversationHistory.addBotMessage(staticReply);
            return staticReply;
        }

        
        // Générer une réponse dynamique avec Gemini (en utilisant l'historique complet)
        String response = geminiApiService.sendMessageToGemini(conversationHistory.getMessages());
        conversationHistory.addBotMessage(response);

        return response;
    }
}
