<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery</title>
    <style>
        body{
            background-color: black;
        }

        .table-container {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        table{
            border: 1px solid black;
            border-radius: 5px;
            width: 90%;
            box-shadow: 0px 0px 25px 11px rgba(12,221,232,1);
        }

        th{
            background-color: black; 
            color: white; 
            border: 1px solid black;
            height: 30px;
            border-radius: 10px;
            min-width: 100px;
        }

        td{
            background-color: gray; 
            color: aqua; 
            border: 1px solid black;
            text-align: center;
            height: 60px;
        }

        .controls {
            background: linear-gradient(145deg, #2a2a2a, #1f1f1f);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 
                0 0 15px rgba(0, 0, 0, 0.6),
                inset 0 0 10px rgba(0, 0, 0, 0.3),
                0 0 15px 2px rgba(12,221,232,1);
            width: 100vh;
            height: 35vh;
            min-height: 300px;
            margin: 30px auto;
        }

        /* Стилі для полів вводу */
        form {
            width: 100%;
            padding: 10px;
        }

        label{
            color: white;
            font-size: 16px;
            width: 100px;
            display: inline-block;
        }

        input[type="text"], input[type="password"], textarea, select, input[type="number"] {
            width: calc(100% - 110px);
            padding: 12px;
            margin-bottom: 20px;
            background: linear-gradient(145deg, #3a3a3a, #2e2e2e);
            border: 2px solid #444;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.5);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus, textarea:focus, select:focus, input[type="number"]:focus {
            outline: none;
            border-color: #00bfff;
            box-shadow: 
                0 0 10px rgba(0, 123, 255, 0.8),
                inset 0 0 15px rgba(0, 123, 255, 0.4);
        }

        input[type="number"]{
            width: 30%;
        }

        option{
            background-color: #3a3a3a;
        }

        /* Стилізація кнопок */
        button, input[type="submit"], input[type="reset"] {
            background: linear-gradient(145deg, #007BFF, #0056b3);
            border: none;
            color: #fff;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 
                0 0 10px rgba(0, 123, 255, 0.8),
                0 0 20px rgba(0, 123, 255, 0.6);
        }

        button:disabled, input[type="submit"]:disabled, input[type="reset"]:disabled{
            background: linear-gradient(145deg, gray, darkgray);
            box-shadow: 0 0 0 white;
        }

        button:enabled:hover, input[type="submit"]:enabled:hover, input[type="reset"]:enabled:hover {
            background: linear-gradient(145deg, #0056b3, #003d7a);
            box-shadow: 
                0 0 15px rgba(0, 123, 255, 1),
                0 0 30px rgba(0, 123, 255, 0.8);
        }

        button:focus, input[type="submit"]:focus, input[type="reset"]:focus {
            outline: none;
            box-shadow: 
                0 0 20px rgba(0, 123, 255, 1),
                0 0 40px rgba(0, 123, 255, 0.8);
        }

        .errors{
            color: red;
            margin: 5px;
            width: auto;
        }

        .way{
            margin: 5px;
            width: auto;
        }

    </style>
</head>
<body>

    <?php 

        // Transports
        abstract class Transport{
            public float $cost; // for 1 km 1 kilo
            public int $speed; // hours
            public float $accidentProbability; // percent
            public float $indexForChangeDistance;

            function calculateCost(float $distance) : float{
                return $distance * $this->cost;
            }

            abstract function getType() : string;
        }

        class Car extends Transport{ // all towns
            function __construct()
            {
                $this->cost = 0.7;
                $this->speed = 100;
                $this->accidentProbability = 10; // %
                $this->indexForChangeDistance = 0.1;
            }

            function getType() : string{
                return "car";
            }
        }

        class Train extends Transport{ // middle and big towns
            function __construct()
            {
                $this->cost = 0.4;
                $this->speed = 117;
                $this->accidentProbability = 7; // %
                $this->indexForChangeDistance = 0.06;
            }

            function getType(): string{
                return "train";
            }
        }

        class Plane extends Transport{ // only big towns
            function __construct()
            {
                $this->cost = 1.7;
                $this->speed = 30;
                $this->accidentProbability = 1; // %
                $this->indexForChangeDistance = 0.01;
            }

            function getType(): string{
                return "plane";
            }
        }

        // Towns
        class Town{
            public string $code;
            public string $name;
            public string $size; // small, middle, big

            // for calculate distance between towns
            public float $coordX; 
            public float $coordY;

            function __construct(string $code, string $name, string $size, float $coordX, float $coordY)
            {
                $this->code = $code;
                $this->name = $name;
                $this->size = $size;
                $this->coordX = $coordX;
                $this->coordY = $coordY;
            }

        }

        // History
        class HistoryNode{
            public Town $from;
            public Town $to;
            public DateTime|null $dateStart;
            public DateTime|null $dateEnd;
            public Transport $transport;
            public bool $delivered;
            public string $status;
            public float $weight; // kg
            public float $cost; // can be with (-) because of accident

            function __construct(Town $from, Town $to, DateTime|null $dateStart, DateTime|null $dateEnd, Transport $transport, bool $delivered, string $status, float $weight, float $cost)
            {
                $this->from = $from;
                $this->to = $to;
                $this->dateStart = $dateStart;
                $this->dateEnd = $dateEnd;
                $this->transport = $transport;
                $this->delivered = $delivered;
                $this->status = $status;
                $this->weight = $weight;
                $this->cost = $cost;
            }
        }

        // Main class
        class Delivery{
            public $arrayOfTowns;
            public $history;

            function __construct()
            {
                $this->arrayOfTowns = [];
                $this->history = [];
            }

            function addTown(string $code, string $name, string $size, float $coordX, float $coordY){
                $this->arrayOfTowns[$code] = new Town($code, $name, $size, $coordX, $coordY);
            }

            function setArrayOfTowns($arrayOfTowns){
                $this->arrayOfTowns = $arrayOfTowns;
            }

            function restoreHistory($history){
                $this->history = $history;
            }

            function getDistanceBetweenTowns(string $code1, string $code2, Transport $transport) : float{
                if($code1 == $code2){
                    return 0;
                }
                else{
                    $town1 = $this->arrayOfTowns[$code1];
                    $town2 = $this->arrayOfTowns[$code2];

                    if($transport->getType() == "plane"){
                        if($town1->size != "big" || $town2->size != "big"){
                            return 0;
                        }
                    }

                    if($transport->getType() == "train"){
                        if($town1->size == "small" || $town2->size == "small"){
                            return 0;
                        }
                    }

                    // Convert coordinates from degrees to radians
                    $lat1 = deg2rad($town1->coordX);
                    $lon1 = deg2rad($town1->coordY);
                    $lat2 = deg2rad($town2->coordX);
                    $lon2 = deg2rad($town2->coordY);

                    // special formula
                    $q = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos(abs($lon1 - $lon2))); // https://mapgroup.com.ua/glavnaya/astronomicheskie-kalkulyatory/1009-rasstoyanie-mezhdu-dvumya-koordinatamirasstoyanie-mezhdu-dvumya-koordinatami


                    $distance = 6372.795 * $q; // earth radius ±= 6372.795
                }
                return round($distance + $distance * $transport->indexForChangeDistance, 2); // distance can be longer because of rough road (plane can fly right, so his index the least, car has the biggest index because it can move only by road)
            }

            function getCost(string $code1, string $code2, Transport $transport, float $weight){
                return round($transport->calculateCost($this->getDistanceBetweenTowns($code1, $code2, $transport)) * $weight, 2);
            }

            function deliver(string $code1, string $code2, string $_transport, float $weight){
                if($code1 == $code2){
                    return 0;
                }
                else{
                    $transport = match(strtolower($_transport)){
                        "car" => new Car(),
                        "train" => new Train(),
                        "plane" => new Plane()
                    };

                    $town1 = $this->arrayOfTowns[$code1];
                    $town2 = $this->arrayOfTowns[$code2];

                    if($transport->getType() == "plane"){
                        if($town1->size != "big" || $town2->size != "big"){
                            return "Літак не може подорожувати не по великим містам (в них нема аеропортів)!";
                        }

                        $badWeather = rand(1, 5);
                        if($badWeather == 1){
                            $this->history[] = new HistoryNode($town1, $town2, null, null, $transport, false, "Літак не зміг взлетіти через погану погоду!", $weight, 0);
                            return;
                        }
                    }

                    if($transport->getType() == "train"){
                        if($town1->size == "small" || $town2->size == "small"){
                            return "Потяг не може подорожувати по малим містам (в них нема вокзалів)!";
                        }
                    }

                    $way = $this->getDistanceBetweenTowns($code1, $code2, $transport);
                    $cost = $this->getCost($code1, $code2, $transport, $weight);

                    $start = new DateTime();
                    $end = clone $start;
                    $end->modify("+".$transport->speed." hours");

                    $accident = rand(1, (int)(100.0 / $transport->accidentProbability));
                    if($accident == 1){
                        $this->history[] = new HistoryNode($town1, $town2, $start, null, $transport, false, "Посилка не була доставлена через аварію. Вам буде повернено компенсацію у розмірі ".($cost * 2)."грн", $weight, -$cost * 2);
                        return;
                    }

                    $this->history[] = new HistoryNode($town1, $town2, $start, $end, $transport, true, "Посилка доставлена. Доставку на $way"."км коштувала $cost"."грн", $weight, $cost);
                }
                return "";
            }

            function showHistory(){ ?>
                <div class="table-container">
                <table>
                <thead>
                    <tr>
                        <th>Звідки</th>
                        <th>Куди</th>
                        <th>Початок доставки</th>
                        <th>Кінець доставки</th>
                        <th>Транспорт</th>
                        <th>Інформація</th>
                        <th>Вага</th>
                        <th>Прибуток</th>

                    </tr>
                </thead>
                <tbody>
                <?php  ?>
                        <?php 
                        foreach($this->history as $elem):?>
                        <tr>
                            <td><i><?php echo $elem->from->name ?></i></td>
                            <td><i><?php echo $elem->to->name ?></i></td>
                            <td><i><?php echo $elem->dateStart != null ? $elem->dateStart->format('Y-m-d H:i:s') : "Доставки не було" ?></i></td>
                            <td><i><?php echo $elem->dateEnd != null ? $elem->dateEnd->format('Y-m-d H:i:s') : "Доставки не було" ?></i></td>
                            <td><i><?php echo $elem->transport->getType() ?></i></td>
                            <td><i><?php echo $elem->status ?></i></td>
                            <td><i><?php echo $elem->weight." кг" ?></i></td>
                            <td><i><?php echo $elem->cost." грн" ?></i></td>

                    </tr>
                        <?php 
                        endforeach ?>
                    </tbody>
                </table>
                </div>
                <?php 
            }

            function showSelectorOfTowns(int $num){?>
                <select name="town<?php echo $num ?>" id="town<?php echo $num ?>">
                <?php
                    foreach($this->arrayOfTowns as $town){?>
                        <option value="<?php echo $town->code ?>"><?php echo $town->name." (".$town->size.")" ?></option>
                    <?php }
                ?>
                </select>
            <?php }

        }

        $delivery = new Delivery();

        if(!isset($_GET["towns"]))
        {
            $delivery->addTown("KHA", "Kharkiv", "big", 50.0, 36.25);
            $delivery->addTown("KY", "Kyiv", "big", 50.45, 30.523);
            $delivery->addTown("LV", "Lviv", "middle", 49.8397, 24.0297);
            $delivery->addTown("OD", "Odesa", "big", 46.4825, 30.7233);
            $delivery->addTown("ZP", "Zaporizhia", "middle", 47.8388, 35.1396);
            $delivery->addTown("PR", "Paris", "big", 48.8566, 2.3522);
            $delivery->addTown("PL", "Poltava", "middle", 49.5883, 34.5514);
            $delivery->addTown("KL", "Kalinivka", "small", 49.4619, 28.5266);

            $delivery->deliver("LV", "PR", "car", 10);
            $delivery->deliver("KY", "PR", "plane", 12);
            $delivery->deliver("KY", "PR", "plane", 6);
            $delivery->deliver("LV", "PR", "car", 15);
        }
        else{
            $delivery->setArrayOfTowns(unserialize(base64_decode($_GET["towns"])));
            $delivery->restoreHistory(unserialize(base64_decode($_GET["history"])));

            if(isset($_GET["town1"]) && isset($_GET["town2"]) && isset($_GET["weight"]) && isset($_GET["1"])){
                $delivery->deliver($_GET["town1"], $_GET["town2"], $_GET["1"], $_GET["weight"]);
            }
        }

        ?>
            <div class="controls">
            <form>
                <label>Транспорт:</label>
                <input type="radio" id="car" value="car" name="1" checked><label>Автомобіль</label>
                <input type="radio" id="train" value="train" name="1"><label>Поїзд</label>
                <input type="radio" id="plane" value="plane" name="1"><label>Літак</label>
                <label id="way" class="way">Ціна: 0грн</label>
                <br>
                <br>
                <label>Звідки</label>
                <?php $delivery->showSelectorOfTowns(1) ?>
                <label>Куди</label>
                <?php $delivery->showSelectorOfTowns(2) ?>
                <label>Вага</label>
                <input type="number" id="weight" name="weight" step="0.1" min="0.1" value="0.1">
                <input type="hidden" name="towns" value="<?php echo base64_encode(serialize($delivery->arrayOfTowns)) ?>"/>
                <input type="hidden" name="history" value="<?php echo base64_encode(serialize($delivery->history)) ?>"/>
                <button id="send" disabled="true">Відправити</button>
                <label id="message" class="errors"></label>
            </form>
            </div>
        <?php
        $delivery->showHistory();
        $arr = json_encode($delivery->arrayOfTowns)
        
    ?>

    <script>
        let towns = <?= $arr; ?>;
        console.log(towns)
        let from = document.getElementById("town1")
        let to = document.getElementById("town2")
        let sendBtn = document.getElementById("send")
        let rdbtnCar = document.getElementById("car")
        let rdbtnTrain = document.getElementById("train")
        let rdbtnPlane = document.getElementById("plane")
        let message = document.getElementById("message")
        let resCost = document.getElementById("way")
        let weight = document.getElementById("weight");


        function check() {
            if (from.value == to.value) {
                sendBtn.disabled = true;
                message.textContent = "Обрані однакові міста";
            } else {
                sendBtn.disabled = false;
                message.textContent = "";
            }
            

            rdbtnCar.disabled = false;
            rdbtnTrain.disabled = false;
            rdbtnPlane.disabled = false;

            if (towns[from.value].size !== "big" || towns[to.value].size !== "big") {
                rdbtnPlane.disabled = true;
                if (rdbtnPlane.checked) {
                    rdbtnCar.checked = true;
                }
            }

            if (towns[from.value].size === "small" || towns[to.value].size === "small") {
                rdbtnTrain.disabled = true;
                if (rdbtnTrain.checked) {
                    rdbtnCar.checked = true;
                }
            }

            calculateCost();
        }

        function calculateCost(){

            if(from.value === to.value){
                resCost.textContent = "Ціна: 0грн"
                return;
            }

            let cost, index;

            if(rdbtnCar.checked){
                cost = 0.7
                index = 0.1
            }
            else if(rdbtnTrain.checked){
                cost = 0.4
                index = 0.06
            }
            else{
                cost = 1.7
                index = 0.01
            }

            let town1 = towns[from.value]
            let town2 = towns[to.value]

            // Convert coordinates from degrees to radians
            let lat1 = degreesToRadians(town1.coordX)
            let lon1 = degreesToRadians(town1.coordY)
            let lat2 = degreesToRadians(town2.coordX)
            let lon2 = degreesToRadians(town2.coordY)

            // special formula
            let q = Math.acos(Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(Math.abs(lon1 - lon2))) // https://mapgroup.com.ua/glavnaya/astronomicheskie-kalkulyatory/1009-rasstoyanie-mezhdu-dvumya-koordinatamirasstoyanie-mezhdu-dvumya-koordinatami


            let distance = 6372.795 * q // earth radius ±= 6372.795
            distance = distance + distance * index

            resCost.textContent = "Ціна: " + (distance * cost * weight.value).toFixed(2)  + "грн" // distance can be longer because of rough road (plane can fly right, so his index the least, car has the biggest index because it can move only by road)
        }

        function degreesToRadians(degrees) {
            return degrees * Math.PI / 180;
        }

        from.addEventListener('change', check)

        to.addEventListener('change', check)

        rdbtnCar.addEventListener('change', calculateCost)

        rdbtnTrain.addEventListener('change', calculateCost)

        rdbtnPlane.addEventListener('change', calculateCost)

        weight.addEventListener('change', calculateCost)

        check();

    </script>
</body>
</html>