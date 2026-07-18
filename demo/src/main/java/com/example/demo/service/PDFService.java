package com.example.demo.service;

import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;
import org.springframework.core.io.ClassPathResource;
import org.springframework.stereotype.Service;
import org.springframework.util.ResourceUtils;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Logger;

@Service
public class PDFService {
    private static final Logger logger = Logger.getLogger(PDFService.class.getName());

    // Chemins des fichiers PDF dans les ressources
    private static final String PDF1_PATH = "pdfs/pdfFile1.pdf";
    private static final String PDF2_PATH = "pdfs/pdfFile2.pdf";

    public String respondFromPdf(String userMessage) {
        try {
            // Chargement des fichiers PDF depuis les ressources
            String text1 = extractTextFromPdfResource(PDF1_PATH);
            String text2 = extractTextFromPdfResource(PDF2_PATH);

            // Combine les textes
            String combinedText = text1 + " " + text2;

            // Si le message de l'utilisateur est vide ou trop court
            if (userMessage == null || userMessage.trim().length() < 3) {
                return "Veuillez poser une question plus précise.";
            }

            // Recherche améliorée - recherche des mots clés
            List<String> relevantContent = findRelevantContent(combinedText, userMessage);

            if (!relevantContent.isEmpty()) {
                StringBuilder response = new StringBuilder("Voici ce que j'ai trouvé dans les PDFs :\n\n");
                for (String content : relevantContent) {
                    response.append("• ").append(content).append("\n\n");
                }
                return response.toString().trim();
            }
        } catch (IOException e) {
            logger.severe("Erreur lors de la lecture des PDF: " + e.getMessage());
            return "Erreur lors de la lecture des PDF: " + e.getMessage();
        }

        return "Désolé, je n'ai pas trouvé d'informations pertinentes dans les PDF concernant \"" + userMessage + "\".";
    }

    private String extractTextFromPdfResource(String resourcePath) throws IOException {
        ClassPathResource resource = new ClassPathResource(resourcePath);
        try (InputStream inputStream = resource.getInputStream();
             PDDocument document = PDDocument.load(inputStream)) {
            PDFTextStripper stripper = new PDFTextStripper();
            return stripper.getText(document);
        }
    }

    private List<String> findRelevantContent(String fullText, String query) {
        List<String> results = new ArrayList<>();
        String[] keywords = query.toLowerCase().split("\\s+");
        String[] sentences = fullText.split("(?<=[.!?])\\s+");

        for (String sentence : sentences) {
            // Vérifier si la phrase contient au moins un mot-clé de la requête
            boolean isRelevant = false;
            for (String keyword : keywords) {
                if (keyword.length() > 2 && sentence.toLowerCase().contains(keyword)) {
                    isRelevant = true;
                    break;
                }
            }

            if (isRelevant) {
                results.add(sentence.trim());

                // Limiter le nombre de résultats pour ne pas surcharger la réponse
                if (results.size() >= 3) {
                    break;
                }
            }
        }

        return results;
    }
}