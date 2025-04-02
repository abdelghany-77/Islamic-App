# Quran Web Application

![islamic 1](https://github.com/user-attachments/assets/1d58eeaa-a46a-478e-a7b5-787c1be7d37b)

## Description

A beautiful and feature-rich Quran web application built with Laravel. This application provides a complete digital Quran experience with an authentic mushaf-style reading interface, tafsir integration, and audio recitation features.

## ✨ Features

- 📖 **Beautiful Mushaf Reading Interface**
  - Classic Quran page styling with traditional design elements
  - Custom Arabic typography optimized for Quranic text
  - Authentic page styling with decorative borders
  - Collection of Dua and azkar 

- 🔊 **Audio Recitation** (soon)
  - Stream recitations by renowned Qaris
  - Built-in audio player with playback controls
  - Background playing capability

- 📚 **Tafsir Integration** (soon)
  - Read verse-by-verse explanations of the Quran
  - Multiple tafsir sources available
  - Clean, scholarly presentation

- 🔍 **Quran Metadata**
  - Comprehensive Surah information
  - Juz' and Hizb indicators
  - Meccan/Medinan classification

- 📱 **Responsive Design**
  - Works beautifully on all device sizes
  - Optimized for both desktop and mobile reading

- 🔖 **Reading Position Tracking**
  - Remembers your last read verse
  - Easy navigation between surahs

## 📷 Screenshots
![islamic 7](https://github.com/user-attachments/assets/a4404809-270d-46d2-8e2d-189d7a4cdbda)
![islamic 6](https://github.com/user-attachments/assets/927cc241-0a28-42e2-bd36-1314b6e1d2cd)
![islamic 5](https://github.com/user-attachments/assets/d29c01e5-e2e0-4c23-be2d-02483d21416e)
![islamic 4](https://github.com/user-attachments/assets/8cf0fd22-4ef6-4b56-a014-0ee707d77b84)
![islamic 3](https://github.com/user-attachments/assets/780f83c8-feed-4a94-8cd6-ad4838e35c3c)
![islamic 2](https://github.com/user-attachments/assets/1f66d390-4f40-4d94-8854-44a9c7147551)
![islamic 1](https://github.com/user-attachments/assets/4b10821b-d6c4-4062-9d04-404083aaa7a9)



## 🚀 Installation

```bash
# Clone the repository
git clone https://github.com/abdelghany-77/quran-web-app.git

# Navigate to the project directory
cd quran-web-app

# Install dependencies
composer install
npm install

# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Compile assets
npm run dev

# Start the development server
php artisan serve
```

## ⚙️ Configuration

Configure the following environment variables in your `.env` file:

```
QURAN_API_BASE_URL=https://api.quran.com/api/v4
QURAN_DEFAULT_TRANSLATION=en.sahih
QURAN_DEFAULT_TAFSIR=ar.muyassar
QURAN_DEFAULT_RECITER=mishari_rashid_al_afasy
```

## 🔧 API Integration

This application integrates with the following APIs:

| API | Purpose | Documentation |
|-----|---------|---------------|
| Quran.com API | Quran text, translations | [Link](https://quran.api-docs.io/v4/getting-started/introduction) |
| TafsirAPI | Tafsir content | [Link](https://tafsir.app) |
| QuranicAudio | Audio recitations | [Link](https://quranicaudio.com/) |

## 📋 Usage

1. **Browse** through the Surah index
2. **Select** a Surah to read
3. **Listen** to recitation using the audio player
4. **Study** with verse-by-verse tafsir
5. **Track** your reading progress automatically

## 💻 Technologies Used

- **Backend**: Laravel PHP Framework
- **Frontend**: TailwindCSS, Alpine.js
- **Data**: Quran.com API, QuranicAudio

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 🙏 Acknowledgments

- [Quran.com](https://quran.com) for inspiration and API
- [QuranicAudio](https://quranicaudio.com) for audio recitations
- [King Fahd Quran Printing Complex](https://qurancomplex.gov.sa) for Quran text standards

---

<div align="center">
  <p>Made with ❤️ for the Quran</p>
</div>
