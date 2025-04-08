# Razorpay_gatway
Without SDK Install Razorpay  Integration 
 Step 1: Create Razorpay Account
Go to https://razorpay.com/

Sign up and complete your KYC.

You will get access to the Dashboard.

Step 2: Generate Razorpay API Keys for Testing or Live
From the Dashboard:

Go to Settings → API Keys

Generate Test Keys for development.

After KYC approval, generate Live Keys for production.

Note down your Key ID and Key Secret.

Step 3: Write Backend (PHP) Code
Here is the folder structure for Razorpay integration in PHP:

Step 1: Razorpay Account Create karo

Step 2: Razorpay
API testing or Live Genarate Karo 

Step 3:  Backend (PHP) Code likho
ye Folder Sturact hai

razorpay-integration/
│
├── vendor/                  ← Composer dependencies (auto-generated)
├── config.php              ← API keys & Razorpay object
├── create_order.php        ← Razorpay order create karega
├── index.php               ← Payment button dikhata hai
├── verify_payment.php      ← Payment verification
└── composer.json           ← Composer file (optional)

