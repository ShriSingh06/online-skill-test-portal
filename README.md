## 📝 README: Online Skill Test Portal

### 💻 Project Summary

A full-stack, secure online examination system built with **PHP 8+, MySQL, HTML, CSS, and Vanilla JavaScript**. It provides separate portals for **Admin** (content/settings management) and **Students** (registration, timed MCQ testing, instant results).

### ✨ Key Features

| Admin Portal (**/admin/**) | Student Portal (**/student/**) | Core Functionality |
| :--- | :--- | :--- |
| **Manage Questions:** Full CRUD for MCQs. | **Secure Auth:** Register/Login with hashed passwords. | **Database:** MySQL, all queries use prepared statements. |
| **View Students:** List of registered users. | **Take Test:** Timed, dynamic MCQ generation based on settings. | **Security:** Hashed passwords, session protection. |
| **Review Results:** Filterable list of all test attempts. | **Instant Grading:** Server-side score calculation upon submission. | **UI/UX:** Clean, responsive, distraction-free test interface. |
| **Global Settings:** Configure duration, question count, and shuffling options. | **Results History:** View scores, percentages, and pass/fail status. | |

### 🛠️ Tech Stack

* **Backend:** PHP 8+ (Procedural/Modular)
* **Database:** MySQL (InnoDB, `mysqli` Prepared Statements)
* **Frontend:** HTML5, CSS3 (Vanilla), Vanilla JavaScript (Timer logic, Validation)

### 🚀 Setup Guide (XAMPP)

1.  **Files:** Place the project in `C:\xampp\htdocs\online-skill-test-portal`.
2.  **DB Setup:**
    * Start Apache and MySQL in XAMPP.
    * Go to `http://localhost/phpmyadmin/`.
    * Create a database named **`online_skill_test_portal`**.
    * Import **`database.sql`** into the new database.
3.  **Run:** Access the application at `http://localhost/online-skill-test-portal/`.

### 🔑 Default Credentials

| Portal | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `Admin@123` |
| **Student** | *(Must Register)* | *(Set own password)* |
