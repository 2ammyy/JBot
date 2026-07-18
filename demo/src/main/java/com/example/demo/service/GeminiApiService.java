package com.example.demo.service;

import com.example.demo.model.ConversationHistory;
import com.example.demo.model.GeminiResponse;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.entity.StringEntity;
import org.apache.http.util.EntityUtils;
import org.springframework.stereotype.Service;

import java.nio.charset.StandardCharsets;
import java.util.List;

@Service
public class GeminiApiService {

    private static final String API_KEY = "AIzaSyDZMqpX48nws24PyBXcqpCa0zd6qvfn7Bw";
    private static final String API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" + API_KEY;

    public String sendMessageToGemini(List<ConversationHistory.Message> history) {
        try (CloseableHttpClient client = HttpClients.createDefault()) {
            HttpPost postRequest = new HttpPost(API_URL);
            postRequest.addHeader("Content-Type", "application/json");

            StringBuilder partsJson = new StringBuilder();
            for (ConversationHistory.Message msg : history) {
                partsJson.append(String.format("""
                    {"role": "%s", "parts": [{"text": "%s"}]},
                """, msg.getRole(), msg.getText().replace("\"", "\\\"")));
            }

            if (partsJson.length() > 0) {
                partsJson.setLength(partsJson.length() - 1);
            }

            String requestBody = String.format("""
            {
              "contents": [
                %s
              ]
            }
            """, partsJson.toString());

            postRequest.setEntity(new StringEntity(requestBody, StandardCharsets.UTF_8));

            return client.execute(postRequest, response -> {
                int statusCode = response.getStatusLine().getStatusCode();
                String responseBody = EntityUtils.toString(response.getEntity());

                if (statusCode != 200) {
                    String errorMsg = responseBody.contains("error")
                            ? new ObjectMapper().readTree(responseBody).path("error").path("message").asText()
                            : responseBody;
                    throw new RuntimeException("Erreur Gemini: " + errorMsg);
                }

                ObjectMapper objectMapper = new ObjectMapper();
                GeminiResponse geminiResponse = objectMapper.readValue(responseBody, GeminiResponse.class);
                return geminiResponse.getCandidates().get(0).getContent().getParts().get(0).getText();
            });

        } catch (Exception e) {
            throw new RuntimeException("Erreur de communication avec Gemini: " + e.getMessage(), e);
        }
    }
}
