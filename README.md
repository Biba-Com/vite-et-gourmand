# 🍽️ Vite & Gourmand

> Eco-responsible catering web application — full-stack project for the DWWM certification (ECF).

![Status](https://img.shields.io/badge/status-online-success)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?logo=mysql&logoColor=white)
![Deployed](https://img.shields.io/badge/deployed-Railway-0B0D0E?logo=railway&logoColor=white)
![License](https://img.shields.io/badge/license-Educational-blue)

Application de commande de menus traiteur événementiel pour l'entreprise Vite & Gourmand (Bordeaux).
Engagement éco-responsable, anti-gaspillage, partenaires locaux.

🌐 **Live application:** https://vite-et-gourmand-production-35bb.up.railway.app

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
    - [Database model — MCD (Merise notation)](#database-model--mcd-merise-notation)
    - [Database model — ERD (Mermaid)](#database-model--erd-mermaid)
    - [Note on entity counts](#note-on-entity-counts)
  - [🌱 Eco-responsible vision](#-eco-responsible-vision)
  - [🔗 Project links](#-project-links)
  - [📁 Project structure](#-project-structure)
  - [👤 Author](#-author)

---

## 🚀 Tech stack

| Layer              | Technology                              |
| ------------------ | --------------------------------------- |
| Front-end          | HTML5, CSS3, Vanilla JavaScript         |
| Charts             | Chart.js (admin statistics)             |
| Back-end           | PHP 8.3 with PDO                        |
| Relational DB      | MySQL 8.4                               |
| Email service      | PHPMailer + Mailpit (development)       |
| Local environment  | Laragon                                 |
| Deployment         | Railway (Docker + Apache)               |
| Project management | Notion                                  |
| Design             | Figma                                   |
| UML diagrams       | PlantUML                                |
| Versioning         | Git + GitHub (GitFlow)                  |

---

## 📦 Local installation

```bash
# Clone the repository
git clone https://github.com/Biba-Com/vite-et-gourmand.git
cd vite-et-gourmand

# Initialize the database (run in this order)
mysql -u root < database/create.sql
mysql -u root < database/fixtures.sql

# Configure the database connection
# Edit src/config/database.php with your local credentials
# (Laragon defaults: host=localhost, user=root, password empty)
```

Then open the application via your local Laragon URL (e.g. `http://vite-et-gourmand.test/` or `http://localhost/vite-et-gourmand/src/public/`).

> **Note:** The application reads database credentials from environment
> variables in production (Railway) and falls back to local defaults
> (Laragon) automatically. No manual switching required.

---

## 🔑 Test credentials

> ⚠️ **Educational project** — these credentials are provided for evaluation
> and testing purposes.

| Role     | Email                              | Password    |
| -------- | ---------------------------------- | ----------- |
| Admin    | jose.viteetgourmand@gmail.com      | Password1@  |
| Employee | julie.viteetgourmand@gmail.com     | password    |
| Client   | sophie.lacaze@gmail.com            | password    |

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

Complete application architecture with 29 classes organized into 7 modules:
1. **Users & Authentication** — User, Session, PasswordResetToken
2. **Catalog** — Menu, Dish, Service, Allergen, Theme, Diet, EcoBadge
3. **Orders** — Order, OrderItem, DistanceCache
4. **Reviews** — Review with moderation workflow
5. **Eco-responsible vision** — AntiWasteOffer, Partner, Certification, AlertSubscription
6. **System** — TeamMember, OpeningHours, RseContent
7. **Audit & Traceability** — OrderStatusHistory, ContactMessage, EmailLog, Notification, AuditLog

![Class Diagram](docs/uml/exports/Vite%20%26%20Gourmand%20-%20Class%20Diagram.png)

📄 [PlantUML source](docs/uml/classe.puml)

### Database model — MCD (Merise notation)

Conceptual data model with 31 entities organized across 7 modules.

![MCD](docs/uml/exports/Vite%20%26%20Gourmand%20-%20MCD.png)

📄 [PlantUML source](docs/uml/mcd.puml)

---

### Database model — ERD (Mermaid)

Interactive entity relationship diagram rendered natively by GitHub.

```mermaid
erDiagram
    UTILISATEUR ||--o{ SESSION : "possede"
    UTILISATEUR ||--o{ TOKEN_MOT_DE_PASSE : "demande"
    UTILISATEUR ||--o{ COMMANDE : "passe"
    UTILISATEUR ||--o{ AVIS : "redige"
    UTILISATEUR ||--o{ ABONNEMENT_ALERTE : "souscrit"
    UTILISATEUR ||--o{ NOTIFICATION : "recoit"
    UTILISATEUR ||--o{ CONTENU_RSE : "modifie"
    UTILISATEUR ||--o{ HISTORIQUE_STATUT : "effectue"
    UTILISATEUR ||--o{ LOG_AUDIT : "trace"
    UTILISATEUR ||--o{ MESSAGE_CONTACT : "assigne_a"
    
    THEME ||--o{ MENU : "categorise"
    MENU ||--o{ COMPOSITION_MENU : "compose"
    PLAT ||--o{ COMPOSITION_MENU : "appartient"
    PLAT ||--o{ PLAT_ALLERGENE : "contient"
    ALLERGENE ||--o{ PLAT_ALLERGENE : "present"
    MENU ||--o{ MENU_REGIME : "respecte"
    REGIME ||--o{ MENU_REGIME : "applique"
    MENU ||--o{ MENU_BADGE_ECO : "etiquete"
    BADGE_ECO ||--o{ MENU_BADGE_ECO : "applique"
    MENU ||--o{ MENU_CERTIFICATION : "certifie"
    CERTIFICATION ||--o{ MENU_CERTIFICATION : "valide"
    
    COMMANDE ||--|{ LIGNE_COMMANDE : "contient"
    MENU ||--o{ LIGNE_COMMANDE : "reference"
    SERVICE ||--o{ LIGNE_COMMANDE : "reference"
    OFFRE_ANTI_GASPI ||--o{ LIGNE_COMMANDE : "vendue_via"
    COMMANDE ||--o| AVIS : "concerne"
    COMMANDE ||--o{ HISTORIQUE_STATUT : "trace"
    MENU ||--o{ OFFRE_ANTI_GASPI : "concerne"
```

📄 [Full ERD with architecture notes](docs/uml/erd-mermaid.md)

### Note on entity counts

The numbers in our documentation reflect different views of the same domain:

- **Class diagram (29 classes)** — Object-oriented application architecture.
  Some simple join tables don't appear as separate classes; their behavior
  is encapsulated in parent class methods.
- **MCD/ERD/SQL (31 tables)** — Physical database structure with explicit
  join tables for many-to-many relationships.

This difference is intentional and follows industry-standard separation
between domain logic (OOP) and data persistence (RDBMS).

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

- 🌐 Live application: https://vite-et-gourmand-production-35bb.up.railway.app
- 📋 Notion project board: https://www.notion.so/Certification-DWWM-3158e984c38080d68fcad936e82e1fb7
- 🎨 Figma mockups: *(see docs/maquettes/)*

---

## 📁 Project structure

```
vite-et-gourmand/
├── database/                # SQL scripts
│   ├── create.sql           # Schema (31 tables)
│   └── fixtures.sql         # Demo data — users, menus, dishes
├── docs/
│   ├── maquettes/           # Figma exports (desktop + mobile)
│   └── uml/                 # PlantUML diagrams
│       └── exports/         # PNG/SVG outputs
├── src/
│   ├── config/              # DB connection (database.php), constants
│   ├── controllers/         # MVC — business logic
│   ├── models/              # MVC — database access
│   ├── views/               # MVC — HTML/PHP templates
│   ├── utils/               # Helper functions
│   └── public/              # Web entry point + assets
├── Dockerfile               # Railway deployment (PHP 8.2 + Apache)
├── .dockerignore
├── .gitignore
└── README.md
```

---

## 👤 Author

**AKHRIB Hassiba** — Web and Mobile Web Developer student
DWWM certification — STUDI 2025/2026
