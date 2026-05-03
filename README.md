# 🍽️ Vite & Gourmand

> Eco-responsible catering web application — full-stack project for the DWWM certification (ECF).


![Status](https://img.shields.io/badge/status-in%20development-orange)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?logo=mysql&logoColor=white)
![MongoDB](https://img.shields.io/badge/MongoDB-8-47A248?logo=mongodb&logoColor=white)
![License](https://img.shields.io/badge/license-Educational-blue)

Application de commande de menus traiteur événementiel pour l'entreprise Vite & Gourmand (Bordeaux).
Engagement éco-responsable, anti-gaspillage, partenaires locaux.

---

## 📋 Table of contents

- [🍽️ Vite \& Gourmand](#️-vite--gourmand)
  - [📋 Table of contents](#-table-of-contents)
  - [🚀 Tech stack](#-tech-stack)
  - [📦 Local installation](#-local-installation)
  - [🔑 Test credentials](#-test-credentials)
  - [📐 Documentation](#-documentation)
    - [MVC Architecture](#mvc-architecture)
    - [Use case diagram](#use-case-diagram)
    - [Sequence diagram — Order workflow](#sequence-diagram--order-workflow)
    - [Class diagram](#class-diagram)
    - [Database model (MCD / ERD)](#database-model-mcd--erd)
  - [🌱 Eco-responsible vision](#-eco-responsible-vision)
  - [🔗 Project links](#-project-links)
  - [📁 Project structure](#-project-structure)
  - [👤 Author](#-author)

---

## 🚀 Tech stack

| Layer              | Technology                              |
| ------------------ | --------------------------------------- |
| Front-end          | HTML5, CSS3, Vanilla JavaScript         |
| Back-end           | PHP 8.3 with PDO                        |
| Relational DB      | MySQL 8.4                               |
| NoSQL DB           | MongoDB 8                               |
| Email service      | PHPMailer + Mailpit (development)       |
| Local environment  | Laragon                                 |
| Deployment         | fly.io                                  |
| Project management | Notion                                  |
| Design             | Figma                                   |
| UML diagrams       | PlantUML                                |
| Versioning         | Git + GitHub                            |

---
## 📦 Local installation

```bash
# Clone the repository
git clone https://github.com/Biba-Com/vite-et-gourmand.git
cd vite-et-gourmand

# Install PHP dependencies
composer install

# Configure environment variables
cp .env.example .env
# Then edit .env with your local credentials

# Initialize the database
mysql -u root -p < database/create.sql
mysql -u root -p < database/fixtures.sql
```
---

## 🔑 Test credentials

| Role     | Email                          | Password       |
| -------- | ------------------------------ | -------------- |
| Admin    | admin@vite-et-gourmand.fr      | Admin@2026     |
| Employee | employe@vite-et-gourmand.fr    | Employe@2026   |
| Client   | client@vite-et-gourmand.fr     | Client@2026    |

---
## 📐 Documentation

### MVC Architecture

The application follows the Model-View-Controller design pattern, ensuring clean separation of concerns.

![MVC Architecture](docs/uml/exports/MVC%20Architecture%20-%20Vite%20%26%20Gourmand.png)

📄 [PlantUML source](docs/uml/architecture-mvc.puml)

### Use case diagram

37 use cases distributed across 4 actors with inheritance (Visiteur → Utilisateur → Employé → Administrateur).

![Use Case Diagram](docs/uml/exports/Vite%20%26%20Gourmand%20-%20UC.png)

📄 [PlantUML source](docs/uml/usecase.puml)

### Sequence diagram — Order workflow

Detailed workflow for placing a multi-service order, including server-side security, database transactions, distance caching, and email notifications.

![Sequence Diagram - Order](docs/uml/exports/Vite%20%26%20Gourmand%20-%20Sequence%20-%20Passer%20une%20commande.png)

📄 [PlantUML source](docs/uml/sequence-commande.puml)

### Class diagram
*(coming soon)*

### Database model (MCD / ERD)
*(coming soon — Merise notation + Mermaid ERD)*

---

## 🌱 Eco-responsible vision

This application includes features beyond the standard catering scope to support sustainability:

- **RSE page** — environmental commitments and carbon footprint
- **Certifications** — organic and eco-friendly labels
- **Local partners** — short-circuit suppliers
- **Anti-waste offers** — discounted menus to reduce food waste
- **Eco badges** on each menu (local, zero-waste, vegetarian)

---

## 🔗 Project links

- 🌐 Live application: *(coming soon)*
- 📋 Notion project board: *(coming soon)*
- 🎨 Figma mockups: *(coming soon)*

---

## 📁 Project structure

\```
vite-et-gourmand/
├── database/                # SQL scripts (create.sql, fixtures.sql)
├── docs/
│   ├── maquettes/           # Figma exports
│   └── uml/                 # PlantUML diagrams
│       └── exports/         # PNG/SVG outputs
├── src/
│   ├── config/              # DB connection, constants
│   ├── controllers/         # MVC — business logic
│   ├── models/              # MVC — database access
│   ├── views/               # MVC — HTML/PHP templates
│   ├── utils/               # Helper functions
│   └── public/              # Web entry point + assets
├── .env.example             # Environment variables template
├── .gitignore
└── README.md
\```

---

## 👤 Author

**Biba-Com** — Web and Mobile Web Developer student
DWWM certification — STUDI 2025/2026