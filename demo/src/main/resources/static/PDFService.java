package com.example.demo.service;

import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;
import org.springframework.core.io.ClassPathResource;
import org.springframework.stereotype.Service;

import java.io.IOException;
import java.io.InputStream;

@Service
public class PDFService {

    // Méthode mise à jour qui ne prend plus les fichiers en paramètre
    public String respondFromPdf(String userMessage) {
        try {
            // Chargement des ressources PDF depuis le classpath
            String text1 = extractTextFromPdfResource("pdfs/pdfFile1.pdf");
            String text2 = extractTextFromPdfResource("pdfs/pdfFile2.pdf");

            // Combine les textes
            String combinedText = text1 + " " + text2;

            // Recherche simple
            if (combinedText.toLowerCase().contains(userMessage.toLowerCase())) {
                return "Oui, j'ai trouvé quelque chose qui correspond dans le PDF :\n\n" +
                        extractRelevantSentence(combinedText, userMessage);
            }

        } catch (IOException e) {
            e.printStackTrace();
            return "Erreur lors de la lecture des PDF : " + e.getMessage();
        }

        return "Désolé, je n'ai pas trouvé une réponse dans le PDF.";
    }

    private String extractTextFromPdfResource(String resourcePath) throws IOException {
        ClassPathResource resource = new ClassPathResource(resourcePath);
        try (InputStream inputStream = resource.getInputStream();
             PDDocument document = PDDocument.load(inputStream)) {
            PDFTextStripper stripper = new PDFTextStripper();
            return stripper.getText(document);
        }
    }

    private String extractRelevantSentence(String fullText, String keyword) {
        String[] sentences = fullText.split("\\.");
        for (String sentence : sentences) {
            if (sentence.toLowerCase().contains(keyword.toLowerCase())) {
                return sentence.trim() + ".";
            }
        }
        return "";
    }
}