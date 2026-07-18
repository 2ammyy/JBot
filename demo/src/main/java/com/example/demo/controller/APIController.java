package com.example.demo.controller;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class APIController {

    @Value("${api.key}")
    private String apiKey;

    @GetMapping("/apikey")
    public String getApiKey() {
        return apiKey;  
    }
}
