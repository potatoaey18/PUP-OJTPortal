# 🚀 OJTPortal: A Web-Based Application of On-the-Job Training Monitoring and Information System of the PUP ITECH

*Bridging the gap between classroom theory and real-world mastery*

---

## 🌟 The Vision

Imagine a world where internship applications don't involve endless paperwork shuffling, where students can track their journey from nervous beginners to confident professionals, and where Host Training Establishments can seamlessly connect with tomorrow's industry leaders.

**OJTPortal** isn't just another web application—it's your digital compass for navigating the exciting, sometimes overwhelming journey of On-the-Job Training. Crafted with passion by the innovative minds of **DIT 3-2 Group 1**, this open-source platform transforms the traditional internship chaos into a streamlined, intelligent ecosystem.

---

## ✨ What Makes Us Special?

### 🎯 **Smart Student Command Center**
Your personal mission control featuring:
- Watch your internship hours grow like stars in the night sky
- Navigate through completed and pending assignments
- Daily motivation to fuel your professional journey
- Quick-access portals to announcements and knowledge base

### 🤝 **Intelligent HTE Partnerships**
Transform company connections with:
- Showcase your organization like never before
- From MOAs to contact management, everything flows effortlessly
- Connect with the perfect intern candidates

### 🤖 **Meet Your AI Companion**
Introducing our **Jotform AI Chatbot**—your 24/7 digital mentor:
- 💬 Real-time guidance through application mazes
- 🔍 Instant answers to burning questions
- 🎯 Personalized support for every user type
- 🌐 Always there, floating gently in your browser corner

### 🎛️ **Administrative Excellence Hub**
Empowering administrators with:
-  Control over the entire ecosystem
-  Seamless intern-HTE matching
-  Comprehensive performance tracking
-  Transform data into actionable insights

---

## 🛠️ Powered by Folllowing Technology

**Frontend** ✨
- HTML5 + CSS3
- JavaScript
- Tailwind CSS

**Backend** ⚡
- **PHP**: Database Management

**Data** 🏰
- **MariaDB 10.4.32**
- **UTF8MB4**

**AI Integration** 🧠
- **Jotform AI Chatbot**: Embedded intelligence that learns and grows

---

## 🚀 Launch Sequence: Installation Guide

### 🔧 Mission Prerequisites
Prepare your digital toolkit:
- **PHP** (v7.4+) - Your server-side engine
- **MariaDB** (v10.4.32+) - Your data 
- **Git** - Your version control 
- **Web Server** (Apache/Nginx) - Your digital launchpad
- **Jotform Account** - Your AI chatbot license

### 🎬 Action Steps

**1. Secure the Codebase**
```bash
git clone https://github.com/potatoaey18/PUP-OJTPortal.git
cd OJTPortal
```

**2. Power Up Dependencies**
```bash
# Backend essentials
composer install
```

**3. Construct Your Data Universe**
- Create your `ojtwebportal` database
- Import the schema (`ojtwebportal.sql`) with 25 interconnected tables
- Configure your digital keys in `config.php`:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_localhost');
define('DB_PASSWORD', 'your_secret_key');
define('DB_NAME', 'ojtwebportal');
```

**4. Awaken Your AI Companion**
- Birth your Jotform AI Chatbot
- Embed your digital assistant with this snippet:

```html
<iframe id="JotFormIFrame-0197455c37c672f1a6542aae56a1539bd888"
  title="OJTPortal: AI Support Agent"
  allowtransparency="true"
  allow="geolocation; microphone; camera; fullscreen"
  src="https://agent.jotform.com/YOUR_CHATBOT_ID?embedMode=iframe&background=1&shadow=1"
  frameborder="0"
  style="position: fixed; bottom: 20px; right: 20px; min-width: 300px; max-width: 400px; height: 500px; border: none; z-index: 1000;"
  scrolling="no">
</iframe>
```

**5. Initialize Launch Sequence**
- Configure your web server's document root
- Fire up your engines at `http://localhost/OJTPortal`

---

### 🌟 *"Every great developer was once a beginner. Every expert was once a disaster. Every success story started with a single step into the unknown."*

**Welcome to OJTPortal—where your professional journey begins! 🚀**

---

*Built with ❤️ by DIT 3-2 Group 1 | Empowering the next generation of tech professionals*
