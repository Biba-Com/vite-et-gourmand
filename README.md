# 🍽️ Vite & Gourmand

> Eco-responsible catering web application — full-stack project for the DWWM certification (ECF).

Application de commande de menus traiteur événementiel pour l'entreprise Vite & Gourmand (Bordeaux).
Engagement éco-responsable, anti-gaspillage, partenaires locaux.

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

---

## 📦 Local installation

```bash
git clone https://github.com/Biba-Com/vite-et-gourmand.git
cd vite-et-gourmand
composer install
cp .env.example .env
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

### Use case diagram
*(coming soon)*

### Database model
*(coming soon — Mermaid ERD)*

---

## 🌱 Eco-responsible vision

This application includes special features beyond the standard catering scope:

- **RSE page** — environmental commitments
- **Certifications** — organic and eco-friendly labels
- **Local partners** — short-circuit suppliers
- **Anti-waste offers** — discounted menus
- **Eco badges** on each menu

---

## 🔗 Project links

- 🌐 Live application: *(coming soon)*
- 📋 Notion project board: *(coming soon)*
- 🎨 Figma mockups: *(coming soon)*

---

## 👤 Author

**Biba-Com** — Web and Mobile Web Developer student
DWWM certification — STUDI 2025/2026
