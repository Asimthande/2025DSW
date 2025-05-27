document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const month = new Date().getMonth(); 
    let season = '';

    if (month >= 5 && month <= 7) {
        season = 'winter'; 
    } else if (month >= 8 && month <= 10) {
        season = 'spring';
    } else if (month === 11 || month === 0 || month === 1) {
        season = 'summer'; 
    } else {
        season = 'autumn'; 
    }

    
    body.classList.add(`season-${season}`);


    console.log(`Detected season: ${season}`);
});
