<?php
$apiKey = 'c31923974be673294f52ae5fe2707c57';

$citiesAndTownships = [
    "Alexandra", "Rossmore", "Atteridgeville", "Bedfordview", "Benoni", "Bertrams", "Boksburg", 
    "Braamfontein", "Crown Mines", "Diepkloof", "Evaton", "Ekurhuleni", "Ennerdale", "Germiston", 
    "Hillbrow", "Kempton Park", "Brixton", "Richmond", "Crosby", "Khayelitsha", "Kagiso", "Lenasia", 
    "Lindelani", "Mamelodi", "Midrand", "Mofolo", "Orange Farm", "Pretoria", "Reiger Park", "Randburg", 
    "Rosebank", "Parktown", "Melville", "Westcliff", "Berea", "Yeoville", "Doornfontein", "Jeppestown", 
    "Newtown", "Shoshanguve", "Soweto", "Tembisa", "Vanderbijlpark", "Westbury", "Zola", "Zeerust"
];

sort($citiesAndTownships);

$weatherData = null;
$errorMessage = null;
$weatherCondition = 'sunny';
$seasonEmoji = '';
$weatherEmoji = '';
$season = '';

// Determine South African season based on current month
$month = date('n'); // 1 = Jan, 12 = Dec
if ($month >= 6 && $month <= 8) {
    $season = "Winter";
    $seasonEmoji = '❄️';
} elseif ($month >= 9 && $month <= 11) {
    $season = "Spring";
    $seasonEmoji = '🌸';
} elseif ($month >= 12 || $month <= 2) {
    $season = "Summer";
    $seasonEmoji = '☀️';
} elseif ($month >= 3 && $month <= 5) {
    $season = "Autumn";
    $seasonEmoji = '🍂';
}

if (isset($_GET['city']) && !empty($_GET['city'])) {
    $city = $_GET['city'];

    $apiUrl = "http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";

    $response = file_get_contents($apiUrl);
    $weatherData = json_decode($response, true);

    if ($weatherData['cod'] != 200) {
        $errorMessage = "City not found! Please try again.";
    } else {
        $weatherCondition = strtolower($weatherData['weather'][0]['main']);

        // Assign emoji based on weather
        switch ($weatherCondition) {
            case 'clear':
            case 'sunny':
                $weatherEmoji = '🌞';
                break;
            case 'rain':
                $weatherEmoji = '🌧️';
                break;
            case 'clouds':
                $weatherEmoji = '☁️';
                break;
            case 'thunderstorm':
                $weatherEmoji = '⛈️';
                break;
            case 'snow':
                $weatherEmoji = '❄️';
                break;
            default:
                $weatherEmoji = '🌡️';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link rel="stylesheet" href="weather.css">
</head>
<body class="<?php echo $weatherCondition; ?>">
    <div class="emoji">
        <?= $weatherEmoji . ' ' . $seasonEmoji ?>
    </div>
    <div class="container">
        <h1>Weather App</h1>
        <form method="get">
            <label for="city">Select City/Township:</label>
            <select name="city" id="city">
                <option value="">Select...</option>
                <?php
                foreach ($citiesAndTownships as $town) {
                    echo "<option value=\"$town\"";
                    if (isset($city) && $city == $town) {
                        echo " selected";  
                    }
                    echo ">$town</option>";
                }
                ?>
            </select>
            <button type="submit">Get Weather</button>
        </form>

        <?php if ($errorMessage): ?>
            <p class="error"><?= $errorMessage ?></p>
        <?php elseif ($weatherData): ?>
            <div class="weather-info">
                <p><strong>Weather:</strong> <?= $weatherData['weather'][0]['description'] ?></p>
                <p><strong>Temperature:</strong> <?= $weatherData['main']['temp'] ?>°C</p>
                <p><strong>Humidity:</strong> <?= $weatherData['main']['humidity'] ?>%</p>
                <p><strong>Wind Speed:</strong> <?= $weatherData['wind']['speed'] ?> m/s</p>
                <p><strong>Current Season:</strong> <?= $season ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
