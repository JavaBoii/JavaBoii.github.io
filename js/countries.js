async function fetchCountries() {
    const response = await fetch('js/data/countryList.json');
    return await response.json();
}

function calculatePoints(country) {
    let points = 40;
    let mp;

    // Define multipliers for each region (for points)
    const regionMultipliers = {
        "AF": 0.9,
        "NA": 0.85,
        "SA": 0.95,
        "EU": 1.1,
        "AS": 1.05,
        "OC": 1.0,
        "AN": 1.15
    };

    // Define multipliers for mp
    const mpMultipliers = {
        "AF": 0.82,
        "NA": 0.8,
        "SA": 0.85,
        "EU": 1.0,
        "AS": 0.95,
        "OC": 0.90,
        "AN": 1.0 // Assuming a default value for Antarctica
    };

    // Adjust points based on language
    if (country.language.name === "French") {
        points -= 15;
    } else if (country.language.name === "German") {
        points += 14.5;
    }

    // Adjust points based on country name
    if (country.name === "France" || country.name === "Russia") {
        points -= 25;
    }

    // Apply the multiplier to the points
    const regionMultiplier = regionMultipliers[country.region] || 1; // Default to 1 if region not found
    points *= regionMultiplier;
    mp = mpMultipliers[country.region] || 1;

    return {points, mp};
}


function populateCountries(selectElementId, countries) {
    const select = document.getElementById(selectElementId);
    countries.forEach(country => {
        // Calculate points and mp for each country
        let calculatedValues = calculatePoints(country);

        let option = document.createElement("option");
        // Create a JSON string with country code, points, and mp
        option.value = JSON.stringify({
            code: country.code,
            points: calculatedValues.points,
            mp: calculatedValues.mp
        });
        option.textContent = country.flag + ' ' + country.name;
        select.appendChild(option);
    });
}

document.addEventListener('DOMContentLoaded', async function() {
    const countries = await fetchCountries();
    populateCountries("origin-select", countries);
});
