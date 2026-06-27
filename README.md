# UCS Monywa - Student & Event Management Portal (Admin Panel)

A secure and responsive web application designed for administration at the University of Computer Studies, Monywa. This portal serves as a centralized dashboard to manage student registrations, approve accounts, and coordinate university events with automated notification systems.

## 🚀 Key Features

- **Dashboard Analytics:** Real-time summary counts of Total Registered, Approved, and Pending students using database triggers.
- **Student Approval Workflow:** Admin can view pending registrations sorted by latest date and approve students with a structured logical flow.
- **Automated Email Notifications:** Integrated with **PHPMailer** to automatically send confirmation and welcome emails to students once approved.
- **Event Management System:** - Create, view, and delete university events (e.g., Freshers' Welcome).
  - Define location, date, time, and specific participant limits (`max_participants`).
  - Track registered student lists for individual events.
  - Broadcast new event notification emails to all students at once.

## 🛠️ Tech Stack & Dependencies

- **Backend Language:** PHP (Session-based Authentication, cURL API handling)
- **Database / BaaS:** Supabase (REST API integration with secure API Keys)
- **Dependency Manager:** Composer
- **Libraries used:** - `phpmailer/phpmailer` (SMTP Email delivery)
  - `predis/predis` (Optional caching/session performance)
- **Frontend UI:** HTML5, CSS3 (Modern Dark Theme UI inspired by Supabase dashboard layout), FontAwesome icons.

## ⚙️ Installation & Setup

1. Clone this repository.
2. Install dependencies using Composer:
   ```bash
   composer install
