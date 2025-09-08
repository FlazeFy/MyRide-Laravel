# 🚗 MYRIDE

> **MyRide** is your all-in-one **vehicle management solution—designed** to help you effortlessly **store vehicle** details, **track journeys**, **monitor fuel** usage, schedule **washes and services**, and manage multiple **drivers**. Smart reminders ensure you never miss a routine check-up, keeping your car or fleet in top condition. You can access MyRide from anywhere: through our **Mobile App** (Android & iOS), via **Telegram Bot** for quick access right from your chats, or directly from your Web Browser. Stay in control, wherever the road takes you.

## 📋 Basic Information

If you want to see the project detail documentation, you can read my software documentation document. 

1. **Pitch Deck**
https://docs.google.com/presentation/d/12D1PBlqCsYw7Cdynasb9Q8dLB8aKsf9jBN7YmmG_bGE/edit?usp=sharing

2. **Diagrams**
https://drive.google.com/file/d/1vnLsQrQzgCNq_nUeyhhc7uIvDax9SfL1/view?usp=drive_link 

3. **Software Requirement Specification**
https://docs.google.com/document/d/1zy1D59uCgKadgyZg_Ek3k0ITsyaNkQV10YE2VN5jur8/edit?usp=sharing   

4. **User Manual**
https://docs.google.com/presentation/d/1nEcFIxMomTQFx8Y6Xq68eiAc_L3PMdfMJqwgi_1bxks/edit?usp=sharing 

5. **Test Cases**
https://docs.google.com/spreadsheets/d/1waMk940fnZddBjjoQZJ8tIeI1wHz94vkwucoLIoGko8/edit?usp=drive_link 

### 🌐 Deployment URL

- Web : https://myride.leonardhors.com 
- Backend (Swagger Docs) : https://myride.leonardhors.com/api/documentation#/

### 📱 Demo video on the actual device

[URL]

---

## 🎯 Product Overview
- **Manage Vehicle**
You can add your vehicle details such as brand, type, category, and other specifications. Based on this data, you can track fuel consumption, set service or cleaning schedules, and much more.

- **Assign Driver**
Easily assign drivers to specific vehicles and monitor their usage, making it ideal for families, teams, or fleet operations.

- **Fuel Monitoring**
Track fuel volume, brand, type, and cost to analyze consumption patterns and optimize efficiency.

- **Record Your Trip History**
Log your travel routes, visited places, and trip durations to keep a complete journey history.

- **Dashboard & Analytics**
Get a visual overview of your vehicle usage, fuel stats, service & clean schedules, and travel history all in one place.

- **Services Schedule**
Set up and manage routine service appointments like oil changes, tire rotations, and inspections with timely reminders.

- **Set Reminder**
Create custom reminders for anything vehicle-related things. Such as insurance renewals, document updates, travel plan, or personal notes.

- **Clean Schedule**
Plan and track your vehicle cleaning routines to keep it fresh and well-maintained.

## 🚀 Target Users

1. **Vehicle Owners**
Individuals who want to keep track of their personal vehicle’s details, fuel usage, service schedules, and trip history all in one place.

2. **Car Rentals**
Companies that manage multiple vehicles and drivers, needing tools to assign vehicles, monitor usage, schedule maintenance, and track fuel consumption efficiently.

3. **Delivery Companies**
Businesses that rely on vehicles for daily operations and benefit from features like driver assignment, route tracking, fuel monitoring, and service reminders.

4. **Taxi Companies**
Operators managing fleets of taxis and drivers who need to monitor vehicle health, usage patterns, and ensure timely maintenance.

5. **Travel Agents**
Agencies that use vehicles for client transportation and tours, requiring tools to manage trip history, vehicle assignments, and service schedules.

6. **Drivers**
Professional or personal drivers who want to log their trips, monitor fuel usage, receive reminders, and stay informed about vehicle status.

## 🧠 Problem to Solve

1. People often **forget vehicle details**, such as service history, fuel usage, or driver assignments, leading to **missed maintenance** and **inefficient tracking**.
2. It's difficult to **monitor fuel consumption and costs** over time, which can result in unnecessary expenses and **poor planning**.
3. Without a centralized system, users rely on **manual notes** or memory to manage vehicles, drivers, and trips—causing **disorganization** and errors.
4. Scheduling **routine services or cleanings** is often neglected, which affects vehicle performance and longevity.
5. Users lack tools to **analyze vehicle usage**, **track trip history**, or **generate reports**, making it hard to gain insights or optimize operations.

## 💡 Solution

1. Provide a way to **store and manage detailed vehicle** data, including brand, type, category, and specifications, enabling smarter tracking and planning.
2. Allow users to **monitor fuel consumption**, including volume, brand, type, and cost, to help optimize fuel efficiency and budgeting.
3. Offer a **cross-platform solution (Web, Mobile App, Telegram Bot)** that lets users sync and manage vehicle data seamlessly.
4. Enable users to **schedule services and cleanings**, with smart reminders to ensure timely maintenance and upkeep.
5. Include tools to **record trip history**, **assign drivers**, and analyze vehicle usage through **dashboards and reports** for better decision-making.

