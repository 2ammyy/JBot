package com.example.demo.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.config.annotation.CorsRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

@Configuration
public class WebConfig implements WebMvcConfigurer {

    @Override
    public void addCorsMappings(CorsRegistry registry) {
        registry.addMapping("/**") // Permet les requêtes CORS pour toutes les routes
                .allowedOrigins("http://localhost:3000") // Remplace par l'URL de ton frontend React
                .allowedMethods("GET", "POST", "PUT", "DELETE") // Méthodes HTTP autorisées
                .allowedHeaders("*") // Autorise tous les en-têtes
                .allowCredentials(true); // Autorise les informations d'identification (cookies, etc.)
    }
}
