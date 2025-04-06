document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const month = new Date().getMonth(); // 0 = Jan, 11 = Dec
    let season = '';

    if (month >= 5 && month <= 7) {
        season = 'winter'; // June - August
    } else if (month >= 8 && month <= 10) {
        season = 'spring'; // September - November
    } else if (month === 11 || month === 0 || month === 1) {
        season = 'summer'; // December - February
    } else {
        season = 'autumn'; // March - May
    }

    // Add season class to body
    body.classList.add(`season-${season}`);

    // Optional: log it for debugging
    console.log(`Detected season: ${season}`);
});
