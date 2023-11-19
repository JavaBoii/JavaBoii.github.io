<!DOCTYPE html>
<html lang="de">
<head>
    <title>Radar-Chart Demo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        .chart-container {
            height: 500px; /* or any other height */
            width: 500px;  /* or any other width */
            margin: auto; /* This centers the div */
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
        }

        canvas {
            background-color: #fff;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
    </style>

</head>
<body>
<div class="chart-container">
    <canvas id="meinRadarChart"></canvas>
</div>
<script>
    // Daten

    let gender = [
        {sex: 'male', points: 45},
        {sex: 'female', points: 60},
        {sex: 'diverse', points: 30},
    ];

    let pupils = [
        {color: 'blue', points: 60},
        {color: 'brown', points: 32},
        {color: 'green', points: 60},
        {color: 'grey', points: 50},
        {color: 'hazel', points: 51},
        {color: 'amber', points: 50}
    ];

    // Optimal ranges
    const optimalHeightRange = [180, 200]; // Optimal range for height in cm
    const optimalWeightRange = [70, 100];   // Optimal range for weight in kg


    <?php
    function sanitizeInput($data): string
    {
        return htmlspecialchars(trim($data));
    }
    // Check if all parameters are set and not empty
    if (isset($_POST['age'], $_POST['gender'], $_POST['origin'], $_POST['height'], $_POST['weight'], $_POST['pupilColor'])) {
        if (is_string($_POST['origin']) && is_array(json_decode($_POST['origin'], true))) {
            $origin = json_decode($_POST['origin'], true);
        } else {
            // Set to default if not valid
            $origin = ["de", 59.95, 1.0];
        }

        $applicant = [
            'age' => sanitizeInput($_POST['age']),
            'gender' => sanitizeInput($_POST['gender']),
            'origin' => $origin,
            'height' => sanitizeInput($_POST['height']),
            'weight' => sanitizeInput($_POST['weight']),
            'pupilColor' => sanitizeInput($_POST['pupilColor'])
        ];
    } else {
    ?>
    console.log("No parameters set, using default values");
    <?php
        $origin = ["de", 59.95, 1.0];
        $applicant = [
            'age' => 50,
            'gender' => 'female',
            'origin' => $origin,
            'height' => 169,
            'weight' => 105,
            'pupilColor' => 'blue'
        ];
    }
    ?>




    // Objekt mit Bewerberdaten
    let applicant = <?php echo json_encode($applicant); ?>;

    // Strafpunkte für das Alter berechnen
    function calculatePenaltyPoints(age, lowerBound, upperBound, maxPoints) {
        const penaltyPerYear = 0.05; // 5% penalty
        const yearsFromUpper = upperBound - age;
        const penalty = yearsFromUpper * penaltyPerYear * maxPoints;
        return maxPoints - penalty;
    }

    // Optimaler Bereich für Größe und Gewicht
    function calculatePenaltyForRange(value, optimalRange, maxPoints) {
        const penaltyPerUnit = 0.025; // 2.5% penalty
        const maxPenalty = maxPoints * 0.80; // Maximum 80% penalty
        const [optimalMin, optimalMax] = optimalRange;

        if (value >= optimalMin && value <= optimalMax) return maxPoints;

        const unitsFromOptimal = value < optimalMin ? optimalMin - value : value - optimalMax;
        let penalty = unitsFromOptimal * penaltyPerUnit * maxPoints;

        // Cap the penalty at a maximum of 20%
        penalty = Math.min(penalty, maxPenalty);

        return Math.max(maxPoints - penalty, 0);
    }

    // Punkte für das Alter holen
    function getPointsForAge(age) {
        if (age >= 18 && age <= 19) return calculatePenaltyPoints(age, 18, 19, 15);
        if (age >= 20 && age <= 24) return calculatePenaltyPoints(age, 20, 24, 28);
        if (age >= 25 && age <= 29) return calculatePenaltyPoints(age, 25, 29, 40);
        if (age >= 30 && age <= 37) return calculatePenaltyPoints(age, 30, 37, 34);
        if (age >= 38 && age <= 45) return calculatePenaltyPoints(age, 38, 45, 40);
        if (age >= 46 && age <= 50) return calculatePenaltyPoints(age, 46, 50, 60);
        if (age >= 51 && age <= 55) return calculatePenaltyPoints(age, 51, 55, 55);
        if (age >= 56 && age <= 60) return calculatePenaltyPoints(age, 56, 60, 50);
        if (age >= 61 && age <= 65) return calculatePenaltyPoints(age, 61, 65, 45);
        if (age >= 66 && age <= 70) return calculatePenaltyPoints(age, 66, 70, 40);
        if (age >= 71 && age <= 77) return calculatePenaltyPoints(age, 71, 77, 30);
        if (age >= 78 && age <= 80) return calculatePenaltyPoints(age, 78, 80, 25);
        return 0;
    }

    // Punkte für den Bewerber berechnen
    function calcPointsForApplicant(applicant) {
        const genderPoint = gender.find(item => item.sex === applicant.gender);
        const pupilColorPoint = pupils.find(item => item.color === applicant.pupilColor);

        return {
            agePoints: (getPointsForAge(applicant.age)) * applicant.origin[2],
            genderPoints: (genderPoint ? genderPoint.points : 0) * applicant.origin[2],
            originPoints: applicant.origin[1],
            heightPoints: (calculatePenaltyForRange(applicant.height, optimalHeightRange, 60)) * applicant.origin[2],
            weightPoints: (calculatePenaltyForRange(applicant.weight, optimalWeightRange, 60)) * applicant.origin[2],
            pupilColorPoints: (pupilColorPoint ? pupilColorPoint.points : 0) * applicant.origin[2]
        };
    }


    // Punkte für den Bewerber holen
    let applicantPoints = calcPointsForApplicant(applicant);

    // Radar-Chart initialisieren
    var ctx = document.getElementById('meinRadarChart').getContext('2d');
    var meinRadarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Alter', 'Geschlecht', 'Herkunft', 'Größe', 'Gewicht', 'Augenfarbe', 'test', 'test'],

            datasets: [{
                label: 'Bewertung',
                data: [applicantPoints.agePoints,
                    applicantPoints.genderPoints,
                    applicantPoints.originPoints,
                    applicantPoints.heightPoints,
                    applicantPoints.weightPoints,
                    applicantPoints.pupilColorPoints
                ], // Punkte
                backgroundColor: "rgba(0,162,255,0.36)",
                borderColor: "rgba(0,132,204,0.37)",
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    ticks: {
                        display: false
                    },
                    min: 0,
                    max: 60,
                    pointLabels: {
                        display: true
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    enabled: false
                }
            }
        }
    });
</script>
</body>
</html>
