<?php
$apiKey = 'c31923974be673294f52ae5fe2707c57';
$weatherData = null;
$error = '';
$city = '';

if (isset($_GET['city']) && !empty($_GET['city'])) {
  $city = htmlspecialchars($_GET['city']);
  $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . ",ZA&appid=" . $apiKey . "&units=metric";
  $response = file_get_contents($url);

  if ($response !== false) {
    $weatherData = json_decode($response, true);
  } else {
    $error = "Could not retrieve weather data.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Weather Display</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      transition: background 1s ease-in-out;
      overflow: hidden;
      background-color: burlywood;
    }

    .container {
      display: flex;
      flex-direction: column;
      text-align: center;
      background-color: rgba(255, 255, 255, 0.8);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      width: 350px;
      max-width: 95%;
      z-index: 1;
    }

    h1 {
      font-size: 1.8rem;
      margin-bottom: 20px;
    }

    label {
      font-size: 1rem;
      margin-bottom: 8px;
    }

    select, button {
      padding: 10px;
      font-size: 1rem;
      width: 100%;
      margin-bottom: 12px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      background-color: #4CAF50;
      color: white;
      cursor: pointer;
    }

    button:hover {
      background-color: #45a049;
    }

    .weather-grid {
      margin-top: 20px;
    }

    .weather-card {
      background-color: rgba(240, 240, 240, 0.9);
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
    }

    .icon img {
      width: 70px;
      height: 70px;
    }

    .label {
      font-weight: bold;
      font-size: 1.2rem;
      margin-top: 10px;
    }

    .temp {
      font-size: 2rem;
      font-weight: bold;
      margin-top: 10px;
    }

    .additional-info {
      font-size: 0.95rem;
      margin-top: 10px;
      line-height: 1.5;
      text-align: left;
    }

    .error {
      color: red;
      font-size: 1rem;
      margin-top: 10px;
    }

    .sunny         { background: linear-gradient(to bottom, #f7b733, #fc4a1a); color: white; }
    .rain          { background-color: #74c2e1; }
    .cloudy        { background-color: #a1a1a1; color: white; }
    .thunderstorm  { background-color: #6f4e6f; color: white; }
    .snow          { background-color: #e1f5fe; }
    .cold          { background-color: #81d4fa; }
    .windy         { background-color: #6fa3cc; }
    .snow-rain     { background-color: #9fa8da; }
    .partly-sunny  { background-color: #ffe0b2; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Weather Display</h1>
    <form method="GET" action="">
      <label for="city">Select a City:</label>
      <select name="city" id="city">
        <option value="">-- Choose One --</option>
        <?php
          $cities = [
            "Alexandra", "Alberton", "Atteridgeville", "Bekkersdal", "Benoni", "Berea", "Bhongweni", "Blybank", "Boipatong", 
            "Boksburg", "Bophelong", "Braamfontein", "Brandvlei", "Brakpan", "Bryanston", "Carletonville", "Centurion", 
            "Clayville", "Cullinan", "Daveyton", "Devon", "Diepsloot", "Duduza", "Edenvale", "Ekangala", "Eldorado Park", 
            "Emmarentia", "Ennerdale", "Evaton", "Ferndale", "Florida", "Fochville", "Fourways", "Ga-Rankuwa", "Germiston", 
            "Glenvista", "Greenside", "Greenspark", "Hillbrow", "Houghton", "Hekpoort", "Heidelberg", "Hammanskraal", 
            "Hillbrow", "Houghton", "Hekpoort", "Impumelelo", "Irene", "Isando", "Johannesburg", "Kagiso", "Katlehong", 
            "Kempton Park", "Kensington", "Khutsong", "Kokosi", "Kromdraai", "Krugersdorp", "KwaThema", "Lenasia", "Linden", 
            "Magaliesburg", "Mabopane", "Mayfair", "Mamelodi", "Melville", "Midrand", "Modderfontein", "Mohlakeng", 
            "Mondeor", "Morningside", "Muldersdrift", "Munsieville", "Meyerton", "Morningside", "Morningside", 
            "Naturena", "Newtown", "Norwood", "Northcliff", "Orange Farm", "Parkhurst", "Parktown", "Pretoria", 
            "Randburg", "Randfontein", "Randvaal", "Ratanda", "Rayton", "Reiger Park", "Refilwe", "Rietvallei", 
            "Roodepoort", "Rossmore", "Rosebank", "Sandton", "Sebokeng", "Sharpeville", "Silverfields", "Simunye", 
            "Soshanguve", "Soweto", "Springs", "Sunninghill", "Sunnyside", "Tarlton", "Tembisa", "Thokoza", "Toekomsrus", 
            "Tsakane", "Turffontein", "Vanderbijlpark", "Vereeniging", "Vosloorus", "Walkerville", "Waterval", 
            "Wattville", "Welverdiend", "Westonaria", "Winterveld", "Yeoville", "Zenzele", "Zithobeni"
        ];
        
          foreach ($cities as $c) {
            $selected = ($c == $city) ? 'selected' : '';
            echo "<option value=\"$c\" $selected>$c</option>";
          }
        ?>
      </select>
      <button type="submit">Get Weather</button>
    </form>

    <div id="weatherGrid" class="weather-grid">
      <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
      <?php elseif ($weatherData): 
        $main      = $weatherData['weather'][0]['main'];
        $desc      = $weatherData['weather'][0]['description'];
        $temp      = round($weatherData['main']['temp']);
        $icon      = $weatherData['weather'][0]['icon'];
        $humidity  = $weatherData['main']['humidity'];
        $windSpeed = $weatherData['wind']['speed'];
        $clouds    = $weatherData['clouds']['all'];

        function chooseBackground($main) {
          if (str_contains($main, 'Wind'))         return 'windy';
          if (str_contains($main, 'Rain'))         return 'rain';
          if (str_contains($main, 'Thunderstorm')) return 'thunderstorm';
          if (str_contains($main, 'Cloud'))        return 'cloudy';
          if (str_contains($main, 'Clear'))        return 'sunny';
          if (str_contains($main, 'Snow'))         return 'snow';
          if (str_contains($main, 'Drizzle'))      return 'snow-rain';
          if (str_contains($main, 'Mist') || str_contains($main, 'Fog')) return 'cold';
          return 'partly-sunny';
        }

        $bgClass = chooseBackground($main);
      ?>
        <div class="weather-card <?= $bgClass ?>">
          <div class="icon">
            <img src="https://openweathermap.org/img/wn/<?= $icon ?>@2x.png" alt="<?= $desc ?>" />
          </div>
          <div class="label"><?= htmlspecialchars($city) ?></div>
          <div class="temp"><?= $temp ?>°C</div>
          <div class="additional-info">
            <p><strong>Condition:</strong> <?= ucfirst($desc) ?></p>
            <p><strong>Humidity:</strong> <?= $humidity ?>%</p>
            <p><strong>Wind Speed:</strong> <?= $windSpeed ?> m/s</p>
            <p><strong>Cloud Cover:</strong> <?= $clouds ?>%</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>