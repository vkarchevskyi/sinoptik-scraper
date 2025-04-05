# Weather scraper of sinoptik.ua

A lightweight PHP library for scraping weather data from [sinoptik.ua](https://sinoptik.ua) — one of Ukraine's most popular weather forecasting websites.

## 🌦️ Features

- Get current weather conditions
- Retrieve 10-day forecasts
- Access weather details by city
- Simple and clean API
- No API key required (web scraping based)

## ⚙️ Installation

You can install the library via Composer:

```bash
composer require vkarchevskyi/sinoptik.ua-parser
```

## 🚀 Usage
```php
$data = new ScraperFactory()
    ->make()
    ->getCurrentTimeData()

echo json_encode($data);
```

Example output:

```json
{
  "time": "15:00",
  "data": {
    "description": "Ясно",
    "temperature": "+12",
    "feelsLike": "+12",
    "pressure": "734",
    "humidity": "64",
    "wind": "2.5",
    "precipitationProbability": "54"
  }
}
```

You can customize scraper by providing city, date or timezone using factory methods:

```php
$data = new ScraperFactory()
    ->setCity('Lviv')
    ->setDate(new DateTimeImmutable('+3 days'))
    ->setTimezone('Europe/Kyiv')
    ->make()
    ->getData()

echo json_encode($data);
```

Example output:

```json
[
  {
    "time": "3:00",
    "data": {
      "description": "Невеликі високі хмари",
      "temperature": "-3",
      "feelsLike": "-3",
      "pressure": "741",
      "humidity": "85",
      "wind": "1.3",
      "precipitationProbability": "17"
    }
  },
  {
    "time": "9:00",
    "data": {
      "description": "Хмарно з проясненнями",
      "temperature": "0",
      "feelsLike": "-3",
      "pressure": "740",
      "humidity": "84",
      "wind": "2",
      "precipitationProbability": "58"
    }
  },
  {
    "time": "15:00",
    "data": {
      "description": "Суцільна хмарність",
      "temperature": "+4",
      "feelsLike": "+1",
      "pressure": "740",
      "humidity": "55",
      "wind": "4.3",
      "precipitationProbability": "76"
    }
  },
  {
    "time": "21:00",
    "data": {
      "description": "Невелика хмарність",
      "temperature": "+1",
      "feelsLike": "-1",
      "pressure": "742",
      "humidity": "57",
      "wind": "1.7",
      "precipitationProbability": "47"
    }
  }
]
```

## 📄 License
MIT License. See [LICENSE](https://github.com/vkarchevskyi/sinoptik.ua-parser/blob/main/LICENCE) for details.

## 🏙 Supported Cities
You can pass any city slug used in the sinoptik.ua URL structure, such as:
`kyiv`
`lviv`
`kharkiv`
`odesa`
`dnipro`

## ❗ Notes
* This library relies on HTML structure of sinoptik.ua, which may change.
* Use responsibly — excessive scraping may lead to IP blocking. 
* This package requires the minimal version of PHP 8.4.
