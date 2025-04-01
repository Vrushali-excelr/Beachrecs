require('dotenv').config();
const express = require('express');
const cors = require('cors');
const axios = require('axios');

const app = express();
const PORT = 5000;

app.use(cors());
app.use(express.json());

app.get('/weather', async (req, res) => {
  try {
    const { beach } = req.query;
    const apiKey = process.env.OPENWEATHER_API_KEY;
    const response = await axios.get(
      `https://api.openweathermap.org/data/2.5/weather?q=${beach},IN&appid=${apiKey}&units=metric`
    );

    const data = response.data;
    const weatherData = {
      current: {
        temp: data.main.temp,
        humidity: data.main.humidity,
        windSpeed: data.wind.speed,
        description: data.weather[0].description,
        icon: data.weather[0].icon,
        precipitation: data.rain ? data.rain['1h'] || 0 : 0,
        sunset: new Date(data.sys.sunset * 1000).toLocaleTimeString('en-US', { 
          hour: '2-digit', 
          minute: '2-digit',
          hour12: true 
        })
      },
      hourly: Array.from({ length: 5 }, (_, i) => ({
        time: new Date(Date.now() + i * 3600000).toLocaleTimeString('en-US', { 
          hour: '2-digit', 
          minute: '2-digit',
          hour12: true 
        }),
        temp: data.main.temp + (i * 0.5), // Simulated hourly temperatures
        icon: data.weather[0].icon
      }))
    };

    res.json(weatherData);
  } catch (error) {
    console.error('Error fetching weather:', error);
    res.status(500).json({ error: 'Failed to fetch weather data' });
  }
});

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});