## 🔗 Features

- 🚗 Vehicle Data Management
- ⏰ Reminder & History
- 📅 Sync with Google Calendar
- 🌍 Location Tracking
- 🤖 Telegram Bot Chat Integration
- 📄 Data Export
- 📊 Analytics & Summaries

---

## 🛠️ Tech Stack

### Backend

- PHP Laravel
- PHP - Telegram Bot

### Database

- MySQL

### Others Data Storage

- Firebase Storage (Cloud Storage for Asset File)

### Infrastructure & Deployment

- Cpanel (Deployment)
- Github (Code Repository)
- Firebase (External Services)

### Other Tools & APIs

- Postman
- Swagger Docs

---

## 🏗️ Architecture
### Structure

### 📁 Project Structure

| Directory/File       | Purpose                                                                                   |
|----------------------|-------------------------------------------------------------------------------------------|
| `app/Exceptions/`    | Custom exception handling logic.                                                          |
| `app/Exports/`       | Data export logic, e.g., for Excel or PDF generation.                                     |
| `app/Helpers/`       | Utility / helper functions used across the app.                                             |
| `app/Http/Controllers/` | Handles incoming HTTP requests and sends responses.                                   |
| `app/Jobs/`          | Queued jobs for background processing.                                                    |
| `app/Mail/`          | Configuration or instance of email broadcast.                                                    |
| `app/Models/`        | Eloquent model definitions mapped to database tables.                                     |
| `app/Providers/`     | Service providers for bootstrapping application services.                                 |
| `app/Rules/`         | Custom form request validation rules like allowed value.                                                     |
| `app/Schedule/`      | Scheduled tasks like cron jobs using Laravel scheduler.                                   |
| `app/Service/`       | External service function like data handling using Firebase and Google Calendar service.                                   |
| `config/`            | Configuration files for services, database, cache, auth, and constants.                   |
| `database/factories/`| Define what kind of data for dummy.                                                 |
| `database/migrations/`| Defines database template.                                                         |
| `database/seeders/`  | Seeds database with default or dummy data.                                              |
| `firebase/`          | Service account JSON.                                                    |
| `public/`            | Publicly accessible folder, serves as the document root for web servers.                  |
| `resources/`         | Views, language files, and other frontend resources.                                      |
| `routes/`            | API routes / endpoints. |
| `storage/`           | File uploads. |
| `tests/`             | Feature and unit tests.                                                                   |
| `tests_reports/`     | Test report outputs.                                           |
| `vendor/`            | Composer-managed PHP dependencies.                                                        |
| `.env`               | Environment-specific variables.                                                           |
| `.env.example`       | Example environment configuration file.                                                   |
| `.gitignore`         | Specifies files and folders to be ignored by Git.                                         |                                             |

---

### 🧾 Environment Variables

To set up the environment variables, just create the `.env` file in the root level directory.

| Variable Name                        | Description                                                              |
|----------------------------------|--------------------------------------------------------------------------|
| `DB_CONNECTION`                  | Database driver/connection (e.g., `mysql`, `pgsql`)                      |
| `DB_HOST`                        | Database host (e.g., `localhost`)                                        |
| `DB_PORT`                        | Database port (e.g., `3306`)                                             |
| `DB_USER`                        | Database username                                                        |
| `DB_PASSWORD`                    | Database password                                                        |
| `DB_DATABASE`                    | Name of the primary database                                             |
| `TEST_DB_HOST`                   | Host for the test database                                               |
| `TEST_DB_PORT`                   | Port for the test database                                               |
| `TEST_DB_USER`                   | Username for the test database                                           |
| `TEST_DB_PASSWORD`               | Password for the test database                                           |
| `TEST_DB_NAME`                   | Name of the test database                                                |
| `FIREBASE_BUCKET_NAME`           | Firebase Storage bucket name for handling file uploads                   |
| `GOOGLE_APPLICATION_CREDENTIALS`| Path to Firebase service account JSON file                               |
| `TELEGRAM_BOT_TOKEN`             | Telegram bot token for chat integration                                  |
| `MAIL_MAILER`                    | Mail transport method (e.g., `smtp`)                                     |
| `MAIL_HOST`                      | Mail server host (e.g., `smtp.mailtrap.io`)                              |
| `MAIL_PORT`                      | Mail server port (e.g., `587`)                                           |
| `MAIL_USERNAME`                  | Mail server username                                                     |
| `MAIL_PASSWORD`                  | Mail server password                                                     |
| `MAIL_FROM_ADDRESS`              | Default email address to send from                                       |
| `MAIL_ENCRYPTION`                | Encryption protocol (e.g., `tls`, `ssl`)                                 |
| `MAIL_FROM_NAME`                 | Name that appears in sent emails                                         |
| `GOOGLE_CLIENT_ID`               | The client ID obtained from Google Cloud Console for OAuth authentication. |
| `GOOGLE_CLIENT_SECRET`           | The client secret associated with the above client ID.                      |
| `GOOGLE_REDIRECT_URI`            | The authorized redirect URI where Google sends users after authentication. |                                 |


