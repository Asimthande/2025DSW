document.getElementById('list-view-btn').addEventListener('click', () => {
    document.getElementById('list-view').style.display = 'block';
    document.getElementById('grid-view').style.display = 'none';
});

document.getElementById('grid-view-btn').addEventListener('click', () => {
    document.getElementById('list-view').style.display = 'none';
    document.getElementById('grid-view').style.display = 'block';
});
