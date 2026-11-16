# Smart Farming

A PHP/MySQL web application that helps farmers with practical farming information: crops, fertilizers, pesticides, equipment, weather, market prices, crop calendars, cost calculation and an AI-assisted chatbot. It also includes an admin portal for managing content and users.

## Features

- **Farming information** – browse crops, fertilizers, pesticides and equipment lists
- **Crop calendar** – planting and harvesting schedule
- **Calculator** – estimate farming costs, with PDF export (via FPDF)
- **Search** across the farming data
- **Projects** – create and track farm projects
- **Chatbot** – answers questions from the database; optionally uses the Claude API for richer answers
- **User accounts** – farmer registration, login, profile and profile-change requests
- **Helper/trainer sign-up** and testimonials
- **Admin portal** – manage admins, crops and profile-change requests (session-protected)

## Tech stack

- PHP (mysqli)
- MySQL / MariaDB
- HTML, CSS, JavaScript, Bootstrap, jQuery, Owl Carousel
- [FPDF](http://www.fpdf.org/) for PDF generation
- Apache (XAMPP)

## Getting started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)

### Installation

1. Clone the repository into your XAMPP web root:
   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/Shawon-ux/smart_farming.git
   ```
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Create a database named `smart_farming` in phpMyAdmin (`http://localhost/phpmyadmin`) and create/import the tables the app uses: `admin`, `farmer`, `trainer`, `helper`, `crops`, `fertilizer`, `pesticides`, `equipments`, `crop_calendar`, `lands`, `market`, `weather`, `project`, `profile_change_request`.
4. Check the database credentials in `agropro/db.php` (defaults: host `localhost`, user `root`, empty password).
5. Open the app at `http://localhost/smart_farming/agropro/`.

### Chatbot (optional)

By default the chatbot answers from the database only. To enable Claude-powered answers, put your API key in `agropro/ai_config.php`:

```php
return [
    'api_key' => 'YOUR_ANTHROPIC_API_KEY',
    'model'   => 'claude-haiku-5-5',
];
```

Keep real keys out of version control.

## Project structure

```
smart_farming/
├── agropro/
│   ├── index.php, homepage.php     # Landing pages
│   ├── login.php, registration.php # Authentication
│   ├── dashboard.php, profile.php  # User area
│   ├── crop_calendar.php, calculator.php, search.php
│   ├── *_list.php                  # Crop/fertilizer/pesticide/equipment lists
│   ├── chat_api.php, ai_config.php # Chatbot endpoint and config
│   ├── admin_*.php                 # Admin portal
│   ├── db.php                      # Database connection
│   ├── css/, js/, images/, fonts/  # Static assets
│   └── fpdf.php, font/             # PDF library
├── LICENSE
└── README.md
```

## Security notes

Before deploying publicly: set a strong MySQL password, change default credentials, remove `connect.php` (a connection test page) and never commit API keys.

## License

Licensed under the [Apache License, Version 2.0](LICENSE).

```
Copyright 2025 Shawon-ux

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