---

## 🗓️ Development Process

### Technical Challenges

- **Daily Limitation** for data transaction in Firebase Storage
- Not all **utils (helpers)** can be tested in **automation testing**
- Feature that return the **output in Telegram Chat or Exported File** must be **tested manually** 

---

## 🚀 Setup & Installation

### Prerequisites

#### 🔧 General
- Git installed
- A GitHub account
- Basic knowledge of PHP, Software Testing, Firebase Service, and SQL Databases
- Code Editor
- Telegram Account
- Postman
- Google Console Account

#### 🧠 Backend
- PHP version 8.1 or higher
- Composer version 2.8 or higher
- Git for cloning the repository.
- MySQL database.
- Make (optional), if your project includes a Makefile to simplify common commands.
- Firebase service account JSON file or Google App Credential.
- Telegram Bot token, you can get it from **Bot Father** `@BotFather`
- Telegram User ID for testing the scheduler chat in your Telegram Account. You can get it from **IDBot** `@username_to_id_bot`
- Internet access from the hosting server (for Telegram webhook polling or long-polling)

### Installation Steps

**Local Init & Run**
1. Download this Codebase as ZIP or Clone to your Git
2. Set Up Environment Variables `.env` at the root level directory. You can see all the variable name to prepare at the **Project Structure** before or `.env.example`
3. Install Dependencies using `composer install`
4. **Database Migration** will run if you execute the command `php artisan migrate`.
5. **Seeders** will run if you execute `php artisan db:seed`.
6. **Task Scheduler** in Laravel is managed via `php artisan schedule:run`, usually triggered by a system cron job.
7. **Queue** for background process will run after you execute `php artisan queue:work`.
8. **Run the Laravel** using `php artisan serve`

**CPanel Deployment**
1. Source code uploaded to CPanel
2. Prepare the `.htaccess` in root directory
3. ...

---

## 👥 Team Information

| Role     | Name                    | GitHub                                     | Responsibility |
| -------- | ----------------------- | ------------------------------------------ | -------------- |
| Backend Developer  | Leonardho R. Sitanggang, Angelina Yessyca Rahardjo Hadi | [@FlazeFy](https://github.com/FlazeFy), [@01Angel01](https://github.com/01Angel01)     | Manage Backend and Telegram Bot Codebase         |
| Frontend Developer  | Leonardho R. Sitanggang, Angelina Yessyca Rahardjo Hadi | [@FlazeFy](https://github.com/FlazeFy), [@01Angel01](https://github.com/01Angel01)     | Manage Frontend Codebase         |
| Mobile Developer  | Leonardho R. Sitanggang | [@FlazeFy](https://github.com/FlazeFy)     | Manage Mobile Codebase         |
| System Analyst  | Leonardho R. Sitanggang | [@FlazeFy](https://github.com/FlazeFy)     | Manage Diagram & Software Docs         |
| Quality Assurance  | Angelina Yessyca Rahardjo Hadi, Leonardho R. Sitanggang | [@01Angel01](https://github.com/01Angel01), [@FlazeFy](https://github.com/FlazeFy)     | Manage Testing & Documented The API         |

---

## 📝 Notes & Limitations

### ⚠️ Precautions When Using the Service
- Ensure API endpoints requiring authentication are protected with proper middleware.
- Do not expose sensitive environment variables (e.g., API keys, database credentials) in public repositories.
- Avoid using seeded dummy data in production environments.
- Avoid using seeded dummy data with large seed at the same time.
- Avoid seeding columns that are supposed to contain uploaded file URLs in cloud storage. Doing so will cause errors when using features that modify the file, since the uploaded file URL will be invalid.

### 🧱 Technical Limitations
- Telegram bot polling may cause delays or downtime if the server experiences high load. 
- Google Maps’ interface shows an alert popup and logs errors in the console due to the free plan limitations.

### 🐞 Known Issues
- Limitation when using Firebase Storage for free plan Firebase Service, upgrade to Blaze Plan to use more.
- There are limitations when using the Google Maps API with the free plan. Upgrade your plan in the Google Cloud Console to access additional features.

---

## 🏆 Appeal Points

- 📦 **Comprehensive Vehicle Management**: Effortlessly manage vehicle with details like name, category, type, brand, fuel consumption, service schedule, and the driver.
- 🤖 **Bot Integration**: Integrated with Telegram for real-time reminders to user's messaging application.
- 📊 **Insightful Reports & Analysis**: View visual vehicle, fuel consumption, trip history, and other related data to summaries and generate PDF/Excel reports for audits, or future plans.
- 🔔 **Smart Reminders**: Schedule reminders for services, cleaning, or travel agenda.
- 🧱 **Built with Laravel**: Clean, modular, and testable code using Laravel’s MVC structure, making it robust and easy to extend.

---

_Made with ❤️ by Leonardho R. Sitanggang_