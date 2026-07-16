# 🏛️ UCS Monywa Events Management System

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Redis](https://img.shields.io/badge/redis-%23DD0031.svg?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io)
[![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)
[![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

An enterprise-grade, high-performance web application tailored to streamline academic seminars, campus festivals, technical workshops, and administrative ceremonies at the **University of Computer Studies, Monywa (UCSM)**.

---

## 🚀 Key Features

*   **📅 Full Event Lifecycle Management:** Administrative dashboard to dynamically publish, manage, and archive university events.
*   **✍️ Seamless Online Registration (RSVP):** Quick registration modules for students, faculty, and outside guests with seat capacity limits.
*   **⚡ Redis-Powered Speed:** Advanced caching mechanisms to lower database queries under heavy student traffic.
*   **📩 Background Queue Handlers:** Offloads resource-heavy jobs (like sending confirmation emails) to background Redis queues for sub-second UI responsiveness.
*   **🐳 Docker Integration:** Fully-containerized setup for predictable local development and production deployments.

---

## 🛠️ Tech Stack

*   **Backend Framework:** PHP 8.2+ & Laravel (Modern MVC & Service Layer)
*   **Database:** MySQL 8.0+
*   **Caching & Queue Broker:** Redis (using high-performance connection drivers)
*   **Containerization:** Docker Desktop & Docker Compose

---

## ⚙️ Getting Started

Follow these instructions to spin up the development environment on your local machine:

### Prerequisites

Ensure you have the following installed:
*   [PHP 8.2 or higher](https://www.php.net/)
*   [Composer](https://getcomposer.org/)
*   [Node.js & NPM](https://nodejs.org/)
*   [Redis Server](https://redis.io/) (For Windows users, an installer is provided at `Redis-x64-3.0.504.msi` in the root folder)

### Installation Steps

#### 1. Clone the Repository
```bash
git clone [https://github.com/your-username/UCS-Monywa-Events.git](https://github.com/your-username/UCS-Monywa-Events.git)
cd UCS-Monywa-Events-main
