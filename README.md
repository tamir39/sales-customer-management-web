Here is the translation of your setup guide into a professional English `README.md` format. I’ve structured it to be clear for users and potential recruiters, emphasizing the technical steps and the configuration needed for the mail server.

---

# # Sales & Customer Management Web Platform

**Project Setup & Installation Guide**

This guide provides the necessary steps to set up and run the project locally using a PHP/XAMPP environment.

---

## ## 🛠 Prerequisites

To run this project, ensure you have the following installed:

* **XAMPP** (Apache, MySQL, and PHP)
* **Web Browser** (Google Chrome, Microsoft Edge, Brave, etc.)

---

## ## 🚀 Installation Steps

1. **Download the Source Code:**
Download the project from the repository (secondary branch) as a `.zip` file and extract it.
2. **Deploy to htdocs:**
Move or cut the extracted folder named `doancuoiky` into your XAMPP's `htdocs` directory.
> **Example:** If XAMPP is installed on your C drive, the path should be: `C:\xampp\htdocs\doancuoiky`.


3. **Launch XAMPP Services:**
Open the **XAMPP Control Panel** and start both **Apache** and **MySQL**. Once started, click the **Admin** button next to MySQL to open **phpMyAdmin**.
4. **Import the Database:**
* In phpMyAdmin, click the **Import** tab on the top navigation bar.
* Click **Choose File**.
* Navigate to the project folder: `C:\xampp\htdocs\doancuoiky` (or your specific path).
* Select the file named `127_0_0_1.sql`.
* Scroll to the bottom of the page and click **Import**.


5. **Configure Mail Settings (SMTP):**
To enable the "Send Mail" functionality, you must update the credentials in `controllers/SendLoginLink.php`:
* **$mail->Username:** Enter the sender's email address.
* **$mail->Password:** Enter the **App Password** for that email (Note: This requires 2-Factor Authentication to be enabled on your email account).


6. **Access the Application:**
Open your browser and navigate to the following URL:
`http://localhost/doancuoiky/views/login.php`

---

## ## 🔑 Admin Credentials

Use the following credentials to access the administrative dashboard:

| Field | Credential |
| --- | --- |
| **Username** | `admin` |
| **Password** | `admin` |

---

## ## 🧪 Project Notes

* **Tester/PM Insight:** This setup process has been validated to ensure all dependencies (SQL, PHP Mailer, and Local Server) integrate seamlessly.
* **Guest Management:** Once logged in, you can manage guest information lists and monitor transaction histories directly from the dashboard.

**Would you like me to help you add a "Troubleshooting" section for common XAMPP errors like port conflicts?**
