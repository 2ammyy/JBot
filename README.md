# JBOT - Java Assistance Chatbot

## Table of Contents
1. [Introduction](#introduction)
2. [Project Context & Problematic](#project-context--problematic)
3. [Demo Videos](#demo-videos)
4. [Features](#features)
5. [Actors](#actors)
6. [Technical Design](#technical-design)
7. [Technologies Used](#technologies-used)
8. [Contributors](#contributors)

---

## Introduction
JBOT is an intelligent chatbot application designed to provide instant assistance on Java concepts, syntax, best practices, and common issues. It is primarily aimed at developers, students, and programming enthusiasts, offering a fluid, intuitive, and interactive web interface to facilitate learning and problem-solving.

---


## Project Context & Problematic
Beginning developers often rely on various resources (websites, videos, forums) which can provide answers that are too complex, unpersonalized, or scattered. This leads to a fragmented learning experience.

**The core problem addressed by JBOT is:** How can we help beginners better understand Java through an interactive, user-friendly chatbot that delivers clear, contextual, and level-appropriate answers?

---
## Demo Videos
Below are the demonstration videos for the JBOT project:

### Video 1: Functional Features Demo
> Showcasing the main features: asking questions, receiving code examples, and code correction.

https://github.com/user-attachments/assets/fb4b7f4c-94bd-4efe-92e6-95118a693c31

---

### Video 2: Architecture & Performance Demo
> Showcasing the backend architecture, response times, and admin features.

https://github.com/user-attachments/assets/a41ce93f-8472-4e2e-8fca-1caef1bb3b9e

---

## Features
The application fulfills several key functional requirements:

- **Basic Q&A:** Answers fundamental questions about Java.  
  *Examples: "What is a class?", "How to write a for loop?", "What is the static keyword?"*

- **Code Examples:** Provides simple, clear code snippets to illustrate each concept.

- **Line-by-Line Explanation:** Details the purpose of each line of a provided code snippet to aid comprehension.

- **Code Correction:** Identifies errors in user-provided code snippets and suggests corrected versions.

- **Natural Language Processing:** Understands questions posed in a natural, conversational manner.  
  *Example: "How do I declare a variable in Java?"*

---

## Actors
Three primary actors interact with the system:

| Actor | Description |
|-------|-------------|
| **Beginner User** | Asks questions and learns Java through interaction with the chatbot. |
| **Administrator** | Manages chatbot settings (updates, availability, testing). |
| **JBOT** | The chatbot itself, which responds to questions, explains concepts, and corrects simple errors. |

---

## Technical Design
The application architecture is as follows:

- **Backend:** Spring Boot framework providing RESTful APIs.
- **Frontend:** HTML, CSS, and JavaScript for a dynamic and responsive user interface.
- **Database:** MySQL for data persistence.
- **NLP Engine:** Integration with an external AI API (Gemini) for natural language processing.

---

## Technologies Used
- **Java** - Core programming language
- **Maven** - Build automation and dependency management
- **Spring Boot** - Backend framework
- **MySQL** - Relational database
- **HTML/CSS/JS** - Frontend development

---



## Contributors
This project was developed by:

- Nour Yahya

- Amira Hamdani

- Yassmine Ouertani

---

### License
This project is developed for educational purposes as part of a mini-project assignment.

---

Thank you for your attention!
