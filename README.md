# **PayMoney – Online Payment Gateway System**

PayMoney is a secure, multi-currency online payment gateway and wallet system similar to PayPal or Stripe. It supports deposits, transfers, withdrawals, currency exchange, merchant payments, crypto transactions, TRC-20 tokens, KYC, 2FA, and a wide selection of premium add-ons.

---

## **Features**

* Multi-currency wallet system
* Deposits via PayPal, Stripe, Payeer, PayUMoney, CoinPayments, Coinbase & Bank
* Money transfer to registered/unregistered users
* Request money from users
* Currency exchange (Local or API-based)
* Manual withdrawals (PayPal/Bank)
* Crypto send/receive (BTC, LTC, DOGE, ETH, TRX)
* TRC-20 token deployment & token wallet
* Merchant system with Standard & Express Checkout
* Dispute handling & support ticket system
* KYC (Identity & Address verification)
* 2FA (Email, Phone, Google Authenticator)
* Email templates & SMTP configuration
* User groups, roles & permissions
* Revenue tracking and reporting
* Notification settings per transaction type
* Add-ons: P2P Exchange, Virtual Card, Escrow, Shop, Event Tickets, Referral System & more

---

## **Demo Access**

### **Admin**

* **URL:** [https://demo.paymoney.techvill.net/admin](https://demo.paymoney.techvill.net/admin)
* **Email:** [admin@techvill.net](mailto:admin@techvill.net)
* **Password:** 123456

### **User**

* **URL:** [https://demo.paymoney.techvill.net/login](https://demo.paymoney.techvill.net/login)
* **Email:** [kyla@gmail.com](mailto:kyla@gmail.com)
* **Password:** 123456

### **Merchant**

* **URL:** [https://demo.paymoney.techvill.net/login](https://demo.paymoney.techvill.net/login)
* **Email:** [irish@gmail.com](mailto:irish@gmail.com)
* **Password:** 123456

### **Stripe Test Card**

```
Card Number: 4242 4242 4242 4242
Expiry: 12/29
CVC: 123
```

---

# **Installation Guide**

## **1. Configure Database**

1. Log in to your cPanel
   Visit: `http://yourdomain.com/cpanel`
2. Go to **MySQL Databases**
3. Create a new **Database**
4. Create a **Database User**
5. Assign a password

   > Save your database name, username, and password.
6. Add the user to the database with **ALL Privileges**

If you are unfamiliar with MySQL setup, refer to your hosting provider's documentation.

---

## **2. Upload Files to Server**

1. (Optional) Create a **Subdomain**

   * `Domains → Subdomains`
2. Open **File Manager → Upload**
3. Upload the **PayMoney Installer ZIP** (from CodeCanyon)
4. Extract the ZIP file inside your domain or subdomain root directory

---

## **3. Installation Wizard Steps**

### **Step 1 — Server Requirements**

The installer will automatically check PHP extensions, version, and server configuration.

### **Step 2 — Folder Permissions**

The installer will list folders requiring write permissions.
Apply the required permissions before continuing.

### **Step 3 — Verify Envato Purchase Code**

Enter your **purchase code**.

If you see:

> "Purchase code has already been used"

This means:

* The script was already installed on another domain.
* A **Regular License** allows installation on **1 domain only**.
* You may reinstall on the **same domain** without issues.

To install on multiple domains, you need an **Extended License**.

Local installations are unlimited.

### **Step 4 — Database Settings**

Enter:

* Database name
* Username
* Password
* Host (usually `localhost`)

If credentials are incorrect, the installer will not proceed.

### **Step 5 — Admin Account Setup**

Enter:

* First name
* Last name
* Admin password

After saving, you should see:
**"Installation Successful"**

---

## **4. Post-Installation Setup**

### **Add Currency**

Navigate to:

```
Currencies → Add New
```

If your currency is not listed, create a new one manually.

---

## **5. Optional (Recommended) Setup**

### **Customize Email Templates**

```
Email Templates → Edit
```

Modify templates for Admin and Users.

### **Configure Roles & Permissions**

1. Admin Roles

   ```
   Settings → Roles & Permissions → Add Role
   ```
2. User & Merchant Groups

   ```
   Settings → Users Group
   ```

### **Review PayMoney Settings**

Configure:

* Payment Methods
* Fees & Limits
* Notifications
* 2FA
* KYC
* Preferences
* Merchant Options

---

# **You're Ready to Use PayMoney! 🎉**
