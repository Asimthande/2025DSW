<?php
// Start the session to check if the user is logged in
session_start();

// Check if the role session variable is set and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    // Redirect to login page if the user is not a student
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Seat Layout</title>
    <link rel="stylesheet" href="reserve-seat.css">
</head>
<body>
    <div class="bus-container">
        <div class="driver-seat">
            <div class="bus-plate">BUS-1234</div>
            <div class="driver-id">Driver ID: 987654</div>
            Driver
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L1</div>
                <div class="seat reserved">L2</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat reserved">R3</div>
                <div class="seat available">R4</div>
                <div class="seat reserved">R5</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L6</div>
                <div class="seat reserved">L7</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R8</div>
                <div class="seat reserved">R9</div>
                <div class="seat available">R10</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L11</div>
                <div class="seat reserved">L12</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R13</div>
                <div class="seat reserved">R14</div>
                <div class="seat available">R15</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L16</div>
                <div class="seat reserved">L17</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat reserved">R18</div>
                <div class="seat available">R19</div>
                <div class="seat reserved">R20</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L21</div>
                <div class="seat reserved">L22</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R23</div>
                <div class="seat reserved">R24</div>
                <div class="seat available">R25</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L26</div>
                <div class="seat reserved">L27</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R28</div>
                <div class="seat reserved">R29</div>
                <div class="seat available">R30</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L31</div>
                <div class="seat reserved">L32</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R33</div>
                <div class="seat reserved">R34</div>
                <div class="seat available">R35</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L36</div>
                <div class="seat reserved">L37</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R38</div>
                <div class="seat reserved">R39</div>
                <div class="seat available">R40</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L41</div>
                <div class="seat reserved">L42</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R43</div>
                <div class="seat reserved">R44</div>
                <div class="seat available">R45</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L46</div>
                <div class="seat reserved">L47</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R48</div>
                <div class="seat reserved">R49</div>
                <div class="seat available">R50</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L51</div>
                <div class="seat reserved">L52</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R53</div>
                <div class="seat reserved">R54</div>
                <div class="seat available">R55</div>
            </div>
        </div>

        <div class="row">
            <div class="seats-left">
                <div class="seat available">L56</div>
                <div class="seat reserved">L57</div>
            </div>
            <div class="aisle"></div>
            <div class="seats-right">
                <div class="seat available">R58</div>
                <div class="seat reserved">R59</div>
                <div class="seat available">R60</div>
            </div>
        </div>

    </div>
    <script src="reserve-seat.js"></script>
</body>
</html>
